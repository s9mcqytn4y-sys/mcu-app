---
trigger: glob
globs: **/otp/**/*, **/auth/otp/**/*, **/services/otp/**/*, **/*Otp*, **/*OneTimePassword*, **/*Verif*
---

# GLOBAL OTP (ONE-TIME PASSWORD) ARCHITECTURE & MULTI-CHANNEL SECURITY RULES (2026 STANDARDS)

## 1. OTP GENERATION & CRYPTOGRAPHIC SECURITY SPECIFICATIONS

Systems MUST generate and persist OTP tokens using cryptographically secure random number generators (CSPRNG) and one-way salted hashes. **NEVER STORE PLAINTEXT OTPS IN DATABASE OR CACHE STORES.**

```text
┌────────────────────────────────────────────────────────────────────────┐
│                      SECURE OTP GENERATION PIPELINE                    │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
 1. CSPRNG Random Generator (`random_int(100000, 999999)`)
 2. TTL Definition (Short Expiration: 3 - 5 Minutes)
 3. Salted One-Way Hashing (`SHA-256(PlaintextOTP + SecretSalt)`)
 4. Fast Memory Persistence (Redis / Valkey with TTL)
 5. Dispatch Plaintext OTP to Delivery Channel (WhatsApp / SMS / Email)
 6. Immediately Purge Plaintext OTP from Memory

A. Generation StandardsEntropy & Randomness: MUST use CSPRNG (random_int() in PHP, crypto.getRandomValues() in JS/TS, crypto/rand in Go). STRICTLY BANNED: rand(), mt_rand(), or Math.random().Format: 6-digit numeric string (Default: 100000 to 999999).Short TTL (Time-To-Live): Maximum 3 to 5 minutes expiration.Single-Use Enforcement (Atomic Invalidation): An OTP MUST be destroyed or marked used immediately upon the first successful verification attempt.B. Secure Storage PatternPlaintext OTPs MUST NOT be saved to relational databases or Redis plain keys.Store only the SHA-256 Hash:$$\text{Stored Value} = \text{SHA256}(\text{Plaintext OTP} + \text{APP\_KEY})$$2. MULTI-CHANNEL DELIVERY STRATEGY & FALLBACK CASCADESystems MUST implement a Cost-Effective and High-Deliverability Fallback Cascade across WhatsApp, Email, and SMS:Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                      OTP CHANNEL FALLBACK CASCADE                      │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                 [ User Requests OTP / Authentication ]
                                    │
                                    ▼
                 [ PRIMARY CHANNEL: WhatsApp (Official / BSP) ]
                 (Fastest, High Open Rate, Medium Cost)
                                    │
                        ┌───────────┴───────────┐
                        ▼                       ▼
            (Delivered & Verified)    (User Clicks "Kirim via SMS / Email")
                        │             (Enforce 60s Resend Cooldown)
                        ▼                       │
                   [ SUCCESS ]                  ▼
                                      [ FALLBACK CHANNEL ]
                                 (SMS / Email Verification)
Multi-Channel Decision MatrixChannelPriority TierSpeed / LatencyTypical CostSecurity & Fraud VulnerabilitiesWhatsApp (Official BSP)PRIMARY (Default)$< 3 \text{ seconds}$Medium (Per HSM Utility)Low ban risk; Secure end-to-end; Requires Meta Template Approval.Email (Transactional SMTP)SECONDARY / SaaS$3 - 10 \text{ seconds}$Very LowVulnerable to inbox compromise / delays if spam filtered.SMS (Twilio / Telkomsel)FALLBACK / LAST RESORT$5 - 15 \text{ seconds}$High / VariableHIGH RISK: Vulnerable to SIM Swapping & SMS Toll Fraud / AIT.Fallback Rules:No Automatic SMS Triggering: NEVER automatically trigger an SMS fallback immediately upon WhatsApp delivery timeout to prevent SMS Toll Fraud (Artificially Inflated Traffic - AIT).Explicit User Action Requirement: Switching channels (e.g., from WhatsApp to SMS) MUST require a manual user tap on the UI after the $60\text{-second}$ cooldown expires.3. RATE LIMITING, THROTTLING & ANTI-TOLL FRAUD ENGINETo prevent SMS Toll Fraud / AIT attacks (where malicious bots trigger thousands of OTP SMS requests to premium-rate numbers) and brute-force guessing:Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                   OTP THROTTLING & DEFENSE LAYERS                      │
├────────────────────────────────────────────────────────────────────────┤
│ 1. Resend Cooldown Timer   : 60 Seconds per target identifier          │
│ 2. Max Generation Cap      : Max 5 OTP requests per target per 24 hours│
│ 3. Max Verification Tries  : Max 3 failed attempts per OTP instance    │
│ 4. IP / Device Fingerprint : Max 10 OTP requests per IP per hour       │
│ 5. CAPTCHA Escalation      : Cloudflare Turnstile required after 2 reqs │
└────────────────────────────────────────────────────────────────────────┘
A. Mathematical Rate Limiting RulesIdentifier Level:Resend Cooldown: $60 \text{ seconds}$ minimum delay between consecutive resend requests for the same phone number/email.Daily Ceiling: Maximum 5 OTP requests per 24-hour window per recipient identifier.Verification Attempt Ceiling: Maximum 3 failed attempts allowed per OTP payload. On the 3rd failed attempt, immediately destroy the active OTP and lock the identifier for 15 minutes.IP Address & Geofencing: Maximum 10 OTP requests per IP address per hour. If the user's IP country does not match the target phone country code (e.g., US IP requesting OTP for an ID +62 number), automatically enforce Cloudflare Turnstile / reCAPTCHA v3.4. OTP STATE MACHINE & ATOMIC VERIFICATION ENGINEAn OTP transaction MUST progress through a deterministic Finite State Machine (FSM):Plaintext[ REQUESTED ] ──► [ DISPATCHED ] ──► [ VERIFIED (Used / Expired) ]
                         │
                         ├─────────────────────────┐
                         ▼                         ▼
                [ EXPIRED (TTL Timeout) ]   [ BLOCKED (3x Failed Tries) ]
Secure Implementation Example (Laravel / PHP)PHPnamespace App\Services;

use Illuminate\Support\Facades\Redis;
use InvalidArgumentException;

class OtpService
{
    private const TTL_SECONDS = 300; // 5 Menit
    private const COOLDOWN_SECONDS = 60; // 60 Detik
    private const MAX_ATTEMPTS = 3;

    /**
     * Generasi OTP Aman dengan Hashing SHA-256 dan Lock Cooldown
     */
    public function generateAndStoreOtp(string $identifier): string
    {
        $cooldownKey = "otp:cooldown:{$identifier}";
        if (Redis::exists($cooldownKey)) {
            throw new InvalidArgumentException("Silakan tunggu 60 detik sebelum meminta OTP baru.");
        }

        // 1. Generate 6-Digit Plaintext via CSPRNG
        $plaintextOtp = (string) random_int(100000, 999999);
        $hashedOtp = hash_hmac('sha256', $plaintextOtp, config('app.key'));

        $dataKey = "otp:data:{$identifier}";
        $attemptsKey = "otp:attempts:{$identifier}";

        // 2. Simpan Hash OTP & Reset Counter Percobaan secara Atomic
        Redis::transaction(function ($tx) use ($dataKey, $attemptsKey, $cooldownKey, $hashedOtp) {
            $tx->setex($dataKey, self::TTL_SECONDS, $hashedOtp);
            $tx->setex($attemptsKey, self::TTL_SECONDS, 0);
            $tx->setex($cooldownKey, self::COOLDOWN_SECONDS, 1);
        });

        return $plaintextOtp; // Return plaintext HANYA untuk dikirim via SMS/WA/Email
    }

    /**
     * Verifikasi OTP secara Atomic
     */
    public function verifyOtp(string $identifier, string $inputOtp): bool
    {
        $dataKey = "otp:data:{$identifier}";
        $attemptsKey = "otp:attempts:{$identifier}";

        $storedHash = Redis::get($dataKey);
        if (!$storedHash) {
            return false; // OTP Kadaluarsa atau Tidak Ditemukan
        }

        // Cek jumlah percobaan
        $currentAttempts = (int) Redis::incr($attemptsKey);
        if ($currentAttempts > self::MAX_ATTEMPTS) {
            Redis::del($dataKey, $attemptsKey); // Blokir & Hapus OTP
            throw new InvalidArgumentException("Batas percobaan OTP terlampaui. Silakan minta OTP baru.");
        }

        // Bandingkan Hash secara Timing-Attack Safe
        $inputHash = hash_hmac('sha256', $inputOtp, config('app.key'));
        if (hash_equals($storedHash, $inputHash)) {
            // Hapus OTP setelah berhasil (Single-Use Enforcement)
            Redis::del($dataKey, $attemptsKey);
            return true;
        }

        return false;
    }
}
5. FRONTEND UX & COPYWRITING STANDARDSA. Input Field UXinputmode="numeric" & autocomplete="one-time-code": Input fields MUST include inputmode="numeric" to automatically display the numeric keypad on mobile devices, and autocomplete="one-time-code" to support WebOTP API / SMS auto-fill prompts.Copy-Paste & Auto-Advance: Support pasting full 6-digit codes directly from clipboard; automatically advance focus across input cells if rendered as individual digit boxes.B. Anti-Phishing Copywriting StandardAll outbound OTP messages (WhatsApp, Email, SMS) MUST include an explicit security warning:Plaintext[MEMBER LOKAL] Kode OTP Anda: 849201.
BERAKHIR DALAM 5 MENIT.
JANGAN BERIKAN KODE INI KEPADA SIAPAPUN, TERMASUK PIHAK TIM KAMI.
Awas Penipuan!
6. EDGE CASES & SECURITY VULNERABILITIES MATRIXVulnerability / Edge CaseRisk / Threat VectorRequired System Mitigation RuleSMS Toll Fraud / AIT AttackAttacker uses automated bots to request SMS OTPs to international premium-rate numbers.Enforce CAPTCHA on OTP request UI; Restrict destination country codes to active user markets; Set strict IP-rate limits.Timing Attack on VerificationAttacker measures string comparison response times to guess digits.Use hash_equals() for constant-time hash comparisons instead of standard == or ===.Race Condition (Double Submit)User double-clicks "Verify" button within milliseconds.Execute atomic Redis checks (Redis::del()) or DB transactions so the first request consumes the OTP and the second fails.SIM Swapping / InterceptionAttacker intercepts SMS via rogue cell towers or SIM swap.Require Secondary MFA (Authenticator App / Passkey) for sensitive high-value actions (e.g., bank transfer, password change).OTP Leak in Log StreamsOTP plaintexts written to laravel.log or APM tools.Mask or filter OTP request parameters from application log formatters (config/logging.php parameter masking).7. AGENT EXECUTION DIRECTIVESZero Plaintext Storage: Never write code that stores raw, unhashed OTPs in databases, logs, or cache stores.Use CSPRNG Generators: Always generate random codes using secure functions (random_int(), crypto.getRandomValues()).Apply "Fix Terkecil yang Aman": Preserve resend cooldown timers, atomic verification checks, and CAPTCHA escalation triggers when modifying authentication or OTP modules.
