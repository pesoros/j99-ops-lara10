CREATE TABLE `ops_expense_recap_approvals` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` CHAR(36) NOT NULL,
    `roadwarrant_uuid` CHAR(36) NOT NULL,
    `stage` VARCHAR(32) NOT NULL,
    `decision` VARCHAR(32) NOT NULL,
    `decided_by` CHAR(36) NULL,
    `role_slug` VARCHAR(64) NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `expense_recap_approval_uuid_unique` (`uuid`),
    KEY `expense_recap_approval_roadwarrant_index` (`roadwarrant_uuid`),
    KEY `expense_recap_approval_history_index` (`roadwarrant_uuid`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
