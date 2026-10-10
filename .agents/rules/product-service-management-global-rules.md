---
trigger: glob
globs: **/products/**/*, **/services/**/*, **/catalog/**/*, **/inventory/**/*, **/pricing/**/*, **/*Product*, **/*Service*, **/*SKU*, **/*Catalog*, **/*Inventory*
---

# GLOBAL PRODUCT & SERVICE MANAGEMENT ARCHITECTURE RULES (2026 STANDARDS)

## 1. SKU (STOCK KEEPING UNIT) STANDARDIZATION & FORMATTING

Systems MUST enforce a deterministic, human-readable, and machine-parsable SKU format across physical inventory and service offerings to eliminate catalog ambiguity.

```text
┌────────────────────────────────────────────────────────────────────────┐
│                        STANDARD SKU SEGMENTATION                       │
├───────────┬───────────┬───────────┬───────────────────┬────────────────┤
│ CATEGORY  │ SUB-CAT   │   BRAND   │ SPEC / VARIANT    │ SEQUENCE / ID  │
│  (3-Char) │  (3-Char) │  (3-Char) │  (3-6 Char)       │ (4-Digit Num)  │
├───────────┼───────────┼───────────┼───────────────────┼────────────────┤
│    ELE    │    PHO    │    SAM    │    S24-256-BLK    │      0042      │
└───────────┴───────────┴───────────┴───────────────────┴────────────────┘
             Example Result: ELE-PHO-SAM-S24256BLK-0042


A. SKU Generation RulesFormatting Constraints:MUST use UPPERCASE alphanumeric characters separated ONLY by hyphens (-).Banned Ambiguous Characters: Never use I, O, 1, 0, or L in random segment generation to prevent human reading errors on physical labels.Maximum length: 30 characters.Physical Goods SKU vs Service SKU Prefixing:Physical Products: Standard category prefixing (e.g., APP-TSH-UNI-BLK-0001).Service Offerings: Prefix with SRV- (e.g., SRV-MNT-AC-CLEAN-0001).Digital Products: Prefix with DIG- (e.g., DIG-SW-WIN11PRO-0001).Immutability Principle: Once an SKU is generated and associated with historical stock movements or financial ledgers, it MUST NEVER BE CHANGED or reassigned to another product.2. PRODUCT & SERVICE ATTRIBUTE MODELING ARCHITECTURECatalog systems MUST segregate physical inventory attributes from service and digital assets using a unified variant matrix model.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                   PRODUCT VS SERVICE CATALOG DUALITY                   │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│     PHYSICAL GOODS CATALOG        │               │       SERVICE CATALOG             │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • Physical Stock Inventory Count  │               │ • Zero Physical Stock Tracked     │
│ • Weight, Dimensions (Volumetric) │               │ • Duration / Service Time Window  │
│ • Unit of Measure (UOM) Conversion│               │ • Staff Skill & Resource Booking  │
│ • Multi-Barcode / EAN Binding     │               │ • Service Location Scope          │
│ • COGS Valuation (FIFO/Average)   │               │ • Service Level Agreement (SLA)   │
└───────────────────────────────────┘               └───────────────────────────────────┘
A. Variant Matrix Model (Parent-Child Hierarchy)Parent Product (Abstract): Contains shared metadata (Title, Description, Brand, Category, Base Media, Tax Class).Child Variant (Sellable Unit): Holds specific SKU, Price, Stock, Weight, Dimensions, Barcode, and specific Option Values (e.g., Size: XL, Color: Navy).B. Unit of Measure (UOM) Conversion EnginePhysical products MUST support multi-tier UOM conversions:$$\text{Base Unit (Pcs)} \xrightarrow{\times 12} \text{Inner Box (Lusin)} \xrightarrow{\times 20} \text{Master Carton (Karton)}$$Stock tracking MUST always persist in the Smallest Base Unit (e.g., Pieces).3. ADVANCED PRICING ENGINE & TIERING MECHANICSThe pricing engine MUST compute final checkout prices using a deterministic 5-layer hierarchy:PlaintextBase List Price ──► Customer Group / B2B Price ──► Quantity Tier / Wholesale ──► Promo / Discount ──► Dynamic Surge
A. Mathematical Order of Price CalculationBase Price: Default MSRP / Retail price.Tiered / Wholesale Pricing: Discount triggered by volume thresholds:PlaintextBuy 1 - 9   Pcs = Rp 100.000 / unit
Buy 10 - 49 Pcs = Rp  90.000 / unit
Buy >= 50   Pcs = Rp  80.000 / unit
Customer Group / B2B Override: Specific pricing rules mapped to client tiers (e.g., VIP Merchant, Distributor).Promotions & Campaign Coupons: Percentage or fixed amount discounts applied on top of tier prices.Tax Inclusion Logic:If tax_inclusive = true:$$\text{Net Price} = \frac{\text{Final Display Price}}{1 + \text{Tax Rate}}$$4. PRODUCT MEDIA & IMAGE PIPELINE STANDARDSTo maintain ultra-fast page load times and mobile performance, image assets MUST pass through an automated transformation pipeline upon upload.Plaintext[ Raw Upload (JPEG/PNG) ] ──► [ Stripper (Remove EXIF Metadata) ] ──► [ WebP / AVIF Converter ]
                                                                             │
                                                                             ▼
                                                  ┌──────────────────────────────────────────┐
                                                  │ RESPONSIVE BREAKPOINT GENERATOR          │
                                                  ├──────────────────────────────────────────┤
                                                  │ • Thumb  : 150x150px (Square Crop)       │
                                                  │ • Medium : 600x600px (Catalog Grid)      │
                                                  │ • Large  : 1200x1200px (Zoom Display)    │
                                                  └──────────────────────────────────────────┘
Image Pipeline Directives:Formats: Convert all uploaded raster images to WebP (default) or AVIF (next-gen).Max File Size Target: Thumbnails $< 15\text{ KB}$, Medium $< 80\text{ KB}$, Large Zoom $< 250\text{ KB}$.Storage Strategy: Store transformed assets in Cloud Object Storage (S3 / MinIO) and serve exclusively via CDN edge caching with Cache-Control: public, max-age=31536000, immutable.Mandatory Alt Text: Every product image MUST include auto-generated or custom alt_text containing the Product Title and Color/Variant for SEO and accessibility.5. HARDWARE SCANNER BINDING & MULTI-BARCODE SYSTEMSystems MUST bind multiple barcode inputs (Manufacturer EAN, Internal Barcode, Supplier Code) to a single child variant SKU.PHP// ✅ BENAR: Handler Pencarian Produk via Hardware Scanner (Mendukung Multi-Barcode)
public function findVariantByScannedCode(string $scannedCode): ProductVariant
{
    $cleanCode = trim($scannedCode);

    $variant = ProductVariant::query()
        ->where('sku', $cleanCode)
        ->orWhere('primary_barcode', $cleanCode)
        ->orWhereHas('supplierBarcodes', function ($query) use ($cleanCode) {
            $query->where('barcode', $cleanCode);
        })
        ->first();

    if (!$variant) {
        throw new ProductNotFoundException("Produk dengan barcode/SKU '{$cleanCode}' tidak ditemukan.");
    }

    return $variant;
}
Hardware Scanner Binding Rules:Instant POS Lookup: Searching by scanned code MUST utilize indexed columns (sku, primary_barcode) with response time $< 15\text{ms}$.Auto-Increment Mode: When scanning in POS / Stock Counting mode, scanning an already listed SKU MUST increment its quantity by $+1$ rather than appending a duplicate row.6. STOCK MOVEMENT ARCHITECTURE & INVENTORY VALUATIONAll physical inventory changes MUST be executed via an Immutable Stock Ledger (stock_movements).Plaintext[ Real-Time Stock Balance ] = Σ (Inbound Movement Quantities) - Σ (Outbound Movement Quantities)
A. Stock Movement Event TypesMovement CodeDirectionTrigger Source EventPURCHASE_RECEIPTIN (+)Goods Received Note (GRN) from Supplier Purchase Order.SALES_DEDUCTIONOUT (-)Completed POS / E-Commerce Sales Invoice.TRANSFER_OUTOUT (-)Stock Transfer initialized from Source Warehouse.TRANSFER_ININ (+)Stock Transfer received at Destination Warehouse.STOCK_ADJUSTMENTIN/OUTPhysical Audit Stock Opname variance correction.DAMAGE_WRITE_OFFOUT (-)Damaged, expired, or stolen goods removal.B. Inventory Valuation Method (Weighted Average COGS)Every inbound movement MUST recalculate the variant's Weighted Average Cost of Goods Sold (COGS):$$\text{New COGS} = \frac{(\text{Current Stock} \times \text{Old COGS}) + (\text{Inbound Qty} \times \text{Inbound Unit Price})}{\text{Current Stock} + \text{Inbound Qty}}$$7. DYNAMIC MERCHANDISING BADGES ENGINE ("BEST SELLER", "LOW STOCK")Merchandising badges MUST be calculated dynamically or via periodic background workers and cached in In-Memory Storage (Redis) to avoid runtime SQL aggregation penalties.PHP// ✅ BENAR: Evaluasi Badge Produk Dinamis
public function evaluateProductBadges(Product $product, int $currentStock, int$safetyStock): array
{
    $badges = [];

    // 1. Low Stock Badge
    if ($product->is_physical && $currentStock > 0 &&$currentStock <= $safetyStock) {$badges[] = ['code' => 'LOW_STOCK', 'label' => 'Stok Menipis', 'color' => 'warning'];
    }

    // 2. Out of Stock Badge
    if ($product->is_physical && $currentStock <= 0) {$badges[] = ['code' => 'OUT_OF_STOCK', 'label' => 'Habis', 'color' => 'danger'];
    }

    // 3. New Arrival Badge (Dibuat < 14 Hari)
    if ($product->created_at->diffInDays(now()) <= 14) {$badges[] = ['code' => 'NEW_ARRIVAL', 'label' => 'Baru', 'color' => 'info'];
    }

    // 4. Best Seller Badge (Diambil dari Redis Cached Top Sellers List)
    if (Redis::sismember('catalog:best_sellers_ids', $product->id)) {$badges[] = ['code' => 'BEST_SELLER', 'label' => 'Terlaris', 'color' => 'success'];
    }

    return $badges;
}
Merchandising Badge Logic:Best Seller Badge: Calculated by a nightly background job looking at the top 5% sales volume over a rolling 30-day window.Low Stock Badge: Triggered when current_physical_stock <= safety_stock_threshold.Fast Moving Badge: Triggered when Inventory Turnover Ratio exceeds category benchmarks.8. EDGE CASES & RESILIENCE MATRIXEdge Case ScenarioRisk / ProblemRequired System Mitigation RuleDeleting Variant with Existing StockGhost inventory in warehouse; Discrepancy in General Ledger.PROHIBIT deleting variants with stock > 0 or historical transactions. Soft-delete / Archive (is_active = false) instead.Supplier Barcode CollisionTwo different suppliers use the same barcode for different items.Store supplier barcodes in a supplier_barcodes table scoped by supplier_id. Prompt cashier to pick product if collision occurs.Simultaneous Stock DeductionTwo online buyers buy the last item at the exact same millisecond.Use Pessimistic Row Locking (FOR UPDATE) or Redis Atomic Decr (DECRBY). Reject second order with OUT_OF_STOCK.Service Purchase without Staff AvailabilityOverbooking service appointments.Verify real-time staff schedule calendar before confirming service booking.9. AGENT EXECUTION DIRECTIVESAlways enforce immutable stock ledger entries for any inventory movement.Ensure all product pricing calculations strictly follow the 5-layer pricing hierarchy.Apply the "Fix Terkecil yang Aman" principle: maintain SKU formats, image breakpoint structures, and barcode lookups when updating catalog or inventory modules.
