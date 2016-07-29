<?php
require_once("users.php");
require_once("./config/conn_db.php");

class User_DAO extends Users {
	
	public function GetSingle($id){
		$record = array();
		$mysqli = connect();
		$query_string = "SELECT * from users where id = ".$id.""; 	
		// echo $query_string;
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
	}
	
	public function GetAll(){
		$record = array();
		$mysqli = connect();
		$query_string = "SELECT * from users ORDER BY id"; 
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
	}

	public function Insert(array $values){
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
			
			//echo $query_string;
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
	}
	
	public function Update($id, array $values){
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
			$query_string.=" WHERE id = ".$id;
			//echo $query_string;exit;
			if (!$error){
				$query_string = trim($query_string," , ");
				//echo $query_string;
				$stmt = $mysqli->prepare($query_string);
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
	}
	
	public function Delete($id){
		$mysqli = connect();
		$query_string = "DELETE FROM users WHERE id = ".$id."";
		//echo $query_string;exit;
		$stmt = $mysqli->prepare($query_string);
		$result = $stmt->execute();
		return $result;
	}
		
		public function find_last_record($limit){
			$record = array();
			$mysqli = connect();
			$query_string = "SELECT * from users ORDER BY id DESC LIMIT ".$limit.""; 
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
			return array_reverse($record);
			}
		}
		
		public function free_username($value){
			$record = array();
			$mysqli = connect();
			$query_string = "SELECT id FROM users WHERE Username = '".$value."'"; 	
			//echo $query_string;
			$stmt = $mysqli->prepare($query_string);
			$result = $stmt->execute();
			$stmt->bind_result($id);
			$i=0;
			while ($row = $stmt->fetch()){
				$i++;
			}
			return $i;
		}
		
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