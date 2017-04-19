<?php
App::uses('Component', 'Controller');

class BankApiComponent extends Component {

    private $server;

    private $timeout;

    private $username = null;

    private $password = null;

    private $session = null;

    /**
     * BankAPI Initialize Hook
     *
     * Sets up a bunch of settings
     */
    public function initialize(Controller $controller) {
        $this->server = env('BANKAPI_SERVER');
        $this->timeout = env('BANKAPI_TIMEOUT');
    }

    /**
     * Set Credentials
     *
     * Saves the user/pass for any future
     * requests done by this component
     *
     * @param $user The username
     * @param $pass The password
     * @return void
     */
    public function setCredentials($user, $pass) {
        $this->username = $user;
        $this->password = $pass;
    }

    /**
     * Login
     *
     * Attempts a login request
     *
     * @return string/bool True if it worked. A string with
     * the message if it doesn't.
     */
    public function login() {
        $result = $this->request('login', ['username' => $this->username, 'password' => $this->password]);

        if ($result['code'] != 200) {
            throw new InternalErrorException('Bank Error: '.$result['message']);
        }

        $this->session = $result['session'];
        return true;
    }

    /**
     * Transfer Money
     *
     * @param $src The source account
     * @param $dst The dest account
     * @param $amt The amount to transfer
     * @param $pin The source account pin
     * @return bool/string True if it worked. A string
     * with the message if it doesn't.
     */
    public function transfer($src, $dst, $amt, $pin) {
        if ($this->session == null) {
            $this->login();
        }

        $result = $this->request('transfer', [
            'session' => $this->session,
            'src'     => $src,
            'dst'     => $dst,
            'amount'  => $amt,
            'pin'     => $pin,
        ]);

        if ($result['code'] != 200) {
            throw new InternalErrorException('Bank Error: '.$result['message']);
        }
        return true;
    }

    /**
     * Get All Transfers
     *
     * @param $account The account you wish to view
     * logs for.
     * @return array/string Array of logs if it worked. A string
     * with the message if it doesn't.
     */
    public function transfers($account) {
        if ($this->session == null) {
            $this->login();
        }

        $result = $this->request('transfers', [
            'session' => $this->session,
            'account' => $account,
        ]);

        if ($result['code'] != 200) {
            throw new InternalErrorException('Bank Error: '.$result['message']);
        }
        return $result['transactions'];
    }

    /**
     * Change PIN
     *
     * @param $account The account you wish to change the pin for
     * @param $oldPin The old pin
     * @param $newPin The new pin
     * @return bool/string True if it worked. A string
     * with the message if it doesn't.
     */
    public function changePin($account, $oldPin, $newPin) {
        if ($this->session == null) {
            $this->login();
        }

        $result = $this->request('changePin', [
            'session' => $this->session,
            'account' => $account,
            'pin'     => $oldPin,
            'newpin'  => $newPin,
        ]);

        if ($result['code'] != 200) {
            throw new InternalErrorException('Bank Error: '.$result['message']);
        }
        return true;
    }

    /**
     * Account List
     *
     * @return array/string Array of accounts if it worked. A string
     * with the message if it doesn't.
     */
    public function accounts() {
        if ($this->session == null) {
            $this->login();
        }

        $result = $this->request('accounts', [
            'session' => $this->session,
        ]);

        if ($result['code'] != 200) {
            throw new InternalErrorException('Bank Error: '.$result['message']);
        }
        return $result['accounts'];
    }

    /**
     * Create Account
     *
     * @param $pin The pin number
     * @return bool/string True if it worked. A string
     * with the message if it doesn't.
     */
    public function newAccount($pin) {
        if ($this->session == null) {
            $this->login();
        }

        $result = $this->request('newAccount', [
            'session' => $this->session,
            'pin'     => $pin,
        ]);

        if ($result['code'] != 200) {
            throw new InternalErrorException('Bank Error: '.$result['message']);
        }
        return true;
    }


    /**
     * API Request
     *
     * General function to make an API request
     * to the BankAPI
     *
     * @param $endpoint The endpoint you wish to dall
     * @param $data An array of post data
     * @return An array with a data
     */
    public function request($endpoint, $data) {
        $postData = [];
        foreach ($data as $k => $v) {
            $postData[] = sprintf('%s=%s', urlencode($k), urlencode($v));
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, sprintf('%s/%s', $this->server, $endpoint));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set the UA to the app version
        list($version_short, $version_long) = Cache::read('version');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ie2/BankWeb @ '.$version_short);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        $result = curl_exec($ch);

        curl_close($ch);

        if ($result === false) {
            throw new RuntimeException('Failed to contact the BankAPI Server. Please try again later.');
        }

        return json_decode($result, true);
    }
}
