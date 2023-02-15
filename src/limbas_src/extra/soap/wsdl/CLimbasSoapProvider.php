<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


class CLimbasSoapProvider extends CLimbasSoapComponent implements ILimbasSoapProvider
{
	private $soapTable;
	private $soapQuery;
	private $soapJoin;
	private $className;
	
	public function __construct($className)
	{
		$this->className = $className;
		$this->soapTable = CLimbasSoapFactory::getInstance()->createTable($className);
		$this->soapQuery = CLimbasSoapFactory::getInstance()->createQuery($className);
		$this->soapJoin = CLimbasSoapFactory::getInstance()->createJoin($className);
	}
	
	public function getClassName() {
		return $this->className;
	}

	public function query($model) {
		
		$retval = array();
		$retval = getGtabLevel($model,$this->soapTable->tableId);

		return $retval;
	}
	
	public function getByPk($id) {
		
		$retval = array();
		if($retval = getGtabLevel($this->soapTable,$this->soapTable->tableId,null,$id)){
			return $retval;
		}else{
			throw new Exception("failure by get data [$id] - ".implode("\n",$GLOBALS["alert"]));
		}
	}
	
	public function delete($id) {
		global $gtab;
		
		$gtabid = $this->soapTable->tableId;
		
		if(del_data($gtabid,$id,0)){
			return $id;
		}
		
		throw new Exception("failure by delete data [$id] - ".implode("\n",$GLOBALS["alert"]));

	}
	
	public function insert($modellist,$gtabid=null,$rel_table=null,$rel_field=null,$rel_dat=null) {
		global $LINK;
		global $gtab;
		global $gfield;

		if(!$gtabid){
			$gtabid = $this->soapTable->tableId;
		}

		if(!$gtab["add"][$gtabid] OR !$gtab["edit"][$gtabid] OR !$LINK[1]){return false;}
		
		$retval = new InsertResultArray();
		
		$items = $modellist->items;
		if(is_object($items)){
			$items = array($items);
		}
		
		# array of datasets
		foreach($items as $model) {

			$repres = '';
			$forceID = null;
		
			# get model attributes
			$sattr = $model->attributes;

			# force new ID
			if($sattr['ID']){
				$forceID = $sattr['ID'];
			}

			$result = new InsertResult();

			# create new dataset
			if($new_ID = new_record($gtabid,1,$rel_field,$rel_table,$rel_dat,null,null,$forceID)){
				# update array
				foreach ($sattr as $key => $value){
					if($fid = $gfield[$gtabid]["argresult_name"][$key]){
						if($gfield[$gtabid]["field_type"][$fid] == 11 AND $value->items){
							$this->insert($value,$gfield[$gtabid]["verkntabid"][$fid],$gtabid,$fid,$new_ID);
						}else{
							if($gfield[$gtabid]["mainfield"] == $fid){$repres = $value;}
							$update[$gtabid.",".$fid.",".$new_ID] = $value;
						}
					}
				}
				# update data
				if($update){
					if(!update_data($update,3,0)){
						$result->ID = -1;
						$result->REPRESENTATION = 'failure by update data - '.implode("\n",$GLOBALS["alert"]);
					}else{
						$result->ID = $new_ID;
						$result->REPRESENTATION = $repres;
					}
				}
				
			}else{
				$result->ID = -1;
				$result->REPRESENTATION = 'failure by insert data - '.implode("\n",$GLOBALS["alert"]);
			}
			
			$retval->items[] = $result;
		
		}
		
		return $retval;

	}
	
	public function update($model,$gtabid=null,$rel_table=null,$rel_field=null,$rel_dat=null) {
		global $LINK;
		global $gtab;
		global $gfield;

		if(!$gtabid){
			$gtabid = $this->soapTable->tableId;
		}

		if(!$gtab["edit"][$gtabid]){return false;}


		if($rel_table){
			$items = $model->items;
		}else{
			$items[] = $model;
		}
		
		if(is_object($items)){
			$items = array($items);
		}
		
		foreach ($items as $ikey => $sattrval){
			
			# get model attributes
			$sattr = $sattrval->attributes;
			
			# set data ID
			$ID = $sattr['ID'];
	
			# update fields
			foreach ($sattr as $key => $value){
				if($key == 'ID'){continue;}
				if($fid = $gfield[$gtabid]["argresult_name"][$key] AND $value){
					if($gfield[$gtabid]["field_type"][$fid] == 11 AND $value->items){
						$this->update($value,$gfield[$gtabid]["verkntabid"][$fid],$gtabid,$fid,$ID);
					}else{
						$update[$gtabid.",".$fid.",".$ID] = $value;
					}
				}
			}
			
			if($update){
				if(update_data($update,3,0)){
					#return($ID);
				}else{
					throw new Exception("failure by update data [$ID] - ".implode("\n",$GLOBALS["alert"]));
				}
			}

		}
	}
	
