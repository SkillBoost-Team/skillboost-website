<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


?>

<?php
class FormationController {
    public static function getAllFormations() {
        return FormationModel::getAllFormations();
    }
}
?>