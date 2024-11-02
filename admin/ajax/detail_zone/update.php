<?php

include("../../include/variable_scripts.php");
$errors = [];

try {

    $zone = Zone::getZone($_POST["id"]);
    if (!$zone || $zone->get("id") === null) {
        $errors[] = "La zone demandée est introuvable.";
    }

    $zone->set('gabarit', $_POST['gabarit']);
    $zone->set('subtype', $_POST['subtype']);
    $zone->set('headtype', $_POST['headtype']);

    $zone->update();

    foreach ($_POST['name'] as $locale => $name) {

        $zoneDetailId = $_POST['zoneDetailId'][$locale] ?? null;
        $detail = [
            'id' => $zoneDetailId,
            'locale' => $locale,
            'name' => $name,
            'hName' => $_POST['hName'][$locale],
            'description' => $_POST['description'][$locale],
            'updatedAt' => date('Y-m-d H:i:s'),
            'zoneId' => $_POST['id'],
        ];

        $zoneDetail = new ZoneDetail($detail);
        try {
            if (!$zoneDetailId) {
                $zoneDetail->create();
            } else {
                $zoneDetail->update();
            }
        } catch (Exception $e) {
            $errors[] = "Erreur dans la gestion des details pour la langue '$locale'. \n" . $e->getMessage();
        }
    }
} catch (Exception $e) {
    $errors[] = "Erreur inattendue : \n" . $e->getMessage();
}

$response = [];
if (empty($errors)) {
    $response = [
        "success" => true,
    ];
} else {
    $response = [
        "success" => false,
        "errorsHtml" => renderErrorMessages($errors)
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
