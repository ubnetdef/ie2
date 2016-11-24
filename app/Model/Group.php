<?php
App::uses('AppModel', 'Model');

/**
 * Group Model
 *
 */
class Group extends AppModel {
	public $actsAs = ['Tree'];
	public $hasMany = ['User'];

	public function getACLPath($id, $separator='/') {
		$path = [];

		foreach ( $this->getPath($id) AS $p ) {
			$path[] = $p[$this->alias]['name'];
		}

		return implode($separator, $path);
	}
}
