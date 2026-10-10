---
trigger: glob
globs: **/cms/**/*, **/content/**/*, **/schemas/**/*, **/admin/**/*, **/filament/**/*, **/strapi/**/*, **/payload.config.*, **/sanity.config.*, **/*Post*, **/*Article*, **/*Page*
---

# GLOBAL CONTENT MANAGEMENT SYSTEM (CMS) ARCHITECTURE RULES (2026 STANDARDS)

## 1. CMS ARCHITECTURE DUALITY (HEADLESS VS MONOLITHIC ADMIN)
Systems MUST define a clear content distribution strategy depending on the scale and frontend decoupled requirement:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                        CMS ARCHITECTURE PATTERNS                       │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│       HEADLESS CMS (API-FIRST)    │               │  MONOLITHIC / HYBRID CMS ADMIN    │
│  (Strapi, Payload, Sanity, Directus)│             │ (Laravel Filament, WordPress FSE) │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • Decoupled Frontend (Next/Nuxt)  │               │ • Tightly Coupled Admin & SSR UI  │
│ • REST / GraphQL API Delivery     │               │ • High Speed Internal CRUD Admin  │
│ • AST-based JSON Rich Text        │               │ • Server-Side Rendered Blade/Blade│
│ • On-Demand Webhook Revalidation  │               │ • Direct Database Access Layer    │
└───────────────────────────────────┘               └───────────────────────────────────┘

Headless First Default: Preferred for multi-channel publishing (Web, Mobile App, Smart Devices). Content is served via typed REST/GraphQL APIs as JSON AST (Abstract Syntax Tree).

Monolithic Admin Default: Preferred for single-team internal publishing or enterprise web portals requiring rapid, unified admin dashboard builds (e.g., Laravel Filament).

2. CONTENT MODELING, SCHEMAS & TAXONOMY RULES
A. Block-Based Content Architecture (Modular Page Builder)
NO RAW HTML BLOBS: Avoid storing long page layouts inside a single monolithic HTML text column.

Modular Component Blocks: Store page content as structured JSON arrays representing component blocks:

JSON
[
  { "type": "hero_banner", "data": { "title": "...", "cta_url": "..." } },
  { "type": "rich_text", "data": { "body": "..." } },
  { "type": "media_gallery", "data": { "images": ["uuid1", "uuid2"] } }
]
B. Slug Generation & Permanent Link Rules
Automatic Slugification: Slugs MUST be generated automatically from the primary title using lowercase kebab-case with non-ASCII characters stripped.

Uniqueness & Immutability Constraint:

Slugs MUST be unique within their collection scope.

301 Redirect Guard: If an existing published post's slug is updated, the system MUST record the old slug in a slug_redirects table and issue a 301 Moved Permanently HTTP response on old URL hits to protect SEO ranking.

C. Taxonomies (Categories vs Tags)
Categories: Hierarchical (Parent-Child relationships allowed), single or few-choice selection for primary content classification.

Tags: Flat structure (No hierarchy), multiple choices allowed for granular topic searching and dynamic filtering.

3. EDITORIAL WORKFLOW, STATES & DRAFT ISOLATION
A. Lifecycle State Machine
Every CMS entity MUST implement an explicit 4-stage publication state machine:

Plaintext
[ DRAFT ] ──► [ SCHEDULED ] ──► [ PUBLISHED ] ──► [ ARCHIVED ]
Draft: Unpublished content visible ONLY to content creators and editors.

Scheduled: Content set to automatically transition to Published state at a future published_at timestamp via background cron/worker.

Published: Publicly accessible content served to frontend end-users.

Archived: Hidden from public indexes and feeds, kept internally for audit/historical purposes.

B. Draft Isolation & Public API Leak Prevention (STRICT SECURITY)
Public API endpoints (/api/v1/articles) MUST ALWAYS automatically filter queries by default:

SQL
WHERE status = 'published' AND published_at <= NOW()
Previewing Draft or Scheduled content on the frontend MUST require a short-lived, cryptographically signed preview token (e.g., /api/preview?token=signed_jwt).

C. Revisions & Version Control
Major content models MUST maintain a revisions audit log storing a snapshot of previous JSON states, the editor's User ID, and a rollback trigger.

4. ASSETS & MEDIA MANAGEMENT (DAM)
Storage Abstraction: All media uploads MUST pass through a Storage Abstraction Layer (S3 / Cloud Storage / MinIO). Never store uploads in the local application web root.

Automated Image Pipeline:

Automatically convert raster images (JPEG/PNG) into modern, compressed WebP or AVIF formats.

Generate responsive image breakpoints (thumbnail: 150px, medium: 640px, large: 1200px).

Extract and store Blurhash or LQIP (Low-Quality Image Placeholder) strings for progressive frontend loading.

Accessibility & SEO Metadata: Every media upload MUST support explicit alt_text, title, and caption metadata fields.

5. SEO, STRUCTURED DATA (JSON-LD) & LOCALIZATION (i18n)
A. Meta Tags Control Panel
Every page/article schema MUST include a dedicated SEO metadata block:

Meta Title (50–60 characters limit).

Meta Description (150–160 characters limit).

OpenGraph (OG) Image (1200x630px ratio).

Canonical URL override.

Indexing directives (noindex, nofollow toggles).

B. Schema.org Structured Data (JSON-LD)
The CMS MUST expose semantic JSON-LD payloads for search engine crawlers (e.g., Article, NewsArticle, FAQPage, BreadcrumbList):

JSON
{
  "@context": "[https://schema.org](https://schema.org)",
  "@type": "Article",
  "headline": "...",
  "image": ["..."],
  "datePublished": "2026-08-13T00:00:00Z",
  "author": [{ "@type": "Person", "name": "..." }]
}
C. Content Localization (i18n) Strategy
Field-Level Localization: Store translated fields within JSON columns or relational translation tables (article_translations).

Locale Fallback: If a requested translation is missing for locale id_ID, gracefully fall back to the default locale en_US while setting the HTML lang attribute accordingly.

6. PERFORMANCE, CACHING & ON-DEMAND REVALIDATION
On-Demand Cache Invalidation: Upon publishing, updating, or unpublishing content, the CMS MUST dispatch Webhook events to trigger on-demand revalidation on decoupled frontends (e.g., Next.js revalidatePath / revalidateTag).

CDN Edge Caching: Public CMS API responses MUST specify cache headers:

HTTP
Cache-Control: public, max-age=60, s-maxage=3600, stale-while-revalidate=86400
7. SECURITY & RICH TEXT SANITIZATION
XSS Prevention in Rich Text:

HTML content entered via WYSIWYG editors MUST be sanitized server-side before persisting or rendering using strict HTML sanitizers (DOMPurify / wp_kses_post).

Disallow <script>, <iframe> (except whitelisted YouTube/Vimeo embeds), onload=, and inline JS attributes.

Editorial RBAC:

Author: Can create and edit ONLY their own draft posts.

Editor: Can review, edit, publish, or unpublish posts from any author.

Admin: Full access to settings, user roles, taxonomy structures, and system configurations.

8. AGENT EXECUTION DIRECTIVES
Always enforce separation between public content endpoints and administrative CMS management routes.

Ensure all generated CMS schemas natively include fields for publication states, slugs, SEO metadata, and media alt text.

Apply the "Fix Terkecil yang Aman" principle: update CMS content models or admin resource forms without breaking existing API response contracts or invalidating public cache tags.
