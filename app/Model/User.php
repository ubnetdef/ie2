<?php
App::uses('AppModel', 'Model');
App::uses('Security', 'Utility');

/**
 * User Model
 *
 */
class User extends AppModel {

    public $belongsTo = ['Group'];

    public $recursive = 1;

    /**
     * Before Save Hook
     *
     * Ensures if the "password" key is set, we hash it correctly (using bcrypt)
     * @param $options Unknown
     * @return boolean If the operation we're doing worked
     */
    public function beforeSave($options = []) {
        if (!empty($this->data['User']['password'])) {
            $this->data['User']['password'] = Security::hash($this->data['User']['password'], 'blowfish');
        }

        return true;
    }
}
