<?php

namespace GreatGetTogether;


class Slack
{
    protected $channel;
    protected $username;
    protected $url;
    protected $icon_emoji;


    public function __construct()
    {
        $this->channel = 'failed-signups';
        $this->username = 'bsd-signup-error';
        $this->url = 'https://hooks.slack.com/services/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';
        $this->icon_emoji = ':robot_face:';
    }

    public function sendMessage($message)
    {
        //$channel = 'greenpeace-error-log';
        //$username = 'error-monster';
        //$url = 'https://hooks.slack.com/services/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';
        //$icon_emoji = ':ghost:';
        //$text = $message;
        //$text = "```[date] => {$date}\n[application] => {$application}\n[errno] => {$errno}\n[errstr] => {$errstr}\n[errfile] => {$errfile}\n[errline] => {$errline}```";

        $data = json_encode(array(
            "channel" => "#{$this->channel}",
            "username" => $this->username,
            "text" => $message,
            "icon_emoji" => $this->icon_emoji
        ));

        $ch = curl_init("{$this->url}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('payload' => $data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function failedSignup($form, $signupData, $signupResponse)
    {
        $message = "SIGNUP: There has been a failed signup attempt for form $form: ```$signupData``` The response from BSD was: ```$signupResponse```";
        return $this->sendMessage($message);
    }

    public function failedUnsub($form, $unsubData, $unsubResponse)
    {
        $message = "UNSUB: There has been a failed unsub attempt for form $form: ```$unsubData``` The response from BSD was: ```$unsubResponse```";
        return $this->sendMessage($message);
    }
}