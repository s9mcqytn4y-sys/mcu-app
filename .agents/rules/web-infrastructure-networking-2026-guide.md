---
trigger: glob
globs: **/.htaccess, **/nginx.conf, **/*.conf, **/routes/**/*
---

# WEB INFRASTRUCTURE, NETWORKING, HTTP PROTOCOLS & SERVER RULES (2026 STANDARDS)

## 1. IDENTIFIERS & NETWORKING FUNDAMENTALS (URI, URL, URN)
- **Identifier Precision**:
  - **URI (Uniform Resource Identifier)**: The umbrella term for identifying resources.
  - **URL (Uniform Resource Locator)**: MUST specify explicit protocol, domain/host, port, path, and query string (e.g., `https://api.domain.com:443/v1/users?status=active`).
  - **URN (Uniform Resource Name)**: Used for persistent, location-independent resource identification (e.g., `urn:isbn:978-3-16-148410-0`).
- **Network Interface Bindings**:
  - `127.0.0.1` / `localhost`: Local loopback interface ONLY. NEVER bind to `127.0.0.1` inside Docker containers or public web servers if external incoming connections are expected.
  - `0.0.0.0`: Binds to ALL available network interfaces. MANDATORY for Docker containers, local network testing, and reverse proxy targets.
- **Standard Port Assignments**:
  - `80`: Unencrypted HTTP Traffic.
  - `443`: Encrypted HTTPS Traffic (TLS 1.3).
  - `22`: SSH Administration.

---

## 2. DNS (DOMAIN NAME SYSTEM) & DOMAIN RESOLUTION
- **DNS Record Standards**:
  - **A Record**: Points a hostname to a static IPv4 address.
  - **AAAA Record**: Points a hostname to an IPv6 address.
  - **CNAME Record**: Alias pointing one domain to another canonical domain. NEVER place a CNAME record on a root apex domain (`@`); use CNAME only for subdomains (`www`, `api`).
  - **MX Record**: Defines mail exchange servers with explicit priority ranks.
  - **TXT Record**: Used for verification proofs, SPF (`v=spf1 ...`), DKIM, and DMARC security policies.
- **Propagation & TTL (Time-To-Live)**:
  - During server migrations, reduce DNS TTL to `300` seconds (5 minutes) at least 24 hours BEFORE the cutover to ensure rapid propagation. Set back to `86400` seconds (24 hours) post-migration.

---

## 3. HTTP/HTTPS PROTOCOLS & STATUS CODE MATRIX

### A. Protocol Standards
- **HTTPS Enforcement**: ALL web applications MUST enforce HTTPS via TLS 1.3/1.2. Plain HTTP MUST permanently redirect (301) to HTTPS.
- **HTTP/2 & HTTP/3 (QUIC)**: Enable HTTP/2 multiplexing and HTTP/3 (QUIC) on production web servers for zero-round-trip time (0-RTT) connection handshakes.

### B. HTTP Response Status Code Matrix
The AI Agent MUST issue and handle HTTP status codes strictly according to their RFC semantic definitions:

| Status Code | Category | Precise Semantic Usage |
| :--- | :--- | :--- |
| **200 OK** | Success | Standard successful HTTP request with response payload. |
| **201 Created** | Success | Resource successfully created via `POST` (MUST return `Location` header or created object). |
| **204 No Content** | Success | Request succeeded, but response body intentionally empty (common for `DELETE` / `OPTIONS`). |
| **301 Moved Permanently** | Redirect | Permanent URL relocation. Search engine indexers MUST update SEO target link. |
| **302 Found / 307 Temp** | Redirect | Temporary URL relocation. Search engines maintain original URL index. |
| **400 Bad Request** | Client Error | Malformed request syntax, invalid JSON payload, or missing mandatory headers. |
| **401 Unauthorized** | Client Error | Missing or invalid authentication token/credentials (User is not logged in). |
| **403 Forbidden** | Client Error | Authenticated, but user lacks permission/role scope to access resource. |
| **404 Not Found** | Client Error | Requested URI path or database entity does not exist. |
| **409 Conflict** | Client Error | State conflict (e.g., attempting to register an email that already exists). |
| **422 Unprocessable** | Client Error | Validation error (Data format correct, but payload fails domain validation rules). |
| **429 Too Many Requests** | Client Error | Rate limit exceeded. MUST include `Retry-After` header. |
| **500 Internal Error** | Server Error | Unhandled server exception or application code crash. |
| **502 Bad Gateway** | Server Error | Reverse proxy (Nginx/Apache) failed to connect to upstream app (Node/PHP-FPM/Docker). |
| **503 Unavailable** | Server Error | Server capacity overload or active maintenance mode. |
| **504 Gateway Timeout** | Server Error | Upstream application took too long to respond to the proxy server. |

### C. Security Headers Requirements
ALL production web server responses MUST include the following security headers:
```http
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()

4. WEB SERVERS: APACHE & .HTACCESS STANDARDS
Directory Listing Ban: ALWAYS disable directory browsing globally:

Apache
Options -Indexes
Pretty URLs & Rewrite Rules (mod_rewrite):
Standard .htaccess rule for routing all traffic to index.php (Laravel/Custom Frameworks):

Apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
Performance: Enable Gzip/Brotli compression via mod_deflate and set explicit browser caching headers via mod_expires.

5. WEB SERVERS: NGINX ARCHITECTURE & REVERSE PROXY
Syntax Validation Directives: BEFORE reloading or restarting Nginx, ALWAYS validate configuration file syntax via terminal:

Bash
sudo nginx -t
Nginx Reverse Proxy Block Template:

Nginx
server {
    listen 80;
    listen [::]:80;
    server_name api.example.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.example.com;

    ssl_certificate /etc/letsencrypt/live/[api.example.com/fullchain.pem](https://api.example.com/fullchain.pem);
    ssl_certificate_key /etc/letsencrypt/live/[api.example.com/privkey.pem](https://api.example.com/privkey.pem);

    # Security Headers
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;

    location / {
        proxy_pass [http://127.0.0.1:8000](http://127.0.0.1:8000);
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
}
6. WEB SERVICES, WEBHOOKS & LOCAL TUNNELING (NGROK)
A. RESTful Web Services Design
Use HTTP Verbs correctly: GET (Read), POST (Create), PUT (Replace), PATCH (Partial Update), DELETE (Remove).

Resource Endpoints MUST use plural nouns in URLs (e.g., /api/v1/customers instead of /api/v1/getCustomer).

B. Webhook Verification & Idempotency
Signature Verification: ALL incoming webhooks MUST be verified using HMAC SHA256 signatures passed in request headers.

Idempotency: Webhook handlers MUST record processed webhook event IDs to prevent duplicate processing on automatic provider retries.

C. Ngrok & Local Tunneling Rules
Port Mapping: Map Ngrok explicitly to local application server ports:

Bash
ngrok http http://localhost:8000
Dynamic CORS & Webhook Testing:

When testing webhooks via Ngrok, dynamically update local .env variables (APP_URL=https://xxxx.ngrok-free.app) to ensure generated callback links match the active Ngrok tunnel.

NEVER commit ephemeral Ngrok URLs (*.ngrok-free.app) to production repository configuration files.

7. AGENT EXECUTION DIRECTIVES
Verify web server syntax (nginx -t or apachectl configtest) BEFORE executing service reloads.

Apply the "Fix Terkecil yang Aman" principle: update specific location blocks or .htaccess directives without breaking existing VirtualHost/SSL bindings.

Ensure all generated API responses include strict HTTP status codes, JSON content-type headers (Content-Type: application/json), and appropriate CORS origins.
