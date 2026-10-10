---
trigger: glob
globs: **/membership/**/*, **/loyalty/**/*, **/rewards/**/*, **/subscriptions/**/*, **/tiers/**/*, **/*Member*, **/*Loyalty*, **/*Point*, **/*Tier*, **/*Subscription*
---

# GLOBAL MEMBERSHIP, LOYALTY & PREMIUM SUBSCRIPTION ARCHITECTURE RULES (2026 STANDARDS)

## 1. MEMBERSHIP TAXONOMY & TIER PROGRESSION ENGINE

Systems MUST maintain a strict separation between **Free Tiered Loyalty Programs** (engagement-driven) and **Paid Premium Subscriptions** (monetization-driven), while supporting dynamic tier evaluation.

```text
┌────────────────────────────────────────────────────────────────────────┐
│                   MEMBERSHIP & SUBSCRIPTION DUALITY                    │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│  1. FREE TIERED LOYALTY PROGRAM   │               │   2. PAID PREMIUM SUBSCRIPTION    │
│ (Bronze -> Silver -> Gold -> Plat)│               │  (Amazon Prime / VIP Pass Style)  │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • Earned via Spend / Visits       │               │ • Entitlement via Recurring Fee   │
│ • Tier Qualification Rolling Window│              │ • Fixed Duration (Monthly/Annual) │
│ • Multipliers on Points Earning   │               │ • Instant Access to Exclusive Perks│
│ • Tier Demotion / Retention Rules │               │ • Dunning & Grace Period Cycle    │
└───────────────────────────────────┘               └───────────────────────────────────┘

A. Tier Progression & Evaluation Window RulesTier qualification MUST be calculated using one of two deterministic evaluation windows:Rolling Window (Preferred Default): Evaluates user activity over the last $N$ days (e.g., last 365 days rolling). Prevents sudden drop-offs on January 1st.Calendar Year Window: Evaluates activity from Jan 1 to Dec 31, resetting progress metrics annually with a 1-year retention grace period.B. Mathematical Tier Multiplier FormulaPoints earned for a transaction MUST scale dynamically based on the active member tier:$$\text{Earned Points} = \left\lfloor \frac{\text{Eligible Spend}}{\text{Base Conversion Unit}} \right\rfloor \times \text{Base Points} \times \text{Tier Multiplier}$$Example: Base Unit = Rp 10.000 (1 Point). Member spends Rp 150.000 on Gold Tier (1.5x Multiplier):$$\text{Earned Points} = \left\lfloor \frac{150.000}{10.000} \right\rfloor \times 1 \times 1.5 = 15 \times 1.5 = 22.5 \xrightarrow{\text{Floor}} 22 \text{ Points}$$2. POINTS ENGINE: EARNING, BURNING & EXPIRATION MECHANICSTo eliminate financial inflation and point manipulation, point engines MUST treat points as a restricted secondary currency.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                      POINTS LIFECYCLE STATE MACHINE                    │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
 [ Transaction Paid ] ──► [ PENDING POINTS (Locking Window e.g. 7 Days) ]
                                    │ (Post-Return Window Passed)
                                    ▼
                         [ ACTIVE / SPENDABLE POINTS ]
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
[ REDEEMED / BURNED ]                                [ EXPIRED / BROKEN ]
A. Pending Points Window (Anti-Fraud Guard)Points earned from a purchase MUST enter a PENDING state during the order return/cancellation window (e.g., 7 to 14 days).Points transition to ACTIVE only after the order status becomes COMPLETED and non-refundable.B. Point Redemption / Burning Valuation PolicySystems MUST enforce a minimum redemption threshold (e.g., minimum 100 points required before first redemption).Points CANNOT be redeemed for cash directly; redemption MUST apply as a store discount voucher or payment offset.Redemption Cap Guard: Points CANNOT cover more than $N\%$ of total order subtotal (e.g., max 50% order offset via points; remaining 50% MUST be paid with real currency).C. Expiration & FIFO Expiry Sweep EnginePoints MUST have an explicit Time-To-Live (TTL) (e.g., 12 months from earning date).FIFO Point Deduction: Redeeming points MUST deduct from the oldest active point batches first.Automated Expiry Batch Sweeper: A nightly background job MUST expire point batches where expires_at <= NOW() and post corresponding accounting ledger entries.3. ACCOUNTING & FINANCIAL RECOGNITION (IFRS 15 / PSAK 72 COMPLIANCE)Under international financial reporting standards (IFRS 15 / PSAK 72), loyalty points ARE NOT immediate marketing expenses. They represent a Separate Performance Obligation.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│              IFRS 15 DEFERRED REVENUE RECOGNITION FLOW                 │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
 1. Customer Purchases Goods (Rp 100.000) & Earns Points (Worth Rp 5.000)
 2. Transaction Price Allocated:
    - Immediate Sales Revenue = Rp 95.238
    - Deferred Loyalty Revenue (Liability) = Rp 4.762
 3. Upon Point Redemption / Expiration:
    - Transfer Deferred Revenue Liability ──► Recognized Sales Revenue
A. Standalone Selling Price (SSP) Allocation FormulaWhen a sale includes loyalty points, allocate the received cash $P_{\text{cash}}$ between product revenue $R_{\text{product}}$ and deferred point liability $L_{\text{points}}$:$$L_{\text{points}} = P_{\text{cash}} \times \left( \frac{\text{Fair Value of Points Earned}}{\text{Standalone Product Price} + \text{Fair Value of Points Earned}} \right)$$$$R_{\text{product}} = P_{\text{cash}} - L_{\text{points}}$$PHP// ✅ BENAR: Handler Pendaftaran Jurnal Akuntansi IFRS 15 / PSAK 72 saat Poin Diberikan
public function recordPointIssuanceJournal(string $orderId, int $cashReceived, int $pointFairValue): void
{
    $totalFairValue = $cashReceived + $pointFairValue;
    $deferredLiability = (int) round($cashReceived * ($pointFairValue / $totalFairValue));
    $immediateRevenue = $cashReceived - $deferredLiability;

    DB::transaction(function () use ($orderId, $cashReceived, $immediateRevenue, $deferredLiability) {
        // Debit Kas
        Ledger::post('10100', 'DEBIT', $cashReceived, $orderId, "Penerimaan Kas Sales #{$orderId}");

        // Kredit Pendapatan Penjualan Langsung
        Ledger::post('40100', 'CREDIT', $immediateRevenue, $orderId, "Pendapatan Penjualan #{$orderId}");

        // Kredit Kewajiban Poin Ditunda (Deferred Revenue Liability)
        Ledger::post('20500', 'CREDIT', $deferredLiability, $orderId, "Kewajiban Poin Ditunda IFRS15 #{$orderId}");
    });
}
4. PAID PREMIUM SUBSCRIPTIONS (RECURRING, DUNNING & PERKS)For paid membership plans (e.g., VIP Pass, Monthly Prime):Plaintext[ ACTIVE ] ──► (Billing Fails) ──► [ PAST_DUE (Dunning Grace Period 7 Days) ]
                                                │
                                 ┌──────────────┴──────────────┐
                                 ▼                             ▼
                    (Payment Retried & OK)           (All Retries Fail)
                                 │                             │
                                 ▼                             ▼
                            [ ACTIVE ]                  [ CANCELED ]
A. Subscription Lifecycle & Dunning ManagementRenewal Billing Window: Trigger automated renewal charges 24 to 48 hours BEFORE active subscription expiration.Grace Period & Dunning Retry: If renewal payment fails (e.g., insufficient funds), set status to PAST_DUE. Retry charging on Days 1, 3, and 7 using exponential backoff.Access During Grace Period: Keep premium perks ACTIVE during the grace period to prevent poor customer experience, but restrict cancellation/refund requests.B. Pro-Rata Upgrades & DowngradesWhen upgrading from Tier A (Rp 100.000/mo) to Tier B (Rp 300.000/mo) mid-cycle:$$\text{Unused Value Tier A} = \text{Price Tier A} \times \left( \frac{\text{Remaining Days}}{\text{Total Days in Cycle}} \right)$$$$\text{Immediate Upgrade Charge} = \text{Price Tier B} - \text{Unused Value Tier A}$$5. FRAUD PREVENTION, CONCURRENCY CONTROL & SECURITYTo prevent "Point Glitch" attacks (e.g., redeeming the same points twice in simultaneous checkout requests or fake referral loops):A. Atomic Point Reservation (Redis Lock Pattern)Point redemptions MUST lock and deduct balance atomically BEFORE database writes:TypeScript// ✅ BENAR: Atomic Point Lock dengan Redis Scripting
export async function redeemPointsAtomic(userId: string, pointsToRedeem: number): Promise<boolean> {
  const luaScript = `
    local currentPoints = tonumber(redis.call('get', KEYS[1]) or '0')
    if currentPoints >= tonumber(ARGV[1]) then
      redis.call('decrby', KEYS[1], ARGV[1])
      return 1
    else
      return 0
    end
  `;

  const key = `user:points:${userId}`;
  const result = await redis.eval(luaScript, 1, key, pointsToRedeem.toString());
  return result === 1;
}
B. Anti-Referral Fraud RulesDevice & IP Binding: Do NOT issue referral bonuses if Referral User and Referred User share the same IP address, Device Fingerprint, or Payment Method.Qualified Purchase Requirement: Referral bonus points MUST ONLY trigger after the referred user completes their first successful, non-refunded purchase above a minimum threshold.6. EDGE CASES & RESILIENCE MATRIXEdge Case ScenarioRisk / Glitch VectorRequired System Mitigation RuleFull Refund on Order with Spent PointsCustomer earns 100 points, spends them immediately on a 2nd order, then requests a refund on the 1st order.Negative Balance Policy: Deduct 100 points from balance (resulting in negative points e.g. -100). Next earned points automatically cover the deficit. Alternatively, deduct point cash value from cash refund.Refund Causes Tier DemotionCustomer reaches Gold Tier, gets benefits, then returns items dropping spend below Gold threshold.Demote user to Silver Tier immediately. Do NOT revoke physical perks already consumed, but revoke digital multipliers and active Gold vouchers.Simultaneous Redemption at POS & WebUser scans loyalty QR code at physical POS and clicks "Pay with Points" on web app simultaneously.Enforce strict database row locking (SELECT * FROM member_balances WHERE user_id = X FOR UPDATE) or Redis Mutex lock.Mass Point Expiration Day (Jan 1st)System crashes under heavy database write load during midnight expiry sweep.Process point expiration in batched background queues (chunk size 500) spread over off-peak hours with worker throttles.7. AGENT EXECUTION DIRECTIVESZero Unbound Point Mutations: Ensure all point earning, redemption, and expiration operations write an immutable record to the point_ledger table.Strict IFRS 15 / PSAK 72 Accounting: Always segregate deferred point liability from immediate sales revenue during checkout journal generation.Apply "Fix Terkecil yang Aman": Preserve tier progression formulas, points Redis locks, and subscription dunning cycles when updating membership or billing modules.
