<?php
App::uses('Cache', 'Cache');
App::uses('Component', 'Controller');
App::uses('Security', 'Utility');

class PreflightComponent extends Component {

    /**
     * Additional components needed
     */
    public $components = ['Slack'];

    /**
     * Array of all the checks that should
     * be ran.
     */
    protected $checks = [
        'verifyPHPVersion', 'verifySecurityKeys', 'checkDatabaseConnection',
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
        // Disable preflight on DEBUG
        if (env('DEBUG') == 2 || !file_exists(ROOT.'/.env')) {
            return;
        }

        // Calculate the hash of the config file. Don't run Preflight if it
        // hasn't changed
        $hash = md5(file_get_contents(ROOT.'/.env'));
        if (Cache::read('preflight_check') == $hash) {
            return;
        }

        // Disable preflight on the Admin plugin (otherwise, how would we fix issues)
        if ($controller->request->params['plugin'] == 'admin') {
            return;
        }

        // Additional checks for Mattermost
        if (env('CHATOPS_SERVICE') == 1) {
            $this->checks[] = 'checkMattermost';
        }

        // Additional checks for Slack
        if (env('CHATOPS_SERVICE') == 2) {
            $this->checks[] = 'checkSlack';
        }

        // Additional checks for ScoreEngine
        if (benv('FEATURE_SCOREENGINE')) {
            $this->checks[] = 'checkScoringDB';
            $this->checks[] = 'checkScoreEngine';
        }

        // Additional checks for BankWeb
        if (benv('FEATURE_BANKWEB')) {
            $this->checks[] = 'checkBankWeb';
            $this->checks[] = 'checkBankWebDB';
            $this->checks[] = 'checkBankWebBadCreds';
        }

        foreach ($this->checks as $check) {
            $passedOrErrorMessage = $this->$check();

            if ($passedOrErrorMessage !== true) {
                throw new InternalErrorException('Preflight Error: '.$passedOrErrorMessage);
            }
        }

        // If we got here, we can save and cache the result
        Cache::write('preflight_check', $hash);
    }

    /**
     * Verify PHP Version
     *
     * Ensures we are on PHP 7.0+
     * @return boolean If the check passed
     */
    public function verifyPHPVersion() {
        return PHP_VERSION_ID >= 70000 ? true : 'ie2 requires PHP 7.0+. You have '.PHP_VERSION;
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
        if (empty(Configure::read('Security.salt'))
            || Configure::read('Security.salt') === 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi'
        ) {
            return 'Please update the Security.salt config value';
        }

        if (empty(Configure::read('Security.cipherSeed'))
            || Configure::read('Security.cipherSeed') === '76859309657453542496749683645'
        ) {
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
        if (!$conn->isConnected()) {
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
        foreach (['GROUP_STAFF', 'GROUP_BLUE', 'GROUP_ADMINS', 'GROUP_WHITE'] as $group) {
            $gid = env($group);

            if (empty($gid)) {
                return 'Please setup a group mapping for '.$group;
            }

            if (empty($GroupModel->findById($gid))) {
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

        if (json_last_error() != JSON_ERROR_NONE) {
            return sprintf('JSON Error decoding "engine.inject_types": %s', json_last_error_msg());
        }

        // Should this be a warning?
        if (empty($injectTypes)) {
            return 'No inject types are configured (See config key: engine.inject_types)';
        }

        foreach ($injectTypes as $type) {
            $className = sprintf('InjectTypes\\%s', $type);

            if (!class_exists($className)) {
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
        if (!$conn->isConnected()) {
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
        $tables = ['Check', 'Round', 'Service', 'Team', 'TeamService'];
        $missing_tables = [];

        foreach ($tables as $table) {
            $tbl = ClassRegistry::init('ScoreEngine.'.$table);

            try {
                $tbl->find('first');
            } catch (Exception $e) {
                $missing_tables[] = $table;
            }
        }

        $missing = implode(', ', $missing_tables);
        return !empty($missing_tables) ? 'ScoreEngine is not setup. Missing DB table(s): '.$missing : true;
    }

    /**
     * Check BankWeb
     *
     * Verifies some env variables are set, and that 'BANKWEB_PRODUCTS' exists
     */
    public function checkBankWeb() {
        foreach ([
            'BANKAPI_SERVER',
            'BANKAPI_TIMEOUT',
            'BANKWEB_WHITETEAM_ACCOUNT',
            'BANKWEB_PUBLIC_APIINFO'
        ] as $key) {
            if (env($key) === null) {
                return sprintf('Please setup the variable "%s" to use the BankWeb Feature.', $key);
            }
        }

        return true;
    }

    /**
     * Check BankWeb DB
     *
     * That the BankWeb Tables exists
     */
    public function checkBankWebDB() {
        $tables = ['AccountMapping', 'Product', 'Purchase'];
        $missing_tables = [];

        foreach ($tables as $table) {
            $tbl = ClassRegistry::init('BankWeb.'.$table);

            try {
                $tbl->find('first');
            } catch (Exception $e) {
                $missing_tables[] = $table;
            }
        }

        $missing_tpl = 'BankWeb is not setup. Missing DB table(s): '.implode(', ', $missing_tables)
            .'. Please run `./cake engine install_bankweb`.';

        return !empty($missing_tables) ? $missing_tpl : true;
    }

    /**
     * Check BankWeb Bad Credentials
     *
     * Verifies we're not using insecure credentials
     */
    public function checkBankWebBadCreds() {
        $tbl = ClassRegistry::init('BankWeb.AccountMapping');
        $bad_creds = [
            'staff' => 'staff',
        ];
        $found_bad = [];

        foreach ($tbl->find('all') as $account) {
            if (isset($bad_creds[$account['AccountMapping']['username']])
                && $bad_creds[$account['AccountMapping']['username']] == $account['AccountMapping']['password']
            ) {
                $found_bad[] = 'BankWeb has detected insecure credentials for the group '.$account['Group']['name'].'.'.
                    ' Please change the password for bank user "'.$account['AccountMapping']['username'].'".';
            }
        }

        return !empty($found_bad) ? implode('\n', $found_bad) : true;
    }

    /**
     * Check Mattermost Configuration
     *
     * Verify that the mattermost configuration is correct
     */
    public function checkMattermost() {
        if (env('MATTERMOST_WEBHOOK_URL') === null) {
            return 'Please configure the "MATTERMOST_WEBHOOK_URL" variable.';
        }

        return true;
    }

    /**
     * Check Slack Configuration
     *
     * Verify that the slack configuration is correct
     */
    public function checkSlack() {
        $res = $this->Slack->test();

        return $res['ok'] ? true : 'Invalid slack API key: '.$res['error'];
    }
}
