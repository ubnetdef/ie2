<?php
App::uses('AppModel', 'Model');

/**
 * Inject Model
 *
 */
class Inject extends AppModel {

    public $hasMany = ['Attachment'];

    public $recursive = 1;
}
