<?php
ob_start();
error_reporting(1);
ini_set("display_errors", 1);
require_once("./clases/class.coneccion.php");
require_once("./clases/class.phpmailer.php");
require_once("./clases/class.smtp.php");
require_once("./clases/class.form.php");
require_once("./clases/class.gMaps.php");
require_once("./clases/class.sqlPlus.php");
//require_once("./clases/class.miniGrid.php"); 
//require_once("./clases/class.upload.php");
class detOrbis extends coneccion{
  public $miForm;
  public $geo;
  public $sql;

  public function __construct(){
    $this->miForm=new form();
		$this->geo=true;
    $this->sql=new sql();	
  }
  public function microtime_float(){
	list($useg, $seg) = explode(" ", microtime());
		return ((float)$useg + (float)$seg);
}
public function tiempoInicio(){
	$this->tInicio = microtime(true);
}
public function tiempoFin(){ 
	$tiempo_fin = microtime(true);
	$s=($tiempo_fin - $this->tInicio);
	echo "página generada en : " . round($tiempo_fin - $this->tInicio, 4) ." segundos"; 
} 
  public function devolverPagina($id){
	$this->link=$this->conectar();
	$sql="select* from mm_coti_contenido where idCate='".$id."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	$id=$r["idContenido"];
 
	return($id);
}
public function detDisp(){		
	$tablet_browser = 0;
	$mobile_browser = 0;
	$body_class = 'desktop'; 
	if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$tablet_browser++;
			$body_class = "tablet";
	} 
	if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		$mobile_browser++;
		$body_class = "mobile";
	} 
	if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		$mobile_browser++;
		$body_class = "mobile";
	} 
	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
	$mobile_agents = array(
	'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
	'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
	'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
	'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
	'newt','noki','palm','pana','pant','phil','play','port','prox',
	'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
	'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
	'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
	'wapr','webc','winw','winw','xda ','xda-'); 
	if (in_array($mobile_ua,$mobile_agents)) {$mobile_browser++;} 
	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {$mobile_browser++;   		
		$stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
	if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {$tablet_browser++;}
}
if ($tablet_browser > 0) {$k="tableta";return($k);}else if ($mobile_browser > 0) {$k="movil";return($k); }else {$k="desktop";return($k);} 
} 	
public function desencriptar($string, $key) {
	
	$result = '';
	$string = base64_decode($string);
	for($i=0; $i<strlen($string); $i++) {
	   $char = substr($string, $i, 1);
	   $keychar = substr($key, ($i % strlen($key))-1, 1);
	   $char = chr(ord($char)-ord($keychar));
	   $result.=$char;
	}
	return $result;
  }
public function  emailContacto($nombre=false,$email2=false,$tel=false,$des=false,$emailDestino=false,$operacion=false,$comuna=false){
	$this->link=$this->conectar();
	$d=$this->datosPag();			
		$c=$d["correo"];			
		$p="a)Fs*C2XKJGK";
		$mail = new PHPMailer();

	 
	

 


	$mail->From = "contacto@clickcorredores.cl";	
	$mail->IsSMTP();
	$mail->CharSet="UTF-8";
	$mail->SMTPSecure = 'ssl';
	$mail->Host = 'mail.clickcorredores.cl';
	$mail->Port = 465;
	$mail->Username = 'contacto@clickcorredores.cl';
	$mail->Password = 'Prueba1234_1234';
	$mail->SMTPAuth = true;		
	$mail->FromName =utf8_encode($nombre);
	$mail->SMTPDebug=0;	 
	$mail->IsHTML(true);
	$mail->Subject    ="Formulario de contacto";
	$mail->AltBody    = "Formulario de contacto";
 
	
	$email=$emailDestino;
	$mail->AddAddress("contacto@clickcorredores.cl"); // send the mail to yourself;
	$mensaje="<div><h3>Formulario de Contacto</h3></div>";
	$mensaje.="<div>Necesitas arrendar o vender:<b>".$operacion."</b></div>";
	$mensaje.="<div>Nombre:<b>".$nombre."</b></div>";
	$mensaje.="<div>Telefono:<b>".$tel."</b></div>";
	$mensaje.="<div>Email:<b>".$email2."</b></div>"; 
	$mensaje.="<div>Comuna:<b>".$comuna."</b></div>"; 
	$mensaje.="<div>Descripción:<b><br><br>".$des."</b></div>";
	$mensaje.="<br><br>";
	
 
	$mensaje.="<div>Enviado desde el formulario de contacto de clickcorredores.cl</div>";

	
	$mail->Body    = $mensaje;
	if($mail->send()){
		return(true);
	}else{
		return(false);
	}
	$mail->ClearAllRecipients();
	
}
private function sliderYapo($fotos){
	 
	echo '<div>
	   <div class="container-body detail">
	   <div class="slider_container">			
	   <div class="slider-for">';
	   foreach ($fotos as $clave=>$valor){
		if($this->verificaAws($valor)){
			echo '<a class="item mfp-gallery" href="'.$valor.'">';	
				echo '<img src="'.$valor.'">';
				echo "</a>";
		   }else{
			echo '<a class="item mfp-gallery" href="./upload/'.$valor.'">';	
				echo '<img src="./upload/'.$valor.'">';
				echo "</a>";
		   }				
	   }	  
	   echo '</div>';
	   
	   echo '<div class="slider-nav">';
	   foreach($fotos as $clave=>$valor){
		if($this->verificaAws($valor)){
		   echo '<div class="item"><img src="'.$valor.'" alt="" /></div>';		
		}else{
			echo '<div class="item"><img src="./upload/'.$valor.'" alt="" /></div>';		
		}
	   }
	   echo '</div>';		
	   echo '</div>	 
   
	   <noscript>
		   <ul class="bx-slider" id="slider">';
		   foreach($fotos as $clave=>$valor){
			   echo '<li data-src="./upload/'.$valor.'" data-index="0" class="imgll">
					   <img src="./upload/'.$valor.'" style="width:334px;height:250px;">						
				  </li>';
		   }
		   echo '</ul>
	   </noscript>
	   </div>
   </div>';
}	 


