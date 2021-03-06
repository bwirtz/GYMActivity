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

class GenyClient extends GenyDatabaseTools {
	public $id = -1;
	public $name = '';
	public function __construct($id = -1){
		parent::__construct("Clients",  "client_id");
		$this->id = -1;
		$this->name = '';
		if($id > -1)
			$this->loadClientById($id);
	}
	public function deleteClient($id=0){
		if(is_numeric($id)){
			if( $id == 0 && $this->id > 0 )
				$id = $this->id;
			if($id <= 0)
				return -1;
			// Avant de supprimer le client il faut supprimer tous les projets de ce client.
			$p_object = new GenyProject();
			foreach( $p_object->getProjectsByClientId($id) as $p ){
				if( $p->deleteProject() <= 0 ) // Celà va déclencher la suppression des Assignements
					return -1;
			}
			$query = "DELETE FROM Clients WHERE client_id=$id";
			if( $this->config->debug )
				error_log("[GYMActivity::DEBUG] GenyClient MySQL DELETE query : $query",0);
			if(mysql_query($query,$this->handle))
				return 1;
			else
				return -1;
		}
		return -1;
	}
	public function insertNewClient($id,$name){
		$query = "INSERT INTO Clients VALUES($id,'".mysql_real_escape_string($name)."')";
		if( $this->config->debug )
			error_log("[GYMActivity::DEBUG] GenyClient MySQL query : $query",0);
		if( mysql_query( $query, $this->handle ) ) {
			return mysql_insert_id( $this->handle );
		}
		else {
			return -1;
		}
	}
	public function getClientsListWithRestrictions($restrictions){
		// $restrictions is in the form of array("project_id=1","project_status_id=2")
		$last_index = count($restrictions)-1;
		$query = "SELECT client_id,client_name FROM Clients";
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
			error_log("[GYMActivity::DEBUG] GenyClient MySQL query : $query",0);
		$result = mysql_query($query, $this->handle);
		$client_list = array();
		if (mysql_num_rows($result) != 0){
			while ($row = mysql_fetch_row($result)){
				$tmp_client = new GenyClient();
				$tmp_client->id = $row[0];
				$tmp_client->name = $row[1];
				$client_list[] = $tmp_client;
			}
		}
// 		mysql_close();
		return $client_list;
	}
	public function getAllClients(){
		return $this->getClientsListWithRestrictions( array() );
	}
	public function searchClients($term){
		$q = mysql_real_escape_string($term);
		return $this->getClientsListWithRestrictions( array("client_name LIKE '%$q%'") );
	}
	public function loadClientByName($name){
		$clients = $this->getClientsListWithRestrictions(array("client_name='".mysql_real_escape_string($name)."'"));
		$client = $clients[0];
		if(isset($client) && $client->id > -1){
			$this->id = $client->id;
			$this->name = $client->name;
		}
	}
	public function loadClientById($id){
		$clients = $this->getClientsListWithRestrictions(array("client_id=".mysql_real_escape_string($id)));
		$client = $clients[0];
		if(isset($client) && $client->id > -1){
			$this->id = $client->id;
			$this->name = $client->name;
		}
	}
}
?>