<?php
App::uses('AppController', 'Controller');

class InjectsController extends AppController {

    public $uses = ['Config', 'Hint', 'UsedHint', 'Schedule', 'Submission'];

    private $groups = [];

    public function beforeFilter() {
        parent::beforeFilter();

        // Enforce logins
        $this->Auth->protect();

        // Mark the tab as active
        $this->set('at_injects', true);

        // Administrator blue team override
        $this->groups = $this->Auth->item('groups');
        if ($this->Auth->isStaff()) {
            $this->groups[] = (int)env('GROUP_BLUE');
        }

        // Load + setup the InjectStyler helper
        $this->helpers[] = 'InjectStyler';
        $this->helpers['InjectStyler'] = [
            'types'  => $this->Config->getInjectTypes(),
            'inject' => new stdClass(), // Nothing...for now
        ];
    }

    /**
     * Inject Inbox Page
     *
     * @url /injects
     * @url /injects/index
     */
    public function index() {
        if (benv('INJECT_INBOX_STREAM_VIEW')) {
            $this->set('injects', $this->Schedule->getInjects($this->groups));
            return $this->render('index_stream');
        } else {
            return $this->render('index_list');
        }
    }

    /**
     * API Endpoint for Injects
     *
     * The Inject Inbox Page will call
     * this endpoint every xx seconds.
     *
     * @url /injects/api
     */
    public function api() {
        return $this->ajaxResponse([
            'injects' => $this->Schedule->getInjects($this->groups),
        ]);
    }

    /**
     * Inject View Page
     *
     * @url /injects/view/<schedule_id>
     */
    public function view($sid = false) {
        $inject = $this->Schedule->getInject($sid, $this->groups);
        if (empty($inject)) {
            throw new NotFoundException('Unknown inject');
        }

        $submissions = $this->Submission->getSubmissions($inject->getInjectId(), $this->Auth->group('id'));

        // Setup the InjectStyler helper with the latest inject
        $this->helpers['InjectStyler']['inject'] = $inject;

        $this->set('hints', $this->Hint->find('count', ['conditions' => ['inject_id' => $inject->getInjectId()]]));
        $this->set('inject', $inject);
        $this->set('submissions', $submissions);
    }

    /**
     * Inject Submission Endpoint
     *
     * @url /injects/submit
     */
    public function submit() {
        if (!$this->request->is('post') || !isset($this->request->data['id'])) {
            throw new MethodNotAllowedException('Unauthorized');
        }

        $inject = $this->Schedule->getInject($this->request->data['id'], $this->groups);
        if (empty($inject)) {
            throw new NotFoundException('Unknown inject');
        }
        if (!$inject->isAcceptingSubmissions()) {
            throw new UnauthorizedException('Inject is no longer accepting submissions');
        }

        // Create a new InjectType Manager
        $typeManager = new InjectTypes\Manager($this->Config->getInjectTypes());
        $injectType = $typeManager->get($inject->getType());

        if (!$injectType->_validateSubmission($inject, $this->request->data)) {
            throw new BadRequestException('Non-valid submission');
        }

        // We're good! Save it!
        $this->Submission->create();
        $this->Submission->save([
            'inject_id' => $inject->getInjectId(),
            'user_id'   => $this->Auth->user('id'),
            'group_id'  => $this->Auth->group('id'),
            'created'   => time(),
            'data'      => $injectType->handleSubmission($inject, $this->request->data),
        ]);

        $this->logMessage(
            'submission',
            sprintf('Created submission for Inject #%d', $inject->getSequence()),
            [],
            $this->Submission->id
        );
        $this->Flash->success('Successfully submitted!');
        return $this->redirect('/injects/view/'.$inject->getScheduleId());
    }

