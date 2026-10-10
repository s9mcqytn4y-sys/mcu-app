---
trigger: glob
globs: **/*.js, **/*.mjs, **/*.cjs, **/*.html
---

# MODERN VANILLA JAVASCRIPT (2026 SPECIFICATIONS) CODING & ARCHITECTURE RULES

## 1. VARIABLES, SCOPE & SYNTAX STANDARDS
- **Variable Declarations**: ALWAYS use `const` by default. Use `let` ONLY when variable reassignment is explicitly required. NEVER use `var`.
- **Strict Equality**: ALWAYS use strict equality (`===` and `!==`). NEVER use loose equality (`==` or `!=`).
- **Modern ES Operators**:
  - Use Optional Chaining (`obj?.prop?.subProp`) for safe nested property access.
  - Use Nullish Coalescing Operator (`??`) instead of Logical OR (`||`) when checking for null or undefined (preserves `0`, `""`, and `false`).
  - Use Logical Assignment Operators (`??=`, `||=`, `&&=`) for concise variable assignments.
- **Type Coercion Avoidance**: PREFER explicit type conversion (`Boolean(val)`, `Number(str)`, `String(num)`) over implicit coercion (`!!val`, `+str`, `"" + num`).

---

## 2. FUNCTIONS, ARROW FUNCTIONS & CALLBACKS
- **Arrow Functions**: Use arrow functions (`() => {}`) for anonymous callbacks, inline handlers, and non-method functions to maintain predictable lexical `this` binding.
- **Standard Functions**: Use standard function declarations (`function name() {}`) for top-level utility functions or methods that require dynamic `this` context or hoisting.
- **Callback Hell Elimination**: NEVER nest callbacks more than 2 levels deep. ALWAYS convert callback-based APIs to Promises using `async/await`.
- **Default Parameters**: ALWAYS use native default parameters (`function fn(param = defaultValue) {}`) instead of manually checking `param || defaultValue` inside function bodies.

---

## 3. ASYNCHRONOUS JS, PROMISES & ERROR HANDLING
- **Async/Await Standard**: PREFER `async/await` syntax over raw `.then() / .catch()` promise chains for readability and unified stack traces.
- **Top-Level Await**: Utilize top-level `await` in ES Modules (`.mjs` or `<script type="module">`) for clean initialization logic.
- **Try-Catch Block Requirement**: EVERY `await` call MUST be wrapped inside a `try-catch` block or handled by a high-level error boundary.
- **AbortController for Timeouts**: ALWAYS pass an `AbortSignal` (via `AbortController`) to `fetch()` requests to prevent hanging promises and network resource leaks.
- **Safe JSON Parsing**: NEVER call `JSON.parse()` directly on unvalidated strings without a `try-catch` block wrapper or a safe parsing utility.

---

## 4. DOM MANIPULATION, EVENTS & PERFORMANCE
- **Modern DOM Queries**: Use `document.querySelector()` and `querySelectorAll()`. Use `element.closest()` for ancestor lookups and `element.matches()` for selector validation.
- **XSS Prevention**: NEVER use `innerHTML` with untrusted or dynamic user input. ALWAYS use `textContent` or construct nodes safely via `document.createElement()` and `Element.append()`.
- **Batch DOM Updates**: Minimize browser reflows and repaints. Use `DocumentFragment` or `Element.replaceChildren()` when inserting multiple DOM nodes.
- **Event Delegation**: PREFER attaching a single event listener to a parent container (using `event.target.closest()`) over binding individual listeners to many child elements.
- **Memory Leak Prevention**: ALWAYS remove unused event listeners (`removeEventListener`) or use `{ once: true }` / `AbortSignal` for temporary event listeners.

---

## 5. STATE MANAGEMENT & STATE HOISTING
- **Single Source of Truth (SSOT)**: Maintain application state in a centralized JavaScript object or top-level module state.
- **State Hoisting**: Lift shared state up to the nearest common parent module or container rather than storing application state directly inside DOM attributes (`data-*`).
- **Immutability**: PREFER immutable state updates. Use `structuredClone()` for deep object cloning or spread syntax (`{ ...state }`, `[...arr]`) for shallow copies.
- **Decoupled Event Bus**: Use native `EventTarget` or `CustomEvent` to dispatch and listen to custom application state changes across decoupled components.

---

## 6. ES MODULES, CLASSES & DATA STRUCTURES
- **Native ES Modules (ESM)**: Use explicit `import` and `export` statements. ALWAYS include explicit file extensions in relative imports (e.g., `import { utils } from './utils.js';`).
- **Modern ES Classes**:
  - Use Native Private Fields (`#privateField`) for strict encapsulation.
  - Use `static` fields and methods for utility classes or factory instances.
- **Modern Array & Object APIs**:
  - Use immutable array methods: `.toSorted()`, `.toSpliced()`, `.toReversed()`, and `.with()` over mutating methods (`.sort()`, `.splice()`, `.reverse()`).
  - Use `Object.groupBy()` or `Array.prototype.reduce()` for data grouping.
  - Use `Set` for unique collection lookups (O(1) time complexity) instead of `Array.includes()` in loops (O(N)).

---

## 7. TRUTHY/FALSY & EDGE CASE HANDLING
- **Truthy/Falsy Awareness**: Be explicit when evaluating falsy values (`false`, `0`, `""`, `null`, `undefined`, `NaN`).
- **Guard Clauses**: Use early `return` guard clauses at the beginning of functions to handle invalid input or edge cases early.
- **Array & Object Integrity Checks**: ALWAYS verify Array length (`Array.isArray(arr) && arr.length > 0`) and Object keys (`Object.keys(obj).length > 0`) before executing iteration logic.

---

## 8. AGENT EXECUTION DIRECTIVES
- Prioritize native browser APIs over importing external third-party libraries (Zero-Dependency Vanilla First).
- Apply the "Fix Terkecil yang Aman" principle: modify only target files and preserve existing working codebase structure.
- Always verify that written code runs natively in modern evergreen browsers without requiring transpilations or legacy polyfills.
