<?php

// Check user holiday role
    if(!function_exists('check_hol_role')){
        function check_hol_role(){
            $session = \Config\Services::session();
            $db = \Config\Database::connect();

            if($session->get('logged_in')){

                // Get user's role
                $holidayuser = $db->table('holidayuser');
                $userrole = $holidayuser->getWhere(['userID' => $session->get('userID')]);
                if($userrole->connID->affected_rows == 0){return FALSE;}
                else{$return = $userrole->getRow();}
                
            }
            else{return FALSE;}

            return $return;
        }
    }

// Get Dates
    // if(!function_exists('getDates')){
    //     function getDates(){

    //         $db = \Config\Database::connect();
    //         $allowanceYears = $db->query("SELECT DISTINCT holallowYear FROM holidayallowance");

    //         foreach($allowanceYears->getResult() as $row){
    //             $data['allowanceYears'][] = $row->holallowYear;
    //         }
        
    //         $data['months'][1] = "January";
    //         $data['months'][2] = "February";
    //         $data['months'][3] = "March";
    //         $data['months'][4] = "April";
    //         $data['months'][5] = "May";
    //         $data['months'][6] = "June";
    //         $data['months'][7] = "July";
    //         $data['months'][8] = "August";
    //         $data['months'][9] = "September";
    //         $data['months'][10] = "October";
    //         $data['months'][11] = "November";
    //         $data['months'][12] = "December";

    //         $data['today']['date'] = date("d");
    //         $data['today']['month'] = date("m");
    //         $data['today']['year'] = date("Y");
            
    //         return $data;
    //     }
    // }


// Get Users and holiday info
    if(!function_exists('getUserHoliday')){
        function getUserHoliday($userid = null){

            $db = \Config\Database::connect();

            $users = $db->table('users');
            $users = $users->get();
            $users = $users->getResult('array');

            $holalw = $db->table('holidayallowance');
            $holalw = $holalw->get();
            $holalw = $holalw->getResult('array');

            $holuser = $db->table('holidayuser');

            $count = 0;
            foreach($users as $user){

                $data[$count] = $user;

                foreach($holalw as $hol){

                    if($hol['userID'] == $user['userID']){
                        $data[$count]['holallow'][$hol['holallowYear']] = $hol['holallowQuantity'];
                    }
                }

                // Get user holiday role
                $holrole = $holuser->getWhere(['userID' => $user['userID']]);
                $holrole = $holrole->getRowArray();

                if(empty($holrole)){
                    $db_data = array(
                        'userID' => $user['userID'],
                    );
                    $db->table('holidayuser')->insert($db_data); 

                    $holrole = $holuser->getWhere(['userID' => $user['userID']]);
                    $holrole = $holrole->getRowArray();
                }

                $data[$count]['holrole'] = $holrole;

                $count++;

            }

            return $data;
        }
    }



// Get holiday managers
    if(!function_exists('getHolidayManagers')){
        function getHolidayManagers(){

            $db = \Config\Database::connect();
            $data = array();

            $users = $db->table('users');

            $holusers = $db->table('holidayuser');
            $holusers = $holusers->getWhere(['holRole' => 1]);
            $holusers = $holusers->getResult('array');


            foreach($holusers as $holuser){

                // Get user
                $user = $users->getWhere(['userID' => $holuser['userID']]);
                $data[] = $user->getRowArray();

            }

            return $data;
        }
    }

// Get User Holiday Role by User ID
    if(!function_exists('getUserHolidayRoleByID')){
        function getUserHolidayRoleByID($userID){

            // Get User Information
            $db = \Config\Database::connect();
            $users = $db->table('holidayuser');
            $user = $users->getWhere(['userID' => $userID]);
            if($user->connID->affected_rows == 1){return $user->getRow();}
            else{return FALSE;}
        }
    }

// Submit to Database function
    function submitRequest($post, $userID, $db, $datediff){

        $data = [
            'userID'        => $userID,
            'holFrom'       => $post['holFrom'],
            'holFromTime'   => $post['holFromTime'],
            'holTo'         => $post['holTo'],
            'holToTime'     => $post['holToTime'],
            'holRef'        => $post['holRef'],
            'holStatus'     => 'pending',
            'holDays'       => $datediff+1
        ];
        
        $db->table('holidayrequests')->insert($data);
        if($db->connID->affected_rows == 1){return true;}
        else{return false;}
    }


// Get user's holiday allowance
    function getAllowance($userID, $year){

        do{
            $db = \Config\Database::connect();

            // Holiday allowance table
            $allowance = $db->table('holidayallowance');
            $allowance = $allowance->getWhere(['userID' => $userID, 'holallowYear' => $year]);
            if($allowance->connID->affected_rows == 0){$return['success'] = false;break;}
            $allowance = $allowance->getRow();

            // Get users requested holidays
            $requests = $db->table('holidayrequests');
            $requests->where('userID', $userID);
            $requests->like('holFrom', $year);
            $requests = $requests->get();

            // Loop through requests
            $used = 0;
            foreach($requests->getResult() as $row){
                
                // check selected year
                if($row->holStatus != 'rejected' && $row->holStatus != 'cancelled'){
                    $used = $used + $row->holDays;
                }
            }

            // Return values
            $return['success'] = true;
            $return['allowance'] = $allowance->holallowQuantity;
            $return['used'] = $used;
            $return['remaining'] = $allowance->holallowQuantity - $used;

        }while(FALSE);

        return $return;
        
    }



    function checkHolClash($post){
        // Variables
        $db = \Config\Database::connect();
        $return['success'] = true;
        $return['message'] = null;

        // Database Check
        $sql = "SELECT * FROM holidayrequests WHERE 
            holFrom >= '" .$post['holFrom']. "' AND holFrom >= '" .$post['holTo']. "'";
        $query = $db->query($sql);

        // If rows have been found
        if($query->connID->affected_rows != 0){
            if(!$post['forceSubmit']){
                
                $results = $query->getResult();
                $return['message'] = "Clashing holidays:<ul>";
                foreach($results as $holiday){

                    $users = $db->table('users');
                    $query = $users->getWhere(['userID' => $holiday->userID]);
                    $user = $query->getRow();

                    $return['message'] = $return['message'] . "<li>".$user->userForename." ".$user->userSurname.": ".$holiday->holFrom." - ".$holiday->holTo."</li>";
                }
                $return['message'] = $return['message'] ."</ul>Press submit again if you'd like to continue.";
                $return['success'] = false;
            }
        }
        return $return;
    }
