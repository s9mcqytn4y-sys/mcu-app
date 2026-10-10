---
trigger: glob
globs: **/package.json, **/composer.json, **/requirements.txt, **/Cargo.toml, **/go.mod, **/*.md
---

# CONTEXT7 DOCUMENTATION FETCHER DIRECTIVE & CLI EXECUTION RULES

## 1. PURPOSE & TRIGGER CONDITIONS
Use the `ctx7` CLI tool to fetch official, up-to-date documentation whenever the user asks about an external library, framework, SDK, API, CLI tool, or cloud service (e.g., React, Next.js, Prisma, Express, Tailwind CSS, Django, Spring Boot, Laravel, Filament, Vue, etc.).

### A. When to Fetch
- The user asks about syntax, API signatures, migration guides, or best practices for third-party packages.
- The local codebase relies on library versions where API signatures might have changed or evolved.

### B. When NOT to Fetch (Bypass)
- The user asks about standard native language features (e.g., standard JavaScript loops, native PHP string functions).
- The answer is already explicitly fully documented within the project's local repository files or active rule files.

---

## 2. CLI EXECUTION WORKFLOW (`npx ctx7@latest`)

The AI Agent MUST follow this strict 4-step execution chain:

### Step 1: Resolve Library Canonical ID
Run the resolution command to search Context7 registry:
```bash
npx ctx7@latest library <canonical_name> "<user_full_question>"
Step 2: Select Best Match
Analyze the output list and pick the most relevant /org/project canonical library ID matching the target technology and framework version.

Step 3: Fetch Documentation Payload
Fetch targeted documentation chunks matching the canonical library ID:

Bash
npx ctx7@latest docs <libraryId> "<user_full_question>"
Step 4: Synthesize Response
Formulate a precise, type-safe answer using the retrieved Context7 documentation payload, adhering to the project's coding rules.

3. OPERATIONAL SAFETY & TERMINAL GUARDRAILS
Execution Limit: Strictly DO NOT execute more than 3 ctx7 CLI calls per single user prompt to prevent terminal loop bloat.

Zero-Secrets Rule: NEVER pass API keys, passwords, database URIs, or authentication tokens inside the <user_full_question> or query parameters.

Strict Library ID Order: ALWAYS execute library resolution first unless an explicit, valid libraryId is already provided in the prompt.

Non-Blocking Execution: Run CLI commands in non-blocking terminal mode to keep the agent execution pipeline responsive.

4. ERROR HANDLING & QUOTA FALLBACK
Quota / Auth Error Protocol:
If the CLI returns rate limits, quota failures, or authentication errors, immediately notify the user with actionable instructions:

"Gagal mengambil dokumentasi terbaru dari Context7 karena batasan kuota/autentikasi. Silakan jalankan npx ctx7@latest login di terminal Anda atau atur variabel lingkungan CONTEXT7_API_KEY."

Package Not Found Fallback:
If ctx7 returns zero matching library IDs, proceed to answer the prompt using standard local analysis while explicitly informing the user that dynamic Context7 docs were unavailable.

5. AGENT EXECUTION DIRECTIVES
Prioritize real-time Context7 documentation over stale pre-trained knowledge when generating third-party API implementation code.

Apply the "Fix Terkecil yang Aman" principle: implement fetched API patterns cleanly without introducing unnecessary external dependencies.