public function devolverEstacionamientos($id){
	$arrSel12=array(1=>"1 estacionamiento",2=>"2 o mas");
	return($arrSel12[$id]);
}
public function verificaAws($foto){		
	if(preg_match("/https/",$foto)){
		return(true);
	}else{
		return(false);
	}
}
public function detProp(){		
	
	$this->link=$this->conectar();
	if(isset($_GET["idProp"])){
		$id=htmlentities($_GET["idProp"]); 
	}
	$sql=$this->sql->sqlConsultarUnaVivienda($id);
 
 
	  
	$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
	$row=mysqli_fetch_array($query);

	$this->titulo=$row["titulo"];
	 $operacion=$row["operacion"];
	$idProp=$row["idProp"];
	$estado=$row["estadoProp"];
	$cadena2=$this->sql->sqlConsultarIdFotos($idProp);
	 
	$queryFotos=mysqli_query($this->link,$cadena2); 
 
	while($r=mysqli_fetch_array($queryFotos)){
	$arch[]=$r["ruta"];
	}
 
	echo "<div style='margin-bottom:30px;margin-top:10px;font-size:14px !important;'><i class='fas fa-home'></i> <a href='index.php' style='color:gray !important;font-size:14px !important;'>Inicio</a> / ".$row["titulo"]."</div>";
	echo "<div><h4>".$row["titulo"]."</h4></div>";	

	echo "<div style='margin-bottom:10px;font-size:16px;'><i class='fas fa-map-marker-alt'></i> Región :".$this->devolverRegion($row["idRegion"])." | Ciudad: ".$this->devolverCiudad($row["idCiudad"])." | Comuna: ".$this->devolverComuna($row["idComuna"])."</div>";
	echo "<div style='margin-bottom:10px;'>";
	echo "<b style='color:gray;'>".$this->devolverOperacion($row["operacion"])."&nbsp;".$this->devolverTipoProp($row["tipoProp"])." &nbsp;";
	if($row["precioUf"]==2){
		echo "UF ".$this->formatoNumerico($row["precio"]);
	}else{
		echo "$ ".$this->formatoNumerico($row["precio"]);
	}
	echo "</b></div>";

	echo "<div>";
	$this->sliderYapo($arch);
	echo "</div>";

 
 
 
	
	

}
public function devolverOperacion($id){
		
	if($id==1){
		$k="Venta";
	}else if($id==2){
		$k="Arriendo";
	}else if($id==3){
		$k="Venta en verde";
	}else if($id==4){
		$k="Venta en blanco";
	}else if($id==5){
		$k="Ventas entrega inmediata";
	}else if($id==6){
		$k="División Usados";
	}
	
	return($k);
}
public function devolverTipoProp($id){
	if($id==1){
		$k="Casas";
	}else if($id==2){
		$k="Departamento";
	}else if($id==3){
		$k="Oficina";
	}else if($id==4){
		$k="Agrícola";
	}else if($id==5){
		$k="Bodega";
	}else if($id==6){
		$k="Comercial";
	}else if($id==7){
		$k="Estacionamiento";
	}else if($id==8){
		$k="Galpón";
	}else if($id==9){
		$k="Industrial";
	}else if($id==10){
		$k="Terreno";
	}else if($id==9){
		$k="Turistico";
	}else if($id==12){
		$k="Parcelas";
	}
	return($k);
}
public function formatoNumerico($num){
	$n=number_format($num, 0,",",".");
	return($n);
}

