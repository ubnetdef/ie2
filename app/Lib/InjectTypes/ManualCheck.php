<?php
namespace InjectTypes;

class ManualCheck extends InjectSubmissionBase {

    public function getID() {
        return 'manual';
    }

    public function getName() {
        return 'Manual Submission (TODO)';
    }

    public function getTemplate() {
        return <<<'TEMPLATE'
<div class="row">
	<div class="col-sm-4 col-sm-offset-4">
		<input type="submit" class="btn btn-success btn-block" value="Request Manual Check" />
	</div>
</div>
TEMPLATE;
    }

    public function getSubmittedTemplate($submissions) {
        $tpl = '<ul class="list-group">';

        foreach ($submissions as $s) {
            $urlDelete = $this->url('/injects/delete/'.$s['Submission']['id']);
            $d = json_decode($s['Submission']['data'], true);

            $tpl .= '<li class="list-group-item">'.
                        '<h4 class="list-group-item-heading">'.
                        'Manual Check Requested on '.$this->date($s['Submission']['created']).
                        '<a href="'.$urlDelete.'" class="btn btn-info pull-right">Delete</a></h4>'.
                    '</li>';
        }

        if (empty($submissions)) {
            $tpl .= '<li class="list-group-item">'.
                        '<h4 class="list-group-item-heading">No manual checks requested.</h4>'.
                    '</li>';
        }

        $tpl .= '</ul>';
        return $tpl;
    }

    public function getGraderTemplate($submissions) {
        return 'TODO';
    }

    public function validateSubmission($inject, $submission) {
        return true;
    }

    public function handleSubmission($inject, $submission) {
        return json_encode([
            'completed' => false,
            'requested' => time(),
        ]);
    }
}
