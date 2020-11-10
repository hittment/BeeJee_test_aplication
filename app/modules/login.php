<?
defined( '_#FWLone' )or die( 'Restricted access' );
//$cms->accessSecure( $user );
  if ( $_POST[ 'type' ] == '1' ) {
	  $login = $cms->guard( $_POST[ 'login' ] );
      $login = $db->safe_sql( $login );

      $pass = $cms->guard( $_POST[ 'pass' ] );
      $pass = md5( $db->safe_sql( $pass ) . 'Trizztime' );

      $nsess = md5( rand( 1111, 1012990 ) . 'key-fwlone' . TIME );
	  
// $db->query( "UPDATE `users` SET `pass` = '" . $pass . "' WHERE `login`='admin2'" );
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

    if ( $_POST[ 'type' ] == '2' ) {

      $login = $cms->guard( $_POST[ 'number' ] );
      $login = $db->safe_sql( $login );

      $temail = $db->safe_sql( $cms->guard( $_POST[ 'email' ] ) );

      $tcity = $db->safe_sql( $cms->guard( $_POST[ 'city' ] ) );

      $pass = $cms->guard( $_POST[ 'pass' ] );
      $pass = md5( $db->safe_sql( $pass ) . 'Trizztime' );

      $name = $cms->guard( $_POST[ 'name' ] );
      $name = $db->safe_sql( $name );

      $us = $db->query( "select * from `users` where `login` = '" . $login . "' limit 1" );
      $us = $db->get_row( $us );

      $cmail = $db->query( "select `email` from `users` where `email` = '$temail' limit 1" );
      $cmail = $db->get_row( $cmail );


      if ( $login == '' ) {
        die( 'Введите номер!' );
      } else if ( $pass == '' ) {
        die( 'Введите пароль!' );
      } else {
        $nsess = md5( rand( 1111, 1012990 ) . 'key-fwlone' . TIME );
        $salt = GenHash( 32, 4 );

        $query = $db->query( "INSERT INTO `users` (`login`,`email`, `pass`, `city`, `salt`, `session`, `first_name`, `regtime`, `lasttime`, `onlinetime`)
                  VALUES ('" . $login . "','$temail', '" . $pass . "','" . $tcity . "', '" . $salt . "', '" . $nsess . "', '" . $name . "', '" . TIME . "', '" . TIME . "', '0')
                " );

        setcookie( 'user', $salt, time() + 86400 * 31, '/' );
        setcookie( 'pass', $nsess, time() + 86400 * 31, '/' ); //remember
        die(); //header('location: /');

      }

    }
