<?
defined( '_#FWLone' )or die( 'Restricted access' );
  if ( $_POST[ 'type' ] == '1' ) {
	  $login = $cms->guard( $_POST[ 'login' ] );
      $login = $db->safe_sql( $login );

      $pass = $cms->guard( $_POST[ 'pass' ] );
      $pass = md5( $db->safe_sql( $pass ) . 'Trizztime' );

      $nsess = md5( rand( 1111, 1012990 ) . 'key-fwlone' . TIME );
	  
      if ( $login == '' ) {
        die( 'Введите логин!' );
      } else if ( $pass == '' ) {
        die( 'Введите пароль!' );
      } else {
        $us = $db->query( "select * from `users` where `login` = '" . $login . "' and `pass` = '" . $pass . "' limit 1" );
        $us = $db->get_row( $us );
        if ( $us[ 'id' ] ) {

          setcookie( 'user', $us[ 'salt' ], time() + 86400 * 31 );
          setcookie( 'pass', $nsess, time() + 86400 * 31 ); //remember
          //die('Ошибка при вводите логина или пароля, пользователь с таким сочетанием не найден!');

          $db->query( "UPDATE `users` SET `session` = '" . $nsess . "' WHERE `id`='" . $us[ 'id' ] . "'" );
          die();
        } else {
          die( 'Ошибка при вводите логина или пароля, пользователь с таким сочетанием не найден!' );
        }
      }
}