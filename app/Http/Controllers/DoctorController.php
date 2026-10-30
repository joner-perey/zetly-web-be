<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    /**
     * Get All Panel Doctors
     */
    public function index(): JsonResponse
    {
        $doctors = Doctor::orderBy('id', 'asc')->get()->map(function ($doc) {
            return [
                'id' => $doc->id,
                'doctor' => $doc->name,
                'clinic' => $doc->clinic_name,
                'specialty' => $doc->specialty,
                'consultation_fee' => $doc->consultation_fee,
                'smc' => $doc->smc,
                'status' => $doc->smc_status === 'verified' ? 'SMC verified' : ($doc->smc_status === 'flagged' ? 'Flagged' : 'Pending SMC'),
                'smc_last_verified_at' => $doc->smc_last_verified_at ? $doc->smc_last_verified_at->format('d M Y') : 'Pending sweep',
            ];
        });

        return response()->json([
            'data' => $doctors,
            'last_sweep' => '8 Sept 2026',
        ]);
    }

    /**
     * Verify SMC Status (After checking MOH / SMC Register)
     */
    public function verifySmc(Request $request, int $id): JsonResponse
    {
        $doctor = Doctor::findOrFail($id);

        $doctor->update([
            'smc_status' => 'verified',
            'smc_last_verified_at' => now(),
        ]);

        return response()->json([
            'message' => "SMC credential verified for {$doctor->name}.",
            'doctor' => $doctor,
        ]);
    }

    /**
     * Admin: Register New Panel Doctor
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'clinic_name' => 'required|string|max:150',
            'specialty' => 'required|string|max:100',
            'consultation_fee' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'smc' => 'required|string|max:50|unique:doctors,smc',
        ]);

        $temporaryPassword = Str::random(10);

        // Create User account for doctor with temporary password
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($temporaryPassword),
            'role' => 'doctor',
            'status' => 'active',
            'must_change_password' => true,
        ]);

        // Create Doctor Profile
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'clinic_name' => $validated['clinic_name'],
            'specialty' => $validated['specialty'],
            'consultation_fee' => $validated['consultation_fee'] ?? 50.00,
            'address' => $validated['address'] ?? null,
            'smc' => $validated['smc'],
            'smc_status' => 'pending',
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Doctor registered successfully. Temporary credentials generated.',
            'doctor' => $doctor,
            'credentials' => [
                'email' => $user->email,
                'temporary_password' => $temporaryPassword,
            ],
        ], 201);
    }
}

