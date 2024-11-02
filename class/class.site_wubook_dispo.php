<?php
class site_wubook_dispo{
	private $date_deb;
	private $date_fin;
	private $list_lcode_req=array();
	private $w_return_obj;
	private $warning=array();
	private $list_lcode_available=array();
	private $type_req;
	
	#constructeur
	public function __construct($tab)
	{
		$this->date_deb = $tab["date_deb"];
		$this->date_fin = $tab["date_fin"];
		$this->list_lcode_req = $tab["list_lcode_req"];
		$this->type_req =(isset($tab["type_req"])) ? $tab["type_req"] : 1;
	}
	
	//Enoi de la requete. Retour : erreur=-1 , sinon retour=nb_res
	public function send_request(){
		
		//Paramètres
		$data = array('lcodes' => $this->list_lcode_req, 'dfrom' => $this->date_deb, 'dto' => $this->date_fin,'compact'=>"0");
		$data_string = json_encode($data); 
		$data_string = 'params=' . urlencode($data_string);
		
		//Requete Post
		$ch = curl_init(_WB_FOUNT_DISPO_URL);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('multipart/form-data', 'Content-Length: ' . strlen($data_string)));
		$this->w_return_obj = json_decode(curl_exec($ch));
		curl_close($ch);		
		// print_r($this->w_return_obj);
		
		//Traitement des resultats
		$this->warnings=$this->w_return_obj->warnings;
		
