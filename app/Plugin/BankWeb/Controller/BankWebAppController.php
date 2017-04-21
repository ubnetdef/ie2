<?php
App::uses('AppController', 'Controller');

class BankWebAppController extends AppController {

    public $components = ['BankWeb.BankApi'];

    public $uses = ['BankWeb.AccountMapping'];

    public function beforeFilter() {
        parent::beforeFilter();

        // Ensure logins
        $this->Auth->protect();

        // Grab the account + set the credentials for the API
        $account = $this->AccountMapping->getAccount($this->Auth->item('groups'));

        if (empty($account)) {
            throw new BadRequestException('You do not have a bank account associated with your user/groups');
        }
        $this->BankApi->setCredentials($account['AccountMapping']['username'], $account['AccountMapping']['password']);
    }

    /**
     * Sends a slack message
     *
     */
    protected function _sendSlackEndpoint($endpoint, $data = []) {
        if (!env('SLACK_APIKEY')) {
            return;
        }

        // Build the payload
        $payload = [
            'token' => env('SLACK_APIKEY'),
        ] + $data;
        $payload = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', http_build_query($payload));

        $ch = curl_init('https://slack.com/api/'.$endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}
