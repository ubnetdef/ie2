<?php
App::uses('AdminAppController', 'Controller');

class AdminGroupsController extends AdminAppController {
	public $uses = ['Group'];

	/**
	 * Group List Page 
	 *
	 * @url /admingroup
	 * @url /admin/group
	 * @url /admingroup/index
	 * @url /admin/group/index
	 */
	public function index() {
		// TODO
	}

	/**
	 * Create Group 
	 *
	 * @url /admingroup/create
	 * @url /admin/group/create
	 */
	public function create() {
		// TODO
	}

	/**
	 * Edit Group 
	 *
	 * @url /admingroup/edit/<uid>
	 * @url /admin/group/edit/<uid>
	 */
	public function edit($uid=false) {
		// TODO
	}

	/**
	 * Delete group 
	 *
	 * @url /admingroup/delete/<uid>
	 * @url /admin/group/delete/<uid>
	 */
	public function delete($uid=false) {
		// TODO
	}
}
