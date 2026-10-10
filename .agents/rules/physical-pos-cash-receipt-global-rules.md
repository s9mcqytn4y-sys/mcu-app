---
trigger: glob
globs: **/pos/**/*, **/cashier/**/*, **/receipts/**/*, **/register/**/*, **/invoices/**/*, **/*Cash*, **/*Receipt*, **/*Register*, **/*POS*
---

# GLOBAL PHYSICAL TRANSACTIONS, POS, CASH HANDLING & LEGAL RECEIPT RULES (2026 STANDARDS)

## 1. PHYSICAL CASH & CASH DRAWER MANAGEMENT ARCHITECTURE

All Point-of-Sale (POS) and physical register systems MUST operate under strict physical cash lifecycle management to prevent cash leakage, theft, and drawer variance.

```text
┌────────────────────────────────────────────────────────────────────────┐
│                   PHYSICAL CASH SHIFT LIFECYCLE                        │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
    ┌───────────────────────────────┼───────────────────────────────┐
    ▼                               ▼                               ▼
┌───────────────────────┐ ┌───────────────────────┐ ┌───────────────────────┐
│ 1. SHIFT OPENING      │ │ 2. MID-SHIFT TRANSACT │ │ 3. SHIFT CLOSING      │
├───────────────────────┤ ├───────────────────────┤ ├───────────────────────┤
│ • Count Starting Float│ │ • Ring Sales & Cash In│ │ • Blind Blind Count   │
│   (Modal Awal)        │ │ • Paid-Out / Petty    │ │ • Calculate Variance  │
│ • Lock Cash Drawer    │ │   Cash Enforcement    │ │   (Selisih Kas)       │
│ • Assign Shift ID     │ │ • Drawer Trigger Audit│ │ • Manager Sign-Off    │
└───────────────────────┘ └───────────────────────┘ └───────────────────────┘


A. Shift Management & Float Money (Modal Awal)Shift Opening Mandatory Action: A cashier session MUST NOT begin without recording and verifying the Starting Float Cash (Modal Awal Kasir).Physical Lock & Drawer Impulse Trigger: The physical cash drawer (RJ11 / RJ12 interface or Serial/USB trigger) MUST ONLY open automatically upon completing a CASH transaction or during an authorized manual trigger.Manual Drawer Open Audit: Any manual drawer trigger outside an active cash sale MUST require a Manager PIN and generate an immutable DRAWER_UNLOCKED_NO_SALE audit event.B. Shift Closing, Blind Count & Cash Variance PolicyBlind Counting Principle: During shift closing, the cashier MUST enter the physical cash count per denomination (e.g., number of 100k, 50k, 20k notes) WITHOUT seeing the system's expected cash total.Cash Variance Formula:$$\text{Expected Cash} = \text{Starting Float} + \text{Total Cash Sales} + \text{Cash In} - \text{Cash Out / Paid-Out}$$$$\text{Variance (Selisih)} = \text{Physical Counted Cash} - \text{Expected Cash}$$Variance Posting:Variance = 0: Perfect reconciliation.Variance < 0 (Kas Minus): Post shortfall to Cash Shortage Expense / Staff Liability account.Variance > 0 (Kas Lebih): Post surplus to Cash Overage Income account. NEVER pocket cash overages.2. CALCULATION MECHANICS: TAXES, SERVICE CHARGES & ROUNDINGTo prevent calculation discrepancies between the POS display, physical receipt, and general ledger, all POS engines MUST follow a strict mathematical Order of Operations.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                   POS MATHEMATICAL ORDER OF OPERATIONS                 │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
 1. Subtotal = Σ (Item Quantity × Item Unit Price)
 2. Line Item Discounts = Σ (Item Level Discount)
 3. Discounted Subtotal = Subtotal - Line Item Discounts - Order Level Discount
 4. Service Charge = Discounted Subtotal × Service Charge %
 5. Tax Base (DPP) = Discounted Subtotal + Service Charge (if Exclusive Tax)
 6. Tax Amount (PPN / PB1) = Tax Base × Tax Rate %
 7. Grand Total = Tax Base + Tax Amount
 8. Cash Rounding Adjustment = Apply Cash Rounding to Grand Total
 9. Final Amount Due = Grand Total + Cash Rounding Adjustment
A. Tax Calculation Modes (Inclusive vs Exclusive)Exclusive Tax (Tax Added On Top): Tax is computed on top of the discounted subtotal + service charge.Inclusive Tax (Tax Embedded in Price): Item prices already include tax. The system MUST extract the Tax Base (DPP) and Tax Amount for financial reporting using:$$\text{Tax Base (DPP)} = \frac{\text{Gross Price}}{1 + \text{Tax Rate}}$$$$\text{Tax Amount} = \text{Gross Price} - \text{Tax Base (DPP)}$$B. Cash Rounding Policy (Pembulatan Tunai)Physical Cash Rounding Rule: Since physical currency coins/notes have minimum denominations (e.g., Rp 100 or Rp 500 in Indonesia), physical cash transactions MAY require rounding adjustments.Banker's Rounding (Half-Even): Rounding MUST use Half-Even Banker's Rounding to eliminate mathematical bias.Rounding Accounting Ledger: Rounding adjustments (e.g., $+Rp\ 40$ or $-Rp\ 60$) MUST be posted to a dedicated Cash Rounding Expense/Income ledger account. Non-cash payments (QRIS, Credit Card, Debit) MUST NEVER be rounded.3. CHANGE CALCULATION & DENOMINATION ALLOCATION ENGINECalculations for cash received and change returned MUST be mathematically exact and immune to manual cashier tampering.PHP// ✅ BENAR: Algoritma Kalkulasi Kembalian dan Validasi Nominal Tunai
public function calculateChange(int $grandTotal, int $cashTendered): ChangeResult
{
    if ($cashTendered < $grandTotal) {
        throw new InsufficientCashException("Uang tunai tidak cukup. Kurang: " . ($grandTotal - $cashTendered));
    }

    $changeAmount = $cashTendered - $grandTotal;
    $denominations = [100000, 50000, 20000, 10000, 5000, 2000, 1000, 500, 200, 100];
    $breakdown = [];
    $remaining = $changeAmount;

    foreach ($denominations as $denom) {
        if ($remaining >= $denom) {
            $count = intdiv($remaining, $denom);
            $breakdown[$denom] = $count;
            $remaining %= $denom;
        }
    }

    return new ChangeResult(
        grandTotal: $grandTotal,
        cashTendered: $cashTendered,
        changeAmount: $changeAmount,
        breakdown: $breakdown
    );
}
Change Rules:Tender Threshold Validation: System MUST reject entering a $cashTendered amount less than the $grandTotal.Change Denomination Suggestion: The POS UI MUST display exact change breakdown suggestions (e.g., "Kembalian Rp 75.000: 1x 50k, 1x 20k, 1x 5k") to guide the cashier and speed up physical throughput.4. TRANSPARENT ITEMIZATION & CUSTOMER DISPLAY (CFD) STANDARDSCustomers MUST have real-time, transparent visibility of the itemized bill before and during physical payment execution.Customer Facing Display (CFD) Requirements:Real-Time Line Item Sync: As items are scanned or tapped, the secondary Customer Display MUST immediately render:Item Name & Selected Options/ModifiersQuantity & Individual Unit PriceApplied Line Item DiscountsTransparent Summary View: Prior to cash acceptance, the CFD MUST clearly distinguish:SubtotalDiscounts (Promotions/Vouchers)Service Charge (if applicable)Local Tax (PB1 / Resto Tax / PPN)Final Grand TotalPayment Execution View: During cash payment, the CFD MUST display:Uang Diterima (Cash Tendered)Kembalian (Change Due) in large, high-contrast text.5. LEGAL PROOF OF PAYMENT STANDARDIZATION (STRUK, NOTA, INVOICE, FAKTUR)Every physical transaction MUST print or issue a legally compliant, tamper-evident Proof of Payment document.Plaintext================================================
            RESTORAN & CAFE SEJAHTERA
       PT KULINER NUSANTARA UTAMA MEMBANGUN
     Jl. Sudirman No. 45, Jakarta Selatan
  NPWP: 01.234.567.8-012.000 | PB1: 101020304
================================================
No. Struk : TRX-20260813-0089  Tgl: 13/08/2026
Kasir     : Budi Santoso       Jam: 12:44:02
Terminal  : POS-01             Shift: SH-02
================================================
2x Nasi Goreng Spesial @35.000        70.000
   - Pedas: Sedang
1x Es Teh Manis                       10.000
------------------------------------------------
Subtotal                              80.000
Diskon Toko (10%)                     -8.000
------------------------------------------------
Subtotal Setelah Diskon               72.000
Service Charge (5%)                    3.600
Dasar Pengenaan Pajak (DPP)           75.600
Pajak Restoran / PB1 (10%)             7.560
================================================
GRAND TOTAL                           83.160
================================================
TUNAI / CASH                         100.000
KEMBALIAN                             16.840
================================================
Metode Pembayaran : TUNAI (CASH)
Status Transaksi  : LUNAS (PAID)
================================================
        [ QR CODE VERIFIKASI KEASLIAN ]
     Hash: a1b2c3d4e5f678901234567890abcdef
    Terima Kasih Atas Kunjungan Anda!
   Barang yang sudah dibeli tidak dapat ditukar
================================================
Mandatory Legal Fields Hierarchy:Document TypePrimary TargetMandatory Legal ElementsStruk Kasir (POS Thermal Receipt)Retail B2C / RestaurantBusiness Name, Address, NPWP/PB1 ID, Terminal ID, Shift ID, Cashier Name, Itemized List, Subtotal, Discounts, Service Charge, Tax DPP, Tax Amount, Grand Total, Tendered, Change, Timestamp, Anti-Fraud QR Code.Nota Penjualan / Nota KontanDirect Physical TradeTransaction Date, Customer Name (optional), Detailed List, Authorized Signature / Cashier Stamp, Total Amount.Invoice / Faktur PenjualanB2B / Corporate PhysicalUnique Serial Invoice Number, Seller & Buyer Full Legal Identity (NPWP/NIK), Payment Terms (Due Date), Itemized Unit Prices, Tax Exemption Statements (if any), Authorized Signature.Faktur PajakTax Authority ComplianceOfficial e-Faktur Serial Number (NSFP), Seller & Buyer Tax Identifiers, Detailed Taxable Base (DPP) & PPN Breakdown.6. PHYSICAL POS EDGE CASES & ANTI-FRAUD MITIGATION MATRIXEdge Case ScenarioThreat / Fraud VectorRequired System Mitigation RulePost-Print Transaction Void / CancelCashier prints receipt, takes customer cash, then voids transaction to steal money.PROHIBITED to void without Manager PIN. Voiding a printed transaction MUST generate an automatic REVERSAL_RECEIPT and log an alert in management dashboard.Offline Mode (Internet Outage)POS loses connectivity during physical cash sale.Allow offline cash transactions. Store encrypted transactions in local IndexedDB / SQLite with sequential tamper-evident hash chaining. Auto-sync on reconnection.Manual Price OverrideCashier lowers item price manually for friends/relatives.Price overrides MUST be bounded by a maximum percentage limit (e.g., max 10%) and require Manager Authorization.Reprinting Receipts (Re-Print)Cashier reprints receipt to give duplicate proof to a second customer while pocketing cash.Watermark reprinted receipts with a bold, non-removable "DUPLIKAT / REPRINT #N" header and track total reprint counts.Fake Cash / Counterfeit BanknotesLoss due to accepting fake currency.POS UI MUST prompt cashier to verify high-denomination notes ($Rp\ 50,000$ / $Rp\ 100,000$) using UV Detector or Detector Pen before confirming cash entry.7. AGENT EXECUTION DIRECTIVESZero Calculation Discrepancy: Ensure all POS calculation logic strictly adheres to the mathematical order of operations defined in Section 2.Enforce Immutable Audit Logs: Never generate code that deletes cash register transaction history or physical shift logs.Apply "Fix Terkecil yang Aman": Maintain receipt template formatting and tax compliance structures when updating POS frontend or backend modules.
