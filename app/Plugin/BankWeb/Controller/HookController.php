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

        // Message stuff
        $prepend = '[COMPLETED] ';
        $postpend = ' - Completed by #USER#';

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

        // If it's completed, well...
        if ($purchase['Purchase']['completed']) {
            $slack_message = $prepend;
            $slack_message .= $payload['original_message']['text'];
            $slack_message .= str_replace('#USER#', $purchase['Purchase']['completed_by'], $postpend);

            $completed_message = ':white_check_mark: Completed by '.$purchase['Purchase']['completed_by'];
        } else {
            // Update the DB
            $this->Purchase->id = $purchase_id;
            $this->Purchase->save([
                'completed' => true,
                'completed_by' => $user,
                'completed_time' => time(),
            ]);

            $slack_message = $prepend;
            $slack_message .= $payload['original_message']['text'];
            $slack_message .= str_replace('#USER#', '<@'.$user.'>', $postpend);

            $completed_message = ':white_check_mark: Completed by <@'.$user.'>';
        }

        // Return the new message to slack
        return $this->ajaxResponse([
            'response_type' => 'in_channel',
            'replace_original' => true,
            'text' => $slack_message,
            'attachments' => [
                [
                    'text' => $completed_message,
                ],
            ],
        ]);
    }
}
