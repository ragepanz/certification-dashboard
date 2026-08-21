<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\CertificationLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Superadmin (LCU)
        $admin = User::firstOrCreate(
            ['email' => 'admin@lcu.com'],
            [
                'name' => 'Superadmin LCU',
                'employee_number' => 'LCU-001',
                'unit' => 'Learning Center Unit',
                'role' => 'superadmin',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Read Excel parsed JSON
        $jsonPath = database_path('excel_import.json');
        if (File::exists($jsonPath)) {
            $data = json_decode(File::get($jsonPath), true);

            $this->command->info("Seeding " . count($data) . " employees and their certifications from Excel data...");

            foreach ($data as $empData) {
                // Generate clean unique email
                $sanitizedName = Str::slug($empData['name'], '.');
                $email = $sanitizedName . '.' . $empData['employee_number'] . '@gmf-aeroasia.co.id';

                $user = User::updateOrCreate(
                    ['employee_number' => $empData['employee_number']],
                    [
                        'name' => $empData['name'],
                        'email' => $email,
                        'unit' => $empData['unit'] ?? 'TN',
                        'role' => 'employee',
                        'password' => Hash::make('password'),
                    ]
                );

                foreach ($empData['certs'] as $certItem) {
                    try {
                        $expiry = Carbon::parse($certItem['expiry_date']);
                        // Assume standard issue date is 2 years before expiry if not specified
                        $issueDate = (clone $expiry)->subYears(2);

                        Certification::create([
                            'user_id' => $user->id,
                            'certificate_name' => $certItem['name'],
                            'issue_date' => $issueDate->format('Y-m-d'),
                            'expiry_date' => $expiry->format('Y-m-d'),
                        ]);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            $this->command->info("Excel data seeded successfully!");
        }
    }
}
