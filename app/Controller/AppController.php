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

	public $uses = ['Announcement', 'Config', 'Log'];
	public $helpers = ['Auth', 'Misc', 'Session'];

	/**
	 * Before Filter Hook
	 * 
	 * Hook ran before ANY request. Currently
	 * sets some template variables depending on user state.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		// Setup the read announcements
		if ( !$this->Session->check('read_announcements') ) {
			$this->Session->write('read_announcements', []);
		}

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

	/**
	 * Ajax Response
	 *
	 * @param $data The output
	 * @param $status The HTTP status code
	 * @return CakeResponse
	 */
	protected function ajaxResponse($data, $status=200) {
		$this->layout = 'ajax';

		return new CakeResponse([
			'type' => 'json',
			'body'   => (is_array($data) ? json_encode($data) : $data),
			'status' => $status,
		]);
	}

	/**
	 * Log Message
	 *
	 * @param $type The log type
	 * @param $message The message
	 * @param $data [Optional] Extra data to log
	 * @param $related [Optional] Related ID of this message
	 * @return void
	 */
	public function logMessage($type, $message, $data=[], $related=0) {
		$this->Log->create();
		$this->Log->save([
			'time'       => time(),
			'type'       => $type,
			'user_id'    => ($this->Auth->loggedIn() ? $this->Auth->user('id') : NULL),
			'related_id' => ($related > 0 ? $related : NULL),
			'data'       => json_encode($data),
			'ip'         => $this->request->clientIp(),
			'message'    => $message,
		]);
	}
}
