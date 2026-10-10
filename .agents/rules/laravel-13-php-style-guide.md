---
trigger: glob
globs: **/*.php, **/composer.json, **/routes/*.php
---

# LARAVEL 13.X & PHP 8.+ CODING STYLE & ARCHITECTURE RULES

## 1. GENERAL PRINCIPLES & PHP 8.+ SYNTAX
- **Strict Typing**: ALWAYS declare `declare(strict_types=1);` at the top of EVERY PHP file.
- **Type Hinting**: ALWAYS explicitly specify argument types and return types for all functions and methods. Use `void` if no value is returned.
- **Constructor Property Promotion**: ALWAYS use constructor property promotion for dependency injection and class properties.
- **Readonly Classes & Properties**: Use `readonly class` or `readonly` properties for Value Objects, Data Transfer Objects (DTOs), and immutable domain classes.
- **Typed Constants**: ALWAYS specify explicit types for class constants (PHP 8.3+): `public const string DEFAULT_STATUS = 'active';`.
- **Match Expressions**: PREFER `match()` expressions over `switch` statements or nested `if-else` chains.
- **Backed Enums**: ALWAYS use Backed Enums (`enum Status: string`) instead of string constants or magic numbers.
- **First-Class Callables**: Use `[Class, 'method']` or `Class::method(...)` for callables instead of string-based function references.

---

## 2. LARAVEL 13 SPECIFIC STANDARDS

### A. Architecture & Controllers
- **Thin Controllers**: Controllers MUST ONLY handle HTTP requests, invoke business logic, and return responses. Do NOT place raw DB queries or heavy business logic in Controllers.
- **Single Responsibility**: PREFER Invokable Controllers (`__invoke()`) for single-action endpoints, or standard RESTful resource methods (`index`, `store`, `show`, `update`, `destroy`).
- **Form Request Validation**: NEVER validate incoming requests inside Controller methods. ALWAYS use dedicated Form Request classes (`php artisan make:request`).
- **Response Formatting**: ALWAYS use Eloquent API Resources (`JsonResource`) or Inertia/Blade views for HTTP responses. NEVER return raw Eloquent Models or Arrays directly.

### B. Eloquent & Database Layer
- **Casts Method**: ALWAYS use the modern `casts(): array` method inside Eloquent Models instead of the legacy `$casts` property.
- **Mass Assignment Guarding**: Define `$fillable` explicitly in Models. Using `$guarded = []` is STRICTLY FORBIDDEN.
- **N+1 Query Prevention**: ALWAYS use Eager Loading (`with()`) for relationships. Never execute queries inside loops.
- **Monetary & Decimal Precision**: ALWAYS use `decimal(15, 2)` or `decimal(12, 2)` for currency and precise measurements. NEVER use `float` or `double`.
- **Anonymous Migrations**: ALWAYS use anonymous migrations (`return new class extends Migration`).
- **Explicit Foreign Keys**: ALWAYS define explicit Foreign Key constraints with deletion actions (`cascadeOnDelete()`, `restrictOnDelete()`).

### C. Business Logic & Domain Layer
- **Action / Service Classes**: Place complex business domain logic inside dedicated Action or Service classes (e.g., `App\Actions\Orders\CreateOrderAction`).
- **Database Transactions**: Wrap multi-table database operations inside `DB::transaction(function () { ... })` to ensure atomic execution and automatic rollback on failure.

---

## 3. CODE STYLE & FORMATTING (PER CS / PSR-12)
- **Formatting Standard**: Strictly follow PER CS 2.0 / PSR-12 formatting conventions (Laravel Pint default).
- **Naming Conventions**:
  - Classes, Interfaces, Enums, Traits: `PascalCase`
  - Methods and Functions: `camelCase`
  - Variables and Properties: `camelCase`
  - Database Tables & Columns: `snake_case` (Plural tables, singular columns)
  - Route Names: `kebab-case` with dot notation (e.g., `work-orders.store`)
- **Imports**: Group imports alphabetically. Unused `use` statements MUST be removed.

---

## 4. ERROR HANDLING & SECURITY
- **Domain Exceptions**: Throw domain-specific Custom Exceptions instead of generic `\Exception`.
- **Environment Variables Scope**: NEVER call `env()` directly outside of `config/*.php` files. ALWAYS access configuration via `config('services.key')` in application code to support config caching.
- **SQL Injection Prevention**: ALWAYS use Eloquent ORM or Query Builder with Parameter Binding. NEVER concatenate unescaped raw strings into raw SQL queries.

---

## 5. AGENT EXECUTION DIRECTIVES
- BEFORE modifying or creating any PHP/Laravel code, inspect existing patterns in the repository to maintain architectural consistency.
- Apply the "Fix Terkecil yang Aman" principle: apply precise modifications without refactoring unrelated working files.
- Always verify that created code compiles without syntax errors and adheres to static analysis rules.
