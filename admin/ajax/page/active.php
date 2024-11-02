<?php
include("../../include/variable_scripts.php");

$id = $_POST["id"];
$active = $_POST['active'];

$pageRequest = Fonction::recup('page', 'where id = ' . $id);
try {
    if ($pageRequest[0]) {
        $page = new Page($pageRequest[0]);
        if ($active == 1) {
            $page->set('active', 0);
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="w-5 h-5 fill-red-500">
                <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
            </svg>';
            $result =  json_encode(["active" => 0, "icon" => $icon]);
        } else {
            $page->set('active', 1);
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 fill-green-500">
                <path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z" />
            </svg>';
            $result = json_encode(["active" => 1, "icon" => $icon]);
        }
        $page->update();
        echo $result;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
