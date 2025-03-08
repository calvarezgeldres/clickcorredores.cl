<?php
 
require_once(dirname( __FILE__ )."/class.wpconeccion.php");
require_once(dirname( __FILE__ )."/class.phpmailer.php");
class dataAuto extends coneccion{
	public function __construct(){
		$this->conectar();
	}
	
	public function desplegarAutos(){
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<h2 style='color:#2370c8;'>Vehículos Destacados</h2>";
		echo "</div>";
		echo "</div>";
		if(isset($_GET["act"])){
			if(isset($_GET["idAuto"])){
				$idAuto=htmlentities($_GET["idAuto"]);
				
			}
			$this->verFichaAutos($idAuto);
		}else{
		$sql="select* from wp_fichaauto order by idAuto desc";
		$q=mysql_query($sql) or die(mysql_error());
		
		echo "<div class='row'>";
		echo '<section class="section-default">
   <div class="container">
      
      <div id="autosUsados">
         <div class="recent-car content-area">
            <div class="container">
               <div class="recent-car-content">
                  <div class="row margin-b-15">
                     <div class="col-md-12">
                        <div class="section-heading">
                           <!--<h2>Destacados</h2>-->
                        </div>
                     </div>
                  </div>
                  <div class="row" id="mssimilares">';
		while($r=mysql_fetch_array($q)){
			$idAuto=$r["idAuto"];
			$sql1="select* from wp_imagen where idAuto='".$idAuto."'";
			$q1=mysql_query($sql1);
			$r1=mysql_fetch_array($q1);
			$foto=$r1["foto"];
			
 
		 
		echo '  <div class="col-md-3  col-xs-6  caja-auto clearfix">
                        <div class="thumbnail car-box">
                           <div class="ribbon-wrapper">
                              <div class="ribbon-css ribbon-yellow">recién llegado</div>
                           </div>
                           <a href="index.php?idAuto='.$idAuto.'&act=det">
						   <img src="http://crediautomotora.cl/wp-content/upload/'.$foto.'" class="img-responsive" style="height:189px;" alt="">
</a>        
                           <div class="caption car-content">
                              <div style="height:50px;" class="header b-items-cars-one-info-header s-lineDownLeft navbar-scrolling">
                                 <h3><a href="index.php?idAuto='.$idAuto.'&act=det">';
								 	echo substr($r["titulo"],0,60);
								 echo '</a>                    <span style="font-weight:bold !important;">';
echo $r["precio"];
echo '								 </span>                </h3>
                              </div>
                              <div >
                                 <ul style="font-size:16px !important;">
                                    <li>Año: '.$r["ano"].'</li>
                                    <li>Combustible: ';
									if($r["combustible"]==0){
										echo "Diesel";
									}else{
										echo "Bencina";
									}
									echo '</li>
                                    <li>Transmisión: ';
									if($r["transmision"]==0){
										echo "Manual";
									}else{
										echo "Automatica";
									}
									echo '</li>
                                    <li>Kms: '.$r["km"].' Kms</li>
									
                                 </ul>
                              </div>
                              <div class="details-button">
							  <a href="index.php?idAuto='.$idAuto.'&act=det">VER MÁS</a></div>
                           </div>
                        </div>
                     </div>';
		
 
		
		
		}
		
		echo '                     
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>';
		echo "</div>";
		
	}
	}
	public function verFichaAutos($idAuto){
		$sql="select* from wp_fichaauto where idAuto='".$idAuto."'";
		$q=mysql_query($sql) or die(mysql_error());
		$r=mysql_fetch_array($q);
		
		$sql1="select* from wp_imagen where idAuto='".$idAuto."'";
		$q1=mysql_query($sql1);
		while($r1=mysql_fetch_array($q1)){
			$arch[]=$r1["foto"];
		}

		
		echo "<div class='row'>";
		echo "<div class='col-md-8'>";
		echo '  <div class="panel panel-default"  >
				<div class="panel-body">';
		echo "<div align='left'>";
		echo "<h3 style='color:#196cc5;'>".$r["titulo"]."</h3>";
		echo "</div>";
		echo "<div align='left'>";
		echo "Detalle del vehiculo";
		echo "</div>";
		echo "<div align='left'>";
		echo 'Color:'.$r["color"].' | Transmisión: ';
		if($r["transmision"]==0){
			echo "Manual";
		}else{
			echo "Automatica";
		}
		echo ' | Año:'.$r["ano"].' | Kms:'.$r["km"].' | Combustible:';
		if($r["combustible"]==0){
			echo "Diesel";
		}else{
			echo "Petroleo";
		}
		echo ' | Cilindrada:'.$r["cilindrada"];
		echo "</div>";
		echo "</div></div>";
		
		echo "<div>";
		echo '  <div class="panel panel-default"  >
				<div class="panel-body">';
				echo "<div>";
				
				echo "<div name='res' id='res'><img src='http://crediautomotora.cl/wp-content/upload/".$arch[0]."' class='img-responsive'/></div>";
				echo "</div>";
				echo "<div align='left' style='margin-top:10px;'>";
				echo "<table width='1%' style='border-width:0px; border-style:none; ' border=0>";
				$numCol=10;
				$k=0;
				echo "<tr>";
				foreach($arch as $clave=>$valor){
					$k++;

					echo "<td align='left'>";
					echo "<div id='k1' name='k1'><a href='#' id='f1' name='f1' alt='".$valor."'>
					<img src='http://crediautomotora.cl/wp-content/upload/".$valor."' class='img-thumbnails' style='margin:5px;width:120px; height:80px;'/>
					</a></div>";
					echo "</td>";
				
					if($k%$numCol==0){
						echo "</tr>";
					}
				}
				echo "</tr>";
				echo "</table>";
				echo "</div>";
				echo "</div>";
				echo "</div>";
				
		echo "</div>";
		 
		
 
		
		echo "<div align='left'>";
		echo '<h3>Equipamento</h3>';
		
		echo '  <div class="panel panel-default"  >
				<div class="panel-body">';
				
				
		echo "<div class='row'>";
		
		echo "<div class='col-md-6'>";
		
 
		if(empty($r["4x4"])){
			echo "<div><img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "4 Air bag</div>";
		}		
		
		
		if(empty($r["alzaVidriosElectricos"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Alzavidrios Eléctricos delanteros</div>";
		}
		
		
		
		if(empty($r["cierreCentralizado"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Cierre Centralizado</div>";
		}
		
		
		
		if(empty($r["controlCruzero"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Control Crucero</div>";
		}
		
		

		if(empty($r["cuero"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Cuero</div>";
		}
		
		
		
		if(empty($r["espejosElectricos"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Espejos Eléctricos</div>";
		}
		
		
		
		if(empty($r["neblineros"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Neblineros</div>";
		}
		
		
		
		if(empty($r["automatica"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Automática</div>";
		}
		
		
		echo "</div>";
		
		echo "<div class='col-md-6'>";
		
		
		if(empty($r["alarmas"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Alarmas</div>";
		}
		
		
		
		if(empty($r["asientosElectricos"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Asientos Eléctricos</div>";
		}
		
		
		
		if(empty($r["combustibleDisel"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Combustible Diesel</div>";
		}
		
		
		
		if(empty($r["controlesenManibrio"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Controles en Manubrio</div>";
		}
		
		
		
		if(empty($r["direccionAsistida"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "Dirección Asistida</div>";
		}
		
		
		
		if(empty($r["frenosAbs"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
					echo "Frenos ABS</div>";
		}

		
		
		if(empty($r["5puertas"])){
			echo "<div>";
			echo "<img src='http://crediautomotora.cl/wp-includes/visto.png'/>&nbsp;";
			echo "5 Puerta</div>";
		}
		
	 
		
		echo "</div>";
		
		echo "</div>";
		
		echo "</div></div>";
		echo "</div>";
		
		echo "</div>";
		
		
		
		
		echo "<div class='col-md-4' >";
		echo "<div>";
		echo '  <div class="panel panel-default"  >
				<div class="panel-body">';
				echo "<span style='font-size:18px; color:black;'>| PRECIO</span><br>&nbsp;<span style='font-size:30px; font-weight:bold; color:red;'>$ ".$this->formatoNumerico2($r["precio"])."</span>";
				echo "</div>";
				echo "</div>";
		echo "</div>";
		
		
			echo "<div>";
		echo '  <div class="panel panel-default"  >
				<div class="panel-body" align="left">';
	echo "<span style='font-size:18px; margin-bottom:20px; color:black;'>|DESCRIPCIÓN</span><br>&nbsp;<span style='font-size:14px; color:gray; line-height:25px; '>".nl2br($r["des"])."</span>";
				echo "</div>";
				echo "</div>";
		echo "</div>";
		
		
		echo "<div style='margin:15px;'>";
 
				$this->formularioContacto();
				 
		echo "</div>";
		
		
		echo "</div>";
		echo "</div>";
		
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<div><h2>Sugeridos</h2></div>";
		$this->sugeridos();
		echo "</div>";
		echo "</div>";
		
	}
	public function sugeridos(){
		$sql="select* from wp_fichaauto order by idAuto desc limit 0,4";
		$q=mysql_query($sql) or die(mysql_error());
		echo "<div class='row' align='left'>";
		echo '<section class="section-default">
   <div class="container">
      
      <div id="autosUsados">
         <div class="recent-car content-area">
            <div class="container">
               <div class="recent-car-content">
                  
                  <div class="row" id="mssimilares">';
		while($r=mysql_fetch_array($q)){
			$idAuto=$r["idAuto"];
			$sql1="select* from wp_imagen where idAuto='".$idAuto."'";
			$q1=mysql_query($sql1);
			$r1=mysql_fetch_array($q1);
			$foto=$r1["foto"];
			
 
		 
		echo '  <div class="col-md-3  col-xs-6  caja-auto clearfix">
                        <div class="thumbnail car-box">
                           <div class="ribbon-wrapper">
                              <div class="ribbon-css ribbon-yellow">recién llegado</div>
                           </div>
                           <a href="index.php?idAuto='.$idAuto.'&act=det">
						   <img src="http://crediautomotora.cl/wp-content/upload/'.$foto.'" class="img-responsive" style="height:189px;" alt="">
</a>        
                           <div class="caption car-content">
                              <div style="height:50px;" class="header b-items-cars-one-info-header s-lineDownLeft navbar-scrolling">
                                 <h3><a href="index.php?idAuto='.$idAuto.'&act=det">';
								 	echo substr($r["titulo"],0,60);
								 echo '</a>                    <span>';
echo $r["precio"];
echo '								 </span>                </h3>
                              </div>
                              <div class="car-tags">
                                 <ul style="font-size:16px !important;">
                                    <li>Año: '.$r["ano"].'</li>
                                    <li>Combustible: ';
									if($r["combustible"]==0){
										echo "Diesel";
									}else{
										echo "Bencina";
									}
									echo '</li>
                                    <li>Transmisión: ';
									if($r["transmision"]==0){
										echo "Manual";
									}else{
										echo "Automatica";
									}
									echo '</li>
                                    <li>Kms: '.$r["km"].' Kms</li>
									
                                 </ul>
                              </div>
                              <div class="details-button">
							  <a href="index.php?idAuto='.$idAuto.'&act=det">VER MÁS</a></div>
                           </div>
                        </div>
                     </div>';
		
 
		
		
		}
		
		echo '                     
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>';
		
	}	
	public function formatoNumerico2($num){
		$n=number_format($num, 2,",",".");
		return($n);
	}
	public function enviarCorreo($nombre,$rut,$email,$telefono,$renta,$antiguedad,$tipo,$interes){	
		$mail = new PHPMailer();
		$mail->From = "validacion@crediautomotora.cl";	
		$mail->IsSMTP();
		$mail->CharSet="UTF-8";
		$mail->SMTPSecure = 'tls';
		$mail->Host = 'mail.crediautomotora.cl';
		$mail->Port = 25;
		$mail->Username ="validacion@crediautomotora.cl";
		$mail->Password = 'guisan1717';
		$mail->SMTPAuth = true;		
		$mail->FromName =utf8_encode($nombre);
		$mail->SMTPDebug=1;	 
		$mail->IsHTML(true);
		$mail->Subject    =utf8_encode($nombre." te ha enviado un mensaje");
		$mail->AltBody    = "Formulario de Cotización";
		$email="crediautomotora@gmail.com"; 
		//$email="contacto@programacionwebchile.cl"; 
		$mail->AddAddress($email); // send the mail to yourself;
		$mensaje="<div><h3>Formulario de Cotización</h3></div>";

		$mensaje.="<div>Nombre:<b>".$nombre."</b></div>";
		$mensaje.="<div>R.U.T:<b>".$rut."</b></div>";
		$mensaje.="<div>Email:<b>".$email."</b></div>";
		$mensaje.="<div>Telefono:<b>".$telefono."</b></div>";
	 
		$mensaje.="<div>Renta:<b>".$renta."</b></div>";
		$mensaje.="<div>Antiguedad:<b>".$antiguedad."</b></div>";
		$mensaje.="<div>Tipo:<b>".$tipo."</b></div>";
		$mensaje.="<div>Interes:<b>".$interes."</b></div>";
	 
		$mensaje.="<br><br>";
		
	 
		$mensaje.="<div>Datos enviados desde el formulario de cotización</div>";
		
		$mail->Body    = $mensaje;
		if($mail->send()){
			return(true);
		}else{
			return(false);
		}
		$mail->ClearAllRecipients();
		
	}
	public function formularioContacto(){
	 
		if(isset($_POST["action"])){
 
			$nombre=htmlentities($_POST["nombre"]);
			$rut=htmlentities($_POST["rut"]);
			$email=htmlentities($_POST["email"]);
			$telefono=htmlentities($_POST["telefono"]);
			$renta=htmlentities($_POST["renta"]);
			$antiguedad=htmlentities($_POST["antiguedad"]);
			$tipo=htmlentities($_POST["tipo"]);
			$interes=htmlentities($_POST["interes"]);
			if(isset($_GET["idAuto"])){
				$idAuto=htmlentities($_GET["idAuto"]);
			}
			if($this->enviarCorreo($nombre,$rut,$email,$telefono,$renta,$antiguedad,$tipo,$interes)){
				header("location:index.php?idAuto=".$idAuto."&msg=1&act=det#.XBhtfdtKjIU");
				exit;
			}
	
		}
		if(isset($_GET["msg"])){
			echo "Tu cotización se ha enviado con exito!!!";
		}
		echo "<div class='row' align='left'>";
		echo "<div class='col-md-12' style='padding:10px;background-color:#f3f3f3;'>";
		echo "<form method='post' name='form1' id='form1' action=''/>";
		echo "<input type='hidden' name='action' id='action' value='true'>";
		echo "<div><span style='font-size:24px;'>Cotiza aqui tu Pre-aprobación por este vehículo</span></div>";
		echo "<div>&nbsp;</div>";
		echo "<div>";
		echo "Nombre Completo:";
		echo "<input type='text' name='nombre' id='nombre' value='' placeholder='Nombre' style='font-size:16px;' class='form-control input-sm'/>";
		echo "</div>";
		
		
		echo "<div style='margin-top:5px;'>";
		echo "R.U.T:";
		echo "<input type='text' name='rut' style='margin-top:5px;' id='rut' value='' style='font-size:16px;' placeholder='Rut' class='form-control input-sm'/>";
		echo "</div>";
		
		
		
		echo "<div style='margin-top:5px;'>";
		echo "Email:";
		echo "<input type='text' name='email' id='email' value='' style='margin-top:5px;' style='font-size:16px;' placeholder='Email' class='form-control input-sm'/>";
		echo "</div>";
		
		
			echo "<div style='margin-top:5px;'>";
		echo "Numero de Telefono:";
		echo "<input type='text' name='telefono' id='telefono' value='' style='margin-top:5px;' style='font-size:16px;' placeholder='Telefono' class='form-control input-sm'/>";
		echo "</div>";
		
		
		
			echo "<div style='margin-top:5px;'>";
		echo "Renta Liquida (Debe ser sobre $450.000):";
		echo "<input type='text' name='renta' id='renta' value='' style='margin-top:5px;' placeholder='Renta' class='form-control input-sm'/>";
		echo "</div>";
		
			echo "<div style='margin-top:5px;'>";
		echo "Antiguedad Laboral en Años:";
		echo "<select name='antiguedad' id='antiguedad' style='margin-top:5px;' class='form-control input-lg'>";
		echo "<option value='1 Año'>1 Año</option>";
		echo "<option value='2 Años'>2 Año</option>";
		echo "<option value='3 Años'>3 Año</option>";
		echo "<option value='4 Años'>4 Año</option>";
		echo "<option value='mas de 5 años'>mas de 5 años</option>";
		echo "</select>";
		echo "</div>";
		
		
		
		echo "<div style='margin-top:5px;'>";
		echo "Tipo de trabajo:";
		echo "<select name='tipo' id='tipo' style='margin-top:5px;' class='form-control input-lg'>";
		echo "<option value='Dependiente'>Dependiente</option>";
		echo "<option value='Independiente'>Independiente</option>";
		
		echo "</select>";
		echo "</div>";
		
		
		echo "<div style='margin-top:5px;'>";
		echo "Interes de tu compra:";
		echo "<select name='interes' id='interes' style='margin-top:5px;' class='form-control input-lg'>";
		echo "<option value='Inmediata'>Inmediata</option>";
		echo "<option value='En un tiempo mas'>En un tiempo mas</option>";
		
		echo "</select>";
		echo "</div>";
		
		
		echo "<div>";
		echo "<input type='button' style='margin-top:20px;width:100%;' name='btnEvaluar' id='btnEvaluar' class='btn btn-primary btn-lg' value='Evaluar mi Credito'/>";
		echo "</div>";
		echo "</form>";
		echo "</div></div>";
	}
}
?>