<?php
App::uses('AdminAppController', 'Controller');
use Respect\Validation\Rules;

class AdminUsersController extends AdminAppController {
	public $uses = ['User', 'Group'];

	public function beforeFilter() {
		parent::beforeFilter();

		$this->validators = [
			'username' => new Rules\AllOf(
				new Rules\Alnum('-_'),
				new Rules\NotEmpty(),
				new Rules\NoWhitespace()
			),
			'password' => new Rules\AllOf(
				new Rules\NotEmpty()
			),
			'group_id' => new Rules\AllOf(
				new Rules\Digit(),
				new Rules\NotEmpty()
			),
			'active' => new Rules\AllOf(
				new Rules\BoolVal()
			),
			'expiration' => new Rules\AllOf(
				new Rules\Length(1, 10, true)
			),
		];
	}

	/**
	 * User List Page 
	 *
	 * @url /adminuser
	 * @url /admin/user
	 * @url /adminuser/index
	 * @url /admin/user/index
	 */
	public function index() {
		$this->set('users', $this->User->find('all', [
			'fields' => [
				'User.id', 'User.username', 'User.expiration', 'User.active', 'Group.name'
			],
		]));
	}

	/**
	 * Emulate User 
	 *
	 * @url /adminuser/emulate/<uid>
	 * @url /admin/user/emulate/<uid>
	 */
	public function emulate($uidOrUsername=false) {
		try {
			$curUID = $this->Auth->user('id');

			$this->Auth->emulate($uidOrUsername);

			$msg = sprintf('Emulated user %s', $this->Auth->user('username'));
			$this->logMessage('emulate', $msg, [], $this->Auth->user('id'), $curUID);
			$this->Flash->success($msg.'!');

			return $this->redirect('/');
		} catch ( InternalErrorException $e ) {
			throw $e;
		}
	}

	/**
	 * Create User 
	 *
	 * @url /adminuser/create
	 * @url /admin/user/create
	 */
	public function create() {
		if ( $this->request->is('post') ) {
			// Validate the input
			$res = $this->_validate();

			if ( empty($res['errors']) ) {
				$this->User->create();
				$this->User->save($res['data']);

				$this->logMessage(
					'users',
					sprintf('Created user "%s"', $res['data']['username']),
					[],
					$this->User->id
				);

				$this->Flash->success('The user has been created!');
				return $this->redirect('/admin/users');
			} else {
				$this->_errorFlash($res['errors']);
			}
		}

		$this->set('groups', $this->Group->generateTreeList(null, null, null, '-- '));
	}

	/**
	 * Edit User 
	 *
	 * @url /adminuser/edit/<uid>
	 * @url /admin/user/edit/<uid>
	 */
	public function edit($uid=false) {
		$user = $this->User->findById($uid);
		if ( empty($user) ) {
			throw new NotFoundException('Unknown user!');
		}

		if ( $this->request->is('post') ) {
			// Validate the input
			$res = $this->_validate();

			if ( empty($res['errors']) ) {
				$this->User->id = $uid;
				$this->User->save($res['data']);

				$this->logMessage(
					'users',
					sprintf('Updated user "%s"', $user['User']['username']),
					[
						'old_user' => $user['User'],
						'new_user' => $res['data'],
					],
					$uid
				);

				$this->Flash->success('The user has been updated!');
				return $this->redirect('/admin/users');
			} else {
				$this->_errorFlash($res['errors']);
			}
		}

		$this->set('groups', $this->Group->generateTreeList(null, null, null, '-- '));
		$this->set('user', $user);
	}

	/**
	 * Delete User 
	 *
	 * @url /adminuser/delete/<uid>
	 * @url /admin/user/delete/<uid>
	 */
	public function delete($uid=false) {
		$user = $this->User->findById($uid);
		if ( empty($user) ) {
			throw new NotFoundException('Unknown user!');
		}

		if ( $this->request->is('post') ) {
			$this->User->delete($uid);

			$msg = sprintf('Deleted user "%s" (#%d)', $user['User']['username'], $uid);

			$this->logMessage(
				'users',
				$msg,
				[
					'user' => $user['User'],
				],
				$uid
			);

			$this->Flash->success($msg);
			return $this->redirect('/admin/users');
		}

		$this->set('user', $user);
	}

	/**
	 * Toggle User Status 
	 *
	 * @url /adminuser/flip/<uid>
	 * @url /admin/user/flip/<uid>
	 */
	public function flip($uid=false) {
		$user = $this->User->findById($uid);
		if ( empty($user) ) {
			throw new NotFoundException('Unknown user!');
		}

		$this->User->id = $uid;
		$this->User->save([
			'active' => !$user['User']['active'],
		]);

		$this->logMessage(
			'users',
			sprintf('Flipped the status user "%s" to %sactive', $user['User']['username'], $user['User']['active'] ? 'in' : ''),
			[
				'old_status' => $user['User']['active'],
				'new_status' => !$user['User']['active'],
			],
			$uid
		);

		$this->Flash->success(sprintf('Toggled status for user %s!', $user['User']['username']));
		return $this->redirect('/admin/users');
	}
}
