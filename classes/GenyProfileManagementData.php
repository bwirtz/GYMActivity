<?php
//  Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
//  adupuis@genymobile.com
//  http://www.genymobile.com
// 
//  This program is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 3 of the License, or
//  (at your option) any later version.
// 
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
// 
//  You should have received a copy of the GNU General Public License
//  along with this program; if not, write to the
//  Free Software Foundation, Inc.,
//  59 Temple Place - Suite 330, Boston, MA  02111-1307, USA

include_once 'GenyWebConfig.php';
include_once 'GenyProfile.php';
include_once 'GenyDatabaseTools.php';

class GenyProfileManagementData extends GenyDatabaseTools {
	public $id = GENYMOBILE_FALSE;
	public $profile_id = GENYMOBILE_FALSE;
	public $salary = GENYMOBILE_FALSE;
	public $variable_salary = GENYMOBILE_FALSE;
	public $objectived_salary = GENYMOBILE_FALSE;
	public $recruitement_date = '1979-01-01';
	public $is_billable = false;
	public $availability_date = '1979-01-01';
	public $group_leader_id = -1;
	public $technology_leader_id = -1;
	public $category = -1;
	public $resignation_date = '9999-12-31';
	public $country_id = -1;
	public $profile_object = null;
	public function __construct($id = -1){
		parent::__construct("ProfileManagementData",
				    "profile_management_data_id");
		$this->id = -1;
		$this->profile_id = GENYMOBILE_FALSE;
		$this->salary = GENYMOBILE_FALSE;
		$this->recruitement_date = '1979-01-01';
		$this->is_billable = false;
		$this->availability_date = '1979-01-01';
		$this->profile_object = null;
		$this->group_leader_id = -1;
		$this->technology_leader_id = -1;
		$this->category = -1;
		$this->resignation_date = '';
		$this->country_id = -1;
		if($id > -1)
			$this->loadProfileManagementDataById($id);
	}
	public function insertNewProfileManagementData($profile_id,$pmd_salary,$pmd_variable_salary,$pmd_objectived_salary,$pmd_recruitement_date,$pmd_is_billable,$pmd_availability_date,$pmd_gl_id=1,$pmd_tl_is=1, $pmd_category=12, $pmd_resignation_date='9999-12-31',$pmd_country_id=1){
		if( ! is_numeric($profile_id) )
			return GENYMOBILE_FALSE;
		
		if( ! is_numeric($pmd_salary) )
			return GENYMOBILE_FALSE;
		
		if( ! is_numeric($pmd_variable_salary) )
			return GENYMOBILE_FALSE;
		
		if( ! is_numeric($pmd_objectived_salary) )
			return GENYMOBILE_FALSE;
		
		if( ! is_numeric($pmd_gl_id) )
			return GENYMOBILE_FALSE;
		
		if( ! is_numeric($pmd_tl_is) )
			return GENYMOBILE_FALSE;
		
		if( $pmd_is_billable != 'true' && $pmd_is_billable != 'false' && $pmd_is_billable != 0 && $pmd_is_billable != 1)
			return GENYMOBILE_FALSE;
		
		$query = "INSERT INTO ProfileManagementData VALUES(0,$profile_id,$pmd_salary,$pmd_variable_salary,$pmd_objectived_salary,'".mysql_real_escape_string($pmd_recruitement_date)."',".$pmd_is_billable.",'".mysql_real_escape_string($pmd_availability_date)."',$pmd_gl_id,$pmd_tl_is,$pmd_category,'".mysql_real_escape_string($pmd_resignation_date)."',$pmd_country_id)";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProfileManagementData MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function getProfileManagementDataListWithRestrictions($restrictions,$restriction_type = "AND"){
		// $restrictions is in the form of array("profile_id=1","profile_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT profile_management_data_id,profile_id,profile_management_data_salary,profile_management_data_variable_salary,profile_management_data_objectived_salary,profile_management_data_recruitement_date,profile_management_data_is_billable,profile_management_data_availability_date,profile_management_data_group_leader_id,profile_management_data_technology_leader_id,profile_management_data_category, profile_management_data_resignation_date,profile_management_data_country_id FROM ProfileManagementData";
		if(count($restrictions) > 0){
			$query .= " WHERE ";
			$op = mysql_real_escape_string($restriction_type);
			foreach($restrictions as $key => $value) {
				$query .= $value;
				if($key != $last_index){
					$query .= " $op ";
				}
			}
		}
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProfileManagementData MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$pmd_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_pmd = new GenyProfileManagementData();
				$tmp_pmd->id = $row[0];
				$tmp_pmd->profile_id = $row[1];
				$tmp_pmd->salary = $row[2];
				$tmp_pmd->variable_salary = $row[3];
				$tmp_pmd->objectived_salary = $row[4];
				$tmp_pmd->recruitement_date = $row[5];
				$tmp_pmd->is_billable = $row[6];
				$tmp_pmd->availability_date = $row[7];
				$tmp_pmd->group_leader_id = $row[8];
				$tmp_pmd->technology_leader_id = $row[9];
				$tmp_pmd->category = $row[10];
				$tmp_pmd->resignation_date = $row[11];
				$tmp_pmd->country_id = $row[12];
				$tmp_pmd->profile_object = new GenyProfile( $tmp_pmd->profile_id );
				$pmd_list[] = $tmp_pmd;
			}
		}
// 		mysql_close();
		return $pmd_list;
	}
	public function searchProfileManagementData($term){
		$q = mysql_real_escape_string($term);
		return $this->getProfileManagementDataListWithRestrictions( array("profile_management_data_salary LIKE '%$q%'","profile_management_data_recruitement_date LIKE '%$q%'","profile_management_data_availability_date LIKE '%$q%'"), "OR" );
	}
	public function getAllProfileManagementData(){
		return $this->getProfileManagementDataListWithRestrictions( array() );
	}
	public function getAllBillableProfileManagementData(){
// 		return $this->getProfileManagementDataListWithRestrictions(array("profile_management_data_is_billable=true"));
		$really_billable_profile_list = array();
		foreach ($this->getProfileManagementDataListWithRestrictions(array("profile_management_data_is_billable=true")) as $pmd) {
			if( $pmd->getProfile()->is_active ){
				array_push($really_billable_profile_list,$pmd);
			}
		}
		return $really_billable_profile_list;
		
	}
	public function loadProfileManagementDataById($id){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		$profiles = $this->getProfileManagementDataListWithRestrictions(array("profile_management_data_id=$id"));
		$profile = $profiles[0];
		if(isset($profile) && $profile->id > -1){
			$this->id = $profile->id;
			$this->profile_id = $profile->profile_id;
			$this->salary = $profile->salary;
			$this->variable_salary = $profile->variable_salary;
			$this->objectived_salary = $profile->objectived_salary;
			$this->recruitement_date = $profile->recruitement_date;
			$this->is_billable = $profile->is_billable;
			$this->availability_date = $profile->availability_date;
			$this->group_leader_id = $profile->group_leader_id;
			$this->technology_leader_id = $profile->technology_leader_id;
			$this->category = $profile->category;
			$this->resignation_date = $profile->resignation_date;
			$this->country_id = $profile->country_id;
		}
		else {
			GenyProfileManagementData::__construct();
		}
	}
	public function loadProfileManagementDataByProfileId($id){
		if( ! is_numeric($id) )
			return GENYMOBILE_FALSE;
		$profiles = $this->getProfileManagementDataListWithRestrictions(array("profile_id=$id"));
		if(is_array($profiles) && count($profiles) >= 1) {
			$profile = $profiles[0];
			if(isset($profile) && $profile->id > -1){
				$this->id = $profile->id;
				$this->profile_id = $profile->profile_id;
				$this->salary = $profile->salary;
				$this->variable_salary = $profile->variable_salary;
				$this->objectived_salary = $profile->objectived_salary;
				$this->recruitement_date = $profile->recruitement_date;
				$this->is_billable = $profile->is_billable;
				$this->availability_date = $profile->availability_date;
				$this->group_leader_id = $profile->group_leader_id;
				$this->technology_leader_id = $profile->technology_leader_id;
				$this->category = $profile->category;
				$this->resignation_date = $profile->resignation_date;
				$this->country_id = $profile->country_id;
			}
		}
	}
	public function getProfile(){
		if( $this->id <= 0 )
			return GENYMOBILE_FALSE;
		if( isset( $this->profile_object ) && ( $this->profile_object ) == "GenyProfile" )
			$this->profile_object = new $GenyProfile( $this->profile_id );
		return $this->profile_object ;
	}
	public function getAllAvailableProfileManagementData(){
		$today = date('Y-m-d');
		return $this->getProfileManagementDataListWithRestrictions(array("profile_management_data_availability_date >= $today"));
	}
}
?>
