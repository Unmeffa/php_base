<div class="zone liste_sejours">
    <div class="container">
        <?php
    $sejourCondition = '';
    $count = 0;
    $categoriesId = [];
    foreach($listSejours as $category){
        if(count($category["ids"]) > 0){
            foreach($category["ids"] as $id){
                $categoriesId[] = $id;
            }
        }
    }
    foreach($categoriesId as $id){
        $sejourCondition .= $count === 0 ? 'AND (' : ' OR ';
        $sejourCondition .= 'cat_sejour_id = '.$id;
        $count++;
        $sejourCondition .= $count ===  count($categoriesId) ? ')' : '';
    }

    include(SERVER_ROOT_URL."/ajax/sejours.php");
    ?>
    </div>
</div>
