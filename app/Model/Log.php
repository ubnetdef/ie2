<?php
App::uses('AppModel', 'Model');

/**
 * Log Model
 *
 */
class Log extends AppModel {

    public $belongsTo = ['User'];

    public $recursive = 2;
}
