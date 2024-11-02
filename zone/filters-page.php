<div class="zone filters filters-page">
    <div class="container">
        <div class="text">
            <div class="prettyTitle">Nos séjours</div>
            <h2 class="title"><?=$page_detail->get("page_detail_titre3")?></h2>
        </div>
        <div class="list">
            <div class="items">
                <?php
                $metaData = $page_detail->get("page_detail_metadonnees");
                $sejourCondition = '';
                $count = 0;
                if(strpos($metaData, "#") !== false) {
                    $explode = explode("#", $metaData);
                    foreach($explode as $piece) {
                        $sejourCondition .= $count === 0 ? 'AND (' : ' OR ';
                        $sejourCondition .= 'cat_sejour_id = '.$piece;
                        $count++;
                        $sejourCondition .= $count ===  count($explode) ? ')' : '';
                    }
                } else {
                    $sejourCondition = 'AND cat_sejour_id ='.$metaData;
                }
                $rs_sejours = fonction::recup('cat_sejour', "where cat_sejour_en_ligne = 0 ".$sejourCondition." order by
                theme_sejour_id ASC");
                $categories = [];
                while($donnees=mysql_fetch_assoc($rs_sejours)) {
                    $sejour = new cat_sejour($donnees);
                    if(!isset($categories[$donnees['theme_sejour_id']])) {
                        $category = $sejour->getMainCategory();
                        if(isset($category) && $category['name'] != '') {
                            $categories[$donnees['theme_sejour_id']] = array('name' => $category['category'], 'filter' =>
                            'cat-'.$donnees['theme_sejour_id']);
                        }

                    }
                    include(SERVER_ROOT_URL."/include/cartouche.php");
                }
                ?>
            </div>
            <div class="sidebar">
                <?php  include(SERVER_ROOT_URL."/include/garanties.php"); ?>
                <?php  if($id_get != 8) include(SERVER_ROOT_URL."/include/box_mesure.php"); ?>
            </div>
        </div>

    </div>
</div>
