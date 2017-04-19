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
}
