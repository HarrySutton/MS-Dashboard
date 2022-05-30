<tr class="date-row" date="<?php echo $date ?>">
    <td class="date">
        <?php
        echo dateString($date);
        $bookings = getDailyBookings($db, $date);
        $count = count($bookings);
        ?>
    </td>
    <td>
        <ul>
            <?php
            if ($count > 0){
                for ($x = 0; $x < $count; $x++){
                    $booking = $bookings[$x];
                    try {
                        $nextBooking = $bookings[$x + 1];
                        $allday = $booking->userID == $nextBooking->userID && $booking->bookingBehalf == $nextBooking->bookingBehalf;
                    } catch(Exception $e){
                        $allday = false;
                    }
                    $name = getBookingName($booking);
                    if ($allday){
                        echo "<li>$name - All Day</li>";
                        $x++;
                    }else{
                        echo "<li>$name - ".timeString($booking->bookingTime)."</li>";
                    }
                }
            }else{
                echo "<li>No bookings</li>";
            }
            ?>
        </ul>
    </td>
</tr>