<?php

/* User_DAO class
Is the data access object associated at Users class. Get the parsed request from the front_controller
and read or write it from the database. */

require_once("users.php");
require_once("./config/conn_db.php");

class User_DAO{
	
	/* Returns the user entity associated at ID passed as an argument */
	
	public function getSingle($id){
		$record = array();
		$mysqli = connect();
		$query_string = "SELECT * from users where id = ?"; 	
		$stmt = $mysqli->prepare($query_string);
		$stmt->bind_param('s', $id);
		$result = $stmt->execute();			
		if($result){
			$stmt->bind_result($id, $username, $nome, $cognome, $birthday);
			$i=0;
			while ($row = $stmt->fetch()){
				$record[$i]["username"] = $username;
				$record[$i]["nome"] = $nome;
				$record[$i]["cognome"] = $cognome;
				$record[$i]["bornDate"] = $birthday;
				$i++;
			}
		return $record;
		}		
		$mysqli->close();
	}
	
	/* Returns all the user entities in the database */
	
	public function getAll(){
		$record = array();
		$mysqli = connect();
		$query_string = "SELECT * from users ORDER BY Cognome"; 
		//echo $query_string;
		$stmt = $mysqli->prepare($query_string);
		$result = $stmt->execute();			
		if($result){
			$stmt->bind_result($id, $username, $nome, $cognome, $birthday);
			$i=0;
			while ($row = $stmt->fetch()){
				$record[$i]["username"] = $username;
				$record[$i]["nome"] = $nome;
				$record[$i]["cognome"] = $cognome;
				$record[$i]["bornDate"] = $birthday;
				$i++;
			}
		return $record;
		}
		$mysqli->close();
	}

	/* Insert into database one or multiple user objects passed as argument */
	
	public function insert(array $values){
		if(!empty($values)){
			$array_val = array();
			$elements = 0;
			$error = false;
			$result = "";
			$mysqli = connect();
			foreach ($values as $key=>$value){
				$_key = $this->no_numbers($key);
				if($_key=="Username"){
					$check= (int)$this->free_username($value);
					if ($check > 0){
						$error = true;
						$error_val = $value;
					}
					$elements++;
				}
				array_push($array_val, $value);
			}
			$array_ins = array_chunk($array_val, 4);
			$query_string = "INSERT INTO users (Username, Nome, Cognome, Birthday) VALUES ";
			for ($x=0; $x<$elements; $x++){
				$query_string .= "(";
				for ($i=0; $i<4; $i++){ 
					if($i==3){
						$query_string .= "'".$array_ins[$x][$i]."'";
					}
					else {
						$query_string .= "'".$array_ins[$x][$i]."', ";
					}
				}
				if($x==$elements-1){
					$query_string .= ")";
				} else {
					$query_string .= "),";
				}
			}
			if (!$error){
				$query_string = trim($query_string," , ");
				$stmt = $mysqli->prepare($query_string);
				$result = $stmt->execute();
			} else {
				$error = "Username ". $error_val ." already exists!";
				return $error;
			}
		}
		if($result){
			$inserted_data = $this->find_last_record($elements);
			return $inserted_data;
		}
		$mysqli->close();
	}
	
	/* Updates the values of the user entity associated at ID passed as an argument  */
	
	public function update($id, array $values){
		if(!empty($values)){
			$error = false;
			$mysqli = connect();
			$query_string = "UPDATE users SET ";
			$assigned = $this->GetSingle($id);
			foreach($values as $key=>$value){
				switch($key){
					case "Username":
						if(!empty($value)){
							$check= (int)$this->free_username($value);
							if ($check > 0){
								if($assigned[0]["username"] != $value){
									$error = true;
								}
								else {
									$query_string.=$key." = '".addslashes(mysqli_real_escape_string($mysqli, $value))."' , ";
							}
							} else {
								$query_string.=$key." = '".addslashes(mysqli_real_escape_string($mysqli, $value))."' , ";
							}
						} else {
							$query_string.=$key." = '".addslashes(mysqli_real_escape_string($mysqli, $assigned[0]["username"]))."' , ";
						}
						break;
					case "Nome":
						if(!empty($value)){
								$query_string.=$key." = '".addslashes(mysqli_real_escape_string($mysqli, $value))."' , ";
						} else {
							$query_string.=$key." = '".addslashes(mysqli_real_escape_string($mysqli, $assigned[0]["nome"]))."' , ";
						}
						break;
					case "Cognome":
						if(!empty($value)){
								$query_string.=$key." = '".addslashes(mysqli_real_escape_string($mysqli, $value))."' , ";
						} else {
							$query_string.=$key." = '".addslashes(mysqli_real_escape_string($mysqli, $assigned[0]["cognome"]))."' , ";
						}
						break;
					case "Birthday":
						if(!empty($value)){
								$query_string.=$key." = '".addslashes(mysqli_real_escape_string($mysqli, $value))."' , ";
						} else {
							$query_string.=$key." = '".addslashes(mysqli_real_escape_string($mysqli, $assigned[0]["bornDate"]))."' , ";
						}
						break;	
				}
			}		
			$query_string = trim($query_string," , ");
			$query_string.=" WHERE id = ?";
			if (!$error){
				$query_string = trim($query_string," , ");
				$stmt = $mysqli->prepare($query_string);
				$stmt->bind_param('s', $id);
				$result = $stmt->execute();
			} else {
				$error = "Username already exists!";
				return $error;
			}
			if($result){
				$inserted_data = $this->GetSingle($id);
				return $inserted_data;
			}			
		}
		$mysqli->close();
	}
	
	/* Remove the user entity associated at ID passed as an argument from database */
	
	public function delete($id){
		$mysqli = connect();
		$query_string = "DELETE FROM users WHERE id = ?";
		$stmt = $mysqli->prepare($query_string);
		$stmt->bind_param('s', $id);
		$result = $stmt->execute();
		return $result;
		$mysqli->close();
	}
		
	/* Returns an array whit the last inserted user entity's information */	
		
	public function find_last_record($limit){
		$record = array();
		$mysqli = connect();
		$query_string = "SELECT * from users ORDER BY id DESC LIMIT ?"; 
		$stmt = $mysqli->prepare($query_string);
		$stmt->bind_param('s', $limit);
		$result = $stmt->execute();			
		if($result){
			$stmt->bind_result($id, $username, $nome, $cognome, $birthday);
			$i=0;
			while ($row = $stmt->fetch()){
				$record[$i]["username"] = $username;
				$record[$i]["nome"] = $nome;
				$record[$i]["cognome"] = $cognome;
				$record[$i]["bornDate"] = $birthday;
				$i++;
			}
			return array_reverse($record);
		}
		$mysqli->close();
	}
	
	/* Returns 1 if the username values, passed as an argument, already exists in database, 0 if not */
	
	public function free_username($value){
		$record = array();
		$mysqli = connect();
		$query_string = "SELECT id FROM users WHERE Username = ?"; 	
		$stmt = $mysqli->prepare($query_string);
		$stmt->bind_param('s', $value);
		$result = $stmt->execute();
		$stmt->bind_result($id);
		$i=0;
		while ($row = $stmt->fetch()){
			$i++;
		}
		return $i;
		$mysqli->close();
	}
	
	/* Remove numeric part of the variables id passed in the body of the request */
		
	function no_numbers($str){
		$newStr="";
		for ($i=0;$i<strlen($str);$i++){
			if (!is_numeric($str{$i}))
				$newStr=$newStr.$str{$i};
			}
			return $newStr;
		}
	}
?>