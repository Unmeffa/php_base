<?php

include("../../include/variable_scripts.php");
$errors = [];

try {

    $page = Page::getPage($_POST["id"]);
    if (!$page || $page->get("id") === null) {
        $errors[] = "La page demandée est introuvable.";
    }
    foreach ($_POST['name'] as $locale => $name) {

        $pageDetailId = $_POST['pageDetailId'][$locale] ?? null;
        $customUrl = $_POST['url'][$locale] ?? null;

        $detail = [
            'id' => $pageDetailId,
            'locale' => $locale,
            'name' => $name,
            'description' => $_POST['description'][$locale],
            'metaTitle' => $_POST['metaTitle'][$locale],
            'metaDescription' => $_POST['metaDescription'][$locale],
            'updatedAt' => date('Y-m-d H:i:s'),
            'pageId' => $_POST['id'],
            'url' => $_POST['url']['locale'],
        ];

        // Instanciation de PageDetail avec les détails fournis
        $pageDetail = new PageDetail($detail);
        try {
            if (!$pageDetailId) {
                $pageDetail->create();
                $pageDetailId = $pageDetail->get('id');
            }

            $pageDetail->updateOrCreateUrl($customUrl);
            if ($pageDetailId) {
                $pageDetail->update();
            }
        } catch (Exception $e) {
            $errors[] = "Erreur dans la gestion de l'URL pour la langue '$locale'. \n" . $e->getMessage();
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
