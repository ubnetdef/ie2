<?php
App::uses('AdminAppController', 'Controller');

use Respect\Validation\Rules;
use Respect\Validation\Exceptions\NestedValidationException;

class AdminInjectsController extends AdminAppController {
	public $uses = ['Config', 'Inject'];
	private $validators = [];

	public function beforeFilter() {
		parent::beforeFilter();

		// Load + setup the InjectStyler helper
		$this->helpers[] = 'InjectStyler';
		$this->helpers['InjectStyler'] = [
			'types'  => $this->Config->getInjectTypes(),
			'inject' => new stdClass(), // Nothing...for now
		];

		// Setup the validators
		$this->validators = [
			'sequence' => new Rules\AllOf(
				new Rules\Digit()
			),
			'title' => new Rules\AllOf(
				new Rules\Alnum('-_'),
				new Rules\NotEmpty()
			),
			'content' => new Rules\AllOf(
				new Rules\Alnum('-_'),
				new Rules\NotEmpty()
			),
			'grading_guide' => new Rules\AllOf(
				new Rules\Alnum('-_')
			),
			'max_points' => new Rules\AllOf(
				new Rules\Digit(),
				new Rules\NotEmpty()
			),
			'max_submissions' => new Rules\AllOf(
				new Rules\Digit(),
				new Rules\NotEmpty()
			),
			'type' => new Rules\AllOf(
				new Rules\Alnum('-_'),
				new Rules\NotEmpty()
			),
		];
	}

	/**
	 * Inject List Page 
	 *
	 * @url /admininjects
	 * @url /admin/injects
	 * @url /admininjects/index
	 * @url /admin/injects/index
	 */
	public function index() {
		$this->set('injects', $this->Inject->find('all'));
	}

	/**
	 * Create Inject 
	 *
	 * @url /admininjects/create
	 * @url /admin/injects/create
	 */
	public function create() {
	}

	/**
	 * Edit Inject 
	 *
	 * @url /admininjects/edit/<id>
	 * @url /admin/injects/edit/<id>
	 */
	public function edit($id=false) {
		$inject = $this->Inject->findById($id);
		if ( empty($inject) ) {
			throw new NotFoundException('Unknown inject!');
		}

		$this->set('inject', $inject);
	}

	/**
	 * Delete Inject 
	 *
	 * @url /admininjects/delete/<id>
	 * @url /admin/injects/delete/<id>
	 */
	public function delete($id=false) {
		$inject = $this->Inject->findById($id);
		if ( empty($inject) ) {
			throw new NotFoundException('Unknown inject!');
		}

		$this->set('inject', $inject);
	}
}
