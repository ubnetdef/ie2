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
     * The time for an active inject
     * to be considered 'recent'
     */
    const ACTIVE_RECENT = 5 * 60;

    /**
     * The time for an expired inject
     * to be considered 'recent'
     */
    const EXPIRED_RECENT = 30 * 60;

    /**
     * String used when an inject
     * starts immediately
     */
    const STR_IMMEDIATELY = 'Immediately';

    /**
     * String used when an inject
     * never ends
     */
    const STR_NEVER = 'Never';

    /**
     * Inject Constructor
     *
     * @param $data Data returned from the model
     * @param $submissionCount Count of how many submissions
     */
    public function __construct($data, $submissionCount) {
        $this->data = $data;
        $this->data['Schedule']['submission_count'] = $submissionCount;
    }

    /**
     * Is Recent Accessor
     *
     * @return bool If the inject is 'recent'
     */
    public function isRecent() {
        $time = ($this->isExpired() ? $this->getEnd() : $this->getStart());
        $recent = ($this->isExpired() ? self::EXPIRED_RECENT : self::ACTIVE_RECENT);
        $howLongAgo = (time() - $time);

        return ($time > 0 && $recent >= $howLongAgo);
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
        return $this->getScheduleFuzzy();
    }

    /**
     * Is Accepting Submissions
     *
     * @return bool If the inject can be submitted
     */
    public function isAcceptingSubmissions() {
        return ($this->isExpired() == false && $this->getRemainingSubmissions() > 0);
    }

    /**
     * Remaining Submissions Accessor
     *
     * @return int The number of remaining submissions
     */
    public function getRemainingSubmissions() {
        return $this->getMaxSubmissions() - $this->getSubmissionCount();
    }

    /**
     * Inject Start Accessor
     *
     * @return int Unix timestamp of the start
     * of this inject.
     */
    public function getStart() {
        $start = $this->getScheduleStart();

        if ($this->isFuzzy() && $start > 0) {
            $start += COMPETITION_START;
        }

        return $start;
    }
    public function getStartString() {
        return ($this->getStart() > 0 ? tz_date(self::DATE_FORMAT, $this->getStart()) : self::STR_IMMEDIATELY);
    }
    public function getManagerStartString() {
        if (!$this->isFuzzy() || $this->getStart() == 0) { return $this->getStartString();
        }

        return $this->_fuzzyDuration('+', $this->getStart());
    }

    /**
     * Inject End Accessor
     *
     * @return int Unix timestamp of the end
     * of this inject.
     */
    public function getEnd() {
        $end = $this->getScheduleEnd();

        if ($this->isFuzzy() && $end > 0) {
            $end += COMPETITION_START;
        }

        return $end;
    }
    public function getEndString() {
        return ($this->getEnd() > 0 ? tz_date(self::DATE_FORMAT, $this->getEnd()) : self::STR_NEVER);
    }
    public function getManagerEndString() {
        if (!$this->isFuzzy() || $this->getEnd() == 0) { return $this->getEndString();
        }

        return $this->_fuzzyDuration('+', $this->getEnd());
    }

    /**
     * Inject Duration Accessor
     *
     * @return int The inject duration in the
     * form of minutes
     */
    public function getDuration() {
        if ($this->getEnd() == 0) { return 0;
        }
        if ($this->getStart() == 0) { return -1;
        }

        $duration = ($this->getEnd() - $this->getStart());
        return round($duration / 60);
    }
    public function getDurationString() {
        $duration = $this->getDuration();

        if ($duration < 0) {
            return 'N/A';
        } elseif ($duration == 0) {
            return '&infin;';
        } else {
            return $this->_fuzzyDuration('', $this->getEnd(), $this->getStart());
        }
    }

    /**
     * Has Attachments
     *
     * @return bool If the inject has attachments
     */
    public function hasAttachments() {
        return isset($this->data['Inject']['Attachment']) && count($this->data['Inject']['Attachment']) > 0;
    }

    /**
     * Get Attachments
     *
     * @return array The inject attachments
     */
    public function getAttachments() {
        if (!$this->hasAttachments()) {
            return [];
        }

        $attachments = [];
        foreach ($this->data['Inject']['Attachment'] as $a) {
            $attachments[] = [
                'id'   => $a['id'],
                'name' => $a['name'],
            ];
        }

        return $attachments;
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
        if (count($args) > 0) { return;
        }
        if (substr($name, 0, 3) != 'get') { return;
        }

        $key = Inflector::underscore(substr($name, 3));

        // Deal with a possible call that includes the
        // group (ex: getGroupName -> ['Group']['name'])
        if (($pos = strpos($key, '_')) !== false) {
            $grp = ucfirst(substr($key, 0, $pos));
            $subkey = substr($key, $pos + 1);

            if (isset($this->data[$grp][$subkey])) {
                return $this->data[$grp][$subkey];
            }
        }

        // Fallback to matching everything
        foreach ($this->data as $m => $data) {
            if (isset($data[$key])) {
                return $data[$key];
            }
        }

        return null;
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
            'id'        => $this->getScheduleId(),
            'title'     => $this->getTitle(),
            'start'     => $this->getStartString(),
            'end'       => $this->getEndString(),
            'expired'   => $this->isExpired(),
            'submitted' => ($this->getSubmissionCount() > 0),
        ];
    }

    /**
     * Fuzzy Duration Generator
     *
     * @source http://stackoverflow.com/a/18602474
     * @param $time The time you're checking
     * @return string The fuzzy time
     */
    private function _fuzzyDuration($prepend, $time, $start = COMPETITION_START) {
        $start = new DateTime('@'.$start);
        $end = new DateTime('@'.$time);
        $diff = $end->diff($start);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        return $prepend.implode(', ', $string);
    }
}
