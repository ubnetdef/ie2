<?php
App::uses('AdminAppController', 'Admin.Controller');
use Respect\Validation\Rules;

class InjectsController extends AdminAppController {
    public $uses = ['Attachment', 'Config', 'Inject', 'Schedule'];

    public function beforeFilter() {
        parent::beforeFilter();

        // Load + setup the InjectStyler helper
        $this->helpers[] = 'InjectStyler';
        $this->helpers['InjectStyler'] = [
            'types'  => $this->Config->getInjectTypes(),
            'inject' => new stdClass(), // Nothing...for now
        ];

        // Setup the validators
        $this->validators = [
            'sequence' => new Rules\AllOf(
                new Rules\Digit()
            ),
            'title' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
            'content' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
            'from_name' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
            'from_email' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
            'grading_guide' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
            'max_points' => new Rules\AllOf(
                new Rules\Digit(),
                new Rules\NotEmpty()
            ),
            'max_submissions' => new Rules\AllOf(
                new Rules\Digit(),
                new Rules\NotEmpty()
            ),
            'type' => new Rules\AllOf(
                new Rules\Alnum('-_'),
                new Rules\NotEmpty()
            ),
        ];
    }

    /**
     * Inject List Page
     *
     * @url /admin/injects
     * @url /admin/injects/index
     */
    public function index() {
        $this->set('injects', $this->Inject->find('all'));
    }

    /**
     * Create Inject
     *
     * @url /admin/injects/create
     */
    public function create() {
        if ($this->request->is('post')) {
            // Validate the input
            $res = $this->_validate();

            if (empty($res['errors'])) {
                // Upload the new attachments
                if (isset($this->request->data['new_attachments'])) {
                    foreach ($this->request->data['new_attachments'] as $new) {
                        $contents = file_get_contents($new['tmp_name']);
                        $data = json_encode([
                            'extension' => pathinfo($new['name'], PATHINFO_EXTENSION),
                            'hash'      => md5($contents),
                            'data'      => base64_encode($contents)
                        ]);

                        $this->Attachment->create();
                        $this->Attachment->save([
                            'inject_id' => $inject['Inject']['id'],
                            'name'      => $new['name'],
                            'data'      => $data,
                        ]);
                    }
                }

                $this->Inject->create();
                $this->Inject->save($res['data']);

                $this->logMessage(
                    'injects',
                    sprintf('Created inject "%s"', $res['data']['title']),
                    [],
                    $this->Inject->id
                );

                $this->Flash->success('The inject has been created!');
                return $this->redirect(['plugin' => 'admin', 'controller' => 'injects', 'action' => 'index']);
            } else {
                $this->_errorFlash($res['errors']);
            }
        }
    }

    /**
     * Edit Inject
     *
     * @url /admin/injects/edit/<id>
     */
    public function edit($id = false) {
        $inject = $this->Inject->findById($id);
        if (empty($inject)) {
            throw new NotFoundException('Unknown inject');
        }

        if ($this->request->is('post')) {
            // Validate the input
            $res = $this->_validate();

            if (empty($res['errors'])) {
                // Figure out if we deleted any attachments
                foreach ($inject['Attachment'] as $i => $a) {
                    if (!isset($this->request->data['attachments'][$a['id']])) {
                        $this->Attachment->delete($a['id']);

                        unset($inject['Attachment'][$i]);
                    }
                }

                // Upload the new attachments
                if (isset($this->request->data['new_attachments'])) {
                    foreach ($this->request->data['new_attachments'] as $new) {
                        $contents = file_get_contents($new['tmp_name']);
                        $data = json_encode([
                            'extension' => pathinfo($new['name'], PATHINFO_EXTENSION),
                            'hash'      => md5($contents),
                            'data'      => base64_encode($contents)
                        ]);

                        $this->Attachment->create();
                        $this->Attachment->save([
                            'inject_id' => $inject['Inject']['id'],
                            'name'      => $new['name'],
                            'data'      => $data,
                        ]);
                    }
                }

                $this->Inject->id = $id;
                $this->Inject->save($res['data']);

                $this->logMessage(
                    'injects',
                    sprintf('Updated inject "%s"', $inject['Inject']['title']),
                    [
                        'old_inject' => $inject['Inject'],
                        'new_inject' => $res['data'],
                    ],
                    $id
                );

                $this->Flash->success('The inject has been updated!');
                return $this->redirect(['plugin' => 'admin', 'controller' => 'injects', 'action' => 'index']);
            } else {
                $this->_errorFlash($res['errors']);
            }
        }

        $this->set('inject', $inject);
    }

    /**
     * Delete Inject
     *
     * @url /admin/injects/delete/<id>
     */
    public function delete($id = false) {
        $inject = $this->Inject->findById($id);
        if (empty($inject)) {
            throw new NotFoundException('Unknown inject');
        }

        if ($this->request->is('post')) {
            $this->Inject->delete($id);

            // Delete all associated schedules
            $schedules = [];
            foreach ($this->Schedule->findByInjectId($id) as $s) {
                $schedules[] = $s['Schedule']['id'];
            }
            $this->Schedule->delete($schedules);

            $msg = sprintf('Deleted inject "%s"', $inject['Inject']['title']);
            $this->logMessage('injects', $msg, ['inject' => $inject], $id);
            $this->Flash->success($msg.'!');
            return $this->redirect(['plugin' => 'admin', 'controller' => 'injects', 'action' => 'index']);
        }

        $this->set('inject', $inject);
    }
}
