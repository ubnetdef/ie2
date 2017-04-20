<?php
App::uses('BankWebAppModel', 'BankWeb.Model');

class Purchase extends BankWebAppModel {

    public $tablePrefix = 'bank_';

    public $belongsTo = ['BankWeb.Product'];

    public $recursive = 1;
}
