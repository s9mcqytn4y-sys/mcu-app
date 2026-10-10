---
trigger: glob
globs: **/auth/**/*, **/guards/**/*, **/middleware/**/*, **/policies/**/*, **/*Auth*, **/*Token*, **/*Policy*, **/*Guard*
---

# GLOBAL AUTHENTICATION (AuthN) & AUTHORIZATION (AuthZ) RULES (2026 STANDARDS)

## 1. DUALITY PRINCIPLE: AuthN vs AuthZ
Systems MUST strictly segregate identity verification (AuthN) from access permission enforcement (AuthZ):

```text
┌────────────────────────────────────────────────────────────────────────┐
│                   IDENTITY & ACCESS MANAGEMENT (IAM)                   │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│     AUTENTIKASI / AuthN           │               │       OTORISASI / AuthZ           │
│      ("Siapa Anda?")              │               │     ("Apa Boleh Anda Lakukan?")   │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • Login, Register, Logout         │               │ • Role-Based Access Control (RBAC)│
│ • Password Hashing (Argon2id)     │               │ • Attribute-Based Control (ABAC)  │
│ • JWT / Session Management        │               │ • BOLA / IDOR Ownership Checks    │
│ • Multi-Factor Auth (MFA/TOTP)    │               │ • Policy & Gate Route Middleware  │
│ • OAuth2 / OIDC Social Logins     │               │ • Resource-Level Permission Scopes│
└───────────────────────────────────┘               └───────────────────────────────────┘

2. AUTHENTICATION (AuthN) STANDARDSA. Password Security & Hashing PolicyHashing Algorithm: MUST use Argon2id (preferred) or Bcrypt with a cost factor $\ge 12$. Plaintext, MD5, SHA1, or un-salted SHA256 hashes are STRICTLY FORBIDDEN.Password Complexity: Enforce minimum 12 characters. Check passwords against known breach databases (e.g., HaveIBeenPwned API) during registration/change.Generic Auth Errors: Authentication failure messages MUST be generic to prevent user enumeration:✅ "Surat elektronik (email) atau kata sandi tidak valid."❌ "Kata sandi salah." / "Email tidak ditemukan."B. Token & Session Lifecycle (JWT / Bearer)Token Split Architecture:Access Token: Short-lived (5 to 15 minutes expiration). Contains user ID and claims.Refresh Token: Long-lived (7 to 30 days expiration). Stored with Automatic Rotation (Refresh Token Rotation - RTR) to detect reuse attacks.Secure Cookie Storage:Tokens sent to Web Frontends MUST be stored in HttpOnly, Secure, and SameSite=Strict (or Lax) cookies to prevent XSS-based token theft.NEVER store JWTs in Browser localStorage or sessionStorage.C. Rate Limiting & Account LockoutBrute-Force Defense: Enforce strict rate-limiting on login/password-reset endpoints:Maximum 5 failed attempts per 15-minute window per IP/User account.Require CAPTCHA or temporary account lockout upon reaching threshold.D. Multi-Factor Authentication (MFA)Support Time-based One-Time Password (TOTP via Authenticator Apps) or WebAuthn/FIDO2 hardware keys for administrative or high-privilege roles.SMS-based OTP is DISCOURAGED due to SIM-swapping attack vectors.3. AUTHORIZATION (AuthZ) STANDARDSA. The "Default Deny" (Fail-Closed) PrincipleALL system resources and API routes MUST default to Access Denied (403 Forbidden) unless explicitly permitted by an authenticated Policy or Guard.B. BOLA / IDOR Prevention (Mandatory Ownership Check)EVERY request accessing or mutating a database record by ID MUST verify resource ownership at the controller/service level:PHP// ✅ BENAR: Memverifikasi bahwa file yang diakses adalah milik pengguna yang sedang login
if ($file->user_id !== $currentUser->id && !$currentUser->isAdmin()) {
    throw new AccessDeniedException("Gagal mengakses berkas.");
}
Use non-sequential, non-enumerable primary identifiers like UUID v7 or ULID in public API routes to prevent ID guessing.C. Fine-Grained Authorization ModelsRole-Based Access Control (RBAC): Use for coarse-grained permissions (e.g., Admin, Manager, Member).Policy/Attribute-Based Access Control (ABAC): Use for dynamic contextual constraints (e.g., "User can edit Document ONLY IF Document is status DRAFT AND User belongs to same Department").4. OAUTH2 & SOCIAL LOGIN SECURITYPKCE Requirement: All public applications (SPA, Mobile) implementing OAuth2 MUST enforce PKCE (Proof Key for Code Exchange) (S256 challenge method).State Parameter Verification: ALWAYS generate and validate a cryptographically secure state parameter to prevent OAuth Login CSRF attacks.5. SESSION REVOCATION & AUDIT LOGGINGGlobal Logout & Invalidation:Changing password, updating security settings, or administrative suspension MUST immediately invalidate ALL active Refresh Tokens and Sessions across all devices for that user ID.Revocation Blacklist: Store revoked JWT JTI IDs in fast In-Memory Storage (Redis/Valkey) with TTL matching token expiration.Security Event Logging:Audit logs MUST record: User ID, Event Type (Login Success, Login Failure, Logout, Password Change, AuthZ Denial), IP Address, User-Agent, and Timestamp.6. AGENT EXECUTION DIRECTIVESZero Auth Bypass: NEVER generate code that disables, bypasses, or mocks authentication/authorization guards in production code paths.No Hardcoded Tokens/Secrets: JWT secret keys or OAuth client secrets MUST strictly originate from environment variables (.env).Apply "Fix Terkecil yang Aman": When updating authentication logic, maintain strict session validation without exposing user state or token structures.
