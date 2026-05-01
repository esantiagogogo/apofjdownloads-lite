# Installation Guide

## System Requirements

| Requirement | Minimum |
|------------|---------|
| **Joomla** | 4.4, 5.x, or 6.x |
| **PHP** | 8.1+ (8.2, 8.3, 8.4 supported) |
| **Database** | MySQL 5.7+ or MariaDB 10.3+ |
| **Web Server** | Apache 2.4+ or Nginx |

## Download

- **Lite (Free):** Download from [apotentia.com/apo-fj-downloads](https://apotentia.com/apo-fj-downloads)
- **Pro:** Purchase from [apotentia.com/apo-fj-downloads](https://apotentia.com/apo-fj-downloads), then download from your [account portal](https://apotentia.com/account/downloads)

## Installation Steps

1. Log into your Joomla administrator panel
2. Navigate to **System > Install > Extensions**
3. Upload the package ZIP file (`pkg_apofjdownloads-1.0.0.zip` or `pkg_apofjdownloads_lite-1.0.0.zip`)
4. Click **Upload & Install**
5. Wait for the installation to complete — all 11 sub-extensions install automatically

You should see confirmation messages for each installed sub-extension:
- APO FJ Downloads Component
- APO FJ Downloads Library
- Content Plugin (Pro only)
- System Plugin
- Action Log Plugin
- 4 Modules (Pro only)

## Post-Install Configuration

After installation, navigate to **Components > APO FJ Downloads > Options** to configure:

### File Storage Path

The `file_storage_path` setting controls where uploaded files are stored.

- **Default:** `media/com_apofjdownloads/files` (relative to Joomla root)
- **Recommended:** Set an absolute path outside the web root for maximum security, e.g. `/home/yoursite/apofjdl-files/`
- The directory must be writable by the web server

### Security Settings

| Setting | Default | Range | Description |
|---------|---------|-------|-------------|
| Token TTL | 3600 | 60–86400 | How long download links stay valid (seconds) |
| Rate Limit | 10/min | 1–1000 | Max downloads per user per minute |
| X-Sendfile | Disabled | — | Enable Apache X-Sendfile or Nginx X-Accel-Redirect for faster file serving |

### Access Control

- **Default Access Level:** Controls who can see new downloads (default: Public)
- Per-category and per-download ACL is available through Joomla's standard permissions system
- Custom actions: `apofjdl.download`, `apofjdl.upload`, `apofjdl.upload.frontend`, `apofjdl.view.logs`, `apofjdl.manage.layouts`

## Verifying Installation

1. Go to **Components > APO FJ Downloads** — you should see the admin dashboard
2. Check **Extensions > Plugins** — verify the system and content plugins are enabled
3. Go to **Extensions > Modules** — verify the 4 modules appear (Pro only)

## File Permissions

Ensure these directories are writable by the web server (typically `www-data` or `apache`):

```
media/com_apofjdownloads/files/    (or your custom storage path)
```

## Uninstallation

1. Go to **System > Manage > Extensions**
2. Find "APO FJ Downloads" (package type)
3. Select and click **Uninstall**

This removes all sub-extensions and drops all database tables. **Back up your files and database first.**

## Troubleshooting

### "Extension package not found" error
- Ensure the ZIP file is not corrupted (re-download if needed)
- Check PHP upload limits: `upload_max_filesize` and `post_max_size` must be larger than the ZIP

### Tables not created
- Check MySQL user has CREATE TABLE permissions
- Check Joomla's error log at `administrator/logs/`

### Files not uploading
- Verify the storage directory exists and is writable
- Check PHP's `upload_max_filesize` setting (must be larger than your files)
- For Lite edition: files are capped at 5MB

### Need help?
- **Pro users:** Submit a ticket at [apotentia.com/account/support-tickets](https://apotentia.com/account/support-tickets)
- **General questions:** support@apotentia.com
