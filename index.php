

<?php

/* Front Controller 
 Get the URI request and derives the class to call. Request at the Hendler to return 
 the request method and to parse the body of the request. Once had the answer, it sends 
 the data to access data object associated at the class, calling the function associated 
 at the method. Finally, it returns the status of the request and the response to the request. */

require __DIR__ . '\vendor\autoload.php';
require __DIR__ . '/src/handler.php';
use Monolog\Logger;

if (isset($_SERVER['REQUEST_URI'])){
	
	$uri = $_SERVER['REQUEST_URI'];
	$domain = explode("/", $uri);
	$log = new Monolog\Logger('name');
	
	/* class select */
	
	switch ($domain[2]){
		case "users":
			require_once("src/Classes/users/model/user_dao.php");
			$user_handler = new Handler();
			$function = $user_handler->getMethod();
			$user_id = (int)$domain[3];
			
			/* userS function select */
			
			switch ($function){
				case "GET":
					if ($domain[3] != "" ){
						$user = new User_DAO();
						$response = $user->getSingle($user_id);
						if (!empty($response)){
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::INFO));
							$log->addInfo('GET request, Stato: 200, Users Found!');
							echo $user_handler->response($response);
						} else {
							$response = "No user found for this id!";
							$status = 404;
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
							$log->addError('GET request, Stato: 404 No user found for this id!');
							echo $user_handler->response($response, $status);
						}
					} else {
						$user = new User_DAO();
						$response = $user->getAll();
						
						$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::INFO));
						$log->addInfo('GET request, Stato: 200, Data returned!');
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
							
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::INFO));
							$log->addInfo('POST request, Stato: 201, Data inserted!');
							echo $user_handler->response($response,$status);
						} else {
							$status = 400;
							
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
							$log->addError('POST request, Stato: 400, Error inserting data!');
							echo $user_handler->response($response,$status);
						}
					} else{
						$response = "Void Request!";
						$status = 400;
						
						$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
						$log->addError('POST request, Stato: 400, Void Request!');
						echo $user_handler->response($response,$status);
					}
					break;
				case "DELETE":
					$user_id = (int)$domain[3];
					if ($domain[3] != "" ){
						$user = new User_DAO();
						$response = $user->delete($user_id);
						if ($response>0){
							$response = "User with id ". $user_id ." has been deleted!";
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::INFO));
							$log->addInfo('DELETE request, Stato: 200, Data deleted!');
							echo $user_handler->response($response);
						} else {
							$status = 404;
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
							$log->addError('DELETE request, Stato: 404, Error accurring deleting data!');
							echo $user_handler->response($status);
						}
					} else{
						$response = "Void Request!";
						$status = 400;
						$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
						$log->addError('DELETE request, Stato: 400, Void Request!');
						echo $user_handler->response($response,$status);
					}
					break;
				case "PUT":
				case "PATCH":
					$user_id = (int)$domain[3];
					$data = $user_handler->inputs();
					if ($domain[3] != "" && !empty($data)){
						$user = new User_DAO();
						$response = $user->update($user_id,$data);
						if ($response>0){
							$status = 201;
							
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::INFO));
							$log->addInfo('PUT/PATCH request, Stato: 201, Data Updated!');
							echo $user_handler->response($response,$status);
						} else {
							$status = 400;
							
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
							$log->addError('PUT/PATCH request, Stato: 400, Error updating data!');
							echo $user_handler->response($response,$status);
						}
					} else{
						$response = "Void Request!";
						$status = 404;
						
						$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
						$log->addError('PUT/PATCH request, Stato: 400, Void Request!');
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