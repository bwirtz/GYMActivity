<?php

//  Copyright (C) 2011 by GENYMOBILE & Quentin Désert
//  qdesert@genymobile.com
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

session_start();
$required_group_rights = 6;
$auth_granted = false;

header('Content-Type: application/json;charset=UTF-8');

include_once 'ajax_authent_checking.php';
include_once 'ajax_toolbox.php';

try {
	$intranet_pages = array();
	if( $auth_granted ) {
		$tmp_intranet_page = new GenyIntranetPage();
		$results = array();
		$intranet_category_id = getParam( "intranet_category_id", -1 );
		$intranet_type_id = getParam( "intranet_type_id", -1 );
		$intranet_tag_id = getParam( "intranet_tag_id", -1 );

		if( $intranet_tag_id > 0 ) {
			$results = $tmp_intranet_page->getIntranetPagesByTag( $intranet_tag_id );
		}
		if( $intranet_type_id > 0 ) {
			$results = $tmp_intranet_page->getIntranetPagesByType( $intranet_type_id );
		}
		else if( $intranet_category_id > 0 ) {
			$results = $tmp_intranet_page->getIntranetPagesByCategory( $intranet_category_id );
		}
		else {
			$results = $tmp_intranet_page->getAllIntranetPages();
		}
		
		foreach( $results as $pg ){
			$tmp = array();
			foreach( get_object_vars( $tmp_intranet_page ) as $field => $value ) {
				$tmp[$field] = $pg->$field ;
			}
			$intranet_pages[] = $tmp;
		}
		$data = json_encode( $intranet_pages );
		echo $data;
	}
} catch (Exception $e) {
    echo "Exception: ".$e->getMessage(), "\n";
}

?>