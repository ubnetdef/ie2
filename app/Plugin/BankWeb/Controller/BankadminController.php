<?php
App::uses('BankWebAppController', 'BankWeb.Controller');
use Respect\Validation\Rules;

class BankadminController extends BankWebAppController {
    public $uses = ['Group', 'BankWeb.AccountMapping'];

    public function beforeFilter() {
        parent::beforeFilter();

        // Set the active menu item
        $this->set('at_backend', true);

        // Setup validators
        $this->validators = [
            'id' => new Rules\AllOf(
                new Rules\Digit()
            ),
            'group_id' => new Rules\AllOf(
                new Rules\Digit()
            ),
            'username' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
            'password' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
        ];
    }

    /**
     * BankWEB Admin Index Page
     *
     * @url /admin/bank
     * @url /bank_web/admin
     * @url /admin/bank/index
     * @url /bank_web/admin/index
     */
    public function index() {
        $this->set('groups', $this->Group->generateTreeList(null, null, null, '--'));
        $this->set('accounts', $this->AccountMapping->find('all'));
    }

    /**
     * BankWEB Admin API
     *
     * @url /admin/bank/api/<id>
     * @url /bank_web/admin/api/<id>
     */
    public function api($id = false) {
        $data = $this->AccountMapping->findById($id);
        if (empty($data)) {
            throw new NotFoundException('Unknown account');
        }

        return $this->ajaxResponse($data['AccountMapping']);
    }

    /**
     * BankWEB Credentials Save
     *
     * @url /admin/bank/save
     * @url /bank_web/admin/save
     */
    public function save() {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        // Validate the input
        $res = $this->_validate();

        if (!empty($res['errors'])) {
            $this->_errorFlash($res['errors']);

            return $this->redirect(['plugin' => 'bank_web', 'controller' => 'bankadmin', 'action' => 'index']);
        }

        if ($res['data']['id'] > 0) {
            $data = $this->AccountMapping->findById($res['data']['id']);
            if (empty($data)) {
                throw new NotFoundException('Unknown account mapping');
            }

            $this->AccountMapping->id = $res['data']['id'];
            $this->AccountMapping->save($res['data']);

            $msg = sprintf('Edited account mapping for user "%s"', $data['AccountMapping']['username']);

            $this->logMessage(
                'bank',
                $msg,
                [
                    'old_mapping' => $data['AccountMapping'],
                    'new_mapping' => $res['data']
                ],
                $data['AccountMapping']['id']
            );
            $this->Flash->success($msg.'!');
        } else {
            // Fix the data
            unset($res['data']['id']);

            $this->AccountMapping->create();
            $this->AccountMapping->save($res['data']);

            $msg = sprintf('Created account mapping on user "%s"', $res['data']['username']);
            $this->logMessage('bank', $msg, [], $this->AccountMapping->id);
            $this->Flash->success($msg.'!');
        }

        return $this->redirect(['plugin' => 'bank_web', 'controller' => 'bankadmin', 'action' => 'index']);
    }

    /**
     * BankWEB Account Mapping Delete
     *
     * @url /admin/bank/delete/<type>/<id>
     */
    public function delete($id = false) {
        $data = $this->AccountMapping->findById($id);
        if (empty($data)) {
            throw new NotFoundException('Unknown acount');
        }

        if ($this->request->is('post')) {
            $this->AccountMapping->delete($id);

            $msg = sprintf('Deleted account mapping for user "%s"', $data['AccountMapping']['username']);
            $this->logMessage('bank', $msg, ['mapping' => $data], $id);
            $this->Flash->success($msg.'!');
            return $this->redirect(['plugin' => 'bank_web', 'controller' => 'bankadmin', 'action' => 'index']);
        }

        $this->set('data', $data);
    }
}
