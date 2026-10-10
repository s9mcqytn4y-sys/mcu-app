---
trigger: glob
globs: **/tests/**/*.php, **/*.test.ts, **/*.spec.ts, **/vitest.config.*, **/pest.php
---

# QA ENGINEERING, TESTING PYRAMID & SECURITY ARCHITECTURE RULES (2026 STANDARDS)

## 1. TESTING PYRAMID ARCHITECTURE (GOOGLE DRIVE SYSTEM SCOPE)
All test suites MUST follow the strict 60-30-10 Testing Pyramid distribution to ensure rapid execution, zero flaky tests, and maximum deployment confidence:

```text
       /\
      /  \     10% End-to-End (E2E) Tests (Playwright / Pest Browser)
     /----\    -----------------------------------------------------
    /      \   30% Integration & Feature API Tests (Pest PHP / Vitest Integration)
   /--------\  -------------------------------------------------------------------
  /          \ 60% Unit Tests (Pest PHP / Vitest Isolation Tests)
 /------------\


Allocation & Responsibilities Matrix:Unit Tests (60% - Base):Focus: Pure functions, path resolution algorithms, virus scan wrappers, quota calculation logic, and utility helpers.Execution Speed: < 10ms per test block.Integration / Feature Tests (30% - Middle):Focus: API endpoints (/api/v1/files), Database transactions, Storage Drivers (S3/MinIO), Authorization Policies (Gate/Policy), Queue Jobs.Execution Speed: < 100ms per test block.E2E / System Tests (10% - Top):Focus: Critical user journeys (e.g., User logs in -> Drag-and-drop upload file -> Share public link with expiration -> Second user downloads file).2. BACKEND TESTING STANDARDS: LARAVEL 13.X WITH PEST PHP (3.X)Pest PHP Syntax: ALWAYS use native Pest PHP functional syntax (test(), it(), expect()). Standard PHPUnit class-based testing is STRICTLY FORBIDDEN in new test suites.Arrange-Act-Assert (AAA) Pattern: Every test block MUST explicitly follow AAA structure.Database Refresh Policy: Use uses(Illuminate\Foundation\Testing\RefreshDatabase::class); inside Pest configuration or test files. NEVER pollute production or local development databases.Strict Expectations: Use Pest expect() API over legacy $this->assert* methods.Example: Pest PHP Feature Test for Drive File Upload & Quota ValidationPHP<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('s3');
});

test('pengguna dapat mengunggah berkas jika kuota penyimpanan mencukupi', function (): void {
    // Arrange (Siapkan Data)
    $user = User::factory()->create([
        'storage_used_bytes' => 0,
        'storage_limit_bytes' => 1024 * 1024 * 100, // 100 MB
    ]);

    $file = UploadedFile::fake()->create('dokumen-rahasia.pdf', 1024, 'application/pdf');

    // Act (Eksekusi Aksi)
    $response = $this->actingAs($user)
        ->postJson('/api/v1/files/upload', [
            'file' => $file,
            'parent_folder_id' => null,
        ]);

    // Assert (Verifikasi Hasil)
    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'dokumen-rahasia.pdf');

    expect($user->fresh()->storage_used_bytes)->toBe(1024 * 1024);
    Storage::disk('s3')->assertExists("drives/{$user->id}/" . $file->hashName());
});

test('pengguna dilarang mengunggah berkas jika melebihi batas kuota', function (): void {
    // Arrange
    $user = User::factory()->create([
        'storage_used_bytes' => 1024 * 1024 * 99, // 99 MB terpakai
        'storage_limit_bytes' => 1024 * 1024 * 100, // Limit 100 MB
    ]);

    $oversizedFile = UploadedFile::fake()->create('video-besar.mp4', 1024 * 5, 'video/mp4'); // 5 MB

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/files/upload', [
            'file' => $oversizedFile,
        ]);

    // Assert
    $response->assertStatus(422)
        ->assertJsonPath('error.code', 'STORAGE_QUOTA_EXCEEDED');

    Storage::disk('s3')->assertMissing("drives/{$user->id}/" . $oversizedFile->hashName());
});
3. FRONTEND TESTING STANDARDS WITH VITESTVitest First: Use Vitest for UI components, state stores, and client-side chunk upload calculations.Component & Hook Isolation: Mock network requests using MSW (Mock Service Worker) or vi.mock() to avoid dependency on live API servers during frontend unit testing.Example: Vitest Test for File Upload Progress CalculatorTypeScriptimport { describe, it, expect } from 'vitest';
import { calculateUploadProgress, isAllowedFileType } from './drive-upload-utils.js';

describe('Drive Upload Utilities', () => {
  it('harus menghitung persentase progres unggahan berkas dengan benar', () => {
    const bytesUploaded = 250;
    const totalBytes = 1000;

    const progress = calculateUploadProgress(bytesUploaded, totalBytes);
    expect(progress).toBe(25);
  });

  it('harus menolak tipe ekstensi berkas berbahaya yang tidak diizinkan', () => {
    const unsafeFile = 'malware.exe';
    const allowedMimeTypes = ['image/png', 'application/pdf'];

    expect(isAllowedFileType(unsafeFile, allowedMimeTypes)).toBe(false);
  });
});
4. MOCKING STRATEGY & ISOLATION RULESNever Mock What You Don't Own Directly: Mock external third-party services (S3 Storage, Virus Scanner API, Transcoding Services, Resend Mailer) using native Laravel / Vitest fakes.Mandatory Mocks in Automated Testing:Cloud Storage: Storage::fake('s3')Queues & Background Jobs: Queue::fake()Notification & Emails: Notification::fake(), Mail::fake()Events: Event::fake()STRICT PROHIBITION: NEVER make real HTTP calls to external production or sandbox APIs (e.g., AWS S3, Google Cloud, VirusTotal) inside test suites.5. PENETRATION TESTING (PEN TEST) & SECURITY QA GUARDRAILSThe QA Engineer MUST write automated security test vectors covering the OWASP Top 10 API Security Risks:A. IDOR / BOLA (Broken Object Level Authorization) TestingMandatory Test Scenario: User A MUST NOT be able to download, rename, view, or delete File ID belonging to User B, even if User A guesses or increments the file ID payload.PHPtest('penetrasi: pengguna A dilarang mengunduh berkas milik pengguna B (BOLA/IDOR Guard)', function (): void {
    $userA = User::factory()->create();$userB = User::factory()->create();

    $fileUserB = File::factory()->create([
        'user_id' => $userB->id,
        'is_public' => false,
    ]);

    $this->actingAs($userA)
        ->getJson("/api/v1/files/{$fileUserB->id}/download")
        ->assertStatus(403); // MUST BE FORBIDDEN
});
B. File Upload Vulnerability TestingMIME-Type Spoofing: Test uploading executable scripts renamed as images (shell.php.png or exploit.php).Path Traversal Attack: Test filenames containing path traversal payloads (../../../../etc/passwd).Zip Bomb / Decompression Attacks: Validate payload limits on archived file extraction endpoints.C. Public Link Expiration & Access ControlTest that public shared links (/s/{share_token}) properly reject requests after the expires_at timestamp or max access count is reached.6. WORST CASE & RESILIENCE TESTING (EDGE CASES & CHAOS)Automated test suites MUST include boundary and worst-case scenarios:CategoryTest ScenarioExpected System BehaviorConcurrency / Race ConditionTwo users attempt to edit/rename the exact same file simultaneously.Lock row via lockForUpdate() and return HTTP 409 Conflict gracefully.Storage Exhaustion Mid-UploadStorage limit reached at chunk 99 out of 100 during chunked upload.Abort transaction, purge orphan temporary chunks, return 422 Unprocessable.Zero-Byte & Ultra-Large FilesUploading 0-Byte file vs 50GB file boundary.Reject 0-Byte file (400 Bad Request); Validate chunk stream handling for 50GB.Special Character FilenamesFilenames with Emojis, RTL scripts, SQL injection strings, and NULL bytes (file\0.pdf).Sanitize name safely without crashing database index or file system paths.Network InterruptionNetwork drop during multi-part upload.Support resumable upload mechanism (TUS protocol / persistent chunk state).7. AGENT EXECUTION DIRECTIVESZero Skipped Tests Rule: NEVER skip tests (->skip()) without an explicit issue link and QA lead approval.Test Execution Before Commit: Execute test suites using RTK proxy (rtk pest or rtk vitest) BEFORE finalizing feature implementation.Apply "Fix Terkecil yang Aman": When fixing broken tests, fix the underlying application logic or update the test contract cleanly without lowering security expectations or removing assertions.
