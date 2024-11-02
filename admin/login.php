<?php
include("include/variable.php");
include("scripts/handleLogin.php");
?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $information->get("name") ?> - Administration</title>
    <link href="css/styles.css" rel="stylesheet">
</head>

<body>
    <div class="flex min-h-screen flex-col justify-center px-12 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-15 w-auto invert" src="img/logo.png" alt="CortoGraphique">
        </div>
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="" method="POST">
                <div>
                    <label for="login" class="block text-sm font-medium leading-6 text-gray-900">Identifiant</label>
                    <div class="mt-2">
                        <input id="login" name="login" type="text" autocomplete="login" required class="block w-full p-2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Mot de passe</label>
                    </div>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="block p-2 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-blue-500 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Connexion</button>
                </div>

                <?php
                if (isset($error)) {
                ?>
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <span class="font-medium">Erreur !</span> <?= $error ?>
                    </div>
                <?php
                }
                ?>

            </form>

        </div>
    </div>
    <?php
    /*<div id="conteneur">
        <div id="head"></div>
        <div id="contenu">
            <div align="center">

                <form action="index.php" method="post" name="form1" id="form1">
                    <table width="50%" border="0" align="center" cellpadding="0" cellspacing="3" id="cnx">
                        <tr>
                            <td colspan="2" bgcolor="#999999">
                                <div align="center">
                                    <strong>
                                        <font color="#FFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">
                                            Identification Administrateur
                                        </font>
                                    </strong>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td width="42%">
                                <div align="right">
                                    <font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Login</font>
                                </div>
                            </td>
                            <td width="58%"><input name="email" type="text" id="email" /></td>
                        </tr>
                        <tr>
                            <td>
                                <div align="right">
                                    <font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Mot de passe</font>
                                </div>
                            </td>
                            <td><input name="pass" type="password" id="pass" /></td>
                        </tr>
                        <tr>
                            <td><input name="valider" type="hidden" id="valider" value="ok" /></td>
                            <td><input type="submit" name="Submit" value=" valider " /></td>
                        </tr>
                    </table>
                </form>
                <?php if ($erreurlog == 1) { ?>
                    <br />
                    <strong>
                        <font color="#fc8c08">
                            Votre login et votre mot de passe sont erron&eacute;s<br />
                            ou ils ne vous autorisent pas l'acc&egrave;s &agrave; l'espace
                            administrateur
                        </font>
                    </strong>
                    <strong>
                        <font color="#CC9933" size="6" face="Arial, Helvetica, sans-serif"><br />
                        <?php } ?>
                        </font>
                    </strong>
            </div>

        </div>
    </div>
    </div>*/
    ?>
</body>

</html>