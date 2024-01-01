<?php
include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/User.php');

use src\User;

if(!isset($user)) {
    $user = new User();
}
?>

<!DOCTYPE html>
<html lang="en">
    <style>
        #background-color-1, #navbarNav {
            background-color: #f1f2ed !important;
        }

        .container {
            flex: 1;
            padding-top: 20px;
        }

        .required-field-block {
            position: relative;
        }

        .required-field-block .required-icon {
            display: inline-block;
            vertical-align: middle;
            margin: -0.25em 0.25em 0em;
            background-color: #E8E8E8;
            border-color: #E8E8E8;
            padding: 0.5em 0.8em;
            color: rgba(0, 0, 0, 0.65);
            text-transform: uppercase;
            font-weight: normal;
            border-radius: 0.325em;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            box-sizing: border-box;
            -webkit-transition: background 0.1s linear;
            -moz-transition: background 0.1s linear;
            transition: background 0.1s linear;
            font-size: 75%;
        }

        .required-field-block .required-icon {
            background-color: transparent;
            position: absolute;
            top: 0em;
            right: 0em;
            z-index: 10;
            margin: 0em;
            width: 30px;
            height: 30px;
            padding: 0em;
            text-align: center;
            -webkit-transition: color 0.2s ease;
            -moz-transition: color 0.2s ease;
            transition: color 0.2s ease;
        }

        .required-field-block .required-icon:after {
            position: absolute;
            content: "";
            right: 1px;
            top: 1px;
            z-index: -1;
            width: 0em;
            height: 0em;
            border-top: 0em solid transparent;
            border-right: 30px solid transparent;
            border-bottom: 30px solid transparent;
            border-left: 0em solid transparent;
            border-right-color: inherit;
            -webkit-transition: border-color 0.2s ease;
            -moz-transition: border-color 0.2s ease;
            transition: border-color 0.2s ease;
        }

        .required-field-block .required-icon .text {
            color: #B80000;
            font-size: 26px;
            margin: -3px 0 0 12px;
        }
        
        .dropdown-item:active {
            background-color: white !important;
            color: black !important;
        }
    </style>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

        <link rel="stylesheet" href="assets/dist/styles.css">
    </head>
    <body>
        <div class="content-wrapper" style="display: flex; flex-direction: column; min-height: 100vh">
            <header class="bg-primary text-white text-center pb-2 pt-1" id="background-color-1" style="height: auto">
                <div class="container pt-2">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <a href="index.php"><img src="img/logo.png" alt="Gymnasium Melle Logo" class="img-fluid" style="max-width: 150px;"></a>
                        </div>
                        <div class="col-md-10">
                            <nav class="navbar navbar-expand-lg navbar-dark">
                                <div class="container">
                                    <button class="navbar-toggler navbar-dark" style="--bs-navbar-color: #f1f2ed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                        <i class="bi bi-list" style="color: #868686; font-size: 30px"></i>
                                    </button>
                                    <div class="collapse navbar-collapse" id="navbarNav">
                                        <ul class="navbar-nav">
                                            <li class="nav-item">
                                                <a class="nav-link" style="color: #000" href="index.php">Alumni werden</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" style="color: #000" href="https://www.melle-gymnasium.de">Website der Schule</a>
                                            </li>
                                        </ul>
                                    <?php
                                    if ($user->isLoggedIn()) {
                                        print(
                                                '<div class="justify-content-end ms-auto">
                                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none; color: black">'
                                                    . $user->getUsername() . '</a>
                                                      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                                                        <li><a class="dropdown-item" href="showData.php">Netzwerkdatenbank</a></li>
                                                        <li><a class="dropdown-item" href="users.php">Benutzerübersicht</a></li>
                                                        <li><a class="dropdown-item" href="backup.php">Backup</a></li>
                                                        <li><a class="dropdown-item" href="Dokumentation.pdf">Dokumentation</a></li>
                                                        <li class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="ucp.php">Persönlicher Bereich</a></li>
                                                        <li><a class="dropdown-item" href="logout.php">Ausloggen</a></li>
                                            </div>');
                                    } else {
                                        print(
                                                '<ul class="navbar-nav ms-auto">
                                                    <li class="nav-item">
                                                    <a class="nav-link" style="color: #000" href="login.php">Einloggen</a>
                                                    </li>
                                                </ul>
                                            </div>');
                                    }
                                    ?>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </header>

