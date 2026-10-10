---
trigger: glob
globs: **/*.{js,ts,jsx,tsx,vue,php,blade.php,md,html,json}
---

# PROJECT NAMING CONVENTIONS, SYNTAX STANDARDS & KBBI 2026 RULES

## 1. DUAL-LANGUAGE ARCHITECTURE PRINCIPLE
- **Code Identifiers (Variables, Functions, Classes, Files, APIs)**: MUST use **English** terms to maintain compatibility with global open-source libraries, frameworks, and static analysis tools.
- **Human-Readable Content (UI Copy, Comments, Logs, Documentation)**: MUST strictly use **Standard Bahasa Indonesia according to KBBI 2026 / EBI** (Ejaan Bahasa Indonesia).

---

## 2. PROJECT, DIRECTORY & FILE NAMING STANDARDS

### A. Project & Repository Name
- **Format**: `kebab-case` (Lowercase with hyphens).
- **Rule**: Must reflect domain purpose and project scope without vague abbreviations.
- **Example**: `mcu-wms-system`, `inventory-management-api`, `hospital-patient-portal`.

### B. Folders & Directories
- **Format**: `kebab-case` for all web and backend projects (unless framework requires specific convention like Laravel `app/Http/Controllers`).
- **Rule**: Plural names for collections of files/modules.
- **Example**: `user-management/`, `payment-gateways/`, `invoice-generators/`.

### C. File Naming Matrix
- **JavaScript / TypeScript Utilities**: `kebab-case` (e.g., `date-formatter.ts`, `string-helper.js`).
- **UI Components (React / Vue / Blade)**:
  - React/Vue: `PascalCase` (e.g., `UserCard.tsx`, `PaymentModal.vue`).
  - Blade Templates: `kebab-case` (e.g., `user-profile.blade.php`).
- **Backend Classes & Controllers**: `PascalCase` (e.g., `UserController.php`, `OrderRepository.ts`).
- **Database Migrations**: `snake_case` with UTC timestamp prefix (e.g., `2026_08_12_000001_create_work_orders_table.php`).

---

## 3. CODE SYNTAX IDENTIFIERS (VARIABLES, FUNCTIONS, OBJECTS)

### A. Variables & Parameters
- **Format**: `camelCase`.
- **Explicitness**: MUST be self-descriptive and unambiguous. Single-letter variable names are STRICTLY FORBIDDEN (except loop counters `i`, `j` scoped under 3 lines).
- **Boolean Variables**: MUST start with a predicate verb (`is`, `has`, `can`, `should`).
  - ✅ `isPaymentCompleted`, `hasAdminPermission`, `canUserDelete`
  - ❌ `paymentStatus` (ambiguous), `admin` (not boolean clear), `flag`

### B. Functions & Methods
- **Format**: `camelCase`.
- **Verb-Noun Structure**: EVERY function MUST start with an active verb describing its operation.
  - Fetching Data: `getUserById()`, `fetchActiveOrders()`
  - Modifying Data: `calculateTaxAmount()`, `updatePatientRecord()`
  - Action/Trigger: `sendInvoiceEmail()`, `processPayment()`
- **Strict Prohibition**: NEVER use vague names like `doStuff()`, `processData()`, `handleItem()`, `exec()`.

### C. Classes, Interfaces, Types & Enums
- **Classes**: `PascalCase` using singular nouns (`InvoiceCalculator`, `DatabaseConnection`).
- **Interfaces**: `PascalCase` (e.g., `UserRepositoryInterface` or `UserRepository`).
- **Types**: `PascalCase` (e.g., `UserProfileType`).
- **Constants & Enums**: `UPPER_SNAKE_CASE` (e.g., `MAX_RETRY_ATTEMPTS`, `DEFAULT_PAGE_LIMIT`).

---

## 4. HUMANS & UI COPY STANDARDS (BAHASA INDONESIA KBBI 2026)

All user-facing text elements MUST follow formal, polite, and standard Bahasa Indonesia (KBBI 2026).

### A. UI Copy & Buttons (Antarmuka Pengguna)
- Use active, polite, and clear action words. Avoid slang, regional dialects, or direct literal English translation that sounds unnatural.
- **Action Buttons**:
  - ✅ "Simpan Perubahan", "Kirim Laporan", "Unduh PDF", "Hapus Data"
  - ❌ "Submit", "Save", "Delete", "Click Disini", "Proses"
- **Form Labels & Placeholders**:
  - ✅ Label: "Alamat Surat Elektronik (Email)", "Nomor Pokok Wajib Pajak (NPWP)"
  - ✅ Placeholder: "Masukkan nama lengkap sesuai KTP..."
- **System Notification & Dialogs**:
  - ✅ Success: "Data pendaftaran pasien berhasil disimpan."
  - ❌ Error (Vague): "Error 500: System crashed!"
  - ✅ Error (Correct): "Gagal menghubungkan ke server basis data. Silakan coba beberapa saat lagi."

---

## 5. CODE COMMENTS, LOGGING & DOCUMENTATION (KBBI 2026)

### A. Inline & Block Code Comments
- MUST be written in Bahasa Indonesia.
- Focus on **WHY** the code logic exists (business rules), NOT **WHAT** the code syntactically does.
- **Example**:
  ```php
  // ✅ BENAR: Menjelaskan alasan bisnis (BRD Ref: FM-SA-001)
  // Menghitung potongan pajak sebesar 11% khusus untuk pelanggan berstatus B2B aktif.
  $pajak =$totalHarga * 0.11;

  // ❌ SALAH: Menjelaskan sintaks kodingan yang sudah jelas
  // Mengalikan total harga dengan 0.11
  $pajak =$totalHarga * 0.11;
B. System Logging Standards
System logs MUST be structured, traceable, and written in clear Bahasa Indonesia with contextual parameters.

Log Levels & Format:

INFO: [INFO] Pengguna id:{userId} berhasil memperbarui data profil.

WARNING: [PERINGATAN] Upaya masuk akun gagal untuk email:{email}. Percobaan ke-{attempt}.

ERROR: [KESALAHAN] Gagal memproses pembayaran pesanan id:{orderId}. Alasan: {errorMessage}.

C. Technical Documentation (*.md Files)
MUST be written in formal Bahasa Indonesia (KBBI 2026) using clear headings, tables, and bullet points.

Key technical terms in English (e.g., database, framework, middleware) MUST be written in italics if not translated, or translated using standard KBBI equivalents (basis data, kerangka kerja, penyaring/perantara).

6. ANTI-AMBIGUITY & TRACKABILITY CHECKLIST
The AI Agent MUST audit generated code against the following "Zero-Ambiguity" checklist:

Category	Allowed / Recommended	Strictly Forbidden
Variable Context	temporaryInvoiceList	temp, data, arr, list1
Function Intent	validateCustomerPhoneNumber()	check(), validate(), fn()
UI Action Copy	"Tambah Baris Baru"	"Add", "Plus", "Klik"
Error Handling	"Format nomor telepon tidak valid. Gunakan awalan +62."	"Invalid input!"
Language Mixing	Pure English syntax + Pure Bahasa Indonesia comments	Mixed syntax like function getNamaUser()
7. AGENT EXECUTION DIRECTIVES
NEVER mix Indonesian and English words within the same variable or function identifier (e.g., getDataPengguna() IS FORBIDDEN; use getUserData() and document it in Indonesian).

Ensure all generated UI templates, notifications, validation messages, and system logs use standard KBBI 2026 spelling.

Apply the "Fix Terkecil yang Aman" principle: update code names without breaking existing references or external public API endpoints.
