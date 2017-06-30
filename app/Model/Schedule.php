<?php
App::uses('AppModel', 'Model');
App::uses('InjectAbstraction', 'Lib');

/**
 * Schedule Model
 *
 */
class Schedule extends AppModel {

    public $belongsTo = ['Inject'];

    public $recursive = 2;

    /**
     * Get Active Injects (RAW)
     *
     * Okay, this is the monster in the room.
     * I'm sorry. It'll grab injects based on
     * if they're active, AND their start time
     * has passed
     *
     * @param $groups The groups to check for injects in
     * @param $onlyActive Only include injects that are active
     * @return array All the active injects
     */
    public function getInjectsRaw($groups, $onlyActive = true) {
        $now = time();
        $conditions = ['Schedule.group_id' => $groups];

        if ($onlyActive) {
            $conditions += [
                'Schedule.active'   => true,
                'OR' => [
                    [
                        'Schedule.fuzzy' => false,
                        'Schedule.start <=' => $now,
                    ],
                    [
                        'Schedule.fuzzy' => true,
                        'Schedule.start <=' => ($now - COMPETITION_START)
                    ],
                    [
                        'Schedule.start' => 0,
                    ],
                ],
            ];
        }

        $injects = $this->find('all', [
            'conditions' => $conditions,

            // Ordering is hard. Sorry.
            // We'll do base ordering on the order.
            // Following that, sequence number,
            // and then end times that aren't
            // forever.
            'order' => [
                'Schedule.order ASC',
                'Inject.sequence ASC',
                '(Schedule.end > 0) DESC',
                'Schedule.end ASC',
            ],
        ]);

        // Now remove any duplicates
        $cache = [];
        foreach ($injects as $k => $v) {
            $injectID = $v['Inject']['id'];
            $newEnd   = $v['Schedule']['end'];

            if (!isset($cache[$injectID])) {
                $cache[$injectID] = [
                    'end' => $newEnd,
                    'key' => $k,
                ];

                continue;
            }

            $oldEnd = $cache[$injectID]['end'];
            $oldKey = $cache[$injectID]['key'];

            // So we're going to prefer an inject
            // with the latest end time
            if (($newEnd == 0 && $oldEnd > 0) || ($oldEnd > 0 && $newEnd > $oldEnd)) {
                unset($injects[$oldKey]);

                $cache[$injectID] = [
                    'end' => $newEnd,
                    'key' => $k,
                ];
            } else {
                unset($injects[$k]);
            }
        }

        return $injects;
    }

    /**
     * Get (an) Inject (RAW)
     *
     * A little nicer than getInjects...but still we have dragons :(
     *
     * @param $id The schedule ID of the inject
     * @param $groups The groups the current user is in
     * @param $show_expired [Optional] Still return the inject, even
     * if it's expired
     * @return array The inject (if it's active/exists)
     */
    public function getInjectRaw($id, $groups, $show_expired = false) {
        $conditions = [
            'Schedule.id' => $id,
            'Schedule.group_id' => $groups,
            'Schedule.active' => true,
        ];

        if (!$show_expired) {
            $now = time();

            $conditions['OR'] = [
                [
                    'Schedule.fuzzy' => false,
                    'Schedule.start <=' => $now,
                ],
                [
                    'Schedule.fuzzy' => true,
                    'Schedule.start <=' => ($now - COMPETITION_START)
                ],
                [
                    'Schedule.start' => 0,
                ]
            ];
        }

        return $this->find('first', [
            'conditions' => $conditions,
        ]);
    }

    /**
     * Get Injects (and wrap them)
     *
     * This function uses the raw data from
     * `getInjectsRaw` and wraps every inject
     * inside an InjectAbstraction class
     *
     * @param $groups The groups to check for injects in
     * @param $onlyActive Only include injects that are active
     * @return array All the active injects
     */
    public function getInjects($groups, $onlyActive = true) {
        $rtn = [];
        $seenInjects = [];

        foreach ($this->getInjectsRaw($groups, $onlyActive) as $inject) {
            $submissionCount = ClassRegistry::init('Submission')->getCount($inject['Inject']['id'], $groups);

            // Resolve dependencies. This is so bad I'm sorry but I need to
            // get this working
            if ($inject['Schedule']['dependency_id'] > 0) {
                $count = ClassRegistry::init('Submission')->isDependencyMet($inject['Schedule']['dependency_id'], $groups);

                if ($count == 0) {
                    continue;
                }
            }

            $rtn[] = new InjectAbstraction($inject, $submissionCount);
        }

        return $rtn;
    }

