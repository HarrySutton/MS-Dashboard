<?php namespace App\Controllers;

class Session extends BaseController{

	public function index(){
		return view('welcome_message');
    }
    
    public function logout(){
		return view('welcome_message');
	}

}
