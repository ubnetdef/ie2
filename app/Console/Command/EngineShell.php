<?php
App::uses('AppShell', 'Console/Command');

/**
 * (Inject)Engine Shell
 *
 * Command line interface for doing limited actions
 * for the InjectEngine.  Here be (some) dragons.
 */
class EngineShell extends AppShell {
	public $uses = ['Group', 'User'];

	/**
	 * Engine Command: Install
	 *
	 * Wipe's the current InjectEngine install, and re-initializes
	 * the database, as well as users.
	 */
	public function install() {
		$this->out('Installing ie2');
		$this->hr();

		$this->out('Initializing the database...', 0);

		// Initialize our database
		$this->dispatchShell('schema', 'create', '--yes', '--quiet');

		// Create the basic groups
		$this->dispatchShell('engine', 'create_group', 'root', 'Staff', '--quiet', '--yes');
		$this->dispatchShell('engine', 'create_group', 'root', 'Blue Teams', '--quiet', '--yes');

		$this->dispatchShell('engine', 'create_group', 'Staff', 'Administrative Team', '--quiet', '--yes');
		$this->dispatchShell('engine', 'create_group', 'Staff', 'White Team', '--quiet', '--yes');

		// DB initialized
		$this->out('DONE!');

		// Create the first user
		$this->out('Creating the initial user...', 0);
		$this->dispatchShell('engine', 'create_user', 'admin', 'Administrative Team', '--quiet', '--yes', '--password', 'admin');
		$this->out('DONE!');

		// Done!
		$this->out('Installation completed!');
		$this->hr();
		$this->out('<header>Administrator Credentials</header>');
		$this->out('Username: admin');
		$this->out('Password: admin');
	}

	/**
	 * Engine Command: Create User
	 *
	 * Create's a user.  Nothing more, nothing less.
	 */
	public function create_user() {
		list($user, $group) = $this->args;
		$yes = (isset($this->params['yes']) && !empty($this->params['yes']));

		if ( !isset($this->params['password']) ) {
			$pass = $this->in('Password: ');
			$this->hr();
		} else {
			$pass = $this->params['password'];
		}

		// Check if the username exists
		if ( !empty($this->User->findByUsername($user)) ) {
			$this->error(sprintf('A user with the same username (%s) already exists!', $user));
		}

		$group_data = $this->Group->findByName($group);
		if ( empty($group_data) ) {
			$this->error(sprintf('Group "%s" not found', $group));
		}

		$this->out(sprintf('Creating user "%s" with group "%s"', $user, $group_data['Group']['name']));

		if ( $yes || $this->in('Are you sure you want to create the user?', ['y', 'n'], 'y') == 'y' ) {
			$this->hr();

			// Create the user
			$this->out('Creating the user...');
			$this->User->create();
			$this->User->save([
				'username' => $user,
				'password' => $pass,
				'group_id' => $group_data['Group']['id'],
				'active'   => true,
			]);

			$this->out(sprintf('User (%d) created!', $this->User->id));
		}
	}

	/**
	 * Engine Command: Create Group
	 *
	 * Create's a group.  Nothing more, nothing less.
	 */
	public function create_group() {
		list($parent, $group) = $this->args;
		$yes = (isset($this->params['yes']) && !empty($this->params['yes']));

		// Check if the group exists
		if ( !empty($this->Group->findByName($group)) ) {
			$this->error(sprintf('Group "%s" already exists', $group));
		}

		// Check if our parent exists, if it's not root
		$parent_id = null;

		if ( $parent != 'root' ) {
			$data = $this->Group->findByName($parent);

			if ( empty($data) ) {
				$this->error(sprintf('I was unable to find the parent group "%s"', $parent));
			}

			$parent = $data['Group']['name'];
			$parent_id = $data['Group']['id'];
		}

		$this->out(sprintf('Creating group "%s" under parent "%s"', $group, $parent));

		if ( $yes || $this->in('Are you sure you want to create the group?', ['y', 'n'], 'y') == 'y' ) {
			$this->hr();

			// Create the group
			$this->out('Creating the group...');
			$this->Group->create();
			$this->Group->save([
				'name'      => $group,
				'parent_id' => $parent_id,
			]);

			$this->out(sprintf('Group (%s) created!', $group));
		}
	}

	/*
	 * ========================================
	 * The following are required for CakePHP,
	 * but are undocumented. Sorry.
	 * ========================================
	 */

	public function startup() {
		parent::startup();
		$this->stdout->styles('header', array('underline' => true));
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser
			->description('A console tool to manage ie2')
			->addSubCommand('install', [
				'help' => 'Installs ie2',
			])
			->addSubCommand('create_user', [
				'help' => 'Create a user for ie2',
				'parser' => [
					'arguments' => [
						'username' => [
							'help' => 'The username of the user you wish to create',
							'required' => true,
						],
						'group' => [
							'help' => 'The group the new user will be apart of',
							'required' => true,
						],
					],
					'options' => [
						'yes' => [
							'short' => 'y',
							'boolean' => true,
							'help' => 'Do not prompt for confirmation. Be careful!',
						],
						'password' => [
							'short' => 'p',
							'help' => 'The password of the user you wish to create',
						],
					],
				],
			])
			->addSubCommand('create_group', [
				'help' => 'Create a group for ie2',
				'parser' => [
					'arguments' => [
						'parent' => [
							'help' => 'The parent of the group you wish to create. If you wish to create a root group, use "root" here.',
							'required' => true,
						],
						'name' => [
							'help' => 'The name of the group you wish to create',
							'required' => true,
						],
					],
					'options' => [
						'yes' => [
							'short' => 'y',
							'boolean' => true,
							'help' => 'Do not prompt for confirmation. Be careful!',
						],
					],
				],
			]);

		return $parser;
	}
}
