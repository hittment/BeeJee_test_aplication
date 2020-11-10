<?

$cats_q = $db->query("select `id`, `title`,`index_pos` from `cats`");
    $cats_arr = [];

    while ($cat = $db->get_row($cats_q)) {
        $cats_arr[$cat['index_pos']] = $cat;
	}
	$tpl->set('cats', $cats_arr);
$us = $db->query("select * from `users` where `session` = '" . $_COOKIE['pass'] . "' limit 1");
$us = $db->get_row($us);
//print_r($us);
$ads_q = $db->query("select * from `ads` WHERE `usr_id` = ".$us['id']."");
//print_r($ads_q);
$ads_arr = [];
while ($ad = $db->get_row($ads_q)) {
    $ads_arr[] = $ad;
}

$creators_q = $db->query("select * from `creators`");
$creators_arr = [];
while ($creator = $db->get_row($creators_q)) {
    $creators_arr[] = $creator;
}

$tpl->set('creators', $creators_arr);
$models_q = $db->query("select * from `models`");
			
//$models = $db->get_row($models);
$models_arr=[];
while ($models = $db->get_row($models_q)) {
	$models_arr[] = $models;
}
$tpl->set('models', $models_arr);
$dialogs_q = $db->query("select * from `dialogs` WHERE `usr_id` = ".$us['id']."");
//print_r($ads_q);
$dialogs_arr = [];
while ($ds = $db->get_row($dialogs_q)) {
    $dialogs_arr[] = $ds;
}
$fav_q = $db->query("select * from `dialogs` WHERE `usr_id` = ".$us['id']."");
//print_r($ads_q);
$fav_arr = [];
while ($fv = $db->get_row($fav_q)) {
    $fav_arr[] = $fv;
}

$tpl->set('my_ads', $ads_arr);
	if (isset($_GET['id']))
	{
		foreach ($ads_arr as $key => $value)
		{
			if ($value['id'] == $_GET['id'])
			{
			$query = "DELETE FROM `ads` WHERE `id`='".$value['id']."'";
			$db->query($query);
			}
		}
		
		foreach ($dialogs_arr as $key => $value)
		{
			if (($value['usr_id'] == $us['id']) and ($value['advert'] == $_GET['id']))
			{
			$query = "UPDATE `dialogs` SET `in_archive` = '1' WHERE `advert`='" . $value['advert'] . "' and `usr_id`= '".$value['usr_id']."'";
			$db->query($query);
			}
		}
	    foreach ($fav_arr as $key => $value)
		{
			if ($value['advert'] == $_GET['id'])
			{
			$query = "UPDATE `favorites` SET `is_deleted` = '1' WHERE `adv_id`='" . $value['advert'] . "'";
			$db->query($query);
			}
		}
		echo '0';
		die();
	}




?>