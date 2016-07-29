<?php

/* Front Controller  */

require_once("includes/handler.php");

if (isset($_SERVER['REQUEST_URI'])){
	
	$uri = $_SERVER['REQUEST_URI'];
	$domain = explode("/", $uri);
	
	/* class select */
	
	switch ($domain[2]){
		case "users":
			require_once("users/model/user_dao.php");
			$user_handler = new Handler();
			$function = $user_handler->get_request_method();
			$user_id = (int)$domain[3];
			
			/* userS function select */
			
			switch ($function){
				case "GET":
					if ($domain[3] != "" ){
						$user = new User_DAO();
						$response = $user->GetSingle($user_id);
						if (!empty($response)){
							echo $user_handler->response($response);
						} else {
							$response = "No user found for this id!";
							$status = 404;
							echo $user_handler->response($response,$status);
						}
					} else {
						$user = new User_DAO();
						$response = $user->GetAll();
						echo $user_handler->response($response);
					}
					break;
				case "POST":
					$data = $user_handler->inputs();
					if(!empty($data)){
						$user = new User_DAO();
						$response = $user->Insert($data);
						if (is_array($response)){
							$status = 201;
							echo $user_handler->response($response,$status);
						} else {
							$status = 400;
							echo $user_handler->response($response,$status);
						}
					} else{
						$response = "Void Request!";
						$status = 400;
						echo $user_handler->response($response,$status);
					}
					break;
				case "DELETE":
					$user_id = (int)$domain[3];
					if ($domain[3] != "" ){
						$user = new User_DAO();
						$response = $user->Delete($user_id);
						if ($response>0){
							$response = "User with id ".$user_id." has been deleted!";
							echo $user_handler->response($response);
						} else {
							$status = 404;
							echo $user_handler->response($response,$status);
						}
					} else{
						$response = "Void Request!";
						$status = 400;
						echo $user_handler->response($response,$status);
					}
					break;
				case "PUT":
				case "PATCH":
					$user_id = (int)$domain[3];
					$data = $user_handler->inputs();
					if ($domain[3] != "" && !empty($data)){
						$user = new User_DAO();
						$response = $user->Update($user_id,$data);
						if ($response>0){
							$status = 201;
							echo $user_handler->response($response,$status);
						} else {
							$status = 400;
							echo $user_handler->response($response,$status);
						}
					} else{
						$response = "Void Request!";
						$status = 404;
						echo $user_handler->response($response,$status);
					}
					break;
				default:
					break;
			}
			break;
		default:
			break;
	}
}
?>