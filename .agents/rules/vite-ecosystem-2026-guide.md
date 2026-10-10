---
trigger: glob
globs: **/vite.config.*, **/index.html, **/.env*
---

# VITE ECOSYSTEM (2026 STANDARDS) BUILD, DEV SERVER & OPTIMIZATION RULES

## 1. CONFIGURATION & ESM-FIRST ARCHITECTURE
- **ESM Config File**:
  - `vite.config.js` or `vite.config.ts` MUST use native ES Module syntax (`import` / `export default defineConfig(...)`).
  - ALWAYS wrap the configuration object inside `defineConfig()` (or `defineConfig(({ command, mode }) => ...)` for conditional setups) to ensure type safety and autocompletion.
- **Path Aliases Alignment**:
  - ALWAYS define path aliases using `resolve.alias` (e.g., `@` pointing to `./src`).
  - Path aliases in `vite.config` MUST match TypeScript/JavaScript configuration (`tsconfig.json` / `jsconfig.json` `compilerOptions.paths`) to prevent IDE navigation errors.
    ```typescript
    import { defineConfig } from 'vite';
    import { fileURLToPath, URL } from 'node:url';

    export default defineConfig({
      resolve: {
        alias: {
          '@': fileURLToPath(new URL('./src', import.meta.url))
        }
      }
    });
    ```

---

## 2. ENVIRONMENT VARIABLES & SECURITY (`import.meta.env`)
- **Client-Exposed Variable Prefix**:
  - ONLY variables prefixed with `VITE_` are exposed to the frontend bundle.
  - STRICTLY FORBIDDEN: NEVER prefix secret keys, database credentials, or private API tokens with `VITE_`.
- **Environment Access Syntax**:
  - ALWAYS access environment variables via `import.meta.env.VITE_VAR_NAME`.
  - STRICTLY FORBIDDEN to use Node.js `process.env` in client-side code.
- **Environment Type Safety**:
  - Define explicit TypeScript interface declarations for custom environment variables inside `src/env.d.ts`:
    ```typescript
    /// <reference types="vite/client" />
    interface ImportMetaEnv {
      readonly VITE_APP_TITLE: string;
      readonly VITE_API_BASE_URL: string;
    }
    interface ImportMeta {
      readonly env: ImportMetaEnv;
    }
    ```

---

## 3. DEV SERVER, PROXY & HMR (HOT MODULE REPLACEMENT)
- **Dev Server Configuration**:
  - Define explicit port, host, and fallback behaviors for team environment consistency:
    ```typescript
    server: {
      port: 3000,
      strictPort: true,
      host: true, // Listen on all network addresses (Docker/Mobile testing)
    }
    ```
- **Backend API Proxying**:
  - Use `server.proxy` to bypass CORS issues during local development when communicating with backend services (Laravel, Node.js, Go, Python):
    ```typescript
    server: {
      proxy: {
        '/api': {
          target: 'http://localhost:8000',
          changeOrigin: true,
          secure: false,
        }
      }
    }
    ```
- **HMR Preservation**:
  - Ensure custom UI modules respect HMR APIs (`import.meta.hot`) to prevent full page reloads when editing stateful components.

---

## 4. ASSETS HANDLING, PUBLIC DIR & CSS PIPELINE
- **Asset Processing vs Public Directory**:
  - **`public/` Directory**: Store static, unreferenced assets that must keep exact filenames (e.g., `favicon.ico`, `robots.txt`, `manifest.json`). Accessed via root path `/filename.ext`.
  - **`src/assets/` Directory**: Store assets that should be processed, hashed, and bundled by Vite. ALWAYS import them explicitly in JavaScript/CSS (`import logo from '@/assets/logo.svg'`).
- **Modern CSS & Lightning CSS**:
  - Prefer Vite's native CSS features (CSS Modules, PostCSS, native nesting) or Lightning CSS engine over heavy external compilers.
  - Enable CSS code splitting (`build.cssCodeSplit: true`) to ensure styles are loaded on-demand alongside their respective code chunks.

---

## 5. BUILD OPTIMIZATION, CHUNKING & TARGETING
- **Modern Target Selection**:
  - Set `build.target` to modern browser standards (`es2022` or `baseline: edge88`) to avoid emitting unnecessary transpilation polyfills for legacy browsers.
- **Chunk Splitting Strategy**:
  - Optimize vendor chunking using `build.rollupOptions.output.manualChunks` to separate large third-party dependencies (e.g., vendor libraries) from application logic for better caching:
    ```typescript
    build: {
      rollupOptions: {
        output: {
          manualChunks(id) {
            if (id.includes('node_modules')) {
              return 'vendor';
            }
          }
        }
      }
    }
    ```
- **Bundle Analysis**:
  - Utilize `rollup-plugin-visualizer` in build mode when analyzing or optimizing bundle sizes exceeding recommended thresholds (500kB warning limit).

---

## 6. AGENT EXECUTION DIRECTIVES
- Prioritize native Vite plugins (`@vitejs/plugin-vue`, `@vitejs/plugin-react`, `laravel-vite-plugin`) over manual, custom Rollup configurations.
- Apply the "Fix Terkecil yang Aman" principle: update `vite.config` or asset imports without breaking HMR or introducing CORS errors.
- Ensure all generated code runs cleanly in development (`vite`) and passes production build checks (`vite build`) without TypeScript or Rollup bundling errors.
