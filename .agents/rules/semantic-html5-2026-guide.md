---
trigger: glob
globs: **/*.html, **/*.blade.php, **/*.vue, **/*.jsx, **/*.tsx, **/*.svelte, **/*.astro
---

# SEMANTIC HTML5 (2026 STANDARDS) CODING & ACCESSIBILITY RULES

## 1. LANDMARK ELEMENTS & DOCUMENT STRUCTURE
- **Div-Soup Ban**: STRICTLY FORBIDDEN to use generic `<div>` or `<span>` elements when a semantic HTML5 element exists for the specific use case.
- **Root Document Attributes**: EVERY HTML document MUST declare `<!DOCTYPE html>`, `<html lang="...">`, and `<meta charset="UTF-8">`.
- **Landmark Mapping**:
  - Use `<header>` for page headers, hero sections, or article headers.
  - Use `<nav>` ONLY for primary or secondary navigation links lists.
  - Use `<main>` EXACTLY ONCE per page to wrap the primary content view.
  - Use `<article>` for self-contained, reusable content blocks (e.g., blog posts, cards, comments).
  - Use `<section>` for thematic grouping of content with a mandatory heading inside.
  - Use `<aside>` for indirectly related content (e.g., sidebars, callout boxes, advertisements).
  - Use `<footer>` for page or section footers (copyright, metadata, author info).

---

## 2. HEADING HIERARCHY & OUTLINE STRUCTURE
- **Single H1 Rule**: EVERY page MUST have EXACTLY ONE `<h1>` element representing the main topic/title of the view.
- **Strict Hierarchy**: Heading levels MUST follow a strict sequential order (`<h1>` -> `<h2>` -> `<h3>`). NEVER skip levels (e.g., jumping from `<h1>` directly to `<h3>` is strictly forbidden).
- **No Presentational Headings**: NEVER use heading tags (`<h1>`-`<h6>`) solely for font sizing styling. Use CSS classes for visual hierarchy.

---

## 3. INTERACTIVE ELEMENTS & FORMS
- **Buttons vs Links**:
  - ALWAYS use `<button>` for actions that change state, trigger dialogs, submit forms, or execute JavaScript logic.
  - ALWAYS use `<a href="...">` ONLY for navigation that alters the URL or scrolls to a target anchor.
  - STRICTLY FORBIDDEN: `<div onclick="...">`, `<span onclick="...">`, or `<a href="#">` used as buttons.
- **Form Controls & Labels**:
  - EVERY `<input>`, `<select>`, and `<textarea>` MUST have an explicitly associated `<label for="id">` or be wrapped inside a `<label>`.
  - Use `<fieldset>` and `<legend>` to group related form inputs (e.g., address fields, radio groups).
  - Use the native `<search>` element to wrap search forms and filtering controls.
- **Native Dialogs**: ALWAYS use the native `<dialog>` element for modals, drawers, and popups instead of custom `<div>` modal wrappers.
- **Interactive Disclosures**: PREFER native `<details>` and `<summary>` elements for accordions, FAQs, and expandable widgets.

---

## 4. CONTENT SEMANTICS & DATA DISPLAY
- **Machine-Readable Time**: ALWAYS wrap dates and timestamps in `<time datetime="YYYY-MM-DDThh:mm:ssZ">`.
- **Text Formatting Semantics**:
  - Use `<strong>` for strong importance or urgency (not just bold visual style).
  - Use `<em>` for emphasized text that changes spoken tone.
  - Use `<mark>` for highlighted text relevant to context (e.g., search term matches).
  - Use `<code>` and `<pre>` for inline code snippets and code blocks.
- **Data Tables**: ALWAYS use `<table>`, `<thead>`, `<tbody>`, `<th>` (with `scope="col|row"`), `<td>`, and `<caption>` for tabular data. NEVER use tables for visual page layouts.
- **Figures & Captions**: Wrap images, diagrams, charts, or code blocks that need standalone captions inside `<figure>` with `<figcaption>`.

---

## 5. ACCESSIBILITY (A11Y) & ARIA INTEGRATION
- **Semantic First**: ALWAYS prefer native HTML5 semantic tags over ARIA roles. ARIA is an enhancement, NOT a replacement for semantic HTML.
- **Mandatory Image Alt Text**:
  - EVERY `<img>` MUST have an `alt` attribute.
  - Informative images MUST have descriptive, meaningful `alt` text.
  - Decorative images MUST have an empty `alt=""` or `aria-hidden="true"` attribute.
- **Focus Management**: Ensure all interactive elements (`<button>`, `<a>`, `<input>`) are keyboard accessible and have visible focus states.
- **Screen Reader Text**: Use dedicated utility classes (e.g., `.sr-only` / `.visually-hidden`) to provide context for icon-only buttons instead of leaving them empty.

---

## 6. MEDIA, GRAPHICS & EMBEDS
- **Responsive Media**: PREFER `<picture>` with multiple `<source>` tags for art direction and modern image format delivery (WebP/AVIF).
- **SVG Accessibility**: Standalone inline `<svg>` graphics MUST include a `<title>` tag or `aria-label`, or be marked `aria-hidden="true"` if decorative.
- **Resource Loading Optimization**: ALWAYS explicitly set `width` and `height` attributes on `<img>` and `<iframe>` to prevent Layout Shifts (CLS), and set `loading="lazy"` for below-the-fold media.

---

## 7. AGENT EXECUTION DIRECTIVES
- Inspect existing HTML template architecture before generating new components to ensure consistency with markup style.
- Reject requests that attempt to construct "div-soup" interfaces; automatically refactor them into valid Semantic HTML5 landmarks.
- Ensure all generated HTML passes standard W3C validation rules and accessibility checks.
