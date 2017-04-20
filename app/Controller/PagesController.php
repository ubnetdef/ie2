<?php
App::uses('AppController', 'Controller');

class PagesController extends AppController {

    public $uses = ['Config', 'BankWeb.Purchase'];

    /**
     * Dynamic Index Page
     *
     * @url /
     * @url /pages/index
     */
    public function index() {
        $this->set('at_home', true);

        $this->set('title', $this->Config->getKey('homepage.title'));
        $this->set('body', $this->Config->getKey('homepage.body'));
    }

    /**
     * Announcement Read Endpoint
     *
     * @url /pages/announcement_read
     */
    public function announcement_read($aid = false) {
        if ($aid == false || !is_numeric($aid)) {
            return $this->ajaxResponse(null);
        }

        $read = $this->Session->consume('read_announcements');
        $read[] = $aid;
        $this->Session->write('read_announcements', $read);

        return $this->ajaxResponse(null);
    }

    public function slack_respond() {
        $payload = json_decode($this->request->data['payload'], true);

        $purchase_id = $payload['callback_id'];
        $user = $payload['user']['name'];
        $mid = $payload['message_ts'];

        $purchase = $this->Purchase->findById($purchase_id);

        $url = Router::url(
            [
                'plugin' => 'BankWeb',
                'controller' => 'bankadmin',
                'action' => 'view',
                $purchase_id,
            ],
            true
        );

        $message = ' - Completed by <@'.$user.'>';

        return $this->ajaxResponse([
            'response_type' => 'in_channel',
            'replace_original' => true,
            'text' => '[COMPLETED] '.$payload['original_message']['text'].$message,
        ]);
    }
}
