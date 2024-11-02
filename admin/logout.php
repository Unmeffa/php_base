<?php
include("include/variable.php");

if (User::checkSession()) {
    User::endSession();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?= $information->get("name") ?> - Déconnexion</title>
    <link href="css/styles.css" rel="stylesheet">
</head>

<body>
    <div class="flex min-h-screen flex-col justify-center px-12 py-12 lg:px-8">
        <div class="text-center">
            <p class="text-base font-semibold text-blue-600">404</p>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">Déconnexion</h1>
            <p class="mt-6 text-base leading-7 text-gray-600">Vous avez été déconnecté</p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="<?= $adminUrl ?>" class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Revenir à l'accueil</a>
            </div>
        </div>
    </div>
</body>

</html>