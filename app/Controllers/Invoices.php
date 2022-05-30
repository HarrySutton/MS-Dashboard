<?php namespace App\Controllers;

class Invoices extends BaseController{
	
	public function index(){

		helper('functions');

        if(!check_session()){return redirect()->to('/login');}
        
        $session = \Config\Services::session();
        $data['permiss'] = $session->get('userPermiss');
        
		echo view('appends/header');
        echo view('appends/navbar');
        echo view('pages/invoices', $data);
        echo view('appends/footer');
	}
	
}