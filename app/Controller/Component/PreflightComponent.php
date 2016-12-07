<?php
App::uses('Cache', 'Cache');
App::uses('Component', 'Controller');
App::uses('Security', 'Utility');

class PreflightComponent extends Component {
	/**
	 * Array of all the checks that should
	 * be ran.
	 */
	protected $checks = [
		'verifySecurityKeys', 'checkDatabaseConnection',
		'checkGroupMappings', 'checkInjectTypes',
	];

	/**
	 * PreflightComponent Initialize Hook
	 * 
	 * This will only be ran once per hour.
	 * It will run through the application, ensuring
	 * it has been configured properly.
	 */
	public function initialize(Controller $controller) {
		if ( env('DEBUG') == 0 && Cache::read('preflight_check') == true ) {
			return;
		}

		// Additional checks for ScoreEngine
		if ( (bool)env('FEATURE_SCOREENGINE') ) {
			$this->checks[] = 'checkScoringDB';
			$this->checks[] = 'checkScoreEngine';
		}

		// Additional checks for BankWeb
		if ( (bool)env('FEATURE_BANKWEB') ) {
			$this->checks[] = 'checkBankWeb';
			$this->checks[] = 'checkBankWebTable';
		}

		foreach ( $this->checks AS $check ) {
			$passedOrErrorMessage = $this->$check();

			if ( $passedOrErrorMessage !== true ) {
				throw new InternalErrorException('Preflight Error: '.$passedOrErrorMessage);
			}
		}

		// If we got here, we can save and cache the result
		if ( env('DEBUG') == 0 ) {
			Cache::write('preflight_check', true);
		}
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
			return 'Please update the Security.salt config value';
		}
		if (empty(Configure::read('Security.cipherSeed')) OR Configure::read('Security.cipherSeed') === '76859309657453542496749683645') {
			return 'Please update the Security.cipherSeed config value.';
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
			return 'Unable to connect to the InjectEngine Database';
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
				return 'Please setup a group mapping for '.$group;
			}

			if ( empty($GroupModel->findById($gid))  ) {
				return sprintf('Invalid GID mapping for %s (GID: %d)', $group, $gid);
			}
		}

		return true;
	}

	/**
	 * Check Inject Types
	 *
	 * Verifies that all the inject types listed in 'engine.inject_types'
	 * exist and are callable.
	 */
	public function checkInjectTypes() {
		$ConfigModel = ClassRegistry::init('Config');
		$injectTypes = json_decode($ConfigModel->getKey('engine.inject_types'));

		if ( json_last_error() != JSON_ERROR_NONE ) {
			return sprintf('JSON Error decoding "engine.inject_types": %s', json_last_error_msg());
		}

		// Should this be a warning?
		if ( empty($injectTypes) ) {
			return 'No inject types are configured (See config key: engine.inject_types)';
		}

		foreach ( $injectTypes AS $type ) {
			$className = sprintf('InjectTypes\\%s', $type);

			if ( !class_exists($className) ) {
				return sprintf('Unknown inject type "%s" - does this file exist in "app/Vendor/InjectTypes"?', $type);
			}
		}

		return true;
	}

	/**
	 * Check ScoreEngine DB
	 *
	 * Verifies the ScoreEngine DB is setup correctly
	 */
	public function checkScoringDB() {
		App::uses('ConnectionManager', 'Model');

		// Check database connection for the InjectEngine
		$conn = ConnectionManager::getDataSource('scoreengine');
		if ( !$conn->isConnected() ) {
			return 'Unable to connect to the ScoreEngine Database';
		}

		return true;
	}

	/**
	 * Check ScoreEngine
	 *
	 * Verifies the ScoreEngine is setup correctly
	 */
	public function checkScoreEngine() {
		return true;
	}

	/**
	 * Check BankWeb
	 *
	 * Verifies some env variables are set, and that 'BANKWEB_PRODUCTS' exists
	 */
	public function checkBankWeb() {
		foreach ( ['BANKAPI_SERVER', 'BANKAPI_TIMEOUT', 'BANKWEB_PRODUCTS', 'BANKWEB_WHITETEAM_ACCOUNT', 'BANKWEB_PUBLIC_APIINFO'] AS $key ) {
			if ( empty(env($key)) ) {
				return sprintf('Please setup the variable "%s" to use the BankWeb Feature.', $key);
			}
		}

		$products = ROOT . DS . env('BANKWEB_PRODUCTS');
		if ( !file_exists($products) ) {
			return sprintf('Please make sure the file "%s" exists (as set in "BANKWEB_PRODUCTS")', $products);
		}

		$contents = json_decode(file_get_contents($products));
		if ( json_last_error() != JSON_ERROR_NONE ) {
			return sprintf('JSON Error with "BANKWEB_PRODUCTS" - %s', json_last_error_msg());
		}

		return true;
	}

	/**
	 * Check BankWeb Table
	 *
	 * That the BankWeb Table (account_mappings) exists
	 */
	public function checkBankWebTable() {
		$table = ClassRegistry::init('BankWeb.AccountMapping');

		try {
			$table->find('first');
		} catch ( Exception $e ) {
			return 'BankWeb plugin is not setup - please run `./cake engine install_bankweb`';
		}

		return true;
	}
}