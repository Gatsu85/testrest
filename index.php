

<?php

/* Front Controller 
 Get the URI request and derives the class to call. Request at the Hendler to return 
 the request method and to parse the body of the request. Once had the answer, it sends 
 the data to access data object associated at the class, calling the function associated 
 at the method. Finally, it returns the status of the request and the response to the request. */

require __DIR__ . '\vendor\autoload.php';
use testrest\handler;
use testrest\users\user_dao;
use Monolog\Logger;


if (isset($_SERVER['REQUEST_URI'])){
	
	$uri = $_SERVER['REQUEST_URI'];
	$domain = explode("/", $uri);
	$log = new Monolog\Logger('name');
	$handler = new testrest\handler();
	
	/* class select */
	
	switch ($domain[2]){
		case "users":
			require_once("src/Classes/users/model/user_dao.php");
			$user = new testrest\users\user_dao();
			$function = $handler->getMethod();
			$user_id = (int)$domain[3];
			
			/* userS function select */
			
			switch ($function){
				case "GET":
					if ($domain[3] != "" ){
						$response = $user->getSingle($user_id);
						if (!empty($response)){
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::INFO));
							$log->addInfo('GET request, Stato: 200, Users Found!');
							echo $handler->response($response);
						} else {
							$response = "No user found for this id!";
							$status = 404;
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
							$log->addError('GET request, Stato: 404 No user found for this id!');
							echo $handler->response($response, $status);
						}
					} else {
						$response = $user->getAll();
						$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::INFO));
						$log->addInfo('GET request, Stato: 200, Data returned!');
						echo $handler->response($response);
					}
					break;
				case "POST":
					$data = $handler->inputs();
					if(!empty($data)){
						$response = $user->Insert($data);
						if (is_array($response)){
							$status = 201;
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::INFO));
							$log->addInfo('POST request, Stato: 201, Data inserted!');
							echo $handler->response($response,$status);
						} else {
							$status = 400;
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
							$log->addError('POST request, Stato: 400, Error inserting data!');
							echo $handler->response($response,$status);
						}
					} else{
						$response = "Void Request!";
						$status = 400;
						$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
						$log->addError('POST request, Stato: 400, Void Request!');
						echo $handler->response($response,$status);
					}
					break;
				case "DELETE":
					$user_id = (int)$domain[3];
					if ($domain[3] != "" ){
						$response = $user->delete($user_id);
						if ($response>0){
							$response = "User with id ". $user_id ." has been deleted!";
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::INFO));
							$log->addInfo('DELETE request, Stato: 200, Data deleted!');
							echo $handler->response($response);
						} else {
							$status = 404;
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
							$log->addError('DELETE request, Stato: 404, Error accurring deleting data!');
							echo $handler->response($status);
						}
					} else{
						$response = "Void Request!";
						$status = 400;
						$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
						$log->addError('DELETE request, Stato: 400, Void Request!');
						echo $handler->response($response,$status);
					}
					break;
				case "PUT":
				case "PATCH":
					$user_id = (int)$domain[3];
					$data = $handler->inputs();
					if ($domain[3] != "" && !empty($data)){
						$response = $user->update($user_id,$data);
						if ($response>0){
							$status = 201;
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::INFO));
							$log->addInfo('PUT/PATCH request, Stato: 201, Data Updated!');
							echo $handler->response($response,$status);
						} else {
							$status = 400;
							$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
							$log->addError('PUT/PATCH request, Stato: 400, Error updating data!');
							echo $handler->response($response,$status);
						}
					} else{
						$response = "Void Request!";
						$status = 404;
						$log->pushHandler(new Monolog\Handler\StreamHandler('app.log', Monolog\Logger::ERROR));
						$log->addError('PUT/PATCH request, Stato: 400, Void Request!');
						echo $handler->response($response,$status);
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