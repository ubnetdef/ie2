<?php
App::uses('AppController', 'Controller');

class DashboardWidgetController extends AppController {

    public $uses = [
        'Config', 'Inject', 'UsedHint', 'Hint', 'Log',
        'Grade', 'Group', 'Schedule', 'Submission',
    ];

    public function beforeFilter() {
        parent::beforeFilter();

        // Enforce staff only
        $this->Auth->protect(env('GROUP_STAFF'));

        // Load+Setup ScoreEngine EngineOutputter, if ScoreEngine is enabled
        if (benv('FEATURE_SCOREENGINE')) {
            $this->helpers[] = 'ScoreEngine.EngineOutputter';
            $this->uses = array_merge($this->uses, ['ScoreEngine.Check', 'ScoreEngine.Team', 'ScoreEngine.Service']);

            $this->helpers['ScoreEngine.EngineOutputter']['data'] = $this->Check->getChecksTable(
                $this->Team->findAllByEnabled(true),
                $this->Service->findAllByEnabled(true)
            );
        }

        // We're at the staff page
        $this->set('at_staff', true);
    }

    public function index() {
        return $this->redirect('/');
    }

    public function scoreoverview() {
        if (!benv('FEATURE_SCOREENGINE')) {
            throw new BadRequestException('Feature disabled.');
        }
    }

    public function scoreboard() {
        if (!benv('FEATURE_SCOREENGINE')) {
            throw new BadRequestException('Feature disabled.');
        }
    }

    public function bank() {
        if (!benv('FEATURE_BANKWEB')) {
            throw new BadRequestException('Feature disabled.');
        }
    }

    public function injects($filter=false) {
        // todo
    }

    public function log() {
        // todo
    }
}
