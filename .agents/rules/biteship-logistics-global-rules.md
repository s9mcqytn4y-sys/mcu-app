---
trigger: glob
globs: **/shipping/**/*, **/biteship/**/*, **/couriers/**/*, **/logistics/**/*, **/*Shipping*, **/*Biteship*, **/*Courier*, **/*Waybill*
---

# GLOBAL BITESHIP LOGISTICS INTEGRATION & COURIER ENGINE RULES (2026 STANDARDS)

## 1. DUAL-ENVIRONMENT ARCHITECTURE & API AUTHENTICATION

Systems MUST maintain strict isolation between **Sandbox (Development)** and **Production** environments for the Biteship API ecosystem, enforcing secure Bearer Token authentication.

```text
┌────────────────────────────────────────────────────────────────────────┐
│                    BITESHIP ARCHITECTURE DATA FLOW                     │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│  1. INSTANT / SAME-DAY DELIVERY   │               │  2. REGULAR / CARGO / EXPRESS     │
│   (GoSend, Grab, Shopee, Paxel)   │               │  (JNE, J&T, SiCepat, Anteraja)    │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • Origin/Dest Lat & Long Required │               │ • Biteship `area_id` Required     │
│ • Real-Time Driver Allocation     │               │ • Scheduled Courier Pickup        │
│ • Immediate Pickup Window         │               │ • Auto-Generated AWB / Resi Code  │
└─────────────────┬─────────────────┘               └─────────────────┬─────────────────┘
                  │                                                   │
                  └─────────────────────────┬─────────────────────────┘
                                            ▼
                             [ Biteship Gateway Engine ]
                                            │
                                 (HTTP Webhook Notification)
                                            ▼
                           [ Backend Logistics State Handler ]

A. Environment Configuration & Base URLsSandbox Environment:Base URL: https://api-sandbox.biteship.com/v1/Token Format: biteship_test.eyJhbGci...Production Environment:Base URL: https://api.biteship.com/v1/Token Format: biteship_live.eyJhbGci...Authentication Header:HTTPAuthorization: Bearer <BITESHIP_API_KEY>
Content-Type: application/json
STRICT SECURITY RULE:BITESHIP_API_KEY MUST strictly originate from server-side environment variables (.env). NEVER expose the API key to client-side single-page applications or mobile frontends.2. LOCATION MAPPING, AREA ID ENGINE & LAT/LONG REQUIREMENTSBiteship uses a dual-location identification model depending on the courier delivery tier.A. Area ID Mapping vs Exact Coordinates MatrixDelivery CategoryCouriersRequired Location PayloadFailure Scenario if MissingInstant / Same DayGoSend, GrabExpress, ShopeeXpress Instant, PaxelExact Latitude & Longitude (latitude, longitude) + area_idDriver allocation fails; API returns 400 Bad Request (Invalid Coordinate).Regular / Express / CargoJNE, SiCepat, J&T, Anteraja, NinjaXpress, Lion Parcel, POSBiteship Area ID (origin_area_id, destination_area_id) + Postal CodeRate calculation fails or returns incorrect shipping cost zones.B. Location Area ID Search & Caching PolicyArea ID Resolution Endpoint: GET /v1/maps/areas?countries=ID&input={query}Caching Mandate: Area IDs for Sub-districts (Kecamatan) and Postal Codes MUST be cached in local database tables (shipping_areas) or Redis to eliminate redundant external Biteship API calls during address autocomplete:Cache Key: biteship:area:<postal_code>:<subdistrict_name_hash>Cache TTL: 30 Days (Location boundaries change infrequently).3. RATES CALCULATION, WEIGHT MECHANICS & INSURANCEA. Charged Weight Formula (Actual vs Volumetric Weight)Logistics providers charge shipping fees based on whichever is greater: Actual Physical Weight or Volumetric Dimensional Weight.$$\text{Volumetric Weight (Grams)} = \frac{\text{Length (cm)} \times \text{Width (cm)} \times \text{Height (cm)}}{6000} \times 1000$$$$\text{Charged Weight (Grams)} = \max\left(\text{Actual Weight (g)}, \text{Volumetric Weight (g)}\right)$$PHP// ✅ BENAR: Kalkulasi Berat Tagihan (Charged Weight) Sesuai Standar Biteship
public function calculateChargedWeight(int $actualWeightGrams, int $lengthCm, int $widthCm, int $heightCm): int
{
    $volumetricWeightGrams = (int) ceil(($lengthCm * $widthCm * $heightCm) / 6000 * 1000);
    return max($actualWeightGrams, $volumetricWeightGrams);
}
B. Insurance Fee CalculationFor fragile, high-value, or electronic items, insurance MUST be enabled (use_insurance: true).Insurance Fee Formula:$$\text{Insurance Fee} \approx 0.2\% \times \text{Declared Item Value (Declared Value)}$$If use_insurance: true, the declared item value MUST match the total net value of items inside the shipment payload.4. ORDER CREATION, WAYBILL (AWB) & LABEL PRINTING ENGINEA. Order Booking Payload Standard (POST /v1/orders)To ensure smooth pickup and waybill issuance, order booking payloads MUST include complete origin, destination, and itemized specs:JSON{
  "shipper_contact_name": "Gudang Utama Store",
  "shipper_contact_phone": "081234567890",
  "shipper_contact_email": "warehouse@domain.com",
  "origin_contact_name": "Gudang Utama Store",
  "origin_contact_phone": "081234567890",
  "origin_address": "Jl. Industri Raya No. 12, Blora, Jakarta Pusat",
  "origin_note": "Samping gerbang biru",
  "origin_area_id": "IDNP1101",
  "origin_coordinate": {
    "latitude": -6.175392,
    "longitude": 106.827153
  },
  "destination_contact_name": "Budi Santoso",
  "destination_contact_phone": "089876543210",
  "destination_contact_email": "budi@email.com",
  "destination_address": "Jl. Sudirman No. 45, RT 01/RW 02, Karet Tengsin",
  "destination_area_id": "IDNP1102",
  "destination_coordinate": {
    "latitude": -6.2088,
    "longitude": 106.8456
  },
  "courier_company": "jne",
  "courier_type": "reg",
  "delivery_type": "now",
  "items": [
    {
      "name": "Sepatu Lari Running Shoes - Black 42",
      "description": "Sepatu olahraga",
      "value": 450000,
      "quantity": 1,
      "weight": 800,
      "height": 12,
      "length": 30,
      "width": 20
    }
  ]
}
B. Thermal Label Printing (PDF / ESC-POS)Upon successful order booking, Biteship returns a waybill_id (Airway Bill / Nomor Resi) and a label URL (waybill_link).The shipping system MUST store the waybill_id in the local shipments table and support downloading/embedding thermal PDF labels ($100\text{mm} \times 150\text{mm}$ format).5. WEBHOOK LIFECYCLE & STATUS STATE MACHINEBiteship dispatches real-time HTTP Webhooks (POST) whenever a shipment status changes.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                   BITESHIP SHIPMENT STATE MACHINE                      │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
 [ Order Created ] ──► [ allocated (Driver Assigned) ] ──► [ picking_up ]
                                                                 │
                                                                 ▼
 [ delivered ] ◄── [ dropping_off / in_transit ] ◄── [ picked (In Hub) ]
       │
       ├────────────────────────────┬────────────────────────────┐
       ▼                            ▼                            ▼
 [ DELIVERED ]            [ rejected / failed ]         [ returned / RTS ]
A. Status Mapping MatrixBiteship Webhook statusLocal Shipment StatusLocal Order StatusSystem Actionplaced / allocatedCOURIER_ALLOCATEDPROCESSINGDriver or courier booking confirmed.picking_upPICKUP_IN_PROGRESSPROCESSINGDriver is en route to warehouse.pickedPICKED_UPSHIPPEDPackage collected; AWB active in courier system.dropping_off / in_transitIN_TRANSITSHIPPEDPackage in transport between hubs/courier.deliveredDELIVEREDCOMPLETEDPackage successfully received. Trigger completion timers.rejected / courier_not_foundALLOCATION_FAILEDPROCESSINGInstant driver not found. Re-trigger driver search.cancelledCANCELLEDCANCELLEDShipment cancelled by merchant or system.returned / returningRETURN_TO_SENDERRETURNEDPackage failed delivery and is returning to warehouse.B. Webhook Security Verification & Header SecretEvery webhook payload MUST be verified against the secret signature header (X-Biteship-Signature or custom Webhook Secret Token configured in Biteship Dashboard) to prevent unauthorized status spoofing.PHP// ✅ BENAR: Verifikasi Secret Header Webhook Biteship
public function verifyBiteshipWebhook(Request $request, string$webhookSecret): bool
{
    $incomingHeader = $request->header('X-Biteship-Signature') ?? $request->header('X-Custom-Token');

    if (empty($incomingHeader)) {
        return false;
    }

    return hash_equals($webhookSecret,$incomingHeader);
}
C. Webhook Idempotency PolicyWebhook notifications MAY be retried by Biteship if network timeouts occur.Idempotency Guard: Check if the shipment's local status is already in the target state or a higher terminal state before executing database updates or dispatching customer notifications.6. COD (CASH ON DELIVERY) & RETURN TO SENDER (RTS) MECHANICSA. COD Collection & Surcharge CalculationFor COD orders (is_cod: true):Total COD Amount to Collect:$$\text{COD Collection Target} = \text{Order Value} + \text{Shipping Fee} + \text{Insurance Fee}$$COD Service Surcharge: Biteship/Couriers charge a percentage fee (typically $2\% - 3\%$ + PPN) on the collected cash amount.B. COD Refusal & Return To Sender (RTS) ManagementIf the end customer refuses to pay or accept the package upon arrival:Webhook receives status = 'returned' or status = 'delivery_failed'.Local system MUST transition order status to COD_REFUSED_RTS.Quarantine the returned package upon arrival back at the warehouse and execute an audit log before restoring stock items.7. BITESHIP EDGE CASES & FAILURE MITIGATION MATRIXEdge Case ScenarioRoot Cause / Threat VectorRequired System Mitigation RuleInstant Driver Not Found (courier_not_found)Rain, peak hours, or remote location prevents driver match.Auto-retry driver allocation up to 3 times with 5-minute intervals. If still unmatched, notify warehouse operator to switch courier or extend timeline.Volumetric Weight Discrepancy at HubWarehouse inputted smaller box size than physical reality.Courier holds package or updates weight. System MUST record weight variance billing adjustment and alert warehouse manager.Address Outside Courier CoverageDestination area_id not serviced by chosen courier.Filter available couriers dynamically via POST /v1/rates/couriers API response before presenting choices to customer on checkout UI.Lost or Damaged Package in TransitPhysical damage during courier handling.Webhook flags status = 'damaged' / 'lost'. Automatically generate an insurance claim log and notify customer support team.Biteship API Outage (HTTP 5xx)Gateway downtime.Implement Circuit Breaker Pattern. Queue non-instant shipment bookings in background job queue (shipment_retry_queue) with exponential backoff.8. AGENT EXECUTION DIRECTIVESZero Hardcoded Secrets: Biteship API keys MUST strictly originate from environment variables (.env).Enforce Mandatory Coordinates for Instant Courier: Always ensure latitude and longitude are included for GoSend, GrabExpress, ShopeeXpress Instant, and Paxel.Apply "Fix Terkecil yang Aman": Preserve webhook idempotency checks, AWB tracking logs, and volumetric weight calculations when modifying shipping or logistics modules.

