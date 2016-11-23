<?php
App::uses('Controller', 'Controller');

class AppController extends Controller {
	public $components = [
		'RequestHandler',
		'Session',
		'Paginator' => ['settings' => ['paramType' => 'querystring', 'limit' => 30]]
	];
}
