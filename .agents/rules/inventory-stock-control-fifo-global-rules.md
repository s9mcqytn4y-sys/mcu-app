---
trigger: glob
globs: **/inventory/**/*, **/stock/**/*, **/warehouse/**/*, **/valuation/**/*, **/*Stock*, **/*Inventory*, **/*Valuation*, **/*Fifo*
---

# GLOBAL INVENTORY MANAGEMENT, STOCK CONTROL & FIFO VALUATION RULES (2026 STANDARDS)

## 1. RETAIL & GENERAL BUSINESS INVENTORY REQUIREMENT MATRIX

Systems MUST deliver distinct capabilities tailored to the specific demands of Retail B2C, Wholesale, and Manufacturing/Services:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                     INVENTORY SYSTEM REQUIREMENTS                      │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
    ┌───────────────────────────────┼───────────────────────────────┐
    ▼                               ▼                               ▼
┌───────────────────────┐ ┌───────────────────────┐ ┌───────────────────────┐
│ RETAIL B2C & OMNI     │ │ WHOLESALE & B2B       │ │ MANUFACTURING & SRV   │
├───────────────────────┤ ├───────────────────────┤ ├───────────────────────┤
│ • POS High-Speed Sync │ │ • Multi-Warehouse Bins│ │ • Bill of Materials   │
│ • Variant Matrix      │ │ • Bulk UOM Conversions│ │   (BOM / Kitting)     │
│ • Safety Stock & ROP  │ │ • Landed Cost Engine  │ │ • Work In Progress    │
│ • Shrinkage & Loss    │ │ • Consignment Stock   │ │   (WIP Tracking)      │
│ • Serial / Batch /    │ │ • Reorder Automation  │ │ • Spare Parts & Cons- │
│   Expiry (FEFO)       │ │ • Backorder Queues    │ │   umables (MRO)       │
└───────────────────────┘ └───────────────────────┘ └───────────────────────┘


