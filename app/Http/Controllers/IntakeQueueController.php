<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntakeQueueController extends Controller
{
    /**
     * Get All Intake Queue Appointments
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status'); // 'all', 'pending', 'verified', 'declined'
        $search = $request->query('search');

        $query = Appointment::with(['client', 'doctor']);

        if ($status && $status !== 'All') {
            if (strtolower($status) === 'pending') {
                $query->where('status', 'awaiting_review');
            } elseif (strtolower($status) === 'verified') {
                $query->where('status', 'sent_to_clinic');
            } elseif (strtolower($status) === 'declined') {
                $query->where('status', 'declined');
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('client_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('doctor', function ($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%")
                         ->orWhere('clinic_name', 'like', "%{$search}%");
                  });
            });
        }

        $appointments = $query->orderBy('received_at', 'desc')->get()->map(function ($item) {
            return [
                'id' => $item->client?->client_code ?? "ZT-{$item->client_id}",
                'appointment_id' => $item->id,
                'requestId' => $item->request_code,
                'patient' => $item->client?->full_name ?? 'Unknown',
                'doctor' => $item->doctor?->name ?? 'Unknown Doctor',
                'clinic' => $item->doctor?->clinic_name ?? 'Unknown Clinic',
                'specialty' => $item->doctor?->specialty ?? 'GP',
                'visitDate' => $item->visit_date ? $item->visit_date->format('d M Y') : 'N/A',
                'visitTime' => $item->time_slot,
                'cover' => $item->cover_type,
                'received' => $item->received_at ? $item->received_at->format('Y-m-d H:i') : '',
                'status' => $item->status === 'sent_to_clinic' ? 'Verified' : ($item->status === 'declined' ? 'Declined' : 'Awaiting review'),
                'raw_status' => $item->status,
                'filterCategory' => $item->status === 'sent_to_clinic' ? 'Verified' : ($item->status === 'declined' ? 'Declined' : 'Pending'),
            ];
        });

        return response()->json([
            'data' => $appointments,
        ]);
    }

    /**
     * Send Intake to Clinic (Verify Cashless)
     */
    public function sendToClinic(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->update([
            'status' => 'sent_to_clinic',
            'reviewed_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Intake request approved and notified to clinic.',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Decline Intake Request
     */
    public function decline(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $appointment->update([
            'status' => 'declined',
            'decline_reason' => $validated['reason'] ?? 'Declined by claims reviewer',
            'reviewed_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Intake request declined.',
            'appointment' => $appointment,
        ]);
    }
}

