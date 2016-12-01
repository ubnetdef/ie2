<?php
App::uses('AppHelper', 'View/Helper');

class EngineOutputterHelper extends AppHelper {
	private $services = ['ICMP (1)', 'ICMP (2)', 'AD', 'HTTP', 'FTP', 'IMAP'];
	private $teams = [
		'Team 1', 'Team 2', 'Team 3', 'Team 4', 'Team 5', 'Team 6',
		'Team 7', 'Team 8', 'Team 9', 'Team 10', 'Team 11', 'Team 12',
	];

	public function generateScoreBoard() {
		$out = '<table class="table table-bordered text-center">';
		$out .= '<tr><td>Team</td>';

		foreach ( $this->services AS $s ) {
			$out.= '<td>'.$s.'</td>';
		}

		$out.= ' </tr>';

		foreach ( $this->teams AS $t ) {
			$out .= '<tr><td width="15%">'.$t.'</td>';
			foreach ( $this->services AS $s ) {
				$out .= '<td class="success" width="12%"></td>';
			}
			$out .= '</tr>';
		}

		$out .= '</table>';
		return $out;
	}
}