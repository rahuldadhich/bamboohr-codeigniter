<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bamboohr extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('bamboohr_model');
	}
	
	// get all locations/site
	public function downloadBambooLocation() {
		$bhr = $this->bamboohr_model->login();
		if($bhr == false) {
			echo $message = "BambooHR auth error. Please authorize again.";
		} else {
			$employees = $bhr->sync_location();
		}
	}
	
	// get all departments
	public function downloadBambooDepartment() {
		$bhr = $this->bamboohr_model->login();
		if($bhr == false) {
			echo $message = "BambooHR auth error. Please authorize again.";
		} else {
			$employees = $bhr->sync_department();
		}
	}
		
	// get all employees
	public function downloadBambooEmployees() {
		$bhr = $this->bamboohr_model->login();
		if($bhr == false) {
			echo $message = "BambooHR auth error. Please authorize again.";
		} else {
			$employees = $bhr->downloadEmployees();
		}
	}
}