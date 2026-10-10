---
trigger: glob
globs: .env*, **/config/**/*.php, **/bootstrap/app.php, **/routes/console.php, **/*.env.example
---

# LARAVEL 13.X ENVIRONMENT BOUNDARIES, .ENV MANAGEMENT & SECURITY RULES

## 1. ENVIRONMENT HIERARCHY & BOUNDARY ISOLATION

Systems MUST maintain absolute logical, data, and service isolation across environment boundaries:

```text
┌────────────────────────────────────────────────────────────────────────┐
│                  LARAVEL 13.X ENVIRONMENT ISOLATION                    │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
    ┌───────────────────────────────┼───────────────────────────────┐
    ▼                               ▼                               ▼
┌───────────────────────┐ ┌───────────────────────┐ ┌───────────────────────┐
│ 1. LOCAL / DEV        │ │ 2. STAGING / QA       │ │ 3. PRODUCTION         │
│ (`APP_ENV=local`)     │ │ (`APP_ENV=staging`)   │ │ (`APP_ENV=production`)│
├───────────────────────┤ ├───────────────────────┤ ├───────────────────────┤
│ • APP_DEBUG=true      │ │ • APP_DEBUG=false     │ │ • APP_DEBUG=false     │
│ • Local DB / SQLite   │ │ • Staging DB (Anonym) │ │ • Production DB Cluster│
│ • Mailpit / Log Mail  │ │ • Sandbox API Keys    │ │ • Live Payment / APIs │
│ • Queue Driver = sync │ │ • Redis Queue Driver  │ │ • Redis Queue / Valkey│
└───────────────────────┘ └───────────────────────┘ └───────────────────────┘

Environment Matrix ComparisonConfiguration KeyLocal (local)Staging (staging)Production (production)APP_DEBUGtruefalse (MANDATORY)false (CRITICAL SECURITY)DB_CONNECTIONsqlite / pgsql_localpgsql_stagingpgsql_prod (Master/Replica)QUEUE_CONNECTIONsync / redisredisredis / valkeyCACHE_STOREfile / redisredisredisMAIL_MAILERmailpit / logsmtp (Mailtrap / Test)postmark / ses / smtpFILESYSTEM_DISKlocals3_staging / minios3_production / gcsPAYMENT_MODEsandboxsandboxlive / production2. THE GOLDEN LAW OF env() VS config()In Laravel 13.x, whenever php artisan config:cache or php artisan optimize is executed during deployment, Laravel completely disables reading directly from .env files.Plaintext ❌ STRICTLY FORBIDDEN IN APPLICATION CODE:
 $apiKey = env('MIDTRANS_SERVER_KEY'); // Will return NULL in production when config is cached!

 ✅ MANDATORY PATTERN:
 1. Define key in .env -> MIDTRANS_SERVER_KEY=SB-Mid-server-xxx
 2. Map key in config/services.php -> 'server_key' => env('MIDTRANS_SERVER_KEY')
 3. Read in code -> $apiKey = config('services.midtrans.server_key');
Directives:Zero env() outside /config: The env() helper function MUST NEVER be called anywhere in application code (Controllers, Services, Repositories, Jobs, Views). Call config('domain.key') instead.All dynamic keys MUST have a fallback value defined inside the config/*.php files.3. .ENV.EXAMPLE SYNCHRONIZATION & SECRET HYGIENEA. .env.example Maintenance.env.example MUST be committed to Git repositories and serve as the Single Source of Truth for all required configuration keys.When developer adds a new environment key to .env, they MUST simultaneously append it to .env.example with dummy/placeholder values.NO SECRETS IN .env.example: Never place real database passwords, API keys, or private certificates inside .env.example.B. .gitignore ComplianceThe main .env file containing actual secrets MUST ALWAYS be ignored by version control. Ensure .gitignore explicitly includes:Code snippet.env
.env.backup
.env.production
.env.staging
.env.local
*.pem
*.key
storage/certs/
4. LARAVEL 13.X ENVIRONMENT ENCRYPTION (env:encrypt)For team collaboration or secure deployment setups, Laravel 13.x supports encrypting environment files using AES-256-CBC.Plaintext┌────────────────────────────────────────────────────────────────────────┐
│                   LARAVEL ENVIRONMENT ENCRYPTION                       │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
 1. Developer Encrypts: `php artisan env:encrypt --env=production`
    └── Generates: `.env.production.encrypted` + Encryption Key
 2. Commit `.env.production.encrypted` safely to Git.
 3. CI/CD Server Decrypts during build:
    └── `php artisan env:decrypt --env=production --key=<LARAVEL_ENV_ENCRYPTION_KEY>`
Encryption Commands Standard:Encrypt: php artisan env:encrypt --env=productionDecrypt: php artisan env:decrypt --env=production --key=base64:...5. STANDARD PRODUCTION .ENV TEMPLATE (REFERENCE)Ini, TOML# ==============================================================================
# APPLICATION IDENTIFICATION & SECURITY
# ==============================================================================
APP_NAME="Enterprise App"
APP_ENV=production
APP_KEY=base64:xK9... # Generated via php artisan key:generate
APP_DEBUG=false
APP_URL="[https://app.domain.com](https://app.domain.com)"
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

# ==============================================================================
# LOGGING CONFIGURATION
# ==============================================================================
LOG_CHANNEL=daily
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# ==============================================================================
# DATABASE CONFIGURATION (POSTGRESQL 16)
# ==============================================================================
DB_CONNECTION=pgsql
DB_HOST=10.0.1.50
DB_PORT=5432
DB_DATABASE=enterprise_prod_db
DB_USERNAME=db_app_user
DB_PASSWORD="STRONG_SECRET_PASSWORD_HERE"
DB_SSLMODE=prefer

# ==============================================================================
# REDIS / VALKEY (SESSION, CACHE, QUEUE)
# ==============================================================================
REDIS_CLIENT=phpredis
REDIS_HOST=10.0.1.60
REDIS_PASSWORD="STRONG_REDIS_PASSWORD_HERE"
REDIS_PORT=6379

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

# ==============================================================================
# MAIL CONFIGURATION
# ==============================================================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.postmarkapp.com
MAIL_PORT=587
MAIL_USERNAME="POSTMARK_SERVER_TOKEN"
MAIL_PASSWORD="POSTMARK_SERVER_TOKEN"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# ==============================================================================
# OBJECT STORAGE (AWS S3 / CLOUDFLARE R2)
# ==============================================================================
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID="AKIA..."
AWS_SECRET_ACCESS_KEY="SecretKey..."
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=app-production-assets
AWS_USE_PATH_STYLE_ENDPOINT=false

# ==============================================================================
# THIRD-PARTY INTEGRATIONS (PAYMENT & LOGISTICS)
# ==============================================================================
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_SERVER_KEY="Mid-server-..."
MIDTRANS_CLIENT_KEY="Mid-client-..."

BITESHIP_API_KEY="biteship_live...."
6. CI/CD DEPLOYMENT & CONFIG CACHING DIRECTIVESDuring production deployment pipeline execution, the CI/CD script MUST run optimization commands in sequence:Bash# 1. Decrypt Environment File (if encrypted)
php artisan env:decrypt --env=production --key=$LARAVEL_ENV_ENCRYPTION_KEY --force

# 2. Run Database Migrations (Forced)
php artisan migrate --force

# 3. Clear Old Caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Cache Configurations & Routes (Laravel 13.x Optimize)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Restart Background Workers
php artisan queue:restart
php artisan horizon:terminate
7. EDGE CASES & SECURITY AUDIT MATRIXFailure ScenarioSecurity / Operational RiskRequired System Mitigation RuleAPP_DEBUG=true in ProductionRaw database credentials, API keys, and stack traces exposed on error pages.Enforce CI/CD pipeline check that fails deployment if APP_DEBUG is true.env() Called in ControllerReturns null unexpectedly after running php artisan config:cache.Use Static Analysis (PHPStan / Pest) rule banning env() calls outside config/.Missing APP_KEYSession payloads and encrypted database columns fail to decrypt.Run php artisan key:generate during initial setup. Ensure APP_KEY is backed up.Leaked .env in Git RepoCredential compromise across infrastructure.Run gitleaks pre-commit hook; rotate ALL compromised keys immediately.Staging Sending Real EmailsReal customers receive test/fake notifications.Hardcode MAIL_MAILER=log or override destination emails in staging config/mail.php.8. AGENT EXECUTION DIRECTIVESZero env() outside /config: Never generate application code (Services, Controllers, Jobs) that directly calls env(). Always map .env variables into a /config/*.php file first.Strict Debug Disabling: Always set APP_DEBUG=false in production configuration examples.Apply "Fix Terkecil yang Aman": Maintain config fallbacks, secret key isolation, and environment encryption pipelines when modifying setup or configuration files.
