<?php
#inclusion de la class mere
require_once("class.site_fonction.php");
require_once("phmagick.php");
class site_document extends site_fonction 
{
	private $document_id;
	private $produit_id;
	private $document_date;
	private $document_nom;
	private $document_fichier;
	private $document_type;
	private $document_langue;
	private $document_prio;
	private $document_jour;

	public static function recupDocument($id)
	{
		#recuperation document
		$query = "select * from ".self::table."_document where document_id=".$id;
		$rs = mysql_query($query);
		$row = mysql_fetch_assoc($rs);
		
		#creation de l'objet document
		$o = new site_document($row);
		
		#on retourne l'objet document
		return $o;
		
	}
	
	
	#constructeur
	public function __construct($tab)
	{
		$this->document_id = $tab["document_id"];
		$this->produit_id = $tab["produit_id"];
		$this->document_date = $tab["document_date"];		
		$this->document_nom = $tab["document_nom"];
		$this->document_fichier = $tab["document_fichier"];
		$this->document_type = $tab["document_type"];
		$this->document_langue = $tab["document_langue"];
		$this->document_prio = $tab["document_prio"];
		$this->document_jour = $tab["document_jour"];
	}
	
	#methode de rÃ©cuperation d'un champ
	public function get($attribut)
	{
		return($this->$attribut);
	}
	
	#methode de modification d'un attribut
	public function set($attribut,$valeur)
	{
		$this->$attribut = $valeur;
	}
	
