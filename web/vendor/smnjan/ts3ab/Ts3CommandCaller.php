<?php
/**
 * Created by Kuhva.
 */
namespace smnjan\ts3ab;

use TS3AB\Commands\History;
use TS3AB\Commands\Playlist;
use TS3AB\Commands\User;


/**
 * Class Ts3CommandCaller
 * @package TS3AB
 */
class Ts3CommandCaller {

    private $instance;

    /**
     * Ts3CommandCaller constructor.
     * @param Ts3AudioBot $ts3audioBotinstance
     */
    public function __construct(Ts3AudioBot $ts3audioBotinstance) {
        $this->instance = $ts3audioBotinstance;
    }


    /**
     * @param string $link
     * @return string json
     */
    public function play(string $link) {
        return $this->instance->request("play/" . rawurlencode($link));
    }

    /**
     * @return mixed
     */
    public function pause() {
        return $this->instance->request("pause");
    }

    /**
     * @return mixed
     */
    public function unpause() {
        return $this->instance->request("play");
    }

    /**
     * @return mixed
     */
    public function song() {
        return $this->instance->request("song");
    }

    /**
     * @param int $volume
     * @return bool|mixed
     */
    public function volume(int $volume) {
        $volume = (int) $volume;
        if ($volume >= 0 && $volume <= 100) {
            return $this->instance->request("volume/" . $volume);
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function stop() {
        return $this->instance->request("stop");
    }

    /**
     * @return string json
     */
    public function makeCommander() {
        return $this->instance->request("bot/commander/on");
    }

    /**
     * @return mixed
     */
    public function takeCommander() {
        return $this->instance->request("bot/commander/off");
    }

    /**
     * @return mixed
     */
    public function connectTo($templateName) {
        return $this->instance->rawRequest("bot/connect/template/" . rawurlencode($templateName));
    }

    /**
     * @return mixed
     */
    public function connectNew($ip,$password = null) {
        if ($password !== null){
            return $this->instance->rawRequest("bot/connect/to/" . rawurlencode($ip). "/".rawurlencode($password));
        } else {
            return $this->instance->rawRequest("bot/connect/to/" . rawurlencode($ip));
        }
    }

    /**
     * @return mixed
     */
    public function info() {
        return $this->instance->request("bot/info");
    }

    /**
     * @return mixed
     */
    public function listBots() {
        return $this->instance->rawRequest("bot/list");
    }

    /**
     * @return mixed
     */
    public function name($name) {
        return $this->instance->request("bot/name/" . rawurlencode($name));
    }

    /**
     * @return mixed
     */
    public function save($templateName) {
        return $this->instance->request("bot/save/" . rawurlencode($templateName));
    }


    /**
     * @return mixed
     */
    public function disconnect() {
        return $this->instance->request("bot/disconnect");
    }

    /**
     * @param $botid
     */
    public function use($botid) {
        $this->instance->botid = $botid;
    }

    /**
     * @return mixed
     */
    public function settings() {
        return $this->instance->rawRequest("settings");
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getSettings($value) {
        return $this->instance->rawRequest("settings/get/" . $value);
    }

    /**
     * @param $string
     * @param $value
     * @return mixed
     */
    public function setSettings($string, $value) {
        return $this->instance->rawRequest("settings/set/" . $string . "/" . rawurlencode($value));
    }

    /**
     * @param $template
     * @param $string
     * @return mixed
     */
    public function getBotSettings($template, $string) {
        return $this->instance->rawRequest("settings/bot/get/" . $template . "/" . $string);
    }

    /**
     * @param $template
     * @param $string
     * @param $value
     * @return mixed
     */
    public function setBotSettings($template, $string, $value) {
        return $this->instance->rawRequest("settings/bot/set/" . rawurlencode($template) . "/" . $string . "/" . rawurlencode($value));
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getGlobalSettings($value) {
        return $this->instance->rawRequest("settings/global/get/" . rawurlencode($value));
    }

    /**
     * @param $string
     * @param $value
     * @return mixed
     */
    public function setGlobalSettings($string, $value) {
        return $this->instance->rawRequest("settings/global/set/" . $string . "/" . rawurlencode($value));
    }

}