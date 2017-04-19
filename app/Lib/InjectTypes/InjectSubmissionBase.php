<?php
namespace InjectTypes;

\App::uses('Router', 'Routing');

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
     * Get Name
     *
     *
     * Returns the unique name for this
     * inject submission type
     *
     * @return string The name
     */
    abstract public function getName();

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
     * Get Submitted Template
     *
     * Returns the template for the
     * inject submission type that blue
     * teams will see when viewing their
     * submitted injects
     *
     * @param $submission The submissions
     * @return string The template
     */
    abstract public function getSubmittedTemplate($submission);

    /**
     * Get Grader Template
     *
     * Returns the template for the
     * inject submission type that the
     * graders will see when viewing a
     * submitted injects
     *
     * @param $submission The submissions
     * @return string The template
     */
    abstract public function getGraderTemplate($submission);

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

    /**
     * Generate URL
     *
     * @param $url The url
     * @return string The full url
     */
    protected function url($url) {
        return \Router::url($url);
    }

    /**
     * Generate Date
     *
     * @param $timestamp The timestamp
     * @return string The date
     */
    protected function date($timestamp) {
        return tz_date('F j, Y \a\t g:iA', $timestamp);
    }
}
