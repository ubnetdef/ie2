<?php
App::uses('AppController', 'Controller');

class AttachmentController extends AppController {

    public $uses = ['Attachment', 'Schedule'];

    /**
     * Dynamic Index Page
     *
     * @url /attachment
     * @url /attachment/index
     */
    public function index() {
        return $this->redirect(['controller' => 'pages', 'action' => 'index']);
    }

    /**
     * Attachment View
     *
     * @url /attachment/view/<schedule_id>/<attachment_id>/<access_key>
     */
    public function view($aid = false, $access_key = false) {
        $attachment = $this->Attachment->findById($aid);
        if (empty($attachment)) {
            throw new NotFoundException('Unknown attachment');
        }

        $data = json_decode($attachment['Attachment']['data'], true);
        $download = (isset($this->params['url']['download']) && $this->params['url']['download'] == true);

        // Verify the "access_key"
        if ($access_key != md5($aid.env('SECURITY_CIPHER_SEED'))) {
            throw new ForbiddenException('Invalid access key');
        }

        // Let's verify our data is correct
        if (md5(base64_decode($data['data'])) !== $data['hash']) {
            throw new InternalErrorException('Data storage failure');
        }

        // Create the new response for the data
        $response = new CakeResponse();
        $response->type($data['extension']);
        $response->body(base64_decode($data['data']));
        $response->disableCache();

        $type = ($download ? 'attachment' : 'inline');
        $filename = $attachment['Attachment']['name'];
        $response->header('Content-Disposition', $type.'; filename="'.$filename.'"');

        return $response;
    }
}
