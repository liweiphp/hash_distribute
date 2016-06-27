<?php
/**
* mc 一致性实现
* @author L
* @
*/
class mc_distribute
{
	private $mc_server = array();
	private $is_stored = FALSE;
	private function _mcHash($key){
		$md5 = substr(md5($key),0,8);
		$seed = 31;
		$hash = 0;
		for ($i=0; $i < 8; $i++) { 
			$hash = $hash*$seed + ord($md5{$i});
			$i++;
		}
		return $hash&0x7FFFFFFF;
	}
	public function addServer($server){
		$hash =- $this->_mcHash($server);
		if(!isset($this->mc_server[$hash])){
			$this->mc_server[$hash] = $server;
		}
		$this->is_stored = FALSE;
		return true;
	}
	public function removeServer($server){
		$hash = $this->_mcHash($server);
		if(isset($this->mc_server[$hash])){
			unset($this->mc_server[$hash]);
		}
		$this->is_stored = FALSE;
		return true;
	}
	public function find($key){
		$hash = $this->_mcHash($key);
		if(!$this->is_stored){
			krsort($this->mc_server,SORT_NUMERIC);
			$this->is_stored = true;
		}
		foreach($this->mc_server as $pos=>$server){
			if($hash >= $pos) return $server;
		}
		return $this->mc_server[count($this->mc_server)-1];
	}
}
