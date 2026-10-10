---
trigger: glob
globs: **/notifications/**/*, **/toasts/**/*, **/badges/**/*, **/push/**/*, **/channels/**/*, **/*Notification*, **/*Toast*, **/*Badge*, **/*Push*, **/*Flash*
---

# GLOBAL NOTIFICATION SYSTEM & USER FEEDBACK ARCHITECTURE RULES (2026 STANDARDS)

## 1. NOTIFICATION TAXONOMY & CHANNEL SELECTION MATRIX

Systems MUST categorize every user message and feedback UI element according to its urgency, persistence, and delivery channel:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                   NOTIFICATION & FEEDBACK TAXONOMY                     │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
    ┌───────────────────────────────┼───────────────────────────────┐
    ▼                               ▼                               ▼
┌───────────────────────┐ ┌───────────────────────┐ ┌───────────────────────┐
│ 1. TRANSIENT UI       │ │ 2. PERSISTENT IN-APP  │ │ 3. OUT-OF-APP CHANNELS│
│    FEEDBACK           │ │    UI & INBOX         │ │    (MULTI-CHANNEL)    │
├───────────────────────┤ ├───────────────────────┤ ├───────────────────────┤
│ • Toast Bar           │ │ • Unread Count Badges │ │ • Mobile Push (FCM)   │
│ • Snackbar            │ │ • In-App Notification │ │ • Web Push (VAPID)    │
│ • Flash Messages      │ │   Center / Inbox      │ │ • Email (Transactional│
│ • System Alert Banner │ │ • Inline Field Error  │ │ • WhatsApp / SMS (OTP)│
└───────────────────────┘ └───────────────────────┘ └───────────────────────┘

Channel Selection Decision MatrixFeedback PurposeUI / Channel TypeDuration / Auto-DismissInteractive Action?Short Operational SuccessToast BarAuto-dismiss ($3 - 5\text{s}$)No (Informational only)Action Confirmation + UndoSnackbarAuto-dismiss ($5 - 8\text{s}$)Yes (e.g., "BATALKAN / UNDO")Page Navigation FeedbackFlash MessageDismiss on next renderOptionalSystem Outage / Critical AlertSticky Top BannerPermanent until resolvedYes (e.g., "BACA DETAIL")Unread Updates IndicatorRed Badge / DotCleared on context viewTriggers navigation/drawerUrgent Account ActivityPush / WhatsApp / SMSOS Notification CenterDeep link to target screen2. TRANSIENT UI FEEDBACK RULES (TOAST, SNACKBAR, FLASH, BANNER)A. Toast Bar RulesNon-Blocking Overlay: Toasts MUST be floating overlays that do NOT block user interaction with underlying page controls (pointer-events: none on container, pointer-events: auto on toast card).Positioning Standard:Desktop: Top-right or Bottom-right corner.Mobile: Top-center or Bottom-center (above navigation bar).Stacking Limit: Maximum 3 visible toasts stacked at once. Excess toasts MUST enter a queue or replace the oldest toast.Duration Rules:SUCCESS / INFO: Auto-dismiss after $3000\text{ms} - 4000\text{ms}$.WARNING: Auto-dismiss after $5000\text{ms} - 6000\text{ms}$.ERROR: Auto-dismiss after $8000\text{ms}$ OR require explicit manual close ([X] button).B. Snackbar RulesPositioning: Bottom-center or Bottom-left of the viewport.Single Action Only: Snackbars MUST contain at most ONE action button (e.g., "URUNGKAN", "LIHAT", "COBA LAGI").Pause on Hover/Focus: Timer MUST pause when the user hovers over or focuses on the Snackbar card.C. Flash Message RulesStored temporarily in server session memory (e.g., Laravel $request->session()->flash(), Next.js cookie/query param) and consumed strictly on the immediate next HTTP response page load.3. PERSISTENT IN-APP NOTIFICATION CENTER & BADGESA. Numeric Badge Counter RulesDisplay Limit: Render exact numbers up to 99. For values $\ge 100$, display 99+.Instant Optimistic Decrement: Opening the notification drawer or viewing the target item MUST instantly decrement or clear the badge counter on the UI before waiting for the server API response.B. Notification Center / Inbox SchemaIn-app notifications MUST be stored in a dedicated database table (notifications):id (UUID v7)user_id (Recipient Identifier)type (Category / Event Name, e.g., ORDER_SHIPPED, PAYMENT_RECEIVED)title (Short Heading)body (Text Content)data_json (Deep link route, target entity ID, metadata)read_at (Timestamp, NULL = Unread)created_at (Timestamp)4. PUSH NOTIFICATION & OUT-OF-APP MULTI-CHANNEL ROUTINGWhen sending notifications outside the application UI (Mobile Push via FCM/APNs, Web Push via VAPID, Email, SMS, WhatsApp):Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                   MULTI-CHANNEL ROUTING ARCHITECTURE                   │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                      [ Event Triggered in System ]
                                    │
                                    ▼
                 [ Check User Notification Preferences ]
                                    │
          ┌─────────────────────────┼─────────────────────────┐
          ▼                         ▼                         ▼
┌───────────────────┐     ┌───────────────────┐     ┌───────────────────┐
│   CRITICAL TIER   │     │    HIGH TIER      │     │   LOW / PROMO TIER│
├───────────────────┤     ├───────────────────┤     ├───────────────────┤
│ • Send SMS / WA   │     │ • Send Push &     │     │ • Send Push ONLY  │
│ • Send Push       │     │   In-App Inbox    │     │   (If opted in)   │
│ • Bypass DND      │     │ • Respect DND     │     │ • Respect DND     │
└───────────────────┘     └───────────────────┘     └───────────────────┘
A. Payload Formatting ConstraintsTitle: Maximum 40 characters (Prevents truncation on mobile lock screens).Body: Maximum 120 characters.Deep Link Data Payload: MUST contain an explicit target URI route (e.g., {"click_action": "/orders/ORD-2026-88"}).B. User Preferences & Preference Center (Opt-In / Opt-Out)Users MUST have a settings panel to independently toggle channels per category:Order & Delivery Updates: [x] Push [x] Email [x] WhatsAppSecurity & Login Alerts: [x] Push [x] Email [x] WhatsApp (Locked Mandatory)Promotions & Marketing: [ ] Push [ ] Email [ ] WhatsApp5. PRIORITY MATRIX, FREQUENCY CAPPING & QUIET HOURS (DND)A. Notification Priority TiersPriority LevelExample EventsQuiet Hours (DND) Bypass?Retry PolicyCRITICALOTP, Security Breach, Payment Due ImmediateYES (Delivered 24/7 immediately)High Frequency (Immediate Retry)HIGHOrder Shipped, Booking Confirmation, Driver ArrivedNO (Queue until morning if DND)Exponential Backoff (3 Retries)MEDIUMSocial Mention, New Comment, Direct MessageNO1 RetryLOWWeekly Digest, Promotional Discount, Loyalty ReminderNONo Retry on FailureB. Frequency Capping & Quiet Hours (Do Not Disturb)Frequency Cap: Maximum 3 promotional push notifications per user per 24 hours.Quiet Hours Policy: Non-critical notifications generated between 22:00 and 07:00 (Local User Time) MUST be held in a background queue and dispatched at 08:00 AM.6. ACCESSIBILITY (WCAG 2.2 AA) & ERGONOMICS RULESA. ARIA Live Region Binding (Mandatory)Every dynamic notification UI component MUST declare WCAG-compliant ARIA live region attributes:HTML<!-- ✅ BENAR: Non-urgent Toast / Snackbar (Screen reader reads when idle) -->
<div role="status" aria-live="polite" aria-atomic="true" class="toast">
  Pesanan berhasil disimpan ke draf.
</div>

<!-- ✅ BENAR: Critical Error Alert (Screen reader interrupts immediately) -->
<div role="alert" aria-live="assertive" aria-atomic="true" class="toast-error">
  Gagal memproses pembayaran. Koneksi terputus.
</div>
B. Keyboard Accessibility & DismissalPressing the Escape key MUST immediately close the active focused Toast or Snackbar.Interactive Snackbars MUST allow navigating to their action button using standard Tab key movement.7. TECHNICAL ARCHITECTURE & QUEUE MANAGEMENTZero Blocking Thread Execution: Out-of-app notification dispatches (FCM, Web Push, SMTP Email, WhatsApp API) MUST NEVER run inside the synchronous HTTP request/response thread.Queue Worker Dispatch: Always dispatch notifications to an asynchronous queue pipeline (e.g., Redis Queue, RabbitMQ, SQS):PHP// ✅ BENAR: Pengiriman Notifikasi Asinkron via Job Queue di Laravel
public function sendOrderShippedNotification(Order $order): void
{
    // Dispatch ke background queue worker
    SendOrderShippedJob::dispatch($order)
        ->onQueue('notifications')
        ->delay(now()->addSeconds(2));
}
8. EDGE CASES & RESILIENCE MATRIXEdge Case ScenarioRoot Cause / Threat VectorRequired System Mitigation RuleOffline Client DeviceDevice disconnected from internet when Toast/Push triggers.Queue local in-app notification in IndexedDB/SQLite. Display "Koneksi Terputus" banner. Sync upon reconnection.Permission Denied for PushCustomer blocked browser/mobile push permissions.Gracefully fall back to In-App Toast & Email. NEVER spam push permission prompts repeatedly.Duplicate Notification DispatchQueue worker retries job after timeout, causing duplicate emails/SMS.Enforce Idempotency Key (notification_event_id). Mark notification as processed before external API call.Mass Broadcast Outage AlertBroadcasting system outage alert to 100,000 users simultaneously.Process broadcast in batched chunks (e.g., 500 users per worker batch) with rate-limiting to prevent crashing mail/push servers.9. AGENT EXECUTION DIRECTIVESZero Blocking External Calls: Ensure all external notification dispatches (Push, Email, WhatsApp) are handled asynchronously via background queue workers.Enforce WCAG 2.2 Accessibility: Always generate dynamic UI notifications with appropriate aria-live and role attributes.Apply "Fix Terkecil yang Aman": Preserve notification queue structures, user preference checks, and frequency cap limits when modifying feedback or messaging modules.
