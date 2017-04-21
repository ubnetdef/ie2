<?php
App::uses('BankWebAppModel', 'BankWeb.Model');

class Purchase extends BankWebAppModel {

    public $tablePrefix = 'bank_';

    public $belongsTo = ['BankWeb.Product', 'User'];

    public $recursive = 2;
}
