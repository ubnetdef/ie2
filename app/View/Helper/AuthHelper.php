<?php
App::uses('AppHelper', 'View/Helper');

class AuthHelper extends AppHelper {

	/**
	 * Magic bridge to the AuthComponent
	 *
	 * If a method being called does not exist, but
	 * it exists in AuthComponent, this will 'proxy'
	 * the method call to it.
	 *
	 * @param $name The method name being called
	 * @param $args An array of arguments
	 * @return mixed
	 */
	public function __call($name, $args) {
		if ( method_exists($this->settings['auth'], $name) ) {
			return call_user_func_array([$this->settings['auth'], $name], $args);
		}
	}
}