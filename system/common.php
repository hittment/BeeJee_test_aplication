<?php
defined('_#FWLone') or die('Restricted access');
ob_start();

//запускаем системку
$cms = new \fw\cms;

//Базу данных
$db = new \fw\db;
$db->start($cms->config('db'));


//Шаблонизатор
$tpl = new fw\template(H . $cms->config('site', 'tpl'));
$tpl->set('fwsys', $cms->config('site'));

//Сортировка
switch($_GET['ordBy'])
{
	case 1:
		$ordBy = ' order by usr_name DESC';
		break;
	case 2:
		$ordBy = ' order by usr_name ASC';
		break;
	case 3:
		$ordBy = ' order by usr_email DESC';
		break;
	case 4:
		$ordBy = ' order by usr_email ASC';
		break;
	case 5:
		$ordBy = ' order by status DESC';
		break;
	case 6:
		$ordBy = ' order by status ASC';
		break;
	default:
		$ordBy = ' order by id DESC';
		break;
}
//Cтраница
(isset($_GET['start']) ? $start = intval($_GET['start']) : $start = 1);


//Юзерская
if (isset($_COOKIE['user'], $_COOKIE['pass']) && $_COOKIE['user'] != NULL && $_COOKIE['pass'] != NULL) {

    $salt = $cms->guard($_COOKIE['user']);
    $salt = $db->safe_sql($salt);

    $sess = $cms->guard($_COOKIE['pass']);
    $sess = $db->safe_sql($sess);

    $user_q = $db->query("SELECT * FROM `users` WHERE `salt` = '" . $salt . "' AND `session` = '" . $sess . "' limit 1");
    $user = $db->get_row($user_q);
	
	if(!is_null($user))
	{

        $ip = $cms->guard($_SERVER['REMOTE_ADDR']);
        $ip = $db->safe_sql($ip);

        $ua = $cms->guard($_SERVER['HTTP_USER_AGENT']);
        $ua = $db->safe_sql($ua);

        $page = $cms->guard($_SERVER['REQUEST_URI']);
        $page = $db->safe_sql($page);

        $db->query("UPDATE `users` SET `lasttime` = '" . TIME . "', " . ($user['lasttime'] > (TIME - 300) ? "`onlinetime`=`onlinetime`+'" . (TIME - $user['lasttime']) . "'," : "") . " `ip` = '" . $ip . "', `useragent` = '" . $ua . "', `page` = '" . $page . "' WHERE `id`='" . $user['id'] . "'");

    }

} else {

    $user = false;

}

$tpl->set('tasks',$db->getRows("SELECT id,usr_name,usr_email,text,status,edited from tasks $ordBy limit ".$cms->guard($db->safe_sql($start)).",3"));//Можно улучшить
$tpl->set('tasks_c', $db->get_row($db->query('SELECT COUNT(*) AS cnt from tasks'))['cnt']);
$tpl->set('user', $user);

//добавление задачи в БД
if(isset($_GET['add_task']))
{
	$name = $cms->guard($_GET['usr_name_input']);
	$email = $cms->guard($_GET['usr_email_input']);
	$text = $cms->guard($_GET['usr_task_input']);
	
	(($name != '' and $email != '' and $text !='') ? $db->insert('tasks',array('usr_name' => $name, 'usr_email' => $email, 'text' => $text)) : $err = 1);
	
	if($err == 1)
	{
		die('Не все поля заполнены!');
	}
	die();
}
//изменение задачи
if(isset($_POST['edit_text']))
{
	if(!$user['id'])
	{
		die('Необходима повторная авторизация, перезагрузите пожайлуста страницу, и перейдите на страницу авторизации!');
	}
	$id = $cms->guard($_POST['id']);
	$text = $cms->guard($_POST['edit_text']);
	$status = $cms->guard($_POST['status']);
	$err = '';
	$status = ($status == 'on' ? 1 : 0);
	
	$edited = ( strcmp($text,$db->exists('tasks',$cms->guard($id),'id','','text')) <> 0 ? array('edited' => '1'):array());
	(($id != ''  and $text !='') ? $db->update('tasks',array('text' => $text, 'status' => $status) + $edited, 'id='.$cms->guard($db->safe_sql($id))) : $err = 'Не все поля заполнены!');
	die($err);
}

