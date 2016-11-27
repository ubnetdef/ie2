<?php
App::uses('AppHelper', 'View/Helper');

class InjectHelper extends AppHelper {
	/**
	 * Instance of \InjectTypes\Manager
	 */
	private $typeManager;

	/**
	 * Constructor for the InjectHelper
	 *
	 * Basically initializes the InjectTypes manager
	 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->typeManager = new InjectTypes\Manager($settings['types']);
	}

	/**
	 * Time output
	 *
	 * This will be the inject page's "start/end/duration"
	 * area.
	 *
	 * @param $inject The inject
	 * @return string The time content
	 */
	public function timeOutput($inject) {
		$template = '<strong>Time</strong>: %s<br /><strong>Due</strong>: %s<br /><strong>Duration</strong>: %s Minutes';

		$start = $inject->getStart();
		$stop = $inject->getEnd();

		// Pretty dates!
		$oldTZ = date_default_timezone_get();
		date_default_timezone_set('America/New_York');

		$startStr = date('F j, Y \a\t g:iA', $start);
		$stopStr = date('F j, Y \a\t g:iA', $stop);
		
		date_default_timezone_set($oldTZ);

		// Temp
		if ( $inject->isFuzzy() ) {
			$startStr .= ' (Fuzzy)';
			$stopStr .= ' (Fuzzy)';
		}

		// Convert the duration into minutes
		$duration = (($stop - $start) / 60);

		// Fix infinite start/stop/duration
		if ( $start == 0 ) $startStr = 'Immediately';
		if ( $stop == 0 ) $stopStr = 'Never';
		if ( $duration == 0 ) $duration = '&infin;';

		return sprintf($template, $startStr, $stopStr, $duration);
	}

	/**
	 * Content Output
	 *
	 * Basically replaces some variables
	 * with actual content. Woah!
	 *
	 * @param $data The inject content
	 * @param $userdata Current user information
	 * @return string The inject content
	 */
	public function contentOutput($data, $userdata) {
		if ( $userdata['Group']['team_number'] == null ) {
			$team = '<strong>X</strong>';
			$team_pad = '<strong>XX</strong>';
		} else {
			$team = $userdata['Group']['team_number'];
			$team_pad = str_pad($team, 2, '0');
		}

		$find = ['#TEAM_NUMBER#', '#TEAM_NUMBER_PADDED#'];
		$replace = [$team, $team_pad];

		return str_replace($find, $replace, $data);
	}

	/**
	 * Inject Type Submission Output
	 *
	 * @param $id Inject Type ID
	 * @return string The template
	 */
	public function typeOutput($id) {
		return $this->typeManager->get($id)->getTemplate();
	}
}