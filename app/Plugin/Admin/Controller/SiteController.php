<?php
App::uses('AdminAppController', 'Admin.Controller');
use Respect\Validation\Rules;

class SiteController extends AdminAppController {

    public $uses = ['Announcement', 'Config'];

    /**
     * Config Index Page
     *
     * @url /admin/site
     * @url /admin/site/index
     */
    public function index() {
        $this->set('announce', $this->Announcement->find('all'));
        $this->set('config', $this->Config->find('all'));
    }

    /**
     * Config API Page
     *
     * @url /admin/site/api/<type>/<id>
     */
    public function api($type = 'announcement', $id = false) {
        switch ($type) {
            case 'config':
                $config = $this->Config->findById($id);
                if (empty($config)) {
                    throw new NotFoundException('Unknown config');
                }

                $data = $config['Config'];
                break;

            case 'announcement':
                $announcement = $this->Announcement->findById($id);
                if (empty($announcement)) {
                    throw new NotFoundException('Unknown announcement');
                }

                $data = $announcement['Announcement'];
                break;

            default:
                throw new MethodNotAllowedException();
        }

        return $this->ajaxResponse($data);
    }

    /**
     * Config Edit/Create URL
     *
     * @url /admin/site/api
     */
    public function config() {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        // Validate the input
        $this->validators = [
            'id' => new Rules\AllOf(
                new Rules\Digit()
            ),
            'key' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
            'value' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
        ];
        $res = $this->_validate();

        if (!empty($res['errors'])) {
            $this->_errorFlash($res['errors']);

            return $this->redirect(['plugin' => 'admin', 'controller' => 'site', 'action' => 'index']);
        }

        if ($res['data']['id'] > 0) {
            $config = $this->Config->findById($res['data']['id']);
            if (empty($config)) {
                throw new NotFoundException('Unknown config');
            }

            $this->Config->id = $res['data']['id'];
            $this->Config->save($res['data']);

            $msg = sprintf('Edited config value "%s"', $config['Config']['key']);

            $this->logMessage(
                'config',
                $msg,
                [
                    'old_config' => $config['Config'],
                    'new_config' => $res['data']
                ],
                $config['Config']['id']
            );
            $this->Flash->success($msg.'!');
        } else {
            // Fix the data
            unset($res['data']['id']);

            $this->Config->create();
            $this->Config->save($res['data']);

            $msg = sprintf('Created config value "%s"', $res['data']['key']);
            $this->logMessage('config', $msg, [], $this->Config->id);
            $this->Flash->success($msg.'!');
        }

        return $this->redirect(['plugin' => 'admin', 'controller' => 'site', 'action' => 'index']);
    }

    /**
     * Announcement Edit/Create URL
     *
     * @url /admin/site/announcement
     */
    public function announcement() {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        // Validate the input
        $this->validators = [
            'id' => new Rules\AllOf(
                new Rules\Digit()
            ),
            'content' => new Rules\AllOf(
                new Rules\NotEmpty()
            ),
            'active' => new Rules\AllOf(
                new Rules\Digit()
            ),
            'expiration' => new Rules\AllOf(
                new Rules\Digit()
            ),
        ];
        $res = $this->_validate();

        if (!empty($res['errors'])) {
            $this->_errorFlash($res['errors']);

            return $this->redirect(['plugin' => 'admin', 'controller' => 'site', 'action' => 'index']);
        }

        if ($res['data']['id'] > 0) {
            $announcement = $this->Announcement->findById($res['data']['id']);
            if (empty($announcement)) {
                throw new NotFoundException('Unknown announcement');
            }

            $this->Announcement->id = $res['data']['id'];
            $this->Announcement->save($res['data']);

            $msg = sprintf('Edited announcement #%d', $announcement['Announcement']['id']);

            $this->logMessage('announcement', $msg, [
                'old_announcement' => $announcement['Announcement'],
                'new_announcement' => $res['data']
            ], $announcement['Announcement']['id']);
            $this->Flash->success($msg.'!');
        } else {
            // Fix the data
            unset($res['data']['id']);

            $this->Announcement->create();
            $this->Announcement->save($res['data']);

            $msg = sprintf('Created announcement #%d', $this->Announcement->id);
            $this->logMessage('announcement', $msg, [], $this->Announcement->id);
            $this->Flash->success($msg.'!');
        }

        return $this->redirect(['plugin' => 'admin', 'controller' => 'site', 'action' => 'index']);
    }

    /**
     * Config Delete
     *
     * @url /admin/site/delete/<type>/<id>
     */
    public function delete($type = 'announcement', $id = false) {
        $modal = ($type == 'announcement' ? $this->Announcement : $this->Config);
        $msgType = ($type == 'announcement' ? 'Announcement' : 'Config');
        $logType = ($type == 'announcement' ? 'announcement' : 'config');

        $data = $modal->findById($id);
        if (empty($data)) {
            throw new NotFoundException('Unknown '.$msgType);
        }

        if ($this->request->is('post')) {
            $modal->delete($id);

            $msg = sprintf('Deleted %s #%d', $msgType, $data[$msgType]['id']);
            $this->logMessage($logType, $msg, [$logType => $data], $id);
            $this->Flash->success($msg.'!');
            return $this->redirect(['plugin' => 'admin', 'controller' => 'site', 'action' => 'index']);
        }

        $this->set('data', $data);
    }
}
