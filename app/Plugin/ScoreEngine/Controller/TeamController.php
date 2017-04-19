<?php
App::uses('ScoreEngineAppController', 'ScoreEngine.Controller');

class TeamController extends ScoreEngineAppController {
    public $uses = ['ScoreEngine.Check', 'ScoreEngine.TeamService'];

    /**
     * The current user's team number
     */
    private $team;

    /**
     * IP Regex
     *
     * Source: http://stackoverflow.com/a/20270082
     */
    const IP_REGEX = "/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/";

    /**
     * Before Filter
     *
     * Locks the page to blue teams' group,
     * as well as sets up the user's team number
     */
    public function beforeFilter() {
        parent::beforeFilter();

        // Only blue teams may access
        $this->Auth->protect(env('GROUP_BLUE'));

        // Set the team number
        $this->team = $this->Auth->group('team_number');
    }

    /**
     * Team Overview Page
     *
     * @url /team
     * @url /score_engine/team
     * @url /score_engine/team/index
     */
    public function index() {
        $this->set('data', $this->Check->getTeamChecks($this->team));
        $this->set('latest', $this->Check->getLastTeamCheck($this->team));
    }

    /**
     * Service Overview Page
     *
     * @url /team/service/<sid>
     * @url /score_engine/team/service/<sid>
     */
    public function service($sid = false) {
        if ($sid === false || !is_numeric($sid)) {
            throw new NotFoundException('Unknown service');
        }

        $this->Check->virtualFields = [];
        $this->set('data', $this->Check->find('all', [
            'conditions' => [
                'team_id' => $this->team,
                'service_id' => $sid,
                'Service.enabled' => true,
            ],
            'limit' => 20,
            'order' => 'time DESC',
        ]));
    }

    /**
     * Config Edit Page
     *
     * @url /team/edit
     * @url /score_engine/team/edit
     */
    public function config() {
        $data = $this->TeamService->getData($this->team);

        $canEdit = function ($id) use ($data) {
            foreach ($data as $group => $options) {
                foreach ($options as $opt) {
                    if ($opt['id'] == $id) {
                        return $opt['edit'];
                    }
                }
            }

            return false;
        };

        $getOpt = function ($id) use (&$data) {
            foreach ($data as $group => &$options) {
                foreach ($options as $opt) {
                    if ($opt['id'] == $id) {
                        return $opt['value'];
                    }
                }
            }

            return false;
        };

        $updateOpt = function ($id, $value) use (&$data) {
            foreach ($data as $group => &$options) {
                foreach ($options as &$opt) {
                    if ($opt['id'] == $id) {
                        $opt['value'] = $value;
                    }
                }
            }

            return false;
        };

        if ($this->request->is('post')) {
            foreach ($this->request->data as $opt => $value) {
                $opt = (int) str_replace('opt', '', $opt);
                if ($opt < 0 || !is_numeric($opt)) { continue;
                }
                if (!$canEdit($opt)) { continue;
                }

                // Only USERPASS is an array
                if (is_array($value)) {
                    $value = $value['user'].'||'.$value['pass'];
                }

                // Do some hacky magic with IPs to check subnets
                $oldVal = $getOpt($opt);

                if (preg_match(self::IP_REGEX, $oldVal, $match)) {
                    $newSubnet = explode('.', $value);
                    array_pop($newSubnet);
                    $newSubnet = implode('.', $newSubnet);

                    $oldSubnet = explode('.', $oldVal);
                    array_pop($oldSubnet);
                    $oldSubnet = implode('.', $oldSubnet);

                    if ($newSubnet != $oldSubnet) {
                        continue;
                    }
                }

                $this->TeamService->updateConfig($opt, $value);
                $updateOpt($opt, $value);
            }

            // Message
            $this->Flash->success('Updated Score Engine Config!');
        }

        $this->set('data', $data);
    }
}
