# Lite vs. Pro Editions

APO FJ Downloads comes in two editions: **Lite** (free, open source) and **Pro** (paid, full-featured).

Both editions share the same core codebase and are licensed under GPL v2+.

## Feature Comparison

| Feature | Lite (Free) | Pro ($49+/yr) |
|---------|:-----------:|:-------------:|
| **Core** | | |
| Download management (CRUD) | Yes | Yes |
| Category organization | Yes | Yes |
| File upload with MIME validation | Yes | Yes |
| Token-based secure file serving | Yes | Yes |
| Joomla ACL integration | Yes | Yes |
| Rate limiting | Yes | Yes |
| Download quotas | Yes | Yes |
| GDPR-compliant download logging | Yes | Yes |
| SEF URLs | Yes | Yes |
| **Layout Engine** | | |
| Twig template engine | Yes | Yes |
| 6 default templates | Yes | Yes |
| Custom layout editor | Yes | Yes |
| 5-level cascade resolution | Yes | Yes |
| **File Size** | | |
| Maximum file size | **5 MB** | **Unlimited** |
| **Extensions** | | |
| Content plugin (shortcodes) | No | Yes |
| Latest Downloads module | No | Yes |
| Most Popular module | No | Yes |
| Categories module | No | Yes |
| Search module | No | Yes |
| **Planned (v1.1+)** | | |
| REST API | No | Planned |
| Smart Search integration | No | Planned |
| **Support** | | |
| Community support (email) | Yes | Yes |
| Priority ticket support | No | Yes |

## How the Edition System Works

APO FJ Downloads uses a single codebase for both editions. The edition is determined by the presence of a `.edition` file:

- **Pro:** The package includes a `.edition` file with a cryptographic token
- **Lite:** No `.edition` file — the system defaults to Lite mode

This means:
- Lite users get the full admin panel, layout engine, and secure file serving
- The 5MB cap is enforced at three independent layers for reliability
- Pro features (shortcodes, modules) simply aren't included in the Lite package

## Upgrading from Lite to Pro

1. Purchase a Pro license at [apotentia.com/apo-fj-downloads](https://apotentia.com/apo-fj-downloads)
2. Download the Pro package from your [account portal](https://apotentia.com/account/downloads)
3. Install the Pro package over your existing Lite installation
4. All your data (downloads, categories, files, settings) is preserved
5. New sub-extensions (content plugin, modules) are installed automatically

No data migration required. The upgrade is seamless.

## Pricing

| Plan | Price | Domains | Best For |
|------|-------|---------|----------|
| **Lite** | Free | Unlimited | Trying the extension, small sites, personal projects |
| **Single Site** | $49/year | 1 | One production website |
| **Agency** | $99/year | 5 | Web agencies managing multiple client sites |
| **Unlimited** | $199/year | Unlimited | Large organizations, hosting companies |

All paid plans include:
- All Pro features
- Priority support via ticket system
- Automatic updates for the license period
- Expired licenses retain full functionality (only updates + support require renewal)

## FAQ

**Can I use Lite in production?**
Yes. Lite is fully functional for files under 5MB. Many sites only need small file distribution.

**What happens if my Pro license expires?**
Nothing breaks. Your site keeps working with all Pro features. You just won't receive updates or priority support until you renew.

**Is the source code available?**
Yes. Both editions are GPL v2+ licensed. Source code is included in the package.

**Can I modify the code?**
Yes. GPL v2+ allows modification. Modified versions must also be GPL if redistributed.