Comprehensive Functional ChecklistMulti-Warehouse & Bin Location: Track stock across physical stores, central distribution centers (DC), and specific shelf/bin locations (WH01-Z02-A03-S01-B05).Real-Time Omni-Channel Stock Allocation: Reserve stock instantly across POS, E-Commerce, and B2B portals to prevent double-selling.Automated Replenishment: Compute Reorder Points (ROP), Safety Stock, and Economic Order Quantity (EOQ) dynamically.Valuation Layer Engine: Track Cost of Goods Sold (COGS) and inventory asset value strictly via FIFO, FEFO, or Weighted Average.Traceability & Compliance: Serial Number tracking for electronics/high-value assets and Batch/Expiry tracking for FMCG, food, and pharmaceuticals.Audit & Reconciliation: Support Blind Stock Opname, Cycle Counting, and automated posting of Inventory Shrinkage/Loss.2. STOCK VALUATION ENGINES (FIFO, FEFO & WEIGHTED AVERAGE)The system MUST enforce explicit stock valuation engines. Under international accounting standards (IFRS / PSAK 14), LIFO IS STRICTLY BANNED.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                        FIFO LAYER (FIRST-IN, FIRST-OUT)                │
├────────────────────────────────────────────────────────────────────────┤
│ Layer 1 (Oldest) : 10 Pcs @ Rp 10.000  ──► [ DEDUCTED FIRST ON SALE ]   │
│ Layer 2          : 20 Pcs @ Rp 12.000                                  │
│ Layer 3 (Newest) : 15 Pcs @ Rp 11.500                                  │
└────────────────────────────────────────────────────────────────────────┘
A. FIFO (First-In, First-Out) Algorithm & Layering RulesConcept: Items received first are sold first. COGS reflects historical cost of the oldest available batch layers.Data Model: Every inbound stock movement (PURCHASE_RECEIPT, PRODUCTION_OUTPUT) creates an immutable row in inventory_fifo_layers.PHP// ✅ BENAR: Algoritma Pengurangan Stok & Kalkulasi COGS Berbasis FIFO Layer
public function deductStockFifo(int $variantId, int $warehouseId, int $qtyToDeduct): FifoDeductionResult
{
    return DB::transaction(function () use ($variantId, $warehouseId, $qtyToDeduct) {
        // Ambil layer stok tertua yang masih memiliki sisa kuantitas (Locking FOR UPDATE)
        $layers = InventoryFifoLayer::query()
            ->where('variant_id', $variantId)
            ->where('warehouse_id', $warehouseId)
            ->where('remaining_qty', '>', 0)
            ->orderBy('received_at', 'asc')
            ->lockForUpdate()
            ->get();

        $totalAvailable = $layers->sum('remaining_qty');
        if ($totalAvailable < $qtyToDeduct) {
            throw new InsufficientStockException("Stok tidak mencukupi untuk FIFO. Dibutuhkan: {$qtyToDeduct}, Tersedia: {$totalAvailable}");
        }

        $remainingNeed = $qtyToDeduct;
        $totalCogs = 0.0;
        $deductedLayers = [];

        foreach ($layers as $layer) {
            if ($remainingNeed <= 0) break;

            $takeQty = min($layer->remaining_qty, $remainingNeed);
            $layerCogs = $takeQty * $layer->unit_cost;
            $totalCogs += $layerCogs;

            // Update sisa kuantitas pada layer FIFO
            $layer->remaining_qty -= $takeQty;
            $layer->save();

            $remainingNeed -= $takeQty;
            $deductedLayers[] = [
                'layer_id' => $layer->id,
                'qty' => $takeQty,
                'unit_cost' => $layer->unit_cost,
                'total_cogs' => $layerCogs
            ];
        }

        return new FifoDeductionResult(
            totalQty: $qtyToDeduct,
            totalCogs: $totalCogs,
            averageCogsPerUnit: $totalCogs / $qtyToDeduct,
            deductedLayers: $deductedLayers
        );
    });
}
B. FEFO (First-Expired, First-Out) PolicyMANDATORY for perishable items, food, beverages, cosmetics, and pharmaceuticals.Sorts inventory layers strictly by expiration_date ASC rather than received_at ASC.C. Moving Weighted Average MethodRecalculates average unit cost upon every inbound receipt:$$\text{New Average Cost} = \frac{(\text{Existing Stock} \times \text{Current Avg Cost}) + (\text{Inbound Qty} \times \text{Inbound Unit Cost})}{\text{Existing Stock} + \text{Inbound Qty}}$$3. STOCK CONTROL, REPLENISHMENT & MIN-MAX FORMULASSystems MUST automatically calculate stock replenishments using standard operational research formulas.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                   STOCK REPLENISHMENT PARAMETERS                       │
├────────────────────────────────────────────────────────────────────────┤
│ MAXIMUM STOCK LEVEL   : Upper capacity limit (Prevents Overstocking)   │
│ REORDER POINT (ROP)   : Trigger threshold to create Purchase Order     │
│ SAFETY STOCK          : Buffer cushion against supply/demand spikes    │
│ MINIMUM STOCK LEVEL   : Operational emergency threshold                │
└────────────────────────────────────────────────────────────────────────┘
A. Safety Stock FormulaCalculates buffer stock to protect against supplier lead time delays and unexpected demand spikes:$$\text{Safety Stock} = Z \times \sigma_d \times \sqrt{L}$$Where:$Z = \text{Service Level Factor}$ ($1.65$ for 95% service level, $2.33$ for 99% service level)$\sigma_d = \text{Standard Deviation of Daily Sales}$$L = \text{Supplier Lead Time (in Days)}$Simplified Business Formula:$$\text{Safety Stock} = (\text{Max Daily Sales} \times \text{Max Lead Time}) - (\text{Avg Daily Sales} \times \text{Avg Lead Time})$$B. Reorder Point (ROP) FormulaTrigger point where a Purchase Order MUST be issued:$$\text{Reorder Point (ROP)} = (\text{Average Daily Sales} \times \text{Lead Time}) + \text{Safety Stock}$$C. Economic Order Quantity (EOQ) FormulaCalculates optimal purchase batch size to minimize total inventory holding and ordering costs:$$\text{EOQ} = \sqrt{\frac{2 \times D \times S}{H}}$$Where:$D = \text{Annual Demand Quantity}$$S = \text{Ordering Cost Per Order (Setup Fee, Freight Administration)}$$H = \text{Holding Cost Per Unit Per Year (Storage, Insurance, Capital Cost)}$4. WAREHOUSE HIERARCHY, BIN LOCATIONS & STOCK TRANSFERSA. Location Hierarchy StructureWarehouse inventory MUST be located down to specific bin coordinates:Warehouse $\rightarrow$ Zone $\rightarrow$ Aisle $\rightarrow$ Shelf $\rightarrow$ BinExample Bin Code: WH01-Z02-A03-S01-B05B. Stock Transfer Execution & In-Transit IsolationTo eliminate "ghost stock" during physical transit between stores or warehouses, transfers MUST use a two-step state machine:Plaintext[ Warehouse A ] ──► [ STEP 1: TRANSFER_OUT ] ──► [ IN-TRANSIT LOCATION ] ──► [ STEP 2: TRANSFER_IN ] ──► [ Warehouse B ]
Step 1 (TRANSFER_OUT): Stock is deducted from Source Warehouse and added to a temporary virtual IN_TRANSIT location.Step 2 (TRANSFER_IN): Upon physical arrival and inspection at Destination Warehouse, stock is deducted from IN_TRANSIT and credited to the destination bin.5. BATCH, EXPIRY & SERIAL NUMBER TRACKINGA. Serial Number Lifecycle ManagementFor serialized items (e.g., Electronics, Laptops, Mobile Devices), every unit's Serial Number MUST be tracked through an explicit status state machine:Plaintext[ RECEIVED ] ──► [ IN_STOCK ] ──► [ RESERVED ] ──► [ SOLD ] ──► [ RETURNED / WARRANTY ]
Serial Uniqueness: A Serial Number MUST be unique within a product variant scope.POS / Checkout Enforce: POS cannot complete sales of serialized items without capturing or scanning the exact physical Serial Number.B. Batch / Lot Number & FEFO PickingEvery batch MUST store batch_number, manufacturing_date, and expiration_date.The picking algorithm MUST automatically direct warehouse staff to pick from the batch closest to expiration (FEFO).6. STOCK AUDIT (OPNAME), SHRINKAGE & LANDED COST ALLOCATIONA. Blind Stock Opname WorkflowBlind Counting Principle: Audit sheets generated for warehouse staff MUST hide the expected system quantity (system_qty) to force authentic physical counts.Reconciliation & Variance Posting:Discrepancy = Physical Count - System QtyPositive Discrepancy (Surplus): Post to Inventory Adjustment Gain account.Negative Discrepancy (Shrinkage / Theft): Post to Inventory Shrinkage Expense account.B. Landed Cost Allocation EngineFreight, import duties, customs tariffs, and handling fees MUST be capitalized into item inventory valuation rather than expensed immediately:$$\text{Allocated Landed Cost Per Unit} = \text{Base Purchase Price} + \left( \frac{\text{Item Value / Weight}}{\text{Total Shipment Value / Weight}} \times \text{Total Freight Cost} \right)$$7. INVENTORY EDGE CASES & ANTI-FRAUD MATRIXEdge Case ScenarioRisk / Threat VectorRequired System Mitigation RuleNegative Stock ScenarioDisruption of FIFO layer calculations; Accounting valuation corruption.STRICT BAN: Prevent sales or deductions if available_stock < requested_qty. If backorders are enabled, log backorder queue without deducting physical balance.Damaged Goods during TransitInaccurate valuation assets.Issue a STOCK_ADJUSTMENT movement with code DAMAGE_WRITE_OFF to transfer asset value to Damaged Goods Expense.Expired Stock Remaining on ShelfLegal fines; Health hazard to customers.Cron job flags batches nearing expiration ($< 30$ days) and sets status to EXPIRED_QUARANTINE to block POS checkout.Landed Cost Added After Stock SoldCOGS distortion on historical sales.Post retroactive landed cost adjustments to COGS Adjustment ledger account rather than modifying closed FIFO layers.8. AGENT EXECUTION DIRECTIVESZero Negative Stock Allowance: Never generate code that allows physical inventory to drop below zero unless operating in an explicit virtual backorder mode.Strict FIFO Layer Integrity: Ensure FIFO layers are created on every inbound movement and locked (FOR UPDATE) during deduction.Apply "Fix Terkecil yang Aman": Preserve stock movement ledgers, valuation formulas, and warehouse bin structures when updating inventory or catalog modules.
