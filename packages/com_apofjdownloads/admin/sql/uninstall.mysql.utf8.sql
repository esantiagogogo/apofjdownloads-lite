--
-- APO FJ Downloads - Uninstall SQL
-- (C) 2026 Apotentia LLC. All rights reserved.
--
-- Drop all 9 custom tables. Categories are handled by Joomla's com_categories.
--

DROP TABLE IF EXISTS `#__apofjdl_tokens`;
DROP TABLE IF EXISTS `#__apofjdl_ratings`;
DROP TABLE IF EXISTS `#__apofjdl_custom_fields`;
DROP TABLE IF EXISTS `#__apofjdl_user_quotas`;
DROP TABLE IF EXISTS `#__apofjdl_download_logs`;
DROP TABLE IF EXISTS `#__apofjdl_layouts`;
DROP TABLE IF EXISTS `#__apofjdl_licenses`;
DROP TABLE IF EXISTS `#__apofjdl_files`;
DROP TABLE IF EXISTS `#__apofjdl_downloads`;
