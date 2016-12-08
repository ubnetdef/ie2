<?php
App::uses('AdminAppController', 'Controller');

class AdminUsersController extends AdminAppController {
	public $uses = ['User'];

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
	public function emulate($uid=false) {
		// TODO
	}

	/**
	 * Create User 
	 *
	 * @url /adminuser/create
	 * @url /admin/user/create
	 */
	public function create() {
		// TODO
	}

	/**
	 * Edit User 
	 *
	 * @url /adminuser/edit/<uid>
	 * @url /admin/user/edit/<uid>
	 */
	public function edit($uid=false) {
		// TODO
	}

	/**
	 * Delete User 
	 *
	 * @url /adminuser/delete/<uid>
	 * @url /admin/user/delete/<uid>
	 */
	public function delete($uid=false) {
		// TODO
	}

	/**
	 * Toggle User Status 
	 *
	 * @url /adminuser/flip/<uid>
	 * @url /admin/user/flip/<uid>
	 */
	public function flip($uid=false) {
		// TODO
	}
}