	#ajout d'une document
	public function ajoutDocument()
	{
		#test pour la prio
		if($this->document_prio == "")
		{
			$rs = site_fonction::recup("document","where produit_id = ".$this->produit_id." and document_type = '$this->document_type'");
			$nb = mysql_num_rows($rs);
			$this->document_prio = $nb+1;
		}
		
		#Requete d'ajout de la document
		$query = "INSERT INTO ".self::table."_document value
				  (
					'$this->document_id',
					'$this->produit_id',
					'$this->document_date',
					'$this->document_nom',
					'$this->document_fichier',
					'$this->document_type',
					'$this->document_langue',
					'$this->document_prio',
					'$this->document_jour'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		
	}
	
	#Mise a jour  document
	public function majDocument()
	{
		$query = "UPDATE  ".self::table."_document set
					produit_id = '$this->produit_id',
					document_date = '$this->document_date',
					document_nom = '$this->document_nom',
					document_fichier = '$this->document_fichier',
					document_type = '$this->document_type',
					document_prio = '$this->document_prio'
					document_langue = '$this->document_langue',
					document_jour = '$this->document_jour'
					WHERE document_id = '$this->document_id'
					";
		$rs = mysql_query($query) or die($query);			
	
	}
	
	#Suppression d'une document
	public function supDocument()
	{

		#suppression bas de données
		$query = "delete from ".self::table."_document where document_id=".$this->document_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#suppression bas de données
		$query = "delete from ".self::table."_detail_document where document_id=".$this->document_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#mise a jour des prio
		site_prio::majPrio("document",$this->document_prio,"and produit_id = $this->produit_id and document_type = '$this->document_type'");
		
		#suppression du serveur
		unlink($this->chemin_document());
		
	}
	
	#methode pour retrouver le chemin vers le dossier contenant toutes les photos
	public function chemin_dossier()
	{
		$chemin = "../document/";
		
		#mise a jour du chemin
		$chemin .= $this->document_type."/".$this->produit_id;
		
		#chemin vers la document
		$chemin_document = $chemin."/".$this->document_fichier;
		
		if(!file_exists($chemin_document))
		{
			$chemin = "../../document/";
			$chemin .= $this->document_type."/".$this->produit_id;
			$chemin_document = $chemin."/".$this->document_fichier;
			if(!file_exists($chemin_document))
			{
				$chemin = "../../../document/";
				$chemin .= $this->document_type."/".$this->produit_id;
				$chemin_document = $chemin."/".$this->document_fichier;
				if(!file_exists($chemin_document))
				{
					$chemin = "admin/document/";
					$chemin .= $this->document_type."/".$this->produit_id;
					$chemin_document = $chemin."/".$this->document_fichier;
					
					if(!file_exists($chemin_document))
					{
						$chemin = "document/";
						$chemin .= $this->document_type."/".$this->produit_id;
						$chemin_document = $chemin."/".$this->document_fichier;
						if(!file_exists($chemin_document))
						{
							echo("Aucun chemin detecté");
						}
					}
				}
			}
		}
		return $chemin;
	}
	
	#methode pour afficher la grande document
	public function chemin_document()
	{
		$chemin = $this->chemin_dossier();
		$src = $chemin."/".$this->document_fichier;

		return $src;
	}
	
	
	
	public static function traitementFormulaire($post,$file)
	{
		#tableau contenant les extensions autorisées
		$Textension = array("pdf","zip");
		
		$chemin = "../document/".$post["document_type"]."/";
		
		#récuperation du nom du document
		$verif_fichier = $file['document_fichier']['name'];
		
		#si il n'y a pas de nom saisie
		if($post["document_nom"] == ""){$post["document_nom"] = site_fonction::clean($file['document_fichier']['name']);}
		
		#si un fichier a bien été selectionner
		if($verif_fichier != "")
		{
			#je recupere l'extension du document
			$Tab = explode(".",$verif_fichier);
			$ext = $Tab[1];
			
			#test sur l'extension du fichier
			if(in_array($ext,$Textension))
			{
				
				#Si le dossier de document n'exite pas
				if(!file_exists($chemin.$post["produit_id"]))
				{
					#creation du dossier chemin_document/produit_id
					mkdir($chemin.$post["produit_id"],0755);
				}
				
				#je renome le fichier
				$nom_fichier = site_fonction::clean(stripslashes($post["document_nom"]))."-".date("Ymd-hms").".".$ext;
				$post["document_fichier"] = $nom_fichier;
				
				#creation de l'objet document
				$l = new site_document($post);
				
				#dossier de telechargement des documents
				$rep_imgs = $chemin.$post["produit_id"]."/".$nom_fichier;
				
				#recuperation du document
				$tmp = $file['document_fichier']['tmp_name'];
				
				#on charge le document 
				move_uploaded_file($tmp, $rep_imgs);
				
				#enregistrement ds la BDD
				$l->ajoutDocument();
				$l->set("document_id",mysql_insert_id());
				
				echo site_fonction::message("Document","Téléchargement réussi.");
				
			}
			else
			{
				echo site_fonction::message("Document","Ce fichier n'a pas une extension valable.","erreur");
			}


		}
		else
		{
			echo site_fonction::message("Document","Vous n'avez pas selectionnez de document.","erreur");
		}
				
	}
	
#methode pour generer une vignette du document avec pdf magique
	public function generer_image($largeur="",$hauteur = "",$autre = "",$regenerer = 0)
	{
		if($largeur == "") $largeur = 0;
		if($hauteur == "") $hauteur = 0;
		$nom_fichier = $this->chemin_dossier()."/".$this->get("document_id")."-".$largeur."-".$hauteur.".jpg";
		if(!file_exists($nom_fichier) or $regenerer == 1)
		{
			$doc_img = new phmagick($this->chemin_document(),$nom_fichier);
			$doc_img->acquireFrame($this->chemin_document());
			$doc_img->resize($largeur,$hauteur);
			#je redimensionne dans mon cadre si nécéssaire
			if($largeur > 0 && $hauteur > 0)
			{
				site_fonction::create_mini_2($nom_fichier,$nom_fichier,$largeur,$hauteur);
			}
			elseif($largeur > 0 && $hauteur == 0)
			{
				site_fonction::create_mini_paysage($nom_fichier,$nom_fichier,$largeur,$hauteur);
			}
			else
			{
				site_fonction::create_mini_portrait($nom_fichier,$nom_fichier,$hauteur,$largeur);
			}
		}
		$img =  "<img src='$nom_fichier' $autre />";
		return $img;
	}
	
	public function generer_chemin_image($largeur = "",$hauteur = "",$autre = "",$regenerer = 0)
	{
		if($largeur == "") $largeur = 0;
		if($hauteur == "") $hauteur = 0;
		$nom_fichier = $this->chemin_dossier()."/".$this->get("document_id")."-".$largeur."-".$hauteur.".jpg";
		if(!file_exists($nom_fichier) or $regenerer == 1)
		{
			$doc_img = new phmagick($this->chemin_document(),$nom_fichier);
			$doc_img->acquireFrame($this->chemin_document());
			$doc_img->resize($largeur,$hauteur);
			#je redimensionne dans mon cadre si nécéssaire
			if($largeur > 0 && $hauteur > 0)
			{
				site_fonction::create_mini_2($nom_fichier,$nom_fichier,$largeur,$hauteur);
			}
			elseif($largeur > 0 && $hauteur == 0)
			{
				site_fonction::create_mini_paysage($nom_fichier,$nom_fichier,$largeur,$hauteur);
			}
			else
			{
				site_fonction::create_mini_portrait($nom_fichier,$nom_fichier,$hauteur,$largeur);
			}
		}
		$img =  $nom_fichier;
		return $img;
	}
	
	
}

?>