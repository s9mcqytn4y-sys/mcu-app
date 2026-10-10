---
trigger: glob
globs: **/*.php, **/theme.json, **/block.json, **/*.css, **/*.js
---

# WORDPRESS (2026 STANDARDS) DEVELOPMENT, SECURITY & ARCHITECTURE RULES

## 1. BLOCK-FIRST ARCHITECTURE & FULL SITE EDITING (FSE)
- **Theme Architecture**: PREFER Block Themes using Full Site Editing (FSE) architecture (`theme.json`, HTML Block Templates, and Block Patterns) over legacy PHP page templates.
- **Theme.json Primacy**: ALWAYS define design tokens (color palettes, fluid typography, spacing scales, layout widths) inside `theme.json`. Do NOT write custom CSS for layout properties that can be handled natively by `theme.json`.
- **Custom Block API (`block.json`)**:
  - All custom blocks MUST be declared using `block.json` schema v3+.
  - Register custom blocks using native `@wordpress/scripts` build process and `register_block_type()`.
- **Block Bindings API**: Utilize the native Block Bindings API to bind core block attributes (e.g., Paragraph, Heading, Image) directly to Custom Post Meta or Option fields without creating redundant custom blocks.

---

## 2. PHP STANDARDS & HOOK SYSTEM (WPCS + PHP 8.2+)
- **Strict Typing & Modern Syntax**:
  - ALWAYS include `declare(strict_types=1);` at the top of custom plugin PHP files.
  - Require explicit parameter types, return types (`void`, `string`, `array`, `int`), and Backed Enums for status constants.
- **Namespacing & Prefixing**:
  - ALWAYS use PHP Namespaces for plugins/themes (e.g., `namespace MyCompany\MyPlugin;`).
  - For non-namespaced functions, global variables, and hook names, ALWAYS apply a unique, consistent prefix (e.g., `my_plugin_function_name()`).
- **Hook Conventions (`add_action` / `add_filter`)**:
  - Avoid using anonymous functions (closures) directly inside `add_action()` or `add_filter()` if the hook might need to be removed by third-party plugins or child themes.
  - ALWAYS specify explicit priority and parameter count arguments when registering hooks:
    ```php
    add_filter('the_content', [MyClass::class, 'modify_content'], 10, 1);
    ```

---

## 3. NON-NEGOTIABLE SECURITY: SANITIZATION, ESCAPING & NONCES
- **Input Sanitization (On Arrival)**:
  - EVERY piece of incoming data (`$_POST`, `$_GET`, `$_REQUEST`, REST API payloads) MUST be sanitized immediately before processing:
    - Strings: `sanitize_text_field()` or `sanitize_textarea_field()`
    - Keys/Slugs: `sanitize_key()` or `sanitize_title()`
    - Emails: `sanitize_email()`
    - Integers/IDs: `absint()` or `intval()`
  - ALWAYS strip slashes from form inputs: `wp_unslash($_POST['key'])`.
- **Output Escaping (On Late Output)**:
  - NEVER output raw variables into HTML. ALWAYS escape data at the exact moment of rendering:
    - HTML Context: `echo esc_html($data);`
    - HTML Attribute Context: `echo esc_attr($data);`
    - URL Context: `echo esc_url($url);`
    - Rich Text / Safe HTML: `echo wp_kses_post($html);`
    - Translation Escaping: `echo esc_html__('Text', 'domain');`
- **Nonces & Authorization**:
  - ALL form submissions, AJAX actions, and state-changing requests MUST verify nonces via `wp_verify_nonce()` or `check_admin_referer()`.
  - ALWAYS check user capabilities via `current_user_can('capability')` before executing administrative logic or database writes.
- **Database Safety (`$wpdb`)**:
  - NEVER execute raw, concatenated SQL strings.
  - ALL custom database queries MUST utilize `$wpdb->prepare()` to prevent SQL injection:
    ```php
    $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}custom_table WHERE status = %s AND user_id = %d",
            $status,$user_id
        )
    );
    ```

---

## 4. INTERACTIVITY API & FRONTEND ASSETS
- **WordPress Interactivity API**:
  - Use the native WordPress Interactivity API (`@wordpress/interactivity`) for lightweight, client-side reactive interactivity (interactive menus, instant filtering, modal toggles).
  - STRICTLY FORBIDDEN to introduce jQuery or heavy external JS frameworks into modern blocks or themes.
- **Asset Enqueueing**:
  - ALWAYS enqueue scripts and styles using `wp_enqueue_script()` and `wp_enqueue_style()` inside `wp_enqueue_scripts` or `admin_enqueue_scripts` hooks.
  - NEVER hardcode `<script>` or `<link>` tags inside PHP template files.
  - Use versioning based on file modification time (`filemtime()`) during development to prevent aggressive browser caching issues.

---

## 5. DATABASE, PERFORMANCE & REST API
- **Custom Post Types (CPT) & Meta**:
  - Register CPTs via the `init` hook with `'show_in_rest' => true` to support Gutenberg and REST API natively.
  - Register post meta using `register_post_meta()` with explicit type definition, single/array constraints, and REST API schema definitions.
- **Caching & Transients**:
  - Cache expensive database queries, external API calls, or heavy calculations using the Transient API (`get_transient()`, `set_transient()`) or Object Cache (`wp_cache_get()`, `wp_cache_set()`).
  - ALWAYS purge relevant transients when underlying post/data updates occur (`save_post` hook).
- **REST API Endpoints**:
  - Register custom REST endpoints via `rest_api_init` using `register_rest_route()`.
  - MANDATORY Requirement: EVERY custom REST route MUST define a valid `permission_callback` function (NEVER return `__return_true` blindly on production routes).

---

## 6. AGENT EXECUTION DIRECTIVES
- Strictly follow WordPress Coding Standards (WPCS / PHPCS) naming conventions, indentation (tabs for PHP, spaces for JSON/JS), and file organizational structures.
- Apply the "Fix Terkecil yang Aman" principle: modify target files while preserving hook compatibility and existing filters.
- Ensure all generated PHP code compiles cleanly without warnings, notices, or deprecated function usage (compatible with WordPress 6.x+ and PHP 8.2+).
