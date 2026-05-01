# Changelog

All notable changes to APO FJ Downloads will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-05-01

Initial stable release of APO FJ Downloads — Premium Download Manager for Joomla 4/5/6.

### Architecture
- Monorepo package with 11 sub-extensions (component, library, 3 plugins, 4 modules)
- PHP 8.1+ with strict typing throughout
- Interface-based dependency isolation for testability
- 248 automated tests (184 passing, 64 skip-if-no-Joomla)
- CI/CD pipeline (GitHub Actions) with lint + test matrix (PHP 8.1–8.4)
- GPL v2+ licensed

### Admin Panel
- **Downloads CRUD** — create, edit, publish/unpublish, feature/unfeature, delete
- **Files management** — upload with MIME validation, per-download file list, delete with cleanup
- **Categories** — Joomla com_categories integration (nested set model)
- **Licenses** — full CRUD for terms of service documents
- **Download logs** — viewer with filters (download, user, status), sortable, GDPR-compliant
- **Layouts** — admin editor for custom Twig templates, per-type and per-category

### Core Library
- **MIME engine** — finfo-based detection, allowlist (110+ MIME types), per-category overrides
- **File upload handler** — sanitized filenames, SHA-256/MD5 hash computation, edition-aware size cap
- **Local storage adapter** — filesystem storage with StorageAdapterInterface for future extensions
- **Token-based secure file serving** — 64-character hex tokens, single-use, configurable TTL
- **FileSender** — PHP/X-Sendfile/X-Accel-Redirect modes with proper headers
- **Permission checker** — Joomla ACL integration with Closure-based lookup for testability
- **Quota manager** — daily/weekly/monthly/total limits per user group
- **Rate limiter** — 60-second sliding window, per-user/IP
- **Download logger** — GDPR-compliant IP hashing (HMAC-SHA256), status tracking
- **Edition checker** — file-based Lite/Pro detection, fail-closed (defaults to Lite)

### Frontend
- **Categories list view** — browse all download categories
- **Category view** — downloads within a category with metadata
- **Download detail view** — full download info, file list, ratings
- **Search results view** — keyword-based search across downloads
- **SEF router** — clean URLs: `/downloads`, `/downloads/{cat}`, `/downloads/{cat}/{dl}`, `/downloads/search`

### Layout Engine
- **Twig 3.0 integration** — auto-escaping, custom template functions
- **6 default templates** — categories_list, category_view, download_list, download_detail, download_summary, search_results
- **5-level cascade resolution** — alias → download-level → category-level → global → template override → system fallback
- **DatabaseLayoutResolver** — load layouts from database or filesystem
- **Admin layout editor** — create/edit custom Twig templates with CSS

### Content Plugin
- **ShortcodeParser** — pure PHP regex: `{apofjdl id=42}`, `{apofjdl category=5 layout=grid limit=10}`, `{apofjdl search=true}`
- **ShortcodeMatch value objects** — structured parameter extraction
- Integrated into Joomla's onContentPrepare event

### Modules
- **mod_apofjdownloads_latest** — most recent downloads widget
- **mod_apofjdownloads_popular** — most downloaded widget
- **mod_apofjdownloads_categories** — category tree widget
- **mod_apofjdownloads_search** — search form widget

### Lite/Pro Editions
- **EditionChecker** — `.edition` file with SHA-256 token for Pro detection
- **Triple-redundant 5MB cap** (Lite only):
  - Layer 1: FileUploadHandler pre-move size check
  - Layer 2: FileModel post-move filesize() verification
  - Layer 3: FileTable database-level check() constraint
- **Build scripts** — `build-lite.sh` and `build-pro.sh` produce installable ZIP packages
- **Feature gating** — Lite excludes content plugin, webservices plugin, finder plugin, all 4 modules

### Plugins
- **plg_content_apofjdownloads** — shortcode rendering in Joomla articles
- **plg_system_apofjdownloads** — system events and category ACL registration
- **plg_actionlog_apofjdownloads** — admin action logging integration

### Database
- 9 tables: downloads, files, licenses, layouts, download_logs, user_quotas, custom_fields, ratings, tokens

## [Unreleased]

### Planned (Phase 4+)
- REST API (plg_webservices_apofjdownloads)
- Smart Search integration (plg_finder_apofjdownloads)
- Media preview (HTML5 audio/video players)
- Mass downloads (ZIP streaming)
- Remote storage adapters (S3, etc.)
- Ratings and reviews system
- Custom fields integration
- License key validation system
- WCAG accessibility audit
