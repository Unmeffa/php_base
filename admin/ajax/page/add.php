<?php
include("../../include/variable_scripts.php");
header('Content-Type: application/json');
try {
    $handlePage = Page::handleEntity($_POST);
    echo json_encode(["success" => true, "message" => "Page ajoutée avec succès"]);
} catch (ErrorException $e) {
    echo json_encode(["success" => false, "errors" => ["Une erreur est survenue lors de l'ajout de la page"]]);
}
