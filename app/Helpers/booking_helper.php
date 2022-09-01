<?php

// CONVERTS INT RETURNED BY createBooking INTO A STRING EXPLAINING THE RESULT
if(!function_exists("bookingMessages")){
    function bookingMessages($i){
        return array(
            0 => "Your place has been booked",
            1 => "Server error, please try again later",
            2 => "This date is on a weekend",
            3 => "This date is in the past",
            4 => "You already have a booking for this date and time",
            5 => "The office is already full at this date and time"
        )[$i];
    }
}

// CREATES BOOKING, INSERTS IT TO THE DATABASE
if(!function_exists("createBooking")){
    function createBooking(&$db, &$user, $date, $time, $behalf, $name, $email)
    {
        // BACKEND VALIDATION
        if (isWeekend($date)){
            return 2;

        }else if (isPast($date)) {
            return 3;

        }else if (alreadyBooked($db, $user, $date, $time) && !$behalf){
            return 4;

        }else if (isFull($db, $date, $time)){
            return 5;

        }else {
            $name   = ($behalf)? $name  : "NULL";
            $email  = ($behalf)? $email : "NULL";
            $behalf = ($behalf)? 1 : 0;
            
            $sql = "INSERT INTO bookings 
                    (bookingid, userID, bookingDate, bookingTime, bookingBehalf, bookingExtraName, bookingExtraEmail) 
                    VALUES (NULL, $user->userID, '$date', $time, $behalf, '$name', '$email');";

            return ($db->query($sql))? 0 : 1;
        }
    }
}

// CREATES ALL DAY BOOKINGS FOR A WHOLE WEEK
if(!function_exists("bookWeek")){
    function bookWeek(&$db, &$user, $date, $behalf, $name, $email)
    {
        $date = getMonday($date);
        $data["dates"] = "";
        $data["message"] = "";
        $data["success"] = false;

        for ($x = 1; $x <= 5; $x++){
            $dayData = bookDay($db, $user, $date, $behalf, $name, $email);

            $data["dates"]      .= $dayData["dates"];
            $data["message"]    .= ($data["success"])? "" : $date . "<br>" . $dayData["message"];
            $data["success"]    .= $data["success"] || $dayData["success"];

            $date = getNextDay($date);
        }
        $data["message"] .= ($data["success"])? "Your places have been booked" : "";
        return $data;
    }
}

// CREATES ALL DAY BOOKING
if(!function_exists("bookDay")){
    function bookDay(&$db, &$user, $date, $behalf, $name, $email)
    {
        $morn = createBooking($db, $user, $date, 0, $behalf, $name, $email);
        $aftn = createBooking($db, $user, $date, 1, $behalf, $name, $email);

        $success = $morn == 0 || $aftn == 0;

        if ($morn == 0 && $aftn == 0){
            $emailDates  = removeSup(dateString($date)) . " all day \n";
        }else{
            $emailDates  = ($morn == 0)? dateAndTime($date, 0) . "\n" : "";
            $emailDates .= ($aftn == 0)? dateAndTime($date, 1) . "\n" : "";
        }

        $bookingMessage  = "Morning: " . bookingMessages($morn) . "<br>";
        $bookingMessage .= "Afternoon: " . bookingMessages($aftn) . "<br>";

        return array(
            "success" => $success,
            "dates"   => $emailDates,
            "message" => $bookingMessage
        );
    }
}

// CONVERTS INT RETURNED BY cancelBooking INTO A STRING EXPLAINING THE RESULT
if(!function_exists("cancelMessages")){
    function cancelMessages($i){
        return array(
            0 => "Booking successfully cancelled",
            1 => "Server error, please try again later",
            2 => "No booking with given id found",
            3 => "You do not have a booking with given id"
        )[$i];
    }
}

// DELETES BOOKING FROM DATABASE AND SENDS REQUIRED EMAIL
if(!function_exists("cancelBooking")){
    function cancelBooking(&$db, &$user, $id)
    {
        $sql = "SELECT * FROM bookings
                INNER JOIN users
                ON bookings.userID = users.userID
                WHERE bookings.bookingID = $id";

        $query = $db->query($sql);
        $bookings = $query->getResult();
        
        if (count($bookings) == 0){
            return 2;

        // CHECKS THAT THE CURRENT USER MADE THE BOOKING THEY'RE TRYING TO CANCEL
        }else if ($bookings[0]->userID != $user->userID){
            return 3;

        }else{
            $booking = $bookings[0];
            $sql = "DELETE FROM bookings 
                    WHERE bookings.bookingID = $id;";

            if ($db->query($sql)) {
                $subject = "Milestone Office Booking";
                $body = getBookingName($booking). " cancelled their booking on $booking->bookingDate in the " . timeString($booking->bookingTime);
                mail("minella@milestonecreative.co.uk", $subject, $body);

                return 0;
            } else {
                return 1;
            }
        }
    }
}

// GETS AN ARRAY OF ALL BOOOKINGS FOR A GIVEN DATE AND TIME
if(!function_exists("getBookings")){
    function getBookings(&$db, $date, $time)
    {
        $sql = "SELECT * FROM bookings 
                INNER JOIN users 
                ON bookings.userID = users.userID 
                WHERE bookings.bookingDate = '$date' 
                AND bookings.bookingTime = $time
                ORDER BY bookings.bookingID;";

        $query = $db->query($sql);
        $bookings = $query->getResult();
        return $bookings;
    }
}

