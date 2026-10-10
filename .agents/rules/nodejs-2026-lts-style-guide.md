---
trigger: glob
globs: **/*.js, **/*.mjs, **/*.ts, **/package.json
---

Berikut adalah **Glob Pattern** yang presisi beserta hasil **tuning & perbaikan format dokumen Content** untuk aturan **Node.js (2026 LTS Standards)**.

---

### 🎯 1. GLOB PATTERN (Salin ke Kolom Input "Glob Pattern")

Masukkan baris berikut ke dalam kolom **Glob Pattern** di IDE Anda:

```text
**/*.js, **/*.mjs, **/*.ts, **/package.json

```

#### 💡 Mengapa Glob Pattern Ini Sangat Tepat?

Aturan ini **akan aktif secara otomatis** hanya ketika Anda sedang membuka atau mengedit file eksekusi backend/Node.js (`.js`, `.mjs`, `.ts`) serta manifesto dependensi proyek (`package.json`).

---

### 📋 2. DOKUMEN CONTENT HASIL TUNING & REVISI PERBAIKAN

Gantikan seluruh teks di dalam kotak **Content** dengan draf rapi yang sudah dibersihkan dan diperbaiki formatnya di bawah ini:

```markdown
# NODE.JS (2026 LTS STANDARDS) ARCHITECTURE, RUNTIME & CODING RULES

## 1. RUNTIME STANDARDS & ESM-FIRST ARCHITECTURE
- **Target Runtime**: STRICTLY targeted for **Node.js 24 LTS / Node.js 22 LTS**.
- **ES Modules (ESM) Only**:
  - EVERY Node.js project MUST declare `"type": "module"` in `package.json`.
  - STRICTLY FORBIDDEN to use CommonJS (`require()`, `module.exports`, `__dirname`, `__filename`).
  - Use `import` and `export` statements exclusively.
  - ALWAYS specify explicit file extensions in relative ESM import paths (e.g., `import { db } from './db.js';`).
- **Native Environment Variables**:
  - NEVER install `dotenv` package. Use Node.js native environment file loader:
    ```bash
    node --env-file=.env app.js
    ```
  - Access environment variables strictly via `process.env.VAR_NAME`.

---

## 2. NATIVE NODE.JS APIS (ZERO-DEPENDENCY FIRST)
- **Native HTTP/Fetch & WebSockets**:
  - NEVER install `axios` or `node-fetch`. ALWAYS use global native `fetch()` and `FormData`.
  - ALWAYS use global native `WebSocket` API for real-time bidirectional communication.
- **Native Database Engine (`node:sqlite`)**:
  - For lightweight database operations, embedded caching, or local storage, PREFER the built-in `node:sqlite` module over external SQLite packages:
    ```javascript
    import { DatabaseSync } from 'node:sqlite';
    const db = new DatabaseSync('app.db');
    ```
- **Native Unit Testing (`node:test`)**:
  - NEVER install `Jest` or `Mocha` for standard unit tests. ALWAYS use Node.js native test runner (`node:test`) and assertion module (`node:assert/strict`):
    ```javascript
    import { test, describe, it } from 'node:test';
    import assert from 'node:assert/strict';
    ```
- **Native CLI Argument Parsing (`node:util`)**:
  - Use `parseArgs()` from `node:util` for building CLI tools instead of importing `commander` or `yargs`.

---

## 3. ASYNCHRONOUS FLOW, STREAMS & EVENT LOOP MANAGEMENT
- **Async/Await Protocol**: ALWAYS use `async/await` for asynchronous control flow. Raw promise chaining (`.then().catch()`) is strictly prohibited.
- **Node.js Prefixed Core Imports**:
  - ALWAYS use the `node:` protocol prefix when importing built-in modules (e.g., `import fs from 'node:fs/promises';`, `import path from 'node:path';`, `import crypto from 'node:crypto';`).
- **Non-Blocking I/O**:
  - NEVER use synchronous file system or CPU-blocking methods (e.g., `fs.readFileSync`, `fs.writeFileSync`) in HTTP server handlers or production pipelines. Use `node:fs/promises` exclusively.
- **Context Tracking**: Use `AsyncLocalStorage` from `node:async_hooks` for tracking request correlation IDs and logging contexts across asynchronous execution trees.

---

## 4. ERROR HANDLING, LOGGING & SECURITY
- **Uncaught Exception & Rejection Handlers**:
  - EVERY production application entry point MUST register process-level error listeners:
    ```javascript
    process.on('uncaughtException', (err) => { ... });
    process.on('unhandledRejection', (reason, promise) => { ... });
    ```
- **Graceful Shutdown**:
  - ALWAYS implement graceful shutdown hooks for `SIGTERM` and `SIGINT` signals to close database connections and pending HTTP servers before process exit.
- **Node.js Permission Model**:
  - Production deployments MUST enforce file system and subprocess security restrictions via flags when applicable:
    ```bash
    node --permission --allow-fs-read=/app/data app.js
    ```
- **Crypto & Password Hashing**: Use native `node:crypto` (`crypto.subtle` or `crypto.scrypt`) or Web Crypto API for secure hashing, UUID generation (`crypto.randomUUID()`), and encryption.

---

## 5. TYPESCRIPT INTEGRATION (NATIVE STRIPPING)
- **Native Type Stripping**:
  - When writing TypeScript for Node.js 24+, leverage native Type Stripping (`--experimental-strip-types` / native TS execution) for execution without heavy build/transpilation steps for backend services.
- **Type Definitions**: Maintain explicit type definitions (`.d.ts`) or JSDoc annotations when runtime type checking is required.

---

## 6. AGENT EXECUTION DIRECTIVES
- Prioritize Node.js native core modules over adding third-party `npm` dependencies.
- Apply the "Fix Terkecil yang Aman" principle: modify target backend files without breaking existing route handlers or database connections.
- Ensure all generated Node.js code executes cleanly without runtime warnings or unhandled promise rejections on Node.js 24 LTS.

```
