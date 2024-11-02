<?php
if ($_GET["type"] == "zone") {
    $zone = Zone::getZone($_GET["id"]);
    $parentZone = $zone->getParentsProps();
    $adminUrl .= '/' . $parentZone['type'] . '?id=' . $parentZone['parentId'];
}
?>

<div class="pl-64 ">
    <div class="p-8 divide-y-2 divide-slate-100 flex flex-col">
        <div class="pb-4 flex items-center">
            <h2 class="text-xl font-bold leading-7 text-gray-900">Gestions des photos de la <?= $entityName ?> - <?= $entity->get('name') ?></h2>
            <a href="<?= $adminUrl ?>" class="ml-auto block min-w-max rounded-md bg-blue-600 px-[25px] py-[10px] text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Revenir en arrière</a>
        </div>

        <div class="py-8 flex flex-col">
            <div class="flex flex-wrap gap-4 mb-4" id="photo-container">
                <?php
                $photos = Fonction::recup('photo', "where parentId = " . $entity->get('id') . " and type = '" . $entityName . "' order by prio ASC");
                foreach ($photos as $photo) {
                    $ph = new Photo($photo);
                    $imageUrl = $ph->getFilePath();
                ?>
                    <div draggable="true"
                        data-id="<?= $ph->get('id') ?>"
                        data-prio="<?= $ph->get('prio') ?>" class="w-1/5 aspect-square max-w-52 bg-gray-200 rounded-lg overflow-hidden shadow-md relative">
                        <img src="<?php echo $imageUrl; ?>" alt="Photo" class="w-full h-full object-cover">
                        <div class="absolute top-2 right-2 flex space-x-2">
                            <a href="<?php echo $imageUrl; ?>" data-fancybox="gallery" class="text-gray-700 bg-white rounded-full p-1 shadow hover:text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M10 2a8 8 0 105.29 14H16l5 5-1.41 1.41-5-5v-.71A8 8 0 0010 2zm0 2a6 6 0 110 12A6 6 0 0110 4z" />
                                </svg>
                            </a>
                            <a href="#" data-id="<?= $ph->get('id') ?>" data-details class="text-gray-700 bg-white rounded-full p-1 shadow hover:text-green-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0a12 12 0 1012 12A12 12 0 0012 0zm1 18h-2v-2h2zm0-4h-2V7h2z" />
                                </svg>
                            </a>
                            <a href="#" data-id="<?= $ph->get('id') ?>" data-delete class="text-gray-700 bg-white rounded-full p-1 shadow hover:text-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 6h18v2H3zm3 14h12a1 1 0 001-1V9H5v10a1 1 0 001 1zm8-3h-2v-7h2z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <div id="preview_container" class="preview_container"></div>
            <div id="dropzone" class="dropzone w-full text-center flex items-center flex-wrap mx-auto justify-center cursor-pointer border-t-2">
                <div class="flex flex-auto mx-auto self-center pointer-events-none w-full">
                    <img class="has-mask h-[250px] w-auto object-center mx-auto object-contain" src="https://img.freepik.com/free-vector/image-upload-concept-landing-page_52683-27130.jpg?size=338&ext=jpg" alt="freepik image">
                </div>
                <p class="pointer-events-none text-gray-500 w-full mb-6">
                    <span class="text-sm">
                        Drag & drop vos fichiers ici</span><br /> ou sélectionner des fichiers sur votre ordinateur.
                </p>
            </div>
        </div>
    </div>
</div>