<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Ajout congés';
$required_group_rights = 6;

include_once 'header.php';
include_once 'menu.php';

$geny_ptr = new GenyProjectTaskRelation();
$geny_tools = new GenyTools();
date_default_timezone_set('Europe/Paris');

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/conges.png"/><p>Congés</p>
</div>


<div id="mainarea">
	<p class="mainarea_title">
		<span class="conges_add">
			Poser des congés
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Ce formulaire permet de faire vos demandes de congés.<br />
		</p>
		<script>
			jQuery(document).ready(function(){
				$("#formID").validationEngine('init');
				// binds form submission and fields to the validation engine
				$("#formID").validationEngine('attach');
			});
		</script>

		<form id="formID" action="conges_validation.php" method="post">
			<input type="hidden" name="create_conges" value="true" />
			<p>
				<label for="assignement_id">Projet</label>
				<select name="assignement_id" id="assignement_id" />
					<?php
						$geny_assignements = new GenyAssignement();
						foreach( $geny_assignements->getAssignementsListByProfileId( $profile->id ) as $assignement ){
							$p = new GenyProject( $assignement->project_id );
							if( strripos($p->name,'congés') !== false ){
								echo "<option value=\"$assignement->id\" title=\"$p->description\">$p->name</input></option>";
							}
						}
					?>
				</select>
			</p>
			<p>
				<label for="task_id">Tâche</label>
				<select name="task_id" id="task_id" />
				</select>
				<script>
					function getTasks(){
						var project_id = $("#assignement_id").val();
						$.get('backend/ajax_server_side/get_project_tasks_list.php?assignement_id='+project_id, function(data){
							$('.tasks_options').remove();
							$.each(data, function(key, val) {
								$("#task_id").append('<option class="tasks_options" value="' + val[0] + '" title="' + val[2] + '">' + val[1] + '</option>');
							});

						},'json');
					}
					$("#assignement_id").change(getTasks);
					getTasks();
					$(function() {
					$( "#assignement_start_date" ).datepicker();
					$( "#assignement_start_date" ).datepicker('setDate', new Date());
					$( "#assignement_start_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#assignement_start_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#assignement_end_date" ).datepicker();
					$( "#assignement_end_date" ).datepicker('setDate', new Date());
					$( "#assignement_end_date" ).datepicker( "option", "showAnim", "slideDown" );
					$( "#assignement_end_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
					$( "#assignement_start_date" ).change( function(){ $( "#assignement_end_date" ).val( $( "#assignement_start_date" ).val() ) } );
					
				});
				</script>
			</p>
			<p>
				<label for="assignement_start_date">Date de début</label>
				<input name="assignement_start_date" id="assignement_start_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="assignement_end_date">Date de fin</label>
				<input name="assignement_end_date" id="assignement_end_date" type="text" class="validate[required,custom[date]] text-input" />
			</p>
			<p>
				<label for="assignement_load">Charge journalière</label>
				<select name="assignement_load" id="assignement_load">
					<option value="4">4 Heures (1/2 journée)</option>
					<option value="8" selected="selected">8 Heures (1 journée)</option>
					
				</select>
			</p>
			<p>
				<input type="submit" value="Faire la demande" /> ou <a href="#formID">annuler</a>
			</p>
		</form>
	</p>
</div>

<?php
include_once 'footer.php';
?>