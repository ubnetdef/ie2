<?php
App::uses('Controller', 'Controller');

class AppController extends Controller {
	public $components = [
		'Auth',
		'Flash' => [
			'className' => 'BootstrapFlash',
		],
		'RequestHandler',
		'Session',
		'Paginator' => [
			'settings' => [
				'paramType' => 'querystring',
				'limit' => 30
			]
		],
		'Preflight',
	];

	public function beforeFilter() {
		parent::beforeFilter();

		if ( $this->Auth->loggedIn() ) {
			$this->set('userinfo', $this->Auth->user());
		}

		$this->set('emulating', ($this->Auth->item('emulating') == true));
	}
}
