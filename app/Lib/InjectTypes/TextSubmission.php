<?php
namespace InjectTypes;

class TextSubmission extends InjectSubmissionBase {

	public function getID() {
		return 'text';
	}

	public function getTemplate() {
		return <<<'TEMPLATE'
<textarea class="form-control" rows="10" name="content"></textarea>

<p></p>

<div class="row">
	<div class="col-sm-4 col-sm-offset-4">
		<input type="submit" class="btn btn-success btn-block" value="Submit!" />
	</div>
</div>
TEMPLATE;
	}

	public function getSubmittedTemplate($submissions) {
		$tpl = '<ul class="list-group">';

		foreach ( $submissions AS $s ) {
			$tpl .= '<li class="list-group-item">'.
						'<h4 class="list-group-item-heading">Submission on '.$s['Submission']['created'].'</h4>'.
						'<p class="list-group-item-text">'.nl2br($s['Submission']['data']).'</p>'.
					'</li>';
		}

		if ( empty($submissions) ) {
			$tpl .= '<li class="list-group-item">'.
						'<h4 class="list-group-item-heading">No submissions.</h4>'.
					'</li>';
		}

		$tpl .= '</ul>';
		return $tpl;
	}

	public function validateSubmission($inject, $submission) {
		return (isset($submission['content']) && !empty($submission['content']));
	}

	public function handleSubmission($inject, $submission) {
		$clean_content = htmlspecialchars($submission['content']);

		return $clean_content;
	}
}