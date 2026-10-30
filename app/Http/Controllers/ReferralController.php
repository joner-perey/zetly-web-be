<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * Get All Referrals
     */
    public function index(): JsonResponse
    {
        $referrals = Referral::with(['client', 'doctor'])->orderBy('id', 'desc')->get()->map(function ($ref) {
            $statusColors = [
                'referred' => ['bg' => '#f1f5f9', 'color' => '#475569', 'border' => '#e2e8f0'],
                'booked' => ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fef3c7'],
                'completed' => ['bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0'],
                'paid_out' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'],
            ];

            $statusKey = strtolower(str_replace(' ', '_', $ref->status));
            $color = $statusColors[$statusKey] ?? $statusColors['referred'];

            $statusDisplay = match ($statusKey) {
                'paid_out' => 'Paid Out',
                'booked' => 'Booked',
                'completed' => 'Completed',
                default => 'Referred',
            };

            return [
                'id' => $ref->id,
                'patient' => $ref->client?->client_code ?? "ZT-{$ref->client_id}",
                'patient_name' => $ref->client?->full_name ?? 'Unknown',
                'doctorClinic' => ($ref->doctor?->name ?? 'Unknown') . ' · ' . ($ref->doctor?->clinic_name ?? 'Clinic'),
                'status' => $statusDisplay,
                'raw_status' => $ref->status,
                'commission' => '$' . number_format($ref->commission_amount, 0),
                'commission_num' => (float) $ref->commission_amount,
                'statusColor' => $color,
            ];
        });

        return response()->json([
            'data' => $referrals,
        ]);
    }

    /**
     * Get Referral Funnel Stats & Pending Amount
     */
    public function stats(): JsonResponse
    {
        $referredCount = Referral::where('status', 'referred')->count();
        $bookedCount = Referral::where('status', 'booked')->count();
        $completedCount = Referral::where('status', 'completed')->count();
        $paidOutCount = Referral::where('status', 'paid_out')->count();

        // Pending commission (not yet paid out)
        $pendingAmount = Referral::where('status', '!=', 'paid_out')->sum('commission_amount');
        $totalReferrals = Referral::count();

        return response()->json([
            'stats' => [
                ['label' => 'REFERRED', 'value' => $referredCount],
                ['label' => 'BOOKED', 'value' => $bookedCount],
                ['label' => 'COMPLETED', 'value' => $completedCount],
                ['label' => 'PAID OUT', 'value' => $paidOutCount],
            ],
            'summary' => [
                'total_referrals' => $totalReferrals,
                'pending_sgd' => (float) $pendingAmount,
                'formatted_pending' => '$' . number_format($pendingAmount, 0) . ' SGD pending',
            ],
            'referral_link' => 'https://app.zetly.health/ref/ABC01',
        ]);
    }

    /**
     * Update Referral Status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $referral = Referral::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:referred,booked,completed,paid_out',
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'paid_out' && !$referral->paid_out_at) {
            $updateData['paid_out_at'] = now();
        }

        $referral->update($updateData);

        return response()->json([
            'message' => 'Referral status updated.',
            'referral' => $referral,
        ]);
    }
}

