<?php
#inclusion de la class mere
require_once("class.site_fonction.php");

class site_tarif extends site_fonction 
{
	private $tarif_id;
	private $typehebergement_id;
	private $tarif_nom;
	private $tarif_prio;
	private $tarif_img;

	public static function recupTarif($tarif_id)
	{
		#recuperation tarif
		$query = "select * from ".self::table."_tarif where tarif_id='".$tarif_id."'";
		$rs = @mysql_query($query);
		$row = @mysql_fetch_assoc($rs);
			
		#creation de l'objet tarif
		$o = new site_tarif($row);
			
		#on retourne l'objet tarif
		return $o;		
	}
		
	#constructeur
	public function __construct($tab)
	{
		$this->tarif_id = $tab["tarif_id"];
		$this->typehebergement_id = $tab["typehebergement_id"];
		$this->tarif_nom = $tab["tarif_nom"];
		$this->tarif_prio = $tab["tarif_prio"];
		$this->tarif_img = $tab["tarif_img"];
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
	
	#ajout d'une tarif
	public function ajoutTarif()
	{
		#test pour la prio
		if($this->tarif_prio == "")
		{
			$rs = site_fonction::recup("tarif","where typehebergement_id = $this->typehebergement_id","","","tarif_id");
			$nb = mysql_num_rows($rs);
			$this->tarif_prio = $nb+1;
		}
		
		#Requete d'ajout de la tarif
		$query = "INSERT INTO ".self::table."_tarif value
				  (
					'$this->tarif_id',
					'$this->typehebergement_id',
					\"".site_fonction::protec($this->tarif_nom)."\",
					'$this->tarif_prio',
					'$this->tarif_img'
				  )";
		$rs = mysql_query($query) or die(mysql_error());
		$this->tarif_id = mysql_insert_id();
				
	}
	
	#methode de msie a jour de tous les tarif_periode lié a ce tarif
	public function ajoutTarifPeriode($Ttarif)#on passe en paramettre un tableau contenant les tarifs
	{
		$i = 0;
		#je recupere les periode de ce type de tarif
		 $rs = site_fonction::recup("periode","where typehebergement_id = ".$this->typehebergement_id." order by periode_id");
		 while($row = mysql_fetch_row($rs))
		 {
			 $i++;
			 #Requete d'ajout du tarif_perdioe
			$query = "INSERT INTO ".self::table."_tarif_periode value
					  (
						'".$row[0]."',
						'$this->tarif_id',
						\"".site_fonction::protec($Ttarif[$i])."\"
					  )";
			$rs2 = mysql_query($query) or die(mysql_error());
		 }
	}
	
	#Mise a jour tarif
	public function majTarif()
	{
		$query = "UPDATE  ".self::table."_tarif set
					typehebergement_id = '$this->typehebergement_id',
					tarif_nom = \"".site_fonction::protec($this->tarif_nom)."\",
					tarif_prio = '$this->tarif_prio',
					tarif_img = '$this->tarif_img'
					WHERE tarif_id = '$this->tarif_id'
					";
		$rs = mysql_query($query) or die($query);			
	}
	
	#methode de msie a jour de tous les tarif_periode lié a ce tarif
	public function majTarifPeriode($Ttarif)#on passe en paramettre un tableau contenant les tarifs
	{
		#je recupere les periode de ce type de tarif
		 $rs = site_fonction::recup("periode","where typehebergement_id = ".$this->typehebergement_id." order by periode_id");
		 $i = 0;
		 while($row = mysql_fetch_row($rs))
		 {
			$i++;
			$query = "UPDATE  ".self::table."_tarif_periode set
					tarif_periode = \"".site_fonction::protec($Ttarif[$i])."\"
					WHERE periode_id = '$row[0]' and tarif_id = $this->tarif_id
					";
			$rs2 = mysql_query($query) or die($query);
		 }
	}
	

	
	#Suppression d'un tarif
	public function supTarif()
	{
		#on supprime la tarif
		$query = "delete from ".self::table."_tarif where tarif_id=".$this->tarif_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les details de ce tarif
		$query = "delete from ".self::table."_detail_tarif where tarif_id=".$this->tarif_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		#on supprime les terif_periode de ce tarif
		$query = "delete from ".self::table."_tarif_periode where tarif_id=".$this->tarif_id;
		$rs = mysql_query($query) or die(mysql_error());
		
		if($this->tarif_img != ""){ @unlink("../icone/".$this->tarif_img); @unlink("../../icone/".$this->tarif_img);}
		
		#mise a jour des prio
		site_prio::majPrio("tarif",$this->tarif_prio,"and typehebergement_id = $this->typehebergement_id");
		
	}
	
	public static function traitementFormulaire($post)
	{
			
		#creation de l'objet tarif de la nouvelelle année
		$a = new site_tarif($post);
		if($a->get("tarif_id") != "")
		{
			
			$a->majTarif();
			echo site_fonction::message("Tarif",utf8_encode("Modification effectuée"));
			
			#on supprime les terif_periode de ce tarif
			$query = "delete from ".self::table."_tarif_periode where tarif_id=".$a->get("tarif_id");
			$rs = mysql_query($query) or die(mysql_error());
			
			#on insere les nouveaux
			$a->ajoutTarifPeriode($post["tarif_periode"]);
		}
		else 
		{
			$a->ajoutTarif();
			echo site_fonction::message("Tarif",utf8_encode("Insertion effectuée"));
			$a->ajoutTarifPeriode($post["tarif_periode"]);
		}
		return $a;	
		
	}
	
	
	public function ajoutIcone($file)
	{
		#mise a jour du chemin
		$chemin = "../icone/";

		#rÃ©cuperation du nom de la photo
		$verif_fichier = $file['image']['name'];
		if($verif_fichier != "")
		{
			if($extension = strrchr(strtolower($verif_fichier), '.') == ".jpg" || strrchr(strtolower($verif_fichier), '.') == ".JPG" || strrchr(strtolower($verif_fichier), '.') == ".jpeg" || strrchr(strtolower($verif_fichier), '.') == ".JPEG" || strrchr(strtolower($verif_fichier), '.') == ".png" || strrchr(strtolower($verif_fichier), '.') == ".gif" || strrchr(strtolower($verif_fichier), '.') == ".PNG"  || strrchr(strtolower($verif_fichier), '.') == ".GIF")
			{
				$infos_img = getimagesize($file['image']['tmp_name']); 
				if($infos_img[0] <= 600 && $infos_img[1] <= 600)
				{
					#recuperation des donnÃ©es de l'image
					$tmp = $file['image']['tmp_name'];
					$name = $file['image']['name'];
					$taille_fichier = $file['image']['size'];
	
					#je recupere l'extension
					$extension = strrchr(strtolower($name), '.');
	
					#Je modifie la valeur du champ nom avec l'extension qui convient
					$this->tarif_img = site_fonction::clean($this->tarif_nom)."-".date("YmdHis").$extension;
	
					#$rename_fichier = cleannom
					$rename_fichier = $this->tarif_img;
	
					#dossier de telechargement des photos
					$rep_imgs = $chemin;
				

					#on charge la photo 
					move_uploaded_file($tmp, $rep_imgs.$rename_fichier);
	
					#Ajout de la photo dans la base de donnÃ©es
					$this->majTarif();					
					echo site_fonction::message("Icone","Insertion effectuée");
				}
				else
				{
					echo site_fonction::message("Icone","Votre image est trop grande","erreur");
				}
			}
			else
			{
				echo site_fonction::message("Icone","Votre image n'est pas au format jpg, png ou gif","erreur");
			}
		}
		else
		{
			echo site_fonction::message("Icone","Vous n'avez selectionné aucune image","erreur");
		}
	}
	
	public function recupImage($chemin = "",$autre = "")
	{
		#chemin
		if($chemin == ""){ $chemin =  "../icone/";}
		
		if($this->tarif_img == "")
		{
			$var =  "Aucun";
		}
		else
		{
			$var = "<img src='".$chemin.$this->tarif_img."' $autre />";
		}
		return $var;
	}
	
	#methode qui renvoi un tableau des tarif_periode liée a ce tarif
	public function recupTabTarif()
	{
		$rs = $rs = site_fonction::recup("periode","where typehebergement_id = $this->typehebergement_id order by periode_id","","","periode_id");
		while($row = @mysql_fetch_row($rs))
		{
			$rs2 = site_fonction::recup("tarif_periode","where tarif_id = $this->tarif_id and periode_id = $row[0]",0,1,"tarif_periode");
			if(mysql_num_rows($rs2)>0)
			{
				$row2 = mysql_fetch_row($rs2);
				$Tab[] = $row2[0];
			}
			else
			{
				$Tab[] = "";
			}
		}
		
		return $Tab;
	}
	
	#methode de recuperation des tarif_periode
	public static function recupTarifPeriode($tarif,$periode)
	{
		$rs = site_fonction::recup("tarif_periode","where tarif_id = $tarif and periode_id = $periode",0,1,"tarif_periode");
		$row = mysql_fetch_row($rs);
		return $row[0];
	}
	
}

?>