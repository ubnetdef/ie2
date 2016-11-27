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
	 * Gets an array of the groups
	 *
	 * @param $id The ID of the group you are getting groups for
	 * @return array An array containing the group, and all higher groups
	 */
	public function getGroups($id) {
		$groups = [];
		foreach ( $this->getPath($id) AS $p ) {
			$groups[] = $p['Group']['id'];
		}

		return $groups;
	}

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
			$path[] = $p['Group']['name'];
		}

		return implode($separator, $path);
	}
}
