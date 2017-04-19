<?php
App::uses('AppModel', 'Model');

/**
 * UsedHint Model
 *
 */
class UsedHint extends AppModel {

    public $belongsTo = ['Hint'];

    public $recursive = 1;
}
