<?php
App::uses('Component', 'Controller');

class SlackComponent extends Component {
    const SLACK_ENDPOINT_ROOT = 'https://slack.com/api/';

    public function send($channel, $message, $extra = []) {
        $data = [
            'text' => $message,
            'channel' => $channel,
        ] + $extra;

        return $this->request('chat.postMessage', $data);
    }

    public function update($ts, $channel, $message, $extra = []) {
        $data = [
            'ts' => $ts,
            'channel' => $channel,
            'text' => $message,
        ] + $extra;

        return $this->request('chat.update', $data);
    }

    private function request($endpoint, $data) {
        if (!env('SLACK_APIKEY')) {
            return;
        }

        // Build the payload
        $payload = [
            'token' => env('SLACK_APIKEY'),
        ] + $data;
        $payload = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', http_build_query($payload));

        $ch = curl_init(self::SLACK_ENDPOINT_ROOT.$endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}
