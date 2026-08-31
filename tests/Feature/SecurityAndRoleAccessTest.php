<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\JobTrainingMatrix;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityAndRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $regularEmployee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'admin_test_sec@example.com'],
            [
                'name' => 'Superadmin Security Test',
                'employee_number' => 'ADM-SEC-01',
                'role' => 'superadmin',
                'password' => Hash::make('password123'),
            ]
        );

        $this->regularEmployee = User::firstOrCreate(
            ['email' => 'emp_test_sec@example.com'],
            [
                'name' => 'Regular Employee Test',
                'employee_number' => 'EMP-SEC-01',
                'role' => 'employee',
                'password' => Hash::make('password123'),
            ]
        );
    }

    /**
     * Test 1: Tamu (Unauthenticated) tidak boleh mengakses route tertutup
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));

        $response = $this->get('/employees');
        $response->assertRedirect(route('login'));

        $response = $this->get('/settings/matrix');
        $response->assertRedirect(route('login'));
    }

    /**
     * Test 2: Pegawai biasa dilarang mengakses halaman sensitif Superadmin (RBAC Protection)
     */
    public function test_employee_cannot_access_superadmin_settings_and_management(): void
    {
        $this->actingAs($this->regularEmployee);

        // Akses menu setting matrix harus di-block 403 Forbidden
        $response = $this->get('/settings/matrix');
        $response->assertStatus(403);

        // Akses manajemen user akun harus di-block 403 Forbidden
        $response = $this->get('/users');
        $response->assertStatus(403);

        // Akses manajemen pegawai harus di-block 403 Forbidden
        $response = $this->get('/employees');
        $response->assertStatus(403);
    }

    /**
     * Test 3: Superadmin bisa mengakses semua dashboard dan modul
     */
    public function test_superadmin_can_access_all_management_modules(): void
    {
        $this->actingAs($this->superAdmin);

        $this->get('/dashboard')->assertStatus(200);
        $this->get('/employees')->assertStatus(200);
        $this->get('/certifications')->assertStatus(200);
        $this->get('/settings/matrix')->assertStatus(200);
        $this->get('/certificate-types')->assertStatus(200);
        $this->get('/reports')->assertStatus(200);
    }

    /**
     * Test 4: Proteksi File Upload (Hanya file dokumen valid yang diizinkan)
     */
    public function test_certification_creation_requires_valid_data(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->post('/certifications', [
            'user_id' => '',
            'certificate_name' => '',
            'expiry_date' => '',
        ]);

        $response->assertSessionHasErrors(['user_id', 'certificate_name', 'expiry_date']);
    }

    /**
     * Test 5: Training Achievement calculation integrity
     */
    public function test_training_achievement_calculation(): void
    {
        $emp = $this->regularEmployee;
        $emp->update(['job_title' => 'Security Tester']);

        JobTrainingMatrix::updateOrCreate(
            ['job_title' => 'Security Tester', 'training_name' => 'Security Awareness'],
            ['validity_type' => '2-Year', 'no_need_training' => false]
        );

        JobTrainingMatrix::updateOrCreate(
            ['job_title' => 'Security Tester', 'training_name' => 'Data Privacy'],
            ['validity_type' => 'Forever', 'no_need_training' => false]
        );

        $this->assertEquals(2, $emp->required_trainings_count);
        $this->assertEquals(0, $emp->completed_trainings_count);
        $this->assertEquals(0.0, $emp->training_achievement);

        // Add 1 certification
        Certification::create([
            'user_id' => $emp->id,
            'certificate_name' => 'Security Awareness',
            'expiry_date' => now()->addYear(),
        ]);

        $emp->refresh();
        $this->assertEquals(1, $emp->completed_trainings_count);
        $this->assertEquals(50.0, $emp->training_achievement);
    }
}
