<?php
App::uses('Cache', 'Cache');
App::uses('Component', 'Controller');
App::uses('Security', 'Utility');

class PreflightComponent extends Component {
	/**
	 * Saved error message from a check
	 */
	protected $errorMessage = null;

	/**
	 * Array of all the checks that should
	 * be ran.
	 */
	protected $checks = [
		'verifySecurityKeys', 'checkDatabaseConnection',
		'checkGroupMappings',
	];

	/**
	 * PreflightComponent Initialize Hook
	 * 
	 * This will only be ran once per hour.
	 * It will run through the application, ensuring
	 * it has been configured properly.
	 */
	public function initialize(Controller $controller) {
		if ( Cache::read('preflight_check') == true ) return;

		foreach ( $this->checks AS $check ) {
			$passed = $this->$check();

			if ( !$passed ) {
				throw new RuntimeException('Preflight Error: '.$this->errorMessage);
			}
		}

		// If we got here, we can save and cache the result
		Cache::write('preflight_check', true);
	}

	/**
	 * Verify Security Keys
	 * 
	 * Ensures 'Security.salt' and 'Security.cipherSeed' are both non-empty and
	 * non-default values.
	 * @return boolean If the check passed
	 */
	public function verifySecurityKeys() {
		// Pls no defaults
		if (empty(Configure::read('Security.salt')) OR Configure::read('Security.salt') === 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi') {
			$this->errorMessage = 'Please update the Security.salt config value';
			return false;
		}
		if (empty(Configure::read('Security.cipherSeed')) OR Configure::read('Security.cipherSeed') === '76859309657453542496749683645') {
			$this->errorMessage = 'Please update the Security.cipherSeed config value.';
			return false;
		}

		return true;
	}

	/**
	 * Check Database Connection
	 * 
	 * Ensures the database connection to the InjectEngine
	 * is working
	 * @return boolean If the check passed
	 */
	public function checkDatabaseConnection() {
		App::uses('ConnectionManager', 'Model');

		// Check database connection for the InjectEngine
		$conn = ConnectionManager::getDataSource('default');
		if ( !$conn->isConnected() ) {
			$this->errorMessage = 'Unable to connect to the InjectEngine Database';
			return false;
		}

		return true;
	}

	/**
	 * Check Group Mappings
	 * 
	 * Ensures 'GROUP_STAFF', 'GROUP_BLUE', 'GROUP_ADMINS', and 'GROUP_WHITE'
	 * have valid group mappings.
	 * @return boolean If the check passed
	 */
	public function checkGroupMappings() {
		$GroupModel = ClassRegistry::init('Group');

		// Check the group mappings
		foreach ( ['GROUP_STAFF', 'GROUP_BLUE', 'GROUP_ADMINS', 'GROUP_WHITE'] AS $group ) {
			$gid = env($group);

			if ( empty($gid) ) {
				$this->errorMessage = 'Please setup a group mapping for '.$group;
				return false;
			}

			if ( empty($GroupModel->findById($gid))  ) {
				$this->errorMessage = sprintf('Invalid GID mapping for %s (GID: %d)', $group, $gid);
				return false;
			}
		}

		return true;
	}
}