<?php
App::uses('AppModel', 'Model');

/**
 * Group Model
 *
 */
class Group extends AppModel {
	public $actsAs = ['Tree'];
	public $hasMany = ['User'];

	/**
	 * Gets the 'pretty' version of the group
	 * Example: Staff/White Team
	 *
	 * @param $id The ID of the group you are getting the path for
	 * @param $separator The separator you wish to use. Defaults to "/" 
	 * @return string The pretty path
	 */
	public function getGroupPath($id, $separator='/') {
		$path = [];

		foreach ( $this->getPath($id) AS $p ) {
			$path[] = $p[$this->alias]['name'];
		}

		return implode($separator, $path);
	}
}
