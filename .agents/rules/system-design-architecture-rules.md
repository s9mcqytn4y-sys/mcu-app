---
trigger: glob
globs: docs/**/*.md, .agents/**/*.md, **/ADR/*.md, **/*architecture*.md, **/*design*.md
---

# SYSTEM DESIGN, SOFTWARE ARCHITECTURE & ANTI-OVER-ENGINEERING RULES

## 1. COMPREHENSIVE SOFTWARE ARCHITECTURE CATALOG
The AI Agent MUST understand the characteristics, trade-offs, and cost-complexity profile of all general software architectures:

### A. Monolithic Architectures
1. **Classic Monolith (Layered / MVC)**: Single codebase, single deployment unit, single database. *(Highest cost-efficiency, lowest operational complexity)*.
2. **Modular Monolith**: Single deployment unit with strict internal boundary decoupling (bounded contexts). *(Best default path before microservices)*.

### B. Distributed & Cloud-Native Architectures
3. **Microservices Architecture**: Multiple independently deployable services communicating via network protocols. *(High operational cost, high complexity; requires dedicated DevOps team)*.
4. **Event-Driven Architecture (EDA)**: Decoupled services communicating via asynchronous event buses (RabbitMQ, Kafka, Redis Pub/Sub).
5. **Serverless / FaaS Architecture**: On-demand function execution (AWS Lambda, Cloudflare Workers, Supabase Functions).
6. **Service-Oriented Architecture (SOA)**: Enterprise-level web services sharing enterprise service buses (ESB).

### C. Domain & Code-Level Architectural Patterns
7. **Clean / Hexagonal / Ports & Adapters Architecture**: Isolation of core domain logic from external frameworks, DBs, and APIs.
8. **CQRS (Command Query Responsibility Segregation)**: Separation of read and write data models.
9. **Event Sourcing**: Storing full history of state changes as an immutable log of events instead of current state.

### D. Frontend & Edge Architectures
10. **Jamstack / Edge Architecture**: Pre-rendered static assets combined with edge serverless APIs.
11. **Microfrontends**: Splitting frontend monoliths into independently deployed sub-applications.
12. **Monolith with Reactive Islands**: Server-side rendered HTML (Laravel Blade / Inertia / Livewire) enhanced with lightweight reactive islands (Alpine.js / Vue / React).

---

## 2. THE "CHEAP, EFFECTIVE & EFFICIENT" DOCTRINE (PRAGMATIC ARCHITECTURE MATRIX)
The AI Agent MUST strictly prioritize simplicity, low operational overhead, and speed of delivery.

### Default System Selection Matrix:
- **Default for MVPs, Small-to-Medium Enterprises (SME), & Single-Team Systems**:
  - **Architecture**: Modular Monolith OR Classic Layered Monolith.
  - **Infrastructure**: Single Cloud VPS (Hostinger / Hetzner / DigitalOcean) or PaaS (Vercel / Render).
  - **Database**: Single PostgreSQL or MySQL instance.
  - **Background Jobs**: Database-backed Queue / Redis Queue.
  - **Frontend**: Server-Side Rendering (SSR) with Reactive Islands (Livewire / Inertia + Tailwind).

- **STRICT FORBIDDEN DEFAULTS**:
  - NEVER propose Microservices, Kubernetes (K8s), Kafka, gRPC, or Polyglot Databases for a new project unless traffic exceeds **100,000+ Daily Active Users (DAU)** OR explicit organizational multi-team boundary requirements exist.

---

## 3. ANTI-OVER-ENGINEERING DIRECTIVES (STRICT BAN LIST)

### Rule A: Ban Premature Abstraction & Premature Optimization
- **YAGNI (You Aren't Gonna Need It)**: NEVER build generic "future-proof" abstraction layers for features that do not exist today.
- **KISS (Keep It Simple, Stupid)**: PREFER direct, readable code over deeply nested abstract classes, dynamic reflection, or complex design pattern chains (e.g., Factory + Strategy + Builder inside a simple CRUD module).
- **Rule of Three**: Do NOT abstract code into reusable functions/utilities until it has been duplicated at least THREE times.

### Rule B: Ban Premature Distributed Complexity
- **No Unnecessary Message Brokers**: Do NOT introduce Kafka/RabbitMQ if a simple PostgreSQL queue, Redis queue, or background job runner (Laravel Queue / BullMQ) is sufficient.
- **No Unnecessary NoSQL**: Do NOT introduce MongoDB/DynamoDB alongside PostgreSQL unless storing unstructured data exceeding gigabytes per day.
- **No Unnecessary Microfrontends**: Do NOT split frontend applications into Microfrontends unless different sub-teams deploy them independently.

### Rule C: Cost & Resource Optimization Limits
- **Maximized Resource Usage**: Always choose architecture options that minimize monthly cloud server bills ($5 - $50/month tier for MVPs).
- **Zero-Vendor Lock-in First**: Prefer open-source, standard Linux/Docker deployments over proprietary cloud-vendor locked services unless explicitly requested.

---

## 4. PROJECT MANAGEMENT & ARCHITECTURAL HIERARCHY

The AI Agent MUST respect and operate within a strict 4-Tier Governance Hierarchy:

```text
┌─────────────────────────────────────────────────────────┐
│ TIER 1: CLIENT / PRODUCT OWNER (Business Goals & Scope) │
└────────────────────────────┬────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────┐
│ TIER 2: SYSTEM ARCHITECT (AI Lead - Pragmatic System)   │
└────────────────────────────┬────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────┐
│ TIER 3: TECH LEAD / TECH PARTNER (Quality & Security)   │
└────────────────────────────┬────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────┐
│ TIER 4: EXECUTION AGENT (Code Implementation)          │
└─────────────────────────────────────────────────────────┘


Governance Rules:
Tier 1 Authority (Scope Locking): Functional requirements defined in BRD/FSD are immutable unless explicitly altered via Change Request (FM-PM-001).

Tier 2 Decision Justification: Every system architecture recommendation MUST include an Architectural Decision Record (ADR) format:

Context: Business problem to solve.

Decision: Proposed architecture / library.

Cost & Complexity Justification: Why this is the cheapest, most effective, and lowest-maintenance solution.

Alternatives Rejected: Why microservices/complex patterns were rejected.

Escalation Trigger: If a human user asks for an overly complex architecture (e.g., "Build me a simple blog using Microservices and Kubernetes"), the AI MUST gently educate and recommend the simpler, pragmatic alternative (Modular Monolith on VPS) before proceeding.

5. INFRASTRUCTURE & TECH STACK COST-BENEFIT BENCHMARK
When choosing a technology stack, evaluation MUST prioritize the following cost-effectiveness scores:

Stack / Pattern	Operational Cost	Complexity	Speed to Market	AI Recommendation Status
Monolith (Laravel / Rails / Django)	🟢 Very Low ($5/mo)	🟢 Low	🚀 Fast (Days)	PREFERRED DEFAULT
Modular Monolith + Inertia/Livewire	🟢 Low ($10/mo)	🟢 Low-Med	🚀 Fast (Days)	PREFERRED DEFAULT
SPA (React/Vue) + REST/GraphQL API	🟡 Medium	🟡 Medium	🐢 Medium (Weeks)	ACCEPTABLE (If mobile app shares API)
Serverless (Lambda + DynamoDB)	🟡 Medium (Pay-per-use)	🟡 Medium	🐢 Medium	CONDITIONALLY ACCEPTABLE
Microservices + Docker + K8s	🔴 Very High ($200+/mo)	🔴 Extreme	🐌 Slow (Months)	STRICTLY DISCOURAGED (Unless Enterprise)
6. AGENT EXECUTION DIRECTIVES
Mandatory Justification: Before generating new services, databases, or complex design abstractions, verify if a simpler monolithic or native framework feature already exists.

Enforce Single Database: Default to ONE primary relational database (PostgreSQL/MySQL) for all domain entities.

Apply the "Fix Terkecil yang Aman": Keep code changes localized, cost-effective, and safe without refactoring system architecture mid-project.

Format Compliance: Always output architectural specs with clear, structured Markdown sections and concise cost trade-off summaries.
