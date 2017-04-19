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
}
