<?php
include_once('conf/config.inc.php');
include_once(ROOT_RESASOFT.'/conf/config.php.inc');

ini_set('arg_separator.output', '&amp;');
ini_set("url_rewriter.tags", "a=href,area=href,frame=src,iframe=src,input=src");
session_start();

spl_autoload_register(function ($classe) {
    if (preg_match("/site_/i", $classe) > 0) {
        if (file_exists(SERVER_ROOT_URL."/class/class.$classe.php")) {
            require_once SERVER_ROOT_URL."/class/class.$classe.php";
        }
    } else {
        if (file_exists(ROOT_RESASOFT."/class/class.".strtolower($classe).".php")) {
            require_once ROOT_RESASOFT."/class/class.".strtolower($classe).".php";
        }
    }
});


site_fonction::se_connecter();
$info = site_fonction::recupInformation();
$rs = site_fonction::recup("page","where page_parent = 0 and page_actif = 1  and page_menu = 1 order by page_prio asc",0,1,"page_id");
$row = mysql_fetch_row($rs);
$id_princ = $row[0];
if( XML_PAGE_PRINCIPALE == 0)
{
	$rs2 = site_fonction::recup("page","where page_parent = $id_princ and page_actif = 1  order by page_prio asc",0,1,"page_id");
	$row = mysql_fetch_row($rs2);
	$id_princ = $row[0];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
	a { color:#00F; text-decoration:none; }
	a:hover { text-decoration:underline; }
</style>
<title>Plan du site : <?=$info["information_nom"]?></title>
</head>
	<h1>Plan du site : <?=$info["information_nom"]?></h1>
    <?=site_page::generer_plan_du_site(0,"fr",XML_MULTI_LANGUE)?>
</body>
</html>