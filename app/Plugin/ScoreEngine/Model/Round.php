<?php
App::uses('ScoreEngineAppModel', 'ScoreEngine.Model');

class Round extends ScoreEngineAppModel {
    public function getLastRound() {
        $round = $this->find('first', [
            'fields' => [
                'MAX(Round.number) AS round'
            ],
            'conditions' => [
                'Round.completed' => true,
            ],
        ]);

        return empty($round[0]['round']) ? 0 : $round[0]['round'];
    }
}