public function leerTitulo($id){
    $this->link=$this->conectar();
    $sql="select* from mm_coti_contenido where idContenido='".$id."'";
 
    $query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
    $row=mysqli_fetch_array($query);
    echo $row["titulo"];
}
public function devolverTipoCocina($id){
	$arrSel122=array(1=>"Cerrada",2=>"Americana",3=>"Integrada");
	return($arrSel122[$id]);
}
public function devolverRegion($idRegion){
	$this->link=$this->conectar();
	$sql="select* from mm_region where idRegion='".$idRegion."'";
	$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
	$row=mysqli_fetch_array($query);	 
	return(utf8_encode($row["nombre"]));
}
public function devolverCiudad($idCiudad){
	$this->link=$this->conectar();
	$sql="select* from mm_ciudad where idCiudad='".$idCiudad."'";
	$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
	$row=mysqli_fetch_array($query);	 
	return($row["ciudad"]);
}
public function devolverComuna($idComuna){
	$this->link=$this->conectar();
	$sql="select* from mm_comuna where idComuna='".$idComuna."'";
	$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
	$row=mysqli_fetch_array($query);	 
	return($row["nombre"]);
}
 public function formatoNumerico2($num){

	$n=number_format($num, 2,",",".");
	return($n);
}
public function setImagen($id){
	$this->link=$this->conectar();
	$sql="select* from mm_coti_contenido where idContenido='".$id."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	$foto=$r["foto"];
	return($foto);
}
public function leerContenido($id){
    $this->link=$this->conectar();
    $sql="select* from mm_coti_contenido where idContenido='".$id."'";
 
    $query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
    $row=mysqli_fetch_array($query);		
    echo "<div style='padding-top:10px;padding-bottom:50px;'>";
    echo "<span style='font-size:medium; text-align: justify;font-size:16px !important;line-height:28px;color:gray;'>";
    echo $row["texto"];
    echo "</span> ";
    echo "</div>";
 
}
public function monitorVisitas(){
        $this->monitor=new monitor();
        $this->monitor->desplegar();
}
public function leerImagen($id){
    $this->link=$this->conectar();
    if(isset($_GET["rd"])){
        $id2=$_GET["rd"];
    }
    $sql="select* from mm_coti_contenido where idContenido='".$id2."'";		 
    $query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
    $row=mysqli_fetch_array($query);
 
    $foto=$row["foto"];
    if(!empty($foto)){
    if($this->detDisp()=="movil"){
        echo "<div style='margin-top:40px;margin-bottom:40px;'>";					
        echo '<img src="./upload/'.$foto.'" class="img-fluid rounded float-start" style="max-width:100%; width:100%;"/>';

        echo "</div>";
    }else{
        echo "<div style='margin-top:90px;margin-bottom:40px;'>";		
        echo '<img src="./upload/'.$foto.'" class="img-fluid rounded float-start" style="max-width:100%; width:100%;"/>';
        echo "</div>";
    }
}
}
public function detMoz($id){
	$this->link=$this->conectar();
	$sql="select* from mm_propiedad where idProp='".$id."'";
	$q=mysqli_query($this->link,$sql);
	$row=mysqli_fetch_array($q);
	echo "<div style='margin-top:20px;'><h5>Descripción</h5></div>";
	echo "<div style='font-size:16px;'>";

	echo $row["descripcion"];
	echo "</div>";
	echo "<div><hr/></div>";
 
	
}
public function mapa1($id){
	 
	echo '<div id="detalle-mapa" style="width:100% !important;margin-left:0px; margin-right:0px;height:400px;margin-bottom:20px;"></div>';
	echo "<div style='margin-bottom:60px;'>&nbsp;</div>";
}
public function detalles($id){
	$this->link=$this->conectar();
	$sql="select* from mm_propiedad where idProp='".$id."'";
	$q=mysqli_query($this->link,$sql);
	$row=mysqli_fetch_array($q);
	
	echo '<div class="card" style="margin-top:20px; width:100%;">
	<div class="card-header" style="background-color: #000;
	color: white;
	font-size: 16px;">
	 Detalles de la Propiedad
	</div>
	<ul class="list-group list-group-flush">';
	  echo '<li class="list-group-item">';
	  echo "<div  style=' font-size:14px;'> <b>Operación :</b> ".$this->devolverOperacion($row["operacion"])."</div>";
	  echo '</li>';
	  echo '<li class="list-group-item">';
	  echo "<div style='margin-top:3px;  font-size:14px;margin-bottom:0px;'> <b>Tipo de Propiedad :</b> ".$this->devolverTipoProp($row["tipoProp"])."</div>";
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  echo "<div style='margin-top:3px; font-size:14px; margin-bottom:0px;'> <b>Dormitorios :</b> ";
		  if($row["dormitorios"]==1){
			  echo $row["dormitorios"]." dormitorio";
		  }else{
			  echo $row["dormitorios"]." dormitorios";
		  }			  
		  echo " </div>";
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  echo "
		  <div style='margin-top:3px; font-size:14px; margin-bottom:0px;'> <b>Baños :</b> ";
		  if($row["banos"]==1){
			echo $row["banos"]." baño ";
		  }else{
			echo $row["banos"]." baños ";
		  }
		  echo "</div>";
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  echo "<div style='margin-top:3px; font-size:14px; margin-bottom:0px;'> <b>Tipo de Cocina :</b> ".$this->devolverTipoCocina($row["tipoCocina"])."</div>";
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  echo "<div style='margin-top:3px; font-size:14px; margin-bottom:0px;'> <b>Logia :</b> ".$row["logia"]."</div>";
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  echo "<div style='margin-top:3px;  font-size:14px; margin-bottom:3px'> <b>Estacionamiento :</b> ".$this->devolverEstacionamientos($row["estacionamiento"])."</div>";
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  echo "<div style='margin-top:3px; font-size:14px; margin-bottom:3px'> <b>Bodega :</b>  ".$row["bodega"]."</div>";
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  	
	  if($row["tipoProp"]==1){	
		echo "<div style='margin-top:3px; font-size:14px; margin-bottom:3px;'> <b>Mt2 Totales :</b> ".$row["mt2Totales"]." m²</div>";
		}else{
		  echo "<div style='margin-top:3px; font-size:14px; margin-bottom:3px;'> <b>Mt2 útiles :</b> ".$row["mt2Totales"]." m² </div>";
		}
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  echo "<div style='margin-top:3px;  font-size:14px; margin-bottom:3px'> <b>Mt2 construidos :</b> ".$row["m2Construido"]." m²</div>";
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  echo "<div style='margin-top:3px;  font-size:14px; margin-bottom:3px'> <b>Conserjería :</b> ".$row["conser"]."</div>";
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  echo "<div style='margin-top:3px; font-size:14px; margin-bottom:3px;'> <b>Quincho :</b> ".$row["quincho"]."</div>";
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  echo "<div style='margin-top:3px;  font-size:14px; margin-bottom:3px'> <b>Áreas comunes :</b> ".$row["areasComunes"]."</div>";				
	  echo "</li>";

	  echo '<li class="list-group-item">';
	  echo "<div style='margin-top:3px;  font-size:14px; margin-bottom:3px'> <b>Piscina :</b> ".$row["piscina"]."</div>	";
	  echo "</li>";

	  echo '
	</ul>
  </div>';
} 
public function devolverCorredora($idProp){
	$sql="select* from mm_propiedad where idProp='".$idProp."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	return($r["idCorredora"]);
}
public function devolverDatosCorredora($id){
	$sql="select* from registro where idReg='".$id."'";
 
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	$d["nombre"]=$r["nombre"];
	$d["apellido"]=$r["apellido"];
	$d["tel"]=$r["telefono"];
	$d["email"]=$r["email"];
	$d["nomEmpresa"]=$r["nomEmpresa"];
	$d["foto"]=$r["rutaFoto"];
	return($d);	
}
public function contacto2($id=false){
    $d=$this->datosPag();
	$idCorredora=$this->devolverCorredora($id);
	$datos=$this->devolverDatosCorredora($idCorredora);
 if(isset($_POST["action2"])){
	if($idCorredora!=0){
		$emailDestino=$datos["email"];	
	}else{
		$emailDestino="mabenite@gmail.com";	
	}
	
     $nombre=htmlentities($_POST["nombre"]);
     $email=htmlentities($_POST["email"]);
     $tel=htmlentities($_POST["telefono"]);
     $msg=htmlentities($_POST["msg"]);

     
     if($this->emailContacto($nombre,$email,$tel,$msg,$emailDestino)){
         if($_GET["mod"]=="contacto"){
             echo '<script>';
             echo 'document.location="index.php?mod=contacto&msg=1";';
             echo '</script>';
                          
         }else{
            echo '<script>';
            echo 'document.location="index.php?mod=det&idProp='.$id.'&msg=1";';
            echo '</script>';
            
         }  
     }     
 }else{
                     if(isset($_GET["mod"])){
                        echo '<section id="contact-form" style="margin-top:0px;padding:0px;">';
                     }else{
                        echo '<section id="contact-form" style="margin-top:120px;padding:15px;">';
                     }
                    
                    
                 
                    echo '<div class="card" style="width:100%; margin:0px;padding:0px;">
					<div class="card-header">Solicitar Información </div>
                    <form method="post" name="form12" id="form12" action="">';
					if($idCorredora!=0){
					 	echo '<div class="row" style="padding-top:10px;">
						 <div class="col-md-4">';
						 if(empty($datos["foto"])){							
							echo '<img src="https://clickcorredores.cl/sinfoto.jpg" style="height:100px;width:100%;max-width:100%;">';	
						 }else{
							echo '<img src="./upload/'.$datos["foto"].'" style="height:100px;width:100%;max-width:100%;">';	
						 }
						 
						 
						 echo '</div>
						 <div class="col-md-8">
							 <div style="font-size:14px;"><i class="fas fa-user" aria-hidden="true"></i> '.$datos["nombre"]."&nbsp;".$datos["apellido"].'</div>';
							 if(!empty($datos["nomEmpresa"])){
								echo '<div style="font-size:14px;"><i class="fas fa-user" aria-hidden="true"></i> '.$datos["nomEmpresa"].'</div>';
							 }
							 echo '
							 <div style="margin-top:10px;font-size:14px;"><i class="fas fa-mobile" aria-hidden="true"></i>: '.$datos["tel"].'</div>
							 <div style="font-size:14px;margin-top:10px;"><i class="fas fa-envelope" aria-hidden="true"></i> '.$datos["email"].'</div>
							 <div><input type="hidden" id="action2" name="action2" value="true"></div>
						 </div> 
						 </div>';
 
						  echo "<div><hr/></div>";
							}
						  
                          
                          
                        if(isset($_GET["msg"])){
                            echo '<div class="alert alert-primary" role="alert">
                            Su mensaje se ha enviado con exito !!
                          </div>';
                        }
						echo '<div class="card-body" style="padding:10px;">';
                                echo '<div>                                    
                                    <input type="text" name="nombre" id="nombre" style="width:100% !important;" placeholder="Ingrese su nombre" class="form-control form-control-mb" title="* Ingrese su nombre" >
                                    <input type="hidden" name="action2" id="action2" value="true"/>
                                </div>
                                <div>                                    
                                    <input type="text" name="email" id="email" style="margin-top:10px;width:100% !important;" placeholder="Ingrese su email" class="form-control form-control-mb"  title="* Ingrese su dirección de Email">
                                </div>
                                <div>                                    
                                    <input type="text" name="telefono" id="telefono" style="margin-top:10px;width:100% !important;" placeholder="Ingrese su telefono" class="form-control form-control-mb">
                                </div>
                                <div>                                    
                                    <textarea  name="msg" id="msg" rows="8" cols="30" style="margin-top:10px;font-size:14px; width:100% !important;" class="form-control form-control-mb" title="* Please provide your message"></textarea>
                                </div>
                                <div>
                                    <button role="button" style="margin-top:30px;width:99% !important;padding-top:10px; padding-bottom:10px;" class="btn btn-primary2  btn-mb"  id="boton133"  name="boton133"><i class="far fa-hand-point-up"></i>  Enviar Mensaje</button>
                                 </div>
							</div>

                         </form>
                             </div>
                        </section>';
						echo '<script>
						$(document).ready(function(){
							$("#boton133").click(function(){
								var nombre=$("#nombre").val();
								var email=$("#email").val();
								var telefono=$("#telefono").val();
								var msg=$("#msg").val();
								if(nombre.length==0){
									alert("Ingrese su nombre");
									$("#nombre").focus();
								}else if(email.length==0){
									alert("Ingrese su email");
									$("#email").focus();
								}else if(msg.length==0){
									alert("Ingrese su mensaje");
									$("#msg").focus();
								}else{
									$("#form12").submit();
								}
								return(false);
							});

							return(false);
						});
						</script>';
 }
}



