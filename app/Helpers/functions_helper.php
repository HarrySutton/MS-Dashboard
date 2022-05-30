<?php

// Error
    if(!function_exists('ms_error')){
        function ms_error($code = 0, $description = FALSE, $codeName=''){
            if(!$description){
                $description = 'There has been an error. Please try again later';    
                $codeName = 'Unknown';
                $db = \Config\Database::connect();
                $errors = $db->table('errors');
                $query = $errors->getWhere(['errorID' => $code]);
                $return = $query->getRow();
                if($return){
                    $description = $return->errorDesc;
                    $codeName = $return->errorName;
                }
            }           
            return array('ErrorStatus' => $code, 'ErrorName' => $codeName, 'ErrorMessage' => $description);
        }
    }

// Check Session
    if(!function_exists('check_session')){
        function check_session($permiss = null){
            $session = \Config\Services::session();
            if($session->get('logged_in')){
                if(isset($permiss)){
                    if($permiss <= $session->get('userPermiss')){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    return true;
                }
            }
            else{
                return FALSE;
            }
        }
    }

// Validation for login
    if(!function_exists('validate')){
        function validate($username, $password){
            // Check 
            $errors = array();
            if(empty($username)){$errors[] = 'Please enter your username.';}
            if(empty($password)){$errors[] = 'Please enter your password.';}

            if(empty($errors)){
                $db = \Config\Database::connect();
                $users = $db->table('users');
                $query = $users->getWhere(['userName' => $username]);

                if($query->connID->affected_rows == 1){
                    $row = $query->getRow();
                    if(password_verify($password, $row->userPassword)){return array(TRUE, $errors);}
                    else{$errors[] = 'Username and/or password not found.';}
                }
                elseif($users->countAllResults() == 0){$errors[] = 'Username and/or password not found.';}
                else{$errors[] = 'Unknown error has occured, please contact the site admin.';}                
            }
            return array(FALSE, $errors);
        }
    }

// Get Dates
    if(!function_exists('getDates')){
        function getDates(){

            $db = \Config\Database::connect();
            $allowanceYears = $db->query("SELECT DISTINCT holallowYear FROM holidayallowance");

            foreach($allowanceYears->getResult() as $row){
                $data['allowanceYears'][] = $row->holallowYear;
            }

            $data['weekdays'][1] = "Monday";
            $data['weekdays'][2] = "Tuesday";
            $data['weekdays'][3] = "Wednesday";
            $data['weekdays'][4] = "Thursday";
            $data['weekdays'][5] = "Friday";
            $data['weekdays'][6] = "Saturday";
            $data['weekdays'][7] = "Sunday";
        
            $data['months'][1] = "January";
            $data['months'][2] = "February";
            $data['months'][3] = "March";
            $data['months'][4] = "April";
            $data['months'][5] = "May";
            $data['months'][6] = "June";
            $data['months'][7] = "July";
            $data['months'][8] = "August";
            $data['months'][9] = "September";
            $data['months'][10] = "October";
            $data['months'][11] = "November";
            $data['months'][12] = "December";

            $data['today']['date'] = date("d");
            $data['today']['month'] = date("j");
            $data['today']['year'] = date("Y");
            
            return $data;
        }
    }

// Get User by ID
    if(!function_exists('getUserByID')){
        function getUserByID($userID){

            // Get User Information
            $db = \Config\Database::connect();
            $users = $db->table('users');
            $user = $users->getWhere(['userID' => $userID]);
            if($user->connID->affected_rows == 1){return $user->getRow();}
            else{return FALSE;}
        }
    }

// Email Notify
    if(!function_exists('emailNotify')){
        function emailNotify($to, $subject, $message){

            $emailmessage = "
            <html>
                <head>
                    <title>Milestone Dashboard Notification</title>
                </head>
                <body>
                    <span class='body'>
                        <h3>Milestone Dashboard Notification</h3>
                        <p>Message:</p>
                        ".$message."
                        <b>Note:</b> This is an automated email sent by the <a href='". base_url() ."'>Milestone Dashboard</a>, please do not reply.<br/><br/>
                    </span>
                    <p>
                        <span class='signature'>
                            <span class='orange'>Milestone Dashboard</span><br/>
                            Milestone Creative Ltd<br/>
                            2 Abbey Barn Business Centre<br/>
                            Abbey Barn Lane<br/>
                            High Wycombe<br/>
                            HP10 9QQ<br/>
                            _____________________________________<br/><br/>
                            <span class='orange'>T</span>&nbsp;&nbsp;&nbsp;01494 676436<br/>
                            <span class='orange'>W</span>&nbsp;&nbsp;milestonecreative.co.uk<br/>
                            _____________________________________<br/><br/>
                            <span class='registered'>
                                Registered in England No. 3110451.<br/>
                                Registered Office: 2 Abbey Barn Business Centre<br/>
                                Abbey Barn Lane, High Wycombe HP10 9QQ
                            </span>
                        </span>
                    </p>
                </body>
            </html>
            ";

            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Milestone Dashboard <noreply@milestonedigital.co.uk>' . "\r\n";

            // Send
            mail($to,$subject,$emailmessage,$headers);
        }
    }

// Roffy Pre
    if(!function_exists('roffy_pre')){
        function roffy_pre($printMe, $title = false){
            if($title){
                echo '<h1>'.$title.'</h1>';
            }
            echo '<hr><pre style="text-align:left;font-size:0.7em;line-height:1.1em;z-index:9999;">';
            print_r($printMe);
            echo '</pre><hr>';
        
            return true;
        }
    }