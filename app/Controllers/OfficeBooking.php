<?php namespace App\Controllers;

class Officebooking extends BaseController{

    public function index(){
        helper("functions");
        helper("booking");

        if(!check_session()){
            return redirect()->to("/");
        }

        echo view("appends/header");
        echo view("appends/navbar");
        echo view("pages/booking");
        echo view("appends/footer");
    }

    public function changeDate(){
        if(!$this->request->isAJAX()){exit("No direct script access allowed");}

        helper("functions");
        helper("booking");
        
        $db         = \Config\Database::connect();
        $session    = \Config\Services::session();
        $userID     = $session->get("userID");
        $user       = getUserByID($userID);

        $date = $this->request->getGet("date");
        $limit = getBookingLimit($db);

        // SETS UP BOOKING VARIABLES
        $mornBookings   = array();
        $aftnBookings   = array();
        $userBookings   = array();
        $isWeekend      = isWeekend($date);
        $isPast         = isPast($date);

        if (!$isWeekend) {
            // GETS CURRENT USER'S BOOKINGS FOR SEARCHED DATE
            $sql = "SELECT * FROM bookings 
                    INNER JOIN users 
                    ON bookings.userID = users.userID 
                    WHERE users.userID = $userID 
                    AND bookings.bookingDate = '$date'
                    ORDER BY bookings.bookingTime;";

            $query = $db->query($sql);
            $userBookings = $query->getResult();
            
            // GETS ALL MORNING AND AFTERNOON BOOKINGS FOR SEARCHED DATE
            $mornBookings = getBookings($db, $date, 0);
            $aftnBookings = getBookings($db, $date, 1);
        }

        // WORKS OUT IF EACH RADIO OPTION SHOULD BE DISABLED
        $mornFull = count($mornBookings) >= $limit || $isWeekend || $isPast;
        $aftnFull = count($aftnBookings) >= $limit || $isWeekend || $isPast;
        $aldyFull = $mornFull || $aftnFull;

        if(!$isPast){
            if (!$isWeekend){
                // SETS UP DATA FOR DISPLAYING DAILY BOOKINGS
                $dayData["db"]          = $db;
                $dayData["date"]        = $date;
                $dayData["todayDate"]   = $date;
                $dayData["user"]        = $user;
                $dayData["limit"]       = $limit;
                $output["dayhtml"]      = view("ajaxrenders/booking/day", $dayData);
            }

            // SETS UP DATA FOR DISPLAYING WEEKLY OVERVIEW
            $weekData["db"] 		= $db;
            $weekData["user"] 		= $user;
            $weekData["mondayDate"] = ($isWeekend)? getNextMonday($date): getMonday($date);
            $weekData["todayDate"] 	= $date;
            $weekData["limit"]      = $limit;
            $output["weekhtml"]     = view("ajaxrenders/booking/week", $weekData);
        }

        // SETS UP DATA FOR DISPLAYING THE CANCEL PANEL IF THE USER HAS BOOKINGS ON THAT DAY
        if (count($userBookings) > 0){
            $cancelData["bookings"] = $userBookings;
            $output["cancelData"]   = $userBookings; // LIST OF BOOKINGS THAT THE USER CAN CANCEL
            $output["cancelhtml"]   = view("ajaxrenders/booking/cancel-panel", $cancelData);
        }

        $output["isWeekend"]        = $isWeekend; // BOOLEAN TRUE IF SEARCHED DATE IS A WEEKEND
        $output["isPast"]           = $isPast; // BOOLEAN TRUE IF SEARCHED DATE IS IN THE PAST
        $output["mornFull"]         = $mornFull;
        $output["aftnFull"]         = $aftnFull;
        $output["aldyFull"]         = $aldyFull;
        $output["date"]             = dateString($date);
        $output["userBookings"]     = count($userBookings) > 0; // BOOLEAN TRUE IF THE USER HAS BOOKINGS ON THE SEARCHED DATE

        return $this->response->setJSON($output);
    }

    public function book(){
        if(!$this->request->isAJAX()){exit("No direct script access allowed");}

        helper("functions");
        helper("booking");

        $db         = \Config\Database::connect();
        $session    = \Config\Services::session();
        $userID     = $session->get("userID");
        $user       = getUserByID($userID);

        $date       = $this->request->getGet("date");
        $time       = $this->request->getGet("time");
        $behalf     = $this->request->getGet("behalf") == "true";
        $name       = $this->request->getGet("name");
        $email      = $this->request->getGet("email");

        if($behalf && ($name == null || $email == null)){
            $success            = false;
            $bookingMessage     = "Please enter name and email when booking on behalf of a freelancer/client";
        }else{
            if ($time == "week"){
                // BOOKS WHOLE WEEK
                $date = (isWeekend($date))? getNextMonday($date) : $date;
        
                $data           = bookWeek($db, $user, $date, $behalf, $name, $email);
                $success        = $data["success"];
                $emailDates     = $data["dates"];
                $bookingMessage = $data["message"];

            }else if ($time == "aldy"){
                // BOOKS ALL DAY
                $data           = bookDay($db, $user, $date, $behalf, $name, $email);
                $success        = $data["success"];
                $emailDates     = $data["dates"];
                $bookingMessage = $data["message"];

            }else if ($time == "morn" || $time == "aftn"){
                // BOOKS JUST MORNING OR AFTERNOON
                $time               = array("morn" => 0, "aftn" => 1)[$time];
                $code               = createBooking($db, $user, $date, $time, $behalf, $name, $email);
                $success            = $code == 0;
                $emailDates         = ($success)? dateAndTime($date, $time) . "\n" : "";
                $bookingMessage     = bookingMessages($code);
            }else{
                $success            = false;
                $bookingMessage     = "Please select a time of day";
            }
        }

        if ($success){
            // IF AT LEAST 1 BOOKING WAS MADE SUCCESSFULLY, THE EMAILS MUST BE SENT
            $subject    = "Milestone Office Booking Confirmation";
            $address    = ($behalf)? $email : $user->userEmail;
            $emailName  = ($behalf)? $name : $user->userForename ." ". $user->userSurname;

            // EMAIL USER
            $body = "Hi $emailName, you have booked to be in the office on the following date(s): \n".
                    "$emailDates \n".
                    "Please fill out this COVID-19 health declaration form each day before coming in: \n".
                    "https://forms.gle/zSS4gD41yQGkyTE17";

            mail($address, $subject, $body);

            $output["email1"] = $address . "\n" . $body;

            // EMAIL MINELLA
            $body = "$emailName has booked to be in the office on the following date(s): \n".
                    "$emailDates";

            mail("minella@milestonecreative.co.uk", $subject, $body);

            $output["email2"] = $body;
        }
        
        $output["bookingMessage"] = $bookingMessage;
        return $this->response->setJSON($output);
    }

    public function cancel(){
        if(!$this->request->isAJAX()){exit("No direct script access allowed");}

        helper("functions");
        helper("booking");

        $db         = \Config\Database::connect();
        $session    = \Config\Services::session();
        $userID     = $session->get("userID");
        $user       = getUserByID($userID);

        $ID = $this->request->getGet("ID");
        $cancelMessage = cancelMessages(cancelBooking($db, $user, $ID));

        $output["cancelMessage"] = $cancelMessage;
        return $this->response->setJSON($output);
    }
}