public function contacto222($id=false){
    $d=$this->datosPag();
	$idCorredora=$this->devolverCorredora($id);
	$datos=$this->devolverDatosCorredora($idCorredora);
 if(isset($_POST["action2"])){
	if($idCorredora!=0){
		$emailDestino=$datos["email"];	
	}else{
		$emailDestino="mabenite@gmail.com";	
	}

	$necesitas=htmlentities($_POST["necesitas"]);
	$comuna=$this->devolverComuna($_POST["comuna"]);
     $nombre=htmlentities($_POST["nombre"]);
     $email=htmlentities($_POST["email"]);
     $tel=htmlentities($_POST["telefono"]);
     $msg=htmlentities($_POST["msg"]);

     
     if($this->emailContacto($nombre,$email,$tel,$msg,$emailDestino,$necesitas,$comuna)){
         if($_GET["mod"]=="contacto"){
             echo '<script>';
             echo 'document.location="index.php?mod=contacto&msg=1";';
             echo '</script>';
                          
         }else{
            echo '<script>';
            echo 'document.location="index.php?mod=det&idProp='.$id.'&msg=1";';
            echo '</script>';
            
         }  
     }     
 }else{
                     if(isset($_GET["mod"])){
                        echo '<section id="contact-form" style="margin-top:0px;padding:0px;">';
                     }else{
                        echo '<section id="contact-form" style="margin-top:120px;padding:15px;">';
                     }
                    
                    
                 
                    echo '<div class="card" style="width:100%; margin:0px;padding:0px;">
					<div class="card-header">Necesitas un corredor</div>
                    <form method="post" name="form12" id="form12" action="">';
					if($idCorredora!=0){
					 	echo '<div class="row" style="padding-top:10px;">
						 <div class="col-md-4"><img src="./upload/'.$datos["foto"].'" style="height:100px;width:100%;max-width:100%;"></div>
						 <div class="col-md-8">
							 <div style="font-size:14px;"><i class="fas fa-user" aria-hidden="true"></i> '.$datos["nombre"]."&nbsp;".$datos["apellido"].'</div>';
							 if(!empty($datos["nomEmpresa"])){
								echo '<div style="font-size:14px;"><i class="fas fa-user" aria-hidden="true"></i> '.$datos["nomEmpresa"].'</div>';
							 }
							 echo '
							 <div style="margin-top:10px;font-size:14px;"><i class="fas fa-mobile" aria-hidden="true"></i>: '.$datos["tel"].'</div>
							 <div style="font-size:14px;margin-top:10px;"><i class="fas fa-envelope" aria-hidden="true"></i> '.$datos["email"].'</div>
							 <div><input type="hidden" id="action2" name="action2" value="true"></div>
						 </div> 
						 </div>';
 
						  echo "<div><hr/></div>";
							}
						  
                          
                          
                        if(isset($_GET["msg"])){
                            echo '<div class="alert alert-primary" role="alert">
                            Su mensaje se ha enviado con exito !!
                          </div>';
                        }
						echo '<div class="card-body" style="padding:10px;">';
                                echo '
								<div style="font-size:16px;">¿Necesitas vender o arrendar?</div>
								<div>                                    
                                <select name="necesitas" id="necesitas" class="form-select form-select-mb">
								<option value="0" selected>Seleccione opción</option>
								<option value="Arrendar">Arrendar</option>
								<option value="Vender">Vender</option>
								</select>
                                </div>
								<div style="font-size:16px;margin-top:5px;">Nombre</div>
								<div>                                    
                                    <input type="text" name="nombre" id="nombre" style="width:100% !important;" placeholder="Ingrese su nombre" class="form-control form-control-mb" title="* Ingrese su nombre" >
                                    <input type="hidden" name="action2" id="action2" value="true"/>
                                </div>
								<div style="font-size:16px;margin-top:5px;">Email</div>
                                <div>                                    
                                    <input type="text" name="email" id="email" style="margin-top:10px;width:100% !important;" placeholder="Ingrese su email" class="form-control form-control-mb"  title="* Ingrese su dirección de Email">
                                </div>
								<div style="font-size:16px;margin-top:5px;">Telefono</div>
                                <div>                                    
                                    <input type="text" name="telefono" id="telefono" style="margin-top:10px;width:100% !important;" placeholder="Ingrese su telefono" class="form-control form-control-mb">
                                </div>
								<div style="font-size:16px;margin-top:5px;">Comuna</div>
								<div>                                    
								<select style="margin-top:5px;margin-bottom:5px;" name="comuna" id="comuna" class="form-control form-control-mb" aria-label=".form-select-lg example">
								<option value="0" selected>Seleccione su comuna</option>
								<option value="4">Cerrillos</option><option value="5">Cerro Navia</option><option value="7">Conchalí</option><option value="9">El Bosque</option><option value="11">Estación Central</option><option value="12">Huechuraba</option><option value="13">Independencia</option><option value="15">La Cisterna</option><option value="16">La Florida</option><option value="17">La Granja</option><option value="18">La Pintana</option><option value="19">La Reina</option><option value="21">Las Condes</option><option value="22">Lo Barnechea</option><option value="23">Lo Espejo</option><option value="24">Lo Prado</option><option value="25">Macul</option><option value="26">Maipú</option><option value="29">Ñuñoa</option><option value="32">Pedro Aguirre Cerda</option><option value="34">Peñalolén</option><option value="36">Providencia</option><option value="37">Pudahuel</option><option value="39">Quilicura</option><option value="40">Quinta Normal</option><option value="41">Recoleta</option><option value="42">Renca</option><option value="44">San Joaquín</option><option value="46">San Miguel</option><option value="48">San Ramón</option><option value="49">Santiago</option><option value="52">Vitacura</option></select>
                                </div>

								<div style="font-size:16px;margin-top:5px;">Mensaje</div>
                                <div>                                    
                                    <textarea  name="msg" id="msg" rows="8" cols="30" style="margin-top:10px;font-size:14px; width:100% !important;" class="form-control form-control-mb" title="* Please provide your message"></textarea>
                                </div>
								
                                <div>
                                    <button role="button" style="margin-top:30px;width:99% !important;padding-top:10px; padding-bottom:10px;" class="btn btn-primary2  btn-mb"  id="boton1331"  name="boton1331"><i class="far fa-hand-point-up"></i>  Enviar Mensaje</button>
                                 </div>
							</div>

                         </form>
                             </div>
                        </section>';
						echo '<script>
						$(document).ready(function(){
							$("#boton133").click(function(){
								var nombre=$("#nombre").val();
								var email=$("#email").val();
								var telefono=$("#telefono").val();
								var msg=$("#msg").val();
								if(nombre.length==0){
									alert("Ingrese su nombre");
									$("#nombre").focus();
								}else if(email.length==0){
									alert("Ingrese su email");
									$("#email").focus();
								}else if(msg.length==0){
									alert("Ingrese su mensaje");
									$("#msg").focus();
								}else{
									$("#form12").submit();
								}
								return(false);
							});


							$("#boton1331").click(function(){
								var nombre=$("#nombre").val();
								var email=$("#email").val();
								var telefono=$("#telefono").val();
								var necesitas=$("#necesitas").val();
								var opcion=$("#opcion").val();
								var comuna=$("#comuna").val();
								var msg=$("#msg").val();
								
								if(necesitas==0){									
									alert("Selecciona si necesitas vender o arrendar");
									$("#necesitas").focus();
								}else if(nombre.length==0){
									alert("Ingrese su nombre");
									$("#nombre").focus();
								}else if(email.length==0){
									alert("Ingrese su email");
									$("#email").focus();
								}else if(telefono.length==0){
									alert("Ingrese el telefono");
									$("#telefono").focus();
								}else if(comuna==0){
									alert("Ingrese su comuna");
									$("#comuna").focus();
								}else if(msg.length==0){
									alert("Ingrese su mensaje");
									$("#msg").focus();
								}else{
									$("#form12").submit();
								}
								return(false);
							});


							return(false);
						});
						</script>';
 }
}