	public function join ($model){
		global $gfield;
		
		$gtabid = $this->soapTable->tableId;
		$sattr = $model->attributes;
		
		$left_id = $sattr['ID'];

		if($sattr){
		foreach ($sattr as $field => $value){
			$fid = $gfield[$gtabid]["argresult_name"][$field];
			$relation = init_relation($gtabid,$fid,$left_id,$value);
			if(!set_relation($relation)){
				throw new Exception("failure by join data [$value] - ".implode("\n",$GLOBALS["alert"]));
			}
		}}
	
	}
	
	public function getClassMap() {
		return array_merge(array('InsertResult' => 'InsertResult', 'InsertResultArray' => 'InsertResultArray'), 
				$this->soapTable->getClassMap(), $this->soapQuery->getClassMap(), $this->soapJoin->getClassMap());
	}
	
	public function getWsdlOperations() {
		$retval = array();
		
		$retval['query'] = '';
		$retval['getByPk'] = '';
		$retval['delete'] = '';
		$retval['insert'] = '';
		$retval['update'] = '';
		$retval['join'] = '';
		
		return $retval;
	}
	
	public function getWsdlTypes() {
		return array_merge(array(
					'InsertResult' => array('ID' => 'xsd:long', 'REPRESENTATION' => 'xsd:string'),
					'InsertResultArray' => 'complex_Type_Array'
				),
				$this->soapTable->getWsdlTypes(), $this->soapQuery->getWsdlTypes(), $this->soapJoin->getWsdlTypes());
	}
	
	public function getWsdlMessages() {
		$retval = array();
		
		$retval['queryRequest'] = array('params' => array('tns:' . $this->className . 'Query'));
		$retval['queryResponse'] = array('return' => array('tns:' . $this->className . 'Array'));
		$retval['getByPkRequest'] = array('params' => array('xsd:long'));
		$retval['getByPkResponse'] = array('return' => array('tns:' . $this->className));
		$retval['deleteRequest'] = array('params' => array('xsd:long'));
		$retval['deleteResponse'] = array();
		$retval['insertRequest'] = array('params' => array('tns:' . $this->className . 'Array'));
		$retval['insertResponse'] = array('return' => array('tns:InsertResultArray'));
		$retval['updateRequest'] = array('params' => array('tns:' . $this->className));
		$retval['updateResponse'] = array();
		$retval['joinRequest'] = array('params' => array('tns:' . $this->className . 'Join'));
		$retval['joinResponse'] = array();
		
		return $retval;
	}	
}




