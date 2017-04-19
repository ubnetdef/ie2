<?php
App::uses('AdminAppController', 'Admin.Controller');
use Respect\Validation\Rules;

class GroupsController extends AdminAppController {

    public $uses = ['Group'];

    public function beforeFilter() {
        parent::beforeFilter();

        $this->validators = [
            'name' => new Rules\AllOf(
                new Rules\Alnum('-_'),
                new Rules\NotEmpty()
            ),
            'team_number' => new Rules\Optional(
                new Rules\Digit()
            ),
            'parent_id' => new Rules\Optional(
                new Rules\Digit()
            ),
        ];
    }

    /**
     * Group List Page
     *
     * @url /admin/group
     * @url /admin/group/index
     */
    public function index() {
        $mappings = [];
        foreach ($this->Group->find('all') as $g) {
            if ($g['Group']['team_number'] === null) {
                continue;
            }

            $mappings[$g['Group']['id']] = $g['Group']['team_number'];
        }

        $this->set('mappings', $mappings);
        $this->set('groups', $this->Group->generateTreeList(null, null, null, '-- '));
    }

    /**
     * Create Group
     *
     * @url /admin/group/create
     */
    public function create() {
        if ($this->request->is('post')) {
            // Validate the input
            $res = $this->validate();

            if (empty($res['errors'])) {
                $this->Group->create();
                $this->Group->save($res['data']);

                $this->logMessage(
                    'groups',
                    sprintf('Created group "%s"', $created['name']),
                    [],
                    $this->Group->id
                );

                $this->Flash->success('The group has been created!');
                return $this->redirect(['plugin' => 'admin', 'controller' => 'groups', 'action' => 'index']);
            } else {
                $this->errorFlash($res['errors']);
            }
        }

        $this->set('groups', $this->Group->generateTreeList(null, null, null, '-- '));
    }

    /**
     * Edit Group
     *
     * @url /admin/group/edit/<gid>
     */
    public function edit($gid = false) {
        $group = $this->Group->findById($gid);
        if (empty($group)) {
            throw new NotFoundException('Unknown group');
        }

        if ($this->request->is('post')) {
            // Validate the input
            $res = $this->validate();

            if (empty($res['errors'])) {
                $this->Group->id = $gid;
                $this->Group->save($res['data']);

                $this->logMessage(
                    'groups',
                    sprintf('Updated group "%s"', $group['Group']['name']),
                    [
                        'old_group' => $group['Group'],
                        'new_group' => $res['data'],
                    ],
                    $uid
                );

                $this->Flash->success('The user has been updated!');
                return $this->redirect(['plugin' => 'admin', 'controller' => 'groups', 'action' => 'index']);
            } else {
                $this->errorFlash($res['errors']);
            }
        }

        $this->set('group', $group);
        $this->set('groups', $this->Group->generateTreeList(null, null, null, '-- '));
    }

    /**
     * Delete group
     *
     * @url /admin/group/delete/<gid>
     */
    public function delete($gid = false) {
        $group = $this->Group->findById($gid);
        if (empty($group)) {
            throw new NotFoundException('Unknown group');
        }

        if ($this->request->is('post')) {
            $this->Group->delete($gid);

            $msg = sprintf('Deleted group "%s" (#%d)', $group['Group']['name'], $gid);

            $this->logMessage(
                'groups',
                $msg,
                [
                    'group' => $group['Group'],
                ],
                $gid
            );

            $this->Flash->success($msg);
            return $this->redirect(['plugin' => 'admin', 'controller' => 'groups', 'action' => 'index']);
        }

        $this->set('group', $group);
    }
}
