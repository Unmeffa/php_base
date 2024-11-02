<?php
foreach($zones as $zoneData) {
    $zone = new site_zone($zoneData);
    $zoneDetail = $zone->recupZone_detail($lang_get);
    file_exists(SERVER_ROOT_URL."/zone/".$zone->get("zone_type").".php") ? include(SERVER_ROOT_URL."/zone/".$zone->get("zone_type").".php") : null;
}