		if(sizeof($this->warnings)>0) return -1;
		else{
			if(sizeof($this->w_return_obj->results)>0){
				foreach($this->w_return_obj->results as $lcode => $capabilities){
					
					$tab_room_tmp=array();
					foreach($capabilities as $capability => $liste_room){
						foreach($liste_room as $room_id=>$price){
							$tab_room_tmp[$room_id]=array($price,$capability);
						}
					}
					if(sizeof($tab_room_tmp)>0){
						$etab_tmp=new site_wubook_etab(array('lcode'=>$lcode,'list_room'=>$tab_room_tmp,'type_req'=>$this->type_req));
						$this->list_lcode_available[$lcode]=$etab_tmp;
					}
				}
			}
			
			return sizeof($this->list_lcode_available);
		}
	}
	
	public function get_tab_lcode_dispo(){
		
		$tab_lcode_tmp=array();
		foreach($this->list_lcode_available as $lcode=>$etablissement){
			$tab_lcode_tmp[]=$lcode;
		}
		
		return $tab_lcode_tmp;
	}
	
	
	#Filtre par etablissement, capacite, tranche tarifaire 
	public function filter_room_list($list_lcode=array(), $nb_adulte=0, $nb_enfant=0, $prix_max=0, $prix_min=0)
	{
		
		if(sizeof($list_lcode)==0) $list_lcode= $this->list_lcode_available;
		
		foreach($this->list_lcode_available as $lcode=>$etablissement){
			if(!(array_key_exists($lcode,$list_lcode))) unset($this->list_lcode_available[$lcode]);
			else{
				foreach($etablissement->get_detail("room_list") as $room_id => $room){
					//Si Nb Adulte inférieur
					if((($nb_adulte>0) && ($room->get_detail('nb_adulte')<$nb_adulte))
						//Si capacité inférieure a adulte + Enfant
						|| (($nb_enfant>0) && ($room->get_detail('capability')<($nb_enfant+$nb_adulte)))
						//Si nb enfant inférieur
						//|| (($nb_enfant>0) && ($room->get_detail('nb_enfant')<$nb_enfant))
							//Si Prix supérieur à prix max
							|| (($room->get_detail('price')>$prix_max) && ($prix_max>0))
								//Si prix inférieur à prix min
								|| (($room->get_detail('price')<$prix_min) && ($prix_min>0))){
								
									$etablissement->unset_room($room_id);
					}
				}
				
				if(sizeof($this->list_lcode_available[$lcode]->get_detail('room_list'))==0) unset($this->list_lcode_available[$lcode]);
				
			}
		}
	}
	
	private function build_sorter($champ,$ordre) {
		return function ($a, $b) use ($champ,$ordre) {
			$liste_chambre_a=$a->get_detail(room_list);
			$first_room_a=reset($liste_chambre_a);
			
			$liste_chambre_b=$b->get_detail(room_list);
			$first_room_b=reset($liste_chambre_b);
			
			if($ordre=="ASC")
				return ($first_room_a->get_detail($champ) > $first_room_b->get_detail($champ));
			else
				return ($first_room_a->get_detail($champ) < $first_room_b->get_detail($champ));
			
		};
	}
	
	public function supprimer_etab($plcode){
		unset($this->list_lcode_available[$plcode]);
	}
	
	#Filtre par etablissement, capacite, tranche tarifaire 
	public function order_dispo($champ, $ordre="DESC")
	{
		$list_lcode= $this->list_lcode_available;
		//print_r($list_lcode);
		
		foreach($this->list_lcode_available as $etablissement){
			$etablissement->sort_room($champ, $ordre);
		}
		
		uasort($this->list_lcode_available, $this->build_sorter($champ, $ordre));
	}
	
	
	#Récupération du prix d'une chambre
	public function get_room_price($req_lcode, $req_room_id)
	{
		$liste_chambre=$this->list_lcode_available[$req_lcode]->get_detail('room_list');
		return $liste_chambre[$req_room_id]->get_detail('price');
		
	}
	
	
	#Récupération du nom d'une chambre
	public function get_room_name($req_lcode, $req_room_id)
	{
		$liste_chambre=$this->list_lcode_available[$req_lcode]->get_detail('room_list');
		return $liste_chambre[$req_room_id]->get_detail('name_fr');
		
	}
	
	#methode de récuperation d'un champ
	public function get($attribut)
	{
		return($this->$attribut);
	}
	
	//Lien pour afficher les résultats sur wubook
	public static function online_resa_link($date_deb,$date_fin,$lcode,$room,$nb_adulte,$tab_enfant,$lang_get,$code_pension=""){
		$url=_WB_RESERVATION_URL;
		
		$date_deb_en=site_fonction_date::convertir_date_en_fr($date_deb);
		$date_fin_en=site_fonction_date::convertir_date_en_fr($date_fin);
		$nb_nuit=site_fonction_date::intervalJour($date_deb_en,$date_fin_en);
		
		$occupancies=$nb_adulte;
		$age_enfant=implode(",",$tab_enfant);
		$occupancies.=",".$age_enfant; 
		
		$referent=$_SESSION['referent_ed'];
		
		//Mise à jour chambre en base : hierachie + capacite + pension par defaut
		$where=" WHERE referent_lib='".$referent."'";
		$rs_referent = site_fonction::recup("referent",$where,"","","referent_id");
		
		if(mysql_num_rows($rs_referent)==1){
			$tab_res=mysql_fetch_array($rs_referent);
			$referent_id=$tab_res[0];
		}else{

			$query = "INSERT INTO ".SITE_CONFIG_TABLE."_referent VALUES(
					'',
					'".$referent."'
				)";
				
			$resu = mysql_query($query) or die($query);
			$referent_id=mysql_insert_id();
		}
		
		
		if((isset($_SESSION['referer_url']))&& ($_SESSION['referer_url']!="")){
			$site_retour_url=urldecode($_SESSION['referer_url']);
			
			$site_retour_url.=urlencode("/iframe/validation_reservation.php?rt_id--".$_SESSION['resa_tmp_id']."||dt_id--".$_SESSION['dossier_tmp_id']."||etab--".$lcode."||referent--".$referent_id);
			//$site_retour_url.=urlencode("/iframe/validation_reservation.php?rt_id=".$_SESSION['resa_tmp_id']."&dt_id=".$_SESSION['dossier_tmp_id']."&etab=".$lcode."&referent=".$referent_id."&action=validation");
			//Enleve le hash pour forcer le rechargeemnt javascript avec le bouton wubook
			//$site_retour_url=str_replace('/#/','/',$site_retour_url);
		}else{
			$site_retour_url=SITE_CONFIG_URL_SITE."validation_reservation.php?rt_id=".$_SESSION['resa_tmp_id']."&dt_id=".$_SESSION['dossier_tmp_id']."&etab=".$lcode."&referent=".$referent_id."&action=validation";
		}
		
		//Paramètres
		$data = array(
		'lcode'         => $lcode,
		'dfrom'         => $date_deb,
		//'dto'        => $date_fin,
		'nights'        => $nb_nuit,
		'lang'        => $lang_get,
		'eurota'        => _EUROTA_CODE,
		'sessionSeed'      => $_SESSION['resa_tmp_id']."|".$_SESSION['dossier_tmp_id']."|".$nb_adulte."-".$age_enfant."|".$code_pension."|".$referent_id,
		'currency'        => "EUR",
		'relink'        => $site_retour_url,
		//'occupancies'        => $occupancies
		);
		
		if($room!=""){
			$data['bookrooms']= $room;
		}
		
		//print_r($data);
		
		$link=$url."?";
		foreach($data as $param => $value){
			$link.=$param."=".urlencode($value)."&";
		}
		$link=substr($link,0,-1);
		return $link;
	}
}

?>
