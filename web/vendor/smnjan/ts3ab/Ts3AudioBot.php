<?php
/**
 * Created by Kuhva.
 */
namespace smnjan\ts3ab;


class Ts3AudioBot {


    private $ip;
    private $port;

    private $username;
    private $accesstoken;
    private $commandExecutor;

    public $botid = 0;

    /**
     * Ts3AudioBot constructor.
     * @param $ip
     * @param $port
     */
    public function __construct($ip, $port) {
        $this->ip = $ip;
        $this->port = $port;
        $this->commandExecutor = new Ts3CommandCaller($this);
    }

    /**
     * @param $token
     */
    public function basicAuth($token) {
        $token = explode(":", $token);
        $this->username =  $token[0];
        $this->accesstoken = $token[1];
    }

    /**
     * @return string
     */
    private function generateHeader() {
        return "Authorization: Basic " . base64_encode($this->username . ":" . $this->accesstoken);
    }

    /**
     * @param $path
     * @return bool|string
     */
    public function request($path) {
        $ch = curl_init();
        $requestpath = "http://". $this->ip . ":" . $this->port . "/api/bot/use/" . $this->botid . "/(/" . $path;
        curl_setopt($ch, CURLOPT_URL, $requestpath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($this->generateHeader()));
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output,true);
    }

    /**
     * @param $path
     * @return bool|string
     */
    public function rawRequest($path) {
        $ch = curl_init();
        $requestpath = "http://" . $this->ip . ":" . $this->port . "/api/" . $path;
        curl_setopt($ch, CURLOPT_URL, $requestpath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($this->generateHeader()));
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output,true);
    }

    /**
     * @return Ts3CommandCaller
     */
    public function getCommandExecutor() {
        return $this->commandExecutor;
    }
}
