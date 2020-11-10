<?php
namespace fw;

class template extends\ fw\ cms {

  private $dir_tmpl;
  private $adir_tmpl;
  private $data = array();


  public function __construct( $dir_tmpl ) {
    $this->dir_tmpl = $dir_tmpl;
  }

  public function set( $name, $value ) {
    $this->data[ $name ] = $value;
  }

  public function del( $name ) {
    unset( $this->data[ $name ] );
  }

  public function __get( $name ) {
    if ( isset( $this->data[ $name ] ) ) {
      return $this->data[ $name ];
    } elseif ( isset( $this->data[ 'fwsys' ][ $name ] ) ) {
      return $this->data[ 'fwsys' ][ $name ];
    }
    return "";
  }

  public function display( $template ) {
    if ( is_file( $this->dir_tmpl . $template . '.html' ) ) {
      require( $this->dir_tmpl . $template . '.html' );
    } else {
      throw new\ Exception( 'Шаблон не найден: ' . $template );
    }
  }

  public function lng( $word ) {
    if ( !empty( $this->data[ 'lng_words' ][ $word ] ) ) {
      return $this->data[ 'lng_words' ][ $word ];
    } else {
      return 'Undef';
    }
  }

  public function set_alert( $type, $mess ) {
    $this->data[ 'alert' ] = array( 'type' => $type, 'message' => $mess );
  }

  public function fw_alert() {
    if ( !empty( $this->data[ 'alert' ] ) ) {
      return '
			<div class="alert alert-' . $this->data[ 'alert' ][ 'type' ] . ' alert-dismissible fade show" role="alert">
  				' . $this->data[ 'alert' ][ 'message' ] . '
  				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    				<span aria-hidden="true">&times;</span>
  				</button>
			</div>';
    } else {
      return '';
    }
  }

}

?>