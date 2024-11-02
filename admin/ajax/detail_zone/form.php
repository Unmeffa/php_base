<?php

include("../../include/variable_scripts.php");
$locale = $_GET['locale'] ?? 'fr';
$page = Zone::getZone($_GET["id"], $locale);
$details = $page->getDetails();
if (!$page || $page->get("id") === null) {
    header('Location: ' . BASE_URL . '/admin/404');
    exit();
}

$formFields =
    [
        [
            [
                'name' => "name[$locale]",
                'label' => 'Nom',
                'description' => 'Nom de la page',
                'required' => true,
                'value' => $details ? $details->get('name') : ''
            ],
            [
                'name' => "hName[$locale]",
                'label' => 'Titre Hn',
                'description' => 'Titre de référencement',
                'value' => $details ? $details->get('hName') : ''
            ],
        ],
        [
            [
                'name' => "description[$locale]",
                'label' => 'Description',
                'description' => 'Description',
                'value' => $details ? $details->get('description') : '',
                'type' => 'textarea',
                'maxWidth' => true
            ],
        ]
    ];

?>
<div class="flex flex-col ajax-form" id="detail_<?= $locale ?>">
    <?php
    foreach ($formFields as $field) {
        renderFields($field);
    }
    ?>
    <input type="hidden" name="zoneDetailId[<?= $locale ?>]" value="<?= $details ? $details->get('id') : '' ?>" />
</div>
<?php
