<?php
App::uses('AppModel', 'Model');

/**
 * Submission Model
 *
 */
class Submission extends AppModel {

    public $belongsTo = ['Inject', 'User', 'Group'];

    public $hasOne = ['Grade'];

    public $recursive = 1;

    /**
     * Get All Ungraded-Submissions
     *
     * This retrieves the submissions done by
     * a group that is ungraded.
     *
     * @return array The submissions that are ungraded.
     */
    public function getAllUngradedSubmissions() {
        return $this->find('all', [
            'fields' => [
                'Submission.id', 'Submission.created', 'Submission.deleted',
                'Inject.id', 'Inject.title', 'Inject.sequence', 'Inject.type',
                'User.username', 'Group.name', 'Group.team_number',
                'Grade.created', 'Grade.grade', 'Grade.comments',
                'Grader.username',
            ],

            'joins' => [
                [
                    'table'      => 'users',
                    'alias'      => 'Grader',
                    'type'       => 'LEFT',
                    'conditions' => [
                        'Grader.id = Grade.grader_id',
                    ],
                ]
            ],

            'conditions' => [
                'Grade.created IS NULL',
                'Submission.deleted' => false,
            ],

            'order' => [
                'Grade.created DESC',
                'Submission.created DESC',
            ],
        ]);
    }

    /**
     * Get All Submissions
     *
     * This retrieves the submissions done by
     * a group.
     *
     * @param $group The group ID
     * @param $noDeleted Discard deleted submissions
     * @return array The submissions done by this group
     */
    public function getAllSubmissions($group = false, $noDeleted = false) {
        $conditions = [];

        if ($group !== false) {
            $conditions['Group.id'] = $group;
        }
        if ($noDeleted) {
            $conditions['Submission.deleted'] = false;
        }

        return $this->find('all', [
            'fields' => [
                'Submission.id', 'Submission.created', 'Submission.deleted',
                'Inject.id', 'Inject.title', 'Inject.sequence', 'Inject.type',
                'User.username', 'Group.id', 'Group.name', 'Group.team_number',
                'Grade.created', 'Grade.grade', 'Grade.comments',
                'Grader.username',
            ],

            'joins' => [
                [
                    'table'      => 'users',
                    'alias'      => 'Grader',
                    'type'       => 'LEFT',
                    'conditions' => [
                        'Grader.id = Grade.grader_id',
                    ],
                ]
            ],

            'conditions' => $conditions,

            'order' => [
                'Grade.created DESC',
                'Submission.created DESC',
            ],
        ]);
    }

    /**
     * Get Submission
     *
     * This retrieves the submission done by
     * a group.
     *
     * @param $sid The submission ID
     * @return array The submissions done by this group
     */
    public function getSubmission($sid, $group = false, $noDeleted = false) {
        $conditions = [
            'Submission.id' => $sid,
        ];

        if ($group !== false) {
            $conditions['Group.id'] = $group;
        }
        if ($noDeleted) {
            $conditions['Submission.deleted'] = false;
        }

        return $this->find('first', [
            'fields' => [
                'Submission.*', 'Inject.*', 'User.*',
                'Group.*', 'Grade.*', 'Grader.*',
            ],

            'joins' => [
                [
                    'table'      => 'users',
                    'alias'      => 'Grader',
                    'type'       => 'LEFT',
                    'conditions' => [
                        'Grader.id = Grade.grader_id',
                    ],
                ]
            ],

            'conditions' => $conditions,
        ]);
    }

    /**
     * Get Submissions
     *
     * This retrieves the the submissions for
     * a specific inject done by a group. This
     * will filter out deleted submissions, too.
     *
     * @param $id The inject ID
     * @param $group The group ID
     * @return array The submissions done by this group
     * for this inject
     */
    public function getSubmissions($id, $group) {
        return $this->find('all', [
            'conditions' => [
                'Inject.id'          => $id,
                'Group.id'           => $group,
                'Submission.deleted' => false,
            ],
        ]);
    }

    /**
     * Get Submissions Count
     *
     * Basically `getSubmissions`
     *
     * @param $id The inject ID
     * @param $group The group ID
     * @return int The number of the submissions done by
     * this group for this inject
     */
    public function getCount($id, $group) {
        return $this->find('count', [
            'conditions' => [
                'Inject.id'          => $id,
                'Group.id'           => $group,
                'Submission.deleted' => false,
            ],
        ]);
    }

    /**
     * Is Dependendy Met
     *
     * @param $id The inject ID
     * @param $group The group ID
     * @return int The number of the submissions done by
     * this group for this inject
     */
    public function isDependencyMet($id, $group) {
        return $this->find('count', [
            'conditions' => [
                'Inject.id'          => $id,
                'Group.id'           => $group,
                'Submission.deleted' => false,
                'Grade.grade > 0',
            ],
        ]) > 0;
    }

    /**
     * Get Grade Totals
     *
     * @param $groups The groups you wish to get grades for
     * @return array The grades for all the groups
     */
    public function getGrades($groups) {
        $this->virtualFields['total_grade'] = 'SUM(Grade.grade)';

        return $this->find('all', [
            'fields' => [
                'Submission.total_grade', 'Group.name', 'Group.team_number',
            ],
            'conditions' => [
                'Group.id'           => $groups,
                'Submission.deleted' => false,
            ],
            'group' => [
                'Group.id'
            ],
            'order' => [
                'Submission.total_grade DESC',
            ],
        ]);
    }
}
