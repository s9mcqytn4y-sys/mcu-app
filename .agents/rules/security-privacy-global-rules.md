---
trigger: glob
globs: **/*
---

# GLOBAL SECURITY, DATA PRIVACY & ZERO TRUST ARCHITECTURE RULES (2026 STANDARDS)

## 1. ZERO TRUST & ACCESS CONTROL (IAM) RULES
- **Never Trust, Always Verify**: Every request—whether coming from internal microservices or external networks—MUST be explicitly authenticated, authorized, and validated.
- **Principle of Least Privilege (PoLP)**: Service accounts, database users, and application roles MUST possess only the minimum required permissions.
- **Strict OIDC / JWT Handling**: Session tokens and JWTs MUST be signed with strong algorithms (EdDSA or RS256/HS256 with $\ge 256$-bit secret keys), set to short expiration times ($< 15$ mins for Access Tokens), and stored in `HttpOnly`, `Secure`, `SameSite=Strict` cookies.
- **BOLA / IDOR Defense**: EVERY API endpoint accessing database records by ID MUST perform explicitly coded ownership authorization checks (`User A` cannot access `User B`'s records).

---

## 2. CODE SECURITY & OWASP TOP 10 GUARDRAILS

### A. Injection & Input Sanitization
- **Parameterized Queries**: ALL SQL interactions MUST use Prepared Statements / Parameterized Queries or safe ORM mappings. Concatenated SQL strings are STRICTLY FORBIDDEN.
- **Strict Input Validation**: Validate incoming payloads on the **server-side** against strict type/length/format schemas (Zod, Valibot, or native framework validators) using explicit whitelisting.

### B. File Upload & Path Traversal Security
- **MIME & Magic Bytes Verification**: Validate actual file headers (*magic bytes*), not just user-supplied file extensions.
- **File Storage Isolation**: Never store user-uploaded files inside the web root. Rename files to random **UUID v7** and store them in isolated Cloud Object Storage (e.g., S3 Bucket) with public execution disabled.

### C. Cross-Site Scripting (XSS) & CSRF Protection
- **Contextual Output Escaping**: Automatically or explicitly escape all dynamic values rendered into HTML/DOM structures.
- **Content Security Policy (CSP)**: Enforce a strict CSP header (`default-src 'self'`).
- **Anti-CSRF Tokens**: Form-based requests and state-changing HTTP methods (`POST`, `PUT`, `PATCH`, `DELETE`) MUST require anti-CSRF token verification or strict `SameSite` cookie policies.

### D. SSRF (Server-Side Request Forgery) Mitigation
- Block outbound server requests targeting private IP ranges (RFC 1918: `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) and local loopback (`127.0.0.1` / AWS Metadata `169.254.169.254`).

---

## 3. DATA PRIVACY (PII) & ENCRYPTION STANDARDS

- **Data Minimization (UU PDP / GDPR)**: Collect and store ONLY data strictly required for application functionality.
- **Encryption In-Transit**: Enforce **TLS 1.3** (or minimal TLS 1.2 with strong cipher suites) for all HTTP endpoints and internal microservice communication (mTLS).
- **Encryption At-Rest**:
  - High-sensitivity PII columns (National ID / NIK, Credit Cards, Medical Data) MUST be encrypted using **AES-256-GCM** or Envelope Encryption before storage.
  - Passwords MUST be hashed using **Argon2id** or **Bcrypt** (cost factor $\ge 12$). Plaintext or weak hashes (MD5, SHA1, SHA256) are STRICTLY FORBIDDEN.
- **Non-Production Data Masking**: NEVER use unmasked production database dumps in Staging or Development environments. Mask or anonymize all PII beforehand.

---

## 4. SECRETS MANAGEMENT & ENVIRONMENT HARDENING

- **ZERO HARDCODED SECRETS**: NEVER commit API keys, database passwords, private certificates, or JWT secrets directly into code or repository files.
- **Secrets Injections**: Always load configuration secrets from environment variables (`.env`) or dedicated Secret Managers (HashiCorp Vault, AWS Secrets Manager).
- **Git Hygiene**: Ensure `.env`, `*.pem`, `*.key`, and build artifacts are explicitly listed inside `.gitignore`.
- **Fail Safely (No Information Disclosure)**:
  - In production, NEVER display raw stack traces, database schema details, or framework error dumps to the end user.
  - Return generic error payloads (e.g., `"Gagal memproses permintaan. Silakan coba beberapa saat lagi."`) while logging full details internally.

---

## 5. AUDIT LOGGING & SECURITY OBSERVABILITY

- **Immutable Audit Logging**: Log critical security events (login attempts, privilege escalation, PII access, password changes, data deletions) into append-only, tamper-proof log stores.
- **Log Sanitization**: PII, passwords, session tokens, and credit card numbers MUST be stripped or masked before writing to log streams.

---

## 6. SECURITY MATRIX & DEFENSE SUMMARY

| Threat Vector | Required Defense Standard | Validation Requirement |
| :--- | :--- | :--- |
| **SQL Injection** | Prepared Statements / ORM Bindings | Automated SAST Scan in CI/CD pipeline. |
| **Broken Auth** | Argon2id / Bcrypt + Rate Limiting + MFA | Lockout policy after 5 consecutive failures. |
| **BOLA / IDOR** | Middleware-level Ownership Verification | Mandatory Pest / Vitest authorization tests. |
| **Secret Leakage** | Vault / `.env` + Pre-commit secret scanning | Git hooks (`gitleaks` / `trufflehog`). |
| **Insecure Dependency** | Software Composition Analysis (SCA) | Automated `npm audit` / `trivy` check. |

---

## 7. AGENT EXECUTION DIRECTIVES
- Prioritize system security, user privacy, and defensive programming above rapid code generation.
- Apply the "Fix Terkecil yang Aman" principle: implement security fixes locally without exposing internal system states or introducing new attack surfaces.
- Always verify that generated code passes static security analysis, input sanitization checks, and zero-secret rules before finalizing execution.
