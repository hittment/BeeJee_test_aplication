<?
define('USERNAME', 'emedico-api');
define('PASSWORD', 'emedico*?1');
define('GATEWAY_URL', 'https://web.rbsuat.com/ab/rest/');
define('RETURN_URL', 'https://medpartico.ru');

if (isset($_GET['pay_id']))
	{
	 $current_time = TIME;
	 $amount = 0;
	 $err['href'] = '0';
	 $amount = ($_GET["recom"] === 'true' ? 40000 : 0) + ($_GET["special"] === 'true' ? 20000 : 0);
	 $amount = ($_GET["comb"] === 'true' ? 48000 : $amount+0);
	 $desc = ($_GET["recom"] === 'true' ? 'Добавление в спецпредложения' : '').($_GET["special"] === 'true' ? ' Поднятие в топ' : '');
	 $desc = ($_GET["comb"] === 'true' ? 'Комбинированное продвижение' : $desc.'');
	 $data = array(
        'userName' => USERNAME,
        'password' => PASSWORD,
        'orderNumber' => $current_time,
        'amount' => $amount,
        'returnUrl' => RETURN_URL,
		'description' => $desc,
    );
	$response = $tpl->gateway('register.do', $data);
	
	if (isset($response['errorCode'])) { // В случае ошибки вывести ее
        $err['errors'] = 'Ошибка #' . $response['errorCode'] . ': ' . $response['errorMessage'];
    } else { // В случае успеха перенаправить пользователя на платежную форму
	 $err['href'] = $response['formUrl'];
	 $query = "INSERT INTO `orders`(`order_id`, `ad_id`,`exp_time`) VALUES ('".$current_time."','".$_GET["pay_id"]."', '". ($current_time + 604800) ."')";
	 $db->query($query);
	  $err['errors'] = false;
        //header('Location: ' . $response['formUrl']);
        //die();
    }
  if (@$err['href'] !=='')
 {
	 //$err['href'] = '../';
	 $err['errors'] = false;
 	 die(json_encode($err));
 }else
 {
	 $err['href'] = '0';
 }
	}

?>