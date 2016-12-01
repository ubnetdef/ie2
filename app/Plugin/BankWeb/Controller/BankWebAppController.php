<?php
App::uses('AppController', 'Controller');

class BankWebAppController extends AppController {
	public $components = ['BankWeb.BankApi'];
}