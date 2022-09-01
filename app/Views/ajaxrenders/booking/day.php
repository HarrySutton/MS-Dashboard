<div class="month">
    <ul>
		<li><?php echo dateString($date) ?></li>
	</ul>
</div>
<?php 
$data["user"] = $user;

$data["title"] = "Morning";
$data["limit"] = $limit;
$data["bookings"] = getBookings($db, $date, 0);
echo view("ajaxrenders/booking/list", $data);

$data["title"] = "Afternoon";
$data["limit"] = $limit;
$data["bookings"] = getBookings($db, $date, 1);
echo view("ajaxrenders/booking/list", $data);
?>