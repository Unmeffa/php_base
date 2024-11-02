<?php
$zone = Zone::getZone($_GET["id"]);
if (!$zone || $zone->get("id") === null) {
    header('Location: ' . BASE_URL . '/admin/404');
    exit();
}

$parentZone = $zone->getParentsProps();
$adminUrl .= '/' . $parentZone['type'] . '?id=' . $parentZone['parentId'];

$formFields =
    [
        [
            'name' => 'gabarit',
            'label' => 'Gabarit',
            'description' => 'Gabarit affecté à la zone',
            'options' => $tZoneType,
            'value' => $zone->get('gabarit'),
        ],
        [
            'name' => 'subtype',
            'label' => 'Variation',
            'description' => 'Possible variation de la zone',
            'options' => $tZoneVariation,
            'value' => $zone->get('subtype'),
        ],
        [
            'name' => 'headtype',
            'label' => 'Type Hn',
            'description' => 'Niveau du titre de référencement',
            'options' => ['1' => 'H1', '2' => 'H2', '3' => 'H3', '4' => 'H4', '5' => 'H5', '6' => 'H6'],
            'value' => $zone->get('headtype') ?? '2',
        ],
    ];
?>

<div class="pl-64 ">
    <div class="p-8 divide-y-2 divide-slate-100 flex flex-col">
        <div class="pb-4 flex items-center">
            <h2 class="text-xl font-bold leading-7 text-gray-900">Gestion de la zone - <?= $zone->get('name') ?></h2>
            <a href="<?= $adminUrl ?>" class="ml-auto block min-w-max rounded-md bg-blue-600 px-[25px] py-[10px] text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Revenir en arrière</a>
        </div>

        <?php
        if (isset($successMessage)) {
            renderSuccessMessage($successMessage);
        }

        if (!empty($errorMessages)) {
            renderErrorMessages($errorMessages);
        }

        ?>

        <div class="py-8 flex flex-col">
            <form class="space-y-6 divide-y-2 divide-slate-100 flex flex-col" action="" method="GET">
                <div class="flex gap-3">
                    <?php
                    foreach ($formFields as $field) {
                        renderFields($field);
                    }
                    ?>
                </div>
                <div class="flex gap-3 pt-4">
                    <?php
                    $langs = DB::getLangs();
                    foreach ($langs as $lab => $val) {
                        if ($val == 1 && DB::getLangLabel($lab)) {
                            $isActive = ($lab === 'fr') ? 'bg-blue-500 text-white' : '';
                    ?>
                            <a data-lang="<?= $lab ?>" href="#" class="block min-w-max rounded-md px-[25px] py-[10px] text-center text-sm font-semibold <?= $isActive ?> shadow-sm border-2 border-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 hover:bg-blue-500 hover:text-white active:bg-blue-500 active:text-white transition-colors duration-300" data-locale="fr">
                                <?= DB::getLangLabel($lab) ?>
                            </a>
                    <?php
                        }
                    }
                    ?>
                </div>
                <div class="flex flex-col" id="ajax-form">
                    <?php include('ajax/detail_zone/form.php') ?>
                </div>
                <div class="pt-4">
                    <button type="submit" class="block min-w-max rounded-md bg-blue-600 px-[25px] py-[10px] text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Valider</button>
                </div>
                <input type="hidden" name="id" value="<?= $zone->get('id') ?>" />
            </form>
        </div>

    </div>
</div>