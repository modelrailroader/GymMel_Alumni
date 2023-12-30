<?php
include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/User.php');
include_once(__DIR__.'/src/DataHelper.php');
include_once(__DIR__.'/src/Alert.php');
include_once(__DIR__.'/src/Logs.php');

use src\User;
use src\DataHelper;
use src\Alert;
use src\Logs;

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if(!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$dataHelper = new DataHelper();
$logs = new Logs();
$alert = new Alert();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

if($action === 'delete') {
    $deleted_alumni = $dataHelper->getAlumniData($id)['name'];
    if($dataHelper->deleteAlumniById($id)) {
        $success_message = $alert->successAlert('Der Datensatz wurde erfolgreich gelöscht.');
        $logs->addLogEntry('The data of the alumni ' . $deleted_alumni . ' was succesfully deleted.');
    }
}

include 'header.php';
?>
            
            <div class="container mt-5">
            <?php 
            if (isset($success_message)) {
                print($success_message);
            }
            ?>
            <h1>Netzwerkdatenbank</h1>
            <div style="margin-top: 40px"></div>

            <table id="alumniTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th data-priority="1">Name</th>
                        <th data-priority="2">E-Mail-Adresse</th>
                        <th>Beruf</th>
                        <th>Ausbildung</th>
                        <th>Unternehmen</th>
                        <th>Registrierung</th>
                        <th>Weitergabe der Daten</th>
                        <th data-priority="3" data-orderable="false">Bearbeiten</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    print($dataHelper->renderTree());
                    ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 50px;"></div>
        
        <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="js/jquery-3.6.0.min.js"></script>
        <script src="js/DataTables/datatables.min.js"></script>
        <script src="js/language datatables de-DE.json"></script>
        <script src="js/DataTables/dataTables.responsive.min.js"></script>
        
        <footer class="text-black text-center py-3" id="background-color-1">
            <p>Created by Jan Harms | <?php print(date('Y')) ?> &copy; <a href="https://www.melle-gymnasium.de">Gymnasium Melle</a> | <a href="https://www.melle-gymnasium.de/kontakt/#impressum">Impressum</a> | <a href="privacy.php">Datenschutz</a></p>
        </footer>
        <script>
            document.title = 'Netzwerkdatenbank | Alumni-Datenbank';
            
            $(document).ready(function() {
                var table = $('#alumniTable').DataTable({
                    "language": {
                        "url": "js/language datatables de-DE.json"
                    },
                    "dom": "Bflrtip",
                    responsive: true,
                    "buttons": [{
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                        }
                    }]
                });;
            });
        </script>
    </body>
</html>

