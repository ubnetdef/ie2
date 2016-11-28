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

	public $uses = ['Announcement', 'Config'];
	public $helpers = ['Auth', 'Misc'];

	/**
	 * Before Filter Hook
	 * 
	 * Hook ran before ANY request. Currently
	 * sets some template variables depending on user state.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		// Setup a constant for the competition start
		if ( !defined('COMPETITION_START') ) {
			define('COMPETITION_START', $this->Config->getKey('competition.start'));
		}

		$this->set('announcements', $this->Announcement->getAll());
		$this->set('emulating', ($this->Auth->item('emulating') == true));
	}

	/**
	 * Before Render Hook
	 *
	 * Basically sets up the AuthHelper (which is a proxy)
	 */
	public function beforeRender() {
		parent::beforeRender();

		$this->helpers['Auth'] = [
			'auth' => $this->Auth,
		];
	}

	/**
	 * After Filter Hook
	 *
	 * Compress all the html!
	 */
	public function afterFilter() {
		parent::afterFilter();

		if ( env('DEBUG') == 0 ) {
			$parser = \WyriHaximus\HtmlCompress\Factory::construct();
			$compressedHtml = $parser->compress($this->response->body());

			$this->response->compress();
			$this->response->body($compressedHtml);
		}
	}
}
