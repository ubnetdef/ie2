<?php
App::uses('AdminAppController', 'Controller');

class AdminInjectsController extends AdminAppController {
	public $uses = ['User', 'Group'];

	/**
	 * Inject List Page 
	 *
	 * @url /admininjects
	 * @url /admin/injects
	 * @url /admininjects/index
	 * @url /admin/injects/index
	 */
	public function index() {
		// TODO
	}

	/**
	 * Create Inject 
	 *
	 * @url /admininjects/create
	 * @url /admin/injects/create
	 */
	public function create() {
		// TODO
	}

	/**
	 * Edit Inject 
	 *
	 * @url /admininjects/edit/<id>
	 * @url /admin/injects/edit/<id>
	 */
	public function edit($id=false) {
		// TODO
	}

	/**
	 * Delete Inject 
	 *
	 * @url /admininjects/delete/<id>
	 * @url /admin/injects/delete/<id>
	 */
	public function delete($uid=false) {
		// TODO
	}
}
