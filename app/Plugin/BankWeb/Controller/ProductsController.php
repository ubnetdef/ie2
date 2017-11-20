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

            if (strlen($user_input) > env('BANKWEB_USERINPUT_MAX')) {
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

                if (env('CHATOPS_SERVICE') > 0) {
                    $this->sendChatOpsNotification($product, $user_input, $this->Purchase->id);
                }
            }

            return $this->redirect(['plugin' => 'BankWeb', 'controller' => 'products', 'action' => 'index']);
        }

        $this->set('item', $product);
        $this->set('accounts', $this->BankApi->accounts());
    }

    private function sendChatOpsNotification($product, $user_input, $purchase_id) {
        $purchase_url = Router::url(
            [
                'plugin' => 'BankWeb',
                'controller' => 'overview',
                'action' => 'view',
                $purchase_id,
            ],
            true
        );

        // Build the dynamic message
        $message = str_replace(
            ['#USERNAME#', '#GROUP#' , '#INPUT#'],
            [$this->Auth->user('username'), $this->Auth->group('name'), $user_input],
            $product['Product']['message_slack']
        );

        switch (env('CHATOPS_SERVICE')) {
            case 1: // Mattermost
                $message .= "\n\n[View Purchase](".$purchase_url.") - Purchase #".$purchase_id;

                $nonce = bin2hex(random_bytes(64));
                $additional = [
                    'username'    => 'ie2 - Bank',
                    'attachments' => [
                        [
                            'text' => 'Please mark this as completed',
                            'actions' => [
                                [
                                    'name' => 'Mark as completed',
                                    'integration' => [
                                        'url' => Router::url(
                                            [
                                                'plugin' => 'BankWeb',
                                                'controller' => 'hook',
                                                'action' => 'mattermost',
                                            ],
                                            true
                                        ),
                                        'context' => [
                                            'purchase_id'      => $purchase_id,
                                            'original_message' => $message,
                                            'nonce'            => $nonce,
                                            'hash'             => hash('sha256', $nonce.env('SECURITY_SALT')),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                $this->Mattermost->send($message, false, $additional);
                break;

            case 2: // Slack
                $message .= "\n\n<".$purchase_url."|View Purchase> - Purchase #".$purchase_id;

                $additional = [
                    'attachments' => json_encode([
                        [
                            'callback_id' => $purchase_id,
                            'fallback' => 'Please go to this URL: '.$purchase_url,
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

                $resp = $this->Slack->send(env('BANKWEB_SLACK_CHANNEL'), $message, $additional);

                // Save the channel and ts
                if ($resp['ok']) {
                    $this->Purchase->id = $purchase_id;
                    $this->Purchase->save([
                        'slack_ts' => $resp['ts'],
                        'slack_channel' => $resp['channel'],
                    ]);
                }
                break;

            default:
                throw new RuntimeException('Unknown ChatOps Service');
        }
    }
}
