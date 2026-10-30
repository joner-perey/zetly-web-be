<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientOnboardingController extends Controller
{
    /**
     * Admin: Register Patient & Generate QR Onboarding Token
     */
    public function registerWithQr(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'client_code' => 'required|string|max:50|unique:clients,client_code',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Female,Male,Other',
            'insurance_provider' => 'nullable|string|max:100',
            'insurance_id' => 'nullable|string|max:100',
            'plan_type' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:30',
            'cashless_eligible' => 'boolean',
        ]);

        $token = 'inv_' . Str::random(12) . '_' . dechex(time());

        $client = Client::create([
            'full_name' => $validated['full_name'],
            'client_code' => $validated['client_code'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'insurance_provider' => $validated['insurance_provider'] ?? null,
            'insurance_id' => $validated['insurance_id'] ?? null,
            'plan_type' => $validated['plan_type'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'cashless_eligible' => $validated['cashless_eligible'] ?? true,
            'onboarding_token' => $token,
            'token_expires_at' => now()->addDays(7),
            'onboarding_status' => 'pending_activation',
        ]);

        $onboardUrl = "https://app.zetly.health/onboard?token={$token}&client=" . urlencode($client->client_code);

        return response()->json([
            'message' => 'Patient registered. Onboarding QR ready.',
            'client' => $client,
            'token' => $token,
            'onboard_url' => $onboardUrl,
        ], 201);
    }

    /**
     * Patient Mobile App / Web: Verify QR Onboarding Token
     */
    public function verifyToken(Request $request): JsonResponse
    {
        $token = $request->query('token');

        if (!$token) {
            return response()->json(['message' => 'Token is required.'], 400);
        }

        $client = Client::where('onboarding_token', $token)->first();

        if (!$client) {
            return response()->json(['message' => 'Invalid or expired QR token.'], 404);
        }

        if ($client->token_expires_at && $client->token_expires_at->isPast()) {
            $client->update(['onboarding_status' => 'expired']);
            return response()->json(['message' => 'QR invitation has expired. Please request a new QR from clinic admin.'], 410);
        }

        if ($client->onboarding_status === 'activated') {
            return response()->json(['message' => 'This account has already been activated. Please login directly.'], 409);
        }

        return response()->json([
            'valid' => true,
            'patient' => [
                'name' => $client->full_name,
                'client_code' => $client->client_code,
                'insurance' => $client->insurance_provider,
                'plan' => $client->plan_type,
            ],
        ]);
    }

    /**
     * Patient Mobile App: Set Username & Password, Link Account
     */
    public function activateAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'username' => 'required|string|min:3|max:50|unique:users,email', // username or email
            'password' => 'required|string|min:6|confirmed',
        ]);

        $client = Client::where('onboarding_token', $validated['token'])->first();

        if (!$client) {
            return response()->json(['message' => 'Invalid token.'], 404);
        }

        if ($client->onboarding_status === 'activated') {
            return response()->json(['message' => 'Account is already activated.'], 409);
        }

        // Create linked user in users table
        $user = User::create([
            'name' => $client->full_name,
            'email' => str_contains($validated['username'], '@') ? $validated['username'] : "{$validated['username']}@patient.zetly.health",
            'password' => Hash::make($validated['password']),
            'role' => 'client',
            'phone' => $client->contact_number,
            'status' => 'active',
            'must_change_password' => false,
        ]);

        // Link client to user & invalidate token
        $client->update([
            'user_id' => $user->id,
            'onboarding_token' => null, // One-time use: invalidate immediately
            'onboarding_status' => 'activated',
            'activated_at' => now(),
        ]);

        $authToken = $user->createToken('zetly-patient-app')->plainTextToken;

        return response()->json([
            'message' => 'Account activated successfully! You are now logged in.',
            'token' => $authToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'client_code' => $client->client_code,
            ],
        ]);
    }
}

