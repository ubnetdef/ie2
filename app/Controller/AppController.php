<?php
App::uses('Controller', 'Controller');
use Respect\Validation\Exceptions\NestedValidationException;

class AppController extends Controller {
    public $components = [
        'Auth',
        'Flash' => [
            'className' => 'BootstrapFlash',
        ],
        'RequestHandler',
        'Session',
        'Paginator' => [
            'settings' => [
                'limit' => 20,
                'paramType' => 'querystring',
            ]
        ],
        'Preflight',
    ];

    public $uses = ['Announcement', 'Config', 'Log'];
    public $helpers = ['Auth', 'Misc', 'Session'];

    protected $validators = [];

    /**
     * Before Filter Hook
     *
     * Hook ran before ANY request. Currently
     * sets some template variables depending on user state.
     */
    public function beforeFilter() {
        parent::beforeFilter();

        // Setup the read announcements
        if (!$this->Session->check('read_announcements')) {
            $this->Session->write('read_announcements', []);
        }

        // Setup a constant for the competition start
        if (!defined('COMPETITION_START')) {
            define('COMPETITION_START', $this->Config->getKey('competition.start'));
        }

        // Git version (because it looks cool)
        if (Cache::read('version') === false) {
            exec('git status', $unused, $status);

            if ($status === 0) {
                exec('git rev-parse --short HEAD', $version_short);
                exec('git rev-parse HEAD', $version_long);

                $version_short = trim($version_short[0]);
                $version_long = trim($version_long[0]);
            } else {
                $version_short = 'DEV';
                $version_long = 'development';
            }

            Cache::write('version', [$version_short, $version_long]);
        } else {
            list($version_short, $version_long) = Cache::read('version');
        }

        $this->set('version', $version_short);
        $this->set('version_long', $version_long);

        // Other template stuff
        $this->set('announcements', $this->Announcement->getAll());
        $this->set('emulating', ($this->Auth->item('emulating') == true));
    }

    /**
     * Before Render Hook
     *
     * Basically sets up the AuthHelper (which is a proxy)
     */
    public function beforeRender() {
        parent::beforeRender();

        $this->helpers['Auth'] = [
            'auth' => $this->Auth,
        ];
    }

    /**
     * After Filter Hook
     *
     * Compress all the html!
     */
    public function afterFilter() {
        parent::afterFilter();

        if (env('DEBUG') == 0) {
            $parser = \WyriHaximus\HtmlCompress\Factory::construct();
            $compressedHtml = $parser->compress($this->response->body());

            $this->response->body($compressedHtml);
        }
    }

    /**
     * Ajax Response
     *
     * @param $data The output
     * @param $status The HTTP status code
     * @return CakeResponse
     */
    protected function ajaxResponse($data, $status = 200) {
        $this->layout = 'ajax';

        return new CakeResponse([
            'type' => 'json',
            'body'   => (is_array($data) ? json_encode($data) : $data),
            'status' => $status,
        ]);
    }

    /**
     * Log Message
     *
     * @param $type The log type
     * @param $message The message
     * @param $data [Optional] Extra data to log
     * @param $related [Optional] Related ID of this message
     * @param $who [Optional] Who did this action
     * @return void
     */
    public function logMessage($type, $message, $data = [], $related = 0, $who = false) {
        if ($who === false) {
            $who = ($this->Auth->loggedIn() ? $this->Auth->user('id') : null);
        }

        $safe = !((bool)env('X_FORWARDED_ENABLED') == true);

        $this->Log->create();
        $this->Log->save([
            'time'       => time(),
            'type'       => $type,
            'user_id'    => $who,
            'related_id' => ($related > 0 ? $related : null),
            'data'       => json_encode($data),
            'ip'         => $this->request->clientIp($safe),
            'message'    => $message,
        ]);
    }

    /**
     * Validate request data against
     * $validators
     *
     * @return array [errors,validated_data]
     */
    protected function _validate() {
        // Validate the input
        $errors = [];
        $modify = [];

        foreach ($this->validators as $key => $validator) {
            // If we're missing something, stop it's bad.
            if (!isset($this->request->data[$key])) {
                $errors[] = sprintf('Missing input "%s"', $key);
                continue;
            }

            try {
                $validator->assert($this->request->data[$key]);

                $data[$key] = $this->request->data[$key];
            } catch (NestedValidationException $e) {
                $errors[] = sprintf(
                    'Input %s must have pass the following rules:<br />-%s',
                    $key,
                    implode('<br />-', $e->getMessages())
                );
            }
        }

        return [
            'errors' => $errors,
            'data'   => $data,
        ];
    }

    /**
     * Generates a flash message for a failed
     * validation
     *
     * @param $errors Array of the errors
     * @return void
     */
    protected function _errorFlash($errors) {
        $this->Flash->danger('The following errors have occured:<br />'.implode('<br />', $errors));
    }

    /**
     * Sends a slack message
     *
     */
    protected function _sendSlack($msg, $extra = []) {
        if (!env('SLACK_ENDPOINT')) { return;
        }

        // Sprintf it
        $msg = sprintf($msg, $this->Auth->user('username'), $this->Auth->group('name'));

        // Build the payload
        $payload = 'payload='.json_encode([
            'text'       => $msg,
            'link_names' => 1,
        ] + $extra);

        $ch = curl_init(env('SLACK_ENDPOINT'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}
