<?php
App::uses('BankWebAppController', 'BankWeb.Controller');

class HookController extends BankWebAppController {

    public $uses = ['BankWeb.Purchase'];

    const MESSAGE_PREPEND = '[COMPLETED] ';
    const MESSAGE_POSTPEND = ' - Completed by #USER#';

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
        if (!env('SLACK_APIKEY')) {
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

        $purchase = $this->Purchase->findById($purchase_id);
        if (empty($purchase)) {
            return $this->ajaxResponse(null);
        }

        // Mark it as completed, if it's not
        if (!$purchase['Purchase']['completed']) {
            // Update the DB
            $this->Purchase->id = $purchase_id;
            $this->Purchase->save([
                'completed' => true,
                'completed_by' => $user,
                'completed_time' => time(),
            ]);

            $completed_by = '<@'.$user.'>';
        } else {
            $completed_by = $purchase['Purchase']['completed_by'];
        }

        // Build the response
        $slack_message = self::MESSAGE_PREPEND;
        $slack_message .= $payload['original_message']['text'];
        $slack_message .= str_replace('#USER#', $completed_by, self::MESSAGE_POSTPEND);

        $completed_message = ':white_check_mark: Completed by '.$completed_by;

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

    /**
     * Mattermost Endpoint
     *
     * @url /bank/hook/mattermost
     */
    public function mattermost() {
        // Ensure mattermost is enabled
        if (!env('MATTERMOST_WEBHOOK_URL')) {
            return $this->ajaxResponse(null);
        }

        // Now verify post data
        $input = $this->request->input();
        if (!$this->request->is('post') || empty($input)) {
            return $this->ajaxResponse(null);
        }

        // Decode the json, and hope it's all good
        $payload = json_decode($input, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            return $this->ajaxResponse(null);
        }

        // Verify the payload
        if (!isset($payload['context']['hash'])
            || hash('sha256', $payload['context']['nonce'].env('SECURITY_SALT')) != $payload['context']['hash']
        ) {
            return $this->ajaxResponse(null);
        }

        $purchase_id = $payload['context']['purchase_id'];
        $user = $payload['user_id'];

        $purchase = $this->Purchase->findById($purchase_id);
        if (empty($purchase)) {
            return $this->ajaxResponse(null);
        }

        // Mark it as completed, if it's not
        if (!$purchase['Purchase']['completed']) {
            // Update the DB
            $this->Purchase->id = $purchase_id;
            $this->Purchase->save([
                'completed' => true,
                'completed_by' => $user,
                'completed_time' => time(),
            ]);

            $completed_by = '<@'.$user.'>';
        } else {
            $completed_by = $purchase['Purchase']['completed_by'];
        }

        // Build the response
        $slack_message = self::MESSAGE_PREPEND;
        $slack_message .= $payload['context']['original_message'];
        $slack_message .= "\n\n".'> :white_check_mark: Completed by '.$completed_by;

        // Return the new message to mattermost
        return $this->ajaxResponse([
            'update' => [
                'message' => $slack_message,
            ],
        ]);
    }
}