# recursiv gtab_erg
function getGtabLevel($model,$gtabid,$verkn=null,$id=null,$rec=null){

	global $gtab;
	global $gfield;
	global $gverkn;
	global $umgvar;
	global $still_done;
	global $still_sdone;
	static $sattr;

	# get model attributes
	if(is_null($id)){
		if(!$sattr){$sattr = $model->attributes;}
		
		# search params for relations
		if($rec AND $sattr[$gtab['table'][$gtabid]]){
			$sattr = (array) $sattr[$gtab['table'][$gtabid]];
		}
		
		foreach ($sattr as $key => $value){
			$val_set = array();
			$fid = $gfield[$gtabid]["argresult_name"][$key];

			# split AND OR
			if($value AND !is_object($value)){
				$value_AND = explode(' AND ',$value);
				foreach ($value_AND as $and_key => $and_val){
					$value_OR = explode(' || ',$and_val);
					foreach ($value_OR as $or_key => $or_val){
						$val_set['val'][] = $or_val;
						if($or_key){
							$val_set['conc'][] = 2;
						}else{
							$val_set['conc'][] = 1;
						}
					}
				}

				# parse text wildcards
				foreach ($val_set['val'] as $keypart => $valpart){
					if(lmb_substr($valpart,lmb_strlen($valpart)-1,1) == '*' AND lmb_substr($valpart,0,1) == '*'){
						$gsr[$gtabid][$fid][0] = lmb_substr($valpart,1,lmb_strlen($valpart)-2);
						$gsr[$gtabid][$fid]["txt"][0] = 1;
					}elseif(lmb_substr($valpart,lmb_strlen($valpart)-1,1) == '*'){
						$gsr[$gtabid][$fid][0] = lmb_substr($valpart,0,lmb_strlen($valpart)-1);
						$gsr[$gtabid][$fid]["txt"][0] = 3;
					}elseif(lmb_substr($valpart,0,1) == '*'){
						$gsr[$gtabid][$fid][0] = lmb_substr($valpart,1,lmb_strlen($valpart));
						$gsr[$gtabid][$fid]["txt"][0] = 5;
					}else{
						$gsr[$gtabid][$fid][0] = $valpart;
						$gsr[$gtabid][$fid]["txt"][0] = 2;
					}
					$gsr[$gtabid][$fid]["andor"][0] = $val_set['conc'][$keypart];
				}
				
			}
		}
	
	
		# --- order ---
		if($sattr['q_orderby']) {
			$order = explode(',',$sattr['q_orderby']);
			if(is_array($order)){
				foreach ($order as $okey => $ovalue){
					$soap_order = explode(" ",trim($ovalue));
					$filter["order"][$gtabid][] = $gtabid."&".$gfield[$gtabid]["argresult_name"][lmb_strtoupper($soap_order[0])]."&".$soap_order[1];
				}
			}
		}
		
		# ---- show long content ----
		foreach($gtab["raverkn"][$gtab["verkn"][$gtabid]] as $key => $ragtabid){
			$filter["getlongval"][$ragtabid] = 1;
		}
		
		# ---- show page ------
		if($sattr['q_page']){
			$filter["page"][$gtabid] = $sattr['q_page'];
		}
		# ---- number of rows ------
		if($sattr['q_rows']){
			$filter["anzahl"][$gtabid] = $sattr['q_rows'];
		}
		
		# --- result space ---
		$resultspace = $umgvar["resultspace"];
		if(is_numeric($sattr['q_rows']) AND $sattr['q_rows'] > $umgvar["resultspace"]){
			$GLOBALS["umgvar"]["resultspace"] = $sattr['q_rows'];
		}
	
		# --- no resultlimit ---
		if($sattr['q_nolimit']){
			$filter["nolimit"][$gtabid] = 1;
		}
	
	}

	######### gresult query ##########
	if(!$gresult = get_gresult($gtabid,1,$filter,$gsr,$verkn,$onlyfield,$id,$extension)){
		throw new Exception("failure by select data - ".implode("\n",$GLOBALS["alert"]));
	}


	$bzm = 0;
	//$outputArray = Array();
	$output = CLimbasSoapFactory::getInstance()->createArray($model->getTable());


	# results
	while($bzm < $gresult[$gtabid]["res_viewcount"]) {
		# ---- auf Endlosverknüpfungen prüfen ------
		$still_done[$gtabid] = $gtabid;

		# ---- Endlosverknüpfungen bei Selbstverknüpfung ------
		if($gverkn[$gtabid]["id"] AND $gtab["sverkn"][$gtabid]){
			if(!$still_sdone[$gtabid]){$still_sdone[$gtabid] = array();}
			if(in_array($gresult[$gtabid]["id"][$bzm],$still_sdone[$gtabid])){$bzm++; continue;}
			$still_sdone[$gtabid][] = $gresult[$gtabid]["id"][$bzm];
		}

		# ID
		$did = $gresult[$gtabid]["id"][$bzm];

		if($GLOBALS["lmpar"]["use_noids"]){
			$fieldlist = translateShowFields($gtabid,$fieldlist);
		}

		# Object
		#$obj = clone $model;
		$obj = CLimbasSoapFactory::getInstance()->createTable($model->getTable());

		# fieldlist
		foreach ($gfield[$gtabid]["sort"] as $key => $value){
			if($gfield[$gtabid]["field_type"][$key] != 11 AND $gfield[$gtabid]["field_type"][$key] < 100){
				# fieldtype function
				$fname = "cftyp_".$gfield[$gtabid]["funcid"][$key];
				$result = $fname($bzm,$key,$gtabid,5,$gresult,0);
				if(is_array($result)){$result = implode(chr(13),$result);}
				$fieldName = $gfield[$gtabid]["field_name"][$key];
				$obj->$fieldName = lmb_utf8_encode($result);
			}
		}


		# 1:n / n:m relations
		if($gverkn[$gtabid]["id"]){
			foreach($gverkn[$gtabid]["id"] as $key => $value){

				#$reltabname = ucfirst(lmb_strtolower($gtab['table'][$value]));
				$relfieldname = $gfield[$gtabid]['field_name'][$key];

				# Abbruch bei Endlosverknüpfung
				if($value != $gtabid AND in_array($value,$still_done)) continue;

				$verkn_ = set_verknpf($gtabid,$gfield[$gtabid]["field_id"][$key],$did,0,0,1,0);

				#$relmodel = new $reltabname();
				$relmodel = CLimbasSoapFactory::getInstance()->createTable(lmb_strtolower($gtab['table'][$value]));

				$obj->$relfieldname = getGtabLevel($relmodel,$gfield[$gtabid]["verkntabid"][$key],$verkn_,null,1);

			}
		}

		$output->items[] = $obj;


		# ---- Endlosverknüpfungen leeren ------
		$still_done[$gtabid] = null;
		$still_sdone[$gtabid] = null;

		$bzm++;
	}

	return $output;

}
