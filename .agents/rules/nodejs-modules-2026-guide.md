---
trigger: glob
globs: **/*.js, **/*.mjs, **/*.cjs, **/package.json
---

# NODE.JS MODULES (2026 STANDARDS) RESOLUTION & SPECIFICATION RULES

## 1. ES MODULES (ESM) SPECIFICATION & FILE EXTENSIONS
- **Default Module System**: EVERY modern Node.js project MUST enforce native ES Modules by defining `"type": "module"` in `package.json`.
- **Strict File Extension Rules**:
  - `.js`: Evaluated as ES Module when `"type": "module"` is set in `package.json`.
  - `.mjs`: ALWAYS evaluated as ES Module regardless of `package.json` setting.
  - `.cjs`: ALWAYS evaluated as CommonJS module regardless of `package.json` setting.
- **Mandatory Relative Import Extensions**:
  - Relative ESM imports MUST explicitly include the target file extension (e.g., `import { helper } from './utils.js';`).
  - Directory imports (e.g., `import { utils } from './utils/index.js';`) MUST specify the full path including filename and extension. Naked directory imports (`import { utils } from './utils';`) are STRICTLY FORBIDDEN in ESM.

---

## 2. PACKAGE EXPORTS & SUBPATH MAPPINGS (`package.json`)
- **Modern `"exports"` Field**:
  - ALL published modules or internal workspace packages MUST define explicit `"exports"` in `package.json` instead of relying solely on `"main"`.
  - Prevent internal module leakage by explicitly exposing only public entry points:
    ```json
    {
      "name": "@my-org/core",
      "type": "module",
      "exports": {
        ".": "./src/index.js",
        "./utils": "./src/utils/index.js",
        "./package.json": "./package.json"
      }
    }
    ```
- **Conditional Exports**: Order export conditions strictly from most specific to default fallback (`"types"`, `"import"`, `"default"`).
- **Subpath Patterns**: Use subpath pattern exports (`"./features/*": "./src/features/*.js"`) for dynamic module routing within large internal packages.
- **Internal Imports Alias (`"#imports"`)**: Use the `"imports"` field in `package.json` for package-internal aliases (e.g., `#db` -> `./src/database/client.js`) instead of fragile relative path traversal (`../../../../db/client.js`).

---

## 3. DYNAMIC IMPORTS, TOP-LEVEL AWAIT & IMPORT ATTRIBUTES
- **Top-Level Await**:
  - Top-level `await` is fully supported and RECOMMENDED for asynchronous module initialization (e.g., establishing database connections, loading dynamic config) inside ESM files.
- **Dynamic Imports (`import()`)**:
  - Use dynamic `import()` for lazy-loading heavy modules, conditional execution, or importing legacy CommonJS modules dynamically.
- **Native JSON & Asset Import Attributes**:
  - Use native TC39 Import Attributes (`with { type: 'json' }`) when importing JSON files in ESM context:
    ```javascript
    import config from './config.json' with { type: 'json' };
    ```
  - NEVER use `fs.readFileSync` or `require('./config.json')` to read JSON configuration files in ESM.

---

## 4. INTEROPERABILITY & COMMONJS (CJS) INTEGRATION
- **Loading CommonJS into ESM**:
  - CommonJS modules CAN be imported into ESM via standard `import` statements (e.g., `import pkg from 'legacy-package';`).
  - ALWAYS use Default Import syntax when importing CJS modules into ESM, as named exports in CJS wrappers can be unreliable during static resolution.
- **Loading ESM into CommonJS**:
  - ESM modules CANNOT be loaded using `require()`. Use dynamic `import()` within CJS if loading an ESM module is unavoidable:
    ```javascript
    const esmModule = await import('esm-package');
    ```
- **Dual Package Hazard Prevention**: Avoid providing both CJS and ESM builds of the same stateful singleton package to prevent duplicate class instances and broken state synchronization.

---

## 5. SCOPE, METADATA & ENVIRONMENT GLOBALS
- **ESM Globals Replacements**:
  - `__dirname` and `__filename` do NOT exist in ESM. Use `import.meta.dirname` and `import.meta.filename` natively (Node.js 20.11+ / 22+ / 24+).
  - Use `import.meta.url` for constructing absolute URLs and resolve relative paths using `new URL('./relative/path', import.meta.url)`.
- **Module Caching**:
  - Node.js caches ESM modules based on resolved URL strings. Query parameters (e.g., `import('./module.js?v=2')`) force a cache-bust and create a distinct module instance.

---

## 6. AGENT EXECUTION DIRECTIVES
- Ensure all generated module imports include explicit extensions (`.js`, `.mjs`) and conform to native ESM resolution rules.
- Prevent broken module references by verifying `package.json` `"exports"` and `"imports"` alias definitions.
- Apply the "Fix Terkecil yang Aman" principle: update module import/export signatures without introducing circular dependencies or breaking module scope interfaces.
