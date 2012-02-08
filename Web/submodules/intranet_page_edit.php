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

// Variable to configure global behaviour

$gritter_notifications = array();

$geny_intranet_page = new GenyIntranetPage();
$geny_intranet_category = new GenyIntranetCategory();
$geny_intranet_tag = new GenyIntranetTag();
$geny_intranet_tag_page_relation = new GenyIntranetTagPageRelation();
$geny_intranet_history = new GenyIntranetHistory();

$geny_intranet_page_status = new GenyIntranetPageStatus();
foreach( $geny_intranet_page_status->getAllIntranetPageStatus() as $status ) {
	$statuses[$status->id] = $status;
}

$geny_profile = new GenyProfile();
foreach( $geny_profile->getAllProfiles() as $prof ) {
	$profiles[$prof->id] = $prof;
}

$current_datetime = date("Y-m-d H:i:s");

$create_intranet_page = GenyTools::getParam( 'create_intranet_page', 'NULL' );
$load_intranet_page = GenyTools::getParam( 'load_intranet_page', 'NULL' );
$load_intranet_history = GenyTools::getParam( 'load_intranet_history', 'NULL' );
$edit_intranet_page = GenyTools::getParam( 'edit_intranet_page', 'NULL' );

if( $create_intranet_page == "true" ) {
	$intranet_page_title = GenyTools::getParam( 'intranet_page_title', 'NULL' );
	$intranet_category_id = GenyTools::getParam( 'intranet_category_id', 'NULL' );
	$intranet_type_id = GenyTools::getParam( 'intranet_type_id', 'NULL' );
	$intranet_page_status_id = GenyTools::getParam( 'intranet_page_status_id', 'NULL' );
	$intranet_page_acl_modification_type = GenyTools::getParam( 'intranet_page_acl_modification_type', 'NULL' );
	$profile_id = $profile->id;
	$intranet_tag_list = GenyTools::getParam( 'intranet_tag_id', 'NULL' );
	$intranet_page_description = GenyTools::getParam( 'intranet_page_description', 'NULL' );
	$intranet_page_content = GenyTools::getParam( 'intranet_page_content_editor', 'NULL' );

	if( $intranet_page_title != 'NULL' && $intranet_category_id && $intranet_type_id && $intranet_page_status_id && $intranet_page_acl_modification_type != 'NULL' && $profile_id && $intranet_page_description != 'NULL' && $intranet_page_content != 'NULL' ) {
		$insert_id = $geny_intranet_page->insertNewIntranetPage( 'NULL', $intranet_page_title, $intranet_category_id, $intranet_type_id, $intranet_page_status_id, $intranet_page_acl_modification_type, $profile_id, $intranet_page_description, $intranet_page_content );
		if( $insert_id != -1 ) {
			$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Page Intranet créée avec succès." );
			$geny_intranet_page->loadIntranetPageById( $insert_id );
			$intranet_page_content_to_display = $geny_intranet_page->content;
			
			if( isset( $_POST['intranet_tag_id'] ) && count( $_POST['intranet_tag_id'] ) > 0 ) {
				foreach( $_POST['intranet_tag_id'] as $key => $value ) {
					$geny_intranet_tag = new GenyIntranetTag( $value );
					if( $geny_intranet_tag_page_relation->insertNewIntranetTagPageRelation( $geny_intranet_tag->id, $geny_intranet_page->id ) ) {
						$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Tag $geny_intranet_tag->name ajouté à la page.");
					}
					else {
						$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du tag $geny_intranet_tag->name.");
					}
				}
			}
			
			$history_insert_id = $geny_intranet_history->insertNewIntranetHistory( 'NULL', $insert_id, $intranet_page_status_id, $profile_id, $current_datetime, $intranet_page_content );
			if( $history_insert_id != -1 ) {
// 				$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Historique créé avec succès." );
				$geny_intranet_history->loadIntranetHistoryById( $history_insert_id );
				$intranet_page_content_to_display = $geny_intranet_history->history_content;
			}
			else {
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout de l'historique.");
			}
			
		}
		else {
			$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de la création de la page Intranet." );
		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Erreur','msg'=>"Certains champs obligatoires sont manquant. Merci de les remplir." );
	}
}
else if( $load_intranet_page == 'true' ) {
	$intranet_page_id = GenyTools::getParam( 'intranet_page_id', 'NULL' );
	if( $intranet_page_id != 'NULL' ) {
//TODO: rights_group check or not on this page
// 		if( $profile->rights_group_id == 1  || /* admin */
// 		    $profile->rights_group_id == 2     /* superuser */ ) {
			$geny_intranet_page->loadIntranetPageById( $intranet_page_id );
			$intranet_page_content_to_display = $geny_intranet_page->content;
// 		}
// 		else {
// 			$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger la page Intranet",'msg'=>"Vous n'êtes pas autorisé.");
// 			header( 'Location: error.php?category=intranet_page' );
// 		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Impossible de charger la page Intranet','msg'=>"id non spécifié." );
	}
}
else if( $load_intranet_history == 'true' ) {
	$intranet_history_id = GenyTools::getParam( 'intranet_history_id', 'NULL' );
	if( $intranet_history_id != 'NULL' ) {
//TODO: rights_group check or not on this page
// 		if( $profile->rights_group_id == 1  || /* admin */
// 		    $profile->rights_group_id == 2     /* superuser */ ) {
			$geny_intranet_page->loadIntranetPageByHistoryId( $intranet_history_id );
			$geny_intranet_history->loadIntranetHistoryById( $intranet_history_id );
			$intranet_page_content_to_display = $geny_intranet_history->history_content;
			
// 			foreach( $geny_intranet_page_status->getAllIntranetPageStatus() as $status ) {
// 				$statuses[$status->id] = $status;
// 			}
			
// 		}
// 		else {
// 			$gritter_notifications[] = array('status'=>'error', 'title' => "Impossible de charger la page Intranet",'msg'=>"Vous n'êtes pas autorisé.");
// 			header( 'Location: error.php?category=intranet_page' );
// 		}
	}
	else {
		$gritter_notifications[] = array( 'status'=>'error', 'title' => 'Impossible de charger la page Intranet','msg'=>"id non spécifié." );
	}
}
else if( $edit_intranet_page == 'true' ) {
	$intranet_page_id = GenyTools::getParam( 'intranet_page_id', 'NULL' );
	if( $intranet_page_id != 'NULL' ) {
		$geny_intranet_page->loadIntranetPageById( $intranet_page_id );
		$intranet_page_content_to_display = $geny_intranet_page->content;
		
// 		if( $profile->rights_group_id == 1 /* admin */       ||
// 		    $profile->rights_group_id == 2 /* superuser */ ) {

			$intranet_page_title = GenyTools::getParam( 'intranet_page_title', 'NULL' );
			$intranet_category_id = GenyTools::getParam( 'intranet_category_id', 'NULL' );
			$intranet_type_id = GenyTools::getParam( 'intranet_type_id', 'NULL' );
			$intranet_page_status_id = GenyTools::getParam( 'intranet_page_status_id', 'NULL' );
			$intranet_page_acl_modification_type = GenyTools::getParam( 'intranet_page_acl_modification_type', 'NULL' );
			$intranet_page_description = GenyTools::getParam( 'intranet_page_description', 'NULL' );
			$intranet_page_content = GenyTools::getParam( 'intranet_page_content_editor', 'NULL' );

			if( $intranet_page_title != 'NULL' && $geny_intranet_page->title != $intranet_page_title ) {
				$geny_intranet_page->updateString( 'intranet_page_title', $intranet_page_title );
			}
			if( $intranet_category_id != 'NULL' && $geny_intranet_page->intranet_category_id != $intranet_category_id ) {
				$geny_intranet_page->updateInt( 'intranet_category_id', $intranet_category_id );
			}
			if( $intranet_type_id != 'NULL' && $geny_intranet_page->intranet_type_id != $intranet_type_id ) {
				$geny_intranet_page->updateInt( 'intranet_type_id', $intranet_type_id );
			}
			if( $intranet_page_status_id != 'NULL' && $geny_intranet_page->status_id != $intranet_page_status_id ) {
				$geny_intranet_page->updateInt( 'intranet_page_status_id', $intranet_page_status_id );
			}
			if( $intranet_page_acl_modification_type != 'NULL' && $geny_intranet_page->acl_modification_type != $intranet_page_acl_modification_type ) {
				$geny_intranet_page->updateInt( 'intranet_page_acl_modification_type', $intranet_page_acl_modification_type );
			}
			
			if( isset( $_POST['intranet_tag_id'] ) && count( $_POST['intranet_tag_id'] ) > 0 ) {
				if( $geny_intranet_tag_page_relation->deleteAllIntranetTagPageRelationsByPageId( $geny_intranet_page->id ) ) {
					$error = 0;
					foreach( $_POST['intranet_tag_id'] as $key => $value ) {
						$geny_intranet_tag = new GenyIntranetTag( $value );
						if( $geny_intranet_tag_page_relation->insertNewIntranetTagPageRelation( $geny_intranet_tag->id, $geny_intranet_page->id ) ) {
// 							$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Tag $geny_intranet_tag->name ajouté à la page.");
						}
						else {
							$error++;
							$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout du tag $geny_intranet_tag->name.");
						}
					}
					if( $error == 0 ) {
						$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Les tags ont été mis à jour avec succès.");
					}
				}
			}
			
			if( $intranet_page_description != 'NULL' && $geny_intranet_page->description != $intranet_page_description ) {
				$geny_intranet_page->updateString( 'intranet_page_description', $intranet_page_description );
			}
			if( $intranet_page_content != 'NULL' && $geny_intranet_page->content != $intranet_page_content ) {
				$geny_intranet_page->updateString( 'intranet_page_content', gzcompress( $intranet_page_content ) );
			}
// 		}
		if( $geny_intranet_page->commitUpdates() ) {
			$gritter_notifications[] = array('status'=>'success', 'title' => 'Succès','msg'=>"Page Intranet mise à jour avec succès.");
			$geny_intranet_page->loadIntranetPageById( $intranet_page_id );
			$intranet_page_content_to_display = $geny_intranet_page->content;
			
			$history_insert_id = $geny_intranet_history->insertNewIntranetHistory( 'NULL', $intranet_page_id, $intranet_page_status_id, $profile->id, $current_datetime, $intranet_page_content );
			if( $history_insert_id != -1 ) {
// 				$gritter_notifications[] = array( 'status'=>'success', 'title' => 'Succès','msg'=>"Historique créé avec succès." );
				$geny_intranet_history->loadIntranetHistoryById( $history_insert_id );
				$intranet_page_content_to_display = $geny_intranet_history->history_content;
			}
			else {
				$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur lors de l'ajout de l'historique.");
			}
		}
		else {
			$gritter_notifications[] = array('status'=>'error', 'title' => 'Erreur','msg'=>"Erreur durant la mise à jour de la page Intranet.");
		}
		//FIXME: mis à part les tags, si rien n'est modifié, on a une erreur à la mise à jour
	}
}

?>

<style>
	@import "styles/genymobile-2012/chosen_override.css";
</style>

<div id="mainarea">
	<p class="mainarea_title">
		<img src="images/<?php echo $web_config->theme; ?>/intranet_page_edit.png"></img>
		<span class="intranet_page_edit">
			Modifier page Intranet
		</span>
	</p>
	<p class="mainarea_content">

		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
			<?php
				// Cette fonction est définie dans header.php
				displayStatusNotifications( $gritter_notifications, $web_config->theme );
			?>
		</script>

		<p class="mainarea_content_intro">
		Ce formulaire permet d'éditer une page Intranet existante. Tous les champs doivent être remplis.
		</p>
		
		<form id="select_intranet_page_form" action="loader.php?module=intranet_page_edit" method="post">
			<input type="hidden" name="load_intranet_page" value="true" />
			<p>
				<label for="intranet_page_id">Sélection page Intranet</label>

				<select name="intranet_page_id" id="intranet_page_id" class="chzn-select" onChange="submit()">
					<?php
						$intranet_pages = $geny_intranet_page->getAllIntranetPages();
						
						foreach( $intranet_pages as $intranet_page ) {
							if( $geny_intranet_page->id == $intranet_page->id ) {
								echo "<option value=\"".$intranet_page->id."\" selected>".$intranet_page->title."</option>\n";
							}
							else {
								echo "<option value=\"".$intranet_page->id."\">".$intranet_page->title."</option>\n";
							}
						}
						if( $geny_intranet_page->id < 0 ) {
							$geny_intranet_page->loadIntranetPageById( $intranet_pages[0]->id );
						}
					?>
				</select>
			</p>
		</form>
		
		<form id="select_intranet_history_form" action="loader.php?module=intranet_page_edit" method="post">
			<input type="hidden" name="load_intranet_history" value="true" />
			<p>
				<label for="intranet_history_id">Historique</label>

				<select name="intranet_history_id" id="intranet_history_id" class="chzn-select" onChange="submit()">
					<?php
						$intranet_histories = $geny_intranet_history->getIntranetHistoriesByIntranetPageId( $geny_intranet_page->id );
						
						foreach( $intranet_histories as $intranet_history ) {
							$tmp_profile = $profiles["$intranet_history->profile_id"];
							if( $tmp_profile->firstname && $tmp_profile->lastname ) {
								$profile_name = $tmp_profile->firstname." ".$tmp_profile->lastname;
							}
							else {
								$profile_name = $tmp_profile->login;
							}
							
							$tmp_intranet_page_status = $statuses["$intranet_history->intranet_page_status_id"];
							
							if( $geny_intranet_history->id == $intranet_history->id ) {
								echo "<option value=\"".$intranet_history->id."\" selected>".$intranet_history->history_date." - ".$profile_name." - ".$tmp_intranet_page_status->name."</option>\n";
							}
							else {
								echo "<option value=\"".$intranet_history->id."\">".$intranet_history->history_date." - ".$profile_name." - ".$tmp_intranet_page_status->name."</option>\n";
							}
						}
						if( $geny_intranet_history->id < 0 ) {
							$geny_intranet_history->loadIntranetHistoryById( $intranet_histories[0]->id );
						}
					?>
				</select>
			</p>
		</form>
		
		<form id="formID" action="loader.php?module=intranet_page_edit" method="post">
			<input type="hidden" name="edit_intranet_page" value="true" />
			<input type="hidden" name="intranet_page_id" value="<?php echo $geny_intranet_page->id ?>" />
			
			<p>
				<label for="intranet_category_id">Catégorie</label>
				<select name="intranet_category_id" id="intranet_category_id" class="chzn-select">
					<?php
						foreach( $geny_intranet_category->getAllIntranetCategories() as $intranet_category ) {
							if( $geny_intranet_page->intranet_category_id == $intranet_category->id ) {
								echo "<option value=\"".$intranet_category->id."\" selected>".$intranet_category->name."</option>\n";
							}
							else {
								echo "<option value=\"".$intranet_category->id."\">".$intranet_category->name."</option>\n";
							}
						}
					?>
				</select>
			</p>
			<p>
				<label for="intranet_type_id">Type</label>
				<select name="intranet_type_id" id="intranet_type_id" class="chzn-select" data-placeholder="Choisissez d'abord une catégorie...">
					<option value=""></option>
				</select>
			</p>
			<p>
				<label for="intranet_tag_id">Tags</label>
				<select name="intranet_tag_id[]" id="intranet_tag_id" multiple class="chzn-select" data-placeholder="Choisissez un ou plusieurs tags...">
					<?php
						$current_page_tags = $geny_intranet_tag->getIntranetTagsByPage( $geny_intranet_page->id );
						foreach( $geny_intranet_tag->getAllIntranetTags() as $intranet_tag ) {
							if( in_array( $intranet_tag, $current_page_tags ) ) {
								echo "<option value=\"".$intranet_tag->id."\" selected>".$intranet_tag->name."</option>\n";
							}
							else {
								echo "<option value=\"".$intranet_tag->id."\">".$intranet_tag->name."</option>\n";
							}
						}
					?>
				</select>
			</p>
			<p>
				<label for="intranet_page_status_id">Statut</label>
				<select name="intranet_page_status_id" id="intranet_page_status_id" class="chzn-select" data-placeholder="Choisissez un statut...">
					<option value=""></option>
					<?php
						foreach( $geny_intranet_page_status->getAllIntranetPageStatus() as $intranet_page_status ) {
							if( $geny_intranet_page->status_id == $intranet_page_status->id ) {
								echo "<option value=\"".$intranet_page_status->id."\" selected>".$intranet_page_status->name." - ".$intranet_page_status->description."</option>\n";
							}
							else {
								echo "<option value=\"".$intranet_page_status->id."\">".$intranet_page_status->name." - ".$intranet_page_status->description."</option>\n";
							}
						}
					?>
				</select>
			</p>
			<p>
				<label for="intranet_page_acl_modification_type">Modification Page</label>
				<select name="intranet_page_acl_modification_type" id="intranet_page_acl_modification_type" class="chzn-select">
					<?php
						if( $geny_intranet_page->acl_modification_type == "owner" ) {
							
							echo "<option value=\"owner\" selected>Créateur de la page</option>";
							echo "<option value=\"group\">Membres du groupe du créateur de la page</option>";
							echo "<option value=\"all\">Tout le monde</option>";
						}
						else if( $geny_intranet_page->acl_modification_type == "group" ) {
							
							echo "<option value=\"owner\">Créateur de la page</option>";
							echo "<option value=\"group\" selected>Membres du groupe du créateur de la page</option>";
							echo "<option value=\"all\">Tout le monde</option>";
						}
						else {
							echo "<option value=\"owner\">Créateur de la page</option>";
							echo "<option value=\"group\">Membres du groupe du créateur de la page</option>";
							echo "<option value=\"all\" selected>Tout le monde</option>";
						}
					?>
				</select>
			</p>
			
			<script type="text/javascript">

				function getIntranetTypes(){
					var intranet_category_id = $("#intranet_category_id").val();
					if( intranet_category_id > 0 ) {
						var intranet_type_id = <?php echo $geny_intranet_page->intranet_type_id ?>;
						
						$.get('backend/api/get_intranet_type_list.php?intranet_category_id='+intranet_category_id, function( data ) {
							$('.intranet_types_options').remove();
							$.each( data, function( key, val ) {
								if( val["id"] == intranet_type_id ) {
									$("#intranet_type_id").append('<option class="intranet_types_options" value="' + val["id"] + '" title="' + val["id"] + '" selected>' + val["name"] + '</option>');
								}
								else {
									$("#intranet_type_id").append('<option class="intranet_types_options" value="' + val["id"] + '" title="' + val["id"] + '">' + val["name"] + '</option>');
								}
							});
							$("#intranet_type_id").attr('data-placeholder','Choisissez un type...');
							$("#intranet_type_id").trigger("liszt:updated");
							$("span:contains('Choisissez d'abord une catégorie...')").text('Choisissez un type...');

						},'json');
					}
				}
				$("#intranet_category_id").change( getIntranetTypes );
				getIntranetTypes();
				
			</script>
			
			<p>
				<label for="intranet_page_title">Titre</label>
				<input name="intranet_page_title" id="intranet_page_title" type="text" value="<?php echo $geny_intranet_page->title ?>" class="validate[required,length[2,25]] text-input" maxlength="25"/>
			</p>
			<p>
				<label for="intranet_page_description">Description courte</label>
				<textarea name="intranet_page_description" id="intranet_page_description" class="validate[required,length[2,140]] text-input" maxlength="140"><?php echo $geny_intranet_page->description ?></textarea>
			</p>
			
			<p>
				<textarea id="intranet_page_content_editor" name="intranet_page_content_editor"><?php echo $intranet_page_content_to_display ?></textarea>
			</p>
			<script type="text/javascript">
				CKEDITOR.replace( 'intranet_page_content_editor' );
			</script>
			
			<p>
				<input type="submit" value="Sauvegarder" /> ou <a href="loader.php?module=intranet_page_list">annuler</a>
			</p>
		</form>
	</p>
</div>
<?php
	$bottomdock_items = array();
	if( $profile->rights_group_id == 1  || /* admin */
	    $profile->rights_group_id == 2     /* superuser */ ) {
		array_push( $bottomdock_items, 'backend/widgets/intranet_page_list.dock.widget.php' );
	}
	array_push( $bottomdock_items, 'backend/widgets/intranet_page_add.dock.widget.php' );
?>