// GETS ALL BOOKINGS FOR ONE DAY, SORTED BY USER NAME, THEN TIME OF DAY
if(!function_exists("getDailyBookings")){
    function getDailyBookings(&$db, $date)
    {
        $sql = "SELECT * FROM bookings 
                INNER JOIN users 
                ON bookings.userID = users.userID 
                WHERE bookings.bookingDate = '$date' 
                ORDER BY users.userName, bookings.bookingExtraName, bookings.bookingTime;";

        $query = $db->query($sql);
        $bookings = $query->getResult();
        return $bookings;
    }
}

// FINDS IF THE USER HAS A BOOKING FOR GIVEN DATE AND TIME
if(!function_exists("alreadyBooked")){
    function alreadyBooked(&$db, &$user, $date, $time)
    {
        $sql = "SELECT * FROM bookings 
                WHERE bookings.bookingDate = '$date' 
                AND bookings.bookingTime = $time 
                AND bookings.userID = $user->userID
                AND bookings.bookingBehalf = 0;";

        $query = $db->query($sql);
        $result = $query->getResult();
        return count($result) > 0;
    }
}

// FINDS IF THE OFFICE IS FULL AT THE GIVEN DATE AND TIME
if(!function_exists("isFull")){
    function isFull(&$db, $date, $time)
    {
        $sql = "SELECT * FROM bookings 
                WHERE bookings.bookingDate = '$date' 
                AND bookings.bookingTime = $time;";

        $query = $db->query($sql);
        $result = $query->getResult();
        return count($result) >= getBookingLimit($db);
    }
}

// FINDS THE MAX NUMBER OF BOOKINGS ALLOWED FROM THE settings DATABASE TABLE 
if(!function_exists("getBookingLimit")){
    function getBookingLimit(&$db)
    {
        $sql = "SELECT * FROM settings 
                WHERE settings.settingsCode = 'maxOfficeBookings';";

        return $db->query($sql)->getResult()[0]->settingsValue;
    }
}

// GETS NAME FROM BOOKING (EITHER THE PERSON WHO MADE THE BOOKING OR THE PERSON THEY MADE IT ON BEHALF OF)
if(!function_exists("getBookingName")){
    function getBookingName($booking)
    {
        return ($booking->bookingBehalf)? $booking->bookingExtraName : $booking->userForename ." ". $booking->userSurname;
    }
}

// GETS EMAIL ADDRESS FROM BOOKING (EITHER THE PERSON WHO MADE THE BOOKING OR THE PERSON THEY MADE IT ON BEHALF OF)
if(!function_exists("getBookingEmail")){
    function getBookingEmail($booking)
    {
        return ($booking->bookingBehalf)? $booking->bookingExtraEmail : $booking->userEmail;
    }
}

// FINDS THE DATE OF THE LATEST MONDAY BEFORE GIVEN DATE
if(!function_exists("getMonday")){
    function getMonday($date)
    {
        $time = strtotime($date);
        return date('Y-m-d', $time - ((date("N", $time) - 1) * 86400));
    }
} 

// FINDS THE DATE OF THE DAY AFTER THE GIVEN DATE
if(!function_exists("getNextDay")){
    function getNextDay($date)
    {
        return date('Y-m-d', strtotime($date) + 86400);
    }
} 

// FINDS THE DATE OF THE NEXT MONDAY AFTER THE GIVEN DATE
if(!function_exists("getNextMonday")){
    function getNextMonday($date)
    {
        return getMonday(date('Y-m-d', strtotime($date) + 604800));
    }
} 

// FINDS IF THE GIVEN DATE IS BEFORE TODAY'S DATE
if(!function_exists("isPast")){
    function isPast($date)
    {   
        return strtotime($date) < strtotime(date("Y-m-d"));
    }
}

// CONVERTS TIMESTAMP TO STRING (Day the Numberth of Month Year)
if(!function_exists("dateString")){
    function dateString($date)
    {
        $timeStamp = strtotime($date);
        return date("l", $timeStamp) . " the " . date("j", $timeStamp) . "<sup>" . date("S", $timeStamp) . "</sup> of " . date("F", $timeStamp);
    }
}

// REMOVES <sup></sup> TAGS FROM A STRING (STRINGS RETURNED BY dateString)
if(!function_exists("removeSup")){
    function removeSup($string)
    {
        return str_replace("</sup>", "", str_replace("<sup>", "", $string));
    }
}

// CONVERTS BOOLEAN TIME VALUE FROM DATABASE INTO STRING AFTERNOON OR MORNING
if(!function_exists("timeString")){
    function timeString($time)
    {
        return ($time)? "Afternoon": "Morning";
    }
}

// MAKES A READABLE STRING FROM A DATE AND TIME OF DAY
if(!function_exists("dateAndTime")){
    function dateAndTime($date, $time)
    {
        return removeSup(dateString($date)) . " in the " . timeString($time);
    }
}

// MAKES THE TEXT OF AN ELEMENT ORANGE IF PARAM IS TRUE
if(!function_exists("highlight")){
    function highlight($bool)
    {
        return ($bool)? "color: #f47920;" : "";
    }
}
