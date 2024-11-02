<div class="pl-64 ">
    <div class="p-8 divide-y-2 divide-slate-100 flex flex-col">
        <div class="pb-4 flex items-center">
            <h2 class="text-xl font-bold leading-7 text-gray-900">Gestion des pages du site internet</h2>
            <a href="#" id="addPage" class="ml-auto block min-w-max rounded-md bg-blue-600 px-[25px] py-[10px] text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Créer une nouvelle page</a>
        </div>
        <div id="append" class="py-8 flex flex-col">
            <?php include("ajax/page/list.php"); ?>
        </div>
    </div>
</div>