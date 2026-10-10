---
trigger: glob
globs: **/*Form*, **/*Input*, **/*Select*, **/*Field*, **/*.tsx, **/*.jsx, **/*.vue, **/*.blade.php
---

# GLOBAL FORM INPUT TYPES, VALIDATION & UI/UX RULES (2026 STANDARDS)

## 1. COMPREHENSIVE FORM INPUT TAXONOMY (TAKSONOMI TIPE INPUT)

Systems MUST support and implement form input controls based on their explicit semantic categories:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                        FORM INPUT CONTROL SYSTEM                       │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
    ┌────────────────┬──────────────┼───────────────┬────────────────┐
    ▼                ▼              ▼               ▼                ▼
┌──────────────┐ ┌──────────────┐ ┌───────────┐ ┌──────────────┐ ┌──────────────┐
│ TEXTUAL      │ │ SELECTION    │ │ TEMPORAL  │ │ FILE & COLOR │ │ COMPLEX /    │
│ CONTROLS     │ │ CONTROLS     │ │ CONTROLS  │ │ CONTROLS     │ │ MOLECULES    │
├──────────────┤ ├──────────────┤ ├───────────┤ ├──────────────┤ ├──────────────┤
│ • Text       │ │ • Checkbox   │ │ • Date    │ │ • File       │ │ • OTP / PIN  │
│ • Password   │ │ • Radio Group│ │ • Time    │ │ • Color      │ │ • Tag Input  │
│ • Email      │ │ • Select     │ │ • DateTime│ │ • Range      │ │ • Combobox   │
│ • Tel        │ │ • Toggle /   │ │ • Month   │ │ • Hidden     │ │ • Rich Text  │
│ • URL        │ │   Switch     │ │ • Week    │ │              │ │ • Masked Num │
│ • Search     │ │              │ │           │ │              │ │ • Signature  │
│ • Number     │ │              │ │           │ │              │ │              │
└──────────────┘ └──────────────┘ └───────────┘ └──────────────┘ └──────────────┘

2. ACCESSIBILITY (A11Y - WCAG 2.2) & ERGONOMICS RULESA. Label Association Rule (Mandatory)EVERY input element MUST be explicitly associated with a <label> via for="input_id" or wrapped directly within a <label>.STRICT PROHIBITION: Placeholders ARE NOT LABELS. Never rely on placeholder="..." as a replacement for a visible <label>.B. ARIA Integration & Error BindingDynamic error messages MUST be linked to the input using aria-describedby="error_id".When an input fails validation, apply aria-invalid="true" dynamically.Required fields MUST specify aria-required="true" or native required attribute.C. Touch Target & Focus RingsAll interactive controls (checkboxes, radio buttons, select triggers, file buttons) MUST have a minimum physical touch target of 44x44px on mobile devices.Focus Ring Requirement: Focus states MUST use :focus-visible with a high-contrast focus ring (outline-offset: 2px). NEVER hide focus rings with outline: none without providing a focus-visible ring.3. MOBILE KEYBOARD OPTIMIZATION (inputmode & autocomplete)Mobile virtual keyboards MUST match the target input type to prevent user friction:Input IntentHTML typeRecommended inputmodeStandard autocompleteEmail AddressemailemailemailPhone NumbertelteltelOTP / PIN Codetext / numbernumericone-time-codeCurrency Amounttext / numberdecimaloffCredit Card Numbertextnumericcc-numberSearch QuerysearchsearchoffURL WeburlurlurlNew Passwordpasswordtextnew-passwordCurrent Passwordpasswordtextcurrent-password4. INPUT SECURITY & SANITIZATION PROTOCOLSA. Password Input SecurityPassword inputs MUST include a toggle to "Show/Hide Password" with accessible keyboard controls (aria-label="Tampilkan kata sandi").Password inputs MUST allow paste operations (never block clipboard paste, as it impedes password managers).B. File Upload Input SecurityType Restriction: Specify allowed MIME types in accept="..." (e.g., accept="image/png, image/jpeg, application/pdf").Size Validation: Validate file size client-side BEFORE triggering network upload streams.Drag-and-Drop Dropzone: Provide visual feedback during dragover states (border-dashed, active background shift) and ensure keyboard fallback (Click to Upload).C. OTP / PIN Code InputsOTP inputs MUST support auto-fill from SMS/System prompts using autocomplete="one-time-code".Automatically advance focus to the next input cell upon entering a digit, and support Backspace key focus regression.5. DATA NORMALIZATION & VALIDATION DUALITYA. Data Normalization Before SubmissionInput values MUST be normalized to standard data formats before sending to the API:Strings: Trim leading and trailing whitespace (val.trim()).Phone Numbers: Normalize to E.164 format (e.g., 0812... -> +62812...).Dates: Convert local calendar dates to ISO 8601 (YYYY-MM-DD or YYYY-MM-DDTHH:mm:ssZ).Currency / Financial Numbers: Strip formatting characters (commas, dots, currency symbols) and convert to raw integer base (e.g., "Rp 150.000" -> 150000).B. Validation Timing StrategyOn Blur (Focus Out): Validate field format (e.g., email syntax, minimum length) AFTER the user leaves the field.On Change: Validate dynamically ONLY AFTER the field has already been marked invalid (gives immediate positive feedback when fixed).On Submit: Re-validate ALL fields in the form. Focus the first invalid input automatically.6. INTERACTIVE STATES MATRIXEVERY custom or native form input control MUST define 6 visual and operational states:Default: Initial resting visual state.Hover: Mouse cursor hover indication.Focus / Focus-Visible: Keyboard selection ring indication.Disabled: Visually muted (opacity: 0.5), non-interactive (pointer-events: none, tabindex="-1").Readonly: Values can be focused and read, but not edited (readonly attribute).Invalid / Error: Red/danger border accent with explicit helper error message text.7. AGENT EXECUTION DIRECTIVESAlways construct forms using native HTML5 semantics (<form>, <fieldset>, <legend>, <label>) before wrapping in custom UI frameworks.Ensure all generated form inputs pass WCAG 2.2 AA accessibility standards and keyboard navigation tests.Apply the "Fix Terkecil yang Aman" principle: update input components or validation schemas without disrupting parent form state or API payload structures.
