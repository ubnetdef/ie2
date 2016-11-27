<?php
namespace InjectTypes;

abstract class InjectSubmissionBase {
	
	/**
	 * Get ID
	 *
	 * Returns a unique identifier for this
	 * inject submission type.  Usually the filename
	 * is good enough
	 *
	 * @return string The ID
	 */
	abstract public function getID();
	
	/**
	 * Get Template
	 *
	 * Returns the template for the
	 * inject submission type that will
	 * be injected into the page. 
	 *
	 * @return string The template
	 */
	abstract public function getTemplate();

	/**
	 * Validate Submission
	 *
	 * Validates a submission if
	 * it is good to proceed.
	 *
	 * @param $inject The inject
	 * @param $submission The attempted submission
	 * @return boolean If the submission is valid
	 */
	abstract public function validateSubmission($inject, $submission);

	/**
	 * Handle Submission
	 *
	 * Gets called when a VALID submission
	 * is being saved. Note, valid means
	 * that the max submissions has not
	 * been reached. It does NOT mean that
	 * this submission is correct
	 *
	 * @param $inject The inject
	 * @param $submission The submission data
	 * @return string Data to save for this
	 * specific submission
	 */
	abstract public function handleSubmission($inject, $submission);
}