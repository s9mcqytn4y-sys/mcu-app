---
trigger: glob
globs: **/*.{html,blade.php,vue,jsx,tsx,svelte,astro,css,scss}
---

# WEB DESIGN, UI/UX, ACCESSIBILITY & ERGONOMICS (2026 STANDARDS) RULES

## 1. DESIGN SYSTEM, GRID & TYPOGRAPHY STANDARDS
- **Spatial System (8pt Grid Rule)**: ALL margins, paddings, gaps, and component dimensions MUST strictly adhere to an 8px/4px spatial scale (`4px`, `8px`, `12px`, `16px`, `24px`, `32px`, `48px`, `64px`). NEVER use arbitrary spacing values (e.g., `13px`, `19px`).
- **Typography Scale**:
  - Implement fluid typography scales using CSS `clamp()` based on a modular scale (1.250 Major Third or 1.333 Perfect Fourth).
  - Body text line-height MUST be between `1.5` and `1.65` for optimal readability.
  - Headings line-height MUST be tighter (`1.1` to `1.25`).
  - Maximum reading line length (Measure) MUST be constrained between `45` and `75` characters (`max-inline-size: 65ch`).
- **Color Systems & Semantics**:
  - ALWAYS use semantic color tokens (`--color-bg-primary`, `--color-text-main`, `--color-status-success`, `--color-border-subtle`) instead of hardcoded hex values.
  - Color palettes MUST define clear functional roles: Primary (Brand), Neutral (Surface/Text), Success, Warning, Error, and Info.

---

## 2. UI COMPONENTS & ATOMIC DESIGN SYSTEM
- **Atomic Hierarchy**:
  - **Atoms**: Buttons, Inputs, Badges, Icons, Labels, Toggles.
  - **Molecules**: Form Fields (Label + Input + Error + Helper), Search Bars, Card Headers.
  - **Organisms**: Navigation Bars, Data Tables, Modal Dialogs, Sidebar Filters.
- **Mandatory Component Interactive States**:
  EVERY interactive component MUST explicitly define and style 6 distinct visual states:
  1. `Default`: Resting visual state.
  2. `Hover`: Mouse cursor overlay indication.
  3. `Focus-Visible`: Keyboard focus indicator (`outline-offset: 2px`).
  4. `Active`: Press/click interaction feedback.
  5. `Disabled`: Visually muted (`opacity: 0.5`), non-interactive (`pointer-events: none`, `tabindex="-1"`).
  6. `Loading / Busy`: Spinner/skeleton indication with `aria-busy="true"`.

---

## 3. ERGONOMICS, TOUCH TARGETS & FITTS'S LAW
- **Minimum Touch Target Size (Fitts's Law)**:
  - ALL interactive controls (buttons, links, icon toggles, checkbox inputs) MUST have a minimum physical touch target of **44x44px** (or **48x48px** for mobile-first views).
  - Add transparent padding/hitbox expansion if the visual icon size is smaller than 44px.
- **Mobile Thumb Zone Layout**:
  - Primary actions (CTA, Bottom Navigation, Submit Buttons) on mobile devices MUST be placed within the natural thumb sweep zone (lower 1/3rd of the screen viewport).
  - Destructive or secondary actions MUST be placed further away to prevent accidental clicks.
- **Visual Affordance**:
  - Buttons MUST visually look clickable (clear background, border, or elevation).
  - Form fields MUST visually look editable (distinct background or border enclosure).
  - Clickable text elements MUST have distinct color or underline affordance.

---

## 4. INFORMATION ARCHITECTURE, NAVIGATION & ANATOMY
- **Page Anatomy Landmarks**:
  - Every layout MUST strictly enforce 5 structural regions: Header (`<header>`), Navigation (`<nav>`), Main Content (`<main>`), Contextual Sidebar/Panel (`<aside>`), and Footer (`<footer>`).
- **Wayfinding & Breadcrumbs**:
  - Deeply nested applications (level 3+ depth) MUST provide explicit Breadcrumbs navigation.
  - Active page/tab links MUST include `aria-current="page"` or `aria-current="step"`.
- **Cognitive Load & Hick's Law**:
  - Limit primary navigation menu items to a maximum of `7 ± 2` top-level choices.
  - Group related form fields into logical steps or accordions (`<fieldset>` / `<legend>`) rather than overwhelming users with long 15+ field scrolling forms.

---

## 5. ACCESSIBILITY (WCAG 2.2 LEVEL AA STANDARDS)
- **Contrast Ratios**:
  - Normal text (< 18pt / 24px): MUST achieve at least **4.5:1** contrast ratio against its background.
  - Large text (≥ 18pt / 24px) & UI Controls: MUST achieve at least **3.0:1** contrast ratio.
  - NEVER rely solely on color to convey meaning (e.g., add an icon or text label alongside red error states).
- **Keyboard Navigation & Tab Order**:
  - Logical DOM tab order MUST match the visual visual flow (Left-to-Right, Top-to-Bottom).
  - FOCUS TRAP: Modal dialogs MUST trap keyboard focus inside the modal when open and return focus to the trigger button when closed.
- **Screen Reader Announcements**:
  - Dynamic content updates (notifications, form validation alerts) MUST use `aria-live="polite"` or `aria-live="assertive"`.

---

## 6. WEB SERVICES INTEGRATION, LOADING STATES & SKELETONS
- **Network State Handling**:
  EVERY data-driven UI view interacting with Web Services/APIs MUST gracefully handle 4 network execution states:
  1. `Idle`: Initial rest state before data request.
  2. `Loading`: Render Skeleton Loaders (matching the exact layout shape) instead of full-screen blocking spinners for page initialization.
  3. `Success`: Smooth content fade-in rendering.
  4. `Error`: Inline error recovery boundary with actionable retry triggers (`Try Again` button).
- **Optimistic UI Updates**:
  - For low-risk user actions (e.g., Likes, Bookmark toggles, Status switches), update the UI state immediately (optimistically) before server confirmation, with automatic rollback if the background API call fails.
- **Feedback & Toasts**:
  - Toast notifications MUST automatically dismiss after 4–6 seconds, unless they convey critical error information requiring manual dismissal.

---

## 7. AGENT EXECUTION DIRECTIVES
- Prioritize user ergonomics, WCAG 2.2 accessibility, and spatial consistency BEFORE writing component code.
- Prevent cluttered UI layouts; enforce white space, visual hierarchy (Z-pattern / F-pattern), and strict 8pt grid alignment.
- Ensure all generated UI templates include mandatory loading skeletons, error states, keyboard focus styles, and ARIA attributes natively.
