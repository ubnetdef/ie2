<?php
App::uses('AppModel', 'Model');

/**
 * Announcement Model
 *
 */
class Announcement extends AppModel {

    /**
     * Get's all active announcements
     *
     * @return array The active announcements
     */
    public function getAll() {
        return $this->find('all', [
            'conditions' => [
                'Announcement.active' => true,
                'OR' => [
                    'Announcement.expiration >' => time(),
                    'Announcement.expiration' => 0
                ],
            ],
        ]);
    }
}
