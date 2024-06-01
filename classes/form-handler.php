<?php
namespace HTRVC;

/**
 * Handles form submissions for Vimeo code management
 * @since 1.0.0
 */
class FormHandler {
	/**
	 * Processes form submissions for adding or deleting Vimeo codes
	 * @param object $handler Instance of handler class for database operations
	 * @param string $field The form field name to process
	 * @since 1.0.0
	 * @return Response Associative array containing success or error messages
	 */
	public function handleFormSubmission ($handler, $field): Response {
		$message = new Response();

		if (isset($_POST['delete_entry'])) {
			$message = $this->handleDelete($handler);
		}

		if (isset($_POST['add_entry'])) {
			$message = $this->handleAdd($handler, $field);
		}

		return $message;
	}

	/**
	 * Handle deletion of a Vimeo code
	 * @param object $handler Database handler
	 * @since 1.0.0
	 * @return Response Response object containing success or error messages
	 */
	private function handleDelete ($handler) {
		$id = intval($_POST['id_entry']);
		return $handler->remove($id);
	}

	/**
	 * Handle addition of new Vimeo codes
	 * @param object $handler Database handler
	 * @param string $field Form field name
	 * @since 1.0.0
	 * @return Response Response object containing success or error messages
	 */
	private function handleAdd ($handler, $field) {
		$message = NULL;
		$codesList = sanitize_text_field($_POST[$field]);
		$lines = preg_split('/[\s,;]+/', $codesList);

		foreach ($lines as $line) {
			$line = trim($line);
			if (empty($line)) continue;

			$message = $handler->add([$field => $line]);
		}

		return $message;
	}
}
