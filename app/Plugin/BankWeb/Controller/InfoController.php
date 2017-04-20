<?php
App::uses('BankWebAppController', 'BankWeb.Controller');

class InfoController extends BankWebAppController {

    public function beforeFilter() {
        parent::beforeFilter();

        // Set the active menu item
        $this->set('at_team', true);
    }

    /**
     * Account Information Page
     *
     * @url /bank/info
     * @url /bank/info/index
     */
    public function index() {
        if ((bool)env('BANKWEB_PUBLIC_APIINFO') == false && !$this->Auth->isAdmin()) {
            throw new ForbiddenException('This feature is disabled');
        }

        $account = $this->AccountMapping->getAccount($this->Auth->item('groups'));

        $this->set('api', parse_url(env('BANKAPI_SERVER')));
        $this->set('username', $account['AccountMapping']['username']);
        $this->set('password', $account['AccountMapping']['password']);
    }

    /**
     * Slack Endpoint
     *
     * @url /bank/info/slack
     */
    public function slack() {
        // Ensure slack is enabled

        // Now verify post data
        if (!$this->request->is('post') || !isset($this->request->data['payload'])) {
            return $this->ajaxResponse(null);
        }

        // Decode the json, and hope it's all good
        $payload = json_decode($this->request->data['payload'], true);
        if (json_last_error() != JSON_ERROR_NONE) {
            return $this->ajaxResponse(null);
        }

        $purchase_id = $payload['callback_id'];
        $user = $payload['user']['name'];

        $purchase = $this->Purchase->findById($purchase_id);
        if (empty($purchase)) {
            return $this->ajaxResponse(null);
        }

        $prepend = '[COMPLETED] ';
        $postpend = ' - Completed by <@'.$user.'>';

        return $this->ajaxResponse([
            'response_type' => 'in_channel',
            'replace_original' => true,
            'text' => $payload['original_message']['text'],
            'attachments' => [
                [
                    'text' => ':white_check_mark: Completed by <@'.$user.'>',
                ]
            ],
        ]);
    }
}
