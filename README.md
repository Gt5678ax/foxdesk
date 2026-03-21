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
  <a href="https://foxdesk.org"><img src="https://img.shields.io/badge/foxdesk.org-Visit_Website-0969da?style=for-the-badge&logo=firefox-browser&logoColor=white" alt="Visit foxdesk.org" /></a>
</p>

<p align="center">
  <a href="https://github.com/lukashanes/foxdesk/releases/latest"><img src="https://img.shields.io/github/v/release/lukashanes/foxdesk?color=blue&label=latest" alt="Latest Release" /></a>
  <a href="https://github.com/lukashanes/foxdesk/blob/main/LICENSE.md"><img src="https://img.shields.io/badge/license-AGPL--3.0-green" alt="License" /></a>
  <a href="https://github.com/lukashanes/foxdesk/stargazers"><img src="https://img.shields.io/github/stars/lukashanes/foxdesk?style=social" alt="GitHub Stars" /></a>
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.1+" />
  <img src="https://img.shields.io/badge/MySQL-8.0%2B-4479A1?logo=mysql&logoColor=white" alt="MySQL" />
</p>

<p align="center">
  <a href="https://foxdesk.org/features/">Features</a> · <a href="https://foxdesk.org/download/">Download</a> · <a href="https://foxdesk.org/getting-started/introduction/">Docs</a> · <a href="https://github.com/lukashanes/foxdesk/discussions">Community</a>
</p>

---

<p align="center">
  <a href="https://foxdesk.org">
    <img src="https://cdn.foxdesk.org/screens/FoxDesk__mini.gif?v=2" alt="FoxDesk Demo" width="1280" />
  </a>
</p>

---

## Why FoxDesk?

Most helpdesks charge per seat, per month — and none of them let your AI agents clock in.

| | FoxDesk | Zendesk | Freshdesk | osTicket |
|---|:---:|:---:|:---:|:---:|
| **Price** | **Free forever** | From $19/agent/mo | From $15/agent/mo | Free |
| **Time tracking** | ✅ Built-in | Marketplace add-on | Marketplace add-on | ❌ |
| **AI agent API** | ✅ Native | ❌ | ❌ | ❌ |
| **Self-hosted** | ✅ Your server | Cloud only | Cloud only | ✅ Your server |
| **Setup** | Upload + wizard | n/a (SaaS) | n/a (SaaS) | Manual config |

---

## Everything you need. Nothing you don't.

### 🎫 Tickets & Support
| | |
|---|---|
| 📨 **Email-to-Ticket** | Incoming emails become tickets automatically via IMAP — no forwarding rules needed |
| ✏️ **Rich Text Editor** | Quill editor with Markdown support and drag-and-drop file attachments |
| 👥 **Collision Detection** | Real-time alerts when another agent is viewing the same ticket |
| 🔗 **Public Sharing** | Share tickets with clients via secure links with expiration |
| ⚡ **Bulk Actions** | Change status, priority, or assignee on multiple tickets at once |
| 🔍 **Full-Text Search** | Find any ticket instantly across all fields and comments |
| 📌 **Custom Fields** | Statuses, priorities, types, tags, and due dates |
| 📝 **Internal Notes** | Agent-only comments that clients never see |
| 📜 **Edit History** | Full audit trail on every ticket field change |

### ⏱️ Time Tracking & Billing
| | |
|---|---|
| ▶️ **Built-in Timers** | Start, pause, resume — global timer bar persists across all pages |
| 💰 **Billable Hours** | Track billable vs. non-billable time per ticket |
| 📊 **Matrix Rates** | Set cost rates per user and billing rates per organization |
| 📈 **Profitability Reports** | See what you charge vs. what it costs you — per client |
| ⏱️ **Time Rounding** | Configurable rounding: none, 15, 30, or 60 minutes |
| 📄 **PDF & CSV Export** | Export any report for invoicing or accounting |

### 🤖 AI Agent Integration
| | |
|---|---|
| 🔌 **REST API** | Your AI agents create tickets, post comments, and log hours via simple API calls |
| 🧠 **Agent Connect** | Built-in page generates a ready-to-use system prompt for any AI model |
| 🔑 **Bearer Tokens** | Per-agent API tokens with usage tracking |
| 🤝 **Works with anything** | Claude, ChatGPT, Cursor, custom bots — if it can call an API, it works |

```bash
# Your AI agent creates a ticket after finishing a task
curl -X POST https://your-domain.tld/api/agent-create-ticket \
  -H "Authorization: Bearer fd_live_abc123" \
  -H "Content-Type: application/json" \
  -d '{
    "subject": "Nightly backup completed",
    "content": "All 3 databases backed up. Total size: 2.4 GB. No errors.",
    "priority": "low"
  }'
```

### 📊 Reports & Analytics
| | |
|---|---|
| 📋 **Report Builder** | Custom date ranges, filters, and grouping |
| 📉 **KPI Dashboard** | Cards showing ticket volume, response time, and agent performance |
| 💵 **Financial Reports** | Billable hours, costs, and profit margins at a glance |
| 🔗 **Shareable Links** | Send report links to clients — with expiration |

