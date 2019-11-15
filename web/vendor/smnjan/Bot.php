<?php
/**
 * Created by Kuhva.
 */
namespace smnjan;

use PDOException;
use smnjan\ts3ab\Ts3AudioBot;

class Bot
{
    private $_botconn;
    private $_dbconn;

    /**
     * Bot constructor.
     * @param int $node
     */
    public function __construct($node = 1) {
        if($node == 0){
            $this->_dbconn = Database::getDB();
        } else {
            $config = Config::nodes;
            if (empty($config)){
                $config = Config::nodes[1];
            }
            $config = $config[$node];
            $this->_node = $node;
            $this->_botconn = new Ts3AudioBot($config['host'],$config['port']);
            $this->_botconn->basicAuth($config['key']);
            $this->_dbconn = Database::getDB();
        }
    }

    public static function getCount()
    {
        $db = Database::getDB();
        try{
            $stmt = $db->prepare("SELECT COUNT(*) FROM `bots`");
            $stmt->execute();
            return $stmt->fetch()[0];
        } catch (PDOException $e){
            error_log($e->getMessage());
        }
        return 0;
    }


    /**
     * Return list of all Bots
     *
     * @return array
     */
    public static function botlist(){
        try {
            $db = Database::getDB();
            $stmt = $db->prepare("SELECT * FROM `bots` ORDER BY id ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return array();
        }
    }

    /**
     * @param $nickname
     * @param $ip
     * @param $name
     * @param $serverpw
     * @return bool
     */
    public function createbot($nickname,$ip,$name,$serverpw){
        $bot = $this->_botconn;
        $template = $this->_node.'-'.time().'-'.self::generateRandomString(6);
        $new_bot = $bot->getCommandExecutor()->connectNew($ip,$serverpw);
        if (isset($new_bot['ErrorName']) || $this->cempty($new_bot['Id'])){
            error_log('BOT CREATE ERROR: '.json_encode($new_bot));
            return false;
        }
        $bot->getCommandExecutor()->use($new_bot['Id']);
        sleep(1);
        $bot->getCommandExecutor()->name($nickname);
        sleep(0.5);
        $bot->getCommandExecutor()->save($template);
        $bot->getCommandExecutor()->setBotSettings($template,'connect.name',$nickname);
        try {
            $sql = "INSERT INTO `bots` (`id`, `name`, `interface_name`, `template`, `server`, `node`, `botid`, `is_online`) VALUES (NULL, :name, :interface_name, :template, :server, :node, :botid, '1')";
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array(
                'name' => $nickname,
                'interface_name' => $name,
                'template' => $template,
                'server' => $ip,
                'node'  => $this->_node,
                'botid' => $new_bot['Id']
            ));
            if ($stmt->rowCount() == 1) {
                return true;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
        return false;
    }

    /**
     * @param $id int
     * @return bool
     */
    public function startBot($id){
        $bot = $this->getById($id);
        if (empty($bot)){
            return false;
        }
        try {
            $botc = $this->_botconn;
            $this->_botconn->getCommandExecutor()->setBotSettings($bot['template'],'connect.channel',$bot['default_channel']);
            $newid = $botc->getCommandExecutor()->connectTo($bot['template']);
            sleep(1);
            if (isset($newid['ErrorName']) || $this->cempty($newid['Id'])){
                return false;
            }
            $newid = $newid['Id'];
            $sql = "UPDATE `bots` SET `is_online` = '1' WHERE `id` = :id;";
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('id' => $id));
            self::updateBId($newid,$bot['template']);
            $botc->getCommandExecutor()->use($newid);
            $botc->getCommandExecutor()->volume($bot['audio.volume']);
            $botc->getCommandExecutor()->play($bot['audio.stream']);
            $botc->getCommandExecutor()->name($bot['name']);
            if ($bot['channel_commander']){
                $botc->getCommandExecutor()->makeCommander();
            }
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Toggle ChannelCommander
     *
     * @param $id int BotDatabaseID
     * @param $cm bool true to activate ccm false to deactivate
     * @return bool true if bot can get ccm false if fails or permission missing
     */
    public function setCCM($id,$cm){
        $bot = $this->getById($id);
        if (empty($bot)){
            return false;
        }
        if ($bot['channel_commander'] == $cm){
            return true;
        }
        $botc = $this->_botconn;
        $botc->getCommandExecutor()->use($bot['botid']);
        if ($cm){
            $ret = $botc->getCommandExecutor()->makeCommander();
        } else {
            $ret = $botc->getCommandExecutor()->takeCommander();
        }
        if ($ret == NULL){
            try {
                $sql = "UPDATE `bots` SET `channel_commander` = :ccm WHERE `id` = :id;";
                $stmt = $this->_dbconn->prepare($sql);
                $stmt->execute(array('id' => $id,'ccm' => $cm));
                return true;
            } catch (PDOException $e) {
                error_log($e->getMessage());
            }
        }
        return false;
    }

    public function stopBot($id){
        $bot = $this->getById($id);
        if (empty($bot)){
            return false;
        }
        try {
            $sql = "UPDATE `bots` SET `is_online` = '0' WHERE `id` = :id;";
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('id' => $id));
            $botc = $this->_botconn;
            $botc->getCommandExecutor()->use($bot['botid']);
            $botc->getCommandExecutor()->disconnect();
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function changeNickname($id,$name){
        if (strlen($name) > 30){
            return false;
        }
        $bot = $this->getById($id);
        if (empty($bot)){
            return false;
        }
        $name = strip_tags($name);
        try {
            $sql = 'UPDATE `bots` SET `name` = :name WHERE `id` = :id;';
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('id' => $id,'name' => $name));
            $botc = $this->_botconn;
            $botc->getCommandExecutor()->setBotSettings($bot['template'],'connect.name',$name);
            if ($bot['botid'] !== NULL){
                $botc->getCommandExecutor()->use($bot['botid']);
                $botc->getCommandExecutor()->name($name);
            }
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function setVolume($id,$volume){
        $bot = $this->getById($id);
        if (empty($bot)){
            return false;
        }
        if ($volume == $bot['audio.volume']){
            return true;
        }
        try {
            $sql = 'UPDATE `bots` SET `audio.volume` = :volume WHERE `id` = :id;';
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('id' => $id,'volume' => $volume));
            $botc = $this->_botconn;
            $botc->getCommandExecutor()->use($bot['botid']);
            $botc->getCommandExecutor()->volume((int)$volume);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function changeServer($id,$server){
        $bot = $this->getById($id);
        if (empty($bot)){
            return false;
        }
        try {
            $sql = 'UPDATE `bots` SET `server` = :server WHERE `id` = :id;';
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('id' => $id,'server' => $server));
            $this->_botconn->getCommandExecutor()->setBotSettings($bot['template'],'connect.address',$server);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function playURL($id,$url,$old){
        $bot = $this->getById($id);
        if (empty($bot)){
            return false;
        }
        if ($url == $old){
            return true;
        }
        try {
            $sql = 'UPDATE `bots` SET `audio.stream` = :stream WHERE `id` = :id;';
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('id' => $id,'stream' => $url));
            $botc = $this->_botconn;
            $botc->getCommandExecutor()->setBotSettings($bot['template'],'events.onconnect',"!play ".$url);
            $botc->getCommandExecutor()->setBotSettings($bot['template'],'events.onidle',"!play ".$url);
            if ($bot['botid'] !== NULL){
                $botc->getCommandExecutor()->use($bot['botid']);
                $botc->getCommandExecutor()->play($url);
            }
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getByTemplate($template){
        try {
            $sql = 'SELECT * FROM `bots` WHERE template = :template LIMIT 1';
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('template' => $template));
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return array();
        }
    }

    public static function getById($id){
        try {
            $db = Database::getDB();
            $sql = 'SELECT * FROM `bots` WHERE id = :id LIMIT 1';
            $stmt =$db->prepare($sql);
            $stmt->execute(array('id' => $id));
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return array();
        }
    }

    public function updateBId($id,$template){
        try {
            $sql = 'UPDATE `bots` SET `botid` = :bid WHERE `template` = :template;';
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('bid' => $id,'template' => $template));
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function setOffline($template){
        try {
            $sql = "UPDATE `bots` SET `botid` = NULL, `is_online` = '0' WHERE `template` = :template;";
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('template' => $template));
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function setBotOffline($id){
        try {
            $sql = "UPDATE `bots` SET `is_online` = '0' WHERE `id` = :id;";
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('id' => $id));
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function setBotOnline($id){
        try {
            $sql = "UPDATE ` bots` SET `is_online` = '1' WHERE `id`= :id;";
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('id' => $id));
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function deleteBot($id){
        $botdb = $this->getById($id);
        if ($botdb == null){
            return false;
        }
        $bot = $this->_botconn;
        $templates = $this->listTemplates();
        if (self::botOnline($botdb['template'],$templates)){
            $botid = self::findbyTemplate($botdb['template'],$templates);
            if (isset($botid['Id'])){
                $bot->getCommandExecutor()->use($botid['Id']);
                $bot->getCommandExecutor()->disconnect();
            }
        }
        try {
            $sql = 'DELETE FROM `bots` WHERE `id` = :id';
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('id' => $id));
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function pauseMusic($id){
        $bot = $this->getById($id);
        if (empty($bot)){
            return false;
        }
        $botc = $this->_botconn;
        $botc->getCommandExecutor()->use($bot['botid']);
        $botc->getCommandExecutor()->pause();
        return true;
    }

    public function resumeMusic($id){
        $bot = $this->getById($id);
        if (empty($bot)){
            return false;
        }
        $botc = $this->_botconn;
        $botc->getCommandExecutor()->use($bot['botid']);
        $botc->getCommandExecutor()->unpause();
        return true;
    }

    public function getBot(){
        return $this->_botconn;
    }

    public function listTemplates(){
        $array = $this->_botconn->getCommandExecutor()->listBots();
        if (isset($array['ErrorName'])){
            return array();
        }
        if($array == null){
            return array();
        }
        $botlist = array();
        foreach ($array as $value){
            $botlist[$value['Name']] = $value;
        }
        return $botlist;
    }

    public function findbyTemplate($template,$botlist){
        if (isset($botlist[$template])){
            return $botlist[$template];
        }
        return array();
    }

    public function botOnline($template,$botlist){
        $botlist = self::findbyTemplate($template,$botlist);
        if (isset($botlist['Name'])){
            if($botlist['Status'] == 2){
                return true;
            }
        }
        return false;
    }

    public function changePassword($id,$passowrd){
        $bot = self::getById($id);
        if (empty($bot)){
            return false;
        }
        if ($passowrd == $bot['host_password']){
            return true;
        }
        try {
            $sql = 'UPDATE `bots` SET `host_password` = :host_password WHERE `id` = :id;';
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('id' => $id,'host_password' => $passowrd));
            $this->_botconn->getCommandExecutor()->setBotSettings($bot['template'],'connect.server_password.pw',$passowrd);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function changeSChannel($id,$channel){
        $bot = self::getById($id);
        if (empty($bot)){
            error_log('BOT NOT FOUND');
            return false;
        }
        if ($channel === ''){
            $channel = ' ';
        }
        if (is_numeric($channel)){
            $channel = '/'.$channel;
        }
        try {
            $sql = 'UPDATE `bots` SET `default_channel` = :default_channel WHERE `id` = :id;';
            $stmt = $this->_dbconn->prepare($sql);
            $stmt->execute(array('id' => $id,'default_channel' => $channel));
            error_log(json_encode($this->_botconn->getCommandExecutor()->setBotSettings($bot['template'],'connect.channel',$channel)));
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public static function getUserQuickPlay(){
        return json_decode(Auth::getInstance()->getCurrentUser()->private_streamurl,true);
    }

    public static function getQuickPlay(){
        // :thinking::
    }

    private function cempty($empty){
        if (empty($empty)){
            if ($empty == 0){
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}