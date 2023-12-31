<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/User.php');
include_once(__DIR__.'/src/Alert.php');

use src\User;
use src\Alert;
$user = new User();
$alert = new Alert();

if ($user->authenticateWithSession()) {
    header('Location: index.php');
    exit();
}

$userid = filter_input(INPUT_GET, 'userid', FILTER_VALIDATE_INT);
$error = filter_input(INPUT_GET, 'error', FILTER_VALIDATE_INT);

if($error === 1) {
    $success_message = $alert->dangerAlert('Der Code war nicht korrekt!');
}

include 'header.php';
?>

            <div class="container">
            <?php
            if(isset($success_message)) {
                print($success_message);
            } ?>
            <div class="container mt-5 justify-content-center align-items-center" style="max-width: 500px; display: flex">
                <div class="login-form" style="width: 100%; border: 0.5px solid #ccc; border-radius: 10px; padding: 40px">
                    <h2>2-Faktor-Authentifizierung</h2>
                    <div style="margin-top: 20px"></div>
                    <p>Gib den Code aus deiner Authenticator-App ein, um Zugriff zu erhalten.</p>
                    <div style="margin-top: 20px"></div>
                    <form action="index.php" method="POST">
                        <input type="hidden" name="userid" value="<?php print($userid) ?>" id="userid">
                        <input type="hidden" id="action" value="twofactor" name="action">
                        <div class="mb-3">
                            <label for="twofactor_code" class="form-label">
                                <i class="bi bi-key"></i> Code:
                            </label>
                            <input type="text" class="form-control" id="twofactor_code" name="twofactor_code" required>
                        </div>
                        <div style="margin-top: 20px;"></div>
                        <button type="submit" class="btn btn-primary">Anmelden</button>
                    </form>
                </div>
              </div>
            </div>

            <div style="margin-top: 50px;"></div>

<?php
include 'footer.php';
