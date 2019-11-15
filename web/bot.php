<?php
/**
 * Created by Kuhva.
 */
require 'vendor/autoload.php';
use smnjan\Auth;
use smnjan\Bot;
use smnjan\Config;
$auth = new Auth();
$auth->requireLogin();
if (isset($_GET['id'])){
    $error = false;
    $online = false;
    $botdb = Bot::getById($_GET['id']);
    if (empty($botdb)){
        header('Location: bots.php');
    } else {
        $botsys = new Bot($botdb['node']);
        if ($botdb['botid'] == null){
            $error = true;
        } else {
            $bot = $botsys->getBot();
            $bot->getCommandExecutor()->use($botdb['botid']);
            $botinfo = $bot->getCommandExecutor()->info();
            if (isset($botinfo['ErrorCode'])){
                $error = true;
            } elseif ($botinfo['Name'] !== $botdb['template']){
                $error = true;
            } else {
                $online = true;
                $botsys->setBotOnline($botdb['id']);
                $botsettings['connect'] = $bot->getCommandExecutor()->getBotSettings($botinfo['Name'],'connect');
                $botsettings['song'] = $bot->getCommandExecutor()->song();
                $botsettings['volume'] = $botdb['audio.volume'];
                $botsettings['channel_commander'] = $botdb['channel_commander'];
            }
        }
        if ($error){
            $list = $botsys->getBot()->getCommandExecutor()->listBots();
            $key = array_search($botdb['template'],array_column($list,'Name'));
            if ($key !== false){
                if($list[$key]['Status'] === 2){
                    $online = true;
                    $botsys->setBotOnline($botdb['id']);
                    $botsys->updateBId($list[$key]['Id'],$botdb['template']);
                    $bot = $botsys->getBot();
                    $bot->getCommandExecutor()->use($list[$key]['Id']);
                    $botsettings['connect'] = $bot->getCommandExecutor()->getBotSettings($botdb['template'],'connect');
                    $botsettings['song'] = $bot->getCommandExecutor()->song();
                    $botsettings['volume'] = $botdb['audio.volume'];
                    $botsettings['channel_commander'] = $botdb['channel_commander'];
                } else {
                    $online = false;
                    $botsys->setOffline($botdb['template']);
                    $bot = $botsys->getByTemplate($botdb['template']);
                    $botsettings['connect'] = array('name' => $bot['name'], 'address' => $bot['server'],"server_password" => array("pw" => $bot['host_password']),'channel' => $bot['default_channel']);
                    $botsettings['QueryConnection'] = array('DefaultChannel' => $bot['default_channel']);
                    $botsettings['song'] = $bot['audio.stream'];
                    $botsettings['volume'] = $bot['audio.volume'];
                    $botsettings['channel_commander'] = $bot['channel_commander'];
                }
            } else {
                $online = false;
                $botsys->setOffline($botdb['template']);
                $bot = $botsys->getByTemplate($botdb['template']);
                $botsettings['connect'] = array('name' => $bot['name'], 'address' => $bot['server'],"server_password" => array("pw" => $bot['host_password']),'channel' => $bot['default_channel']);
                $botsettings['QueryConnection'] = array('DefaultChannel' => $bot['default_channel']);
                $botsettings['song'] = $bot['audio.stream'];
                $botsettings['volume'] = $bot['audio.volume'];
                $botsettings['channel_commander'] = $bot['channel_commander'];
            }
        }
    }
} else {
    header('Location: dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <title>Musicbot | <?= $botdb['interface_name'] ?></title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <link href="assets/css/sweetalert2.min.css" rel="stylesheet" />
    <link href="assets/css/black-dashboard.css?v=1.0.0" rel="stylesheet" />
    <link href="assets/css/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body>
<div class="wrapper">
    <div class="sidebar" data-color="red">
        <div class="sidebar-wrapper">
            <ul class="nav">
                <li>
                    <a href="./dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="active">
                    <a href="./bots.php">
                        <i class="fas fa-hdd"></i>
                        <p>Musicbots</p>
                    </a>
                </li>
                <li>
                    <a href="./account.php">
                        <i class="far fa-user"></i>
                        <p>Profil</p>
                    </a>
                </li>
            </ul>
            <div style="position: absolute; bottom: 80px;width: 100%;height: 75px;background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAccAAACKCAYAAADfTYG0AAAACXBIWXMAAAsTAAALEwEAmpwYAAAHtmlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDIgNzkuMTYwOTI0LCAyMDE3LzA3LzEzLTAxOjA2OjM5ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtbG5zOnN0RXZ0PSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VFdmVudCMiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIiB4bWxuczpwaG90b3Nob3A9Imh0dHA6Ly9ucy5hZG9iZS5jb20vcGhvdG9zaG9wLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0iMjQ5QzM4NzkzNkRCMzBENEUxRDdDODlCRTUyM0NBOEMiIHhtcE1NOkRvY3VtZW50SUQ9ImFkb2JlOmRvY2lkOnBob3Rvc2hvcDphMmQ3OTE1NS1kZDBkLWM1NGUtYjY1Ny03ZTYyZjY1ZWUwNjciIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6ZTExYjViNGItMzc1Ni1hODQzLWI2OWUtOGRlNTU3MGVmZmQzIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE3IChXaW5kb3dzKSIgeG1wOkNyZWF0ZURhdGU9IjIwMTgtMTItMjRUMDI6MDY6MDcrMDE6MDAiIHhtcDpNb2RpZnlEYXRlPSIyMDE5LTAzLTIxVDE0OjQ4OjA1KzAxOjAwIiB4bXA6TWV0YWRhdGFEYXRlPSIyMDE5LTAzLTIxVDE0OjQ4OjA1KzAxOjAwIiBkYzpmb3JtYXQ9ImltYWdlL3BuZyIgcGhvdG9zaG9wOkNvbG9yTW9kZT0iMyIgcGhvdG9zaG9wOklDQ1Byb2ZpbGU9InNSR0IgSUVDNjE5NjYtMi4xIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MDE0NmQ3ZjEtMDBiYy00MDRlLWI2ODgtNDNmM2Y5MTFmYTAxIiBzdFJlZjpkb2N1bWVudElEPSJhZG9iZTpkb2NpZDpwaG90b3Nob3A6NmE2NDQyNjgtNjhkMy1hNjQ1LWFkYWYtYjI3YTAzODRhNWY1Ii8+IDx4bXBNTTpIaXN0b3J5PiA8cmRmOlNlcT4gPHJkZjpsaSBzdEV2dDphY3Rpb249InNhdmVkIiBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOjYzYjdiZjdhLWI2NGYtYjM0Zi1hZGNhLThmNzljMDNhNjFmNiIgc3RFdnQ6d2hlbj0iMjAxOC0xMi0yNFQwMjoxMzowNyswMTowMCIgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTggKFdpbmRvd3MpIiBzdEV2dDpjaGFuZ2VkPSIvIi8+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJzYXZlZCIgc3RFdnQ6aW5zdGFuY2VJRD0ieG1wLmlpZDplMTFiNWI0Yi0zNzU2LWE4NDMtYjY5ZS04ZGU1NTcwZWZmZDMiIHN0RXZ0OndoZW49IjIwMTktMDMtMjFUMTQ6NDg6MDUrMDE6MDAiIHN0RXZ0OnNvZnR3YXJlQWdlbnQ9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE4IChXaW5kb3dzKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8L3JkZjpTZXE+IDwveG1wTU06SGlzdG9yeT4gPHBob3Rvc2hvcDpEb2N1bWVudEFuY2VzdG9ycz4gPHJkZjpCYWc+IDxy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);overflow: hidden;background-size: contain;background-repeat: no-repeat;cursor: pointer" onclick="window.location = 'https://smnjan.de';"></div>
        </div>
    </div>
    <div class="main-panel">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-absolute navbar-transparent">
            <div class="container-fluid">
                <div class="navbar-wrapper">
                    <div class="navbar-toggle d-inline">
                        <button type="button" class="navbar-toggler">
                            <span class="navbar-toggler-bar bar1"></span>
                            <span class="navbar-toggler-bar bar2"></span>
                            <span class="navbar-toggler-bar bar3"></span>
                        </button>
                    </div>
                    <img class="navbar-brand" src="assets/img/logo.png" style="margin-left: 0; width: 15rem">
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                    <span class="navbar-toggler-bar navbar-kebab"></span>
                </button>
                <div class="collapse navbar-collapse" id="navigation">
                    <ul class="navbar-nav ml-auto">
                        <li class="dropdown nav-item">
                            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                                <div class="photo">
                                    <img src="assets/img/profile.png" alt="Profile Photo">
                                </div>
                                <b class="caret d-none d-lg-block d-xl-block"></b>
                                <p class="d-lg-none">Abmelden</p>
                            </a>
                            <ul class="dropdown-menu dropdown-navbar">
                                <li class="nav-link">
                                    <a href="account.php" class="nav-item dropdown-item"><?= ucfirst($user['username']) ?></a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li class="nav-link">
                                    <a href="logout.php" class="nav-item dropdown-item">Abmelden</a>
                                </li>
                            </ul>
                        </li>
                        <li class="separator d-lg-none"></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="content">
            <div class="row">
                <div class="col-md-8">
                    <div class="card pb-3">
                        <div class="card-header">
                            <h4>Verbindung</h4>
                        </div>
                        <form id="connectionsettings">
                            <input type="hidden" name="botid" value="<?= $_GET['id'] ?>">
                            <input type="hidden" name="method" value="connection">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6  pr-md-1">
                                        <div class="form-group">
                                            <label>Teamspeak Nickname</label>
                                            <input type="text" class="form-control" name="nickname" required="required" placeholder="<?= $botsettings['connect']['name'] ?>" value="<?= $botsettings['connect']['name'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6  pr-md-1">
                                        <div class="form-group">
                                            <label>Teamspeak Server</label>
                                            <input type="text" class="form-control" name="server" required="required" placeholder="<?= $botsettings['connect']['address'] ?>" value="<?= $botsettings['connect']['address'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6  pr-md-1">
                                        <div class="form-group">
                                            <label>Teamspeak Passwort</label>
                                            <input type="text" class="form-control" name="hostpassword" placeholder="<?= $botsettings['connect']['server_password']['pw'] ?>" value="<?= $botsettings['connect']['server_password']['pw'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6  pr-md-1">
                                        <div class="form-group">
                                            <label>Standart Channel</label>
                                            <?php if (strpos($botsettings['connect']['channel'], '/') === 0) {
                                                $botsettings['connect']['channel'] = ltrim($botsettings['connect']['channel'],'/');
                                            } ?>
                                            <input type="text" class="form-control" name="default_channel" placeholder="<?= $botsettings['connect']['channel'] ?>" value="<?= $botsettings['connect']['channel'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <?php if ($online){ ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Song</label>
                                                <?php
                                                $req = $botsettings['song'];
                                                if (isset($req['ErrorName'])){
                                                    $song =$req['ErrorMessage'];
                                                } else {
                                                    $song = $req['Title'];
                                                }?>
                                                <input type="text" class="form-control" id="currentsong" readonly="readonly" value="<?= $song ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-fill btn-primary float-right">Übernehmen</button>
                            </div>
                        </form>
                    </div>
                    <div class="card pb-3">
                        <div class="card-header">
                            <h4>Musik</h4>
                        </div>
                        <form id="audiosettings">
                            <input type="hidden" name="botid" value="<?= $_GET['id'] ?>">
                            <input type="hidden" name="method" value="audio">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Stream URL</label>
                                            <?php
                                            if ($online){
                                                $req = $botsettings['song'];
                                                if (isset($req['ErrorName'])){
                                                    $stream = 'value=""';
                                                } else {
                                                    $stream = 'value='.$req['Link'];
                                                }
                                            } else {
                                                $stream = 'value='.$botsettings['song'];
                                            } ?>
                                            <input type="text" class="form-control" name="streamurl" required="required" placeholder="STREAM LINK EINTRAGEN" <?= $stream ?>>
                                            <input type="hidden" name="oldstream" <?= $stream ?>>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($online){ ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Lautstärke</label>
                                                <input type="range" min="0" max="100" value="<?= $botsettings['volume'] ?>" class="volume-slider" id="myRange" name="volume">
                                                <small id="volume-display">Lautstärke: <?= $botsettings['volume'] ?>%</small>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="checkbox" value="true" name="channelcommander" id="cccb" <?php if ($botsettings['channel_commander']){echo "checked";} ?>>
                                            <label for="cccb">Channelcomander</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="float-left"  data-bot-id="<?= $_GET['id']; ?>">
                                    <button class="btn btn-fill btn-primary do-playmusic"><i class="fas fa-play"></i></button>
                                    <button class="btn btn-fill btn-primary do-pausemusic"><i class="fas fa-pause"></i></button>
                                </div>
                                <button type="submit" class="btn btn-fill btn-primary float-right">Übernehmen</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Informationen</h4>
                        </div>
                        <div class="card-body text-white">
                            <table class="w-75 m-auto">
                                <tr>
                                    <td class="float-left">Status:</td>
                                    <?php if ($online){ ?>
                                        <td class="float-right" style="color: green;">Online</td>
                                    <?php } else { ?>
                                        <td class="float-right" style="color: red">Offline</td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="float-left">Nickname:</td>
                                    <td class="float-right"><?= strip_tags($botsettings['connect']['name']) ?></td>
                                </tr>
                                <tr>
                                    <td class="float-left">Server:</td>
                                    <td class="float-right"><?= strip_tags($botsettings['connect']['address']) ?></td>
                                </tr>
                                <tr>
                                    <td class="float-left">ID:</td>
                                    <td class="float-right"><?= strip_tags($_GET['id']) ?></td>
                                </tr>
                                <tr>
                                    <td class="float-left">Server:</td>
                                    <td class="float-right"><?= Config::nodes[$botdb['node']]['name']?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer row" style="text-align: center;margin: auto" data-bot-id="<?= $_GET['id']; ?>">
                            <div class="btn-group">
                                <?php if($online){ ?>
                                    <button type="submit" class="btn btn-fill btn-warning float-right do-botstop" id="bot-stop">Stoppen</button>
                                <?php } else { ?>
                                    <button type="submit" class="btn btn-fill btn-success float-right do-botstart" id="bot-start">Starten</button>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Bot Löschen</h4>
                        </div>
                        <div class="card-footer row" style="text-align: center;margin: auto;" data-bot-id="<?= $_GET['id']; ?>">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-fill btn-danger float-right do-botdelete">Musicbot löschen</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'assets/footer.php'?>
    </div>
    <!--   Core JS Files   -->
    <script src="assets/js/core/jquery.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
    <script src="assets/js/plugins/sweetalert2.min.js"></script>
    <script src="assets/js/plugins/bootstrap-notify.js"></script>
    <script src="assets/js/black-dashboard.min.js?v=1.0.0"></script>
    <script src="assets/js/smnjan.js"></script>
    <script src="assets/js/musicbot.js"></script>
    <script>
        $('.qp-button').click(function (event) {
            event.preventDefault();
        });
    </script>
</body>

</html>