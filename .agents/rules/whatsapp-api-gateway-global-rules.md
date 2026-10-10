---
trigger: always_on
---

# GLOBAL WHATSAPP API GATEWAY, BOT & INTERACTIVE MESSAGING RULES (2026 STANDARDS)

## 1. WHATSAPP GATEWAY ARCHITECTURE DUALITY

Systems MUST define an explicit integration strategy based on whether they utilize the **Official Meta WhatsApp Cloud API** or a **Custom/Self-Hosted Gateway Engine (Baileys / WPPConnect / Node-WA)**:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                     WHATSAPP ARCHITECTURE DUALITY                      │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│ 1. OFFICIAL META CLOUD API        │               │ 2. CUSTOM / SELF-HOSTED GATEWAY   │
│   (Graph API v20.0+)              │               │   (Baileys / WPPConnect / Web-WA) │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • Zero Ban Risk (Official BSP)    │               │ • High Ban Risk if Spammed        │
│ • Requires Meta Template Approval │               │ • Send Any Message Instantly      │
│ • Strict 24-Hour Customer Window  │               │ • Session Auth via QR Code / Pair │
│ • Per-Conversation Meta Pricing   │               │ • Requires Anti-Ban Rate Limiting │
└───────────────────────────────────┘               └───────────────────────────────────┘

A. Official Meta Cloud API DirectivesBase Endpoint: https://graph.facebook.com/v20.0/{PHONE_NUMBER_ID}/messagesAuthentication: Bearer Token via WHATSAPP_META_TOKEN from environment variables.24-Hour Customer Service Window Rule:Business-Initiated Messages (Outbound outreach, OTP, Invoices, Delivery updates): MUST use a pre-approved Message Template (HSM - Highly Structured Message).User-Initiated Messages (Customer texts first): Opens a 24-hour session window where free-form text, media, interactive buttons, and native flows can be sent without pre-approval.B. Custom / Self-Hosted Gateway Directives (Baileys Engine)Session Auth Storage: Store multi-device auth credentials (auth_info_baileys) in encrypted storage or Redis.Auto-Reconnection State Engine: Implement automatic socket reconnect loops with exponential backoff if WhatsApp disconnects (DisconnectReason.connectionClosed or loggedOut).Pairing Code Fallback: Support 8-digit Phone Pairing Code as an alternative to QR scanning for headless server environments.2. MESSAGE TEMPLATES (HSM), VARIABLES & INTERACTIVE CTA BUTTONSTo maximize engagement and conversion, messages MUST utilize structured interactive UI components rather than plain unformatted text.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                   INTERACTIVE MESSAGE COMPONENTS                       │
├────────────────────────────────────────────────────────────────────────┤
│ 1. QUICK REPLY BUTTONS : Up to 3 quick action buttons (Max 20 chars)   │
│ 2. CTA URL BUTTON      : Dynamic Link (e.g., "Bayar Sekarang ->")      │
│ 3. CTA CALL BUTTON     : Direct Phone Call trigger                     │
│ 4. LIST MESSAGES       : Select Menu with up to 10 options             │
│ 5. NATIVE FLOWS        : In-app interactive form fields inside WhatsApp│
└────────────────────────────────────────────────────────────────────────┘
A. Template Category & Variable MappingMeta template submissions MUST be categorized accurately:AUTHENTICATION: OTP codes with single-click copy buttons.UTILITY: Order updates, invoices, Biteship tracking AWB, Midtrans payment links.MARKETING: Promotional broadcasts, offers, loyalty reminders.B. Interactive Payload Construction Engine (Node.js Example)TypeScript// ✅ BENAR: Mengirimkan Pesan Interaktif CTA & Quick Reply (Meta Cloud API Standard)
export async function sendPaymentCtaLink(
  recipientPhone: string,
  customerName: string,
  invoiceId: string,
  paymentUrl: string
) {
  const payload = {
    messaging_product: "whatsapp",
    recipient_type: "individual",
    to: recipientPhone,
    type: "template",
    template: {
      name: "payment_reminder_cta",
      language: { code: "id" },
      components: [
        {
          type: "body",
          parameters: [
            { type: "text", text: customerName },
            { type: "text", text: invoiceId }
          ]
        },
        {
          type: "button",
          sub_type: "url",
          index: "0",
          parameters: [
            { type: "text", text: paymentUrl.replace("[https://domain.com/pay/](https://domain.com/pay/)", "") }
          ]
        }
      ]
    }
  };

  return await httpPostToMeta(payload);
}
3. AUTOMATION ENGINE, CONVERSATION STATE & HUMAN HANDOFFWhatsApp automation MUST be managed using a deterministic Finite State Machine (FSM).Plaintext[ Incoming Message ] ──► [ Check 24h Window ] ──► [ Active Bot FSM State? ]
                                                          │
                        ┌─────────────────────────────────┴─────────────────────────────────┐
                        ▼                                                                   ▼
         [ State = AUTOMATED_BOT ]                                             [ State = HUMAN_AGENT ]
                        │                                                                   │
        ┌───────────────┴───────────────┐                                         (Route to Live Agent
        ▼                               ▼                                          Inbox Dashboard)
