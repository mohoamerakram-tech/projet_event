<?php
require_once "../config/db.php";

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "Connexion à la base réussie !";
} else {
    echo "Échec de connexion.";
}
?>
