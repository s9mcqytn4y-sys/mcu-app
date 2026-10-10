---
trigger: glob
globs: **/payments/**/*, **/midtrans/**/*, **/webhooks/**/*, **/checkout/**/*, **/*Payment*, **/*Midtrans*, **/*Webhook*, **/*Invoice*
---

# GLOBAL MIDTRANS PAYMENT GATEWAY INTEGRATION & PRICING ENGINE RULES (2026 STANDARDS)

## 1. DUAL-MODE ARCHITECTURE & ENVIRONMENT CONFIGURATION

Systems MUST maintain strict isolation between **Sandbox (Development)** and **Production** environments, while supporting both **Midtrans Snap UI** (Hosted/Embedded) and **Core API** (Custom UI).

```text
┌────────────────────────────────────────────────────────────────────────┐
│                   MIDTRANS ARCHITECTURE & DATA FLOW                    │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│     1. MIDTRANS SNAP (HOSTED)     │               │     2. CORE API (CUSTOM UI)       │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • Backend requests Snap Token     │               │ • Frontend builds custom UI       │
│ • Frontend renders Snap Popup/Redir│              │ • Backend sends direct Charge Req │
│ • Quickest PCI-DSS compliance     │               │ • Full UX control on checkout     │
└─────────────────┬─────────────────┘               └─────────────────┬─────────────────┘
                  │                                                   │
                  └─────────────────────────┬─────────────────────────┘
                                            ▼
                           [ Midtrans Payment Engine ]
                                            │
                                 (HTTP Webhook Event)
                                            ▼
                           [ Backend Notification Handler ]

. Environment Keys & Environment Variables IsolationSandbox Keys: Prefix SB-Mid-server-... (Server Key) & SB-Mid-client-... (Client Key).API Base URL: https://app.sandbox.midtrans.com/snap/v1/ (Snap) / https://api.sandbox.midtrans.com/v2/ (Core API).Production Keys: Prefix Mid-server-... (Server Key) & Mid-client-... (Client Key).API Base URL: https://app.midtrans.com/snap/v1/ (Snap) / https://api.midtrans.com/v2/ (Core API).STRICT SECURITY RULE:Server Key MUST NEVER be exposed to the frontend or committed to source control. It MUST be loaded strictly via server-side environment variables (MIDTRANS_SERVER_KEY).Client Key MAY be exposed to the frontend for initializing Snap JS (snap.js) or tokenizing credit cards.2. MIDTRANS PAYMENT METHODS TAXONOMY & HANDLING SPECIFICATIONSSystems MUST explicitly implement payload transformers and status parsers for all supported Midtrans payment channels:Payment Method CategoryMidtrans Payment Type CodeSpecific Requirements & Expiry RulesVirtual Account (VA)bank_transfer, echannelSupports BCA, Mandiri (Bill), BNI, BRI, Permata, Cimb. Custom expiry time set via custom_expiry. Output: va_number / bill_key.E-Wallets & QRISgopay, shopeepay, qrisOutput: qr_string (for QRIS), deeplink_url (for GoPay/ShopeePay app redirect). Set short expiry (5–15 mins).Credit / Debit Cardcredit_cardPCI-DSS compliant tokenization. MUST enforce 3D-Secure (3DS) ("secure": true). Supports 1-click & 2-click recurring.Convenience Store (OTC)cstoreIndomaret (indomaret), Alfamart (alfamart). Output: payment_code. Expiry default 24 hours.Cardless Credit / PayLaterakulaku, kredivo, indodanaRequires detailed customer itemization (item_details). Returns redirect URL to provider authorization page.Direct Debitbca_klikpay, cimb_clicks, bri_epayDirect online banking auth. Requires active customer internet banking account.3. MIDTRANS PAYMENT STATUS STATE MACHINE & WEBHOOK HANDLINGPlaintext                               ┌────────────────┐
                               │    PENDING     │
                               └───────┬────────┘
                                       │
            ┌──────────────────────────┼──────────────────────────┐
            ▼                          ▼                          ▼
   ┌────────────────┐         ┌────────────────┐         ┌────────────────┐
   │   SETTLEMENT   │         │     EXPIRE     │         │ CANCEL / DENY  │
   └───────┬────────┘         └────────────────┘         └────────────────┘
           │
           ▼
   ┌────────────────┐
   │ REFUND / CHARGE│
   │      BACK      │
   └────────────────┘
A. Midtrans Status Mapping MatrixMidtrans transaction_statusMidtrans fraud_statusLocal System StatusAction RequiredcaptureacceptPAIDCredit Card transaction successful. Fulfill order.capturechallengeACTION_REQUIREDFlagged by FDS (Fraud Detection System). Requires manual review in Midtrans MAP.settlementaccept / nullPAIDPayment confirmed (VA/E-Wallet/OTC). Release goods/services immediately.pendingnullPENDING_PAYMENTPayment instructions generated. Await customer action.denynullFAILEDPayment rejected by bank/FDS. Notify customer to retry.expirenullEXPIREDPayment window closed. Release reserved inventory back to stock.cancelnullCANCELLEDOrder cancelled by merchant/customer before payment.refund / partial_refundnullREFUNDEDMoney returned to customer. Post reversal accounting entry.B. Webhook Verification & Signature Key Formula (MANDATORY)To prevent Webhook Spoofing (fake HTTP calls pretending to be Midtrans), EVERY incoming webhook MUST be verified against its SHA-512 signature signature before processing:$$\text{Signature Key} = \text{SHA512}\left(\text{order\_id} + \text{status\_code} + \text{gross\_amount} + \text{ServerKey}\right)$$PHP// ✅ BENAR: Verifikasi Signature Key Midtrans secara Otentik di Backend
public function verifyWebhookSignature(array $payload, string $serverKey): bool
{
    $orderId = $payload['order_id'] ?? '';
    $statusCode = $payload['status_code'] ?? '';
    $grossAmount = $payload['gross_amount'] ?? ''; // Format string persis dari payload

    $inputString = $orderId . $statusCode . $grossAmount . $serverKey;
    $calculatedSignature = hash('sha512', $inputString);

    return hash_equals($calculatedSignature, $payload['signature_key'] ?? '');
}
C. Webhook Idempotency GuardMidtrans may send duplicate notification webhooks for a single transaction.Idempotency Check: The notification handler MUST check if the target order is already marked as PAID. If the status update is redundant, return HTTP 200 OK immediately without re-processing order fulfillment or double-crediting balances.4. PRICING ENGINE & MIDTRANS FEE HANDLING POLICYSystems MUST support 3 distinct fee allocation strategies depending on merchant policy:Plaintext1. MERCHANT_ABSORBED  : Customer Pays = Net Price (Merchant covers Midtrans Fee).
2. CUSTOMER_ABSORBED  : Customer Pays = Net Price + Surcharge Fee.
3. HYBRID_SPLIT_FEE   : Customer covers Fixed Fee, Merchant covers Percentage Fee.
A. Midtrans Standard Fee Schedule (Reference 2026)Virtual Account (VA): Fixed $\approx \text{Rp } 4.000 - \text{Rp } 4.500$ per transaction.QRIS: Percentage $\approx 0.7\%$ (Standard / Reguler).GoPay / ShopeePay: Percentage $\approx 1.5\% - 2.0\%$.Credit Card: Percentage $\approx 2.9\% + \text{Rp } 2.000$ per transaction (+ $11\%$ PPN on fee).Convenience Store: Fixed $\approx \text{Rp } 5.000$.B. Mathematical Gross-Up Surcharge FormulaWhen passing fees onto the customer (CUSTOMER_ABSORBED), simple addition ($N + \text{Fee}$) leaves the merchant underpaid due to percentage calculations on the gross total. Use the Gross-Up Formula:$$\text{Gross Amount} = \frac{\text{Net Amount} + \text{Fixed Fee}}{1 - \text{Percentage Fee Rate}}$$Example: Net Price = Rp 100.000, Fee = 2% (0.02) + Rp 2.000.$$\text{Gross Amount} = \frac{100.000 + 2.000}{1 - 0.02} = \frac{102.000}{0.98} = \text{Rp } 104.082$$5. HARDWARE INTEGRATION: EDC MACHINES & OFFLINE POSWhen integrating Midtrans with physical Point-of-Sale (POS) hardware and Electronic Data Capture (EDC) terminals:A. Static vs Dynamic QRIS on POS DisplayDynamic QRIS (Preferred): POS generates a unique QRIS payload per transaction via Midtrans Core API containing the exact gross_amount. The QR code is rendered on a secondary Customer Facing Display (CFD) or POS printer. Upon scanning and paying, Midtrans dispatches a webhook to automatically close the POS transaction.Static QRIS + Deep Linking: POS displays a fixed merchant QR. Cashier MUST manually verify incoming payment status via Midtrans Merchant Administration Portal (MAP) or real-time WebSocket notifications.B. Integrated EDC Terminal TriggeringPOS sends transaction payload to Midtrans EDC API / Integrated Terminal Gateway via LAN/Bluetooth.The EDC machine prompts the customer to Dip/Tap/Swipe card or enter PIN.The POS listens for the EDC execution response block (approval_code, masked_card).6. INVOICING, RECURRING & AUTOMATED BILLINGDynamic Invoice Expiry: Always set custom_expiry inside the charge payload matching order reservation constraints (e.g., flash sales = 15 minutes expiry; standard invoices = 24 hours expiry).Midtrans Subscription / Recurring API:Store tokenized card keys (saved_token_id) for 1-click / 2-click recurring payments.Set up automated billing schedules using Midtrans Subscription API (/v2/subscriptions).7. MIDTRANS INTEGRATION EDGE CASES & FAILURE MATRIXEdge Case ScenarioRisk / ThreatRequired System Mitigation RuleLost / Delayed WebhookCustomer pays successfully, but webhook fails to reach server due to network drop.Implement a Background Status Polling Worker (Cron Job) that queries GET /v2/{order_id}/status for all pending orders older than 15 minutes.Customer Pays Wrong Amount (Over/Under)Payment mismatch on manual bank transfers.Midtrans Virtual Accounts enforce exact-amount locking; reject non-matching manual bank transfers.Race Condition: User Cancels While PayingCustomer hits "Cancel Order" on web UI at the exact second they complete payment on GoPay.Check Midtrans Status API before executing local order cancellation. If Midtrans returns settlement, override cancel request and mark order as PAID.Refund Execution (Full / Partial)Customer requests order return.Trigger POST /v2/{order_id}/refund via server-side API. Update local ledger with REFUNDED status and post reversal accounting journal.Expired Order Payment AttemptCustomer scans QRIS code after expiry window.Midtrans automatically rejects payment on expired QRIS/VA codes. Local system MUST handle expire status by releasing locked inventory.8. FRONTEND IMPLEMENTATION: SNAP UI VS CUSTOM UIA. Handling Midtrans Snap UI (Quickest Setup)JavaScript// ✅ BENAR: Frontend Integration dengan Midtrans Snap JS
window.snap.pay(snapToken, {
  onSuccess: function(result) {
    // Redirect ke Halaman Sukses & Verifikasi via Backend Polling
    window.location.href = "/checkout/success?order_id=" + result.order_id;
  },
  onPending: function(result) {
    // Tampilkan Instruksi Pembayaran (Nomor VA / Cara Bayar)
    window.location.href = "/checkout/instructions?order_id=" + result.order_id;
  },
  onError: function(result) {
    // Tampilkan Pesan Gagal
    alert("Pembayaran Gagal: " + result.status_message);
  },
  onClose: function() {
    // User menutup popup tanpa menyelesaikan pembayaran
    console.log("Customer menutup popup Snap tanpa membayar.");
  }
});
B. Handling Custom UI (Core API)Frontend collects payment choice -> Sends request to Backend -> Backend executes Midtrans /v2/charge -> Backend returns raw payment details (va_number, qr_string, deeplink_url) -> Frontend renders custom native components.9. AGENT EXECUTION DIRECTIVESZero Hardcoded Keys: Server Keys and Client Keys MUST strictly originate from environment variables (.env).Mandatory Signature Verification: NEVER process incoming Webhook notifications without validating the SHA-512 signature key.Apply "Fix Terkecil yang Aman": Preserve payment status mapping, idempotency checks, and accounting ledger posting when modifying payment modules or checkout handlers.

