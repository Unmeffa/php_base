<?php

include("../../include/variable_scripts.php");
$locale = $_GET['locale'] ?? 'fr';
$photo = Photo::getPhoto($_GET["id"], $locale);
$details = $photo->getDetails();
if (!$photo || $photo->get("id") === null) {
    header('Location: ' . BASE_URL . '/admin/404');
    exit();
}

$formFields =
    [
        [
            'name' => "alt[$locale]",
            'label' => 'Alt',
            'description' => "Alt de la photo (pour le référencement)",
            'value' => $details ? $details->get('alt') : ''
        ],
        [
            'name' => "name[$locale]",
            'label' => 'Nom',
            'description' => 'Nom de la photo',
            'value' => $details ? $details->get('name') : ''
        ],
        [
            'name' => "description[$locale]",
            'label' => 'Description',
            'description' => 'Description de la photo',
            'value' => $details ? $details->get('description') : '',
            'type' => 'textarea'
        ],

    ];

?>
<div class="flex flex-col ajax-form" id="detail_<?= $locale ?>">
    <?php
    foreach ($formFields as $field) {
        renderFields($field);
    }
    ?>
    <input type="hidden" name="photoDetailId[<?= $locale ?>]" value="<?= $details ? $details->get('id') : '' ?>" />
</div>
<?php
