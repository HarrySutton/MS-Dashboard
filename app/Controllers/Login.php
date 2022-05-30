<?php namespace App\Controllers;

class Login extends BaseController{

    public function index(){
        helper('functions');

        if(check_session()){
            return redirect()->to('/');
        }

        echo view('appends/header');
        echo view('pages/login');
        echo view('appends/footer');
    }


    public function ajaxLogin(){
        // Kill if not an ajax request
        if($this->request->isAJAX()){
            helper('functions');
            $db = \Config\Database::connect();
            $post = $this->request->getPost('data');
            $output['error'] = ms_error(3);
        }
        else{exit('No direct script access allowed');}

        do{
            // Assign variables
            $username = strtolower($post['username']);
            $password = $post['password'];

            // Check details
            list($check, $data) = validate($username, $password);
            if($check){
                // Assign details to session
                $users = $db->table('users');
                $query = $users->getWhere(['userName' => $username]);

                if($query->connID->affected_rows == 1){
                    $session = \Config\Services::session();
                    $row = $query->getRow();

                    if($row->userActive){
                        $userdata = array(
                            'userID'  => $row->userID,
                            'userName' => $row->userName,
                            'userForename' => $row->userForename,
                            'userPermiss' => $row->userPermiss,
                            'logged_in' => TRUE
                        );
                        $session->set($userdata);
                        $output['error'] = ms_error(0);
                    }                
                }else{break;}     
            }else{break;}
        }while(FALSE);

        // Output in JSON
        $db->close();
        sleep(2);
        return $this->response->setJSON($output);
		
    }
    
    public function logout(){
        // End session and redirect to login
        $session = \Config\Services::session();
        $session->destroy();
        return redirect()->to('/login');
	}
}
