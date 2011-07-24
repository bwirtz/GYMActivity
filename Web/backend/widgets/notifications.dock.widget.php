<?php

$notification = new GenyNotification();
$notif_count = $notification->getUnreadNotificationCountByProfileId($profile->id);

?>
<li class="notifications">
	<a href="notification_list.php">
		<span class="notifications_count"><span class="notification_count_content"><?php echo $notif_count; ?></span></span>
		<span class="dock_item_title">Notifications</span><br/>
		<span class="dock_item_content">La liste de toutes les notifications que le système vous a envoyé. Le cercle rouge contient le nombre de notifications non lus que vous avez.</span>
	</a>
</li>