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
        $purchase = $this->Purchase->findByIdAndCompleted($pid, true);
        if (empty($purchase)) {
            $this->Purchase->id = $pid;
            $this->Purchase->save([
                'completed' => true,
                'completed_time' => time(),
                'completed_by' => $this->Auth->user('username'),
            ]);

            $this->Flash->success('Marked Purchase #'.$pid.' as completed!');
        }

        return $this->redirect(['plugin' => 'BankWeb', 'controller' => 'overview', 'action' => 'index']);
    }
}
