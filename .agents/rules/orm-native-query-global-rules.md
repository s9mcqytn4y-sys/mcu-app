---
trigger: glob
globs: **/models/**/*, **/entities/**/*, **/mappers/**/*, **/repositories/**/*, **/queries/**/*, **/*.sql, **/prisma/schema.prisma, **/typeorm/**/*
---

# ORM & NATIVE QUERY ARCHITECTURE STANDARDS (2026 HIGH-SCALE RULES)

## 1. NAMING CONVENTIONS & SCHEMATIC HARMONY

Systems MUST maintain 100% strict naming mapping between DB Schemas and Application Models:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                     DATABASE VS ORM NAMING MAPPING                     │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
          ┌─────────────────────────┴─────────────────────────┐
          ▼                                                   ▼
┌───────────────────────────────────┐               ┌───────────────────────────────────┐
│     DATABASE LAYER (SQL/DDL)      │               │     APPLICATION / ORM LAYER       │
├───────────────────────────────────┤               ├───────────────────────────────────┤
│ • Tables: snake_case, PLURAL      │ ◄───────────► │ • Models: PascalCase, SINGULAR    │
│   (e.g., `user_profiles`)         │   (Mapping)   │   (e.g., `UserProfile`)           │
│ • Columns: snake_case, SINGULAR   │ ◄───────────► │ • Fields: camelCase / snake_case  │
│   (e.g., `first_name`)            │               │   (e.g., `firstName`)             │
│ • Foreign Keys: `<table>_id`      │ ◄───────────► │ • Relations: Singular / Plural    │
│   (e.g., `category_id`)           │               │   (`category()`, `products()`)    │
└───────────────────────────────────┘               └───────────────────────────────────┘

Pivot / Junction Tables:Pure Join Tables: Combine singular names in alphabetical order (category_product).Domain Explicit Pivot Tables: Use business domain terms when the entity carries context (order_items, subscription_renewals).ORM Relationship Methods:hasOne / belongsTo: MUST use Singular method name (user(), author(), order()).hasMany / belongsToMany: MUST use Plural method name (orders(), roles(), comments()).2. HIGH-EFFICIENCY DATA TYPES & FOOTPRINT OPTIMIZATIONChoosing the smallest datatype that safely accommodates domain boundaries reduces I/O bandwidth and maximizes CPU cache page efficiency:Data DomainMySQL / MariaDBPostgreSQLEfficiency JustificationSmall Counters / EnumsTINYINT UNSIGNED (1 Byte)SMALLINT (2 Bytes)Saves up to 75% memory vs 8-Byte BIGINT.Standard Primary KeyBIGINT UNSIGNED (8 Bytes)BIGINT / IDENTITYPrevents 32-bit INT overflow (2.1B limits).Monetary Amounts / CashDECIMAL(15,4)NUMERIC(15,4)NEVER use FLOAT/DOUBLE (causes binary floating-point rounding errors).Status / State MachineVARCHAR(30) / TINYINTVARCHAR(30) / ENUMHuman-readable in native queries without joining lookup tables.Dynamic Payload / MetaJSONJSONBPostgreSQL JSONB stores decomposed binary data, enabling GIN indexing.TimestampsDATETIME(3)TIMESTAMPTZALWAYS store in UTC with millisecond precision ((3)).3. SCALABLE UNIQUE IDENTIFIERS (UUID v7, ULID, TSID)Avoid random UUID v4 as primary keys on clustered indexes (causes severe B-Tree page fragmentation and write amplification).Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                        UUID v7 STRUCTURE (128-BIT)                     │
├───────────────────────────────────┬────────────────────────────────────┤
│   48-bit Unix Timestamp (ms)      │   74-bit Cryptographic Randomness  │
├───────────────────────────────────┴────────────────────────────────────┤
│ ✅ Time-ordered sorting          │ ✅ B-Tree locality friendly         │
│ ✅ Zero central lock required     │ ✅ Zero ID enumeration attacks     │
└────────────────────────────────────────────────────────────────────────┘
Identifier Selection Strategy MatrixUUID v7 (Preferred Default for Microservices & APIs): Time-sortable 128-bit structure.TSID / Snowflake ID: 64-bit integer format. Best choice if frontend requires native numeric JavaScript ID representation without precision loss ($< 2^{53} - 1$).Internal Sequential Auto-Increment: Permitted ONLY for internal/non-public lookup tables hidden behind API gateways.4. CONSTRAINTS, DEFAULT VALUES & CASCADE POLICIESForeign Key Cascade Decision MatrixALWAYS define explicit Foreign Key constraints at the database engine level (never rely solely on application-level validations):Cascade StrategyWhen to ApplyDomain ExampleON DELETE RESTRICT (Default)Primary relational entities where child records contain business/audit value.users -> orders (Deleting user MUST fail if orders exist).ON DELETE CASCADEStrictly owned sub-entities that have NO independent existence outside parent.orders -> order_items, invoices -> invoice_taxes.ON DELETE SET NULLOptional associations where child entity remains valid without parent reference.articles -> author_id (If author is deleted, keep article content).Default ValuesDatabase schema MUST enforce defaults at DB level (DEFAULT CURRENT_TIMESTAMP, DEFAULT 0, DEFAULT 'draft') so native SQL scripts and ORMs behave identically.5. INDEXING, FOREIGN KEYS & JOIN EFFICIENCYA. Composite Indexing Ordering Rule (ESR Principle)When creating composite indexes, order columns by Equality, Sort, then Range:Equality (=): Columns filtered by exact match first (tenant_id, status).Sort (ORDER BY): Columns used for sorting.Range (>, <, LIKE, BETWEEN): Columns used for range filtering (created_at).SQL-- ✅ BENAR: Mengikuti prinsip ESR
CREATE INDEX idx_orders_tenant_status_created
ON orders (tenant_id, status, created_at DESC);
B. JOIN Query Optimization RulesIndexed Join Columns: Every column involved in ON a.fk_id = b.id MUST be indexed.Explicit Column Projection: STRICTLY FORBIDDEN to use SELECT * in JOIN queries. Project ONLY required columns to prevent memory buffer pool bloating:SQL-- ✅ BENAR: Proyeksi kolom spesifik
SELECT o.id, o.total_amount, u.email
FROM orders o
INNER JOIN users u ON o.user_id = u.id;
Join Depth Boundary: Max 3-4 JOINs per real-time OLTP query. For deeper analytical reads, use Read Replicas, Denormalized Views, or CQRS patterns.6. LOADING STRATEGIES & N+1 PROBLEM MITIGATIONThe N+1 Problem occurs when an ORM executes 1 query for a parent collection and N additional queries for child relations inside a loop.Plaintext ❌ UN-OPTIMIZED (N+1 Queries):
 SELECT * FROM posts; -- 1 Query (returns 100 rows)
 SELECT * FROM users WHERE id = 1; -- +100 Queries inside loop!
 Total: 101 Database roundtrips.

 ✅ EAGER LOADED (2 Queries):
 SELECT * FROM posts; -- 1 Query
 SELECT * FROM users WHERE id IN (1, 2, 3, ... 100); -- 1 Query
 Total: 2 Database roundtrips.
