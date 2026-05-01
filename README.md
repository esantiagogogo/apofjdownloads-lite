# APO FJ Downloads — Lite Edition

**Free Download Manager for Joomla 4 / 5 / 6**

By [Apotentia LLC](https://apotentia.com) | [Product Page](https://apotentia.com/apo-fj-downloads) | [Documentation](https://apotentia.com/apo-fj-downloads)

## Overview

APO FJ Downloads is a secure, full-featured download management extension for Joomla. The Lite edition is free and open source — no email gate, no account required, no strings attached.

## Features

- **Download management** — full admin CRUD for downloads, categories, files, and licenses
- **MIME-type enforcement** — finfo-based detection with 110+ type allowlist
- **Token-based secure file serving** — 64-character hex tokens, single-use, configurable TTL
- **Joomla ACL integration** — per-category and per-download permissions
- **Rate limiting** — per-user/IP sliding window to prevent abuse
- **Download quotas** — daily/weekly/monthly/total limits per user group
- **GDPR-compliant logging** — HMAC-SHA256 IP hashing, status tracking
- **Twig 3.0 layout engine** — 6 default templates, 5-level cascade resolution, custom layout editor
- **SEF URLs** — `/downloads`, `/downloads/{category}`, `/downloads/{category}/{download}`
- **5MB file size cap** — Lite edition limit (removed in Pro)

## Requirements

| Requirement | Minimum |
|------------|---------|
| Joomla | 4.4+ / 5.x / 6.x |
| PHP | 8.1+ |
| Database | MySQL 5.7+ or MariaDB 10.3+ |

## Installation

1. Download the latest release ZIP from the [Releases](https://github.com/esantiagogogo/apofjdownloads-lite/releases) page
2. In Joomla admin, go to **System > Install > Extensions**
3. Upload the ZIP file
4. Done — all sub-extensions install automatically

## Package Contents

| Sub-extension | Description |
|--------------|-------------|
| `com_apofjdownloads` | Core component — admin panel, models, views, database |
| `lib_apofjdownloads` | Shared library — MIME, Storage, Token, ACL, Layout, Upload |
| `plg_system_apofjdownloads` | System plugin — ACL registration, MIME events |
| `plg_actionlog_apofjdownloads` | Action log — admin activity tracking |

## Lite vs. Pro

| Feature | Lite (Free) | Pro ($49+/yr) |
|---------|:-----------:|:-------------:|
| Download management | Yes | Yes |
| MIME validation | Yes | Yes |
| Secure token serving | Yes | Yes |
| ACL, quotas, rate limiting | Yes | Yes |
| Download logging | Yes | Yes |
| Twig layout engine | Yes | Yes |
| SEF URLs | Yes | Yes |
| **File size limit** | **5MB** | **Unlimited** |
| Content shortcodes | No | Yes |
| 4 sidebar modules | No | Yes |
| Priority support | No | Yes |

### Pro Pricing

| Plan | Price | Domains |
|------|-------|---------|
| **Lite** | Free | Unlimited |
| **Single Site** | $49/year | 1 |
| **Agency** | $99/year | 5 |
| **Unlimited** | $199/year | Unlimited |

[Upgrade to Pro](https://apotentia.com/apo-fj-downloads)

## Documentation

- [Installation Guide](docs/user-manual/installation.md)
- [Getting Started](docs/user-manual/getting-started.md)
- [Lite vs. Pro](docs/user-manual/lite-vs-pro.md)

## License

GNU General Public License version 2 or later (GPL v2+). See [LICENSE.txt](LICENSE.txt).

Copyright (C) 2026 Apotentia LLC.

## Support

- **Lite users:** support@apotentia.com
- **Pro users:** [Priority ticket support](https://apotentia.com/account/support-tickets)
- **Issues:** [GitHub Issues](https://github.com/esantiagogogo/apofjdownloads-lite/issues)

## Contributing

Bug reports and feature requests are welcome via [GitHub Issues](https://github.com/esantiagogogo/apofjdownloads-lite/issues). Pull requests are considered on a case-by-case basis.
