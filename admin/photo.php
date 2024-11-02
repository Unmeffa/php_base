<?php
include("include/variable.php");

if (empty($_GET['id']) || empty($_GET["type"])) {
    header('Location: ' . BASE_URL . '/admin/404');
    exit();
}

if (!in_array($_GET["type"], ["page", "zone"])) {
    header('Location: ' . BASE_URL . '/admin/404');
    exit();
}

$entityName = $_GET['type'];
$entity = null;
if ($entityName == 'page') {
    $entity = Page::getPage($_GET['id']);
} else if ($entityName == 'zone') {
    $entity = Zone::getZone($_GET['id']);
}

if ($entity === null || $entity->get('id') < 1) {
    header('Location: ' . BASE_URL . '/admin/404');
    exit();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?= $information->get("name") ?> - Administration</title>
    <link href="css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4/dist/fancybox.css" />
    <style>
        .dropzone {
            border-color: rgba(133, 133, 133, .2);
        }

        .dropzone .dz-default.dz-message {
            display: none;
        }
    </style>
</head>

<body class="min-h-full">
    <?php include("include/menu.php") ?>
    <?php include("include/photo.php") ?>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4/dist/fancybox.umd.js"></script>
    <script>
        Dropzone.autoDiscover = false;
        var dropzone = new Dropzone("#dropzone", {
            url: "ajax/photo/upload.php",
            dictDefaultMessage: "Ajouter des photos",
            uploadMultiple: true,
            maxFiles: 10,
            params: {
                'type': "<?= $_GET['type'] ?>",
                "id": "<?= $_GET['id'] ?>",
            },
            accept: function(file, done) {
                var accepts = [
                    'image/jpgg',
                    'image/jpeg',
                    'image/png',
                    'image/gif'
                ];

                if (accepts.includes(file.type)) {
                    done();
                } else {
                    done('Fichier non autorisé');
                }
            },
            success: function(thisFile, done) {
                console.log(done);
            },
            queuecomplete: function() {
                // Cette fonction est appelée lorsque tous les uploads sont terminés
                window.location.reload();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script type="module" src="js/photo.js"></script>
</body>

</html>