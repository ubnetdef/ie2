<?php
App::uses('BankWebAppController', 'BankWeb.Controller');

class ProductsController extends BankWebAppController {

    /**
     * Array of products loaded from the
     * env variable 'BANKWEB_PRODUCTS'
     */
    private $products = [];

    public function beforeFilter() {
        parent::beforeFilter();

        // Load the products
        $filename = ROOT . DS . env('BANKWEB_PRODUCTS');
        $this->products = json_decode(file_get_contents($filename), true);

        // Set the active menu item
        $this->set('at_bank', true);
    }

    /**
     * Product List Page
     *
     * @url /bank
     * @url /bank/products
     * @url /bank/products/index
     */
    public function index() {
        $this->set('products', $this->products);
    }

    /**
     * Product Purchase Confirmation
     *
     * @url /bank/products/confirm/<id>
     */
    public function confirm($id = false) {
        if ($id === false || !isset($this->products[$id])) {
            throw new NotFoundException('Unknown product');
        }

        $product = $this->products[$id];
        if (!$product['enabled']) {
            throw new NotFoundException('Unknown product');
        }

        if ($this->request->is('post')
            && isset($this->request->data['srcAcc'])
            && is_numeric($this->request->data['srcAcc'])
            && isset($this->request->data['pin'])
            && is_numeric($this->request->data['pin'])
        ) {
            try {
                $res = $this->BankApi->transfer(
                    $_POST['srcAcc'],
                    env('BANKWEB_WHITETEAM_ACCOUNT'),
                    $product['cost'],
                    $_POST['pin']
                );
            } catch (Exception $e) {
                $this->Flash->danger($e->getMessage());
            }

            if ($res === true) {
                $this->Flash->success($product['on_purchase']);

                if ((bool)env('BANKWEB_SLACK_ENABLED')) {
                    $this->_sendSlack($product['slack_message']);
                }
            }

            return $this->redirect(['plugin' => 'bank_web', 'controller' => 'products', 'action' => 'index']);
        }

        $this->set('item', $product);
        $this->set('accounts', $this->BankApi->accounts());
    }
}
