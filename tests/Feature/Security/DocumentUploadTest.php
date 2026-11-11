<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Hallazgo C4: las subidas aceptaban cualquier extensión y se guardaban en
 * public/document, servido por Apache (ejecución remota de código).
 */
class DocumentUploadTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        Storage::fake('local');
    }

    public function test_executable_files_are_rejected(): void
    {
        $me = $this->createStudent();
        $before = $this->publicDocumentFiles();

        $response = $this->actingAs($me->user)->post("/documentStore/{$me->id}", [
            'post1_extenso' => UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;'),
            'post1_carta' => UploadedFile::fake()->create('carta.pdf', 10, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('post1_extenso');
        $this->assertSame($before, $this->publicDocumentFiles());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_uploads_never_land_in_the_public_directory(): void
    {
        $me = $this->createStudent();
        $before = $this->publicDocumentFiles();

        $this->actingAs($me->user)->post("/documentStore/{$me->id}", [
            'post1_extenso' => UploadedFile::fake()->create('extenso.pdf', 10, 'application/pdf'),
            'post1_carta' => UploadedFile::fake()->create('carta.pdf', 10, 'application/pdf'),
        ]);

        $this->assertSame($before, $this->publicDocumentFiles());
    }

    public function test_download_rejects_path_traversal(): void
    {
        $admin = $this->createUser('Director');

        $this->actingAs($admin)->get('/download/document/..%2F.env')->assertNotFound();
    }

    public function test_staff_can_download_a_stored_document(): void
    {
        $admin = $this->createUser('Coordinador');
        Storage::disk('local')->put('documents/abc123.pdf', '%PDF-1.4');

        $this->actingAs($admin)->get('/download/document/abc123.pdf')->assertOk();
    }

    private function publicDocumentFiles(): array
    {
        $dir = public_path('document');

        return File::isDirectory($dir) ? array_map('basename', File::files($dir)) : [];
    }
}
