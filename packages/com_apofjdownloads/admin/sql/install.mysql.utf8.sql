--
-- APO FJ Downloads - Install SQL
-- (C) 2026 Apotentia LLC. All rights reserved.
--
-- Categories use Joomla's com_categories integration (nested set).
-- No custom categories table needed.
--

-- Downloads
CREATE TABLE IF NOT EXISTS `#__apofjdl_downloads` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `alias` VARCHAR(400) NOT NULL DEFAULT '',
  `description` MEDIUMTEXT NOT NULL,
  `state` TINYINT NOT NULL DEFAULT 0,
  `access` INT UNSIGNED NOT NULL DEFAULT 0,
  `featured` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `language` CHAR(7) NOT NULL DEFAULT '*',
  `version_note` VARCHAR(255) NOT NULL DEFAULT '',
  `changelog` TEXT NOT NULL,
  `license_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `catid` INT UNSIGNED NOT NULL DEFAULT 0,
  `created` DATETIME NOT NULL,
  `created_by` INT UNSIGNED NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL,
  `modified_by` INT UNSIGNED NOT NULL DEFAULT 0,
  `checked_out` INT UNSIGNED,
  `checked_out_time` DATETIME,
  `hits` INT UNSIGNED NOT NULL DEFAULT 0,
  `ordering` INT NOT NULL DEFAULT 0,
  `params` TEXT NOT NULL,
  `metadata` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_access` (`access`),
  KEY `idx_language` (`language`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_featured` (`featured`),
  KEY `idx_alias` (`alias`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Files
CREATE TABLE IF NOT EXISTS `#__apofjdl_files` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `download_id` INT UNSIGNED NOT NULL,
  `filename` VARCHAR(255) NOT NULL DEFAULT '',
  `filepath` VARCHAR(1024) NOT NULL DEFAULT '',
  `storage_adapter` VARCHAR(50) NOT NULL DEFAULT 'local',
  `size` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `mime_type` VARCHAR(255) NOT NULL DEFAULT '',
  `mime_verified` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `hash_sha256` CHAR(64) NOT NULL DEFAULT '',
  `hash_md5` CHAR(32) NOT NULL DEFAULT '',
  `download_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `ordering` INT NOT NULL DEFAULT 0,
  `state` TINYINT NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_download_id` (`download_id`),
  KEY `idx_mime_type` (`mime_type`(100)),
  KEY `idx_hash_sha256` (`hash_sha256`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Licenses
CREATE TABLE IF NOT EXISTS `#__apofjdl_licenses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `body` MEDIUMTEXT NOT NULL,
  `require_agree` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `state` TINYINT NOT NULL DEFAULT 1,
  `ordering` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Layouts
CREATE TABLE IF NOT EXISTS `#__apofjdl_layouts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `alias` VARCHAR(400) NOT NULL DEFAULT '',
  `type` VARCHAR(50) NOT NULL DEFAULT '',
  `scope` VARCHAR(50) NOT NULL DEFAULT 'global',
  `category_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `body_twig` MEDIUMTEXT NOT NULL,
  `css` MEDIUMTEXT NOT NULL,
  `state` TINYINT NOT NULL DEFAULT 1,
  `ordering` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_scope_category` (`scope`, `category_id`),
  KEY `idx_alias` (`alias`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Download Logs
CREATE TABLE IF NOT EXISTS `#__apofjdl_download_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `download_id` INT UNSIGNED NOT NULL,
  `file_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `ip_hash` VARCHAR(128) NOT NULL DEFAULT '',
  `user_agent` VARCHAR(512) NOT NULL DEFAULT '',
  `downloaded_at` DATETIME NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'completed',
  PRIMARY KEY (`id`),
  KEY `idx_download_id` (`download_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_downloaded_at` (`downloaded_at`),
  KEY `idx_ip_hash` (`ip_hash`(64))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- User Quotas
CREATE TABLE IF NOT EXISTS `#__apofjdl_user_quotas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `group_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `period` VARCHAR(20) NOT NULL DEFAULT 'daily',
  `count` INT UNSIGNED NOT NULL DEFAULT 0,
  `limit_value` INT UNSIGNED NOT NULL DEFAULT 0,
  `period_start` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_group_period` (`user_id`, `group_id`, `period`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Custom Fields (extension-specific metadata, Joomla com_fields used where possible)
CREATE TABLE IF NOT EXISTS `#__apofjdl_custom_fields` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `download_id` INT UNSIGNED NOT NULL,
  `field_id` INT UNSIGNED NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_download_field` (`download_id`, `field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Ratings
CREATE TABLE IF NOT EXISTS `#__apofjdl_ratings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `download_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `review` TEXT NOT NULL,
  `state` TINYINT NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_download_user` (`download_id`, `user_id`),
  KEY `idx_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Tokens
CREATE TABLE IF NOT EXISTS `#__apofjdl_tokens` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` CHAR(64) NOT NULL,
  `file_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` DATETIME NOT NULL,
  `used` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `used_at` DATETIME,
  `created` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_token` (`token`),
  KEY `idx_file_id` (`file_id`),
  KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