//Выход
if (isset($_GET["is_exit"])) {
    if ($_GET["is_exit"] == 1) {
        setcookie('user', '', time() + 86400 * 31, '/');
        setcookie('pass', '', time() + 86400 * 31, '/');
        $db->query("UPDATE `users` SET `session` = '" . md5(rand(1111, 1012990) . 'key-fwlone' . TIME) . "' WHERE `id`='" . $user['id'] . "'");

        header('Location: /');
    }
}
//Хеш для юзеров
function GenHash($length = 12, $strength = 4)
{

    $vowels = 'aeuyio0123456789';
    $consonants = 'bdghjmnpqrstvz';

    if ($strength >= 1) {

        $consonants .= 'BDGHJLMNPQRSTVWXZ';

    } elseif ($strength >= 2) {

        $vowels .= "AEUYIO";

    } elseif ($strength >= 4) {

        $vowels .= '0123456789';

    } elseif ($strength >= 8) {

        $vowels .= '@#$%';
    }

    // Генерируем
    $hash = '';

    $alt = TIME % 2;

    for ($i = 0; $i < $length; $i++) {

        if ($alt == 1) {

            $hash .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;

        } else {

            $hash .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;

        }

    }

    return $hash;

}

//Hавигация
function page($link, $posts, $total, $range = 2)
{
	$start = $GLOBALS['start'];
    $pg_cnt = ceil($total / $posts);
    $cur_page = ceil(($start + 1) / $posts);
    $idx_fst = max($cur_page - $range, 1);
    $idx_lst = min($cur_page + $range, $pg_cnt);
    $res = '<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">';
	
    if ($cur_page != 1) {
        $res .= '<li class="page-item"><a class="page-link" href="' . $link . 'start=' . ($cur_page - 2) * $posts . '">Назад</a></li>';
    } else {
        $res .= '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Назад</a></li>';
    }
	
    if (($start - $posts) >= 0) {
        if ($cur_page > ($range + 1)) {
            $res .= ' <li class="page-item"><a class="page-link" href="?start=1" title="Страница №1">1</a></li>';
            if ($cur_page != ($range + 2)) {
                $res .= '';
            }
        }
    }
    for ($i = $idx_fst; $i <= $idx_lst; $i++) {
        $offset_page = ($i - 1) * $posts;
        if ($i == $cur_page) {
            $res .= ' <li class="page-item active"><a  class="page-link" href="#">' . $i . '</a></li> ';
        } else {
            $res .= ' <li class="page-item"><a class="page-link" href="' . ($offset_page != 1 ? $link . 'start=' . $offset_page : str_replace('?', '', $link)) . '"  title="Страница №' . $i . '">' . $i . '</a></li>';
        }
    }
    if (($start + $posts) < $total) {
        if ($cur_page < ($pg_cnt - $range)) {
            if ($cur_page != ($pg_cnt - $range - 1)) {
                $res .= '';
            }
            $res .= ' <li class="page-item"><a class="page-link" href="' . $link . 'start=' . ($pg_cnt - 1) * $posts . '" title="Страница №' . $pg_cnt . '">' . $pg_cnt . '</a></li>';
        }
    }
    if ($cur_page != $pg_cnt) {
        $res .= ' <li class="page-item"><a class="page-link" href="' . $link . 'start=' . ($cur_page * $posts) . '">Вперед</a></li>';
    } else {
        $res .= ' <li class="page-item disabled"><a class="page-link" href="#">Вперед</a></li>';
    }
    $res .= ' </ul></nav>';
    return $res;
}
function z_add_url_get($a_data,$url = false){
        $http = $_SERVER['HTTPS'] ? 'https':'http';

if($url === false){
                   $url = $http.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            }
        $query_str = parse_url($url);
        $path = !empty($query_str['path']) ? $query_str['path'] : '';
        $return_url = $query_str['scheme'].'://'.$query_str['host'].$path;
        $query_str = !empty($query_str['query']) ? $query_str['query'] : false;
        $a_query = array();
        if($query_str) {
            parse_str($query_str,$a_query);
        }
        $a_query = array_merge($a_query,$a_data);
        $s_query = http_build_query($a_query);
        if($s_query){
        $s_query = '?'.$s_query;    
        }
        return $return_url.$s_query;
        }
