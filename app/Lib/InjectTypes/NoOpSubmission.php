<?php
namespace InjectTypes;

class NoOpSubmission extends InjectSubmissionBase {
    const TPL = '<ul class="list-group"><li class="list-group-item">'.
            '<h4 class="list-group-item-heading">%s</h4></li></ul>';

    public function getID() {
        return 'noop';
    }

    public function getName() {
        return 'NoOp Submission';
    }

    public function getTemplate() {
        return sprintf(self::TPL, 'No submission actions available for this inject.');
    }

    public function getSubmittedTemplate($submissions) {
        return sprintf(self::TPL, 'No submissions.');
    }

    public function getGraderTemplate($submissions) {
        return 'This shouldn\'t happen.';
    }

    public function validateSubmission($inject, $submission) {
        return false;
    }

    public function handleSubmission($inject, $submission) {
        throw new BadMethodCallException('No-Op submissions cannot be submitted');
    }
}
