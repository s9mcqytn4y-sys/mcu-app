---
trigger: glob
globs: **/*.css, **/*.scss, **/*.postcss
---

# MODERN CSS (2026 STANDARDS) CODING & ARCHITECTURE RULES

## 1. CSS ARCHITECTURE, CASCADE LAYERS & NESTING
- **Cascade Layers**: EVERY CSS codebase MUST organize global styles into Cascade Layers (`@layer`) to strictly control specificity:
  ```css
  @layer reset, base, components, utilities;
Native CSS Nesting:

Use native CSS Nesting for grouping descendant and pseudo-state selectors.

STRICT LIMIT: Do NOT nest selectors more than 3 levels deep to prevent specificity inflation and unmaintainable code.

Specificity Management:

Use :where() to apply default styles with zero specificity (specificity: (0,0,0)).

Use :is() to group complex selectors without duplicating rule blocks.

NEVER use !important unless overriding third-party utility classes or inline style constraints.

2. DESIGN TOKENS, CUSTOM PROPERTIES & MODERN COLOR SPACES
CSS Custom Properties (Variables):

Define design tokens (colors, typography, spacing, shadows) as Custom Properties on :root or within @layer base.

Type-check critical tokens using @property for CSS animations and transition support:

CSS
@property --accent-color {
  syntax: "<color>";
  inherits: true;
  initial-value: oklch(0.6 0.25 240);
}
Modern Color Spaces:

PREFER OKLCH (oklch(L C H)) over HSL or RGB for dynamic, perceptually uniform color palettes and accessible contrast ratios.

Use color-mix() for programmatic tinting, shading, and opacity adjustments instead of hardcoding multiple hex values:

CSS
background-color: color-mix(in oklch, var(--primary) 85%, transparent);
3. LOGICAL PROPERTIES, RESPONSIVENESS & CONTAINER QUERIES
Logical Properties First: ALWAYS use CSS Logical Properties over physical directional properties to support multi-directional layouts (LTR/RTL) natively:

Use inline-size and block-size instead of width and height.

Use margin-inline and padding-block instead of margin-left/right and padding-top/bottom.

Use inset-inline and inset-block instead of left/right and top/bottom.

Fluid Typography & Spacing:

Use clamp(), min(), and max() for fluid typography and responsive layouts without relying solely on viewport media queries:

CSS
font-size: clamp(1rem, 0.8rem + 1vw, 2.25rem);
Container Queries First:

PREFER Container Queries (@container) over Viewport Media Queries (@media) for component-level responsiveness:

CSS
.card-container { container-type: inline-size; }
@container (inline-size > 400px) {
  .card { display: grid; grid-template-columns: 1fr 2fr; }
}
Modern Grid & Subgrid:

Use grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)) for intrinsic, media-query-free responsive layouts.

Use grid-template-rows: subgrid to align card headers and footers across adjacent grid items.

4. PSEUDO-CLASSES, PSEUDO-ELEMENTS & MODERN SELECTORS
Relational Selector (:has()): Use :has() for parent and sibling state-driven styling without needing JavaScript:

CSS
.form-group:has(:invalid) { border-color: var(--color-error); }
.card:has(img) { grid-template-rows: auto 1fr; }
Accessible Focus States:

NEVER remove default outlines without providing an explicit replacement.

ALWAYS use :focus-visible instead of :focus so focus rings appear only for keyboard navigation:

CSS
:focus-visible { outline: 2px solid var(--accent-color); outline-offset: 4px; }
User Form States: PREFER :user-valid and :user-invalid over :valid / :invalid to prevent validation errors from showing before user interaction.

Entry & Exit Animations: Use @starting-style alongside transition-behavior: allow-discrete to animate elements transitioning to/from display: none or <dialog> states natively.

5. CROSS-BROWSER COMPATIBILITY, RESETS & CLS PREVENTION
Modern CSS Reset Requirements:

Apply box-sizing: border-box globally via universal selector.

Set min-block-size: 100vh on body.

Ensure media elements have max-inline-size: 100% and block-size: auto.

Prevent Cumulative Layout Shift (CLS):

ALWAYS declare aspect-ratio or explicit width and height attributes on images, videos, and canvas elements.

Use contain-intrinsic-size alongside content-visibility: auto for long scrollable lists to optimize render performance.

Progressive Enhancement (@supports):

Wrap cutting-edge features in @supports queries if a fallback layout is necessary for legacy evergreen browsers.

Reduced Motion & Accessibility:

ALWAYS respect user OS accessibility preferences:

CSS
@media (prefers-reduced-motion: reduce) {
  *, ::before, ::after {
    animation-duration: 0.01ms !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
6. AGENT EXECUTION DIRECTIVES
Prioritize native CSS solutions (:has(), @container, color-mix(), clamp()) over importing heavy CSS frameworks or JavaScript layout helpers.

Strictly adhere to the "Fix Terkecil yang Aman" principle: modify only target selectors without polluting global specificity or breaking existing CSS utility layers.

Ensure all generated CSS validates cleanly without syntax errors and respects dark mode via color-scheme: light dark or @media (prefers-color-scheme: dark).
