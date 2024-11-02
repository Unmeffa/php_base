<?php
if (!User::checkSession()) {
    header('Location: ' . $adminUrl . '/');
    exit();
}

$filters = [
    'mail' => [
        'label' => 'Email',
        'filter' => FILTER_VALIDATE_EMAIL
    ],
    'receptionMail' => [
        'label' => 'Email de réception',
        'filter' => FILTER_VALIDATE_EMAIL
    ],
    'facebook' => [
        'label' => 'Facebook',
        'filter' => FILTER_VALIDATE_URL
    ],
    'instagram' => [
        'label' => 'Instagram',
        'filter' => FILTER_VALIDATE_URL
    ]
];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = validateFormData($_POST, $filters);
    if (isset($result['errors'])) {
        $errorMessages = $result['errors'];
    } else {

        $information->loadFromArray($_POST);
        if ($information->save()) {
            $successMessage = 'Les informations ont été mises à jour avec succès.';
        } else {
            $errorMessages[] = 'Une erreur est survenue lors de la mise à jour des informations.';
        }
    }
}
