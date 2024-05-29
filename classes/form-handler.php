<?php
namespace HTRVC;

class FormHandler {
	public function handleFormSubmission ($handler, $field) {
		$message = [];
		$class = $handler;

		if (isset($_POST['delete_entry'])) {
			$id = intval($_POST['id_entry']);
			$deleteMessage = $class->remove($id);
			$message['success'][] = $deleteMessage;
		}

		if (isset($_POST['add_entry'])) {
			$codesList = sanitize_text_field($_POST[$field]);
			$lines = preg_split('/[\s,;]+/', $codesList);

			foreach ($lines as $line) {
				$line = trim($line);
				if (empty($line)) return;

				$errors = $class->add([$field => $line]);

				if (!empty($errors)) {
					$message['error'] = $errors;
				}
				else {
					$message['success'][] = 'Entry added successfully.';
				}
			}
		}

		return $message;
	}
}