    /**
     * Delete Inject Submission
     *
     * @url /injects/delete/<sid>
     */
    public function delete($sid = false) {
        $submission = $this->Submission->findById($sid);
        if (empty($submission) || !in_array($submission['Group']['id'], $this->groups)) {
            throw new NotFoundException('Unknown submission');
        }

        $this->Submission->id = $submission['Submission']['id'];
        $this->Submission->save([
            'deleted' => true,
        ]);

        $this->logMessage(
            'submission',
            sprintf('Deleted submission #%d on Inject #%d', $sid, $submission['Inject']['sequence']),
            [
                'user' => $this->Auth->user('username'),
            ],
            $sid
        );
        $this->Flash->success('Successfully deleted the submission!');
        $this->redirect($this->referer());
    }

    /**
     * View (download) Submission
     *
     * @url /injects/submission/<sid>
     */
    public function submission($sid = false) {
        $submission = $this->Submission->getSubmission($sid, $this->Auth->group('id'), true);
        if (empty($submission)) {
            throw new NotFoundException('Unknown submission');
        }

        $data = json_decode($submission['Submission']['data'], true);
        $download = (isset($this->params['url']['download']) && $this->params['url']['download'] == true);

        // Let's verify our data is correct
        if (md5(base64_decode($data['data'])) !== $data['hash']) {
            throw new InternalErrorException('Data storage failure');
        }

        // Create the new response for the data
        $response = new CakeResponse();
        $response->type($data['extension']);
        $response->body(base64_decode($data['data']));
        $response->disableCache();

        $type = ($download ? 'attachment' : 'inline');
        $filename = $data['filename'];
        $response->header('Content-Disposition', $type.'; filename="'.$filename.'"');

        return $response;
    }

    /**
     * API Endpoint for Hints
     *
     * Gets all the unlocked hints for an inject
     *
     * @url /injects/hints/<schedule_id>
     */
    public function hints($id = false) {
        if ( !benv('FEATURE_HINT_SUBSYSTEM') ) {
            throw new BadRequestException('Hint subsystem not enabled');
        }

        $inject = $this->Schedule->getInject($id, $this->groups);
        if (empty($inject)) {
            throw new NotFoundException('Unknown inject');
        }

        $hints = $this->Hint->getHints($inject->getInjectId(), $this->Auth->group('id'));
        if (empty($hints)) {
            throw new NotFoundException('Unknown inject');
        }

        $this->layout = 'ajax';
        $this->set('inject', $inject);
        $this->set('hints', $hints);
    }

    /**
     * API Endpoint to Unlock a Hint
     *
     * Unlocks a hint
     *
     * @url /injects/unlock/<hint_id>
     */
    public function unlock($sid = false, $id = false) {
        if ( !benv('FEATURE_HINT_SUBSYSTEM') ) {
            throw new BadRequestException('Hint subsystem not enabled');
        }

        $inject = $this->Schedule->getInject($sid, $this->groups);
        if (empty($inject)) {
            return $this->ajaxResponse(false);
        }

        $hints = $this->Hint->getHints($inject->getInjectId(), $this->Auth->group('id'));
        if (empty($hints)) {
            return $this->ajaxResponse(false);
        }

        // Find said hint, make sure we can unlock it
        $hint_title = '-error-';
        foreach ($hints as $h) {
            if ($h['Hint']['id'] != $id) {
                continue;
            }

            if ($h['Hint']['unlocked']) {
                return $this->ajaxResponse(true);
            }
            if (!$h['Hint']['dependency_met']) {
                return $this->ajaxResponse(false);
            }
            if ($h['Hint']['time_wait'] > 0 && $inject->getStart() + $h['Hint']['time_wait'] > time()) {
                return $this->ajaxResponse(false);
            }

            $hint_title = $h['Hint']['title'];
        }

        // If we got here, we're good
        $this->UsedHint->create();
        $this->UsedHint->save([
            'hint_id'  => $id,
            'user_id'  => $this->Auth->user('id'),
            'group_id' => $this->Auth->group('id'),
            'time'     => time(),
        ]);

        $this->logMessage(
            'hint',
            sprintf('Unlocked Hint "%s" for Inject #%d', $hint_title, $inject->getSequence()),
            [],
            $id
        );

        return $this->ajaxResponse(true);
    }
}
