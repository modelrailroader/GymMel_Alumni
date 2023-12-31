<?php
include 'constants.php';
include_once(__DIR__ . '/vendor/autoload.php');
include_once(__DIR__ . '/src/User.php');

use src\User;

$user = new User();
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($user->authenticateWithSession()) {
    header('Location: index.php');
    exit();
}

include 'header.php';
?>

            <div class="container">
            <?php
            if(isset($error_message)) {
                print($error_message);
            } ?>
            <div class="container mt-5 justify-content-center align-items-center" style="max-width: 500px; display: flex">
                <div class="login-form" style="width: 100%; border: 0.5px solid #ccc; border-radius: 10px; padding: 40px">
                    <h2>Admin-Login</h2>
                    <div style="margin-top: 20px"></div>
                    <p>Du kannst dich einloggen, um die Daten des Alumni-Formulars einzusehen.</p>
                    <div style="margin-top: 20px"></div>
                    <form action="index.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user"></i> Benutzername:
                            </label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Passwort:
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <p style="text-align: end"><a href="forgetPassword.php">Passwort vergessen?</a></p>
                        <div style="margin-top: 20px;"></div>
                        <button type="submit" id="submit" name="submit" class="btn btn-primary">Anmelden</button>
                    </form>
                </div>
              </div>
            </div>

            <div style="margin-top: 50px;"></div>

<?php
include 'footer.php';
