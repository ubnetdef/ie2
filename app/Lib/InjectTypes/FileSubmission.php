<?php
namespace InjectTypes;

class FileSubmission extends InjectSubmissionBase {
	
	public function getID() {
		return 'file';
	}

	public function getTemplate() {
		return <<<'TEMPLATE'
<input type="file" id="exampleInputFile">
<p></p>
<div class="row">
	<div class="col-sm-4 col-sm-offset-4">
		<span class="btn btn-success btn-block">Submit!</span>
	</div>
</div>
TEMPLATE;
	}

	public function getSubmittedTemplate($submissions) {
		return 'TODO';
	}

	public function validateSubmission($inject, $submission) {
		return false;
	}

	public function handleSubmission($inject, $submission) {
		throw new BadMethodCallException('Not implemented');
	}
}