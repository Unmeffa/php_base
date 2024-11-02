<?php
	class site_wubook_chambre{
		private $room_id;
		private $name_fr;
		private $descr_fr;
		private $lcode;
		private $price;
		private $capability;
		private $nb_adulte;
		private $nb_enfant;
		private $code_pension_defaut;
		private $list_code_pension=array();
		
		private $parent_room_id;
		
		private $tab_descr=array();
		
		#constructeur
		public function __construct($room_id, $lcode, $price, $nb_adult,$type_req=2){
			$this->room_id = $room_id;
			$this->lcode = $lcode;
			$this->nb_adulte = $nb_adult;
			$this->price = $price;
			
			if($type_req==2){
			
				$where=" WHERE wb_lcode=".$lcode." AND wb_chambre_id=".$room_id;
				
				$rs_chambre = site_fonction::recup("wb_chambre",$where,"","","wb_parent_chambre_id, nb_adulte, nb_enfant, code_pension");
				
				$rs_chambre_op = site_fonction::recup("wb_chambre_pension_opt",$where,"","","code_pension, pension_valeur");
				
				if(mysql_num_rows($rs_chambre)==1){
					$tab_chambre=mysql_fetch_array($rs_chambre);
					$this->parent_room_id=$tab_chambre[0];
					$this->nb_adulte=$tab_chambre[1];
					$this->nb_enfant=$tab_chambre[2];
					
					$this->code_pension_defaut=$tab_chambre[3];
					
					if(mysql_num_rows($rs_chambre_op)>0){
						while($tab_res_opt=mysql_fetch_assoc($rs_chambre_op)){
							$this->list_code_pension[$tab_res_opt['code_pension']]=$tab_res_opt['pension_valeur'];
						}
					}
				}else{
					$this->parent_room_id=0;
				}
			}
			
		}
		
		public function get_detail($detail_name){
			return $this->$detail_name;
		}
		
		public function set_detail($detail_name,$value){
			$this->$detail_name=$value;
		}
		
		public function set_desc($tab_detail){
			$this->name_fr=$tab_detail->name_fr;
			$this->descr_fr=$tab_detail->descr_fr;
			$this->capability=$tab_detail->occupancy;
			$this->nb_enfant=$this->capability-$this->nb_adulte;
			//$this->tab_descr=$tab_detail;
			
			
			
		}
		
		public function get_desc($detail_name){
			return $this->tab_descr->$detail_name;
		}
	}

?>