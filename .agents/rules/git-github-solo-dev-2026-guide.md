---
trigger: glob
globs: **/.gitignore, **/.gitattributes, **/.gitkeep, **/.github/**/*
---

# GIT, GITHUB & SOLO DEVELOPMENT (2026 STANDARDS) RULES

## 1. MODERN GIT VERSION & ENVIRONMENT CONFIGURATION
- **Git Engine Version**: Targets **Git 2.45+ / 2.40+**. Ensure Git binary is up to date (`git --version`).
- **Primary Branch Naming**: Default branch MUST be named `main` (NEVER `master`).
- **Line Endings Consistency**:
  - Enforce explicit LF (Unix) line endings across all operating systems to prevent Git dirty diffs.
  - Set `core.autocrlf = input` (macOS/Linux) or `core.autocrlf = false` (Windows) alongside `.gitattributes`.

---

## 2. METADATA FILES STANDARDIZATION (`.git*`)

### A. `.gitignore` Policy
- EVERY repository MUST have a strict `.gitignore` at the root directory.
- **Mandatory Exclusions**:
  - Environment Files: `.env`, `.env.local`, `.env.*.local` (EXCEPT `.env.example`).
  - Dependencies: `node_modules/`, `vendor/`, `.venv/`.
  - Build & Cache Outputs: `dist/`, `build/`, `.next/`, `.nuxt/`, `.astro/`, `.cache/`.
  - OS & Editor Artifacts: `.DS_Store`, `Thumbs.db`, `.vscode/*` (except `settings.json` if shared), `.idea/`.
  - Logs & Databases: `*.log`, `*.sqlite`, `*.db`.

### B. `.gitattributes` Standard
Enforce explicit text normalization and binary handling across Linux/Windows:
```gitattributes
* text=auto eol=lf
*.png binary
*.jpg binary
*.jpeg binary
*.gif binary
*.ico binary
*.woff2 binary

C. .gitkeep Usage RuleUse .gitkeep ONLY to track intentionally empty directories required by application runtime (e.g., storage/logs/.gitkeep, public/uploads/.gitkeep). Do NOT leave empty .gitkeep files in source code directories.3. SOLO DEVELOPMENT WORKFLOW (DIRECT-TO-MAIN, NO PULL REQUESTS)In a single-developer setup, Pull Requests (PRs) introduce unnecessary operational friction. Direct commits to main are permitted under strict atomic safety rules:Direct Push to main: All features, fixes, and updates can be committed directly to main.Atomic Commits Principle:EVERY commit MUST represent a single, working, and self-contained logical unit of change.NEVER group unrelated modifications (e.g., fixing a database bug and restyling a CSS button in the same commit IS STRICTLY FORBIDDEN).Code on main MUST compile and pass tests at EVERY commit checkpoint (No broken "Work In Progress" pushes).Linear History First:Avoid merge commits (Merge branch 'main' into...).If ephemeral feature branches are used locally, integrate them back using Rebase (git rebase main or git merge --ff-only).4. COMMIT MESSAGE STANDARDIZATION (BAHASA INDONESIA KBBI 2026)All commit messages MUST follow Conventional Commits format, but the description MUST strictly use Standard Bahasa Indonesia Baku (KBBI 2026) with clear active verbs.A. Structure FormatPlaintext<tipe>(<cakupan>): <deskripsi singkat dalam Bahasa Indonesia>
B. Conventional Type Mapping (Tipe Commit)TipePadanan ConventionalPenggunaan SemanticfiturfeatPenambahan fitur baru untuk pengguna/sistem.perbaikanfixPerbaikan bug, galat, atau kesalahan logika.dokumentasidocsPerubahan pada README, dokumentasi API, atau komentar.gayastylePerapihan format kode, titik koma, spasi (tanpa ubah logika).refaktorrefactorPengorganisasian ulang kode tanpa ubah fitur/bug.ujitestPenambahan atau perbaikan unit test/integration test.rutinitaschorePembaruan dependensi, konfigurasi build, atau file .gitignore.C. Examples of Valid Commit Messages✅ fitur(autentikasi): tambahkan verifikasi token JWT pada harian login✅ perbaikan(pembayaran): perbaiki kalkulasi pajak PPN 11 persen✅ dokumentasi(readme): perbarui instruksi instalasi lokal dan variabel env✅ rutinitas(dependensi): perbarui versi laravel ke 13.x❌ fixed bug (Terlalu singkat, Bahasa Inggris, tanpa scope)❌ fitur: update code (Ambigu, tidak ada konteks spesifik)5. VERSIONING & GIT TAGGING (SEMANTIC VERSIONING)Use Semantic Versioning (vMAJOR.MINOR.PATCH):MAJOR: Breaking changes (Perubahan besar yang tidak kompatibel ke belakang).MINOR: New features added in a backward-compatible manner (Fitur baru yang kompatibel).PATCH: Backward-compatible bug fixes (Perbaikan galat kecil).Tag Creation ProtocolALWAYS use Annotated Tags for releases:Bash# Membuat tag rilis beranotasi
git tag -a v1.0.0 -m "Rilis Versi 1.0.0 - Sistem Informasi MCU Siap Produksi"

# Mengirim tag ke remote repository
git push origin v1.0.0
6. GITHUB INTEGRATION, gh CLI & CI PIPELINEA. GitHub CLI (gh) Solo WorkflowLeverage the native GitHub CLI tool (gh) for seamless terminal operations:Bash# Otentikasi & Verifikasi Status
gh auth status

# Membuat Repository Remote Langsung dari Terminal
gh repo create my-app --private --source=. --remote=origin --push

# Mengelola Environment Secrets
gh secret set DATABASE_URL --body "mysql://user:pass@host:3306/db"

# Membuat Release Otomatis dari Git Tag
gh release create v1.0.0 --title "Rilis v1.0.0" --notes "Catatan perubahan versi 1.0.0"
B. Lightweight Continuous Integration (.github/workflows/ci.yml)For solo projects, CI serves as an automated safety net to catch broken builds before deployment.Create .github/workflows/ci.yml:YAMLname: Continuous Integration (Solo Safety Net)

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  build-and-test:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout Code
        uses: actions/checkout@v4

      - name: Setup Runtime (Node.js / PHP / Go)
        uses: actions/setup-node@v4
        with:
          node-version: '22'
          cache: 'npm'

      - name: Install Dependencies
        run: npm ci

      - name: Run Linter & Static Analysis
        run: npm run lint

      - name: Run Automated Tests
        run: npm test
7. AGENT EXECUTION DIRECTIVESALWAYS run rtk git status or git status BEFORE committing to ensure untracked/secret files are not included.NEVER force push (git push --force) to main if remote history is already synchronized with server pipelines.Apply the "Fix Terkecil yang Aman" principle: maintain a clean, readable Git commit tree without leaving uncommitted workspace clutter.
