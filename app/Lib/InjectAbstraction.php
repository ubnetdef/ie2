<?php
App::uses('Inflector', 'Utility');

/**
 * Inject Class
 *
 * This represents a specific inject
 */
class InjectAbstraction implements JsonSerializable {
	/**
	 * Copy of the data returned from the
	 * Schedule model
	 */
	private $data;

	/**
	 * Default date string output
	 */
	const DATE_FORMAT = 'F j, Y \a\t g:iA';

	/**
	 * Inject Constructor
	 *
	 * @param $data Data returned from the model
	 */
	public function __construct($data) {
		$this->data = $data;

		// Clean up some stuff like submission_count
		if ( isset($data[0]['submission_count']) ) {
			$this->data['Schedule']['submission_count'] = $data[0]['submission_count'];
		}
	}

	/**
	 * Is Expired Accessor
	 *
	 * @return bool If the inject has expired
	 */
	public function isExpired() {
		return ($this->getEnd() > 0 ? $this->getEnd() <= time() : false);
	}

	/**
	 * Is Fuzzy Accessor
	 *
	 * @return bool If the inject is fuzzy scheduled
	 */
	public function isFuzzy() {
		return $this->data['Schedule']['fuzzy'];
	}

	/**
	 * Is Accepting Submissions
	 *
	 * @return bool If the inject can be submitted
	 */
	public function isAcceptingSubmissions() {
		return (!$this->isExpired() && $this->getSubmissionCount() < $this->getMaxSubmissions());
	}

	/**
	 * Inject Start Accessor
	 *
	 * @return int Unix timestamp of the start
	 * of this inject.
	 */
	public function getStart() {
		$start = $this->data['Schedule']['start'];

		if ( $this->isFuzzy() ) {
			$start += COMPETITION_START;
		}

		return $start;
	}
	public function getStartString() {
		return ($this->getStart() > 0
			? date(self::DATE_FORMAT, $this->getStart()) : 'Immediately');
	}

	/**
	 * Inject End Accessor
	 *
	 * @return int Unix timestamp of the end
	 * of this inject.
	 */
	public function getEnd() {
		$end = $this->data['Schedule']['end'];

		if ( $this->isFuzzy() && $end > 0 ) {
			$end += COMPETITION_START;
		}

		return $end;
	}
	public function getEndString() {
		return ($this->getEnd() > 0
			? date(self::DATE_FORMAT, $this->getEnd()) : 'Never');
	}

	/**
	 * Inject Duration Accessor
	 *
	 * @return int The inject duration in the
	 * form of minutes
	 */
	public function getDuration() {
		if ( $this->getEnd() == 0 ) return 0;

		$duration = ($this->getEnd() - $this->getStart());
		return round($duration / 60);
	}

	/**
	 * Inject ID Accessor
	 *
	 * @return int The inject ID
	 */
	public function getInjectID() {
		return $this->data['Inject']['id'];
	}

	/**
	 * Schedule ID Accessor
	 *
	 * @return int The schedule ID
	 */
	public function getScheduleID() {
		return $this->data['Schedule']['id'];
	}

	/**
	 * Generic accessor method
	 *
	 * This method will capture all "getSOMETHING"
	 * method calls
	 *
	 * @return mixed The data you're looking for
	 */
	public function __call($name, $args) {
		if ( count($args) > 0 ) return;
		if ( substr($name, 0, 3) != 'get' ) return;

		$key = Inflector::underscore(substr($name, 3));

		foreach ( ['Inject', 'Schedule'] AS $m ) {
			if ( isset($this->data[$m][$key]) ) {
				return $this->data[$m][$key];
			}
		}
	}

	/**
	 * JSON Serialize Method
	 *
	 * This method gets called when we
	 * json_encode this object
	 *
	 * @return array Data to be serialized
	 */
	public function jsonSerialize() {
		return [
			'id'        => $this->getInjectID(),
			'title'     => $this->getTitle(),
			'start'     => $this->getStartString(),
			'end'       => $this->getEndString(),
			'expired'   => $this->isExpired(),
			'submitted' => ($this->getSubmissionCount() > 0),
		];
	}
}