---
trigger: glob
globs: **/controllers/**/*, **/services/**/*, **/repositories/**/*, **/domain/**/*, **/usecases/**/*, **/modules/**/*, **/*Controller*, **/*Service*, **/*Repository*, **/*UseCase*
---

# GLOBAL ARCHITECTURE LAYERS, BOUNDARIES & DEPENDENCY RULES (2026 STANDARDS)

## 1. THE GOLDEN RULES OF LAYERED ARCHITECTURE

Systems MUST strictly obey 3 foundational laws regardless of architecture level:

1. **The Law of Dependency Direction (Point Inward)**: High-level business policies MUST NEVER depend on low-level implementation details (Frameworks, Web Servers, Database Engines). Dependencies MUST always point inward toward business rules.
2. **The Law of Adjacent Communication**: A layer MUST only communicate with its immediate lower layer or via explicitly defined Abstractions (Interfaces / Ports).
3. **The Law of Data Boundary Isolation**: Never leak HTTP Request/Response objects to the Business or Database layers. Never expose Database ORM entities directly to the Web/API Presentation layer.

---

## LEVEL 1: CLASSIC LAYERED / MVC ARCHITECTURE (SIMPLEST DEFAULT)

Recommended for **MVPs, Small-to-Medium Enterprise (SME) Apps, & Single-Team Projects**.

