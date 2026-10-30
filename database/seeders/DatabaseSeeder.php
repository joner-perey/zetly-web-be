<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Content;
use App\Models\Doctor;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        $admin = User::create([
            'name' => 'Alex Tan',
            'email' => 'alex.tan@zetly.health',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '+65 9123 4567',
            'status' => 'active',
            'must_change_password' => false,
        ]);

        // 2. Doctors
        $doctor1User = User::create([
            'name' => 'Dr. Rajesh Kumar',
            'email' => 'rajesh.kumar@orchardclinic.sg',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'status' => 'active',
        ]);
        $doctor1 = Doctor::create([
            'user_id' => $doctor1User->id,
            'name' => 'Dr. Rajesh Kumar',
            'clinic_name' => 'Orchard Family Clinic',
            'specialty' => 'GP',
            'consultation_fee' => 45.00,
            'address' => '290 Orchard Rd, #08-01 Paragon, Singapore 238859',
            'smc' => 'M18702C',
            'smc_status' => 'verified',
            'smc_last_verified_at' => '2026-09-08',
            'status' => 'active',
        ]);

        $doctor2User = User::create([
            'name' => 'Dr. Amelia Tan',
            'email' => 'amelia.tan@camden.sg',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'status' => 'active',
        ]);
        $doctor2 = Doctor::create([
            'user_id' => $doctor2User->id,
            'name' => 'Dr. Amelia Tan',
            'clinic_name' => 'Camden Aesthetics',
            'specialty' => 'Aesthetic Medicine',
            'consultation_fee' => 85.00,
            'address' => '1 Orchard Blvd, #11-03 Camden Medical Centre, Singapore 248649',
            'smc' => 'M23481Z',
            'smc_status' => 'verified',
            'smc_last_verified_at' => '2026-09-08',
            'status' => 'active',
        ]);

        $doctor3User = User::create([
            'name' => 'Dr. Sophia Lim',
            'email' => 'sophia.lim@novenadental.sg',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'status' => 'active',
        ]);
        $doctor3 = Doctor::create([
            'user_id' => $doctor3User->id,
            'name' => 'Dr. Sophia Lim',
            'clinic_name' => 'Novena Dental Studio',
            'specialty' => 'Dental Surgeon',
            'consultation_fee' => 60.00,
            'address' => '10 Sinaran Dr, #04-12 Novena Medical Center, Singapore 307506',
            'smc' => 'D44109H',
            'smc_status' => 'verified',
            'smc_last_verified_at' => '2026-09-08',
            'status' => 'active',
        ]);

        $doctor4User = User::create([
            'name' => 'Dr. Marcus Chen',
            'email' => 'marcus.chen@orthosports.sg',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'status' => 'active',
        ]);
        $doctor4 = Doctor::create([
            'user_id' => $doctor4User->id,
            'name' => 'Dr. Marcus Chen',
            'clinic_name' => 'Orthosports Singapore',
            'specialty' => 'Orthopedics',
            'consultation_fee' => 120.00,
            'address' => '3 Mount Elizabeth, #15-08 Mount Elizabeth Medical Centre, Singapore 228510',
            'smc' => 'M15620B',
            'smc_status' => 'verified',
            'smc_last_verified_at' => '2026-09-08',
            'status' => 'active',
        ]);

        // 3. Clients / Patients
        $client1 = Client::create([
            'client_code' => 'ZT-10501',
            'full_name' => 'Elena Ong',
            'date_of_birth' => '1992-05-14',
            'gender' => 'Female',
            'contact_number' => '+65 9234 5678',
            'insurance_provider' => 'AIA Singapore',
            'insurance_id' => 'AIA-POL-88219',
            'plan_type' => 'Corporate Comprehensive',
            'cashless_eligible' => true,
            'onboarding_status' => 'activated',
            'activated_at' => now(),
        ]);

        $client2 = Client::create([
            'client_code' => 'ZT-10544',
            'full_name' => 'Marcus Wei',
            'date_of_birth' => '1988-11-20',
            'gender' => 'Male',
            'contact_number' => '+65 9345 6789',
            'insurance_provider' => 'Prudential Singapore',
            'insurance_id' => 'PRU-MED-44102',
            'plan_type' => 'Premier Cashless Care',
            'cashless_eligible' => true,
            'onboarding_status' => 'activated',
            'activated_at' => now(),
        ]);

        $client3 = Client::create([
            'client_code' => 'ZT-10482',
            'full_name' => 'Priya Nair',
            'date_of_birth' => '1995-02-18',
            'gender' => 'Female',
            'contact_number' => '+65 9456 7890',
            'insurance_provider' => 'Great Eastern',
            'insurance_id' => 'GE-CORP-91024',
            'plan_type' => 'Executive Health Saver',
            'cashless_eligible' => true,
            'onboarding_status' => 'activated',
            'activated_at' => now(),
        ]);

        $client4 = Client::create([
            'client_code' => 'ZT-10390',
            'full_name' => 'David Tan',
            'date_of_birth' => '1985-07-30',
            'gender' => 'Male',
            'contact_number' => '+65 9567 8901',
            'insurance_provider' => 'Singlife',
            'insurance_id' => 'SL-SHIELD-10294',
            'plan_type' => 'Integrated Shield Plan',
            'cashless_eligible' => true,
            'onboarding_status' => 'activated',
            'activated_at' => now(),
        ]);

        // 4. Appointments (Intake Queue)
        Appointment::create([
            'request_code' => 'CLM-2041',
            'client_id' => $client1->id,
            'doctor_id' => $doctor1->id,
            'visit_date' => '2026-09-11',
            'time_slot' => 'Morning',
            'cover_type' => 'Discounted consultation',
            'status' => 'awaiting_review',
            'received_at' => '2026-09-09 08:14:00',
        ]);

        Appointment::create([
            'request_code' => 'CLM-2042',
            'client_id' => $client2->id,
            'doctor_id' => $doctor3->id,
            'visit_date' => '2026-09-12',
            'time_slot' => 'Afternoon',
            'cover_type' => 'Cashless treatment',
            'status' => 'awaiting_review',
            'received_at' => '2026-09-09 09:30:00',
        ]);

        Appointment::create([
            'request_code' => 'CLM-2043',
            'client_id' => $client3->id,
            'doctor_id' => $doctor2->id,
            'visit_date' => '2026-09-10',
            'time_slot' => 'Afternoon',
            'cover_type' => 'Cashless treatment',
            'status' => 'awaiting_review',
            'received_at' => '2026-09-09 10:15:00',
        ]);

        // 5. Referrals
        Referral::create([
            'referral_code' => 'ABC01',
            'referrer_id' => $admin->id,
            'client_id' => $client2->id,
            'doctor_id' => $doctor3->id,
            'status' => 'referred',
            'commission_amount' => 60.00,
            'currency' => 'SGD',
            'notes' => 'Novena Dental Studio consultation booking via referral link.',
        ]);

        Referral::create([
            'referral_code' => 'ABC01',
            'referrer_id' => $admin->id,
            'client_id' => $client1->id,
            'doctor_id' => $doctor1->id,
            'status' => 'booked',
            'commission_amount' => 42.00,
            'currency' => 'SGD',
            'notes' => 'Patient completed slot booking at Orchard Family Clinic.',
        ]);

        Referral::create([
            'referral_code' => 'ABC01',
            'referrer_id' => $admin->id,
            'client_id' => $client3->id,
            'doctor_id' => $doctor2->id,
            'status' => 'completed',
            'commission_amount' => 85.00,
            'currency' => 'SGD',
            'notes' => 'Clinical consultation concluded at Camden Aesthetics.',
        ]);

        Referral::create([
            'referral_code' => 'ABC01',
            'referrer_id' => $admin->id,
            'client_id' => $client4->id,
            'doctor_id' => $doctor4->id,
            'status' => 'paid_out',
            'commission_amount' => 118.00,
            'currency' => 'SGD',
            'paid_out_at' => '2026-09-15 14:00:00',
            'notes' => 'Commission paid out via PayNow transfer.',
        ]);

        // 6. Contents
        Content::create([
            'title' => 'Plastic surgery in Phuket',
            'tag' => 'Medical tourism',
            'excerpt' => 'What to know before booking surgical procedures overseas.',
            'body' => 'Overseas medical treatments require careful evaluation of post-op recovery and travel clearance...',
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        Content::create([
            'title' => 'Laser rejuvenation, without the guesswork',
            'tag' => 'Aesthetics',
            'excerpt' => 'Understanding recovery times and post-treatment skin hydration.',
            'body' => 'Post-laser recovery depends largely on deep moisture retention and avoiding UV exposure...',
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        Content::create([
            'title' => 'What a cashless visit looks like',
            'tag' => 'How-to',
            'excerpt' => 'Your step-by-step guide to panel clinic intake with zero out-of-pocket fees.',
            'body' => 'Present your Zetly App digital member card at the front desk. Clinic will verify directly against intake queue...',
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        Content::create([
            'title' => 'Recovery week, done properly',
            'tag' => 'Wellness',
            'excerpt' => 'Best practices for downtime after clinical skincare treatments.',
            'body' => 'Rest, nutrition, and avoiding rigorous workouts are essential in the first 72 hours...',
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        Content::create([
            'title' => 'Hydration after laser resurfacing',
            'tag' => 'Health tip',
            'excerpt' => 'Short member-facing note that will appear in For You.',
            'body' => 'Always apply prescribed barrier ointment in the first 48 hours before switching to mild hydration lotion.',
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
