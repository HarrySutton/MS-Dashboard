<h2>Overview for Week Commencing <?php echo dateString($mondayDate) ?></h2>
<table class="weekly-overview-table">
<?php 
$date = $mondayDate;
for ($x = 0; $x < 5; $x++) {
    $data["db"]         = $db;
    $data["user"]       = $user;
    $data["date"]       = $date;
    $data["todayDate"]  = $todayDate;
    $data["limit"]      = $limit;
    echo view("ajaxrenders/booking/week-day", $data);

    $date = getNextDay($date);
};
?>
</table>