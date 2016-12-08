<?php
App::uses('AdminAppController', 'Controller');

use Respect\Validation\Rules;
use Respect\Validation\Exceptions\NestedValidationException;

class AdminGroupsController extends AdminAppController {
	public $uses = ['Group'];
	private $validators = [];

	public function beforeFilter() {
		parent::beforeFilter();

		$this->validators = [
			'name' => new Rules\AllOf(
				new Rules\Alnum('-_'),
				new Rules\NotEmpty()
			),
			'team_number' => new Rules\OneOf(
				new Rules\Not(new Rules\NotEmpty()),
				new Rules\Digit()
			),
			'parent_id' => new Rules\OneOf(
				new Rules\Not(new Rules\NotEmpty()),
				new Rules\Digit()
			),
		];
	}

	/**
	 * Group List Page 
	 *
	 * @url /admingroup
	 * @url /admin/group
	 * @url /admingroup/index
	 * @url /admin/group/index
	 */
	public function index() {
		$mappings = [];
		foreach ( $this->Group->find('all') AS $g ) {
			if ( $g['Group']['team_number'] === NULL ) continue;

			$mappings[$g['Group']['id']] = $g['Group']['team_number'];
		}

		$this->set('mappings', $mappings);
		$this->set('groups', $this->Group->generateTreeList(null, null, null, '-- '));
	}

	/**
	 * Create Group 
	 *
	 * @url /admingroup/create
	 * @url /admin/group/create
	 */
	public function create() {
		if ( $this->request->is('post') ) {
			// Validate the input
			$errors = [];
			$create = [];

			foreach ( $this->validators AS $key => $validator ) {
				// If we're missing something, stop it's bad.
				if ( !isset($this->request->data[$key]) ) {
					$errors[] = sprintf('Missing input "%s"', $key);
					continue;
				}

				try {
					$validator->assert($this->request->data[$key]);

					$create[$key] = $this->request->data[$key];
				} catch ( NestedValidationException $e ) {
					$errors[] = sprintf(
						'Input %s must have pass the following rules:<br />-%s',
						$key,
						implode('<br />-', $e->getMessages())
					);
				}
			}

			if ( empty($errors) ) {
				$this->Group->create();
				$this->Group->save($create);

				$this->logMessage(
					'groups',
					sprintf('Created group "%s"', $created['name']),
					[],
					$this->Group->id
				);

				$this->Flash->success('The group has been created!');
				return $this->redirect('/admin/groups');
			} else {
				$this->Flash->danger('The following errors have occured:<br />'.implode('<br />', $errors));
			}
		}

		$this->set('groups', $this->Group->generateTreeList(null, null, null, '-- '));
	}

	/**
	 * Edit Group 
	 *
	 * @url /admingroup/edit/<gid>
	 * @url /admin/group/edit/<gid>
	 */
	public function edit($gid=false) {
		$group = $this->Group->findById($gid);
		if ( empty($group) ) {
			throw new NotFoundException('Unknown group!');
		}

		if ( $this->request->is('post') ) {
			// Validate the input
			$errors = [];
			$update = [];

			foreach ( $this->validators AS $key => $validator ) {
				// If we're missing something, stop it's bad.
				if ( !isset($this->request->data[$key]) ) {
					$errors[] = sprintf('Missing input "%s"', $key);
					continue;
				}

				try {
					$validator->assert($this->request->data[$key]);

					$update[$key] = $this->request->data[$key];
				} catch ( NestedValidationException $e ) {
					$errors[] = sprintf(
						'Input %s must have pass the following rules:<br />-%s',
						$key,
						implode('<br />-', $e->getMessages())
					);
				}
			}

			if ( empty($errors) ) {
				$this->Group->id = $gid;
				$this->Group->save($update);

				$this->logMessage(
					'groups',
					sprintf('Updated group "%s"', $group['Group']['name']),
					[
						'old_group' => $group['Group'],
						'new_group' => $update,
					],
					$uid
				);

				$this->Flash->success('The user has been updated!');
				return $this->redirect('/admin/groups');
			} else {
				$this->Flash->danger('The following errors have occured:<br />'.implode('<br />', $errors));
			}
		}

		$this->set('group', $group);
		$this->set('groups', $this->Group->generateTreeList(null, null, null, '-- '));
	}

	/**
	 * Delete group 
	 *
	 * @url /admingroup/delete/<gid>
	 * @url /admin/group/delete/<gid>
	 */
	public function delete($gid=false) {
		$group = $this->Group->findById($gid);
		if ( empty($group) ) {
			throw new NotFoundException('Unknown group!');
		}

		if ( $this->request->is('post') ) {
			$this->Group->delete($gid);

			$msg = sprintf('Deleted group "%s" (#%d)', $group['Group']['name'], $gid);

			$this->logMessage(
				'groups',
				$msg,
				[
					'group' => $group['Group'],
				],
				$gid
			);

			$this->Flash->success($msg);
			return $this->redirect('/admin/groups');
		}

		$this->set('group', $group);
	}
}
