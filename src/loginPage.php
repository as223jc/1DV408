<!-- // <?php

// class LoginModel {
// 	private $users;
// 	private $passwords;
// 	public function __construct(){	
// 		$this->users = array(
// 			"pete",
// 			"marshall",
// 			"jesus",
// 			"joe"
// 			);
// 		$this->passwords = array(
// 				"pete"=>"123",
// 				"marshall"=>"pass",
// 				"jesus"=>"mypw"	
// 			);
// 	}

// 	public function userExists($username){
// 		foreach ($this->users as $user) {
// 			if (strtolower($username) === strtolower($user)) {
// 				 return true;
// 			}
// 		}
// 		return false;
// 	}

// 	public function passwordMatch($username, $password){		
// 		foreach ($this->passwords as $user => $pw) {
// 			if (strtolower($user) === strtolower($username)) {
// 				if ($pw === $password) {
// 					return true;
// 				}
// 			}
// 		}
// 		return false;
// 	}
// }

// class LoginView {
// 	private $loginModel;
// 	private $loginController;

// 	public function __construct($loginModel, $loginController){		
// 		$this->loginModel = $loginModel;
// 		$this->loginController = $loginController;
// 	}
	
	

// }

// class LoginController {
// 	private $loginModel;
// 	public function __construct($loginModel){
// 		$this->loginModel = $loginModel;
// 	}

	
	
// } -->