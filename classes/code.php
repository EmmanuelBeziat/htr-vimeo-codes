<?php
namespace HTRVC;

class Code {
	private $wpdb;
	private $tableName;

	public function __construct($wpdb) {
		$this->wpdb = $wpdb;
		$this->tableName = $wpdb->prefix . 'vimeo_codes';
	}

	/**
	 * Add a new entry to the database
	 * @param array $data Associative array of column => value pairs
	 * @param array $uniqueColumns Columns to check for uniqueness (optional)
	 * @return array Any entry errors
	 */
	public function add (array $data, array $uniqueColumns = []): array {
		$errors = $this->checkForExistingEntry($data, $uniqueColumns);
		if (!empty($errors)) return $errors;

		$data['created_at'] = current_time('mysql', 1);
		if (!$this->wpdb->insert($this->tableName, $data)) {
			$errors[] = 'Failed to insert data.';
		}
		return $errors;
	}

	/**
	 * Delete an entry from the database
	 * @param int $id The ID of the entry to delete
	 * @return string Deletion status message
	 */
	public function remove (int $id): string {
		$this->wpdb->delete($this->tableName, ['id' => $id]);
		return $this->wpdb->rows_affected ? 'Entry deleted.' : 'An error occurred, the entry may not have been deleted.';
	}

	/**
	 * Get listings from the database based on a specific column and value
	 * @param string $column The column to filter by
	 * @param mixed $value The value to match in the specified column
	 * @return array|object|null The listings or null on failure
	 */
	public function list (): ?array {
		// Prepare the SQL statement to prevent SQL injection
		return $this->wpdb->get_results("SELECT * FROM {$this->tableName}", OBJECT);
	}

	/**
	 * Get a single entry from the database by ID
	 * @param int $id The ID of the entry to retrieve
	 * @return object|null The entry object or null if not found
	 */
	public function get (int $id): ?object {
		return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->tableName} WHERE id = %d", $id), OBJECT);
	}

	 /**
	 * Checks for existing entries based on unique columns.
	 */
	private function checkForExistingEntry($data, $uniqueColumns): array {
		if (empty($uniqueColumns)) return [];

		$conditions = array_map(function($col) use ($data) {
			return $this->wpdb->prepare("$col = %s", $data[$col]);
		}, array_keys($uniqueColumns));

		$conditionString = implode(' AND ', $conditions);

		if ($this->wpdb->get_row("SELECT * FROM {$this->tableName} WHERE $conditionString")) {
			return ['This entry already exists'];
		}

		return [];
	}
}
