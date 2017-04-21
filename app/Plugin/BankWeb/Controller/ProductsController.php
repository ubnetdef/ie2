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
            $user_input = isset($this->request->data['user_input'])
                ? htmlentities($this->request->data['user_input'])
                : 'N/A';

            if ( strlen($user_input) > env('BANKWEB_USERINPUT_MAX') ) {
                $user_input = substr($user_input, 0, env('BANKWEB_USERINPUT_MAX'));
                $user_input .= '... (truncated)';
            }

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
                    'user_input' => $user_input,
                ]);

                if (!empty($product['Product']['message_user'])) {
                    $this->Flash->success($product['Product']['message_user']);
                }

                if (benv('BANKWEB_SLACK_ENABLED') && !empty($product['Product']['message_slack'])) {
                    $url = Router::url(
                        [
                            'plugin' => 'BankWeb',
                            'controller' => 'bankadmin',
                            'action' => 'view',
                            $this->Purchase->id,
                        ],
                        true
                    );

                    // Build the message
                    $message = $product['Product']['message_slack'];
                    $message .= "\n\n<".$url."|View Purchase> - Purchase #".$this->Purchase->id;

                    // Make the message dynamic
                    $message = str_replace(
                        ['#USERNAME#', '#GROUP#' , '#INPUT#'],
                        [$this->Auth->user('username'), $this->Auth->group('name'), $user_input],
                        $message
                    );

                    // Add our SICK attachment
                    $extra = [
                        'attachments' => json_encode([
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
                        ]),
                    ];

                    // Send it over to slack
                    $resp = $this->Slack->send(env('BANKWEB_SLACK_CHANNEL'), $message, $extra);

                    // Save the channel and ts
                    if ($resp['ok']) {
                        $this->Purchase->save([
                            'slack_ts' => $resp['ts'],
                            'slack_channel' => $resp['channel'],
                        ]);
                    }
                }
            }

            return $this->redirect(['plugin' => 'BankWeb', 'controller' => 'products', 'action' => 'index']);
        }

        $this->set('item', $product);
        $this->set('accounts', $this->BankApi->accounts());
    }
}