Mandatory Rules for ORM LoadingDisable Lazy Loading in Non-Production/CI:Laravel: Model::preventLazyLoading(!app()->isProduction());Prisma: Explicit include or select blocks required.Eager Loading Default: Always use with(['user', 'comments']) or include when serializing collection responses.Batch / Keyset Pagination for Large Datasets:BANNED: OFFSET 100000 (causes full table scan up to offset).MANDATORY: Keyset/Cursor Pagination (WHERE id > last_seen_id LIMIT 50).7. NORMALIZATION VS PRAGMATIC DENORMALIZATIONDefault (3NF - Third Normal Form): Write operations MUST target fully normalized tables to prevent update anomalies and duplicate data states.Pragmatic Denormalization Threshold: Permitted for ultra-high-frequency read counters (comments_count, likes_count, total_amount_cached).Denormalization Governance:Cached denormalized columns MUST be updated atomically via Database Triggers or DB Transactions.Provide a background reconciliation worker to audit and fix counter drifts periodically.8. CONCURRENCY, RACE CONDITIONS & LOCKINGPrevent race conditions (e.g., negative stock, double-spending) using appropriate locking tiers:A. Atomic SQL Updates (First Line of Defense)For simple mathematical operations, execute atomic in-place updates:SQL-- ✅ BENAR: Bebas dari Race Condition tanpa lock berat
UPDATE products
SET stock = stock - 1
WHERE id = 42 AND stock >= 1;
B. Optimistic Locking (High Read / Low Conflict)Add a version or updated_at column:SQLUPDATE accounts
SET balance = balance - 100, version = version + 1
WHERE id = 10 AND version = 3;
-- Jika baris yang ter-update == 0, lemparkan ConcurrentModificationException
C. Pessimistic Locking (High Conflict / Flash Sales)Explicitly lock rows during transaction read:SQL-- PostgreSQL / MySQL
SELECT * FROM inventory WHERE product_id = 100 FOR UPDATE;
9. TRANSACTIONS, ISOLATION & CONNECTION POOLINGA. Transaction Boundaries PolicyKeep Transactions Micro-Short: NEVER perform external HTTP API calls, heavy disk I/O, or email dispatching INSIDE a database transaction block.Structure:Validate Input & Fetch External Data (Outside DB Tx).BEGIN TRANSACTION.Execute DB Mutations.COMMIT.Dispatch External Notifications / Queues (Outside DB Tx).B. Connection Pool Sizing FormulaDo NOT guess connection pool sizes. Use the standard PostgreSQL/HikariCP formula:$$\text{Pool Size} = (\text{CPU Cores} \times 2) + \text{Effective Spindle/Disk Count}$$Example: An 8-Core server with SSD requires a pool size of roughly $(8 \times 2) + 1 = 17$ connections. Excessive connection pools cause CPU context switching degradation.10. COLUMN ENCRYPTION, CACHING & SEARCH ENGINESColumn-Level Encryption: Encrypt PII columns before passing data to ORM persistence layer using AES-256-GCM.Query Caching Strategy:Cache Key Naming: entity:<id> or query:<entity>:<md5_hash>.Invalidate cache explicitly on ORM updated/deleted model hooks.Search Engine Offloading:Database LIKE '%search%' queries on large text columns are STRICTLY FORBIDDEN.For simple text matching: Use PostgreSQL GIN tsvector Full-Text Search.For advanced multi-faceted search: Offload data sync via CDC (Change Data Capture) or ORM events to Meilisearch / Elasticsearch.11. AGENT EXECUTION DIRECTIVESAlways ensure generated native SQL or ORM queries contain explicit parameter bindings (? or :param) to prevent SQL Injection.Enforce strict Eager Loading on ORM queries to prevent N+1 performance bottlenecks.Apply the "Fix Terkecil yang Aman" principle: optimize DB queries and indexes without altering public model contracts or breaking API schemas.
