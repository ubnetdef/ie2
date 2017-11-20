<?php
App::uses('Component', 'Controller');

class MattermostComponent extends Component {

    public function send($message, $channel = false, $extra = []) {
        $data = $extra;

        if ($channel !== false) {
            $data['channel'] = $channel;
        }
        if ($message !== null) {
            $data['text'] = $message;
        }

        return $this->request($data);
    }

    private function request($data) {
        if (!env('MATTERMOST_WEBHOOK_URL')) {
            return;
        }

        $payload = 'payload='.json_encode($data);

        $ch = curl_init(env('MATTERMOST_WEBHOOK_URL'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);

        return json_decode($result, true);
    }
}
