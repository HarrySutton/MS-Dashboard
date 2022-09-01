<?php namespace App\Controllers;

class Admin extends BaseController{

// INDEX
	public function index(){

		helper('functions');

		// Check session & permission
		if(!check_session(2)){$output['error'] = ms_error(403);}

		// Get users
		$db = \Config\Database::connect();
		$users = $db->table('users');
		$users = $users->get();
		$data['users'] = $users->getResult();

		// Output views
        echo view('appends/header');
		echo view('appends/navbar');
		echo view('pages/admin', $data);
        echo view('appends/footer');
	}
	
// GET USERS
	public function ajaxGetUsers(){

		if($this->request->isAJAX()){
			helper('functions');
			helper('holiday');
			$db = \Config\Database::connect();
			$post = $this->request->getPost('data');
			$output['error'] = ms_error();
        }
		else{exit('No direct script access allowed');}

		do{
			// Check session and permissions
			if(!check_session(2)){$output['error'] = ms_error(403);break;}

			// Active Tab
			$data['userbtn'] = '';
			$data['usertab'] = '';
			$data['holbtn'] = '';
			$data['holtab'] = '';

			if($post['tab'] == "user-tab"){
				$data['userbtn'] = ' active';
				$data['usertab'] = ' show active';
			}
			elseif($post['tab'] == "holiday-tab"){
				$data['holbtn'] = ' active';
				$data['holtab'] = ' show active';
			}

			// Get users and holiday info
			$data['users'] = getUserHoliday();
			$data['holmanagers'] = getHolidayManagers();
			

			// Output
			$output['html'] = view('ajaxrenders/admin/users', $data);

		}while(false);

		$db->close();
		return $this->response->setJSON($output);
	}

// ADD USER
    public function ajaxAddUser(){

        if($this->request->isAJAX()){
			helper('functions');
            $db = \Config\Database::connect();
			$post = $this->request->getPost('data');
			$output['error'] = ms_error();
        }
        else{exit('No direct script access allowed');}

		do{
			// Check user session
			if(!check_session(2)){$output['error'] = ms_error(403);break;}

			// Check if post items are empty
			$errors = array();
			if(empty($post['userEmail'])){$errors[] = 'Please enter an email.';}
			if(empty($post['userName'])){$errors[] = 'Please enter a username.';}
			if(empty($post['userForename'])){$errors[] = 'Please enter a forename.';}
			if(empty($post['userSurname'])){$errors[] = 'Please enter a surname.';}
			if(empty($post['userPassword'])){$errors[] = 'Please enter a password.';}

			// Check for existing Username
			$users = $db->table('users');
			$users = $users->getWhere(['userName' => $post['userName']]);
			$users = $users->getResult();
			if(!empty($users)){$errors[] = 'Username is already in use.';}

			// Check for existing Email
			$users = $db->table('users');
			$users = $users->getWhere(['userEmail' => $post['userEmail']]);
			$users = $users->getResult();
			if(!empty($users)){$errors[] = 'Email is already in use.';}	
			
			// Output errors
			if(!empty($errors)){
				$errorlist = "<ul>";
				foreach($errors as $error){
					$errorlist = $errorlist."<li>".$error."</li>";
				}
				$errorlist = $errorlist."</ul>";

				$output['error'] = ms_error(4);
				$output['error']['formerror'] = $errorlist;
				break;
			}

			// Store data
			else{
				if($post['userAdmin']){$permiss = 2;}
				else{$permiss = 1;}
				$password = password_hash($post['userPassword'], PASSWORD_DEFAULT);
				$db_data = array(
					'userName' => $post['userName'],
					'userForename' => $post['userForename'],
					'userSurname' => $post['userSurname'],
					'userEmail' => $post['userEmail'],
					'userPassword' => $password,
					'userPermiss' => $permiss
				);
				$db->table('users')->insert($db_data); 
				if($db->affectedRows() == 1){
					$output['error'] = ms_error(0);
				}
				else{
					$errors[]='A problem occured when adding data to database.';
				}
			}
			

		}while(FALSE);
		
		$db->close();
		return $this->response->setJSON($output);
    }


// GET USER
	public function ajaxGetUser(){

		if($this->request->isAJAX()){
			helper('functions');
            $db = \Config\Database::connect();
			$post = $this->request->getPost('data');
			$output['error'] = ms_error();
		}
		else{exit('No direct script access allowed');}

		do{
			// Check user session
			if(!check_session(2)){$output['error'] = ms_error(403);break;}

			// Get user from database
			$user = $db->table('users');
			$user = $user->getWhere(['userID' => $post['userID']]);
			$data['user'] = $user->getRow();

			$output['html'] = view('ajaxrenders/admin/edituser', $data);

		}while(FALSE);

		$db->close();
		return $this->response->setJSON($output);
	}


// EDIT USER
	public function ajaxEdituser(){

		if($this->request->isAJAX()){
			helper('functions');
			$db = \Config\Database::connect();
			$post = $this->request->getPost('data');
			$output['error'] = ms_error();
		}
		else{exit('No direct script access allowed');}

		do{

			// Check user session
			if(!check_session(2)){$output['error'] = ms_error(403);break;}
			$users = $db->table('users');

			// Password change
			if(isset($post['password']) && $post['password'] != ""){
				$password = password_hash($post['password'], PASSWORD_DEFAULT);
				$db_data = array(
					'userPassword' => $password
				);
				$users->where('userID', $post['userID']);
				$users->update($db_data);
			}

			// Update in Database
			$db_data = array(
				'userName' 		=> $post['username'],
				'userForename' 	=> $post['forename'],
				'userSurname' 	=> $post['surname'],
				'userEmail' 	=> $post['email'],
				'userActive' 	=> $post['enabled'],
				'userPermiss' 	=> $post['admin'],
			);
			$users->where('userID', $post['userID']);
			$users->update($db_data);

		}while(FALSE);

		$db->close();
		return $this->response->setJSON($output);
	}


// DELETE USER
    public function ajaxDeleteuser(){

        if($this->request->isAJAX()){

            // ...
        }
        else{exit('No direct script access allowed');}



	}
	

// SAVE HOLIDAY SETTINGS
    public function ajaxSaveHoliday(){

        if($this->request->isAJAX()){
            helper('functions');
            $db = \Config\Database::connect();
			$post = $this->request->getPost('data');
			$output['error'] = ms_error();
        }
		else{exit('No direct script access allowed');}

		do{

			// Database
			$holidayallowance = $db->table('holidayallowance');

			foreach($post as $user){

				// Change blanks to null
				foreach($user as $key => $value){
					if($key == 'linemanager' || $key == 'director'){
						if($value == 0){$user[$key] = null;}
					}
					else{
						if(!is_numeric($value) || $value == ""){$user[$key] = null;}
					}
				}

				// Holiday Allowances

				// (Current) Check for allowance
				$thisyear = null;
				$thisyear = $holidayallowance->getWhere(['userID' => $user['userID'], 'holallowYear' => date('Y')]);
				$thisyear = $thisyear->getResult();

					// (Current) Insert new row if none exist
					if(empty($thisyear)){
						$db_data = array(
							'userID' => $user['userID'],
							'holallowYear' => date('Y'),
							'holallowQuantity' => $user['holallowthis']
						);
						$db->table('holidayallowance')->insert($db_data); 
					}

					// (Current) Update existing row
					else{
						$holidayallowance->set('holallowQuantity', $user['holallowthis']);
						$holidayallowance->where(['userID' => $user['userID'], 'holallowYear' => date('Y')]);
            			$holidayallowance->update();
					}

				// (Next) Check for next year allowance
				$nextyear = null;
				$nextyear = $holidayallowance->getWhere(['userID' => $user['userID'], 'holallowYear' => date('Y', strtotime('+1 years'))]);
				$nextyear = $nextyear->getResult();

					// (Next) Insert new row if none exist
					if(empty($nextyear)){
						$db_data = array(
							'userID' => $user['userID'],
							'holallowYear' => date('Y', strtotime('+1 years')),
							'holallowQuantity' => $user['holallownext']
						);
						$db->table('holidayallowance')->insert($db_data); 
					}

					// (Next) Update existing row
					else{
						$holidayallowance->set('holallowQuantity', $user['holallownext']);
						$holidayallowance->where(['userID' => $user['userID'], 'holallowYear' => date('Y', strtotime('+1 years'))]);
						$holidayallowance->update();
					}

				// Update User role
				$holidayuser = $db->table('holidayuser');
				$role = $holidayuser->getWhere(['userID' => $user['userID']]);
				$role = $role->getResult();

					// Data
					$db_data = array(
						'userID' => $user['userID'],
						'holRole' => $user['role'],
						'holLine' => $user['linemanager'],
						'holDir' => $user['director']
					);

					// Insert new row if none exist
					if(empty($role)){

						$db->table('holidayuser')->insert($db_data); 
					}

					// Update existing row
					else{
						$holidayuser->where('userID', $user['userID']);
						$holidayuser->update($db_data);
					}

			}

		}while(FALSE);

		//sleep(2);
		return $this->response->setJSON($output);

    }
}
