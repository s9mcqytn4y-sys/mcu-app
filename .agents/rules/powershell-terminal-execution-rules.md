---
trigger: glob
globs: **/*.ps1, **/*.psm1, **/*.sh, **/*.bat, **/*.cmd, **/.vscode/tasks.json
---

# POWERSHELL & TERMINAL AI EXECUTION (2026 STANDARDS) SAFETY RULES

## 1. SHELL ENGINE SELECTION & COMPATIBILITY
- **Default Shell Binary**: ALWAYS prefer modern PowerShell 7+ Core (`pwsh` / `pwsh.exe`) over legacy Windows PowerShell 5.1 (`powershell.exe`).
- **Cross-Platform Terminal Standards**:
  - Write terminal commands that are compatible with PowerShell 7+ on Windows, macOS, and Linux.
  - ALWAYS use native Cmdlets or standard PWSH aliases in execution scripts (`Get-ChildItem`, `Remove-Item`, `Copy-Item`, `Get-Content`) rather than relying on OS-specific legacy binaries (`dir`, `del`, `copy`, `type`).

---

## 2. DESTRUCTIVE COMMAND SAFEGUARDS & GUARDRAILS (STRICT BAN LIST)
- **NO Automatic Unconfirmed Destruction**: The AI Agent MUST NEVER execute commands that result in unrecoverable data loss without explicit user confirmation:
  - `Remove-Item -Recurse -Force` / `rm -rf` on top-level project directories or system folders.
  - `git reset --hard` / `git clean -fd` when uncommitted work exists.
  - `Format-Volume`, `Remove-Partition`, `Clear-Disk`.
  - Mass process termination: `Stop-Process -Force` or `taskkill /f` on system/critical processes.
- **Dry-Run Requirement**: Use the `-WhatIf` or `-Confirm` parameter first when executing potentially dangerous file/system manipulations to inspect affected targets before actual execution:
  ```powershell
  Remove-Item -Path "./dist/*" -Recurse -WhatIf
3. SYNTAX, OPERATORS & PATH HANDLING (PWSH 7.x FEATURES)
Modern PWSH 7 Operators:

Use native PWSH 7 chaining operators && (AND) and || (OR) instead of legacy -and/-or string chaining hacks:

PowerShell
npm run build && npm test
Use Null-Coalescing ?? and Ternary operators ? : inside scripts for concise condition evaluation.

Path Escaping & Spaces Handling:

ALWAYS quote file paths containing spaces using single quotes ('C:\My Folder\file.txt') or double quotes with variable expansion ("$env:USERPROFILE\project").

Use Join-Path or forward slashes (/) for cross-platform directory concatenation (PowerShell 7 handles / natively on Windows).

Encoding Requirements:

Ensure all created/modified script files use UTF-8 without BOM encoding (Set-Content -Encoding utf8NoBOM or Out-File -Encoding utf8).

4. ERROR HANDLING, PIPELINES & CONTEXT WINDOW CLEANUP
Strict Error Action: Enforce strict error handling in automation scripts:

PowerShell
$ErrorActionPreference = 'Stop'
Pipeline Data Handling:

Keep raw pipeline objects intact during processing (Where-Object, Select-Object, Sort-Object) before applying visual formatting (Format-Table, Format-List).

Use ConvertTo-Json -Depth 10 when outputting structured object data for LLM analysis.

Context Window Output Control:

Suppress verbose, unnecessary output from fill/setup commands to keep the AI context window clean:

PowerShell
$null = New-Item -ItemType Directory -Path "./logs" -Force
# OR
npm install --quiet | Out-Null
5. ASYNCHRONOUS & NON-BLOCKING EXECUTION
No Terminal Freezing: Long-running background processes (e.g., npm run dev, php artisan serve, docker compose up) MUST NOT be executed synchronously in the foreground if it blocks the AI agent terminal loop.

Background Execution: Use Start-Process, Start-Job, or background execution flags when launching persistent local servers/services:

PowerShell
Start-Process -FilePath "php" -ArgumentList "artisan serve" -NoNewWindow
6. AGENT EXECUTION DIRECTIVES
Inspect local directory context (Get-Location) before executing relative path commands.

Never output or log plain-text passwords, API keys, or JWT tokens to terminal output streams.

Apply the "Fix Terkecil yang Aman" principle: modify target files without polluting system PATH environment variables or breaking global shell configurations.

Verify that created .ps1 scripts execute cleanly without triggering ExecutionPolicy restrictions (use -ExecutionPolicy Bypass if running ephemeral script blocks).
