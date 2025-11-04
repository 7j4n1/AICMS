<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DatabaseRestoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that restore route exists and requires authentication.
     */
    public function test_restore_route_requires_authentication(): void
    {
        $response = $this->post(route('backup.restore'));

        // Should redirect to login page
        $response->assertStatus(302);
    }

    /**
     * Test that restore requires a file to be uploaded.
     */
    public function test_restore_requires_file(): void
    {
        // Create an admin user for authentication
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('backup.restore'), []);

        // Should return validation error
        $response->assertSessionHasErrors('backup_file');
    }

    /**
     * Test that restore only accepts zip files.
     */
    public function test_restore_only_accepts_zip_files(): void
    {
        // Create an admin user for authentication
        $admin = Admin::factory()->create();

        // Create a fake text file
        $file = UploadedFile::fake()->create('backup.txt', 100);

        $response = $this->actingAs($admin, 'admin')
            ->post(route('backup.restore'), [
                'backup_file' => $file,
            ]);

        // Should return validation error
        $response->assertSessionHasErrors('backup_file');
    }

    /**
     * Test that backup index page loads correctly.
     */
    public function test_backup_index_page_loads(): void
    {
        // Create an admin user for authentication
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('backup.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backup.index');
        $response->assertViewHas(['backups', 'lastBackupTime']);
    }

    /**
     * Test that backup index page contains restore form.
     */
    public function test_backup_index_contains_restore_form(): void
    {
        // Create an admin user for authentication
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('backup.index'));

        $response->assertStatus(200);
        $response->assertSee('Restore Database from Backup');
        $response->assertSee('backup_file');
        $response->assertSee('Restore Database');
    }
}
