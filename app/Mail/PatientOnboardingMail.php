<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PatientOnboardingMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $patientName;
    public string $clientCode;
    public string $insurance;
    public string $policyNumber;
    public string $planType;
    public bool   $cashless;
    public string $onboardUrl;
    public string $qrBase64;       // raw base64 PNG (no data: prefix)
    public string $qrFileName;

    public function __construct(array $data)
    {
        $this->patientName  = $data['patient_name'];
        $this->clientCode   = $data['client_code'];
        $this->insurance    = $data['insurance'];
        $this->policyNumber = $data['policy_number'];
        $this->planType     = $data['plan_type'];
        $this->cashless     = $data['cashless'];
        $this->onboardUrl   = $data['onboard_url'];
        $this->qrBase64     = $data['qr_base64'];
        $this->qrFileName   = $data['qr_file_name'];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Welcome to Zetly - Complete your account setup ({$this->patientName})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.patient-onboarding',
        );
    }

    public function attachments(): array
    {
        // Attach QR image from raw base64 string
        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => base64_decode($this->qrBase64),
                $this->qrFileName
            )->withMime('image/png'),
        ];
    }
}
