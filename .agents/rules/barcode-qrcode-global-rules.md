---
trigger: glob
globs: **/barcodes/**/*, **/qrcodes/**/*, **/scanner/**/*, **/labeling/**/*, **/*Barcode*, **/*QRCode*, **/*Scanner*
---

# GLOBAL BARCODE & QR CODE ARCHITECTURE RULES (2026 STANDARDS)

## 1. BARCODE & 2D CODE SYMBOLOGY SELECTION TAXONOMY

Systems MUST select the appropriate symbology based on data density, physical scan environment, and industry standards:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                   BARCODE & QR CODE TAXONOMY MATRIX                    │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│     1D LINEAR BARCODES            │               │     2D MATRIX CODES               │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • EAN-13 / UPC-A: Retail B2C POS  │               │ • QR Code (Model 2): Dynamic URLs,│
│ • Code 128: Internal Inventory &  │               │   Payments (QRIS), E-Tickets      │
│   Logistics (Alphanumeric)        │               │ • Data Matrix: Small Hardware Parts,│
│ • ITF-14: Master Carton Packaging │               │   Pharmaceuticals (Ultra-Dense)   │
└───────────────────────────────────┘               └───────────────────────────────────┘

Symbology Selection Rules:Retail Consumer Goods (B2C): MUST use EAN-13 (Global) or UPC-A (North America). Must comply with GS1 prefix rules and include a valid Modulo 10 Checksum Digit.Internal Inventory & Asset Tracking: MUST use Code 128 (high density, full ASCII support) or Code 39 (legacy hardware compatibility).High-Density Data & Interactive Workflow: MUST use QR Code (Model 2) or GS1 Digital Link QR Code.Ultra-Small Surface Area (e.g., PCB, Medical Supplies): MUST use Data Matrix (readable down to $2\text{mm} \times 2\text{mm}$).2. PAYLOAD DESIGN, GS1 STANDARDS & SECURITY ARCHITECTUREA. Dynamic vs Static QR Code ArchitectureStatic QR Code: Payload contains the raw, immutable data directly (e.g., plain Wi-Fi credentials, fixed text). Use ONLY when internet connectivity is absent.Dynamic QR Code (Preferred): Payload contains a short canonical redirection URL pointing to the central API gateway (https://id.domain.com/q/{short_code}).Advantage: Allows updating destination logic, revoking access, tracking scan analytics, and enforcing expiration without re-printing physical labels.B. Cryptographically Signed QR Payloads (Anti-Forgery / Anti-Tampering)For high-security operations (E-Tickets, Medical Receipts, Access Badges, Proof of Purchase), the payload MUST be digitally signed to prevent manual payload manipulation.$$\text{QR Payload} = \text{Data Payload} + \text{"."} + \text{HMAC-SHA256}(\text{Data Payload}, \text{SecretKey})$$PHP// ✅ BENAR: Generasi Payload QR Code Terenkripsi & Tanda Tangan Digital (HMAC)
public function generateSignedQrPayload(string $ticketId, int $userId, int $expiresAt, string $secretKey): string
{
    $data = json_encode([
        'tid' => $ticketId,
        'uid' => $userId,
        'exp' => $expiresAt
    ], JSON_UNESCAPED_SLASHES);

    $base64Data = rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    $signature = rtrim(strtr(base64_encode(hash_hmac('sha256', $base64Data, $secretKey, true)), '+/', '-_'), '=');

    return $base64Data . '.' . $signature;
}
C. Payload Length OptimizationKeep Payloads Short: Larger QR payloads increase matrix version size ($V1$ to $V40$), making the printed code harder to scan in low-light or damaged conditions.Max Target Version: Strive to keep QR Code payloads below 120 alphanumeric characters to stay within QR Version 4 ($33 \times 33$ modules) or smaller.3. GEOMETRY, ERROR CORRECTION & PRINTING STANDARDSA. Error Correction Level (ECL) SelectionQR Codes support 4 Reed-Solomon Error Correction levels:ECL LevelRecovery CapabilityRequired Use CaseLevel L (Low)$\approx 7\%$ damage recoveryInternal digital screens, pristine environments with short payloads.Level M (Medium)$\approx 15\%$ damage recoveryDefault Standard for general retail, packaging, and logistics labels.Level Q (Quartile)$\approx 25\%$ damage recoveryOutdoor industrial environments subject to dirt, scratches, or wear.Level H (High)$\approx 30\%$ damage recoveryMANDATORY if embedding a custom Brand Logo/Icon inside the QR matrix.B. Logo Branding GuardrailWhen embedding a logo inside a QR Code:Error Correction Level MUST be set to Level H (30%).The logo area MUST NOT exceed $20\%$ of the total QR surface area.Keep the 3 primary Finder Patterns (top-left, top-right, bottom-left square corners) completely clear of logos or design decorations.C. Quiet Zone (Margin) & Color Contrast RulesQuiet Zone: Every barcode/QR Code MUST be surrounded by a blank margin:QR Codes: Minimum 4 modules wide on all 4 sides.1D Barcodes: Minimum $10 \times$ Module Width on left and right sides.Color Contrast:Dark foreground on Light background (Default: Black #000000 on White #FFFFFF).STRICT PROHIBITION: NEVER invert colors (light code on dark background) unless guaranteed that all targeted physical scanners support reflection inversion.NEVER use red ink for 1D barcodes (red laser scanners cannot read red lines).D. Printing & DPI ResolutionExport label assets as Vector formats (SVG, EPS, PDF) for thermal printers.If raster format (PNG) is required, render at a minimum of 300 DPI to prevent blurry pixelated edges that cause scanner read failures.Human Readable Text (HRT): All 1D barcodes MUST print the raw text value directly below the barcode lines as a manual fallback.4. HARDWARE SCANNERS & SOFTWARE SCANNING ENGINESystems MUST seamlessly support both physical hardware scanners and mobile camera scanning engines.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                      SCANNER INPUT ARCHITECTURE                        │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│ 1. HARDWARE SCANNER (HID / SERIAL)│               │ 2. SOFTWARE SCANNER (CAMERA)      │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • Emulates Keyboard Input         │               │ • WebRTC Camera Stream            │
│ • Listens to fast keystrokes      │               │ • ZXing / ML Kit / html5-qrcode   │
│ • Trailing `Enter` key signal     │               │ • Frame analysis worker thread    │
└─────────────────┬─────────────────┘               └─────────────────┬─────────────────┘
                  │                                                   │
                  └─────────────────────────┬─────────────────────────┘
                                            ▼
                             [ Debounce & Validation Layer ]
A. Hardware Keyboard Emulation (HID Mode) & DebouncingHardware scanners send rapid keystrokes followed by a suffix key (usually Enter / ASCII 13).Scan Debouncing: Prevent duplicate triggers from double-scanning by enforcing a $1000\text{ms}$ debounce window on identical barcode values.TypeScript// ✅ BENAR: Frontend Barcode Scanner Event Listener dengan Debouncing & Suffiks Enter
export function setupBarcodeListener(onScanSuccess: (code: string) => void, debounceMs = 1000) {
  let buffer = '';
  let lastKeyTime = Date.now();
  let lastScannedCode = '';
  let lastScanTimestamp = 0;

  window.addEventListener('keydown', (e: KeyboardEvent) => {
    const currentTime = Date.now();

    // Reset buffer jika jeda antar ketukan > 50ms (membedakan ketikan manusia vs scanner)
    if (currentTime - lastKeyTime > 50) {
      buffer = '';
    }
    lastKeyTime = currentTime;

    if (e.key === 'Enter') {
      if (buffer.length >= 3) {
        const now = Date.now();
        if (buffer !== lastScannedCode || now - lastScanTimestamp > debounceMs) {
          lastScannedCode = buffer;
          lastScanTimestamp = now;
          onScanSuccess(buffer);
        }
      }
      buffer = '';
    } else if (e.key.length === 1) {
      buffer += e.key;
    }
  });
}
5. EDGE CASES & RESILIENCE MATRIXEdge Case ScenarioPhysical / Technical Root CauseRequired System Mitigation RuleScratched / Torn Physical LabelCode missing 10–20% of surface area.Enforce Level H Error Correction on QR generation; Provide manual HRT text input on UI.Screen Reflection / Mobile GlareOverhead light reflecting on customer mobile screen.Soften scanner brightness; Use camera autofocus API with torch/flash toggle in UI.Duplicate Rapid ScanCashier holds item in front of scanner too long.Software-level Debounce Buffer ($1000\text{ms}$ cooldown per code).Offline Scan ValidationNetwork connectivity drops at event entrance gate.Decrypt & verify payload signature offline client-side using local public key / secret key.Invalid / Spoofed QR CodeAttacker presents random forged QR payload.Verify HMAC signature; If signature verification fails, flag security alert and deny access immediately.6. AGENT EXECUTION DIRECTIVESAlways generate barcodes and QR codes using vector formats (SVG/PDF) or high-DPI rasterization.Enforce cryptographic payload signatures and HMAC validation for all high-security 2D codes.Apply the "Fix Terkecil yang Aman" principle: maintain scan debouncing, quiet zones, and error correction levels when modifying barcode engines or scanner component handlers.
