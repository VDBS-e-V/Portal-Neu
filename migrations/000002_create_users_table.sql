CREATE TABLE IF NOT EXISTS ids_users (
	-- Primärschlüssel
	id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
		COMMENT 'Beschreibung: Interner Primärschlüssel (laufende ID) des Benutzers; Mögliche Werte: BIGINT UNSIGNED, AUTO_INCREMENT',

	-- Öffentliche/technische IDs (für APIs, URLs, Tokens)
	uuid CHAR(36) NOT NULL
		COMMENT 'Beschreibung: Öffentliche, nicht-ratbare ID (z.B. für URLs/APIs), unabhängig von id; Mögliche Werte: CHAR(36) (UUID v4 als String)',

	-- Login / Identität
	username VARCHAR(50) NULL
		COMMENT 'Beschreibung: Benutzername des Benutzers (optional, falls Login/Anzeige über Username gewünscht); Mögliche Werte: VARCHAR(50) oder NULL',

	email VARCHAR(255) NOT NULL
		COMMENT 'Beschreibung: E-Mail-Adresse des Benutzers im Originalformat (Anzeige/Kommunikation); Mögliche Werte: VARCHAR(255)',

	email_normalized VARCHAR(255) NOT NULL
		COMMENT 'Beschreibung: Normalisierte E-Mail (z.B. trim + lowercase) zur eindeutigen Identifikation/Abgleich; Mögliche Werte: VARCHAR(255)',

	-- Passwort (für lokale Auth)
	password_hash VARCHAR(255) NULL
		COMMENT 'Beschreibung: Passwort-Hash für lokale Anmeldung (kein Klartext), optional bei externem Login; Mögliche Werte: VARCHAR(255) oder NULL',

	-- Status/Flags
	is_active TINYINT(1) NOT NULL DEFAULT 1
		COMMENT 'Beschreibung: Aktiv-Flag (deaktivierte Benutzer dürfen sich nicht anmelden); Mögliche Werte: TINYINT(1) (0/1), Default 1',

	is_email_verified TINYINT(1) NOT NULL DEFAULT 0
		COMMENT 'Beschreibung: Flag, ob die E-Mail-Adresse verifiziert wurde; Mögliche Werte: TINYINT(1) (0/1), Default 0',

	-- Rollen/Permissions (minimal ohne extra Tabellen)
	permissions JSON NOT NULL DEFAULT '{"portal":["user"],"bibliocollect":["user"]}'
		COMMENT 'Beschreibung: Objekt mit Projektnamen als Keys und Arrays von Rollen/Permissions als Values (z.B. {"portal":["admin","developer","mitglied","user"]}).',

	-- Profil
	display_name VARCHAR(120) NULL
		COMMENT 'Beschreibung: Anzeigename (UI-Name), unabhängig von Vor-/Nachname; Mögliche Werte: VARCHAR(120) oder NULL',

	first_name VARCHAR(120) NULL
		COMMENT 'Beschreibung: Vorname des Benutzers (Profilfeld); Mögliche Werte: VARCHAR(120) oder NULL',

	last_name VARCHAR(120) NULL
		COMMENT 'Beschreibung: Nachname des Benutzers (Profilfeld); Mögliche Werte: VARCHAR(120) oder NULL',

	phone VARCHAR(50) NULL
		COMMENT 'Beschreibung: Telefonnummer des Benutzers (Profilfeld); Mögliche Werte: VARCHAR(50) oder NULL',

	locale VARCHAR(10) NOT NULL DEFAULT 'de-DE'
		COMMENT 'Beschreibung: Locale/Sprachregion für UI/Formatierung; Mögliche Werte: VARCHAR(10), z.B. de-DE, en-US (Default de-DE)',

	timezone VARCHAR(64) NOT NULL DEFAULT 'Europe/Berlin'
		COMMENT 'Beschreibung: Zeitzone des Benutzers für Datums-/Zeitdarstellung; Mögliche Werte: VARCHAR(64), z.B. Europe/Berlin (Default Europe/Berlin)',

	-- Security / Abuse Prevention
	failed_login_count INT UNSIGNED NOT NULL DEFAULT 0
		COMMENT 'Beschreibung: Anzahl fehlgeschlagener Loginversuche seit letztem Reset/Lock-Event; Mögliche Werte: INT UNSIGNED (>=0), Default 0',

	locked_until DATETIME NULL
		COMMENT 'Beschreibung: Zeitpunkt, bis zu dem der Account gesperrt ist (Brute-Force-Schutz); Mögliche Werte: DATETIME oder NULL',

	last_login_at DATETIME NULL
		COMMENT 'Beschreibung: Zeitpunkt des letzten erfolgreichen Logins; Mögliche Werte: DATETIME oder NULL',

	last_login_ip VARCHAR(45) NULL
		COMMENT 'Beschreibung: IP-Adresse des letzten erfolgreichen Logins (IPv4/IPv6); Mögliche Werte: VARCHAR(45) oder NULL',

	-- Email Verification Token
	email_verify_token_hash CHAR(64) NULL
		COMMENT 'Beschreibung: Hash des E-Mail-Verifizierungs-Tokens (Token wird niemals im Klartext gespeichert); Mögliche Werte: CHAR(64) (SHA-256 hex) oder NULL',

	email_verify_token_expires_at DATETIME NULL
		COMMENT 'Beschreibung: Ablaufzeitpunkt des E-Mail-Verifizierungs-Tokens; Mögliche Werte: DATETIME oder NULL',

	-- Password Reset Token
	password_reset_token_hash CHAR(64) NULL
		COMMENT 'Beschreibung: Hash des Passwort-Reset-Tokens (Token wird niemals im Klartext gespeichert); Mögliche Werte: CHAR(64) (SHA-256 hex) oder NULL',

	password_reset_token_expires_at DATETIME NULL
		COMMENT 'Beschreibung: Ablaufzeitpunkt des Passwort-Reset-Tokens; Mögliche Werte: DATETIME oder NULL',

	-- Audit
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
		COMMENT 'Beschreibung: Zeitpunkt der Erstellung des Benutzer-Datensatzes; Mögliche Werte: DATETIME, Default CURRENT_TIMESTAMP',

	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		COMMENT 'Beschreibung: Zeitpunkt der letzten Aktualisierung des Benutzer-Datensatzes; Mögliche Werte: DATETIME, Default CURRENT_TIMESTAMP, ON UPDATE CURRENT_TIMESTAMP',

	deleted_at DATETIME NULL
		COMMENT 'Beschreibung: Soft-Delete Zeitpunkt (wenn gesetzt gilt der Benutzer als gelöscht/deaktiviert); Mögliche Werte: DATETIME oder NULL',

	PRIMARY KEY (id),

	UNIQUE KEY uniq_ids_users_uuid (uuid),
	UNIQUE KEY uniq_ids_users_email_norm (email_normalized),
	UNIQUE KEY uniq_ids_users_username (username),

	KEY idx_ids_users_active (is_active),
	KEY idx_ids_users_deleted_at (deleted_at),
	KEY idx_ids_users_last_login_at (last_login_at)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Beschreibung: Benutzer-Tabelle für Auth/Profil/Security im Portal; Mögliche Werte: InnoDB, utf8mb4';