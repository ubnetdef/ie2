<?php
namespace InjectTypes;

class ManualCheck extends InjectSubmissionBase {

    public function getID() {
        return 'manual';
    }

    public function getName() {
        return 'Manual Check';
    }

    public function getTemplate() {
        return <<<'TEMPLATE'
<p></p>

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
        return 'N/A';
    }

    public function getGraderForm($submission) {
        $submitted = !empty($submission['Grade']['comments']);
        $approved = $submission['Grade']['grade'] == $submission['Inject']['max_points'];

        $btn_deny         = $submitted && !$approved ? ' active' : '';
        $btn_deny_text    = $submitted && !$approved ? ' (Selected)' : '';
        $btn_approve      = $submitted && $approved ? ' active' : '';
        $btn_approve_text = $submitted && $approved ? ' (Selected)' : '';

        return <<<TEMPLATE
<script>
$(document).ready(function() {
    $('#btn_approve').click(function() {
        $('input[name="grade"]').val({$submission['Inject']['max_points']});
    });
});
</script>

<form method="post">
    <input type="hidden" name="comments" value="N/A" />
    <input type="hidden" name="grade" value="0" />

    <div class="row">
        <div class="col-sm-6">
            <input id="btn_deny" type="submit" class="btn btn-danger btn-block{$btn_deny}" value="Deny!{$btn_deny_text}" />
        </div>
        <div class="col-sm-6">
            <input id="btn_approve" type="submit" class="btn btn-success btn-block{$btn_approve}" value="Approve!{$btn_approve_text}" />
        </div>
    </div>
</form>
TEMPLATE;
    }

    public function validateSubmission($inject, $submission) {
        return true;
    }

    public function handleSubmission($inject, $submission) {
        return json_encode([
            'completed'    => false,
            'completed_by' => 'N/A',
            'accepted'     => false,
            'requested'    => time(),
        ]);
    }
}
