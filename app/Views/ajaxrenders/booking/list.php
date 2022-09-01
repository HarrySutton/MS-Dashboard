<div>
    <ul class="weekdays">
        <li style="width: 100%; text-align: left; padding-left: 10px"><?php echo $title ?><span> (<?php echo count($bookings)."/".$limit?>)</span></li>
    </ul>
    <?php     
    if (count($bookings) > 0){
        echo "<ul class='days' style='flex-direction: column'>";
        foreach ($bookings as $booking) {
            $name = getBookingName($booking);
            echo "<li style='text-align: left; width: 100%; ".highlight($user->userID == $booking->userID)."'>$name</li>";
        };
        echo "</ul>";
    };
    ?>
</div>