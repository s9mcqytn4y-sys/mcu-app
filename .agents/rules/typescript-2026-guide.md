---
trigger: glob
globs: **/*.ts, **/*.tsx, **/*.mts, **/*.cts, **/tsconfig*.json
---

# TYPESCRIPT (2026 STANDARDS - TS 5.X+) CODING, COMPILER & ARCHITECTURE RULES

## 1. COMPILER STRICTNESS & TSCONFIG.JSON STANDARDS
- **Strict Mode Primacy**: ALL projects MUST enable `"strict": true` in `tsconfig.json`.
- **Mandatory Strictness Flags**:
  - `"noUncheckedIndexedAccess": true` -> Mandatory! Forces array/object index lookups to include `| undefined` handling.
  - `"exactOptionalPropertyTypes": true` -> Prevents assigning `undefined` explicitly to optional properties unless specified in union.
  - `"noImplicitOverride": true` -> Enforces `override` keyword when overriding class methods in derived classes.
  - `"verbatimModuleSyntax": true` -> Enforces clean type-only imports/exports for fast type stripping and bundler execution.
  - `"isolatedModules": true` -> Ensures files can be safely transpiled individually by native Node.js type strippers, Vite, or esbuild.
- **Module Resolution**: Use `"moduleResolution": "nodenext"` for backend/Node.js or `"bundler"` for Vite/frontend setups.

---

## 2. NULL SAFETY, STRICT TYPING & MODERN OPERATORS
- **BAN ON `any`**:
  - The use of `any` is STRICTLY FORBIDDEN.
  - ALWAYS use `unknown` for values of unknown types, and narrow them using Type Guards (`is`), `instanceof`, `typeof`, or Schema validation libraries (Zod/Valibot).
- **BAN ON Non-Null Assertion Operator (`!`)**:
  - NEVER force type assertion using `!` (e.g., `user!.name` is forbidden).
  - Use Optional Chaining (`user?.name`), Nullish Coalescing (`??`), or explicit `if (!user) throw ...` type guard checks instead.
- **`satisfies` Operator**:
  - Use `satisfies` when validating that an object matches a type interface WITHOUT widening or losing its exact literal inference:
    ```typescript
    const config = {
      endpoint: '/api/v1',
      timeout: 5000,
    } satisfies Record<string, unknown>;
    ```
- **Explicit Resource Management (`using` / `await using`)**:
  - ALWAYS use native TC39 `using` or `await using` declarations for automatic cleanup of disposable resources (database connections, file handles, locks) instead of manual `try/finally` blocks:
    ```typescript
    await using connection = await dbPool.getConnection();
    ```

---

## 3. TYPES VS INTERFACES & DATA STRUCTURES
- **Interfaces**: Use `interface` for public API contracts, Object-Oriented Class definitions, and extensible library shapes.
- **Type Aliases (`type`)**: Use `type` for Unions, Intersections, Primitives, Tuples, Function Signatures, Mapped Types, and Utility Type transformations.
- **BAN ON TypeScript `enum`**:
  - STRICTLY FORBIDDEN to use standard TypeScript `enum`. Enums emit bloated runtime JavaScript objects.
  - PREFER String Union Types or `as const` object maps:
    ```typescript
    // ✅ PREFERRED
    export const OrderStatus = {
      Pending: 'PENDING',
      Completed: 'COMPLETED',
    } as const;
    export type OrderStatus = (typeof OrderStatus)[keyof typeof OrderStatus];
    ```
- **Immutability First**:
  - Use `readonly` for array and object properties that should not be mutated after creation (`readonly string[]`, `Readonly<User>`).
- **Data Structures**:
  - Use `Map<K, V>` and `Set<T>` for dynamic key-value lookups and unique collections with strict generic typing.

---

## 4. SYNTAX, ES MODULES & TYPE-ONLY IMPORTS
- **Explicit Type Imports**:
  - MUST use explicit `import type` and `export type` syntax to allow complete type stripping by runtimes:
    ```typescript
    import type { User, OrderId } from './types.js';
    import { userService } from './userService.js';
    ```
- **Explicit Return Types**:
  - ALL exported functions, class methods, and API endpoints MUST explicitly specify return types. Do NOT rely solely on type inference for public interfaces.

---

## 5. FRONTEND & COMPONENT TYPING (`.tsx` STANDARDS)
- **BAN ON `React.FC` / `React.FunctionComponent`**:
  - DO NOT type components using `React.FC<Props>`.
  - Type component props directly as plain interface/type objects in function parameters:
    ```tsx
    interface ButtonProps {
      readonly label: string;
      readonly onClick: (event: React.MouseEvent<HTMLButtonElement>) => void;
      readonly children?: React.ReactNode;
    }

    export function Button({ label, onClick, children }: ButtonProps): React.JSX.Element {
      return <button onClick={onClick}>{label}{children}</button>;
    }
    ```
- **Event Handler Typing**: ALWAYS use explicit React event types (`React.ChangeEvent<HTMLInputElement>`, `React.FormEvent<HTMLFormElement>`).

---

## 6. JSON PARSING & TYPE-SAFE ALGORITHMS
- **Safe JSON Parsing**:
  - Native `JSON.parse()` returns `any` by default. ALWAYS cast the output of `JSON.parse()` to `unknown` immediately, or parse using a validator function:
    ```typescript
    function safeJsonParse<T>(jsonString: string): T | null {
      try {
        return JSON.parse(jsonString) as T;
      } catch {
        return null;
      }
    }
    ```
- **Generic Algorithm Constraints**:
  - Type parameter generics in algorithms MUST extend strict base bounds (`<T extends Record<string, unknown>>` or `<T extends { id: string }>`) rather than unbound `<T>`.

---

## 7. DECORATORS, REFLECTION & DEPENDENCY INJECTION (DI)
- **Standard ECMAScript Stage 3 Decorators**:
  - Use native Stage 3 Decorators (TS 5.0+) without requiring `experimentalDecorators` flag when building domain abstractions.
- **Constructor-Based Dependency Injection**:
  - PREFER Lightweight Constructor Injection using Interfaces over heavy reflection-based DI containers:
    ```typescript
    export class OrderService {
      constructor(
        private readonly repository: OrderRepositoryInterface,
        private readonly logger: LoggerInterface
      ) {}
    }
    ```

---

## 8. TSDOC, COMMENTS & CODE DOCUMENTATION
- **TSDoc Standard**: Use TSDoc format (`/** ... */`) for exported APIs, public interfaces, and domain actions.
- **Document Intent, Not Syntax**:
  - Use `@param`, `@returns`, `@throws`, and `@deprecated` tags.
  - DO NOT write redundant inline comments explaining what code obviously does. Explain *WHY* an edge case is handled or business rule exists.
    ```typescript
    /**
     * Calculates tax rates based on regional compliance rules (BRD Ref: FM-SA-001).
     *
     * @param amount - Base monetary value in decimal format.
     * @throws {InvalidAmountException} If amount is negative.
     */
    export function calculateTax(amount: number): number { ... }
    ```

---

## 9. AGENT EXECUTION DIRECTIVES
- NEVER fix type errors by casting to `any` or using non-null assertion `!`.
- Always generate TypeScript code that compiles under `"strict": true` and `"noUncheckedIndexedAccess": true`.
- Apply the "Fix Terkecil yang Aman" principle: update type definitions cleanly without breaking existing interface contracts or introducing unnecessary generic complexity.
