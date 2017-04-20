<?php
App::uses('BankWebAppController', 'BankWeb.Controller');

class ProductsController extends BankWebAppController {

    public $uses = ['BankWeb.Product', 'BankWeb.Purchase'];

    /**
     * Array of products loaded from the
     * env variable 'BANKWEB_PRODUCTS'
     */
    private $products = [];

    public function beforeFilter() {
        parent::beforeFilter();

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
        $this->set('products', $this->Product->findAllByEnabled(true));
    }

    /**
     * Product Purchase Confirmation
     *
     * @url /bank/products/confirm/<id>
     */
    public function confirm($id = false) {
        $product = $this->Product->findByIdAndEnabled($id, true);
        if (empty($product)) {
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
                    $product['Product']['cost'],
                    $_POST['pin']
                );
            } catch (Exception $e) {
                $this->Flash->danger($e->getMessage());
            }

            if ($res === true) {
                // Create the purchase
                $this->Purchase->create();
                $this->Purchase->save([
                    'product_id' => $product['Product']['id'],
                    'user_id'    => $this->Auth->user('id'),
                    'group_id'   => $this->Auth->group('id'),
                    'time'       => time(),
                ]);

                if (!empty($product['Product']['message_user'])) {
                    $this->Flash->success($product['Product']['message_user']);
                }

                if ((bool)env('BANKWEB_SLACK_ENABLED') && !empty($product['Product']['message_slack'])) {
                    $url = Router::url(
                        [
                            'plugin' => 'BankWeb',
                            'controller' => 'bankadmin',
                            'action' => 'view',
                            $this->Purchase->id,
                        ],
                        true
                    );

                    $message = $product['Product']['message_slack'];
                    $message .= "\n\n<".$url."|View Purchase> - Purchase #".$this->Purchase->id;

                    $extra = [];
                    if ((bool)env('BANKWEB_SLACK_EXTENDED')) {
                        $extra = [
                            'attachments' => [
                                [
                                    'callback_id' => $this->Purchase->id,
                                    'fallback' => 'Please go to this URL: '.$url,
                                    'actions' => [
                                        [
                                            'name' => 'handled',
                                            'text' => 'Mark as completed',
                                            'type' => 'button',
                                            'style' => 'primary',
                                        ],
                                    ],
                                ],
                            ],
                        ];
                    }

                    $this->_sendSlack($message, $extra);
                }
            }

            return $this->redirect(['plugin' => 'BankWeb', 'controller' => 'products', 'action' => 'index']);
        }

        $this->set('item', $product);
        $this->set('accounts', $this->BankApi->accounts());
    }
}
