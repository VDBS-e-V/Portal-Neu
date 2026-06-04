CREATE TABLE IF NOT EXISTS `pt_ticket_comments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` BIGINT UNSIGNED NOT NULL,
  `author_user_id` BIGINT UNSIGNED NOT NULL,
  `comment_body` MEDIUMTEXT NOT NULL,
  `is_internal` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pt_ticket_comments_ticket` (`ticket_id`),
  KEY `idx_pt_ticket_comments_author` (`author_user_id`),
  CONSTRAINT `fk_pt_ticket_comments_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `pt_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pt_ticket_comments_author` FOREIGN KEY (`author_user_id`) REFERENCES `ids_users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
