<?php
App::uses('AppModel', 'Model');

/**
 * Hint Model
 *
 */
class Hint extends AppModel {
    public $belongsTo = ['Inject'];
    public $recursive = 1;

    /**
     * All the hints (and which are unlocked)
     *
     * @param $inject_id Inject ID you are getting hints for
     * @param $group_id The Group ID you are getting hints for
     * @return array An array containing the hints
     */
    public function getHints($inject_id, $group_id) {
        $data = $this->find('all', [
            'fields' => [
                'Hint.id', 'Hint.title', 'Hint.content', 'Hint.parent_id',
                'Hint.time_wait', 'Hint.cost', 'UsedHint.time'
            ],
            'joins' => [
                [
                    'table' => 'used_hints',
                    'alias' => 'UsedHint',
                    'type'  => 'left',
                    'conditions' => [
                        'UsedHint.hint_id = Hint.id',
                        'UsedHint.group_id' => $group_id
                    ]
                ]
            ],
            'conditions' => [
                'Hint.inject_id' => $inject_id,
            ]
        ]);

        // Unlocked hints lookup table
        $unlockedHints = [];
        foreach ($data as $d) {
            $unlockedHints[$d['Hint']['id']] = (!empty($d['UsedHint']['time']));
        }

        // Add some helper data like if it's unlocked,
        // or a dependency is met
        foreach ($data as &$d) {
            $dependency_met = ($d['Hint']['parent_id'] != null) ? $unlockedHints[$d['Hint']['parent_id']] : true;

            $d['Hint']['unlocked'] = $unlockedHints[$d['Hint']['id']];
            $d['Hint']['dependency_met'] = $dependency_met;
        }

        return $data;
    }
}
