<?php
// Variable to configure global behaviour
$header_title = 'GENYMOBILE - Reporting mensuel';
$required_group_rights = 2; // TODO: Et la on voit que mon système d'ACL est merdique... Pas très grave je l'avais vu depuis le début mais bon...

include_once 'header.php';
include_once 'menu.php';

$reporting_data = array();
$geny_tools = new GenyTools();
$geny_project = new GenyProject();
$geny_profile = new GenyProfile();
$geny_client = new GenyClient();
$geny_assignement = new GenyAssignement();
foreach( $geny_client->getAllClients() as $client ){
	$clients[$client->id] = $client;
}

$geny_pt = new GenyProjectType();
foreach( $geny_pt->getAllProjectTypes() as $pt ){
	$pts[$pt->id] = $pt;
}

$geny_ps = new GenyProjectStatus();
foreach( $geny_ps->getAllProjectStatus() as $ps ){
	$pss[$ps->id] = $ps;
}

$geny_ar = new GenyActivityReport();
foreach( $geny_ar->getAllActivityReports() as $ar ){
	$geny_activity = new GenyActivity( $ar->activity_id ); // Contient la charge et l'assignement_id
	if( !isset( $reporting_data[$ar->profile_id] ) )
		$reporting_data[$ar->profile_id] = array();
	if( !isset($reporting_data[$ar->profile_id][$geny_activity->assignement_id]) )
		$reporting_data[$ar->profile_id][$geny_activity->assignement_id]=0;
	$reporting_data[$ar->profile_id][$geny_activity->assignement_id] += $geny_activity->load;
}

?>

<div class="page_title">
	<img src="images/<?php echo $web_config->theme ?>/reporting.png"/><p>Projet</p>
</div>


<script>
	(function($) {
		/*
		 * Function: fnGetColumnData
		 * Purpose:  Return an array of table values from a particular column.
		 * Returns:  array string: 1d data array 
		 * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
		 *           int:iColumn - the id of the column to extract the data from
		 *           bool:bUnique - optional - if set to false duplicated values are not filtered out
		 *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
		 *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
		 * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
		 */
		$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
			// check that we have a column id
			if ( typeof iColumn == "undefined" ) return new Array();
			
			// by default we only wany unique data
			if ( typeof bUnique == "undefined" ) bUnique = true;
			
			// by default we do want to only look at filtered data
			if ( typeof bFiltered == "undefined" ) bFiltered = true;
			
			// by default we do not wany to include empty values
			if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;
			
			// list of rows which we're going to loop through
			var aiRows;
			
			// use only filtered rows
			if (bFiltered == true) aiRows = oSettings.aiDisplay; 
			// use all rows
			else aiRows = oSettings.aiDisplayMaster; // all row numbers
		
			// set up data array	
			var asResultData = new Array();
			
			for (var i=0,c=aiRows.length; i<c; i++) {
				iRow = aiRows[i];
				var aData = this.fnGetData(iRow);
				var sValue = aData[iColumn];
				
				// Ignore html
				if( sValue.indexOf("<a") >= 0 ) continue;
				
				// ignore empty values?
				if (bIgnoreEmpty == true && sValue.length == 0) continue;
		
				// ignore unique values?
				else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;
				
				// else push the value onto the result data array
				else asResultData.push(sValue);
			}
			
			return asResultData;
		}}(jQuery));


		function fnCreateSelect( aData )
		{
			var r='<select><option value=""></option>', i, iLen=aData.length;
			for ( i=0 ; i<iLen ; i++ )
			{
				r += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
			}
			return r+'</select>';
		}
		
		jQuery(document).ready(function(){
			
				var oTable = $('#reporting_list').dataTable( {
					"bJQueryUI": true,
					"bAutoWidth": false,
					"sPaginationType": "full_numbers",
					"oLanguage": {
						"sSearch": "Recherche :",
						"sLengthMenu": "Lignes par page _MENU_",
						"sZeroRecords": "Aucun résultat",
						"sInfo": "Aff. _START_ à _END_ de _TOTAL_ lignes",
						"sInfoEmpty": "Aff. 0 à 0 de 0 lignes",
						"sInfoFiltered": "(filtré de _MAX_ lignes)",
						"oPaginate":{ 
							"sFirst":"Début",
							"sLast": "Fin",
							"sNext": "Suivant",
							"sPrevious": "Précédent"
						}
					}
				} );
				/* Add a select menu for each TH element in the table footer */
				$("tfoot th").each( function ( i ) {
					if( i < 3){
						this.innerHTML = fnCreateSelect( oTable.fnGetColumnData(i) );
						$('select', this).change( function () {
							oTable.fnFilter( $(this).val(), i );
						} );
					}
				} );
			});
</script>

<div id="mainarea">
	<p class="mainarea_title">
		<span class="reporting_monthly_view">
			Reporting mensuel
		</span>
	</p>
	<p class="mainarea_content">
		<p class="mainarea_content_intro">
		Voici la liste des CRA ventilés par collaborateurs, par client et par projet.
		</p>
		<style>
			@import 'styles/default/reporting_monthly_view.css';
		</style>
		<div class="table_container">
		<p>
			
			<table id="reporting_list">
			<thead>
				<th>Collab.</th>
				<th>Client</th>
				<th>Projet</th>
				<th>Nbr. <strong>jours</strong></th>
			</thead>
			<tbody>
			<?php
				foreach( $reporting_data as $profile_id => $data ){
					$geny_profile->loadProfileById($profile_id);
					foreach( $data as $assignement_id => $total_load ){
						$geny_assignement->loadAssignementById($assignement_id);
						$geny_project->loadProjectById($geny_assignement->project_id);
						echo "<tr><td>".GenyTools::getProfileDisplayName($geny_profile)."</td><td>".$clients[$geny_project->client_id]->name."</td><td>".$geny_project->name."</td><td>".($total_load/8)."</td></tr>";
					}
				}
			?>
			</tbody>
			<tfoot>
				<th>Collab.</th>
				<th>Client</th>
				<th>Projet</th>
				<th>Nbr. <strong>jours</strong></th>
			</tfoot>
			</table>
		</p>
		</div>
	</p>
</div>
<div id="bottomdock">
	<ul>
<!-- 		<?php include 'backend/widgets/project_add.dock.widget.php'; ?> -->
	</ul>
</div>
<?php
include_once 'footer.php';
?>
