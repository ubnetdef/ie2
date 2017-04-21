<?php
App::uses('BankWebAppController', 'BankWeb.Controller');

class OverviewController extends BankWebAppController {

    public $uses = ['BankWeb.Purchase'];

    public function beforeFilter() {
        parent::beforeFilter();

        // Set the active menu item
        $this->set('at_staff', true);

        // Enforce staff
        $this->Auth->protect(env('GROUP_STAFF'));
    }

    /**
     * Overview Page
     *
     * @url /staff/bank
     * @url /staff/bank/index
     */
    public function index() {
        $this->set('purchases', $this->Purchase->find('all', [
            'order' => [
                'Purchase.id DESC',
            ],
        ]));
    }

    /**
     * Overview Mark as Completed
     *
     * @url /staff/bank/mark/<purchase_id>
     */
    public function mark($pid) {
        $purchase = $this->Purchase->findByIdAndCompleted($pid, false);
        if (!empty($purchase)) {
            $this->Purchase->id = $pid;
            $this->Purchase->save([
                'completed' => true,
                'completed_time' => time(),
                'completed_by' => $this->Auth->user('username'),
            ]);

            // Update slack
            if (benv('BANKWEB_SLACK_ENABLED')
                && !empty($purchase['Purchase']['slack_ts'])
                && !empty($purchase['Purchase']['slack_channel'])
            ) {
                $url = Router::url(
                    [
                        'plugin' => 'BankWeb',
                        'controller' => 'bankadmin',
                        'action' => 'view',
                        $pid,
                    ],
                    true
                );

                // Rebuild the message, but add some more tags
                $message = '[COMPLETED] ';
                $message .= $purchase['Product']['message_slack'];
                $message .= "\n\n<".$url."|View Purchase> - Purchase #".$pid;
                $message .= ' - Completed by '.$this->Auth->user('username');
                $message .= ' (via InjectEngine2)';

                // Make the message dynamic again
                $message = str_replace(
                    ['#USERNAME#', '#GROUP#'],
                    [$purchase['User']['username'], $purchase['User']['Group']['name']],
                    $message
                );

                $attachments = [
                    [
                        'text' => ':white_check_mark: Completed by '.$this->Auth->user('username'),
                    ],
                ];

                $resp = $this->Slack->update(
                    $purchase['Purchase']['slack_ts'],
                    $purchase['Purchase']['slack_channel'],
                    $message,
                    [
                        'parse' => 'none',
                        'attachments' => json_encode($attachments),
                    ]
                );
            }

            $this->Flash->success('Marked Purchase #'.$pid.' as completed!');
        }

        return $this->redirect(['plugin' => 'BankWeb', 'controller' => 'overview', 'action' => 'index']);
    }
}
