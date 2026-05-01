# Getting Started

This guide walks you through creating your first download in under 5 minutes.

## Step 1: Create a Category

Categories organize your downloads. You need at least one.

1. Go to **Components > APO FJ Downloads**
2. Click **Categories** in the sidebar
3. Click **New**
4. Enter a **Title** (e.g., "Software", "Documents", "Templates")
5. Optionally set:
   - **Description** — shown on the frontend category page
   - **Access** — who can see this category (default: Public)
   - **Permissions** — per-group download/upload access
6. Click **Save & Close**

## Step 2: Create a Download

Downloads are the main items your users will access.

1. Click **Downloads** in the sidebar
2. Click **New**
3. Fill in:
   - **Title** — the download name (e.g., "Annual Report 2026")
   - **Category** — select the category you just created
   - **Alias** — auto-generated from the title (used in SEF URLs)
   - **Description** — what this download contains
   - **Access** — who can see it (inherits from category if not set)
4. Click **Save**

## Step 3: Upload a File

After saving the download, add files to it.

1. While editing the download, go to the **Files** tab
2. Click **Upload File**
3. Select a file from your computer
4. The system automatically:
   - Validates the MIME type against the allowlist (110+ supported types)
   - Computes SHA-256 and MD5 hashes for integrity
   - Sanitizes the filename
   - Enforces the 5MB cap (Lite edition only)
5. Click **Save & Close**

## Step 4: View on the Frontend

Create a menu item to display your downloads.

1. Go to **Menus > Main Menu > New**
2. For **Menu Item Type**, select **APO FJ Downloads**
3. Choose a view:
   - **Categories** — shows all categories
   - **Category** — shows downloads in a specific category
   - **Download** — links directly to a single download
4. Click **Save & Close**
5. Visit your site — your downloads are live!

### SEF URL Structure

| URL | Page |
|-----|------|
| `/downloads` | All categories |
| `/downloads/software` | Downloads in "Software" category |
| `/downloads/software/annual-report-2026` | Single download detail |
| `/downloads/search` | Search page |

## Step 5: Embed with Shortcodes (Pro Only)

Use shortcodes to embed downloads anywhere in Joomla articles.

```
{apofjdl id=42}                         — Single download
{apofjdl category=5 layout=grid}        — Category grid
{apofjdl category=5 limit=10}           — Category with limit
{apofjdl search=true}                   — Search form
```

Write these directly in your article content. The content plugin renders them automatically.

## Next Steps

- **Set up quotas** — limit downloads per user group (daily/weekly/monthly)
- **Configure rate limiting** — prevent abuse (default: 10/minute)
- **Create custom layouts** — design Twig templates for your brand
- **Add licenses** — require users to agree to terms before downloading
- **Enable modules** — add Latest, Popular, Categories, or Search widgets to your sidebars

## Lite vs. Pro Quick Reference

| Feature | Lite | Pro |
|---------|------|-----|
| Downloads, categories, files | Yes | Yes |
| MIME validation | Yes | Yes |
| Token-based secure serving | Yes | Yes |
| Access control (ACL) | Yes | Yes |
| Rate limiting & quotas | Yes | Yes |
| Download logging | Yes | Yes |
| Twig layout engine | Yes | Yes |
| **File size limit** | **5MB** | **Unlimited** |
| Shortcodes in articles | No | Yes |
| Modules (Latest, Popular, etc.) | No | Yes |
| Smart Search integration | No | Planned |
| REST API | No | Planned |

## Need Help?

- **Documentation:** You're reading it
- **Pro support:** [apotentia.com/account/support-tickets](https://apotentia.com/account/support-tickets)
- **General questions:** support@apotentia.com
