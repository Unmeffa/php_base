<?php
$page = Page::getPage($_GET["id"]);
if (!$page || $page->get("id") === null) {
    header('Location: ' . BASE_URL . '/admin/404');
    exit();
}
?>
<div class="pl-64 ">
    <div class="p-8 divide-y-2 divide-slate-100 flex flex-col">
        <div class="pb-4 flex items-center">
            <h2 class="text-xl font-bold leading-7 text-gray-900">Gestion de la page - <?= $page->get('name') ?></h2>
            <a href="<?= $adminUrl ?>" class="ml-auto block min-w-max rounded-md bg-blue-600 px-[25px] py-[10px] text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Revenir à la liste des pages</a>
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
                    <?php include('ajax/detail_page/form.php') ?>
                </div>
                <div class="pt-4">
                    <button type="submit" class="block min-w-max rounded-md bg-blue-600 px-[25px] py-[10px] text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Valider</button>
                </div>
                <input type="hidden" name="id" value="<?= $page->get('id') ?>" />
            </form>
        </div>
        <div class="pb-4 flex items-center pt-4 !border-0">
            <h2 class="text-xl font-bold leading-7 text-gray-900">Gestion des zones de la page - <?= $page->get('name') ?></h2>
            <a href="#" id="addZone" data-type="page" data-parentId="<?= $page->get('id') ?>" class="ml-auto block min-w-max rounded-md bg-blue-600 px-[25px] py-[10px] text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Créer une nouvelle zone</a>
        </div>
        <div id="append" class="py-8 flex flex-col">
            <?php include("ajax/zone/list.php"); ?>
        </div>
    </div>
</div>