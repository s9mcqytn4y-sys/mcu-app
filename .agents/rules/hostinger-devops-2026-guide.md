---
trigger: glob
globs: **/.htaccess, **/nginx.conf, **/*.conf, .github/workflows/*.{yml,yaml}, **/*.sh, **/.env*
---

# HOSTINGER PLATFORM, INFRASTRUCTURE & DEVOPS (2026 STANDARDS) RULES

## 1. HOSTINGER ENVIRONMENT DETECTION & ENVIRONMENT BOUNDARIES
The AI Agent MUST detect the target Hostinger environment before suggesting deployment or terminal commands:

### A. Hostinger Web Hosting & Cloud Hosting (hPanel - Managed Environment)
- **Architecture**: Managed LiteSpeed Web Server (LSWS) with MariaDB and hPanel Control Panel.
- **RESTRICTIONS**:
  - STRICTLY FORBIDDEN to suggest `sudo`, `systemctl`, `docker`, or custom Linux package installations (`apt-get`).
  - STRICTLY FORBIDDEN to assume Nginx configuration files (`nginx.conf`). MUST use LiteSpeed-compatible `.htaccess` rules instead.
  - Node.js applications MUST use the native hPanel Node.js App Selector or Passenger wrapper.
  - Background processes MUST use hPanel Scheduled Tasks (Cron Jobs) instead of systemd daemons.

### B. Hostinger VPS Hosting (KVM Virtualization - Unmanaged Environment)
- **Architecture**: Full Root Access Linux (Ubuntu 24.04 LTS / 22.04 LTS recommended).
- **CAPABILITIES**:
  - Full terminal authority: Nginx/Apache reverse proxy, Docker, Systemd services, PostgreSQL, Redis, PM2, and custom firewalls.

---

## 2. DEPLOYMENT ARCHITECTURE DECISION MATRIX

The AI Agent MUST align technology stack recommendations with Hostinger product capabilities:

| Technology Stack | Target Hostinger Product | Web Server Engine | Deployment Method |
| :--- | :--- | :--- | :--- |
| **WordPress / Classic PHP / Static Web** | Web / Cloud Hosting | LiteSpeed (LSWS) | hPanel Git / FTP / File Manager |
| **Laravel / Livewire / Inertia (Standard)** | Cloud Hosting / VPS | LiteSpeed or Nginx | hPanel Git Auto-Deploy / SSH |
| **Node.js API / Express / NestJS / Next.js** | VPS Hosting (KVM) | Nginx Reverse Proxy | PM2 / Docker / Systemd |
| **Custom Containers / PostgreSQL / Redis** | VPS Hosting (KVM) | Nginx / Traefik | Docker Compose / Coolify |

---

## 3. HOSTINGER WEB & CLOUD HOSTING (HPANEL) RULES
- **LiteSpeed Caching (LSCache)**: ALWAYS optimize `.htaccess` for LiteSpeed Cache and web asset compression (Gzip/Brotli).
- **PHP Configuration**:
  - Set PHP version to **PHP 8.2 or 8.3+** via hPanel.
  - Ensure mandatory extensions are enabled: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `curl`, `zip`, `gd`.
- **Public Folder Mapping**: For frameworks with public web roots (e.g., Laravel), map the domain document root to `public_html/public` via hPanel or `.htaccess` redirect rules.
- **SSL Enforcement**: ALWAYS enforce HTTPS redirection via hPanel SSL Auto-Enforce or LiteSpeed `.htaccess` rules.

---

## 4. HOSTINGER VPS & DEVOPS INFRASTRUCTURE RULES

### A. Server Security Hardening
- **SSH Key Authentication**: Disable password-based SSH root login in `/etc/ssh/sshd_config`. Use SSH key pairs exclusively.
- **UFW Firewall**: Enforce minimal open ports:
  ```bash
  ufw default deny incoming
  ufw default allow outgoing
  ufw allow 22/tcp    # SSH
  ufw allow 80/tcp    # HTTP
  ufw allow 443/tcp   # HTTPS
  ufw enable
Brute-Force Protection: Install and enable fail2ban for SSH and web authentication protection.

B. Nginx Reverse Proxy Standard
Configure Nginx as a reverse proxy for Node.js, Python, or Docker applications running on internal ports (e.g., 127.0.0.1:3000):

Nginx
server {
    listen 80;
    server_name example.com [www.example.com](https://www.example.com);

    location / {
        proxy_pass [http://127.0.0.1:3000](http://127.0.0.1:3000);
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
C. Process Management (PM2 / Systemd)
For Node.js applications on VPS, manage background processes using PM2 or native Systemd services:

Bash
pm2 start app.js --name "production-api" --instances max
pm2 save
pm2 startup
D. Automated SSL Certification (Certbot)
Issue free SSL certificates using EFF Certbot with automatic Nginx configuration:

Bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d example.com -d [www.example.com](https://www.example.com) --non-interactive --agree-tos -m admin@example.com
5. DOMAIN, DNS ZONE & TRANSACTIONAL MAIL SECURITY
When configuring Hostinger Domains and DNS Zone Management, the AI Agent MUST ensure complete mail and domain validation records:

A / AAAA Records: Point @ and www to the Hostinger Server IP (Cloud Dedicated IP or VPS Public IP).

SPF Record (TXT): Include Hostinger mail servers: v=spf1 include:hostinger.com ~all

DKIM Record (TXT): Ensure DKIM key provided by Hostinger Webmail or transactional mail provider is published.

DMARC Record (TXT): Publish explicit DMARC policy: v=DMARC1; p=none; rua=mailto:dmarc-reports@example.com

6. CI/CD PIPELINE & AUTOMATED DEPLOYMENT (GITHUB ACTIONS)
hPanel Git Auto-Deploy: Configure Hostinger hPanel Webhook URL inside GitHub Repository Webhook settings for instant main branch deployments.

VPS Deployment via GitHub Actions (Zero-Downtime):
Use SSH action pipelines for automated deployment:

YAML
name: Deploy to Hostinger VPS

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout Code
        uses: actions/checkout@v4

      - name: Deploy via SSH
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: ${{ secrets.HOSTINGER_VPS_IP }}
          username: ${{ secrets.HOSTINGER_VPS_USER }}
          key: ${{ secrets.HOSTINGER_SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/my-app
            git pull origin main
            npm install --production
            pm2 reload production-api
7. AGENT EXECUTION DIRECTIVES
NEVER recommend unsupported tools on Hostinger Shared/Cloud Hosting (e.g., Docker, Supervisor, custom Nginx modules).

Apply the "Fix Terkecil yang Aman" principle: modify deployment scripts and web server configs without breaking existing site bindings.

Always double-check IP addresses, domain names, and database connection strings before generating environment configurations.
