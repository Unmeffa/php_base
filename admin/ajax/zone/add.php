<?php
include("../../include/variable_scripts.php");
header('Content-Type: application/json');
try {
    $handleZone = Zone::handleEntity($_POST);
    echo json_encode(["success" => true, "message" => "Zone ajoutée avec succès"]);
} catch (ErrorException $e) {
    echo json_encode(["success" => false, "errors" => ["Une erreur est survenue lors de l'ajout de la zone" . $e->getMessage()]]);
}
