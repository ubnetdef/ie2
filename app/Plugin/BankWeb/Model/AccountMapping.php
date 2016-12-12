<?php
App::uses('BankWebAppModel', 'BankWeb.Model');

class AccountMapping extends BankWebAppModel {
	public $belongsTo = ['Group'];
	public $recursive = 1;

	public function getAccount($groups) {
		// Check groups
		$mapping = $this->find('all', [
			'conditions' => [
				'AccountMapping.group_id' => $groups,
			],
		]);

		// Re-organize the mappings
		$maps = [];
		foreach ( $mapping AS $m ) {
			$maps[$m['AccountMapping']['group_id']] = $m;
		}

		// Now let's deal with the groups
		if ( !empty($maps) ) {
			// We're going to assume (I'm sorry), that the last
			// items in $groups is the closest thing being used
			$wanted = array_reverse($groups);

			foreach ( $wanted AS $id ) {
				if ( isset($maps[$id]) ) {
					return $maps[$id];
				}
			}
		}

		return [];
	}
}