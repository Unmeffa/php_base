<?php

include("../../include/variable_scripts.php");
$locale = $_GET['locale'] ?? 'fr';
$page = Page::getPage($_GET["id"], $locale);
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
                'value' => $details->get('name')
            ],
            [
                'name' => "metaTitle[$locale]",
                'label' => 'Meta Titre',
                'description' => 'Nom affiché sur la recherche Google',
                'value' => $details->get('metaTitle')
            ],
            [
                'name' =>  "url[$locale]",
                'label' => 'Url',
                'description' => 'Adresse de la page (unique)',
                'value' => $page->getUrl(),
                'required' => true
            ],
        ],
        [
            [
                'name' => "description[$locale]",
                'label' => 'Description',
                'description' => 'Description facultative',
                'value' => $details->get('description'),
                'type' => 'textarea'
            ],
            [
                'name' => "metaDescription[$locale]",
                'label' => 'Meta Description',
                'description' => 'Description affichée sur la recherche Google',
                'value' => $details->get('metaDescription'),
                'type' => 'textarea'
            ],
        ],
    ];

?>
<div class="flex flex-col ajax-form" id="detail_<?= $locale ?>">
    <?php
    foreach ($formFields as $field) {
        renderFields($field);
    }
    ?>
    <input type="hidden" name="pageDetailId[<?= $locale ?>]" value="<?= $details->get('id') ?>" />
</div>
<?php
