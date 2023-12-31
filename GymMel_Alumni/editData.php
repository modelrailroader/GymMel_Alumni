<?php
include 'constants.php';
include_once(__DIR__ . '/vendor/autoload.php');
include_once(__DIR__ . '/src/User.php');
include_once(__DIR__ . '/src/DataHelper.php');
include_once(__DIR__ . '/src/Logs.php');

use src\User;
use src\DataHelper;
use src\Logs;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if (!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$dataHelper = new DataHelper();
$logs = new Logs();

$dataid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
$studies = filter_input(INPUT_POST, 'studies', FILTER_SANITIZE_SPECIAL_CHARS);
$job = filter_input(INPUT_POST, 'job', FILTER_SANITIZE_SPECIAL_CHARS);
$company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_SPECIAL_CHARS);
$transfer_privacy = filter_input(INPUT_POST, 'transfer_privacy');
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$submit = filter_input(INPUT_POST, 'submit');

if(!is_null($dataid)) {
    $data = $dataHelper->getAlumniData($dataid);
}

if(isset($submit)) {
    $data_change = array(
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'studies' => $studies,
        'job' => $job,
        'company' => $company,
        'transfer_privacy' => ($transfer_privacy === 'on') ? 1 : 0
    );
    if($dataHelper->updateData($data_change)) {
        $success_message = '<div class="alert alert-success" role="alert">
          Die Daten wurden erfolgreich geändert!
        </div>';
        $logs->addLogEntry('The data of the alumni ' . $name . ' was successfully changed.');
    }
    $data = $dataHelper->getAlumniData($id);
}

if(is_null($submit) & is_null($dataid)) {
    require 'showData.php';
    exit();
}

include 'header.php';
?>

            <div class="container mt-5">
                <?php
                if(isset($success_message)) {
                    print($success_message);
                }
                ?>

                <h1>Datensatz bearbeiten</h1>
                <div style="margin-top: 30px"></div>
                <form action="<?php print($_SERVER['PHP_SELF']) ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person"></i>&nbsp; Vor- und Nachname:
                        </label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php print($data['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope-at"></i>&nbsp; E-Mail-Adresse:
                        </label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php print($data['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="ausbildung" class="form-label">
                            <i class="bi bi-book"></i>&nbsp; Studium/Ausbildung:
                        </label>
                        <input type="text" class="form-control" id="studies" name="studies" value="<?php print($data['studies']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="beruf" class="form-label">
                            <i class="bi bi-briefcase"></i>&nbsp; Beruf/Tätigkeit:
                        </label>
                        <input type="text" class="form-control" id="job" name="job" value="<?php print($data['job']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="firma" class="form-label">
                            <i class="bi bi-building"></i>&nbsp; Aktueller Arbeitgeber:in:
                        </label>
                        <input type="text" class="form-control" id="company" name="company" value="<?php print($data['company']) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="registrierungszeitpunkt" class="form-label">
                            <i class="bi bi-calendar-date"></i>&nbsp; Registrierung
                        </label>
                        <input type="text" class="form-control" id="date_registered" name="date_registered" value="<?php print(date('d.m.Y - H:i', $data['date_registered'])) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="transfer_privacy" name="transfer_privacy" <?php print($dataHelper->getTransferPrivacyFromData($data)) ?>>
                            <label class="form-check-label" for="datenschutz">Weitergabe der Daten an Schüler:innen</label>
                        </div>
                    </div>
                    <input type="hidden" id="id" name="id" value="<?php print($dataid) ?>">
                    <div style="margin-top: 20px"></div>
                    <button type="submit" id="submit" name="submit" class="btn btn-primary">Speichern</button>
                    <div style="margin-top: 50px"></div>
                </form>
            </div>

<?php 
include 'footer.php';