### 🏢 Teams & Organizations
| | |
|---|---|
| 🏗️ **Organizations** | Manage multiple clients with separate billing rates and contacts |
| 🔒 **Role-Based Access** | Admin, Agent, and Client roles with granular permissions |
| 👤 **Client Portal** | Clients submit and track their own tickets — nothing more |
| 🏷️ **Custom Branding** | Your logo, icons, avatars, and email templates |

### ⚙️ Built for Real Life
| | |
|---|---|
| 🌍 **5 Languages** | English, Czech, German, Spanish, Italian — per-user preference |
| 🌙 **Dark Mode** | Adapts to your OS preference automatically |
| 📱 **PWA** | Install as a native app on desktop and mobile from Chrome or Edge |
| 🔄 **Recurring Tasks** | Scheduled ticket creation — daily, weekly, monthly, or custom |
| 🔔 **Smart Notifications** | Per-ticket, per-user controls for email, in-app, and sound |
| 🔄 **One-Click Updates** | Update from admin panel with automatic backup before each upgrade |
| ⏰ **Pseudo-Cron** | Tasks run on page load — no system cron required on shared hosting |
| ⌨️ **Keyboard Shortcuts** | Navigate faster without touching the mouse |

---

## Screenshots

<p align="center">
  <img src="https://cdn.foxdesk.org/screens/ticket-detail-dark.png" alt="Ticket Detail — Dark Mode" width="640" />
  <img src="https://cdn.foxdesk.org/screens/ticket-detail-light.png" alt="Ticket Detail — Light Mode" width="640" />
</p>
<p align="center"><em>Ticket detail with rich text, attachments, and internal notes</em></p>

<p align="center">
  <img src="https://cdn.foxdesk.org/screens/time-report-dark.png" alt="Time Report — Dark Mode" width="640" />
  <img src="https://cdn.foxdesk.org/screens/time-report-light.png" alt="Time Report — Light Mode" width="640" />
</p>
<p align="center"><em>Time tracking with billable hours and profitability</em></p>

---

## ⚡ Get Started in 5 Minutes

No Docker. No Node. No build pipeline. Upload to any PHP host and go.

```bash
# 1. Download
wget https://github.com/lukashanes/foxdesk/releases/latest/download/foxdesk.zip

# 2. Extract and upload to your web server
unzip foxdesk.zip

# 3. Open your-domain.tld/install.php → follow the wizard → done
```

Works on DigitalOcean, Hetzner, AWS, cPanel, DirectAdmin, or any VPS with PHP 8.1+.

📖 [Detailed install guide](https://foxdesk.org/installation/shared-hosting/)

### Requirements

| | Minimum |
|---|---|
| PHP | 8.1+ |
| MySQL | 8.0+ (or MariaDB 10.5+) |
| PHP extensions | pdo_mysql, mbstring, json, openssl |
| Disk space | ~50 MB |

---

<details>
<summary><strong>🏗 Tech Stack & Project Structure</strong></summary>

### Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.1+ (no framework, no Composer) |
| Database | MySQL / MariaDB |
| Frontend | Tailwind CSS + Alpine.js |
| Theming | CSS custom properties with dark mode |

### Project Structure

```
index.php              → Entry point & router
install.php            → Web installer wizard
config.example.php     → Configuration template
includes/              → Core PHP (auth, DB, API, i18n)
includes/api/          → REST API handlers
includes/lang/         → Translation files (en, cs, de, es, it)
pages/                 → Page controllers
pages/admin/           → Admin panel pages
assets/js/             → JavaScript modules
bin/                   → CLI scripts (cron, email ingest, maintenance)
```

### Cron Jobs (optional)

FoxDesk includes pseudo-cron that runs on page load — no system cron required. For precise scheduling:

```cron
*/5 * * * * php /path/to/bin/ingest-emails.php            # Email → ticket
0   * * * * php /path/to/bin/process-recurring-tasks.php   # Recurring tasks
0   3 * * * php /path/to/bin/run-maintenance.php           # Daily cleanup
```

</details>

---

## 🤝 Contributing

Contributions are welcome — bug fixes, translations, or feature ideas.

1. Check [open issues](https://github.com/lukashanes/foxdesk/issues) or create a new one
2. Fork the repo and create your branch (`git checkout -b fix/my-fix`)
3. Submit a pull request

For questions or ideas, start a thread in [Discussions](https://github.com/lukashanes/foxdesk/discussions).

## 📜 License

[GNU Affero General Public License v3.0](LICENSE.md) — use it, modify it, self-host it. If you distribute a modified version, share the source.

## 🔗 Links

🌐 [foxdesk.org](https://foxdesk.org) — Website & docs
&nbsp;&nbsp;·&nbsp;&nbsp;📦 [Download](https://foxdesk.org/download/)
&nbsp;&nbsp;·&nbsp;&nbsp;💬 [Discussions](https://github.com/lukashanes/foxdesk/discussions)
&nbsp;&nbsp;·&nbsp;&nbsp;🐛 [Issues](https://github.com/lukashanes/foxdesk/issues)
&nbsp;&nbsp;·&nbsp;&nbsp;𝕏 [@lukhanes](https://x.com/lukhanes)

---

<p align="center">
  <strong>If FoxDesk saves you time or money, give it a ⭐ — it helps others find it too.</strong>
</p>

<p align="center">
  Built by <a href="https://lukashanes.com">Lukas Hanes</a> in Prague 🇨🇿
</p>
