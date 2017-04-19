<?php
namespace InjectTypes;

class FileSubmission extends InjectSubmissionBase {
    private $acceptedExtensions = ['pdf', 'doc', 'docx'];

    public function getID() {
        return 'file';
    }

    public function getName() {
        return 'File Submission';
    }

    public function getTemplate() {
        return <<<'TEMPLATE'
<input type="file" name="data[content]" class="form-control">
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

        foreach ($submissions as $s) {
            $urlDelete = $this->_url('/injects/delete/'.$s['Submission']['id']);
            $urlDownload = $this->_url('/injects/submission/'.$s['Submission']['id']);
            $d = json_decode($s['Submission']['data'], true);

            $tpl .= '<li class="list-group-item">'.
                        '<h4 class="list-group-item-heading">'.
                        'Submission on '.$this->_date($s['Submission']['created']).
                        '<a href="'.$urlDelete.'" class="btn btn-info pull-right">Delete</a></h4>'.
                        '<p class="list-group-item-text">File: <a href="'.$urlDownload.'" target="_blank">'.$d['filename'].'</a></p>'.
                    '</li>';
        }

        if (empty($submissions)) {
            $tpl .= '<li class="list-group-item">'.
                        '<h4 class="list-group-item-heading">No submissions.</h4>'.
                    '</li>';
        }

        $tpl .= '</ul>';
        return $tpl;
    }

    public function getGraderTemplate($s) {
        $data = json_decode($s['Submission']['data'], true);
        $url = $this->_url('/staff/submission/'.$s['Submission']['id']);

        $rtn = '<a href="'.$url.'" class="btn btn-block btn-info" target="_blank">Submission Download ('.$data['filename'].')</a>';
        if ($data['extension'] != 'pdf') {
            return $rtn;
        }

        $rtn .= '<div class="embed-responsive embed-responsive-4by3" style="min-height: 750px;">'.
            '<embed src="'.$url.'" type="application/pdf" class="embed-responsive-item">'.
            '</div>';

        return $rtn;
    }

    public function validateSubmission($inject, $submission) {
        return (
            isset($submission['content']) &&
            !empty($submission['content']) &&
            is_uploaded_file($submission['content']['tmp_name']) &&
            in_array(pathinfo($submission['content']['name'], PATHINFO_EXTENSION), $this->acceptedExtensions)
        );
    }

    public function handleSubmission($inject, $submission) {
        $uploadedFile = $submission['content'];
        $contents = file_get_contents($uploadedFile['tmp_name']);

        return json_encode([
            'filename'  => htmlentities($uploadedFile['name']),
            'extension' => pathinfo($uploadedFile['name'], PATHINFO_EXTENSION),
            'hash'      => md5($contents),
            'data'      => base64_encode($contents),
        ]);
    }
}
