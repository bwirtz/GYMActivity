<?php
// Variable to configure global behaviour
$header_title = 'GenY Mobile - Reporting';
$required_group_rights = 5;

include_once 'header.php';
include_once 'menu.php';


?>

<div class="page_title">
	<img src="images/default/reporting.png"/><p>Reporting</p>
</div>

<div id="maindock">
	<ul>
		<?php
			include 'backend/widgets/reporting_monthly_view.dock.widget.php';
		?>
	</ul>
</div>

<?php
include_once 'footer.php';
?>
