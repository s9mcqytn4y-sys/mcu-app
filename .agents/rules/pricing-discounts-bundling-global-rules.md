---
trigger: glob
globs: **/pricing/**/*, **/discounts/**/*, **/promotions/**/*, **/bundles/**/*, **/packages/**/*, **/*Pricing*, **/*Discount*, **/*Promo*, **/*Bundle*, **/*Package*
---

# GLOBAL PRICING ENGINE, DISCOUNTS, PROMOTIONS & BUNDLING ARCHITECTURE RULES (2026 STANDARDS)

## 1. PRICING RESOLUTION ENGINE & ORDER OF OPERATIONS

To prevent calculation race conditions, double-discount glitches, and margin loss, the pricing engine MUST execute price resolution sequentially through a deterministic **8-Stage Pipeline**:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                     8-STAGE PRICING ENGINE PIPELINE                    │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
 1. Base List Price Resolution (Product / Variant MSRP)
 2. Customer Group / B2B Price Matrix Override (Retail / Wholesale / VIP)
 3. Volume & Tiered Quantity Discount (Wholesale Thresholds)
 4. Item-Level Catalog Promotion (Flash Sales / Item-level Discounts)
 5. Bundle / Package Price Allocation (Kitting & Proportional Revenue)
 6. Cart / Order-Level Coupon & Voucher Discount
 7. Tax Calculation (Inclusive / Exclusive Tax Base DPP)
 8. Currency & Cash Rounding Adjustment

Mathematical Order of Calculation FormulaFor a given cart item with quantity $Q$, base MSRP $P_{\text{base}}$, customer tier multiplier $M_{\text{tier}}$, item promo discount $D_{\text{item}}$, order coupon discount $D_{\text{order\_share}}$, tax rate $T$, and currency rounding $R$:$$\text{Effective Unit Price} = (P_{\text{base}} \times M_{\text{tier}}) - D_{\text{item}}$$$$\text{Line Subtotal} = \text{Effective Unit Price} \times Q$$$$\text{Line Net Amount (Pre-Tax)} = \text{Line Subtotal} - D_{\text{order\_share}}$$$$\text{Line Tax Amount} = \text{Line Net Amount} \times T \quad \text{(If Exclusive Tax)}$$$$\text{Final Line Total} = \text{Line Net Amount} + \text{Line Tax Amount} + R$$2. DISCOUNT & PROMOTION ENGINE TAXONOMYThe system MUST support 6 core discount types while maintaining strict stacking policies.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                      PROMOTION TAXONOMY MATRIX                         │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
    ┌───────────────────────────────┼───────────────────────────────┐
    ▼                               ▼                               ▼
┌───────────────────────┐ ┌───────────────────────┐ ┌───────────────────────┐
│ PERCENTAGE OFF (%)    │ │ FIXED AMOUNT OFF ($)  │ │ BUY X GET Y (BXGY)    │
├───────────────────────┤ ├───────────────────────┤ ├───────────────────────┤
│ • Cap limit mandatory │ │ • Cannot exceed       │ │ • Cheapest item free  │
│   (e.g., 20% max 50k) │ │   subtotal floor      │ │ • Auto-add or prompt  │
└───────────────────────┘ └───────────────────────┘ └───────────────────────┘
    ▲                               ▲                               ▲
    │                               │                               │
┌───────────────────────┐ ┌───────────────────────┐ ┌───────────────────────┐
│ FREE SHIPPING PROMO   │ │ TIERED SUBTOTAL PROMO │ │ FLASH SALE / QUOTA    │
├───────────────────────┤ ├───────────────────────┤ ├───────────────────────┤
│ • Max shipping subsidy│ │ • Spend 500k get 50k  │ │ • Time-window locked  │
│   cap                 │ │ • Spend 1m get 120k   │ │ • Atomic usage limit  │
└───────────────────────┘ └───────────────────────┘ └───────────────────────┘
A. Coupon Stacking PoliciesEvery promotion MUST define an explicit stacking policy rule:NON_STACKABLE (Default): Cannot be combined with any other coupon or item promo. The system selects the single discount that gives the customer the highest savings (Best Deal Rule).STACKABLE_CASCADE: Discounts apply sequentially (e.g., 10% catalog discount applied first, then a $5 coupon applied on the reduced amount).STACKABLE_PARALLEL: Percentages add up based on the original base price (e.g., 10% + 5% = 15% off original base price). DISCOURAGED unless explicitly approved by finance.B. Minimum Margin Protection Guard (Price Floor)Margin Guard Rule: NO discount or coupon combination is permitted to drop an item's selling price below its Cost Floor:$$\text{Minimum Allowed Price} = \text{Item COGS} \times (1 + \text{Min Margin \%})$$If a discount breaches the price floor, the system MUST automatically clamp the maximum discount amount to maintain the minimum margin.3. BUNDLING, KITTING & REVENUE ALLOCATION ARCHITECTUREA Bundle combines multiple distinct products sold together at a single unified price.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                   BUNDLE TYPES & INVENTORY BINDING                     │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│      1. FIXED BUNDLE (KITTING)    │               │    2. DYNAMIC / CUSTOM BUNDLE      │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • Pre-defined fixed items         │               │ • Mix & Match choices             │
│   (e.g., "Kamera + Lensa + Tripod")│              │   (e.g., "Pilih 3 Baju seharga 200k")│
│ • SKU Parent = Virtual Bundle SKU │               │ • Dynamic component selection     │
│ • Inventory = Tracked at child    │               │ • Dynamic pricing allocation      │
│   components                      │               │                                   │
└───────────────────────────────────┘               └───────────────────────────────────┘
A. Proportional Revenue Allocation Engine (Accounting & Tax Compliance)When a bundle is sold at a discounted package price $P_{\text{bundle}}$, financial ledgers MUST NOT record the components at $Rp\ 0$. The bundle price MUST be proportionally allocated to each component $i$ based on its standalone regular price $P_i$:$$A_i = P_{\text{bundle}} \times \left( \frac{P_i}{\sum_{j=1}^{n} P_j} \right)$$Where:$A_i = \text{Allocated Revenue for Item } i$$P_i = \text{Standalone Price of Item } i$$P_{\text{bundle}} = \text{Discounted Selling Price of the Bundle}$PHP// ✅ BENAR: Algoritma Proportional Revenue Allocation untuk Bundling
public function calculateBundleAllocation(int $bundlePrice, array $items): array
{
    $totalStandalonePrice = array_reduce($items, fn($sum, $item) => $sum + ($item['standalone_price'] * $item['qty']), 0);

    if ($totalStandalonePrice <= 0) {
        throw new InvalidArgumentException("Total harga standalone item bundel harus lebih dari 0.");
    }

    $allocatedTotal = 0;
    $result = [];
    $itemCount = count($items);

    foreach ($items as $index => $item) {
        $itemTotalStandalone = $item['standalone_price'] * $item['qty'];

        // Alokasi proporsional
        if ($index === $itemCount - 1) {
            // Item terakhir mengambil sisa pembulatan cent/rupiah untuk mencegah selisih total
            $allocatedRevenue = $bundlePrice - $allocatedTotal;
        } else {
            $allocatedRevenue = (int) round($bundlePrice * ($itemTotalStandalone / $totalStandalonePrice));
            $allocatedTotal += $allocatedRevenue;
        }

        $result[] = [
            'variant_id' => $item['variant_id'],
            'qty' => $item['qty'],
            'standalone_price' => $item['standalone_price'],
            'allocated_revenue_total' => $allocatedRevenue,
            'allocated_unit_price' => (int) round($allocatedRevenue / $item['qty'])
        ];
    }

    return $result;
}
B. Atomic Bundle Inventory Deduction RuleWhen a Bundle is purchased:The system MUST deduct physical inventory from EACH child component's stock ledger atomically.Maximum Available Bundle Stock:$$\text{Max Bundles Sellable} = \min \left( \left\lfloor \frac{\text{Available Stock of Component } i}{\text{Required Qty of Component } i \text{ in Bundle}} \right\rfloor \right)$$4. CONCURRENCY, QUOTA LOCKING & ANTI-GLITCH SECURITYTo prevent "Coupon Glitch" attacks where high-concurrency requests exploit race conditions to exceed voucher usage limits or flash sale quotas:Plaintext[ Incoming Checkout Request ] ──► [ Redis Atomic Quota Decr (DECRBY) ]
                                            │
                           ┌────────────────┴────────────────┐
                           ▼                                 ▼
                 [ Quota Remaining >= 0 ]         [ Quota Exceeded (< 0) ]
                           │                                 │
                           ▼                                 ▼
                (Proceed to DB Transaction)       (Rollback Redis & Reject HTTP 422)
A. Atomic Quota Lock PatternVoucher usage limits MUST be enforced in fast In-Memory Storage (Redis) using atomic operations (DECR / DECRBY) BEFORE executing slow relational database transactions.If the database transaction fails or time out, dispatch a compensating command (INCRBY) to restore the voucher quota in Redis.B. Single-Use Per Customer EnforcementEnforce unique compound database constraints or Redis lock keys: lock:coupon:<coupon_id>:user:<user_id>.5. RETURN, REFUND & PARTIAL CANCELLATION RULESHandling returns or partial order cancellations on discounted/bundled orders MUST follow strict financial adjustment rules:ScenarioRisk / Glitch VectorRequired System Mitigation RulePartial Return of Bundle ItemCustomer buys 3-item bundle for discount, then returns 1 item to keep discount on remaining 2.Policy Option A: Prohibit partial bundle returns (Require returning entire bundle).Policy Option B: Recalculate remaining items at full standalone prices and deduct difference from refund.Partial Return on Order with Subtotal Threshold CouponCustomer buys 1m to get 100k off, then returns 300k item (subtotal drops to 700k).Deduct the invalidated 100k coupon discount from the refunded item amount.Refund Calculation AmountRefunding original list price instead of net paid price.Refund amount MUST be calculated based strictly on Allocated Net Revenue ($A_i$), NEVER original list price ($P_i$).6. AGENT EXECUTION DIRECTIVESZero Over-Discounting: Never write pricing code that omits price floor checks or allows total discounts to exceed order subtotal.Enforce Proportional Revenue Allocation: Always allocate bundle prices across individual item components for accurate accounting and tax reporting.Apply "Fix Terkecil yang Aman": Maintain the 8-stage pricing pipeline, coupon concurrency locks, and tax base calculations when modifying pricing or promo modules.
