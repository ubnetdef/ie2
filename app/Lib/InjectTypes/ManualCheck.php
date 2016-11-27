<?php
namespace InjectTypes;

class ManualCheck extends InjectSubmissionBase {
	
	public function getID() {
		return 'manual';
	}

	public function getTemplate() {
		return 'TODO.';
	}

	public function validateSubmission($inject, $submission) {
		return false;
	}

	public function handleSubmission($inject, $submission) {
		throw new BadMethodCallException('Not implemented');
	}
}