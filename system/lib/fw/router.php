<?php

namespace fw;

class router {

	public function __construct()
	{
		$this -> template = $this -> load();
	}

	public function load()
	{
		$request = isset($_GET['fw']) && $_GET['fw'] !== '' ? str_replace('..', '', $_GET['fw']) : 'main';
		
		if (substr($request, -1) == '/')
			$request = $request.'index';
		
		if (!file_exists(H.'/app/modules/'.$request.'.php'))
			$request = 'main';	

		if(!is_file(H.'/app/modules/'.$request.'.php'))
			throw new \Exception('Модуль не найден: ' . $request);
		else
		return $request;

	}
}