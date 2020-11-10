<?php
namespace fw;

class autoloader
{

    public static function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }
	
    public static function autoload($class)
    {
		$class = str_replace('\\', '/', $class);
		$path = $_SERVER['DOCUMENT_ROOT'] . '/system/lib/' . $class . '.php';
		if(!is_file($path)) {
			throw new \Exception('?????????? ?? ???????: ' . $path);
		}else{
			require $path;
		}
    }
}
