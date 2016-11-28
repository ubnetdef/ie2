<?php
namespace InjectTypes;

class NoOpSubmission extends InjectSubmissionBase {
	
	public function getID() {
		return 'noop';
	}

	public function getTemplate() {
		return 'No submission actions available for this inject.';
	}

	public function getSubmittedTemplate($submissions) {
		return 'TODO';
	}

	public function validateSubmission($inject, $submission) {
		return false;
	}

	public function handleSubmission($inject, $submission) {
		throw new BadMethodCallException('No-Op submissions cannot be submitted');
	}
}