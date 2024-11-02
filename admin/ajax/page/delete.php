<?php
include("../../include/variable_scripts.php");

$id = $_POST["id"];
$delete = $_POST['delete'];

$pageRequest = Fonction::recup('page', 'where id = ' . $id);
try {
    if ($pageRequest[0]) {
        $page = new Page($pageRequest[0]);
        $page->delete();
        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
