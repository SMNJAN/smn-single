<?php
/**
 * Created by Kuhva.
 */
require '../vendor/autoload.php';
use smnjan\Auth;
use smnjan\Bot;

$auth = new Auth();
$auth->requireLogin();
if (isset($_POST['method'],$_POST['botid'])){
    $botdb = Bot::getById($_POST['botid']);
    $botsys = new Bot($botdb['node']);
    switch ($_POST['method']){
        case "connection":
            if (isset($_POST['nickname'],$_POST['server'],$_POST['hostpassword'])){
                if ( $botsys->changeNickname($_POST['botid'],$_POST['nickname'])){
                    if ($botsys->changeServer($_POST['botid'],$_POST['server'])){
                        if ($botsys->changePassword($_POST['botid'],$_POST['hostpassword'])){
                            if ($botsys->changeSChannel($_POST['botid'],$_POST['default_channel'])){
                                echo "success";
                            } else {
                                echo "error";
                            }
                        } else {
                            echo "error";
                        }
                    } else {
                        echo "error";
                    }
                } else {
                    echo "error";
                }
            }
            break;
        case "audio":
            if (isset($_POST['streamurl'],$_POST['volume'])){
                if ( $botsys->playURL($_POST['botid'],$_POST['streamurl'],$_POST['oldstream'])){
                    if ($botsys->setVolume($_POST['botid'],$_POST['volume'])){
                        if(isset($_POST['channelcommander'])){
                            $cm = true;
                        } else {
                            $cm = false;
                        }
                        if ($botsys->setCCM($_POST['botid'],$cm)){
                            echo "success";
                        } else {
                            echo "error";
                        }
                    } else {
                        echo "error";
                    }
                } else {
                    echo "error";
                }
            }
            break;
        case "quickplay":
            if (isset($_POST['streamurl'])){
                if ( $botsys->playURL($_POST['botid'],$_POST['streamurl'],'')){
                    echo "success";
                } else {
                    echo "error";
                }
            }
            break;
        case "start":
            if ($botsys->startBot($_POST['botid'])){
                echo "success";
            } else {
                echo "error";
            }
            break;
        case "stop":
            if ($botsys->stopBot($_POST['botid'])){
                echo "success";
            } else {
                echo "error";
            }
            break;
        case "delete":
            if ($botsys->deleteBot($_POST['botid'])){
                echo "success";
            } else {
                echo "error";
            }
            break;
        case "pause":
            if ($botsys->pauseMusic($_POST['botid'])){
                echo "success";
            } else {
                echo "error";
            }
            break;
        case "resume":
            if ($botsys->resumeMusic($_POST['botid'])){
                echo "success";
            } else {
                echo "error";
            }
            break;
        default:
            echo "error";
            break;
    }

}