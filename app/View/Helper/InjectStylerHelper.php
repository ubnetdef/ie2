<?php
App::uses('AppHelper', 'View/Helper');

class InjectStylerHelper extends AppHelper {
	/**
	 * Instance of \InjectTypes\Manager
	 */
	private $typeManager;

	/**
	 * Instance of InjectAbstraction
	 */
	private $inject;

	const TYPE_OUTPUT_TPL = '<ul class="list-group"><li class="list-group-item">'.
				'<h4 class="list-group-item-heading">%s</h4></li></ul>';

	/**
	 * Constructor for the InjectHelper
	 *
	 * Basically initializes the InjectTypes manager
	 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		if ( !isset($settings['types']) || !isset($settings['inject']) ) {
			throw new InternalErrorException('InjectStyler is missing types/inject settings');
		}

		$this->typeManager = new InjectTypes\Manager($settings['types']);
		$this->setInject($settings['inject']);
	}

	/**
	 * Set the current inject
	 *
	 * @param $inject The InjectAbstraction object
	 * @return void
	 */
	public function setInject($inject) {
		$this->inject = $inject;
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

		// Duration infinite fix
		$duration = $inject->getDuration();
		if ( $duration == 0 ) $duration = '&infin;';

		return sprintf($template, $inject->getStartString(), $inject->getEndString(), $duration);
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
		$injectType = $this->typeManager->get($id);

		if ( $this->inject->isAcceptingSubmissions() ) {
			$tpl = '<form method="post" action="'.$this->url('/injects/submit').'" enctype="multipart/form-data">';
			$tpl .= '<input type="hidden" name="id" value="'.$this->inject->getScheduleId().'" />';
			$tpl .= $injectType->getTemplate();
			$tpl .= '</form>';
			
			return $tpl;
		}

		if ( $this->inject->isExpired() ) {
			return sprintf(self::TYPE_OUTPUT_TPL, 'Submission for this inject has expired.');
		}

		if ( $this->inject->getSubmissionCount() >= $this->inject->getMaxSubmissions() ) {
			return ($this->inject->getMaxSubmissions() > 1
				? sprintf(self::TYPE_OUTPUT_TPL, 'Max submissions reached.')
				: sprintf(self::TYPE_OUTPUT_TPL, 'This inject has already been submitted.'));
		}

		return 'Unknown error';
	}

	/**
	 * Inject Type Submitted Output
	 *
	 * @param $id Inject Type ID
	 * @return string The template
	 */
	public function submittedOutput($id, $submissions) {
		return $this->typeManager->get($id)->getSubmittedTemplate($submissions);
	}

	/**
	 * Inject Type Grader Output
	 *
	 * @param $id Inject Type ID
	 * @return string The template
	 */
	public function graderOutput($id, $submission) {
		return $this->typeManager->get($id)->getGraderTemplate($submission);
	}

	/**
	 * Get Inject Type Name
	 *
	 * @param $id Inject Type ID
	 * @return string The name
	 */
	public function getName($id) {
		return $this->typeManager->get($id)->getName();
	}
}