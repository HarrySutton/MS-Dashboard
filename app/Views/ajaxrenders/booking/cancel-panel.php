<hr>
<h4>Cancel Your Booking</h4>
<table style="width: 100%">
<?php 
foreach ($bookings as $booking) {
    echo 
    "<tr>
        <td>";
    echo timeString($booking->bookingTime);
    echo ($booking->bookingBehalf)? "<br>on behalf of $booking->bookingExtraName": "";
    echo "</td>
        <td>
            <button type='button' id='cancel-$booking->bookingID' cancel='$booking->bookingID' style='width: 100%'>Cancel</button>
        </td>
    </tr>";
};
?>
</table>
