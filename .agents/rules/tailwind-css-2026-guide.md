---
trigger: glob
globs: **/*.css, **/*.html, **/*.blade.php, **/*.vue, **/*.jsx, **/*.tsx, **/*.svelte, **/*.astro
---

# TAILWIND CSS (2026 STANDARDS - V4.X ENGINE) CODING & UI/UX RULES

## 1. SETUP, INSTALLATION & CSS-FIRST CONFIGURATION (TAILWIND V4)
- **Modern CSS-First Configuration**:
  - Tailwind v4 DOES NOT use `tailwind.config.js`. ALWAYS configure theme tokens, custom utility classes, and fonts directly inside the main CSS entry file using the `@theme` directive:
    ```css
    @import "tailwindcss";

    @theme {
      --color-brand-primary: oklch(0.62 0.22 250);
      --color-brand-surface: oklch(0.98 0.01 250);
      --font-display: 'Inter', sans-serif;
    }
    ```
- **Installation Standard (Vite / CLI)**:
  - PREFER official Vite integration (`@tailwindcss/vite`) or official CLI binary over legacy PostCSS wrappers.
- **Play CDN Usage (Prototyping Only)**:
  - When CDN is required for rapid prototyping or single-file HTML demos, ALWAYS use the official v4 browser script:
    ```html
    <script src="[https://unpkg.com/@tailwindcss/browser@4](https://unpkg.com/@tailwindcss/browser@4)"></script>
    ```
  - STRICTLY FORBIDDEN to use legacy v3 Play CDN scripts (`https://cdn.tailwindcss.com`) in new 2026 projects.

---

## 2. COLORING, THEME TOKENS & DARK/LIGHT MODE
- **Semantic Color Tokens**:
  - NEVER hardcode arbitrary color shades directly in markup (e.g., avoid `bg-blue-600` for primary CTA buttons).
  - ALWAYS use semantic variable aliases: `bg-primary`, `text-foreground`, `bg-surface`, `border-muted`, `bg-danger`.
- **OKLCH Color Space**: PREFER defining custom theme colors in OKLCH for perceptually uniform contrast and accessible dark mode variants.
- **Dark Mode Strategy**:
  - Default to CSS media queries (`dark:`) or class-based dark mode (`.dark` class on root `<html>`).
  - ALWAYS pair background and text colors explicitly for dark mode transitions:
    ```html
    <div class="bg-surface text-foreground dark:bg-neutral-900 dark:text-neutral-100">
    ```

---

## 3. LAYOUTING, GRID, CONTAINER QUERIES & RESPONSIVENESS
- **Mobile-First Breakpoint Hierarchy**:
  - ALWAYS write styles mobile-first (`sm:`, `md:`, `lg:`, `xl:`, `2xl:`). Unprefixed utilities apply to mobile screens.
- **Container Queries First**:
  - PREFER Container Queries (`@container` and `@sm:`, `@md:`, `@lg:`) over Viewport Media Queries when building reusable UI components (cards, user lists, form widgets).
- **Modern Grid & Subgrid**:
  - Use `grid-cols-[repeat(auto-fit,minmax(min(100%,280px),1fr))]` for intrinsic, media-query-free responsive layouts.
  - Use `grid-cols-subgrid` on nested grid items to align child elements across adjacent cards.
- **Flexbox & Gap Utility**: ALWAYS use `gap-*` (or `gap-x-*` / `gap-y-*`) for spacing children inside Flexbox or Grid. NEVER use negative margins or `space-x-*` legacy stack utilities.

---

## 4. SIZING, SPACING, TYPOGRAPHY & COPYWRITING
- **8pt Spatial Alignment**:
  - ALL padding, margin, and size utilities MUST follow the 4px/8px scale (`p-1`=4px, `p-2`=8px, `p-3`=12px, `p-4`=16px, `p-6`=24px, `p-8`=32px).
- **Combined Size Utility (`size-*`)**:
  - Use `size-*` when width and height are equal instead of writing `w-* h-*` separately (e.g., `size-10` instead of `w-10 h-10`).
- **Typography & Copywriting Elegance**:
  - **Headings**: ALWAYS apply `text-balance` on titles and headings to eliminate lonely orphaned words.
  - **Paragraphs**: ALWAYS apply `text-pretty` on long body copy to enforce natural line wrapping and prevent ragged edges.
  - **Reading Measure**: Limit paragraph line width using `max-w-prose` or `max-w-[65ch]`.
  - **Line Clamping**: Use `line-clamp-2` or `line-clamp-3` alongside `overflow-hidden` for multi-line card summaries.

---

## 5. STATES, PSEUDO-CLASSES & INTERACTION
- **Accessibility Focus Rings**:
  - EVERY interactive control MUST specify explicit `:focus-visible` styles:
    ```html
    <button class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
    ```
  - NEVER use `focus:outline-none` without providing a `focus-visible:ring-*` replacement.
- **Parent & Sibling State Handling**:
  - Use `group` on parent containers and `group-hover:`, `group-focus-visible:` on child elements.
  - Use `peer` on input controls and `peer-checked:`, `peer-disabled:`, `peer-invalid:` on sibling labels or indicator badges.
  - Use Relational Variant (`has-*`) to style parent containers based on child state:
    ```html
    <div class="border-neutral-200 has-[:invalid]:border-danger-500">
    ```
- **Disabled State**: ALWAYS style disabled states explicitly using `disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed`.

---

## 6. MOTION, ANIMATIONS & VISUAL EFFECTS
- **Smooth State Transitions**:
  - ALWAYS declare `transition-colors`, `transition-transform`, or `transition-all` with explicit duration (`duration-200` or `duration-300`) and easing (`ease-in-out`).
- **Modern Backdrop Filters**: Use `backdrop-blur-md` and semi-transparent background colors (`bg-surface/80` or `bg-neutral-900/80`) for sticky headers, modals, and dropdown overlays.
- **Micro-Interactions**: Add subtle elevation or scale feedback on hover/active states (`hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98]`).

---

## 7. MEDIA RENDERING, DYNAMIC CLASSES & PRESERVATION
- **Aspect Ratio & Object Fitting**:
  - ALWAYS define `aspect-video`, `aspect-square`, or `aspect-[4/3]` on image and video containers to reserve layout space and prevent Cumulative Layout Shift (CLS).
  - Use `object-cover` or `object-contain` on media elements (`<img>`, `<video>`).
- **NO DYNAMIC STRING CONCATENATION (STRICT RULE)**:
  - Tailwind scans source files statically. NEVER construct dynamic class names via string interpolation (e.g., `class={`bg-${color}-500`}` IS STRICTLY FORBIDDEN).
  - ALWAYS write complete class strings or use a static lookup map:
    ```javascript
    // ✅ CORRECT LOOKUP MAP
    const colorMap = {
      primary: 'bg-primary-500 text-white',
      danger: 'bg-danger-500 text-white',
    };
    ```

---

## 8. AGENT EXECUTION DIRECTIVES
- Prioritize native Tailwind v4 utility classes over writing custom CSS rules or inline `style="..."` attributes.
- Ensure all generated UI templates are mobile-first, fully responsive, dark-mode ready, and keyboard accessible.
- Apply the "Fix Terkecil yang Aman" principle: modify target utility classes without disrupting layout structure or parent flex/grid alignments.