public function datosPag(){
	$this->link=$this->conectar();
	$sql="select* from mm_coti_datos";
	$q=mysqli_query($this->link,$sql);     
	while($r=mysqli_fetch_array($q)){
		$d["nombreEmpresa"]=$r["nombreEmpresa"];
		$d["nombrePag"]=$r["nombrePag"];
		 $d["celular"]=$r["celular"];
		$d["telefono"]=$r["telefono"]; 
		$d["email"]=$r["email"];
		$d["telefono1"]=$r["telefono1"]; 
		$d["email1"]=$r["email1"];
		$d["direccion"]=$r["direccion"];
		$d["facebook"]=$r["facebook"];
		$d["in"]=$r["instagram"];
		$d["twitter"]=$r["twitter"];
		$d["w"]=$r["whatsApp"];
		$d["i"]=$r["instagram"];
		$d["linkedin"]=$r["linkedin"];
		$d["youtube"]=$r["youtube"];
		$d["google"]=$r["google"];
		$d["wasap"]=$r["whatsApp"];
		$d["meta"]=$r["metatag"];
		$d["autor"]=$r["autor"];
		$d["titulo"]=$r["titulo"];
		 $d["meta"]=$r["metatag"];
		$d["des"]=$r["des"];
		$d["discado"]=$r["discado"];
		$d["enlaceIndicador"]=$r["enlaceIndicador"];			
	 }	 
	 return($d);
}
public function contacto(){	 	 
    echo '<div style="padding-left:30px; padding-right:20px;">	        			
                    <p class="texto">
                        Para contactarse con nosotros, complete el siguiente formulario con todos los datos requeridos, le responderemos lo m&aacute;s pronto posible.	
                    </p>        					        		
                    <div class="frm-contacto">
                        <form id="frm-contacto" name="frm-contacto" action="" method="post">
                            <input type="hidden" name="codigo" value="" />
                        <div>
                                        <label for="nombre">Nombre:</label> 
                                        <input type="text" name="nombre" id="nombre" class="input" style="width:100%;padding:18px;" tabindex="1" />
                        </div>
                        <div>
                                        <label for="email">E-mail:</label>
                                        <input type="text" name="email" id="email"  style="width:100%;padding:18px;" class="input" tabindex="2" />
                        </div>
                        <div>
                                        <label for="telefono">Tel&eacute;fono:</label>
                                        <input type="text" name="telefono" id="telefono"  style="width:100%;padding:18px;" class="input" tabindex="3" />
                            
                        </div>
                        <div>
                            
                                        <label for="mensaje=">Mensaje:</label>
                                        <textarea name="mensaje" id="mensaje" class="textarea"  style="width:100%;padding:18px;" tabindex="5"></textarea>
                        </div>
                        <div>
                                        <label for="math=">Seguridad:</label>
                                        <input type="hidden" name="num1" value="8" >
                                        <input type="hidden" name="num2" value="4" />
                                        <input type="text"  style="width:20%;padding:18px;" name="captchaImage" size="3" value="8 + 4" disabled="disabled" class="captcha" />
                                        =
                                        <input type="text"  style="width:20%;padding:18px;" name="math" id="math" size="2" maxlength="2" title="Ingrese el resultado correcto" class="captcha" tabindex="6" />
                            </div>
                            <div>
                                
                                        <input type="button" name="en" id="en" value="Enviar mensaje" class="btn btn-primary btn-lg" tabindex="7" />
                                </div>
                            
                        </form>
                    </div>
                    
                    <div class="datos-contacto">
                        
                        <div class="tbl-datos">
                                                                <strong>Tel&eacute;fono:</strong><br />
                                <span class="dato tel">+56 9 7108 3974</span>
                                <br />
                                                            
                                                        </div>
                        <br />
                        
                                                        <div class="tbl-datos">
                                <strong>E-mail:</strong><br />
                                <span class="dato"><span class="correo"></span></span>
                                <br /><br />
                            </div>
                                                
                    </div>
                </div>';
    
 
 
 
}
public function devolverCategoriaOpcion($id){
    $this->link=$this->conectar();
    $sql="select* from coti_categoria where idCategoria='".$id."'";
    $q=mysqli_query($this->link,$sql);
    $r=mysqli_fetch_array($q);
    return($r["nombre"]);
}
public function readPag($id){ 
    $this->link=$this->conectar();
     if(isset($_GET["mod"]) && $_GET["mod"]=="pag" && isset($_GET["rd"])){
        $rd=htmlentities($_GET["rd"]);
        $mod=htmlentities($_GET["mod"]);
        if(is_numeric($rd)){
            if(empty($rd) && empty($mod)){
                die("no Permitido");
                exit;
            }else{
                if(isset($_GET["m"])){						
                    $this->leerContenido($rd);
                }else{
                $sql="select * from mm_coti_contenido where idSubMenu='".$rd."'";				 
                $q=mysqli_query($this->link,$sql);					
                $total=mysqli_num_rows($q);
                 if($total==0){
                    echo "<div style='float: left;background: #fcfcfc;border: 1px solid #e0e0e0;width: 97%;padding: 20px;margin: 0 0 0 20px;font-size: medium; overflow: hidden;'>";
                    echo "<div style='margin-bottom:30px;color: #011A2C; padding: 0 0 5px 0;letter-spacing: 0;overflow: hidden;color:gray;'>Página no existe</div>";
                    echo "</div>";
                 }else{
                    $r=mysqli_fetch_array($q);
                    $id1=$r["idContenido"];		
                
                    $this->leerContenido($id1);
                 }
            }
            }
        }else{
            die("no permitido");
            exit;
        }
     }else{
         die("no permitido");
         exit;
     } 
      
}



}
?>