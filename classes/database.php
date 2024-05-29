<?php
namespace HTRVC;

class Database {
	private $wpdb;

	public function __construct ($wpdb) {
		$this->wpdb = $wpdb;
	}

	/**
	 * Generic method to create a table if it doesn't exist
	 * @param string $tableName The name of the table to create
	 * @param string $schema The SQL schema for the table
	 */
	private function createTableIfNotExists ($tableName, $schema) {
		$fullTableName = $this->wpdb->prefix . $tableName;
		if ($this->wpdb->get_var("SHOW TABLES LIKE '$fullTableName'") !== $fullTableName) {
			$charset_collate = $this->wpdb->get_charset_collate();
			$sql = "CREATE TABLE $fullTableName ($schema) $charset_collate;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}

	public function createTableCodesList () {
		$tableName = 'vimeo_codes';
		$schema = "
			id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
			code varchar(255) NOT NULL,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE (code)
		";
		$this->createTableIfNotExists($tableName, $schema);
	}
}
