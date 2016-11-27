<?php
namespace InjectTypes;

class TextSubmission extends InjectSubmissionBase {

	public function getID() {
		return 'text';
	}

	public function getTemplate() {
		return 'TODO';
	}

	public function validateSubmission($inject, $submission) {
		return (isset($submission['content']) && !empty($submission['content']));
	}

	public function handleSubmission($inject, $submission) {
		$clean_content = htmlspecialchars($submission['content']);

		return $clean_content;
	}
}