[ Intent: Keyword / FAQ ]    [ Intent: Complex / Unresolved ]
        │                               │
        ▼                               ▼
(Execute Auto-Response)      (Trigger Human Handoff)
A. Human Agent Handoff ProtocolWhen a user types keywords like "OPERATOR", "CS", "BANTUAN", or when an AI bot fails intent detection 2 consecutive times, the system MUST:Transition user FSM state from BOT to HUMAN_AGENT.Mute automatic bot responses for that user.Dispatch a real-time notification to the Agent Support Dashboard (WebSockets).Auto-resume bot control ONLY if no agent responds after 30 minutes or if the agent explicitly clicks "End Conversation".4. ANTI-BAN, RATE LIMITING & ACCOUNT HEALTH GUARDRAILSTo prevent account suspension—especially when using Custom Gateways (Baileys/WPPConnect)—systems MUST enforce strict human emulation and throttling protocols.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                   CUSTOM GATEWAY ANTI-BAN GUARDRAILS                   │
├────────────────────────────────────────────────────────────────────────┤
│ • Random Message Delay   : 2000ms - 5000ms jitter between messages     │
│ • Typing Presence Simulation: Send `composing` status for 1 - 3 seconds│
│ • Daily Sending Cap      : Gradual Warm-Up Schedule                    │
│ • Mandatory Opt-Out      : "Ketik STOP untuk berhenti berlangganan"    │
└────────────────────────────────────────────────────────────────────────┘
A. Number Warm-Up Schedule (New Custom Gateway Numbers)Week 1: Maximum 50 outbound messages / day.Week 2: Maximum 200 outbound messages / day.Week 3: Maximum 1,000 outbound messages / day.Week 4+: Unlocked based on positive engagement ratio (received vs sent ratio $> 0.3$).B. Human Emulation Code Pattern (Baileys Example)JavaScript// ✅ BENAR: Mengirim Pesan dengan Simulasi Mengetik & Random Delay (Anti-Ban)
async function sendHumanizedWaMessage(sock, jid, textContent) {
  // 1. Tampilkan status "Mengetik / Composing"
  await sock.sendPresenceUpdate('composing', jid);

  // 2. Jeda acak (Delay 2 - 4 Detik) membedakan dari bot spammer
  const randomDelay = Math.floor(Math.random() * 2000) + 2000;
  await new Promise(resolve => setTimeout(resolve, randomDelay));

  // 3. Hentikan status mengetik
  await sock.sendPresenceUpdate('paused', jid);

  // 4. Kirim Pesan
  return await sock.sendMessage(jid, { text: textContent });
}
5. INTEGRATIONS & WEBHOOK PROCESSING ENGINEA. Webhook Signature Verification (X-Hub-Signature-256)Meta Cloud API dispatches HTTP Webhooks for incoming messages and delivery status updates. Webhooks MUST be verified against your APP_SECRET using HMAC SHA-256:PHP// ✅ BENAR: Verifikasi Signature Webhook WhatsApp Meta di Backend (Laravel)
public function verifyMetaWebhookSignature(Request $request, string$appSecret): bool
{
    $signature =$request->header('X-Hub-Signature-256');
    if (!$signature) return false;

    $expectedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(),$appSecret);
    return hash_equals($expectedSignature,$signature);
}
B. Message Delivery Status TrackingStore status updates in the whatsapp_message_logs table matching status callbacks:$$\text{Status Chain}: \text{sent} \longrightarrow \text{delivered} \longrightarrow \text{read} \quad (\text{or } \text{failed})$$If status = 'failed', parse error codes (e.g., Error 131026: Receiver number not registered on WhatsApp) and flag the customer record as invalid.6. EDGE CASES & RESILIENCE MATRIXEdge Case ScenarioRoot CauseRequired System Mitigation RuleMeta Template RejectedContent violates promotional or formatting rules.Maintain fallback plain text template; Resubmit with corrected category in Meta Business Manager.Custom Gateway Session DisconnectedPhone lost internet connection or WhatsApp web session logged out.Trigger automated socket reconnect loop; If unrecoverable, notify admin via Email/SMS to re-scan QR Code.Customer Opts Out ("STOP")Customer requests to stop receiving broadcast messages.Immediately insert phone number into whatsapp_blacklists table. Block future marketing broadcasts automatically.Webhook High Concurrency SpikeThousands of incoming messages during a broadcast campaign.Return HTTP 200 OK instantly to Meta/Gateway within $2\text{ seconds}$; Push raw webhook payload into background Redis Queue (whatsapp-incoming-queue) for processing.7. AGENT EXECUTION DIRECTIVESZero Hardcoded Secrets: Access Tokens, App Secrets, and Webhook Verify Tokens MUST strictly originate from environment variables (.env).Enforce Anti-Ban Throttling: Never generate broadcast scripts without incorporating random delays, typing presence updates, and opt-out clauses.Apply "Fix Terkecil yang Aman": Preserve webhook signature checks, 24-hour window validations, and FSM conversation states when modifying messaging or bot modules.
