<?php namespace App\Controllers;

class Booking extends BaseController{
    public function index(){

		helper('functions');
		if(!check_session()){return redirect()->to('/login');}

		echo view('appends/header');
        echo view('appends/navbar');
        echo view('pages/booking');
        echo view('appends/footer');
    }
}