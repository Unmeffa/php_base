<?php

if (User::checkSession()) {
    header('Location: ' . $adminUrl . '/');
    exit();
}

if (isset($_POST["login"]) && isset($_POST["password"])) {
    $login = $_POST["login"];
    $password = $_POST["password"];

    $user = new User($login, $password);
    if ($user->startSession()) {
        header('Location: ' . $adminUrl . '/');
        exit();
    } else {
        $error = "Identifiant ou mot de passe incorrect.";
    }
}