    /**
     * Get (an) Inject (and wrap it)
     *
     * This function uses the raw data from
     * `getInjectRaw` and wraps the inject
     * inside an InjectAbstraction class
     *
     * @param $id The schedule ID of the inject
     * @param $groups The groups the current user is in
     * @param $show_expired [Optional] Still return the inject, even
     * if it's expired
     * @return array The inject (if it's active/exists)
     */
    public function getInject($id, $groups, $show_expired = false) {
        $inject = $this->getInjectRaw($id, $groups, $show_expired);

        if (!empty($inject)) {
            $submissionCount = ClassRegistry::init('Submission')->getCount($inject['Inject']['id'], $groups);
            $inject = new InjectAbstraction($inject, $submissionCount);
        }

        return $inject;
    }

    /**
     * Get recently expired injects
     *
     * This function uses the raw data from
     * `getInjectsRaw` and wraps every inject
     * inside an InjectAbstraction class
     *
     * @param $groups The groups to check for injects in
     * @param $howRecent How recent the injects have expired
     * @return array All the active injects
     */
    public function getRecentExpired($groups, $howRecent = (90 * 60)) {
        $now = time();
        $nowCS = ($now - COMPETITION_START);

        $data = $this->find('all', [
            'conditions' => [
                'Schedule.active'   => true,
                'Schedule.group_id' => $groups,
                'Schedule.end !='   => 0,
                'OR' => [
                    [
                        'Schedule.fuzzy'  => true,
                        'Schedule.end <'  => $nowCS,
                        'Schedule.end >=' => ($nowCS - $howRecent),
                    ],
                    [
                        'Schedule.fuzzy'  => false,
                        'Schedule.end <'  => $now,
                        'Schedule.end >=' => ($now - $howRecent),
                    ]
                ],
            ],
        ]);

        $rtn = [];
        foreach ($data as $d) {
            $rtn[] = new InjectAbstraction($d, 0);
        }

        return $rtn;
    }

    /**
     * Get _ALL_ Schedules
     *
     * @param $activeOnly [Optional] Only show active
     * @return array All the [active] schedules
     */
    public function getAllSchedules($activeOnly = true) {
        $this->bindModel([
            'belongsTo' => ['Group'],
        ]);

        $data = $this->find('all', [
            'conditions' => [
                'Schedule.active' => ($activeOnly ? true : [true,false]),
            ],
        ]);

        $rtn = [];
        foreach ($data as $d) {
            $rtn[] = new InjectAbstraction($d, 0);
        }

        return $rtn;
    }

    /**
     * Get Schedule Bounds
     *
     * Returns an array containing the min and
     * max times for the schedule list
     *
     * @param $round Should we round the times
     * @return array The min/max times
     */
    public function getScheduleBounds($round = true) {
        $this->virtualFields['min'] = 'IF(Schedule.fuzzy = 1, Schedule.start + '.COMPETITION_START.', Schedule.start)';
        $this->virtualFields['max'] = 'IF(Schedule.fuzzy = 1 AND Schedule.end > 0, '.
                    'Schedule.end + '.COMPETITION_START.', Schedule.end)';

        $min = $this->find('first', [
            'fields' => [
                'Schedule.min'
            ],
            'conditions' => [
                'Schedule.active' => true,
            ],
            'order' => [
                'Schedule.min ASC',
            ],
        ]);

        $max = $this->find('first', [
            'fields' => [
                'Schedule.max'
            ],
            'conditions' => [
                'Schedule.active' => true,
            ],
            'order' => [
                'Schedule.max DESC',
            ],
        ]);

        $bounds = [
            'min' => $min['Schedule']['min'],
            'max' => $max['Schedule']['max'],
        ];

        // Now round them
        if ($round) {
            $min = DateTime::createFromFormat('Y-m-d H:00:00', date('Y-m-d H:00:00', $bounds['min']));
            $max = DateTime::createFromFormat('Y-m-d H:00:00', date('Y-m-d H:00:00', $bounds['max']));
            $min->modify('-1 hour');
            $max->modify('+1 hour');

            $bounds['min'] = $min->getTimestamp();
            $bounds['max'] = $max->getTimestamp();
        }

        return $bounds;
    }
}
