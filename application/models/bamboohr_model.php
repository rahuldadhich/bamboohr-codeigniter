<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bamboohr_model extends CI_Model {
	private $BAMBOOHR_APPKEY;	// bamboohr app key
	private $username;	// bamboohr username
	private $password;	// bamboohr password
	private $companyID;	// bamboohr companyID
	private $clientSecret;	// client secret key, get from login()
	private $clientPassword;
	public $baseUrl = "https://api.bamboohr.com/api/gateway.php/";
	
	public function __construct() {
        parent::__construct();
		$this->BAMBOOHR_APPKEY = "xxx";
		$this->username = "xxx";
		$this->password = "xxx";
		$this->companyID = "xxx";
		
		$this->baseUrl .= $this->companyID."/";
    }
	
	/**
     * Login and get keys from BambooHR for future request
     */
    public function login() {
        $response = $this->curlRequest("POST", "v1/login", "applicationKey=" . urlencode($this->BAMBOOHR_APPKEY) . "&user=" . urlencode($this->username) . "&password=" . urlencode($this->password));

        $data = json_decode($response['content']);
        if ($data->success) {
            $this->clientSecret = $data->key;
            $this->clientPassword = "x";

            // can be save in DB for further processing

            return true;
        } else {
            return false;
        }
    }
	
	public function curlRequest($method = "GET", $url = "", $param = "", $include_header = 1) {
        if ($include_header) {
            $this->headers = [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded'
            ];
        }
        $http = curl_init();
        curl_setopt($http, CURLOPT_URL, $this->baseUrl . $url);
        curl_setopt($http, CURLOPT_CUSTOMREQUEST, $method);
        if ($include_header == 1) {
            curl_setopt($http, CURLOPT_HTTPHEADER, $this->headers);
        }
        curl_setopt($http, CURLOPT_POSTFIELDS, $param);

        curl_setopt($http, CURLOPT_HEADER, true);
        curl_setopt($http, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($http, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($http, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($http, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($http, CURLOPT_USERPWD, $this->clientSecret . ':' . $this->clientPassword);
        $response = curl_exec($http);

        $result = array();

        if ($response !== false) {
            $result['status'] = curl_getinfo($http, CURLINFO_HTTP_CODE);
            $headerSize = curl_getinfo($http, CURLINFO_HEADER_SIZE);
            $result['header'] = $this->parseHeaders(substr($response, 0, $headerSize));
            $result['content'] = substr($response, $headerSize);
        } else {
            $result['status'] = 0;
            $result['content'] = "Connection error";
        }
        return $result;
    }
	
	function parseHeaders($headerString) {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headerString));
        foreach ($fields as $field) {
            if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
                $retVal[$match[1]] = trim($match[2]);
            }
        }
        return $retVal;
    }
	
	/**
     *
     * Sync Location
     *
     */
    public function sync_location() {
        $return = false;
        $request_url = "v1/meta/lists";
        $response = $this->curlRequest("GET", $request_url);
        if ($response['status'] == 200) {
            $meta_data = json_decode($response['content'], true);
            $dept_key = array_search('location', array_map(function($v) {
                        if (array_key_exists('alias', $v)) {
                            return $v['alias'];
                        }
                    }, $meta_data));
            if (array_key_exists('options', $meta_data[$dept_key])) {
                if (is_array($meta_data[$dept_key]['options']) && !empty($meta_data[$dept_key]['options'])) {
                    foreach ($meta_data[$dept_key]['options'] as $key => $value) {
                        $site_code = $value['id'];
                        $site_name = $value['name'];
						
						// save locations in db...
						
                        $return = true;
                    }
                }
            }
        }
        return $return;
    }
	
	/**
     *
     * Sync Department
     *
     */
	public function sync_department() {
        $return = false;
        $request_url = "v1/meta/lists";
        $response = $this->curlRequest("GET", $request_url);

        if ($response['status'] == 200) {
            $meta_data = json_decode($response['content'], true);

            $dept_key = array_search('department', array_map(function($v) {
                        if (array_key_exists('alias', $v)) {
                            return $v['alias'];
                        }
                    }, $meta_data));

            if (array_key_exists('options', $meta_data[$dept_key])) {
                if (is_array($meta_data[$dept_key]['options']) && !empty($meta_data[$dept_key]['options'])) {
                    foreach ($meta_data[$dept_key]['options'] as $key => $value) {
                        // save department in db...
                    }
                }
            }
        }
        return $return;
    }
	
	/**
     *
     * Download Employees
     *
     */
	public function downloadEmployees() {
		$response = $this->curlRequest("GET", "v1/employees/directory");
		if ($response['status'] >= 200 && $response['status'] <= 299) {
            $emps = json_decode($response['content'], true);
            $employees = $emps['employees'];
            $result['count'] = count($employees);
            
            foreach ($employees as $employee) {
                $update = $this->saveExtEmployee($employee);
            }
            return $result;
        }
	}
	
	private function saveExtEmployee($data) {
		$result = null;
        $employeeID = $data['id'];
        $request_url = "v1/employees/" . $employeeID . "?fields=firstName,lastName,middleName,address1,address2,city,state,stateCode,country,gender,hireDate,lastChanged,payRate,status,bestEmail,homePhone,mobilePhone,zipcode,dateOfBirth,ssn,department,division,payGroup,terminationDate,payType,paidPer,employeeNumber,jobTitle,supervisorEId";
        $response = $this->curlRequest("GET", $request_url);
		
		if ($response['status'] == 200) {
			$employee_detail_data = json_decode($response['content'], true);
			
			// save each employee data in db...
		} else {
			return false;
		}
		
	}
	
	
}