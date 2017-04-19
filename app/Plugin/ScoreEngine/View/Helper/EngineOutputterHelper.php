<?php
App::uses('AppHelper', 'View/Helper');

class EngineOutputterHelper extends AppHelper {

    public function generateScoreBoard() {
        if (!isset($this->settings['data'])) {
            throw new InternalErrorException('ScoreEngine.EngineOutputter not setup correctly');
        }

        $out = '<table class="table table-bordered text-center">';
        $out .= '<tr><td>Team</td>';

        // Grab the first element from the data array
        // This is two lines due to a pass-by-reference warning
        $services = array_values($this->settings['data']);
        $services = array_shift($services);

        foreach ($services as $service_name => $status) {
            $out .= '<td>'.$service_name.'</td>';
        }

        $out .= ' </tr>';

        foreach ($this->settings['data'] as $team_name => $services) {
            $out .= '<tr><td width="15%">'.$team_name.'</td>';

            foreach ($services as $service_name => $status) {
                if ($status === null) {
                    $class = 'info';
                } else {
                    $class = ($status ? 'success' : 'danger');
                }

                $out .= '<td class="'.$class.'" width="'.floor((100 - 15) / count($services)).'%"></td>';
            }
            $out .= '</tr>';
        }

        $out .= '</table>';
        return $out;
    }
}
