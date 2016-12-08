<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', ['controller' => 'pages', 'action' => 'index']);

	App::uses('Folder', 'Utility');
	foreach ( Configure::read('ie.special_controllers') AS $route ) {
		$dirname = ROOT . DS . APP_DIR . DS . 'Controller' . DS . $route;
		$dir = new Folder($dirname);
		$rtLow = strtolower($route);
		$files = $dir->read(false, true);

		foreach ( $files[1] AS $file ) {
			if ( $file == $route.'AppController.php' ) continue;

			$controller = substr($file, 0, strpos($file, 'Controller'));
			$controllerRt  = strtolower(substr($controller, strlen($route)));

			Router::connect('/'.$rtLow.'/'.$controllerRt, ['controller' => $controller, 'action' => 'index']);
			Router::connect('/'.$rtLow.'/'.$controllerRt.'/:action/*', ['controller' => $controller]);
		}
	}

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
