<?php

function renderPrio($id, $prio, $maxPrio)
{

    $beforePrio = $prio - 1;
    $nextPrio = $prio + 1;

?>

    <a href="javascript:void(0)"
        class="<?= $beforePrio <= 0 ? 'opacity-50 cursor-default' : '' ?>"
        data-id="<?= $id ?>"
        data-new-prio="<?= $beforePrio ?>" <?= $beforePrio <= 0 ? 'disabled' : '' ?>>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-3 h-3 fill-current">
            <path d="M233.4 105.4c12.5-12.5 32.8-12.5 45.3 0l192 192c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L256 173.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l192-192z" />
        </svg>
    </a>

    <a href="javascript:void(0)"
        class="<?= $nextPrio > $maxPrio ? 'opacity-50 cursor-default' : '' ?>"
        data-id="<?= $id ?>"
        data-new-prio="<?= $nextPrio ?>" <?= $nextPrio > $maxPrio ? 'disabled' : '' ?>>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-3 h-3 fill-current">
            <path d="M278.6 406.6c-12.5 12.5-32.8 12.5-45.3 0l-192-192c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L256 338.7l169.4-169.4c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3l-192 192z" />
        </svg>
    </a>

<?php

}

function renderActive(int $id, bool $isActive)
{
?>
    <a href="#" data-id="<?= $id ?>" data-active="<?= $isActive ?>" class="inline-block">
        <?php
        if ($isActive) {
        ?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 fill-green-500">
                <path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z" />
            </svg>
        <?php
        } else {
        ?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="w-5 h-5 fill-red-500">
                <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
            </svg>
        <?php
        }
        ?>
    </a>
    <?php
}

function renderTableRows(array $pages, int $level = 0, string $parentName = '-')
{
    $adminUrl = BASE_URL . "/admin";
    foreach ($pages as $page) {
        $prio = $page->get('prio');
        $maxPrio = count($pages);
        $bgClass = $level > 0 ? 'bg-slate-100 border-b-2 border-b-slate-200' : 'border-b-2 border-b-slate-200';
        $pageName = htmlspecialchars($page->get('name'));
        $gabarit = $page->getGabaritName();
        $pageId = $page->get('id');
    ?>

        <tr id="page<?= $page->get("id") ?>" class="<?= $bgClass ?>">
            <td class="py-4 text-sm text-center"><?= renderActive($pageId, $page->get('active')) ?></td>
            <td class="py-4 text-sm text-center"><?= $pageName ?></td>
            <td class="py-4 text-sm text-center"><?= $gabarit ?></td>
            <td class="py-4 text-sm text-center flex flex-col items-center">
                <?php renderPrio($pageId, $prio, $maxPrio); ?>
            </td>
            <td class="py-4 text-sm text-center">
                <a href="<?= $adminUrl ?>/photo?id=<?= $page->get('id') ?>&type=zone" class="hover:text-blue-500 inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-5 h-5 fill-current">
                        <path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z" />
                    </svg>
                </a>
            </td>
            <td class="py-4 text-sm text-center">
                <a href="<?= $adminUrl ?>/zone?id=<?= $page->get('id') ?>" class="hover:text-blue-500 inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-5 h-5 fill-current">
                        <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z" />
                    </svg>
                </a>
            </td>
            <td class="py-4 text-sm text-center">
                <a href="#" data-delete data-name="<?= $pageName ?>" data-id="<?= $pageId ?>" class="hover:text-red-500 inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 fill-current">
                        <path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z" />
                    </svg>
                </a>
            </td>
        </tr>

<?php
    }
}

$zones = $page->getZones();

?>

<table class="p-4 border-2 border-b-slate-100">
    <thead class="border-b-2 border-b-slate-100">
        <tr>
            <th class="py-4 text-sm">Active</th>
            <th class="py-4 text-sm">Nom</th>
            <th class="py-4 text-sm">Gabarit</th>
            <th class="py-4 text-sm">Priorité</th>
            <th class="py-4 text-sm">Photos</th>
            <th class="py-4 text-sm">Contenu</th>
            <th class="py-4 text-sm">Supprimer</th>
        </tr>
    </thead>
    <tbody>
    <tbody>
        <?php
        if (count($zones) > 0) {
            renderTableRows($zones);
        } else {
        ?>
            <tr>
                <td colspan="7" class="py-4 text-sm text-center">Aucune zone n'a été créée.</td>
            </tr>
        <?php
        }
        ?>
    </tbody>
    </tbody>
</table>