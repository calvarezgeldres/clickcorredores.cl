<?php
/* Programador : Luis Olguin - ProgWebChile.cl
 * Descripcion: Generador de mensajes de alerta
 * */
 error_reporting(1);
class msgBox{
	public $tipo;
	public $msg;
	public function __construct($tipo,$msg){
		$this->tipo=$tipo;
		$this->msg=$msg;
		if($this->tipo==1){
			$this->mensajeInfo($this->msg);
		}else if($this->tipo=2){
			$this->mensajeError($this->msg);
		}else if($this->tipo==3){
			$this->mensajeSuceso($this->msg);
		}else if($this->tipo==4){
			$this->mensajeAdvertencia($this->msg);
		}else{
			$this->msg->mensajeInfo($this->msg);
		}
	}
	public function msgBoxButton($msg){
		echo '<div class="alert alert-warning alert-dismissable">
  			<button type="button" class="close" data-dismiss="alert">&times;</button>
  			<strong>¡Cuidado!</strong>'.$msg.'.
			</div>';
			return(true);
	}
	public function msgBoxUrl($msg,$url){
		echo '<div class="alert alert-success">'.$msg.'<a href="'.$url.'" class="alert-link">'.$url.'</a></div>';
		return(true);
	}
	public function mensajeInfo($msg){
		echo "<div style='padding:3px; width:100%;font-size:14px;' class='alert alert-info'>".$msg."</div>";                
		return(true);
	}
	public function mensajeError($msg){
		echo "<div style='padding:3px; width:70%;' class='alert alert-danger'>".$msg."</div>";
        return(true);      
	}
	public function mensajeSuceso($msg){
		echo "<div style='padding:3px; width:70%;' class='alert alert-success'>".$msg."</div>";
      	return(true);        
	}
	public function mensajeAdvertencia($msg){
		echo "<div style='padding:3px; width:70%;' class='alert alert-warning'>".$msg."</div>";
       return(true);        
	}
}

?>