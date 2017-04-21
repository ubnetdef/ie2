<?php
App::uses('BankWebAppController', 'BankWeb.Controller');
use Respect\Validation\Rules;

class BankadminController extends BankWebAppController {

    public $uses = ['Group', 'BankWeb.AccountMapping', 'BankWeb.Product', 'BankWeb.Purchase'];

    public function beforeFilter() {
        parent::beforeFilter();

        // Set the active menu item
        $this->set('at_backend', true);

        // Enforce admins
        $this->Auth->protect(env('GROUP_ADMINS'));
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
        $this->set('products', $this->Product->find('all'));
    }

    /**
     * BankWEB Admin API
     *
     * @url /admin/bank/api/<table>/<id>
     * @url /bank_web/admin/api/<table>/<id>
     */
    public function api($table = false, $id = false) {
        switch ($table) {
            case 'mapping':
                $data = $this->AccountMapping->findById($id);
                if (empty($data)) {
                    throw new NotFoundException('Unknown account');
                }

                $result = $data['AccountMapping'];
                break;

            case 'product':
                $data = $this->Product->findById($id);
                if (empty($data)) {
                    throw new NotFoundException('Unknown account');
                }

                $result = $data['Product'];
                break;

            default:
                $result = [];
                break;
        }

        return $this->ajaxResponse($result);
    }

    /**
     * BankWEB Product Save
     *
     * @url /admin/bank/saveProduct
     * @url /bank_web/admin/saveProduct
     */
    public function saveProduct() {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        // Validate the input
        $this->validators = [
            'id' => new Rules\AllOf(
                new Rules\Digit()
            ),
            'enabled' => new Rules\AllOf(
                new Rules\BoolVal()
            ),
            'name' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
            'description' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
            'cost' => new Rules\AllOf(
                new Rules\Digit()
            ),
            'user_input' => new Rules\Optional(
                new Rules\NotEmpty()
            ),
            'message_user' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
            'message_slack' => new Rules\Optional(
                new Rules\NotEmpty()
            ),
        ];
        
        $res = $this->_validate();

        if (!empty($res['errors'])) {
            $this->_errorFlash($res['errors']);

            return $this->redirect(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'index']);
        }

        if ($res['data']['id'] > 0) {
            $data = $this->Product->findById($res['data']['id']);
            if (empty($data)) {
                throw new NotFoundException('Unknown product');
            }

            $this->Product->id = $res['data']['id'];
            $this->Product->save($res['data']);

            $msg = sprintf('Edited product #%d', $data['Product']['id']);

            $this->logMessage(
                'bank',
                $msg,
                [
                    'old_product' => $data['Product'],
                    'new_product' => $res['data']
                ],
                $data['Product']['id']
            );
            $this->Flash->success($msg.'!');
        } else {
            // Fix the data
            unset($res['data']['id']);

            $this->Product->create();
            $this->Product->save($res['data']);

            $msg = sprintf('Created product - %s', $res['data']['name']);
            $this->logMessage('bank', $msg, [], $this->Product->id);
            $this->Flash->success($msg.'!');
        }

        return $this->redirect(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'index']);
    }

    /**
     * BankWEB Credentials Save
     *
     * @url /admin/bank/saveAccount
     * @url /bank_web/admin/saveAccount
     */
    public function saveAccount() {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        // Validate the input
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

        $res = $this->_validate();

        if (!empty($res['errors'])) {
            $this->_errorFlash($res['errors']);

            return $this->redirect(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'index']);
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

        return $this->redirect(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'index']);
    }

    /**
     * BankWEB Product Delete
     *
     * @url /admin/bank/deleteProduct/<id>
     */
    public function deleteProduct($id = false) {
        $data = $this->Product->findById($id);
        if (empty($data)) {
            throw new NotFoundException('Unknown product');
        }

        if ($this->request->is('post')) {
            $this->Product->delete($id);

            $msg = sprintf('Deleted product "%s"', $data['Product']['name']);
            $this->logMessage('bank', $msg, ['product' => $data['Product']], $id);
            $this->Flash->success($msg.'!');
            return $this->redirect(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'index']);
        }

        $this->set('data', $data);
    }

    /**
     * BankWEB Account Mapping Delete
     *
     * @url /admin/bank/deleteMapping/<id>
     */
    public function deleteMapping($id = false) {
        $data = $this->AccountMapping->findById($id);
        if (empty($data)) {
            throw new NotFoundException('Unknown acount');
        }

        if ($this->request->is('post')) {
            $this->AccountMapping->delete($id);

            $msg = sprintf('Deleted account mapping for user "%s"', $data['AccountMapping']['username']);
            $this->logMessage('bank', $msg, ['mapping' => $data['AccountMapping']], $id);
            $this->Flash->success($msg.'!');
            return $this->redirect(['plugin' => 'BankWeb', 'controller' => 'bankadmin', 'action' => 'index']);
        }

        $this->set('data', $data);
    }
}
