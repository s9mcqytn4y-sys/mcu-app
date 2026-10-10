---
trigger: glob
globs: **/mail/**/*, **/emails/**/*, **/mailer/**/*, **/smtp/**/*, **/*Mail*, **/*Email*, **/*Mailer*, **/*Mailable*
---

# GLOBAL SMTP EMAIL, TRANSACTIONAL MAILER & DELIVERABILITY RULES (2026 STANDARDS)

## 1. ARCHITECTURE DUALITY & PROVIDER SELECTION

Systems MUST enforce a strict physical and logical separation between **Transactional Emails** (critical operational messages) and **Marketing / Bulk Emails** (promotional outreach) to protect domain reputation and deliverability rates.

```text
┌────────────────────────────────────────────────────────────────────────┐
│                   EMAIL DELIVERY ARCHITECTURE DUALITY                  │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│ 1. TRANSACTIONAL EMAIL ENGINE     │               │ 2. MARKETING / BULK EMAIL ENGINE  │
│   (Postmark, AWS SES, Resend)     │               │   (Brevo, Mailchimp, SendGrid)    │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • Subdomain: `txn.domain.com`     │               │ • Subdomain: `mkt.domain.com`     │
│ • High Priority, Zero Latency     │               │ • Batched / Scheduled Queue       │
│ • OTP, Password Reset, Invoices   │               │ • Newsletters, Promotions, Promos │
│ • 100% Delivery Rate Target       │               │ • Mandatory Unsubscribe Headers   │
└───────────────────────────────────┘               └───────────────────────────────────┘
Architectural Directives:IP / Subdomain Isolation Rule: NEVER send marketing/bulk campaigns using the same IP pool or subdomain assigned to transactional emails. A spam flag on marketing emails MUST NOT compromise critical transactional messages like OTPs or invoice receipts.Provider Protocol: Prefer high-speed HTTP REST APIs (AWS SES v2, Postmark, Resend API) over legacy synchronous SMTP sockets (port 587 / 25) to eliminate TCP connection overhead in high-concurrency environments.2. DOMAIN AUTHENTICATION & DELIVERABILITY PROTOCOLS (MANDATORY)Every sending domain MUST configure and maintain 4 core cryptographic and policy protocols. Messages missing these headers WILL be rejected or junked by major Inbox Providers (Gmail, Outlook, Yahoo).Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                    FOUR PILLARS OF EMAIL AUTHENTICATION                │
├────────────────────────────────────────────────────────────────────────┤
│ 1. SPF (Sender Policy Framework) : Whitelists authorized sending IPs  │
│ 2. DKIM (DomainKeys Identified)  : 2048-bit RSA Cryptographic Signature│
│ 3. DMARC (Policy Enforcer)       : Defines action on SPF/DKIM fail     │
│ 4. BIMI (Brand Identification)   : Renders verified brand logo in Inbox│
└────────────────────────────────────────────────────────────────────────┘
Mandatory DNS Record Standards:SPF Record: v=spf1 include:amazonses.com include:spf.postmarkapp.com ~allDKIM Record: Minimum 2048-bit RSA Key or Ed25519. Enable automatic DKIM key rotation every 180 days.DMARC Record:Plaintextv=DMARC1; p=reject; rua=mailto:dmarc-reports@domain.com; ruf=mailto:dmarc-forensics@domain.com; pct=100
Requirement: p=reject or p=quarantine. Policy p=none is PERMITTED ONLY during initial 14-day setup monitoring.Reverse DNS (PTR Record): Sending IP addresses MUST resolve symmetrically back to the sending hostname (mail.domain.com).3. EMAIL TEMPLATING, HTML COMPATIBILITY & RENDERING STANDARDSEmail rendering engines (Outlook, Gmail Mobile, Apple Mail) use fragmented HTML/CSS standards. All templates MUST strictly adhere to email-safe markup guidelines.A. HTML Layout & Styling RulesTable-Based Layouts: Use HTML <table> structures for layout positioning. Modern CSS Flexbox and Grid ARE NOT reliably supported across legacy Outlook clients.Inlined CSS: All CSS styles MUST be explicitly inlined into HTML elements prior to dispatch (e.g., via Juice / Premailer or Tailwind for Email engines).Max Container Width: Set email body container width to $600\text{px}$ centered (margin: 0 auto;).B. Payload Size Limit & Gmail Clipping GuardHTML Payload Ceiling: The raw HTML string size MUST NOT exceed $102\text{ KB}$. If an email exceeds $102\text{ KB}$, Gmail will clip the message ([Message clipped] View entire message), breaking tracking pixels and unsubscribe links.C. Multipart / Alternative MandateEVERY HTML email dispatch MUST include a clean, plain-text fallback counterpart (multipart/alternative mime type) for screen readers and low-bandwidth terminals.HTML<!-- ✅ BENAR: Template Structure dengan Preheader Text & Inlined CSS -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Faktur Pembayaran #INV-2026-001</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f8; font-family: Arial, sans-serif;">
  <!-- Hidden Preheader Text (Inbox Snippet) -->
  <div style="display: none; max-height: 0px; overflow: hidden;">
    Pembayaran Anda sebesar Rp 150.000 telah kami terima. Unduh faktur lengkap di sini.
  </div>

  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
      <td align="center" style="padding: 20px 0;">
        <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 8px; padding: 30px;">
          <tr>
            <td>
              <h1 style="color: #111827; font-size: 20px; margin-bottom: 16px;">Terima Kasih Atas Pembayaran Anda!</h1>
              <p style="color: #4b5563; font-size: 14px; line-height: 1.5;">Faktur resmi untuk pesanan <strong>#INV-2026-001</strong> telah diterbitkan.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
4. ASYNCHRONOUS QUEUEING, RETRY & THROTTLING ENGINEZero Synchronous HTTP Mail Calls: NEVER invoke SMTP transmission inside synchronous HTTP request/response loops. Always dispatch email payloads to background Queue Workers (e.g., Redis Queue, RabbitMQ, Laravel Horizon).Rate-Limiting & Burst Throttling: Configure queue workers to respect provider sending limits (e.g., AWS SES Sandbox = 14 msgs/sec; Postmark = 500 msgs/minute burst limit).PHP// ✅ BENAR: Dispatch Email via Background Queue Job dengan Headers RFC 8058
namespace App\Jobs;

use App\Mail\InvoiceReceiptMailable;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(public int $invoiceId) {}

    public function backoff(): array
    {
        return [10, 60, 300]; // Exponential Backoff Jeda Retry
    }

    public function handle(): void
    {
        $invoice = Invoice::with('customer')->find($this->invoiceId);
        if (!$invoice) return;

        Mail::to($invoice->customer->email)
            ->send(new InvoiceReceiptMailable($invoice));
    }
}
5. BOUNCE, COMPLAINT & SUPPRESSION LIST ENGINESystems MUST automatically parse and act upon delivery feedback loop webhooks (SNS / Postmark Webhooks) to prevent sending emails to invalid addresses.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                   BOUNCE & COMPLAINT HANDLING ENGINE                   │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│     HARD BOUNCE / SPAM COMPLAINT  │               │           SOFT BOUNCE             │
│ (Invalid Email / Domain / Spam)   │               │   (Inbox Full / Server Busy)      │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • ACTION: IMMEDIATE SUPPRESSION   │               │ • ACTION: RETRY SCHEDULER         │
│ • Insert email to `suppressions`  │               │ • Retry up to 3 times over 24h    │
│ • Block all future dispatches     │               │ • If still failing, convert to    │
│   automatically                   │               │   Hard Bounce                     │
└───────────────────────────────────┘               └───────────────────────────────────┘
One-Click Unsubscribe Headers (RFC 8058 Standard)All marketing and non-critical notification emails MUST include mandatory RFC 8058 headers for single-click inbox unsubscriptions:HTTPList-Unsubscribe: [https://domain.com/email/unsubscribe?token=abc123xyz](https://domain.com/email/unsubscribe?token=abc123xyz), <mailto:unsubscribe@domain.com?subject=unsubscribe>
List-Unsubscribe-Post: List-Unsubscribe=One-Click
6. EDGE CASES & RESILIENCE MATRIXEdge Case ScenarioRoot Cause / Risk VectorRequired System Mitigation RulePrimary Provider OutageAWS SES or Postmark service downtime.Implement Mailer Failover Adapter. Automatically switch to secondary SMTP provider (e.g., SES $\rightarrow$ Resend) if primary fails 5 consecutive times.Gmail Clipping TriggeredEmail HTML size $> 102\text{ KB}$ due to redundant CSS.Run CSS Inliner and HTML Minifier in build step. Enforce automated test checking raw payload size $< 100\text{ KB}$.Sending to Suppressed EmailDispatching mail to a previously hard-bounced address.Pre-filter recipient email against local suppression_lists table BEFORE queueing mail job.Spam Trap ExposureScraping unverified list leads to honeypot addresses.Enforce Double Opt-In (DOI) for newsletters and verify email MX records via DNS lookup prior to registration.Delayed OTP DeliveryTransactional queue blocked by marketing bulk job.Use dedicated high-priority queue for OTP/Reset Password emails, separated from standard notification queues.7. AGENT EXECUTION DIRECTIVESZero Hardcoded Mail Credentials: SMTP hostnames, ports, usernames, and passwords MUST strictly originate from environment variables (.env).Enforce Multipart Alternative: Always generate both HTML and Plain-Text rendering formats for every email template.Apply "Fix Terkecil yang Aman": Preserve SPF/DKIM headers, bounce suppression filters, and async queue structures when modifying mail or notification modules.
