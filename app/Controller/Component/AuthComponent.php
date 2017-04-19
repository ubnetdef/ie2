<?php
App::uses('Component', 'Controller');
App::uses('Security', 'Utility');

class AuthComponent extends Component {

    public $components = ['Cookie', 'Session'];

    public $uses = ['User'];

    protected $controller;

    /**
     * String to prefix all our
     * session keys with.
     */
    const SESSION_PREFIX = 'auth';

    /**
     * String to store our
     * redirect URL.
     */
    const SESSION_REDIRECT = 'auth_redirect';

    /**
     * AuthComponent Initialize Hook
     *
     * Auto logs out the user if their account has
     * expired.  Also updates their last activity session time.
     */
    public function initialize(Controller $controller) {
        // If we're logged in, make sure we haven't expired
        if ($this->loggedIn() && $this->isExpired($this->user('expiration'))) {
            $this->logout();
        }

        if ($this->loggedIn()) {
            $this->Session->write(self::SESSION_PREFIX.'.last_activity', time());
        }

        $this->controller = $controller;
    }

    /**
     * Checks if a timestamp has passed ('expired')
     *
     */
    private function isExpired($expiration) {
        return ($expiration > 0) ? (time() > $expiration) : false;
    }

    /**
     * Login
     *
     * @param $username The username of the account
     * @param $password The password of the account
     * @return boolean If the login was successful
     */
    public function login($username, $password) {
        $UserModel = ClassRegistry::init('User');
        $GroupModel = ClassRegistry::init('Group');
        $user = $UserModel->findByUsername($username);

        // Does the user exist?
        if (empty($user)) { return false;
        }

        // Do we have the right password?
        $actual_password = $user['User']['password'];
        if (Security::hash($password, 'blowfish', $actual_password) !== $actual_password) { return false;
        }

        // Is the user active?
        if ($user['User']['active'] == 0) { return false;
        }

        // Is the user expired?
        if ($this->isExpired($user['User']['expiration'])) { return false;
        }

        // Save the data
        unset($user['User']['password']);
        $this->Session->write(self::SESSION_PREFIX, $user);
        $this->Session->write(self::SESSION_PREFIX.'.last_activity', time());
        $this->Session->write(self::SESSION_PREFIX.'.groups', $GroupModel->getGroups($user['Group']['id']));

        return true;
    }

    /**
     * Logout
     *
     * @return void
     */
    public function logout() {
        $this->Session->destroy();
    }

    /**
     * Protects a page
     *
     * If the user is not logged in, they will be
     * redirected to /user/login.
     *
     * In addition, we can protect a page by
     * requiring a certain group id
     *
     * @param $required_group The group ID the current
     * user must be apart of, in order to view the page
     * @return mixed
     */
    public function protect($required_group = false) {
        if (!$this->loggedIn()) {
            $this->Session->write(self::SESSION_REDIRECT, $this->controller->request->here);

            return $this->controller->redirect('/user/login');
        }

        if ($required_group !== false) {
            if (!in_array($required_group, $this->item('groups'))) {
                throw new ForbiddenException('You are unauthorized to view this page');
            }
        }

        return true;
    }

    /**
     * Redirect URL
     *
     * Get's the redirect url after a user has logged
     * in.
     *
     * @return string
     */
    public function redirectURL() {
        $url = $this->Session->consume(self::SESSION_REDIRECT);

        return (empty($url) ? '/' : $url);
    }

    /**
     * Emulates a user account
     *
     * Emulation will change the current session's user to the emulated user
     * account. The current session will be saved, however.
     *
     * @param $uidOrUsername The username or id of the account you are emulating
     */
    public function emulate($uidOrUsername) {
        $UserModel = ClassRegistry::init('User');
        $GroupModel = ClassRegistry::init('Group');
        $user = $UserModel->find('first', [
            'conditions' => [
                'OR' => [
                    'User.username' => $uidOrUsername,
                    'User.id' => $uidOrUsername,
                ],
            ]
        ]);

        if (empty($user)) {
            throw new InternalErrorException('Unknown username!');
        }

        $oldSession = $this->Session->consume(self::SESSION_PREFIX);
        $this->Session->write(self::SESSION_PREFIX, $user);
        $this->Session->write([
            self::SESSION_PREFIX.'.last_activity' => time(),
            self::SESSION_PREFIX.'.oldSession'    => $oldSession,
            self::SESSION_PREFIX.'.emulating'     => true,
            self::SESSION_PREFIX.'.groups'        => $GroupModel->getGroups($user['Group']['id']),
        ]);
    }

    /**
     * Ends an emulation session
     *
     * Kills the current session, and restores the previously saved session.
     */
    public function emulateExit() {
        if ($this->item('emulating') != true) {
            throw new InternalErrorException('A valid emulation session is not present!');
        }

        $newSession = $this->Session->consume(self::SESSION_PREFIX.'.oldSession');
        $this->Session->destroy();

        $this->Session->write(self::SESSION_PREFIX, $newSession);
        $this->Session->write(self::SESSION_PREFIX.'.last_activity', time());
    }

    /**
     * Logged In
     *
     * @return boolean If the user is logged in
     */
    public function loggedIn() {
        return ($this->Session->check(self::SESSION_PREFIX));
    }

    /**
     * Is Staff
     *
     * @return boolean If the user is a staff member
     */
    public function isStaff() {
        return $this->is(env('GROUP_STAFF'));
    }

    /**
     * Is Administrator
     *
     * @return boolean If the user is an administrator
     */
    public function isAdmin() {
        return $this->is(env('GROUP_ADMINS'));
    }

    /**
     * Is White Team
     *
     * @return boolean If the user is a White Team member
     */
    public function isWhiteTeam() {
        return $this->is(env('GROUP_WHITE'));
    }

    /**
     * Is Blue Team
     *
     * @return boolean If the user is a Blue Team Member
     */
    public function isBlueTeam() {
        return $this->is(env('GROUP_BLUE'));
    }

    /**
     * Is Helper
     *
     * @param $group The group id
     * @return boolean If the user is in the group
     */
    public function is($group) {
        return ($this->loggedIn() ? in_array($group, $this->item('groups')) : false);
    }

    /**
     * User Information Accessor
     *
     * @param string The key of the item you wish to access (optional)
     * @return mixed The value of the item you are accessing
     */
    public function user($item = '') {
        $key = 'User'.(empty($item) ? '' : '.'.$item);
        return $this->item($key);
    }

    /**
     * Group Information Accessor
     *
     * @param string The key of the item you wish to access (optional)
     * @return mixed The value of the item you are accessing
     */
    public function group($item = '') {
        $key = 'Group'.(empty($item) ? '' : '.'.$item);
        return $this->item($key);
    }

    /**
     * Item Accessor
     *
     * @param string The key of the item you wish to access (optional)
     * @return mixed The value of the item you are accessing
     */
    public function item($item = false) {
        $key = ($item === false ? self::SESSION_PREFIX : implode('.', [self::SESSION_PREFIX, $item]));

        return ($this->loggedIn() ? $this->Session->read($key) : '');
    }
}
