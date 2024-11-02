<?php
include("../../include/variable_scripts.php");


$id = $_POST['id'];
$newPrio = $_POST['newPrio'];
$pageRequest = Fonction::recup('page', 'where id = ' . $id);
$page = new Page($pageRequest[0]);

try {
    $page->updatePrio($newPrio);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
