<?php

namespace fw;

class db extends \MySqli {
	public $count_sql = 0;
	private $connid = 0;
	public $query = false;
	public $mysqli = false;
	public $error_connect = false;
	public $error_query = false;
	
	public $email = 'valera22031996@gmail.com';
	
	public $checkAttack = true;
	private $attackBuffer = array();
	public $stopAttack = false;
	
	public function start($array){
		$this->mysqli = new \MySqli($array['host'], $array['user'], $array['pass'], $array['base']);
		$this->mysqli->set_charset("utf8");
		if(mysqli_connect_errno()){
			$this->error_connect = true;
			die(mysqli_connect_error());
		}
	}
	
	public function query($query){
		if(!$this->mysqli) $this->connect();
		if(!$this->query = $this->mysqli->query($query)){
			$this->error_query = true;
			echo $this->mysqli->error;
			die(mysqli_connect_error());
		}
		$this->count_sql++;
		return $this->query;
	}
	private function _arrayKeysToSet($values){
		 $ret='';
		  if( is_array($values) ){
			foreach($values as $key=>$value){
			  if(!empty($ret))$ret.=',';
			  $ret.="`$key`='".$this->safe_sql($value)."'";
			}
		  }else $ret=$values;
		  return $ret;
	}
	
	function insertid(){
		if(!$this->mysqli) $this->connect();
		return $this->mysqli->insert_id;
	}
	
	public function exists($sTable,$id,$fieldname='id',$allf='',$field = ''){
		if( !$field )
			$field = $fieldname;
		$item = $this->get_row($this->query('select '.$field.' from '.$this->pfx.$sTable.' where `'.$fieldname.'`=\''.$this->safe_sql($id).'\' '.$allf));
		return $item[$field] ? $item[$field]:false;
	}
	
	function insert( $sTable,$values ){
		$ret = $this->_arrayKeysToSet($values);
		return $this->query('insert into '.$sTable.' set '.$ret);
	  return false;
	}
	
	public function update( $sTable, $values, $sWhere=1 ){
		$ret = $this->_arrayKeysToSet($values);
		return $this->query('update '.$this->pfx.$sTable.' set '.$ret.' where '.$sWhere);
	}

	public function delete( $sTable, $sWhere ){
		return $this->query('delete from '.$this->pfx.$sTable.' where '.$sWhere);
	}
	
	public function get_row($query){
		if(!$this->mysqli) $this->connect();
		return $query->fetch_assoc();
	}
	
	public function get_num_rows($query){
		if(!$this->mysqli) $this->connect();
		return $query->num_rows;
	}
	
	function isItAttack( $id ){
	    if( $this->checkAttack and !in_array($id,$this->attackBuffer) ){
	        $hook = 0 ;
	        if( 
	            (preg_match('/[\']/', $id) and $hook=1) or
	            (preg_match('/(and|null|not)/i', $id) and $hook=3) or
	            (preg_match('/(union|select|from|where)/i', $id) and $hook=4)or
	            (preg_match('/(group|order|having|limit)/i', $id) and $hook=5) or
	            (preg_match('/(into|file|case)/i', $id) and $hook=6) or
	            (preg_match('/--|#|\/\*/', $id) and $hook=7)
	        ){
	            $this->attackBuffer[] = $id;
	            mail($this->email,'Attack on the site',"Site was be attacked - \n\n".
	                "value:".$id."\n\n".
	                "hook:".$hook."\n\n".
	                "ip:".$this->myIP()."\n\n".
	                "request:".$_SERVER['REQUEST_URI']."\n\n".
	                "REQUEST:".var_export($_REQUEST,true));
	            if( $this->stopAttack ){
	                header('HTTP/1.1 500 Internal Server Error');
	                throw new Exception('Divizion by zerro');
	                exit();
	            }
	            return true;
	        }
	    }
	    return false;
	}
	
	public function myIP(){
	    $ipa = explode( ',',@$_SERVER['HTTP_X_FORWARDED_FOR'] );
	    $ip = isset($_SERVER['HTTP_X_REAL_IP'])?$_SERVER['HTTP_X_REAL_IP']:(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:(!empty($ipa[0])?$ipa[0]:'127.0.0.1'));
	    return preg_match('#^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$#',$ip)?$ip:'127.0.0.1';
	}
	
	public function safe_sql($sql){
		if(!$this->mysqli) $this->connect();
		 $this->isItAttack($value);
		return $this->mysqli->real_escape_string($sql);
	}
	
	function getRows( $sql,$field = '' ){
		$inq = $this->query($sql);$items = array();
		while($item = @mysqli_fetch_array($inq)) $items[] = ($field=='')?$item:$item[$field]; 
		return $items;
	}
	
	public function version(){
		if(!$this->mysqli) $this->connect();
		return $this->mysqli->server_info;
	}
	
	public function close(){
		if($this->mysqli) $this->mysqli->close();
	}
}