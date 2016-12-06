<?php
App::uses('BankWebAppModel', 'BankWeb.Model');

class AccountMapping extends BankWebAppModel {
	const TBL_USER = 'User';
	const TBL_GROUP = 'Group';

	public function getAccount($user, $groups) {
		// First, let's try to check if we have a user mapping
		$mapping = $this->find('first', [
			'conditions' => [
				'AccountMapping.object'    => self::TBL_USER,
				'AccountMapping.object_id' => $user,
			],
		]);

		if ( !empty($mapping) ) {
			return $mapping;
		}

		// Now check groups
		$mapping = $this->find('all', [
			'conditions' => [
				'AccountMapping.object'    => self::TBL_GROUP,
				'AccountMapping.object_id' => $groups,
			],
		]);

		// Re-organize the mappings
		$maps = [];
		foreach ( $mapping AS $m ) {
			$maps[$m['AccountMapping']['object_id']] = $m;
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