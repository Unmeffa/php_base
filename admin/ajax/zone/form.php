<?php
include("../../include/variable_scripts.php");

$formFields =
    [
        [
            'name' => 'name',
            'label' => 'Nom',
            'description' => 'Nom de la zone',
            'required' => true
        ],
        [
            'name' => 'gabarit',
            'label' => 'Gabarit',
            'description' => 'Gabarit affecté à la zone',
            'options' => $tZoneType
        ],
        [
            'name' => 'subtype',
            'label' => 'Variation',
            'description' => 'Possible variation de la zone',
            'options' => $tZoneVariation
        ],
    ];
?>

<div id="modal" class="fixed left-0 top-0 w-full h-full bg-black bg-opacity-50 z-50 flex flex-col">
    <div class="m-auto p-6 bg-white w-full max-w-[380px] max-h-[85vh] relative">
        <div id="close-modal" class="absolute bg-white text-slate-600 border border-slate-200 right-0 top-0 w-8 h-8 p-2 rounded-md cursor-pointer transform translate-x-1/2 -translate-y-1/2 hover:bg-blue-600 hover:text-white">
            <svg class="w-full h-full fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
            </svg>
        </div>
        <div class="flex flex-col">
            <div class="pb-4">
                <h2 class="text-xl font-bold leading-7 text-gray-900">Créer une zone</h2>
            </div>
            <form name="addZone" class="space-y-6 flex flex-col" action="#" method="POST">
                <?php foreach ($formFields as $field) {
                    renderFields($field);
                } ?>
                <div class="pt-4">
                    <button type="submit" class="block min-w-max rounded-md bg-blue-600 px-[25px] py-[10px] text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Valider</button>
                </div>
                <input type='hidden' name="parentId" value="<?= $_GET['parentId'] ?>" />
                <input type='hidden' name="type" value="<?= $_GET['type'] ?>" />
            </form>
        </div>
    </div>
</div>