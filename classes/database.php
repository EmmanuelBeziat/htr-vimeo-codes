<?php
namespace HTRVC;

/**
 * Handles database operations for Vimeo codes.
 * @since 1.0.0
 */
class Database {
	private $wpdb;

	/**
	 * Constructs the Database object
	 * @param object $wpdb A reference to the WordPress database access abstraction object, typically global $wpdb
	 * @since 1.0.0
	 */
	public function __construct ($wpdb) {
		$this->wpdb = $wpdb;
	}

	/**
	 * Generic method to create a table if it doesn't already exist
	 * @param string $tableName The name of the table to create
	 * @param string $schema The SQL schema for the table
	 * @since 1.0.0
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

	/**
	 * Creates the 'vimeo_codes' table in the database
	 * @since 1.0.0
	 */
	public function createTableCodesList () {
		$tableName = 'vimeo_codes';
		$schema = "
			id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
			code varchar(255) NOT NULL,
			movie mediumint(9) UNSIGNED NOT NULL,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE (code)
		";
		$this->createTableIfNotExists($tableName, $schema);
	}

	/**
	 * Begins a database transaction
	 * @since 1.0.0
	 */
	public function startTransaction () {
    $this->wpdb->query('START TRANSACTION');
	}

	/**
	 * Ends a database transaction, committing or rolling back changes
	 * @param bool $success Whether to commit (true) or rollback (false) the transaction
	 * @since 1.0.0
	 */
	public function endTransaction ($success) {
		if ($success) {
			$this->wpdb->query('COMMIT');
		}
		else {
			$this->wpdb->query('ROLLBACK');
		}
	}
}
