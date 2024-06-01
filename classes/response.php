<?php
namespace HTRVC;

/**
 * Standard response structure for handling operations across the application
 * @since 1.0.0
 */
class Response {
	/**
	 * Indicates if the operation was successful
	 * @var bool
	 * @since 1.0.0
	 */
	private $success;

	/**
	 * Array of messages related to the operation
	 * @var array
	 * @since 1.0.0
	 */
	private $messages;

	/**
	 * Constructs a new Response object
	 * @param bool $success Indicates the success of the operation
	 * @param array $messages Messages related to the operation's outcome
	 * @since 1.0.0
	 */
	public function __construct ($success = false, $messages = []) {
		$this->success = $success;
		$this->messages = $messages;
	}

	/**
	 * Returns the success status of the response
	 * @since 1.0.0
	 * @return bool
	 */
	public function isSuccess (): bool {
		return $this->success;
	}

	/**
	 * Returns the messages associated with the response
	 * @since 1.0.0
	 * @return array
	 */
	public function getMessages (): array {
		return $this->messages;
	}
}
