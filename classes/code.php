<?php
namespace HTRVC;

/**
 * Manages Vimeo code entries in the database
 * @since 1.0.0
 */
class Code {
	private $wpdb;
	private $tableName;
	private $database;

	/**
	 * Constructor for the Code class. Initializes the database connection and sets the table name used for operations
	 * @param object $wpdb A reference to the WordPress database access abstraction object, typically global $wpdb
	 * @since 1.0.0
	 */
	public function __construct ($wpdb) {
		$this->wpdb = $wpdb;
		$this->tableName = $wpdb->prefix . 'vimeo_codes';
		$this->database = new Database($wpdb);
	}

	/**
	 * Add a new entry to the database
	 * @param array $data Associative array of column => value pairs
	 * @param array $uniqueColumns Columns to check for uniqueness (optional)
	 * @since 1.0.0
	 * @return Response Response object containing success or error messages
	 */
	public function add (array $data, array $uniqueColumns = []): Response {
		$response = $this->checkForExistingEntry($data, $uniqueColumns);
		if (!$response->isSuccess()) return $response;

		$data['created_at'] = current_time('mysql', 1);
		if (!$this->wpdb->insert($this->tableName, $data)) {
			return new Response(false, ['Failed to insert data.']);
		}

		return new Response(true, ['Entry added successfully.']);
	}

	/**
	 * Delete an entry from the database
	 * @param int $id The ID of the entry to delete
	 * @since 1.0.0
	 * @return Response Response object containing success or error messages
	 */
	public function remove (int $id): Response {
		$this->wpdb->delete($this->tableName, ['id' => $id]);

		if ($this->wpdb->rows_affected) {
			return new Response(true, ['Entry deleted.']);
		}
		else {
			return new Response(false, ['An error occurred, the entry may not have been deleted.']);
		}
	}

	/**
	 * Get listings from the database based on a specific column and value
	 * @param string $column The column to filter by
	 * @param mixed $value The value to match in the specified column
	 * @since 1.0.0
	 * @return array|object|null The listings or null on failure
	 */
	public function list (): ?array {
		// Prepare the SQL statement to prevent SQL injection
		return $this->wpdb->get_results("SELECT * FROM {$this->tableName}", OBJECT);
	}

	/**
	 * Get a single entry from the database by ID
	 * @param int $id The ID of the entry to retrieve
	 * @since 1.0.0
	 * @return object|null The entry object or null if not found
	 */
	public function get (int $id): ?object {
		return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->tableName} WHERE id = %d", $id), OBJECT);
	}

	/**
	 * Checks for existing entries in the database based on provided unique columns
	 * This method is used to ensure that no duplicate entries are created based on the specified unique columns
	 * @param array $data Associative array containing the data to check. Keys should correspond to database columns
	 * @param array $uniqueColumns Array of column names that should be checked for uniqueness
	 * @since 1.0.0
	 * @return Response Returns a Response object containing error messages if duplicate entries are found, or a success message if no duplicates are found
	 */
	private function checkForExistingEntry ($data, $uniqueColumns): Response {
		if (empty($uniqueColumns)) return new Response(true, []);

		$conditions = array_map(function($col) use ($data) {
			return $this->wpdb->prepare("$col = %s", $data[$col]);
		}, array_keys($uniqueColumns));

		$conditionString = implode(' AND ', $conditions);

		if ($this->wpdb->get_row("SELECT * FROM {$this->tableName} WHERE $conditionString")) {
			return new Response(false, ['This entry already exists']);
		}

		return new Response(true, []);
	}

	/**
	 * Retrieves and deletes the first item in the database table, ensuring the operation is atomic
	 * @since 1.0.0
	 * @return object|null Returns the deleted item object if successful, or null if the operation fails
	 */
	public function applyCodeAtSale (): ?object {
		$this->database->startTransaction();

		$item = $this->wpdb->get_row("SELECT * FROM {$this->tableName} ORDER BY id ASC LIMIT 1 FOR UPDATE", OBJECT);
		if (!$item) {
			$this->endTransaction(false);
			return null;
		}

		if (!$this->wpdb->rows_affected || !$this->remove($item->id)) {
			$this->endTransaction(false);
			return null;
		}

		$this->database->endTransaction(true);
		return $item;
	}
}
