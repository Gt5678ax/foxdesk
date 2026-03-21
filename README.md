<p align="center">
  <a href="https://foxdesk.org">
    <img src="https://foxdesk.org/logo.png" alt="FoxDesk Logo" width="80" />
  </a>
</p>

<h1 align="center">FoxDesk</h1>

<p align="center">
  <strong>Helpdesk & time tracking — for your team and your AI agents.</strong><br/>
  Self-hosted. Open-source. No per-agent fees, ever.
</p>

<p align="center">
  <a href="https://github.com/lukashanes/foxdesk/releases/latest"><img src="https://img.shields.io/github/v/release/lukashanes/foxdesk?color=blue&label=latest" alt="Latest Release" /></a>
  <a href="https://github.com/lukashanes/foxdesk/blob/main/LICENSE.md"><img src="https://img.shields.io/badge/license-AGPL--3.0-green" alt="License" /></a>
  <a href="https://github.com/lukashanes/foxdesk/stargazers"><img src="https://img.shields.io/github/stars/lukashanes/foxdesk?style=social" alt="GitHub Stars" /></a>
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.1+" />
  <img src="https://img.shields.io/badge/MySQL-8.0%2B-4479A1?logo=mysql&logoColor=white" alt="MySQL" />
</p>

<p align="center">
  <a href="https://foxdesk.org">Website</a> · <a href="https://foxdesk.org/features/">Features</a> · <a href="https://foxdesk.org/download/">Download</a> · <a href="https://github.com/lukashanes/foxdesk/discussions">Discussions</a>
</p>

---

<!-- Replace with an actual GIF or screenshot. Ideal: 800×450px animated GIF showing the dashboard, creating a ticket, and timer running. -->
<p align="center">
  <a href="https://foxdesk.org">
    <img src="https://cdn.foxdesk.org/screens/ticket-detail-dark.png" alt="FoxDesk Screenshot" width="720" />
  </a>
</p>

## Why FoxDesk?

Most helpdesks charge **per seat, per month**. FoxDesk is different:

| | FoxDesk | Zendesk | Freshdesk | osTicket |
|---|:---:|:---:|:---:|:---:|
| **Price** | Free forever | $19–115/agent/mo | $15–79/agent/mo | Free |
| **Time Tracking** | ✅ Built-in | ❌ Add-on | ❌ Add-on | ❌ No |
| **AI Agent API** | ✅ Native | ❌ No | ❌ No | ❌ No |
| **Self-hosted** | ✅ | ❌ | ❌ | ✅ |
| **Install time** | 5 minutes | — | — | 30+ min |
| **No Docker/Node** | ✅ PHP only | — | — | ✅ |

**Your AI agents are doing real work. FoxDesk lets them report it.**

## ⚡ 5-Minute Install

No Docker. No Node. No build pipeline. Upload to any PHP host and run the wizard.

```bash
# 1. Download
wget https://github.com/lukashanes/foxdesk/releases/latest/download/foxdesk.zip

# 2. Extract & upload to your web server
unzip foxdesk.zip

# 3. Open in browser
# → https://your-domain.tld/install.php
# → Follow the wizard (DB credentials + admin account)
# → Done. Start resolving tickets.
```

Works on: DigitalOcean, Hetzner, AWS, any shared hosting (cPanel, DirectAdmin), VPS.

📖 [Detailed install guide →](https://foxdesk.org/installation/shared-hosting/)

## 🎯 Key Features

### Tickets
Create, assign, resolve, and archive. Email-to-ticket via IMAP. Collision detection. Rich text with drag-and-drop attachments. Bulk actions. Full-text search. Public sharing via secure links.

### Time Tracking & Billing
Built-in timers with start/pause/resume. Billable vs. non-billable hours. Cost rates per user, billing rates per organization. Profitability reports. PDF & CSV export.

### AI Agent Integration
Give Claude, GPT, Cursor, or your custom bots their own accounts. They create tickets, post updates, and **log their own hours** — through a dead-simple REST API:

```bash
# Your AI agent logs 45 minutes of work
curl -X POST https://your-domain.tld/api/agent-log-time \
  -H "Authorization: Bearer fd_live_abc123" \
  -H "Content-Type: application/json" \
  -d '{
    "ticket_id": 542,
    "minutes": 45,
    "description": "Automated security scan completed. 3 issues found and patched.",
    "billable": true
  }'
```

The **Agent Connect** page generates a ready-to-use system prompt for any AI model — copy, paste, done.

### Reports & Analytics
Report builder with date ranges, filters, and KPI dashboards. Financial reports showing billable hours, costs, and profit margins. Shareable report links with expiration.

### And more…
🌍 **5 languages** — EN, CS, DE, ES, IT (per-user preference)
🌙 **Dark mode** — auto-adapts to your OS
📱 **PWA** — install as native app on desktop & mobile
🔄 **Recurring tasks** — scheduled ticket creation
🔔 **Granular notifications** — per-ticket, per-user, email + in-app
🏢 **Organizations** — multi-org with custom billing rates
🔒 **Roles** — Admin, Agent, Client with granular permissions

## 🏗 Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.1+ (no framework) |
| Database | MySQL 8.0+ / MariaDB 10.5+ |
| Frontend | Tailwind CSS + Alpine.js |
| Theming | CSS variables with dark mode |

## 📁 Project Structure

```
index.php              → Entry point & router
install.php            → Web installer
config.example.php     → Configuration template
includes/              → Core PHP (auth, DB, API, i18n)
includes/api/          → REST API handlers
pages/                 → Page controllers
pages/admin/           → Admin panel
assets/js/             → JavaScript modules
bin/                   → CLI scripts (cron, email, maintenance)
```

## ⏰ Cron Jobs (optional)

FoxDesk includes a **pseudo-cron** that runs on page load — no system cron required on shared hosting.

```cron
*/5 * * * * php /path/to/bin/ingest-emails.php       # Email ingestion
0   * * * * php /path/to/bin/process-recurring-tasks.php  # Recurring tasks
0   3 * * * php /path/to/bin/run-maintenance.php      # Daily maintenance
```

## 🤝 Contributing

FoxDesk is open to contributions! Check [Issues](https://github.com/lukashanes/foxdesk/issues), open a [Discussion](https://github.com/lukashanes/foxdesk/discussions), or submit a PR.

## 📜 License

[AGPL-3.0](LICENSE.md) — free as in freedom.

## 🔗 Links

- 🌐 [foxdesk.org](https://foxdesk.org) — Website & docs
- 📦 [Download](https://foxdesk.org/download/) — Latest release
- 💬 [Discussions](https://github.com/lukashanes/foxdesk/discussions) — Community
- 🐛 [Issues](https://github.com/lukashanes/foxdesk/issues) — Bug reports
- 🐦 [Follow @lukhanes](https://x.com/lukhanes) — Updates & building in public

---

<p align="center">
  <strong>If FoxDesk helps you, give it a ⭐ — it helps others find it too.</strong>
</p>

<p align="center">
  Created by <a href="https://lukashanes.com">Lukas Hanes</a> · Built with ❤️ in Prague
</p>