```text
 Client Request ──► [ Presentation Layer ] ──► [ Service Layer ] ──► [ Persistence Layer ] ──► Database
                       (Controllers/UI)        (Business Logic)       (Repositories/Models)
Layer A: Presentation Layer (Controllers / Handlers)Primary Responsibility: Receive HTTP/CLI input, validate payload format (Form Request / Zod), invoke Service Layer, and return HTTP Response (JSON / HTML DTO).Allowed Dependencies: Service Layer Interfaces, Request/Response DTOs.STRICT BANNED IN CONTROLLER:❌ NO Raw SQL or ORM queries (User::where(...) inside Controller IS STRICTLY FORBIDDEN).❌ NO Business Calculations (e.g., calculating tax, discount formulas, or user eligibility).❌ NO Direct Database Transactions (DB::beginTransaction() belongs in the Service Layer).Layer B: Service / Business Logic LayerPrimary Responsibility: Execute business domain rules, orchestrate multiple operations, manage database transactions, and enforce permissions.Allowed Dependencies: Repository Interfaces, Domain Entities, External Service Adapters (Mailer, Payment Gateway).STRICT BANNED IN SERVICE:❌ NO HTTP Context: Never import $request, Request, Response, or HTTP Status Code constants inside Services.❌ NO Direct HTML Rendering or View Templates.Layer C: Persistence / Data Access Layer (Repositories / Models)Primary Responsibility: Execute database queries (ORM or Native SQL), manage database constraints, and map database rows to Data Objects.Allowed Dependencies: Database Connection Pool, ORM Engine, Table Entities.STRICT BANNED IN PERSISTENCE:❌ NO Business Decisions: Repositories only perform CRUD/Query operations; they do not decide if an order should be approved.LEVEL 2: MODULAR MONOLITH ARCHITECTURE (SCALABLE DEFAULT)Recommended for Growing Applications & Multi-Feature Teams before splitting into Microservices.Plaintext┌─────────────────────────────────────────────────────────────────────────┐
│                           MODULAR MONOLITH                              │
├─────────────────────────┬───────────────────────┬───────────────────────┤
│    [ USER MODULE ]      │   [ ORDER MODULE ]    │   [ PAYMENT MODULE ]  │
│  - Controller           │  - Controller         │  - Controller         │
│  - Service              │  - Service            │  - Service            │
│  - Private DB Schema    │  - Private DB Schema  │  - Private DB Schema  │
└───────────┬─────────────┴───────────┬───────────┴───────────┬───────────┘
            │                         │                       │
            └─────────────────────────┼───────────────────────┘
                                      ▼
                        [ Module Public Contracts API ]
Rules & Boundaries for Modular Monoliths:Module Autonomy: Each module (e.g., OrderModule, PaymentModule) acts as a self-contained mini-application with its own Controllers, Services, and Models.STRICT DB BOUNDARY RULE: Module A MUST NEVER execute direct JOIN queries or ORM queries against Module B's database tables.Cross-Module Communication: Modules communicate ONLY via:Public Module Contracts / Interfaces (Synchronous function calls).Internal Domain Events / Event Bus (Asynchronous decoupled events).LEVEL 3: CLEAN / HEXAGONAL ARCHITECTURE (PORTS & ADAPTERS)Recommended for Complex Enterprise Core Systems & Mission-Critical Applications.Plaintext                       ┌─────────────────────────────────────┐
                       │   Frameworks & Drivers (External)   │
                       │   (PostgreSQL, Express, AWS S3)     │
                       └──────────────────┬──────────────────┘
                                          │
                       ┌──────────────────▼──────────────────┐
                       │     Interface Adapters Layer        │
                       │  (Controllers, Repositories Impl)   │
                       └──────────────────┬──────────────────┘
                                          │
                       ┌──────────────────▼──────────────────┐
                       │     Application Use Cases Layer     │
                       │   (CreateOrder, CancelSubscription) │
                       └──────────────────┬──────────────────┘
                                          │
                       ┌──────────────────▼──────────────────┐
                       │     Domain Core (Pure Entities)     │
                       │   (Order, Money, DiscountPolicy)    │
                       └─────────────────────────────────────┘
Layer A: Domain Core (Center - Purest Layer)Responsibility: Enterprise Business Rules, Entities, Value Objects, and Domain Events.Constraints: ZERO EXTERNAL DEPENDENCIES. No framework imports, no ORM annotations, no HTTP libraries. Pure programming language constructs only.Layer B: Application Layer (Use Cases / Commands / Queries)Responsibility: Application-specific business workflows (e.g., RegisterUserUseCase). Defines Ports (Interfaces) for Repositories, Queue Dispatchers, and Mailers.Constraints: Depends ONLY on the Domain Core. Knows nothing about PostgreSQL, MySQL, Redis, or Web Frameworks.Layer C: Interface Adapters (Gateways / Controllers / Presenters)Responsibility: Implements Adapters for the Ports defined in the Application Layer. Converts raw data from HTTP/CLI into Use Case Input DTOs, and converts Use Case Output DTOs to HTTP Responses.Layer D: Frameworks & Drivers (Outer Boundary)Responsibility: Web framework infrastructure, Database Drivers, Redis Cache, AWS SDKs, Third-party APIs.LEVEL 4: MICROSERVICES & EVENT-DRIVEN ARCHITECTURE (DISTRIBUTED)Recommended ONLY for Large Enterprises with 100,000+ DAU & Independent Sub-Teams.Plaintext Client ──► [ API Gateway ] ──► (HTTP/gRPC) ──► [ Order Service ] ──► [ Isolated DB A ]
                                                       │
                                              (Dispatches Event)
                                                       ▼
                                            [ Event Bus (Kafka/Redis) ]
                                                       │
                                              (Consumes Event)
                                                       ▼
                                             [ Payment Service ] ──► [ Isolated DB B ]
Constraints & Architectural Boundaries:Database-Per-Service Rule: Every Microservice OWNS its database exclusively. Direct cross-database connections across network boundaries are STRICTLY FORBIDDEN.Asynchronous Decoupling: Prefer Event-Driven Communication (Kafka / RabbitMQ / Redis PubSub) over synchronous REST calls to prevent cascading network failures.Resilience Boundaries: All inter-service REST/gRPC calls MUST incorporate Circuit Breakers, Timeouts, and Retry Policies with Exponential Backoff.DEPENDENCY INJECTION (DI) & DATA FLOW MATRIXTo maintain clean boundaries, always pass data between layers using strict Data Transfer Objects (DTOs):Data Flow PhaseSource LayerTarget LayerData Format PassedInjection Pattern1. Request EntryHTTP / ClientControllerRaw HTTP Request / JSONFramework Routing2. ValidationControllerServicePrimitive / Request DTOConstructor DI (UserService)3. Logic ExecutionServiceRepositoryDomain Entity / Command DTOInterface DI (UserRepositoryInterface)4. PersistenceRepositoryDatabase EngineSQL Statement / ORM QueryDB Connection Pool5. Response ReturnServiceControllerOutput DTO / Result PrimitiveReturn Statement6. Client OutputControllerHTTP / ClientResponse JSON / Rendered ViewHTTP Response StreamAGENT EXECUTION DIRECTIVESEnforce Inward Dependency Rule: Never generate code where high-level domain entities or services import low-level framework classes or HTTP controllers.Isolate Database Models: Never pass ORM models directly to frontend views or API responses without a mapping/DTO layer.Apply "Fix Terkecil yang Aman": Maintain clean architectural layer boundaries without introducing unnecessary abstraction over-engineering for simple tasks.
