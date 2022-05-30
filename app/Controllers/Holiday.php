<?php namespace App\Controllers;

class Holiday extends BaseController{
    
// ----- INDEX ----------------------------------------------------------
	public function index(){

		helper('functions');
        helper('holiday');
		if(!check_session()){return redirect()->to('/login');}

        $data = getDates();
        $data['manager'] = true;

		echo view('appends/header');
        echo view('appends/navbar');
        echo view('pages/holiday', $data);
        echo view('appends/footer');
    }

// ----- MANAGER APPROVAL -----------------------------------------------
    public function manager(){

        helper('functions');
        helper('holiday');
		if(!check_session()){return redirect()->to('/login');}

        // Check user is a manager
        if(!check_hol_role()){return redirect()->to('/holiday');}

        // Get list of employees
        $db             = \Config\Database::connect();
		$users          = $db->table('users');
		$users          = $users->getWhere(['userActive' => 1]);
        $data['users']  = $users->getResult();

        // Dates
        $data['dates']  = getDates();

		echo view('appends/header');
        echo view('appends/navbar');
        echo view('pages/holiday-manager', $data);
        echo view('appends/footer');
    }

// ----- ALLOWANCE ------------------------------------------------------
    
    public function getAllowance(){

        if($this->request->isAJAX()){
            helper('functions');
            helper('holiday');
            $db = \Config\Database::connect();
            $post = $this->request->getPost('data');
            $output['error'] = ms_error(0);
        }
        else{exit('No direct script access allowed');}

        do{
            // Check Session
            if(!check_session()){$output['error'] = ms_error(403);break;}

            // Set Variables
            $session    = \Config\Services::session();
            $userID     = $session->get('userID');
            $used       = 0;

            // Get user's allowance
            // check selected year***
            $allowance = $db->table('holidayallowance');
            $allowance = $allowance->getWhere(['userID' => $userID, 'holallowYear' => $post['allowYear']]);
            if($allowance->connID->affected_rows == 0){$output['error'] = ms_error(1);break;}
            $allowance = $allowance->getRow();

            // Get users requested holidays
            $requests = $db->table('holidayrequests');
            $requests->where('userID', $userID);
            $requests->like('holFrom', $post['allowYear']);
            $requests = $requests->get();

            // Loop through requests
            foreach($requests->getResult() as $row){
                
                // check selected year
                if($row->holStatus != 'rejected' && $row->holStatus != 'cancelled'){
                    $used = $used + $row->holDays;
                }
            }

            // Check if user role
            $holuser = $db->table('holidayuser');
            $holuser = $holuser->getWhere(['userID' => $userID]);
            $holuser = $holuser->getRow();
            $data['manager'] = $holuser->holRole;

            // Render Output
            $data['allowance'] = $allowance->holallowQuantity;
            $data['used'] = $used;
            $data['remaining'] = $allowance->holallowQuantity - $used;
            $output['html'] = view('ajaxrenders/holiday/allowance', $data);

        }while(FALSE);
        
        // Return Output
        return $this->response->setJSON($output);
    }
    
// ----- CALENDAR -------------------------------------------------------
    public function getCalendar(){

        if($this->request->isAJAX()){
            helper('functions');
            helper('holiday');
            $db = \Config\Database::connect();
            $post = $this->request->getPost('data');
            $output['error'] = ms_error(0);
        }
        else{exit('No direct script access allowed');}

        do{
            // Check Session
            if(!check_session()){$output['error'] = ms_error(403);break;}

            // Get Holiday Requests
            $requests = $db->table('holidayrequests');
            $requests->Like('holFrom', $post['year'].'-'.$post['month']);
            $requests->orLike('holTo', $post['year'].'-'.$post['month']);
            $requests = $requests->get();
            
            // Loop through requests
            if($requests->connID->affected_rows != 0){

                $i = 0;
                $holidays = array();
                $continue = TRUE;

                foreach($requests->getResult() as $row){

                    if($row->holStatus == 'cancelled'){continue;}

                    $dateStart = $current_date = $row->holFrom;
                    $dateEnd = $row->holTo;
                    
                    while(strtotime($current_date) <= strtotime($dateEnd)){

                        foreach($holidays as $hol){
                            if($hol['day'] == date("d", strtotime($current_date))){
                                $continue = FALSE;
                                break;
                            }
                            else{
                                $continue = TRUE;
                            }
                        }

                        if($continue){
        
                            // Assign Variables
                            $holidays[$i]['time']   = "AD";
                            $holidays[$i]['day']    = date("d", strtotime($current_date));
                            $holidays[$i]['month']  = date("m", strtotime($current_date));
                            $holidays[$i]['year']   = date("Y", strtotime($current_date));
        
                            // Increment for Loop
                            $i++;
                        }
                        
                        $current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
                    }
                }
            }
            else{
                $holidays[0]['day'] = $holidays[0]['month'] = $holidays[0]['year'] = null;
            }

            // Month Starting Date
            $date = $post['year']."-".$post['month']."-01";
            $unixTimestamp = strtotime($date);
            $dayOfWeek = date("N", $unixTimestamp);
            $months = getDates();
            if(substr($post['month'], 0, 1) === '0'){$m = ltrim($post['month'], '0');}
            else{$m=$post['month'];}
            //roffy_pre($months);

            // Render Output
            $data['month']['name']  = $months['months'][$m];
            $data['month']['month'] = $post['month'];
            $data['month']['year']  = $post['year'];
            $data['month']['days']  = cal_days_in_month(CAL_GREGORIAN, $post['month'], $post['year']);
            $data['month']['start'] = $dayOfWeek;
            $data['holidays']       = $holidays;
            $output['html']         = view('ajaxrenders/holiday/calendar', $data);
        
        }while(FALSE);

        // Return Output
        return $this->response->setJSON($output);
    }

// ----- SELECTED CALENDAR DATE -----------------------------------------
    public function getSelectedHols(){

        if($this->request->isAJAX()){
            helper('functions');
            helper('holiday');
            $db = \Config\Database::connect();
            $post = $this->request->getPost('data');
            $output['error'] = ms_error(0);
        }
        else{exit('No direct script access allowed');}

        do{
            // Check Session
            if(!check_session()){$output['error'] = ms_error(403);break;}

            // Get holiday requests from the same week
            $givenDate = strtotime($post['date']);
            $weekMon = date('Y-m-d', strtotime('previous monday', $givenDate));
            $weekFri = date('Y-m-d', strtotime('next friday', $givenDate));
            if(date('N', $givenDate) == 1){$weekMon = date('Y-m-d', $givenDate);}
            elseif(date('N', $givenDate) == 5){$weekFri = date('Y-m-d', $givenDate);}
            
            $requests = $db->table('holidayrequests');
            $requests->where('holFrom BETWEEN "'.$weekMon.'" AND "'.$weekFri.'"');
            $requests->where('holTo BETWEEN "'.$weekMon.'" AND "'.$weekFri.'"');
            $weekreq = $requests->get();
            $weekreq = $weekreq->getResult();

            // Add user info to each request
            $users = $db->table('users');
            $dates = getDates();

            foreach($weekreq as $req){
                if($req->holFromTime == "AD"){$req->holFromTime = "";}
                if($req->holToTime == "AD"){$req->holToTime = "";}

                $req->holFrom = "<strong>".$dates['weekdays'][date('N', strtotime($req->holFrom))]." ".$req->holFromTime."</strong><br>".date('d&\n\b\s\p;F', strtotime($req->holFrom));
                $req->holTo = "<strong>".$dates['weekdays'][date('N', strtotime($req->holTo))]." ".$req->holFromTime."</strong><br>".date('d&\n\b\s\p;F', strtotime($req->holTo));
                $requser = $users->getWhere(['userID' => $req->userID]);
                if($requser->connID->affected_rows == 1){$req->user = $requser->getRow();}
                else{$req->user = "Error";}
            }


            $data['weekreq'] = $weekreq;




            //roffy_pre($weekreq);

            $output['html'] = view('ajaxrenders/holiday/holiday-list-week', $data);

        }while(FALSE);

        // Return Output
        return $this->response->setJSON($output);
    }
// ----- GET HOLIDAY LIST -----------------------------------------------
    public function getHolidays(){

        if($this->request->isAJAX()){
            helper('functions');
            helper('holiday');
            $db = \Config\Database::connect();
            $output['error'] = ms_error(0);
        }
        else{exit('No direct script access allowed');}

        do{
            // Check Session
            if(!check_session()){
                $output['error'] = ms_error(403);
                break;
            }

            // Set Variables
            $session    = \Config\Services::session();
            $userID     = $session->get('userID');
            $used       = 0;

            // Get user's requested holidays
            $holidays = $db->table('holidayrequests');
            $holidays = $holidays->getWhere(['userID' => $userID]);
            if($holidays->connID->affected_rows == 0){$output['error'] = ms_error(1);break;}
            //$holidays = $holidays->getRow();

            // Loop through requests
            $dates = getDates();
            $data['past'] = array();
            $data['upcoming'] = array();

            foreach($holidays->getResult() as $row){
                if($row->holFromTime == "AD"){$row->holFromTime = "";}
                if($row->holToTime == "AD"){$row->holToTime = "";}


                if(strtotime($row->holTo) <= strtotime('now')){
                    $row->holFrom = "<strong>".$dates['weekdays'][date('N', strtotime($row->holFrom))]." ".$row->holFromTime."</strong><br>".date('d&\n\b\s\p;F&\n\b\s\p;Y', strtotime($row->holFrom));
                    $row->holTo = "<strong>".$dates['weekdays'][date('N', strtotime($row->holTo))]." ".$row->holToTime."</strong><br>".date('d&\n\b\s\p;F&\n\b\s\p;Y', strtotime($row->holTo));
                    $data['past'][] = $row;
                }
                else{
                    $row->holFrom = "<strong>".$dates['weekdays'][date('N', strtotime($row->holFrom))]." ".$row->holFromTime."</strong><br>".date('d&\n\b\s\p;F&\n\b\s\p;Y', strtotime($row->holFrom));
                    $row->holTo = "<strong>".$dates['weekdays'][date('N', strtotime($row->holTo))]." ".$row->holToTime."</strong><br>".date('d&\n\b\s\p;F&\n\b\s\p;Y', strtotime($row->holTo));
                    $data['upcoming'][] = $row;
                }
            }

            // Render Output
            $output['html'] = view('ajaxrenders/holiday/holiday-list', $data);





        }while(FALSE);

        // Return Output
        return $this->response->setJSON($output);
    }

// ----- SUBMIT REQUEST -------------------------------------------------
    public function submitHolRequest(){

        if($this->request->isAJAX()){
            helper('functions');
            helper('holiday');
            $db = \Config\Database::connect();
            $post = $this->request->getPost('data');
            $output['error'] = ms_error(0);
        }
        else{exit('No direct script access allowed');}

        // Count number of days
        $post['holTo'] = date("Y-m-d", strtotime($post['holTo']));
        $post['holFrom'] = date("Y-m-d", strtotime($post['holFrom']));
        $date1 = date_create($post['holTo']);
        $date2 = date_create($post['holFrom']);

        $datediff = date_diff($date1, $date2);
        $datediff = $datediff->days;

        if($datediff == 0){
            if($post['holFromTime'] == 'AM' && $post['holToTime'] == 'PM'){
                $post['holFromTime'] = 'AD';
                $post['holToTime'] = 'AD';
            }

            if($post['holFromTime'] == 'AD' && $post['holToTime'] == 'AD'){
                $datediff = $datediff + 1;
                $datediff = $datediff - 1;
            }
            elseif($post['holFromTime'] == $post['holToTime']){
                $datediff = $datediff + 0.5;
                $datediff = $datediff - 1;
            }
            
        }
        elseif($post['holFromTime'] != 'AD' && $post['holToTime'] != 'AD'){
            $datediff = $datediff - 1;
        }
        elseif($post['holFromTime'] != 'AD' || $post['holToTime'] != 'AD'){
            $datediff = $datediff - 0.5;
        }
        

        do{
            // Check Session
            if(!check_session()){$output['error'] = ms_error(403);break;}

            // Set Variables
            $post       = \Config\Services::request();
            $post       = $post->getPost('data');
            $session    = \Config\Services::session();
            $userID     = $session->get('userID');
            $used       = 0;
            $validation = array();

            // Form Validation
            if(empty($post['holFrom'])){$validation[] = "From date is required.";}
            if(empty($post['holFromTime'])){$validation[] = "From date time is required.";}
            if(empty($post['holFrom'])){$validation[] = "To date is required.";}
            if(empty($post['holFromTime'])){$validation[] = "To date time is required.";}

            // Form Validation Extended
            if(empty($validation)){
                // Times
                if($post['holFromTime'] == "AM" || $post['holFromTime'] == "PM" || $post['holFromTime'] == "AD"){}
                else{$validation[] = "Invalid From Time";}
                if($post['holToTime'] == "AM" || $post['holToTime'] == "PM" || $post['holToTime'] == "AD"){}
                else{$validation[] = "Invalid From Time";}

                // Logic
                if($post['holFrom'] > $post['holTo']){$validation[] = "The From Date needs to be before the To Date.";}
                if($post['holFrom'] == $post['holTo']){
                    if($post['holFrom'] == 'PM' && $post['holTo'] == 'AM'){$validation[] = "The From Time needs to be before the To Time on a single day.";}
                }
            }
            
            // Output Errors
            if(!empty($validation)){
                $output['error'] = ms_error(5);
                $output['message'] = "Errors found:<ul>";
                foreach($validation as $error){
                    $output['message'] = $output['message'] . "<li>".$error."</li>";
                }
                $output['message'] = $output['message'] ."</ul>";
                break;
            }

            // Skip clash check
            // ---

            // Request Holiday
            if(submitRequest($post, $userID, $db, $datediff)){
                // Email Notify
                $user_role = getUserHolidayRoleByID($userID);
                $user = getUserByID($userID);
                $line_manager = getUserByID($user_role->holLine);
                $message = $user->userForename." ".$user->userSurname." has submitted a holiday request and is awaiting your approval.";
                emailNotify($line_manager->userEmail, "Holiday Requested", $message);
            }

        }while(FALSE);

        // Return Output
        return $this->response->setJSON($output);
    }

// ----- PENDING HOLIDAYS -----------------------------------------------
    public function HolsPending(){

        if($this->request->isAJAX()){
            helper('functions');
            helper('holiday');
            $session = \Config\Services::session();
            $db = \Config\Database::connect();
            $post = $this->request->getPost('data');
            $output['error'] = ms_error(0);
        }
        else{exit('No direct script access allowed');}

        do{

            // Check Session & Role
            if(!check_session()){$output['error'] = ms_error(403);break;}
            if(!check_hol_role()){return redirect()->to('/holiday');}

            // Get user roles
            $userroles = $db->table('holidayuser');
            $userroles->where(['holLine' => $session->get('userID')]);
            $userroles->orWhere(['holDir' => $session->get('userID')]);
            $userroles = $userroles->get();

            // Check users are found
            if($userroles->connID->affected_rows == 0){$output['error'] = ms_error(5);break;}
            $userroles = $userroles->getResult();

            // Holday requests
            $requests = $db->table('holidayrequests');
            $usersDB = $db->table('users');
            $count = 0;
            
            foreach($userroles as $user){

                // Check if Line Manager and/or Director

                // Both
                if($user->holLine == $session->get('userID') && $user->holDir == $session->get('userID')){
                    $requests->where(['userID' => $user->userID, 'holStatus' => 'pending']);
                }

                // Line Manager
                elseif($user->holLine == $session->get('userID')){
                    $requests->where(['userID' => $user->userID, 'holLineStatus' => null, 'holStatus' => 'pending']);
                }

                // Director
                elseif($user->holDir == $session->get('userID')){
                    $requests->where(['userID' => $user->userID, 'holLineStatus' => 1 , 'holStatus' => 'pending']);
                }

                // Get results
                $userRequests = $requests->get();
                if($userRequests->connID->affected_rows != 0){
            
                    $rowUser = $usersDB->getWhere(['userID' => $user->userID]);

                    $data['rows'][$count]['requests'] = $userRequests->getResult();
                    $data['rows'][$count]['user'] = $rowUser->getRow();

                }
                $count++;
            }

            if(!empty($data)){
                $output['html'] = view('ajaxrenders/holiday/holiday-approval', $data);
            }
            else{
                $output['error'] = ms_error(5);break;
            }

        }while(FALSE);

        // Return Output
        return $this->response->setJSON($output);
    }

// ----- VIEW REQUEST ---------------------------------------------------
    public function holViewRequest(){

        if($this->request->isAJAX()){
            helper('functions');
            helper('holiday');
            $db = \Config\Database::connect();
            $post = $this->request->getPost('data');
            $session = \Config\Services::session();
            $output['error'] = ms_error(0);
        }
        else{exit('No direct script access allowed');}        

        do{
            // Check Session and Holiday Role
            if(!check_session()){$output['error'] = ms_error(403);break;}
            if(!check_hol_role()){return redirect()->to('/holiday');}

            // Get holiday request
            $request = $db->table('holidayrequests');
            $request = $request->getWhere(['holreqID' => $post['reqID']]);
            if($request->connID->affected_rows == 1){$request = $request->getRow();}
            else{$output['error'] = ms_error(5);break;}

            // Get User Information
            $users = $db->table('users');
            $user = $users->getWhere(['userID' => $request->userID]);
            if($user->connID->affected_rows == 1){$user = $user->getRow();}
            else{$output['error'] = ms_error(5);break;}

            // Get holiday requests from the same week
            $givenDate = strtotime($request->holFrom);
            $weekMon = date('Y-m-d', strtotime('previous monday', $givenDate));
            $weekFri = date('Y-m-d', strtotime('next friday', $givenDate));
            if(date('N', $givenDate) == 1){$weekMon = date('Y-m-d', $givenDate);}
            elseif(date('N', $givenDate) == 5){$weekFri = date('Y-m-d', $givenDate);}
            
            $requests = $db->table('holidayrequests');
            $requests->where('holFrom BETWEEN "'.$weekMon.'" AND "'.$weekFri.'"');
            $requests->where('holTo BETWEEN "'.$weekMon.'" AND "'.$weekFri.'"');
            $weekreq = $requests->get();
            $weekreq = $weekreq->getResult();

            // Add user info to each request
            foreach($weekreq as $req){
                if($req->holreqID == $post['reqID']){$req->holStatus = "Current (Pending)";}
                $req->holFrom = date('d/m', strtotime($req->holFrom));
                $req->holTo = date('d/m', strtotime($req->holTo));


                $requser = $users->getWhere(['userID' => $req->userID]);
                if($requser->connID->affected_rows == 1){$req->user = $requser->getRow();}
                else{$req->user = "Error";}
            }

            // Check user found
            // if($userroles->connID->affected_rows == 0){$output['error'] = ms_error(5);break;}

            // echo "Next Monday:". date('Y-m-d', strtotime('next monday', strtotime($givenDate)));
            // echo "Previous Monday:". date('Y-m-d', strtotime('previous monday', strtotime($givenDate)));

            //roffy_pre($request);

            $data['user'] = $user;
            $data['request'] = $request;
            $data['weekreq'] = $weekreq;
            $data['allowance'] = getAllowance($request->userID, date('Y', strtotime($request->holFrom)));
            $output['html'] = view('ajaxrenders/holiday/holiday-viewrequest', $data);

        }while(FALSE);

        // Return Output
        return $this->response->setJSON($output);
    }

// ----- ACTION REQUEST -------------------------------------------------
    public function actionRequest(){

        if($this->request->isAJAX()){
            helper('functions');
            helper('holiday');
            $db = \Config\Database::connect();
            $post = $this->request->getPost('data');
            $session = \Config\Services::session();
            $output['error'] = ms_error(0);
            $notify['send'] = FALSE;
        }
        else{exit('No direct script access allowed');}        

        do{
            // Check Session and Holiday Role
            if(!check_session()){$output['error'] = ms_error(403);break;}
            if(!check_hol_role()){return redirect()->to('/holiday');}

            // Get holiday request
            $requests = $db->table('holidayrequests');
            $request = $requests->getWhere(['holreqID' => $post['reqID']]);
            if($request->connID->affected_rows == 1){$request = $request->getRow();}
            else{$output['error'] = ms_error(5);break;}

            // Get User Information
            $users = $db->table('users');
            $user = $users->getWhere(['userID' => $request->userID]);
            if($user->connID->affected_rows == 1){$user = $user->getRow();}
            else{$output['error'] = ms_error(5);break;}

            // Get holidayuser
            $userrole = $db->table('holidayuser');
            $userrole->where(['userID' => $request->userID]);
            $userrole = $userrole->get();
            $userrole = $userrole->getrow();

            // Reviewed by Line Manager and Director
            if($userrole->holLine == $session->get('userID') && $userrole->holDir == $session->get('userID')){

                // Notify User of approved holiday
                if($post['action'] == 1){
                    $final_action = 'approved';
                    $notify['send'] = TRUE;
                    $notify['to'] = $user->userForename." ".$user->userSurname."<".$user->userEmail.">";
                    $notify['subject'] = "Holiday Request Approved";
                    $notify['message'] = "Your holiday request has been approved.";
                }
                // Notify User of rejected holiday
                elseif($post['action'] == 0){
                    $final_action = 'rejected';
                    $notify['send'] = TRUE;
                    $notify['to'] = $user->userForename." ".$user->userSurname."<".$user->userEmail.">";
                    $notify['subject'] = "Holiday Request Rejected";
                    $notify['message'] = "Your holiday request has been rejected.";

                }
                else{$output['error'] = ms_error(1);break;}

                // Database Data
                $db_data = array(
                    'holLineStatus' => $post['action'],
                    'holDirStatus' => $post['action'],
                    'holStatus' => $final_action
                );
                
            }

            // Reviewed by Line Manager
            elseif($userrole->holLine == $session->get('userID')){

                // Database Data
                $db_data = array('holLineStatus' => $post['action']);

                // Notify Director of pending holiday request
                if($post['action'] == 1){
                    $user_role = getUserHolidayRoleByID($userrole->holLine);
                    $director = getUserByID($user_role->holDir);
                    $notify['send'] = TRUE;
                    $notify['to'] = $director->userForename." ".$director->userSurname."<".$director->userEmail.">";
                    $notify['subject'] = "Holiday Request Pending";
                    $notify['message'] = "You have a holiday request pending from ".$user->userForename." ".$user->userSurname.".";
                }

                // Notify User of rejected holiday request
                elseif($post['action'] == 0){
                    $requests->set('holStatus', 'rejected');
                    $notify['send'] = TRUE;
                    $notify['to'] = $user->userForename." ".$user->userSurname."<".$user->userEmail.">";
                    $notify['subject'] = "Holiday Request Rejected";
                    $notify['message'] = "Your holiday request has been rejected.";
                }
            }

            // Reviewed by Directer
            elseif($userrole->holDir == $session->get('userID')){

                // Notify User of approved holiday
                if($post['action'] == 1){
                    $final_action = 'approved';
                    $notify['send'] = TRUE;
                    $notify['to'] = $user->userForename." ".$user->userSurname."<".$user->userEmail.">";
                    $notify['subject'] = "Holiday Request Approved";
                    $notify['message'] = "Your holiday request has been approved.";
                }
                // Notify User of rejected holiday
                elseif($post['action'] == 0){
                    $final_action = 'rejected';
                    $notify['send'] = TRUE;
                    $notify['to'] = $user->userForename." ".$user->userSurname."<".$user->userEmail.">";
                    $notify['subject'] = "Holiday Request Rejected";
                    $notify['message'] = "Your holiday request has been rejected.";
                }
                else{$output['error'] = ms_error(1);break;}

                // Database Data
                $db_data = array(
                    'holDirStatus' => $post['action'],
                    'holStatus' => $final_action
                );
            }

            else{$output['error'] = ms_error(1);break;}

            // Update Database
            $requests->where('holreqID', $post['reqID']);
            $requests->update($db_data);

            // Notify if set
            if($notify['send']){
                emailNotify($notify['to'], $notify['subject'], $notify['message']);
            }


        }while(FALSE);

        // Return Output
        return $this->response->setJSON($output);
    }

// ----- CANCEL REQUEST -------------------------------------------------
    public function cancelRequest(){

        if($this->request->isAJAX()){
            helper('functions');
            helper('holiday');
            $db = \Config\Database::connect();
            $post = $this->request->getPost('data');
            $session = \Config\Services::session();
            $output['error'] = ms_error(0);
            $notify['send'] = FALSE;
        }
        else{exit('No direct script access allowed');}        

        do{
            // Check Session and Holiday Role
            if(!check_session()){$output['error'] = ms_error(403);break;}
            
            // Get selected holiday request
            $requests = $db->table('holidayrequests');
            $request = $requests->getWhere(['holreqID' => $post['holreqID']]);
            if($request->connID->affected_rows == 1){$request = $request->getRow();}
            else{$output['error'] = ms_error(5);break;}

            // Get database details
            $user_role = getUserHolidayRoleByID($request->userID);
            $user = getUserByID($request->userID);
            $line_manager = getUserByID($user_role->holLine);
            $director = getUserByID($user_role->holDir);


            // Set up Email notification
            if($request->holStatus == 'pending'){
                $notify['to'] = $line_manager->userForename." ".$line_manager->userSurname."<".$line_manager->userEmail.">";
                if($request->holLineStatus == 1){
                    $notify['to'].= ", ".$director->userForename." ".$director->userSurname."<".$director->userEmail.">";
                }
            }
            elseif($request->holStatus == 'approved'){
                $notify['to'] = $line_manager->userForename." ".$line_manager->userSurname."<".$line_manager->userEmail.">";
                $notify['to'].= ", ".$director->userForename." ".$director->userSurname."<".$director->userEmail.">";
            }

            $notify['subject'] = "Cancelled Holiday Request";
            $notify['message'] = "
                <p>".$user->userForename." ".$user->userSurname." has cancelled their holiday request:</p>
                <p>Requested day(s) were from ".$request->holFrom." to ".$request->holTo."</p>
            ";
            $notify['send'] = TRUE;

            // Update Database
            $requests->set('holStatus', 'cancelled');
            $requests->where('holreqID', $post['holreqID']);
            if($requests->update()){
                // Notify if set
                if($notify['send']){
                    emailNotify($notify['to'], $notify['subject'], $notify['message']);
                }
            }
            else{$output['error'] = ms_error(1);break;}
            
        }while(FALSE);

        // Return Output
        return $this->response->setJSON($output);
    }

// ----- MANAGER * GET REQUESTS -------------------------------------------------
    public function getAllRequests(){

        if($this->request->isAJAX()){
            helper('functions');
            helper('holiday');
            $db = \Config\Database::connect();
            $post = $this->request->getPost('data');
            $session = \Config\Services::session();
            $output['error'] = ms_error(0);
        }
        else{exit('No direct script access allowed');}        

        do{

            // Check Session and Holiday Role
            if(!check_session()){$output['error'] = ms_error(403);break;}
            if(!check_hol_role()){$output['error'] = ms_error(403);break;}

            // Get Dates
            $dates = getDates();

            // Get Users or Selected User
            if($post['user'] == 'all'){
                $allusers = $db->table('users');
                $allusers = $allusers->getWhere(['userActive' => 1]);
                $allusers = $allusers->getResult();
            }else{
                $allusers = $db->table('users');
                $allusers = $allusers->getWhere(['userID' => $post['user']]);
                $allusers = $allusers->getResult();
            }
            
            // Get requests
            $requests = $db->table('holidayrequests');
            $requests->select('*');
            $requests->where('holFrom >', $post['year'].'-01-01');
            $requests->where('holTo <', $post['year'].'-12-31');
            $requests->orderBy('holFrom', 'ASC');
            $requests = $requests->get();
            $requests = $requests->getResult();

            // Sort Users
            foreach($allusers as $user){
                $users[$user->userID] = $user;
            }

            // Sort Requests
            $i = 0;
            $data = array();
            foreach($requests as $request){
                
                // Check user still exists
                if(!isset($users[$request->userID])){continue;}

                // Prep data for Ajax Render
                $data['requests'][$i]['employee'] = $users[$request->userID]->userForename ." ".$users[$request->userID]->userSurname;
                $data['requests'][$i]['holFrom'] = date("d F, Y", strtotime($request->holFrom));
                $data['requests'][$i]['holTo'] = date("d F, Y", strtotime($request->holTo));
                $data['requests'][$i]['holDays'] = $request->holDays;

                    // From and To Time - hide 'AD'
                    if($request->holFromTime == 'AD'){
                        $data['requests'][$i]['holFromTime'] = NULL;
                    }else{
                        $data['requests'][$i]['holFromTime'] = "<strong>".$request->holFromTime."</strong>";
                    }
                    if($request->holToTime == 'AD'){
                        $data['requests'][$i]['holToTime'] = NULL;
                    }else{
                        $data['requests'][$i]['holToTime'] = "<strong>".$request->holToTime."</strong>";
                    }

                    // Status - change colours
                    if($request->holStatus == 'approved'){
                        $data['requests'][$i]['holStatus'] = "<span style='color:green'>".ucfirst($request->holStatus)."</span>";
                    }else{
                        $data['requests'][$i]['holStatus'] = "<span style='color:red'>".ucfirst($request->holStatus)."</span>";
                    }

                $i++;
            }

            // Render Holiday List
            if(!empty($data)){
                $output['html'] = view('ajaxrenders/holiday/holiday-all-requests', $data);
            }
            else{$output['error'] = ms_error(5);break;}
            
        }while(FALSE);

        // Return Output
        return $this->response->setJSON($output);
    }

// ----- MANAGER * GET ALLOWANCES -------------------------------------------------
public function getAllowances(){

    if($this->request->isAJAX()){
        helper('functions');
        helper('holiday');
        $db = \Config\Database::connect();
        $post = $this->request->getPost('data');
        $session = \Config\Services::session();
        $output['error'] = ms_error(0);
    }
    else{exit('No direct script access allowed');}        

    do{

        // Check Session and Holiday Role
        if(!check_session()){$output['error'] = ms_error(403);break;}
        if(!check_hol_role()){$output['error'] = ms_error(403);break;}

        // Get Users or Selected User
        $allusers = $db->table('users');
        $allusers->select('userID, userForename, userSurname');
        $allusers->where('userActive', 1);
        $allusers->orderBy('userForename', 'ASC');
        $allusers = $allusers->get();
        $allusers = $allusers->getResult();



        // $allusers = $db->table('users');
        // $allusers = $allusers->getWhere(['userActive' => 1]);
        // $allusers = $allusers->getResult();

        // Get allowances for each user
        $i = 0;
        foreach($allusers as $user){

            $allowance = getAllowance($user->userID, $post['year']);
            //roffy_pre($allowance);
            if($allowance['success']){
                $data['allowances'][$i]['employee'] = $user->userForename ." ". $user->userSurname;
                $data['allowances'][$i]['allowance'] = $allowance['allowance'];
                $data['allowances'][$i]['used'] = $allowance['used'];
                $data['allowances'][$i]['remaining'] = $allowance['remaining'];
                $i++;
            }
        }

        // Render Allowance List
        if(!empty($data)){
            $output['html'] = view('ajaxrenders/holiday/holiday-allowances', $data);
        }
        else{$output['error'] = ms_error(5);break;}
        
    }while(FALSE);

    // Return Output
    return $this->response->setJSON($output);
}
}