<?php

include("../../include/variable_scripts.php");
$errors = [];

try {

    $photo = Photo::getPhoto($_POST["id"]);
    if (!$photo || $photo->get("id") === null) {
        $errors[] = "La photo demandée est introuvable.";
    }
    foreach ($_POST['name'] as $locale => $name) {

        $photoDetailId = $_POST['photoDetailId'][$locale] ?? null;

        $detail = [
            'id' => $photoDetailId,
            'locale' => $locale,
            'name' => $name,
            'alt' => $_POST['alt'][$locale],
            'description' => $_POST['description'][$locale],
            'updatedAt' => date('Y-m-d H:i:s'),
            'photoId' => $_POST['id'],
        ];

        // Instanciation de PageDetail avec les détails fournis
        $photoDetail = new PhotoDetail($detail);
        try {
            if (!$photoDetailId) {
                $photoDetail->create();
            } else {
                $photoDetail->update();
            }
        } catch (Exception $e) {
            $errors[] = "Erreur dans la gestion des details de la photo pour la langue '$locale'. \n" . $e->getMessage();
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
        "errorsHtml" => ($errors)
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
