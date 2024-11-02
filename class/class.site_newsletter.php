<?php
#inclusion de la class mere
require_once("class.site_fonction.php");
require_once("class.site_phpmailer.php");

require_once (realpath(dirname(__FILE__))) .  '/../conf/config_newsletter.php.inc';

class site_newsletter extends site_fonction 
{
	#Constante la config de la news
	const style_h2 = STYLE_H2;
	const style_h4 = STYLE_H4;
	const style_texte = STYLE_TEXTE;
	const style_a = STYLE_A;
	const style_border = STYLE_BORDER; 
	const style_fond_contenu = STYLE_FOND_CONTENU;
	const style_fond_footer = STYLE_FOND_FOOTER; 
	
	private $newsletter_id;
	private $newsletter_nom;

	public static function recupNewsletter($newsletter_id)
	{
		#recuperation newsletter
		$query = "select * from ".self::table."_newsletter where newsletter_id='".$newsletter_id."'";
		$rs = @mysql_query($query) or die($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet newsletter
		$o = new site_newsletter($row);
			
		#on retourne l'objet newsletter
		return $o;		
	}
	
	#methode de recuperation des dtail d'une newsletter
	public function recupDetailNewsletter($langue = "fr")
	{
		$rs = site_fonction::recup("detail_newsletter","where newsletter_id = $this->newsletter_id and detail_newsletter_langue = '$langue'",0,1," detail_newsletter_titre,detail_newsletter_description ");
		if(@mysql_num_rows($rs) == 0 && $langue != "fr")
		{
			#si il exite pas ds la langue je recupere le franais
			$rs = site_fonction::recup("detail_newsletter","where newsletter_id = $this->newsletter_id and detail_newsletter_langue = 'fr'",0,1," detail_newsletter_titre,detail_newsletter_description ");
		}
		if(@mysql_num_rows($rs) > 0){ $d = @mysql_fetch_assoc($rs); } else { $d["detail_newsletter_titre"] = ""; $d["detail_newsletter_description"] = "";}
		return $d;
	}
		
	#constructeur
	public function __construct($tab)
	{
		$this->newsletter_id = $tab["newsletter_id"];
		$this->newsletter_nom = stripslashes($tab["newsletter_nom"]);
	}
	
	
	#methode de récuperation d'un champ
	public function get($attribut)
	{
		return($this->$attribut);
	}
	
	#methode de modification d'un attribut
	public function set($attribut,$valeur)
	{
		$this->$attribut = $valeur;
	}
	
	#ajout d'une newsletter
	public function ajoutNewsletter()
	{

		#Requete d'ajout de la newsletter
		$query = "INSERT INTO ".self::table."_newsletter value
				  (
					'$this->newsletter_id',
					'".site_fonction::protec($this->newsletter_nom)."'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		$this->newsletter_id = mysql_insert_id();
		
	}
	
	#Mise a jour newsletter
	public function majNewsletter()
	{
		$query = "UPDATE  ".self::table."_newsletter set
					newsletter_nom = '".site_fonction::protec($this->newsletter_nom)."'
					WHERE newsletter_id = '$this->newsletter_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#Suppression d'un newsletter
	public function supNewsletter()
	{
		#On recupere les photo du produit
		$rs = site_fonction::recup("photo","where produit_id = $this->newsletter_id and photo_type = 'newsletter'",0,1000,"photo_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_photo::recupPhoto($ph[0]);
			$chemin = $o->chemin_dossier();
			$o->supPhoto();
		}
		#on supprime le dossier de photo du produit si il existe
		if(file_exists($chemin))
		{
			rmdir($chemin);
		}
		#On recupere les documents du produit
		$rs = site_fonction::recup("document","where produit_id = $this->newsletter_id and document_type = 'newsletter'",0,1000,"document_id");
		while( $ph = @mysql_fetch_row($rs) )
		{
			#suppresion des photos
			$o = site_document::recupDocument($ph[0]);
			$chemin = $o->chemin_dossier();
			$o->supDocument();
		}
		#on supprime le dossier de document du produit si il existe
		if(file_exists($chemin))
		{
			rmdir($chemin);
		}
		
		#on supprime la newsletter
		$query = "delete from ".self::table."_newsletter where newsletter_id=".$this->newsletter_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les details de cette newsletter
		$query = "delete from ".self::table."_detail_newsletter where newsletter_id=".$this->newsletter_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les envois de cette newsletter
		$query = "delete from ".self::table."_newsletter_type_client where newsletter_id=".$this->newsletter_id;
		$rs = mysql_query($query) or die(mysql_error());
				
	}
	
	public static function traitementFormulaire($post)
	{
			
		#creation de l'objet newsletter de la nouvelelle anne
		$a = new site_newsletter($post);
		if($a->get("newsletter_id") != "")
		{
			$a->majNewsletter();
			echo site_fonction::message("Newsletter",utf8_encode("Modification effectue"));			
		}
		else 
		{
			$a->ajoutNewsletter();
			echo site_fonction::message("Newsletter",utf8_encode("Insertion effectue"));	
		}
		
		return $a;	
		
	}
	
	#fonction pour afficher les dtails de l'envoi d'un newslleter
	public static function detailEnvoiNewsletter($newsletter_id)
	{
		$n = site_newsletter::recupNewsletter($newsletter_id);
		$rs = site_fonction::recup("newsletter_type_client","where newsletter_id = $newsletter_id order by newsletter_type_client_date");
		if(mysql_num_rows($rs) > 0)
		{
			while($row = @mysql_fetch_assoc($rs))
			{
				$tp = site_type_client::recupType_client($row["client_type_id"]);
				$mes .= " Envoy&eacute;e le ".site_fonction_date::decodeDate($row["newsletter_type_client_date"])." aux clients de type : ".$tp->get("type_client_titre")."          <br />";
			}
		}
		else
		{
			$mes = "Jamais envoy&eacute;e\r\n";
		}
		return site_fonction::protec($mes);
		
	}
	
	#methode pour utiliser php mailer et envoyer un mail
	public static function envoyerMail($objet,$message,$destinataire)
	{
		
		if($objet != "" && $message != "" && $destinataire != "")
		{
			#recuperation des info de l'entreprise
			$info = site_fonction::recupInformation();
			
			#creation de l'objet phpmailer
			$mail = new site_PHPmailer();
			$mail->CharSet='utf-8';
			$mail->IsMail();
			$mail->IsHTML(true);
			$mail->Host='hote_smtp';
			$mail->From=$info['information_mail'];
			$mail->FromName=$info['information_nom'];
			$mail->AddAddress(''.$destinataire.'');
			$mail->AddReplyTo(site_fonction::mail_edcorses);	
			$mail->Subject=''.$info['information_nom'].' : '.$objet.' ';
			$mail->Body=''.site_newsletter::style_mail($message).'';
			$mail->Send();
			$mail->SmtpClose();
			unset($mail);
		}
	}
	
	#MISE EN PAGE NEWSLETTER
	public static function style_mail($message)
	{
		#recuperation des info de l'entreprise
		$information=site_fonction::recupInformation();
		$envoie_mail='
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<style>
				*{
					margin:0px;
					padding:0px;
					font-family:Times New Roman, Times, serif;
					color:'.site_newsletter::style_texte.';
				}
				li
				{
					margin-left:20px;
				}
				a{
					color:'.site_newsletter::style_a.';
					text-decoration:none;
				}
				a:hover
				{
					text-decoration:underline;
				}
				h2{ color:'.site_newsletter::style_h2.'; }
				h4{ color:'.site_newsletter::style_h4.'; }
			</style>
			</head>
			<body>
				<div align="center" style="width:1000px; margin:10px auto; text-align:center;">
					<table width="1000px" align="center" >
						<tr>
							<td>
								<table width="1000px" cellspacing="0" cellpadding="0" align="center" style="width:1000px; border:1px solid '.site_newsletter::style_border.';">
									<tr>
										<td align="center" style=" border-bottom:1px solid '.site_newsletter::style_border.';"><img src="'.site_fonction::url_site.'/admin/img/top_news.jpg" width="1000px" /></td>
									</tr>
									<tr bgcolor="'.site_newsletter::style_fond_contenu.'" style="background-color:'.site_newsletter::style_fond_contenu.'">
										<td style="padding:10px 10px 10px 10px; border-bottom:1px solid '.site_newsletter::style_border.'; text-align:justify;">
											'.stripslashes($message).'
										</td>
									</tr>
									<tr>
										<td  align="center" bgcolor="'.site_newsletter::style_fond_footer.'" style="padding:3px; background-color:'.site_newsletter::style_fond_footer.'"">
											'.$information['information_nom'].' - '.$information['information_adresse'].' - '.$information['information_cp'].' - '.$information['information_ville'].'<br />Site : <a href="'.$information['information_site'].'">'.$information['information_site'].'</a> - Mail : <a href="mailto:'.$information['information_mail'].'">'.$information['information_mail'].'</a> - Tel : '.$information['information_tel'].' - Fax :'.$information['information_fax'].'
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</body>
			</html>
			';
		return $envoie_mail ;
	}
	
	#methode pour envoyer la newsletter
	public function envoyerNewsletter($TabTypeClient)//tableau contenant tous les type de client 
	{
		#recuperation des images lie a la newsletter
		$Tphoto = array();
		$rs = site_fonction::recup("photo","where produit_id = $this->newsletter_id and photo_type = 'newsletter' order by photo_prio",0,3,"photo_id");
		if(@mysql_num_rows($rs)>0)
		{
			while($row = mysql_fetch_row($rs))
			{
				#recuperation de la photo et creation de la mini
				$ph = site_photo::recupPhoto($row[0]);
				//$img2 = $ph->mini_photo("",140);
				#creation du tableau contenant toutes les id des photos
				$Tphoto[] = $row[0];
			}
		}
		
		#on parcours le tableau contenat les type de client devant recevoir la newslleter
		foreach($TabTypeClient as $type_client_id)
		{
			#je recupere tous les clients de cette categorie possedant un mail et aboner a la newsletter
			$rs = site_fonction::recup("client","where type_client_id = $type_client_id and client_newsletter = 1 and client_mail != ''");
			while($row = mysql_fetch_assoc($rs))
			{
				#recuperation des details sur la photo dans la langue du client
				$detail = $this->recupDetailNewsletter($row["client_lang"]);
				
				#formation du message a envoyer dans la newsletter
				#ajout du detail de la newsletter
				$message = "<h2>".$detail["detail_newsletter_titre"]."</h2><p>".$detail["detail_newsletter_description"]."</p><br />\n";
				#ajout des docuements
				$rs_doc = site_fonction::recup("document","where produit_id = $this->newsletter_id and document_type = 'newsletter' order by document_prio");
				while($row_doc = mysql_fetch_assoc($rs_doc))
				{
					#recuperation des détails du document
					$dd = site_fonction::recupDetail("document",$row_doc["document_id"],$row["client_lang"]);
					$message .= "<a style='font-size:14px;' href='".site_fonction::url_site."/document/newsletter/".$this->newsletter_id."/".$row_doc["document_fichier"]."' title='{$dd["titre"]}'> Télécharger : {$dd["titre"]}</a><br />";
				}
				#ajout des photos de la newsletter ds le message
				$message .= "<table cellpadding='10' cellspacing='10' style='width:100%'>\n<tr>\n";
				#je parcours le tableau des photos
				foreach( $Tphoto as $value)
				{
					#creation de l'objet photo
					$ph = site_photo::recupPhoto($value);
					#recuperation du nom de la mini photo
					$nom = site_fonction::url_site."/".str_replace("../../","",$ph->chemin_miniature("",140));
					#ajout du chemin vers la photo ds le message
					$message .= "<td align='center' valign='top' width='220px'><img src=\"".$nom."\" /></td>\n";
					#ajout des details de la photo dans le message
					$detail_photo = $ph->recupDetailPhoto($row["client_lang"]);
					if($detail_photo["detail_photo_titre"] != "" || $detail_photo["detail_photo_description"] != "")
					{
						$message .= "<td align='left'><h4>".$detail_photo["detail_photo_titre"]."</h4><p>".$detail_photo["detail_photo_description"]."</p></td>\n";
					}
					
				}
				$message .= "\n</tr>\n</table>";
				$message .= $this->messagelien($row["client_lang"]);
				$message .= $this->messageDcnx($row["client_lang"],$row["client_id"]);
				
				#envoi de la newsletter
				site_newsletter::envoyerMail($detail["detail_newsletter_titre"],$message,$row["client_mail"]);
			}
			#Requete d'ajout de la table croise client_type et newsletter
			$query = "INSERT INTO ".self::table."_newsletter_type_client value
					  (
						'',
						'$this->newsletter_id',
						'$type_client_id',
						'".date("Y-m-d")."'
					  )";
			$rs = mysql_query($query) or die(mysql_error());
		}
	}
	
	public static function configNewsletter($post)
	{
			$fp =  fopen ("../../conf/config_newsletter.php.inc", 'w+') or die("Impossible de crer le fichier config_newsletter.php.inc.php");
			$ligne = "<?php \n";
			$ligne.= "define ('STYLE_H2', '".$post["h2"]."');\n";
			$ligne.= "define ('STYLE_H4', '".$post["h4"]."');\n";
			$ligne.= "define ('STYLE_TEXTE', '".$post["texte"]."');\n";
			$ligne.= "define ('STYLE_A', '".$post["a"]."');\n";
			$ligne.= "define ('STYLE_BORDER', '".$post["border"]."');\n";
			$ligne.= "define ('STYLE_FOND_CONTENU', '".$post["fond_contenu"]."');\n";
			$ligne.= "define ('STYLE_FOND_FOOTER', '".$post["fond_footer"]."');\n";
			$ligne.= "?>";
			fputs ($fp, $ligne) or die("Impossible d'ecrire le texte du fichier config_newsletter.php.inc.php") ;
			fclose($fp);
	}
	
	public function messagelien($langue = "fr")
	{
		if($langue == "")
		{
		}
		else
		{
			$message = "<br />Si vous n'arrivez pas &agrave; visualiser correctement ce mail cliquez sur lien suivant : <a href='".site_fonction::url_site."/admin/affiche_newsletter.php?id=".$this->newsletter_id."'>".site_fonction::url_site."/admin/affiche_newsletter.php?id=".$this->newsletter_id."</a><br />Si le lien ne marche pas copier l'url dans votre navigateur.<br />";
		}
		return $message;
	}
	public function messageDcnx($langue = "fr",$client_id)
	{
		if($langue == "")
		{
		}
		else
		{
			$message = "<br />Si vous ne voulez plus recevoir de newsletter cliquez <a href='".site_fonction::url_site."/admin/dcnx_newsletter.php?id=$client_id'>ici</a><br />";
		}
		return $message;
	}
	
}

?>