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
include_once 'GenyDatabaseTools.php';

class GenyProjectStatus extends GenyDatabaseTools {
	public $id = -1;
	public $name = '';
	public $description = '';
	public function __construct($id = -1){
		parent::__construct("ProjectStatus",  "project_status_id");
		$this->id = -1;
		$this->name = '';
		$this->description = '';
		if($id > -1)
			$this->loadProjectStatusById($id);
	}
	public function insertNewProjectStatus($id,$name,$description){
		$query = "INSERT INTO ProjectStatus VALUES($id,'".mysql_real_escape_string($name)."','".mysql_real_escape_string($description)."')";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProjectStatus MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function getProjectStatusListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT project_status_id,project_status_name,project_status_description FROM ProjectStatus";
		if(count($restrictions) > 0){
			$query .= " WHERE ";
			foreach($restrictions as $key => $value) {
				$query .= $value;
				if($key != $last_index){
					$query .= " AND ";
				}
			}
		}
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyProjectStatus MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$object_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_object = new GenyProjectStatus();
				$tmp_object->id = $row[0];
				$tmp_object->name = $row[1];
				$tmp_object->description = $row[2];
				$object_list[] = $tmp_object;
			}
		}
// 		mysql_close();
		return $object_list;
	}
	public function getAllProjectStatus(){
		return $this->getProjectStatusListWithRestrictions( array() );
	}
	public function loadProjectStatusByName($name){
		$objects = $this->getProjectStatusListWithRestrictions(array("project_status_name='".mysql_real_escape_string($name)."'"));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->name = $object->name;
			$this->description = $object->description;
		}
	}
	public function loadProjectStatusById($id){
		$objects = $this->getProjectStatusListWithRestrictions(array("project_status_id=".mysql_real_escape_string($id)));
		$object = $objects[0];
		if(isset($object) && $object->id > -1){
			$this->id = $object->id;
			$this->name = $object->name;
			$this->description = $object->description;
		}
	}
}
?>