<?php
App::uses('BankWebAppController', 'BankWeb.Controller');

class HookController extends BankWebAppController {

    public $uses = ['BankWeb.Purchase'];

    public function beforeFilter() {
        // Don't call the parent's beforeFilter, as it
        // enforces logins.
    }

    /**
     * Slack Endpoint
     *
     * @url /bank/hook/slack
     */
    public function slack() {
        // Ensure slack is enabled
        if (!(bool)env('BANKWEB_SLACK_ENABLED') && !(bool)env('BANKWEB_SLACK_EXTENDED')) {
            return $this->ajaxResponse(null);
        }

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

        $purchase = $this->Purchase->findByIdAndCompleted($purchase_id, false);
        if (empty($purchase)) {
            return $this->ajaxResponse(null);
        }

        // Update the DB
        $this->Purchase->id = $purchase_id;
        $this->Purchase->save([
            'completed' => true,
            'completed_by' => $user,
            'completed_time' => time(),
        ]);

        // Return the new message to slack
        $prepend = '[COMPLETED] ';
        $postpend = ' - Completed by <@'.$user.'>';

        return $this->ajaxResponse([
            'response_type' => 'in_channel',
            'replace_original' => true,
            'text' => $prepend.$payload['original_message']['text'].$postpend,
            'attachments' => [
                [
                    'text' => ':white_check_mark: Completed by <@'.$user.'>',
                ],
            ],
        ]);
    }
}
