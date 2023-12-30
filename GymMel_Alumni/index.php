<?php
include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/User.php');
include_once(__DIR__.'/src/Logs.php');
include_once(__DIR__.'/src/Alert.php');
include_once(__DIR__.'/src/DataHelper.php');

use A1phanumeric\DBPDO;
use src\User;
use src\Logs;
use src\Alert;
use src\DataHelper;

$logs = new Logs();
$alert = new Alert();
$dataHelper = new DataHelper();

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbclient = new DBPDO($db_host, $db_name, $db_user, $db_password);
$return = null;

$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$studies = filter_input(INPUT_POST, 'studies', FILTER_SANITIZE_SPECIAL_CHARS);
$job = filter_input(INPUT_POST, 'job', FILTER_SANITIZE_SPECIAL_CHARS);
$company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_SPECIAL_CHARS);
$privacy_checkbox = filter_input(INPUT_POST, 'data-privacy');
$transfer_checkbox = filter_input(INPUT_POST, 'transfer-privacy');
$submit = filter_input(INPUT_POST, 'submit');

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
$password = filter_input(INPUT_POST, 'password');

$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
$userid = filter_input(INPUT_POST, 'userid', FILTER_VALIDATE_INT);
$twofactor_code = filter_input(INPUT_POST, 'twofactor_code', FILTER_VALIDATE_INT);

$user = new User();
$user->authenticateWithSession();

// If login-form-data is submitted
if(isset($username) && isset($password)) {
    // @todo: SQL Injection und weitere Sicherheitsstandards
    
    // try to login
    if(($user->authenticate($username, $password))) {
        if($user->isTwofactorEnabled()) {
            header('Location: twofactor_code.php?userid=' . $user->getUserId());
            exit();
        }
        else {
            $logs->addLogEntry('The user was logged in.');
            $logs->updateLastLogin($_SESSION['userid']);
        }
    }
    else {
        $error_message = $alert->dangerAlert('Der eingegebene Benutzername oder das Passwort ist falsch! Sollten Sie sich bereits 10 Mal erfolglos angemeldet haben, ist der Login gesperrt. '
                . 'In diesem Fall fordern Sie bitte ein neues Passwort über <a href="forgetPassword.php">Passwort vergessen</a> an.');
        include 'login.php';
        exit();
          
    }
}

// If twofactor code is submitted
if($action === 'twofactor') {
    if(!$user->validateTwofactorCode($userid, $twofactor_code)) {
        $success_message = $alert->dangerAlert('Der Code war nicht korrekt!');
        header('Location: twofactor_code.php?userid=' . $userid . '&error=1');
        exit();
    }
}

// If alumni-form-data is submitted
if(isset($submit) && isset($privacy_checkbox)) {
    if(isset($transfer_checkbox)) {
        $transfer = 1;
    }
    else {
        $transfer = 0;
    }
    $dataHelper->saveNewAlumni($name, $email, $studies, $job, $company, $transfer);

    $success_message = $alert->successAlert('Danke fürs Eingeben deiner Daten!<br>Hast du schon unseren Image-Film für die Ehemaligen-Party gesehen? Nein? Dann <a href="https://www.melle-gymnasium.de/Schulfilm">hier</a> entlang!');
}

include 'header.php';
?>

            <div class="container mt-5">
            <?php
            if(isset($success_message)) {
                echo($success_message);
                unset($success_message);
            }?>
                <h2>Alumni werden</h2>
                <div style="margin-top: 20px"></div>
                <p>Danke, dass du Interesse hast, unserem Alumni-Netzwerk beizutreten. Fülle dazu einfach das Formular aus.</p>
                <div style="margin-top: 20px"></div>
                <form action="<?php print($_SERVER['PHP_SELF']) ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person"></i>&nbsp; Vor- und Nachname:
                        </label>
                        <div class="required-field-block">
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="required-icon">
                                <div class="text">*</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope-at"></i>&nbsp; E-Mail-Adresse:
                        </label>
                        <div class="required-field-block">
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="required-icon">
                                <div class="text">*</div>
                            </div>
                        </div>
                    </div>
                    <p>Wir würden uns freuen, wenn du uns auch Daten zu deinem beruflichen Werdegang mitteilst. Wir würden dich ggf. bei Bedarf gerne zum Zweck der Berufsorientierung kontaktieren.</p>
                    <div class="mb-3">
                        <label for="studies" class="form-label">
                            <i class="bi bi-book"></i>&nbsp; Studium/Ausbildung:
                        </label>
                        <input type="text" class="form-control" id="studies" name="studies">
                    </div>
                    <div class="mb-3">
                        <label for="job" class="form-label">
                            <i class="bi bi-briefcase"></i>&nbsp; Beruf/Tätigkeit:
                        </label>
                        <input type="text" class="form-control" id="job" name="job">
                    </div>
                    <div class="mb-3">
                        <label for="company" class="form-label">
                            <i class="bi bi-building"></i>&nbsp; Aktueller Arbeitgeber:in:
                        </label>
                        <input type="text" class="form-control" id="company" name="company">
                    </div>
                    <p style="font-size: 14px"><span style="color: red">*</span> Pflichtfeld</p>
                    <div style="margin-top: 30px"></div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="" id="data-privacy" name="data-privacy" value="agreed" required>
                        <label class="form-check-label" for="data-privacy">
                            Ich habe die <a href="privacy.php">Datenschutzerklärung</a> zur Kenntnis genommen und stimme der Verarbeitung meiner Daten zu. <span style="color: red">*</span>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="" id="transfer-privacy" name="transfer-privacy" value="agreed">
                        <label class="form-check-label" for="transfer-privacy">
                            Zusätzlich stimme ich der Weitergabe meiner Daten insbesondere meiner E-Mail-Adresse an Schüler:innen zur Kontaktaufnahme zu. Meine Einwilligung kann ich jederzeit widerrufen.
                        </label>
                    </div>
                    <div style="margin-top: 20px"></div>
                    <button type="submit" id="submit" name="submit" class="btn btn-primary"><i class="bi bi-send"></i> Absenden</button>
                </form>
            </div>

            <div style="margin-top: 50px;"></div>
            
            <script>
                document.title = 'Alumni werden | Gymnasium Melle';
            </script>

<?php
include 'footer.php';
