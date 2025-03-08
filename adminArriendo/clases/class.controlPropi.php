 <?php
 ob_start();
/*
Autor: Luis Olguin  - Programación Web Chile 2023 - luisalbchile21@gmail.com
Descripción : Sistema básico de administración de arriendo 
Fecha : 18/10/2023
Descripcion:cmsAdm v.1.1
  

*/
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
 
require_once("./clases/class.coneccion.php");
require_once("./clases/class.form1.php");
require_once("./clases/class.miniGrid.php");
require_once("./clases/class.msgBox.php");
require_once("./clases/class.monitor.php");
require_once("./clases/class.upload.php");
require_once("./clases/class.paginator.php");

require_once("./clases/class.generador.php");


class controlProp extends coneccion{     
public $link;
public $fecha;
public $pdf;


public function __construct(){
$this->link=$this->conectar();
$this->miForm=new form();
$this->paginator=new paginator(9,9);

$this->fecha = date("Y-n-j");  
}
public function devolverFecha(){
	setlocale(LC_TIME, 'es_ES'); // Establecer el idioma en español
	$fecha = strtotime(date("Y-m-d"));
	$fecha_formateada = strftime('%A %d de %B del %Y', $fecha);
	return("<span style='font-size:16px!important; margin-bottom:20px;'>".utf8_encode(ucfirst($fecha_formateada))."</span>");
}

public function detectarProximaFechaPago($fechaActual,$fechaInicioContrato,$frecuenciaPago,$fechaTerminoContrato){

 if (strtotime($fechaActual) > strtotime($fechaTerminoContrato)) {
    $proxFechaPago=0;
}else{
	switch ($frecuenciaPago) {
    case 1:
        $proxFechaPago = date('d-m-Y', strtotime($fechaInicioContrato . ' + 1 month'));
        break;
    case 2:
        $proxFechaPago = date('d-m-Y', strtotime($fechaInicioContrato . ' + 15 days'));
        break;
    case 3:
        $proxFechaPago = date('d-m-Y', strtotime($fechaInicioContrato . ' + 3 months'));
        break;
    default:
        $proxFechaPago = null;
	}
}

return($proxFechaPago);
}

public function verificaMoroso($fechaActual,$proxFechaPago){
	if (strtotime($fechaActual) > strtotime($proxFechaPago)) {
		return(true);
	}else{
		return(false);
	}
}
public function verificarFechaPago($fechaActual,$proxFechaPago){
	if ($fechaActual == $proxFechaPago) { 
		return(true);
	} else{	 
		return(false);
	}
}
public function verificaNotificacion($proxFechaPago,$numDias){
	// Calcular la fecha de notificación (5 días antes del próximo pago)
	$fechaNotificacion = date('d-m-Y', strtotime($proxFechaPago . ' - '.$numDias.' days'));
	echo "fecha de notificacion ".$numDias." dias ".$fechaNotificacion."<br>";
}
 
public function ingresarLiquidacion($idArriendo,$idArrendatario,$idProp,$idPropietario,$fechaPago,$montoPagar,$intervalo){
	$sql1="select COUNT(*) as countLiquidaciones FROM mm_liquidaciones WHERE idArriendo='".$idArriendo."' and fecha = ".strtotime($fechaPago).";";
	 
	$q1=mysqli_query($this->link,$sql1);
	$r1=mysqli_fetch_array($q1);

	if($r1["countLiquidaciones"]==0){
	$razon="Pago del arriendo con fecha ".$fechaPago;
	$sql="insert into mm_liquidaciones (
		idArriendo,
		idArrendatario,
		idProp,
		racon,
		idPropietario,
		fecha,
		montoPagar		
	) ";
	$sql.=" values ('".$idArriendo."',
	'".$idArrendatario."',
	'".$idProp."',
	'".$razon."',
	'".$idPropietario."',
	'".strtotime($fechaPago)."',
	'".$montoPagar."'
	)";
	
	mysqli_query($this->link,$sql);
	
	//$nuevaFechaPago = $this->nuevaFechaPago($fechaPago,$intervalo);
	// ingresar fecha de pago tabla pagos
	$sql2="insert into fechaDePago (idArriendo, fechaPago) VALUES ('".$idArriendo."','".strtotime($fechaPago)."')";
	mysqli_query($this->link,$sql2);
	}
	return(true); 
}
public function nuevaFechaPago($fechaPago1,$frecuenciaPago){	
	switch ($frecuenciaPago) {
		case 1:
			$intervalo=30;
			break;
		case 2:
			$intervalo=15;
			break;
		case 3:
			$intervalo=90;
			break;
		default:
			$intervalo=365;
		}
	$fechaPago = $fechaPago1; // Cambia esto por la última fecha de pago registrada
	$intervaloDias = $intervalo; // Puedes ajustar el intervalo de días según tu contrato
	// Obtener la próxima fecha de pago
	$nuevaFechaPago = date('d-m-Y', strtotime($fechaPago));
	$nuevaFechaPago = date('d-m-Y', strtotime("$nuevaFechaPago +$intervaloDias days"));
	// Asegurarse de que la fecha sea válida para el mes actual
	while (date('d', strtotime($nuevaFechaPago)) !== '01') {
    	$nuevaFechaPago = date('d-m-Y', strtotime("$nuevaFechaPago +1 day"));
	}
	return($nuevaFechaPago);
}
public function leeArriendos(){
	$sql="select* from mm_arriendos";
	$q=mysqli_query($this->link,$sql);
	while($r=mysqli_fetch_array($q)){ 
	// datos generales	
	$idArriendo=$r["idArriendo"];
	$idArrendatario=$r["idArrendatarios"];
	$idProp=$r["idProp"];
	$idPropietario=$r["idPropietario"];
	$montoPagar=$r["montoArriendo"];
	$razon="";
	$saldo="";
	//$fechaActual=date("d-m-Y");
	$fechaActual="02-12-2023";	
	$sql4="select* from fechaDePago where idArriendo='".$idArriendo."'";
	$q4=mysqli_query($this->link,$sql4);
	$r4=mysqli_fetch_array($q4);
	if(mysqli_num_rows($q4)==0){
		$fechaInicioContrato=date("d-m-Y",$r["fechaInicio"]);
	}else{
		$fechaInicioContrato=date("d-m-Y",$r4["fechaPago"]);
	}
	$frecuenciaPago=$r["frecuencia_pago"];
	$fechaTerminoContrato=date("d-m-Y",$r["fechaCancelacion"]);
	$fechaDePago=$this->detectarProximaFechaPago($fechaActual,$fechaInicioContrato,$frecuenciaPago,$fechaTerminoContrato);
	echo "inicio contrato ".$fechaInicioContrato."<br>";
	echo "fechaActual ".$fechaActual."<BR>";
	echo "fecha de pago ".$fechaDePago."<br>";	

	if($this->verificaMoroso($fechaActual,$fechaDePago)){
		/* 0=activo 1=moroso 2=pendiente a pago	*/
		$this->actualizaEstadoArriendo($idArriendo,1);
		return(false);
	}else{
		if($this->verificarFechaPago($fechaActual,$fechaDePago)){
			if($this->ingresarLiquidacion($idArriendo,$idArrendatario,$idProp,$idPropietario,$fechaDePago,$montoPagar,$frecuenciaPago)){				
				if($this->actualizaEstadoArriendo($idArriendo,2)){
					// aqui genera loquidacion
					echo "<br>liquidacion generada<br>";
					return(true);
				}				
			}
		}else{
			if($this->verificaNotificacion($fechaDePago,5)){
					echo "se notifica 5 dias antes <br>";
					return(false);
			}else{
				echo "no es fecha de pago ni notificacion<br>";
				return(false);
			}			
		}		
	}
	echo "<br><hr/><br/>";
	}	
}
public function devolverEstadoArriendo($id){
	if($id==0){
		$k="Activo";
	}else if($id==1){
		$k="Moroso";
	}else{
		$k="Pendiente";
	}
	return($k);
}
public function tablaClasificaArriendos($estado){
	// 
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==2){$this->msgBox=new msgBox(1,"Agente ha sido eliminado con exito!!");}}
	if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
 
	if($op=="editar"){$id=htmlentities($_GET["idq"]);
		$this->modificar_mm_arriendos($id);
	}else if($op=="borrar"){
		$id=htmlentities($_GET["idq"]);
		

		$sql3="delete from mm_arriendos where idArriendo='".$id."' ";	
		mysqli_query($this->link,$sql3);
			echo "<script>document.location='panel.php?m=1&msg=1';</script>";
		exit;
	}else{
		$campoIndice="idArriendo";
		$index="panel.php?m=1";
		$nomTabla="mm_arriendos";
		$campoFoto=array("ruta"=>true);
		//$nomTablaFoto="mm_cape_fotos";

		$sql="SELECT 
		mm_arriendos.*, 
		mm_propietarios.*, 
		mm_propiedad1.*,
		mm_liquidacionArriendo.* 
	FROM 
		mm_arriendos 
	INNER JOIN 
		mm_propietarios ON mm_arriendos.idPropietarios = mm_propietarios.idProp 
	INNER JOIN 
		mm_liquidacionArriendo ON mm_arriendos.idArriendo = mm_liquidacionArriendo.idArriendo 
	INNER JOIN 
		mm_propiedad1 ON mm_propiedad1.idprop = mm_arriendos.idprop 
	WHERE 
		mm_liquidacionArriendo.estado = ".$estado."
	GROUP BY 
		mm_arriendos.idArriendo	
	";
	 
		$campos=array('Ficha'=>'propiedad','titulo'=>'Propiedad','Arrendatario'=>'Arrendatarios','fechaLiqui'=>'Liquidación','Precio'=>'montoArriendo');
		
		$tamCol=array(8,30,15,0,10,10);
		$campoFoto=array("foto"=>true);
		$filtrar1=array('buscador'=>'nombrePack');
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_arriendos";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
		$grid->desplegarDatosArriendo();
	 
		return($sql);
	}
}

public function modificarPass(){
		
	
	 
	if(isset($_POST["action"])){
		$idUser=$_SESSION["auth"]["idReg"];
		$currentPassword = $_POST["currentPassword"];
		$newPassword = $_POST["newPassword"];
 
		$sql="update mm_adminProp set pass='".$newPassword."' where idReg='".$idUser."'";   
       
		mysqli_query($this->link, $sql);
			echo "<script>document.location='panel.php?mod=panel&op=10&msg=1';</script>";
		exit;            
	}
	echo '<div align="left" style="margin-top:20px;">';
	if(isset($_GET["msg"])){
		echo '<div class="alert alert-primary" role="alert">
		Contraseña ha sido modificada con exito
	  </div>';
	}
	echo '<form method="post" name="form1" id="form1">
	<div class="container">
	  <div class="row">
		<div class="col-md-6" align="left">
		  <h5>Modificar Contraseña</h5>
		  <form method="POST" action="actualizar_contrasena.php">
			<input type="hidden" name="action" value="updatePassword">
			<div class="mb-3">
			  <label for="currentPassword" class="form-label">Contraseña Actual</label>
			  <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
			</div>
			<div class="mb-3">
			  <label for="newPassword" class="form-label">Nueva Contraseña</label>
			  <input type="password" class="form-control" id="newPassword" name="newPassword" required>
			</div>
			<div class="mb-3">
			  <label for="confirmPassword" class="form-label">Confirmar Nueva Contraseña</label>
			  <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
			</div>
			<button type="submit" class="btn btn-primary">Modificar</button>
		  </form>
		</div>
	  </div>
	</div>
  </form>
  </div>
  ';
}
public function editarCuentaAdmin(){
 
 
		$idReg = $_SESSION["auth"]["idReg"];
		
	
		$sql = "SELECT * FROM mm_adminProp WHERE idReg='".$idReg."'";
 
		$q = mysqli_query($this->link, $sql);
	
		echo "<form method='post' enctype='multipart/form-data' name='form1' id='form1'>";
		$_SESSION["auth"]["nick"] = $r["nombre"];
		if (isset($_GET["msg"])) {
			echo '<div class="alert alert-primary" role="alert">Cambios guardados con éxito !!!</div>';
		}
		$r = mysqli_fetch_array($q);
	
		echo "<div class='container' style='margin-top:20px;'>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<div style='margin-bottom:30px;'><h5><i class='fas fa-edit'></i>&nbsp;Editar Perfil de tu corredora</h5></div>";
		echo "</div>";
		echo "</div>";
		echo "<div class='row'>";
		echo  "<div class='col-md-7'>";
		
	
        
		$this->miForm->addText("nomEmpresa",350,"Nombre de su empresa","Nombre de su empresa:",$r["nomEmpresa"],false,0);
		echo "<div>Registrado como </div>";
		echo "<div>";
		echo '<select   style="margin-top:5px;margin-bottom:5px;" name="tipo" id="tipo" class="form-select form-select-mb" aria-label=".form-select-lg example">';
		echo "<option value='".$r["tipo"]."'>".$this->devolverTipo($r["tipo"])."</option>";
		echo '<option value="1">Particular</option>		
				 <option value="2">Inmobiliaria</option>		
				 <option value="3">Corredora</option>		
				</select>';
		echo "</div>";

        $this->miForm->addText("nombre",350,"Nombre","Nombre:",$r["nombre"],false,0);
		$this->miForm->addText("apellido",350,"Apellido","Apellido:",$r["apellido"],false,0);
		
 
		$this->miForm->addEmail("email",350,"Email","Email",$r["email"],false,0);

		echo "<div>Región</div>";
		echo "<div>";
		echo '<select  class="form-select form-select-mb" name="region5x" id="region5x"  style="margin-top:5px;margin-bottom:5px;" aria-label=".form-select-lg example">';
		if(!empty($r["region"])){
			echo '<option value="'.$r["region"].'" selected="selected">'.utf8_encode($this->devolverRegion($r["region"])).'</option>';
		}else{
			echo '<option value="0" selected="selected">Región</option>';
		}
		
		$sql="select* from mm_region order by idRegion asc";
		$q1=mysqli_query($this->link,$sql);
		while($r1=mysqli_fetch_array($q1)){
			   echo '<option value="'.$r1["idRegion"].'">'.utf8_encode($r1["nombre"]).'</option>';
		}			   
		echo '</select>';
		echo "</div>";

		echo "<div>Ciudad</div>";
		echo "<div>";
		echo '<select name="ciudad3"  style="margin-top:5px;margin-bottom:5px;" id="ciudad3" class="form-select form-select-mb" aria-label=".form-select-lg example">';
		if(!empty($r["ciudad"])){
			echo '<option value="'.$r["ciudad"].'" selected="selected">'.$this->devolverCiudad($r["ciudad"]).'</option>';
		}else{
			echo '<option value="0" selected="selected">Ciudad</option>';
		}
	   	
	   		  echo '</select>';
		echo "</div>";


		echo "<div>Comuna</div>";
		echo "<div>";
 
		echo '<select   style="margin-top:5px;margin-bottom:5px;" name="comuna3" id="comuna3" class="form-select form-select-mb" aria-label=".form-select-lg example">';

		if(!empty($r["comuna"])){
			echo '<option value="'.$r["comuna"].'" selected="selected">'.$this->devolverComuna($r["comuna"]).'</option>';
		}else{
			echo '<option value="0" selected="selected">Comuna</option>';
		}

				echo '</select>';
		echo "</div>";

		$this->miForm->addTelefono("telefono",350,"Telefono","Telefono:",$r["telefono"],false,0);
		$this->miForm->addCelular("celular",350,"Celular","Celular:",$r["celular"],false,0);
		$this->miForm->addWasap("wasap",350,"WhatsApp","WhatsApp:",$r["wasap"],false,0);
		$this->miForm->addWebSite("website",350,"Sitio Web","Sitio Web:",$r["website"],false,0);

 
		$this->miForm->addFace("facebook",350,"Facebook","Facebook:",$r["facebook"],false,0);
		$this->miForm->addTwitter("twitter",350,"Twitter","Twitter:",$r["twitter"],false,0);
		$this->miForm->addInstagram("instagram",350,"Instagram","Instagram:",$r["instagram"],false,0);
		$this->miForm->addLinkedin("linkedin",350,"Linkedin","Linkedin",$r["linkedin"],false,0);


	//	$this->miForm->addpassword("contra",350,"Contrase�a",utf8_encode("contrase�a"),$r["pass"],false,0);
	//	$this->miForm->addPassword("confirmar",350,utf8_decode("Confirmar contraseña"),utf8_decode("Confirmar contraseña:"),false);
	
		$this->miForm->addHidden("action","true");
		echo "<div>&nbsp;</div>";
		$this->miForm->addButton("Enviar","Guardar Cambios",false,false);
        echo "<div>&nbsp;</div>";
        echo "<div>&nbsp;</div>";
        echo "<div>&nbsp;</div>";
	
		echo "</div>";
	
		echo "<div class='col-md-5'>";
		echo "<div align='center' style='padding-left:20px; padding-right:20px;'>";
		echo "<div class='card' style='width:35%;'>";
		if (!empty($r["rutaFoto"])) {
			echo "<img style='width;100%;' src='./upload/".$r["rutaFoto"]."'/>";
		} else {			
			echo "<img src='https://www.planwebinmobiliario.cl/demoProp/imagen/nouser.jpg'/>";
		}
		echo "</div>";
		echo "</div>";
		echo "<div>";
		$this->miForm->addFile(1, "Foto Perfil :", $editarImagen);
		echo "</div>";
		echo "<div>250px x 250px</div>";
		echo "</div>";
	
		echo "</form>";
	
		$this->miForm->procesar();
		if ($this->miForm->procesarImagen(250, 150)) {
			$arch = $this->miForm->getDataArch();  		               
		}	 	  
		$post = $this->miForm->getDataPost();	 
		if ($_POST["action"]) {
			$d["tipo"] = htmlentities($_POST["tipo"]);
			$d["nombre"] = htmlentities($_POST["nombre"]);
			$d["nomEmpresa"] = htmlentities($_POST["nomEmpresa"]);
			$d["apellido"] = htmlentities($_POST["apellido"]);
			$d["email"] = htmlentities($_POST["email"]);
			$d["telefono"] = htmlentities($_POST["telefono"]);
			$d["celular"] = htmlentities($_POST["celular"]);
			$d["wasap"] = htmlentities($_POST["wasap"]);
			$d["website"] = htmlentities($_POST["website"]);
			$d["pass"] = htmlentities($_POST["contra"]);
			$d["condicion"] = htmlentities($_POST["condiciones"]);
			$d["w"] = htmlentities($_POST["w"]);
			$d["region"] = $_POST["region5x"];
			$d["ciudad"] = $_POST["ciudad3"];
			$d["comuna"] = $_POST["comuna3"];
			$d["facebook"] = htmlentities($_POST["facebook"]);
			$d["twitter"] = htmlentities($_POST["twitter"]);
			$d["instagram"] = htmlentities($_POST["instagram"]);
			$d["linkedin"] = htmlentities($_POST["linkedin"]);
			$d["direccion"] = htmlentities($_POST["addr"]);
			$cor = $_POST["lat"] . "," . $_POST["lon"];
		
			$sql = "UPDATE mm_adminProp SET 
						tipo = '".$d["tipo"]."',
						nomEmpresa = '".$d["nomEmpresa"]."',
						nombre = '".$d["nombre"]."',
						apellido = '".$d["apellido"]."',
						email = '".$d["email"]."',
						celular = '".$d["celular"]."',
						wasap = '".$d["wasap"]."',
						website = '".$d["website"]."',
						region = '".$d["region"]."',
						ciudad = '".$d["ciudad"]."',
						comuna = '".$d["comuna"]."',
						facebook = '".$d["facebook"]."',
						twitter = '".$d["twitter"]."',
						instagram = '".$d["instagram"]."',
						linkedin = '".$d["linkedin"]."',
						direccion = '".$d["direccion"]."',
						cordenadas = '".$cor."',
						telefono = '".$d["telefono"]."'";
		
			if (!empty($_POST["contra"])) {
				$sql .= ", pass = '".md5($d["contra"])."'";
			}
			
			if (!empty($arch[0])) {
				$sql .= ", rutaFoto = '".$arch[0]."'";
			}
		
			$sql .= " WHERE idReg = '".$idReg."'";
		 
			mysqli_query($this->link, $sql) or die(mysqli_error($this->link));
				echo "<script>document.location='panel.php?mod=panel&op=9&msg=1';</script>";
			exit;
		}
		
		$this->miForm->cerrarForm();
		mysqli_free_result($q);
 
	

}
public function tablaPagos(){
	
	$this->link=$this->conectar();	 
	
	$idArrendatario=$_SESSION["auth"]["idUser"];
	  if(isset($_GET["msg"])){
		  $msg=htmlentities($_GET["msg"]);
	 
		 if($msg==1){
		 
		 }else{
			echo '<div class="alert alert-primary" role="alert">
			Cambios se han realizado con exito
		  </div>';
		 }
	  }
 

	  
	 
	 
 
	$index="panel.php?op=36";
	 


	$campos=array(
				  "fechaLiqui"=>"fechaLiqui",
				  "estado"=>"estado"

	); 
	$tamCol=array(55,30,10,15,10);
	
	
				 
	
   
	$campoFoto=array("ruta"=>true);        
	$campoIndice="idProp";            
	$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);
   
	$tabla="mm_propiedad1";

	$grid->asignarCampos($campos,$tamCol,$sql,$tabla);
	 
	if(isset($_GET["msg"])){
		echo '<div class="alert alert-primary" role="alert">
		Propiedad eliminada con exito
	  </div>';
	}
	 

	$tipo=$_SESSION["auth"]["tipo"];
	if(isset($_POST["action"])){
		$col=$_POST["columna"];
		$buscador=$_POST["buscador"];
		 
	} 
	if(isset($_GET["idProp"])){
		$idProp=htmlentities($_GET["idProp"]);
	}
	$idArrendatario=$_SESSION["auth"]["idReg"];
	$sql = "SELECT * FROM mm_liquidacionArriendo WHERE idArrendatario = '".$idArrendatario."' and idProp='".$idProp."' order by idLiquidacion desc";
	
	$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
	$this->controles("Historial de pagos");
	echo '<div style="margin-bottom:5px;"><a href="panelArrendatario.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a></div>';
	$grid->desplegarPagos();

 
	 
	return(true); 
 }
public function tablaPropiedades(){ 
 
	$this->link=$this->conectar();	 
	
	$idCorredora=$_SESSION["auth"]["idUser"];
	  if(isset($_GET["msg"])){
		  $msg=htmlentities($_GET["msg"]);
	 
		 if($msg==1){
		 
		 }else{
			echo '<div class="alert alert-primary" role="alert">
			Cambios se han realizado con exito
		  </div>';
		 }
	  }
 

	  
	 
	 
	if($tipo=="admin"){
		$index="panel.php?op=1";
	}else{
		$index="panelPropietario.php?op=1";
	}
		
	


	$campos=array(
				  "titulo"=>"Titulo",
				  "operacion"=>"Operacion",
				  "tipoProp"=>"Tipo",        			  
				  "Estado"=>"estado",
				  "precio"=>"Precio" 

	); 
	$tamCol=array(55,30,10,15,10);
	
	
				 
	
   
	$campoFoto=array("ruta"=>true);        
	$campoIndice="idProp";            
	$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);
   
	$tabla="mm_propiedad1";

	$grid->asignarCampos($campos,$tamCol,$sql,$tabla);
	 
	if(isset($_GET["msg"])){
		echo '<div class="alert alert-primary" role="alert">
		Propiedad eliminada con exito
	  </div>';
	}
	 

	$tipo=$_SESSION["auth"]["tipo"];
	if(isset($_POST["action"])){
		$col=$_POST["columna"];
		$buscador=$_POST["buscador"];
		 
	} 
	$idPropi=$_SESSION["auth"]["idReg"];
	$sql="SELECT * FROM `mm_propiedad1` where idPropietario='".$idPropi."' order by idProp desc";
 
	$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
	 
	
	$grid->desplegarDatosPortada();

 
	 
	return(true); 
 }

 public function tablaPropiedadesArrendadas(){ 

	$this->link=$this->conectar();	 
	
	$idCorredora=$_SESSION["auth"]["idUser"];
	  if(isset($_GET["msg"])){
		  $msg=htmlentities($_GET["msg"]);
	 
		 if($msg==1){
		 
		 }else{
			echo '<div class="alert alert-primary" role="alert">
			Cambios se han realizado con exito
		  </div>';
		 }
	  }
 

	  
	 
	 
	  
	 


	
	$tamCol=array(65,40,2,2);
	
	
				 
	
   
	$campoFoto=array("ruta"=>true);        
	$campoIndice="idArriendo";            
	$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);
   
	$tabla="mm_arriendos";

	$grid->asignarCampos($campos,$tamCol,$sql,$tabla);
	 
	if(isset($_GET["msg"])){
		echo '<div class="alert alert-primary" role="alert">
		Propiedad eliminada con exito
	  </div>';
	}
	 

	$tipo=$_SESSION["auth"]["tipo"];
	if(isset($_POST["action"])){
		$col=$_POST["columna"];
		$buscador=$_POST["buscador"];
		 
	} 
	$campos=array(
		"direccionProp"=>"direccionProp",
		"montoArriendo"=>"montoArriendo"
); 
	$idArrendatario=$_SESSION["auth"]["idReg"];
	$sql="SELECT mm_propiedad1.idProp, mm_propiedad1.direccionProp, mm_arriendos.montoArriendo FROM mm_arriendos JOIN mm_propiedad1 ON mm_arriendos.idProp = mm_propiedad1.idProp WHERE mm_arriendos.idArrendatarios ='".$idArrendatario."'";
 
	$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
	if(isset($_GET["op"])){
		$this->controles("Propiedades Arrendadas");
	}
	
	
	$grid->desplegarDatosArren();

 
	 
	return(true); 
 }


public function liquidacionPropi($idPropi){
echo '<ul class="nav nav-tabs" id="myTab" role="tablist">
<li class="nav-item" role="presentation">
  <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Tus Liquidaciones</button>
</li>';
if(isset($_GET["ks"])){
	if(isset($_GET["act"])){
		echo '<a href="panelPropietario.php?ks=1&idProp='.$idPropi.'" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver a Liquidaciones</a>';
	}else{
		echo '<a href="panelPropietario.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>';
	}
	
}


echo '</ul>
<div class="tab-content" id="myTabContent">
<div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">';
if(isset($_GET["ks"])){
	$idProp=$_GET["idProp"];
	if(isset($_GET["act"])){
		$idLiq=$_GET["idLiq"];
		$this->tablaCobros($idLiq);
	}else{
		$this->tablaLiquidacionesPropiedad($idProp);
	}
	
}else{
	$this->tablaPropiedades($idPropi);
}


echo '</div>

</div>';

}
public function clasificaArriendos(){
	
	echo '<ul class="nav nav-tabs" id="myTabs" role="tablist">
	<li class="nav-item" role="presentation">
	  <a class="nav-link active" id="arriendos-morosos-tab" data-bs-toggle="tab" href="#arriendos-morosos" role="tab" aria-controls="arriendos-morosos" aria-selected="true">Arriendos Morosos</a>
	</li>
	<li class="nav-item" role="presentation">
	  <a class="nav-link" id="propiedades-pendientes-tab" data-bs-toggle="tab" href="#propiedades-pendientes" role="tab" aria-controls="propiedades-pendientes" aria-selected="false">Propiedades Pendientes</a>
	</li>
	<li class="nav-item" role="presentation">
	  <a class="nav-link" id="propiedades-liquidadas-tab" data-bs-toggle="tab" href="#propiedades-liquidadas" role="tab" aria-controls="propiedades-liquidadas" aria-selected="false">Propiedades Liquidadas</a>
	</li>
  </ul>
  
  <div class="tab-content" id="myTabsContent">
	<div class="tab-pane fade show active" id="arriendos-morosos" role="tabpanel" aria-labelledby="arriendos-morosos-tab">
		<div style="margin-top:20px;margin-bottom:20px;"><h5>Arriendos morosos</h5></div>
		<div>';
		$this->tablaClasificaArriendos(3);
		echo '</div>
	</div>
	<div class="tab-pane fade" id="propiedades-pendientes" role="tabpanel" aria-labelledby="propiedades-pendientes-tab">
		<div style="margin-top:20px;margin-bottom:20px;"><h5>Propiedades Pendientes</h5></div>
		<div>';
		$this->tablaClasificaArriendos(0);
		echo '</div>
	</div>
	<div class="tab-pane fade" id="propiedades-liquidadas" role="tabpanel" aria-labelledby="propiedades-liquidadas-tab">
	<div style="margin-top:20px;margin-bottom:20px;"><h5>Propiedades Liquidadas</h5></div>
	<div>';
		$this->tablaClasificaArriendos(0);
		echo '</div>
	</div>
  </div>';
}
public function actualizaEstadoArriendo($idArriendo,$estado){
	$sql3="update from mm_arriendos set estadoArriendo='".$estado."' where idArriendo='".$idArriendo."'";
	mysqli_query($this->link,$sql3);
	return(true);
}
public function accionaLiquidacion(){
	$this->leeArriendos();		 
}
public function generarLiquidacion(){
	echo "<div class='row'>";
	echo "<div class='col-md-12'>";
	echo "<h4>Liquidaciones</h4>";
	if(isset($_GET["msg"]) && $_GET["msg"]==1){
		echo '<div class="alert alert-primary" role="alert">
		Se ha generado las liquidaciones con exito !!
	  </div>';
	}
	echo "<div>";
	$this->tablaLiquidaciones();
	echo "</div>";
	echo "</div>";
	echo "</div>";	 
}
public function devolverTipo($id){
	if($id==1){
		return("propietario");
	}else{
		return("corredor");
	}
}
public function indicador(){	 
	$apiUrl = 'http://mindicador.cl:80/api';
	if ( ini_get('allow_url_fopen') ) {
		$json = file_get_contents($apiUrl);
	}else{
		$curl = curl_init($apiUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($curl);
		curl_close($curl);
	} 
 
	$d = json_decode($json);
 
 $tipo=$_SESSION["auth"]["tipo"];
 if($tipo=="propi"){
	echo "<div style='margin-bottom:10px;margin-top:10px;'>Bienvenido <b>".$_SESSION["auth"]["usuario"]."</b> a tu espacio de propietario | ".$this->devolverFecha()."</div>";
 }else if($tipo=="agente"){
	echo "<div style='margin-bottom:10px;margin-top:10px;'>Bienvenido <b>".$_SESSION["auth"]["usuario"]."</b> a tu espacio de agente inmobiliario | ".$this->devolverFecha()."</div>";
 }else if($tipo=="arrendatario"){
	echo "<div style='margin-bottom:10px;margin-top:10px;'>Bienvenido <b>".$_SESSION["auth"]["usuario"]."</b> a tu espacio de arrendatario | ".$this->devolverFecha()."</div>";
 }else{
	echo "<div style='margin-bottom:10px;margin-top:10px;'>Bienvenido <b>".$_SESSION["auth"]["usuario"]."</b> a tu espacio de corredor de propiedades | ".$this->devolverFecha()."</div>";
 }
	
	
	if($tipo!="arrendatario"){
	echo "<div>";
	echo '<span class="badge text-bg-primary" style="font-size:14px; color:white !important;padding:8px;margin-right:10px;">';
	echo "UF: $".$this->formatoNumerico($d->uf->valor);
	echo '</span>';

	echo '<span class="badge text-bg-primary" style="font-size:14px; color:white !important;padding:8px;margin-right:10px;">';
	echo "UTM: $".$this->formatoNumerico($d->utm->valor);
	echo '</span>';

	echo '<span class="badge text-bg-primary" style="font-size:14px; color:white !important;padding:8px;margin-right:10px;">';
	echo "IPC:".$this->formatoNumerico($d->ipc->valor)." %";
	echo '</span>';
	
	echo '<span class="badge text-bg-primary" style="font-size:14px; color:white !important;padding:8px;margin-right:10px;">';
	echo "Dolar: $ ".$this->formatoNumerico($d->dolar->valor);
	echo '</span>';

	echo '<span class="badge text-bg-primary" style="font-size:14px; color:white !important;padding:8px;margin-right:10px;">';
	echo "EURO: $".$this->formatoNumerico($d->euro->valor);
	echo '</span>';

	
	echo '<span class="badge text-bg-primary" style="font-size:14px; color:white !important;padding:8px;margin-right:10px;">';
	echo "IMASEC: ".$this->formatoNumerico($d->imasec->valor)." %";
	echo '</span>';

	echo '<span class="badge text-bg-primary" style="font-size:14px; color:white !important;padding:8px;margin-right:10px;">';
	echo "Bitcoin: US Dolar $ ".$this->formatoNumerico($d->bitcoin->valor);
	echo '</span>';

	echo "</div>";
	}else{
 
		echo '<span class="badge text-bg-secondary" style="font-size:14px; color:white !important;padding:8px;margin-right:10px;">';
		echo "<div>Estado de pago </div>";
		echo "<div style='margin-top:5px;'>";
		echo $this->estadoPago();
		echo "</div>";
		echo '</span>';
		echo '<span class="badge text-bg-info" style="font-size:14px; color:white !important;padding:8px;margin-right:10px;">';
		echo "Recordatorio de pagos ";
		echo "<div style='margin-top:10px;'>";
		echo $this->proximoPago();
		echo "</div>";		
		echo '</span>';
 

		echo '<span class="badge text-bg-info" style="font-size:14px; color:white !important;padding:8px;margin-top:2px; margin-right:10px;">';
		echo "Solicitudes de Mantenimiento ";
		echo "<div style='margin-top:10px;'>";
		echo "Tiene ";
		echo $this->numSolicitudes($_SESSION["auth"]["idReg"]);
		echo " pendientes ";
		echo "</div>";
		echo '</span>';
		
	
		 
	}

 
  }	 
  public function portadaArrendatario(){
	echo '<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item" role="presentation">
	  <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Propiedades Arrendadas</button>
	</li>
	
	<li class="nav-item" role="presentation">
	  <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Solicitudes Mantención</button>
	</li>
	
  </ul>
  <div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">';
	
	$this->tablaPropiedadesArrendadas();
	echo '</div>
	<div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">...</div>
	<div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">';
	$this->tablaMantenimiento();
	echo '</div>
	<div class="tab-pane fade" id="disabled-tab-pane" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">...</div>
  </div>';
  }
public function estadoPago(){
	$idArrendatario = $_SESSION["auth"]["idReg"];
 
	$sql = "SELECT COUNT(*) AS total_registros FROM mm_liquidacionArriendo 
			WHERE idArrendatario = '$idArrendatario' AND estado = 3";

			$q=mysqli_query($this->link,$sql);
			$r=mysqli_fetch_array($q);
			$total=$r["total_registros"];

			if($total>0){
				echo "Tiene ".$total." cuenta por pagar";
			}else{
				echo "Al Dia";
			}
			
 
}
public function proximoPago(){
	$idArrendatario = $_SESSION["auth"]["idReg"];

	// Consulta SQL para obtener la fecha de inicio y fecha de término del arriendo
	$sql = "SELECT fechaInicio, fechaTermino FROM mm_arrendatario WHERE idArrendatario = '$idArrendatario'";
	
	// Ejecutar la consulta
	$resultado = mysqli_query($this->link, $sql);
	
	// Verificar si la consulta fue exitosa
	if ($resultado) {
		// Obtener el resultado como un array asociativo
		$arriendo = mysqli_fetch_assoc($resultado);
	
		// Verificar si hay un resultado válido
		if ($arriendo) {
			// Obtener la fecha de inicio y fecha de término del arriendo
			$fechaInicio = $arriendo['fechaInicio'];
			$fechaTermino = $arriendo['fechaTermino'];
	
			// Obtener la fecha actual
			$fechaActual = date('Y-m-d');
	
			// Verificar si la fecha actual está después de la fecha de término del arriendo
			if ($fechaActual > $fechaTermino) {
				echo "Contrato terminado";
			} else {
				// Calcular la próxima fecha de pago (suponiendo periodicidad mensual)
				$proximoPago = strtotime('+1 month', strtotime($fechaInicio));
				$proximoPago = date('Y-m-d', $proximoPago);
				echo "La próxima fecha de pago es: " . $proximoPago;
			}
		} else {
			echo "No se encontró un arriendo para el arrendatario con ID: " . $idArrendatario;
		}
	
		// Liberar el resultado de la consulta
		mysqli_free_result($resultado);
	} else {
		// Si la consulta falla, mostrar un mensaje de error
		echo "Error al ejecutar la consulta: " . mysqli_error($this->link);
	}
	
}
public function bandejaEntrada(){
	$this->controles("Bandeja de entrada");
	echo "<div class='container'>";
	echo "<div class='row'>";
	echo "<div class='col-md-3'>";
	
	echo '<div class="list-group">
	<a href="panel.php?op=50&act=solicitudes" class="list-group-item list-group-item-action active" aria-current="true">
	  Solicitudes Arrendatarios
	</a>
	<a href="#" class="list-group-item list-group-item-action">Mensajeria</a>
	
  </div>';

	echo "</div>";

	echo "<div class='col-md-9'>";
	
	if(isset($_GET["act"]) && $_GET["act"]=="solicitudes"){
		$g=$_GET["act"];
		if($g=="solicitudes"){
			$this->leerSol();
		}else{
			$this->leerMsn();
		}
	}else if($_GET["act"]=="leer"){
		$idMan=$_GET["idMan"];
		$this->responderSol($idMan);
	}

	
	echo "</div>";


	echo "</div>";
	echo "</div>";
}
public function devolverCorreoArrendatario($id){
	$sql="select email,telefono from mm_arrendatario where idArrendatario='".$id."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	$m["email"]=$r["email"];
	$m["telefono"]=$r["telefono"];
	return($m);
}
public function actualizarLectura($id){
	$sql="update mm_mantenimiento set leido=1 where idMantenimiento='".$id."'";
	mysqli_query($this->link,$sql);
	return(true);
}
public function responderSol($id){
	$this->actualizarLectura($id);
	$sql="select* from mm_mantenimiento where idMantenimiento='".$id."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	echo '<div class="email-message">';
	echo '<div class="email-header">';
	echo '<h2>'.$r["tipo_servicio"].'</h2>';
	echo '<p>Fecha: ' . date('Y-m-d', $r['fecha']) . '</p>';
	echo '<p>Autor: ' . $this->devolverArrendatario($r["idArrendatario"]) . '</p>';
	echo '<p>Propiedad: ' . $this->devolverPropiedad($r["idPropiedad"]) . '</p>';

	echo '</div>';
	echo '<div class="email-body">';
	echo '<p style="padding-top:10px; padding-bottom:10px;">' . $r['descripcion_problema'] . '</p>';
	echo '<p>Fecha preferida: ' . $r['fecha_preferida'] . '</p>';
	echo '<p>Hora preferida: ' . $r['hora_preferida'] . '</p>';
	echo "<p>Imagen del problema</p>";
	echo '<img src="' . $r['imagen_problema'] . '"  alt="Imagen del problema">';
	echo '</div>';
	echo '<div class="email-footer">';
	$c=$this->devolverCorreoArrendatario($r["idArrendatario"]);
	echo '<a href="mailto:'.$c["email"].'" class="btn btn-primary btn-sm"><i class="fas fa-envelope"></i> Responder por correo</a>&nbsp;&nbsp;';
	echo '<a href="tel:'.$c["telefono"].'" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i> Llamar por telefono</a>';
	echo '</div>';
	echo '</div>';
	echo '<style>
	.email-message {
		border: 1px solid #ccc;
		border-radius: 5px;
		margin-bottom: 20px;
		padding: 10px;
	}
	
	.email-header {
		margin-bottom: 10px;
	}
	
	.email-header h2 {
		margin: 0;
	}
	
	.email-header p {
		margin: 5px 0;
		color: #666;
	}
	
	.email-body img {
		max-width: 100%;
		height: auto;
		margin-top: 10px;
	}
	
	.email-footer {
		text-align: center;
		margin-top: 10px;
	}
	
	</style>';
	
}
public function devolverArrendatario($id){
	$sql="select* from mm_arrendatario where idArrendatario='".$id."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	return($r["nombre"]);
}
public function nSoli(){
	
		$sql = "SELECT COUNT(*) AS total_registros_no_leidos FROM mm_mantenimiento WHERE leido = 0";
		$result = mysqli_query($this->link, $sql);
		if ($result) {
		  $row = mysqli_fetch_assoc($result);
		$total_registros_no_leidos = $row['total_registros_no_leidos'];
		echo "<div>Tiene un total de ".$total_registros_no_leidos." mensajes</div>";
	} 
	
}
public function leerSol(){
	$sql="select* from mm_mantenimiento order by idMantenimiento desc";
	$q=mysqli_query($this->link,$sql);
	echo "<div>".$this->nSoli()."</div>";
	echo '<div class="list-group">';

	while ($r = mysqli_fetch_array($q)) {
		echo '<a href="panel.php?op=50&act=solicitudes&act=leer&idMan='.$r["idMantenimiento"].'" class="list-group-item list-group-item-action">';
	
		// Contenedor principal de la fila
		echo '<div class="d-flex align-items-center justify-content-between">';
		
		// Contenedor para el nombre y tipo de servicio
		echo '<div>';
		echo '<span class="fw-bold">' . $this->devolverArrendatario($r['idArrendatario']) . '</span>';
		echo '<span class="ms-2 badge bg-primary">' . $r['tipo_servicio'] . '</span>';
		echo '</div>';
		
		// Contenedor para la fecha
		echo '<div class="text-muted">';
		echo '<span class="small">' .date("d-m-Y",$r["fecha"]) . '</span><br>';
		echo '<span style="font-size:14px;">';
		if($r["leido"]==0){
			echo "No Leido";
		}else{
			echo "<i class='fas fa-check'></i>  Leido";
		}
		echo "</span>";
		echo '</div>';
	
		// Cierre del contenedor principal
		echo '</div>';
	
		// Contenedor para la descripción
		echo '<p class="mb-1">' . $r['descripcion_problema'] . '</p>';
	
		echo '</a>';
	}
	
	echo '</div>';
	echo '<style>
	.list-group-item {
		border: none;
	}
	
	.list-group-item:hover {
		background-color: #f0f0f0;
	}
	
	.list-group-item .badge {
		font-size: 0.8rem;
	}
	
	.list-group-item p {
		margin-bottom: 0;
		font-size: 0.9rem;
	}
	
	</style>';
}
public function leerMsn(){

}
public function contarSoli(){
	$sql = "SELECT COUNT(*) AS total_registros_no_leidos FROM mm_mantenimiento WHERE leido = 0";
	$result = mysqli_query($this->link, $sql);
	if ($result) {
      $row = mysqli_fetch_assoc($result);
    $total_registros_no_leidos = $row['total_registros_no_leidos'];
    echo $total_registros_no_leidos;
} 
}
public function numSolicitudes($idArrendatario){
	$sql="select count(*) as total from mm_mantenimiento where idArrendatario='".$idArrendatario."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	$total=$r["total"];
	return($total);
}
public function leerMan(){
	$idMan=$_GET["idMan"];
	$sql="select* from mm_mantenimiento where idMantenimiento='".$idMan."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	echo "<div class='container'>";
	echo "<div class='row'>";
	echo "<div class='col-md-9'>";
if($_SESSION["auth"]["tipo"]!="admin"){
	echo '
<div style="margin-bottom:15px;"><h5>Detalles de Mantenimiento</h5> <a href="panelArrendatario.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i>
Volver</a></div>';
}


echo '
<div>
    
    
    <p><strong>Propiedad:</strong><br> ' . $this->devolverPropiedad($r['idPropiedad']) . '</p>
    <p><strong>Tipo de Servicio:</strong><br> ' . $r['tipo_servicio'] . '</p>
    <p><strong>Descripción del Problema:</strong><br> ' . $r['descripcion_problema'] . '</p>
    <p><strong>Fecha Preferida:</strong><br> ' . $r['fecha_preferida'] . '</p>
    <p><strong>Hora Preferida:</strong><br> ' . $r['hora_preferida'] . '</p>
    <p><strong>Imagen del Problema:</strong><br> <img src="' . $r['imagen_problema'] . '" alt="Imagen del Problema" style="max-width: 200px;"></p>
    <p><strong>Estado de Avance:</strong><br> ' . $r['estado_avance'] . '</p>
</div>';
	echo "</div>";
	echo "</div>";
	echo "</div>";
}
  public function tablaMantenimiento(){ 

	$this->link=$this->conectar();	 
	
	$idArrendatario=$_SESSION["auth"]["idUser"];
	  if(isset($_GET["msg"])){
		  $msg=htmlentities($_GET["msg"]);
	 
		 if($msg==1){
		 
		 }else{
			echo '<div class="alert alert-primary" role="alert">
			Cambios se han realizado con exito
		  </div>';
		 }
	  }
	
	$tamCol=array(40,30,2,2);
	
	
				 
	
   
	$campoFoto=array("ruta"=>true);        
	$campoIndice="idArriendo";            
	$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);
   
	$tabla="mm_mantenimiento";

	$grid->asignarCampos($campos,$tamCol,$sql,$tabla);
	 
	if(isset($_GET["msg"])){
		echo '<div class="alert alert-primary" role="alert">
		Propiedad eliminada con exito
	  </div>';
	}
	 

	$tipo=$_SESSION["auth"]["tipo"];
	if(isset($_POST["action"])){
		$col=$_POST["columna"];
		$buscador=$_POST["buscador"];
		 
	} 
	$campos=array(
		"tipo_servicio"=>"tipo_servicio",
		"titulo"=>"titulo","estado_avance"=>"estado_avance","fecha_preferida"=>"fecha_preferida","hora_preferida"=>"hora_preferida"
); 
if($_SESSION["auth"]["tipo"]=="admin"){
	$idArrendatario=$_GET["idArrendatario"];
}else{
	$idArrendatario=$_SESSION["auth"]["idReg"];
}
	
	$sql = "SELECT mm_mantenimiento.*, mm_propiedad1.*
        FROM mm_mantenimiento
        INNER JOIN mm_propiedad1 ON mm_mantenimiento.idPropiedad = mm_propiedad1.idProp
        WHERE mm_mantenimiento.idArrendatario = '$idArrendatario' order by idMantenimiento desc";
 
	$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
	if(isset($_GET["op"])){
		$this->controles("Solicitudes de mantención");
	}
	
	if(isset($_GET["idMan"])){
		$this->leerMan();
	}else{
		$grid->desplegarDatosMantencion();
	}
	

 
	 
	return(true); 
 }


  public function solicitarMantenimiento(){
 
	if(isset($_POST["enviar"])){
		$idArrendatario=$_SESSION["auth"]["idReg"];
		$idPropiedad = $_GET["idProp"];
		$tipoServicio = $_POST["tipo_servicio"];
		$descripcionProblema = $_POST["descripcion"];
		$fechaPreferida = $_POST["fecha_preferida"];
		$horaPreferida = $_POST["hora_preferida"];
		$imagenProblema = $_FILES["imagen"]["name"]; // Obtener el nombre del archivo de la imagen
		$fecha=strtotime(date("Y-m-d"));
		// Ruta de destino para guardar la imagen
		$targetDir = "imgArren/";
		$targetFile = $targetDir . basename($imagenProblema);
	
		// Mover la imagen al directorio de subida
		if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $targetFile)) {		
	
			
			$imagenProblema = $targetFile;
	
			// Preparar la consulta SQL de inserción
			$sql = "INSERT INTO  mm_mantenimiento (idArrendatario,idPropiedad, tipo_servicio, descripcion_problema, fecha_preferida, hora_preferida, imagen_problema,fecha)
					VALUES ('$idArrendatario','$idPropiedad', '$tipoServicio', '$descripcionProblema', '$fechaPreferida', '$horaPreferida', '$imagenProblema', '$fecha')";
	
			mysqli_query($this->link,$sql);
				echo "<script>document.location='panelArrendatario.php?op=30&idProp=".$idPropiedad."&msg=1';</script>";
			exit;
		} else {
			echo "Error al subir la imagen.";
		}
	}
	echo "<div class='container'>";
	echo "<div class='row'>";
	echo "<div class='col-md-9'>";
	if(isset($_GET["msg"])){
		echo '<div class="alert alert-primary" role="alert">
		Solicitud se ha enviado con exito, un ejecutivo se contactara con usted a la brevedad !!!
	  </div>';
	}
	echo '   <form action="" name="form1" id="form1" enctype="multipart/form-data" method="POST">
	<div style="margin-bottom: 10px;"><h5>Solicitud de mantenimiento</h5></div>
	<div style="margin-bottom:5px;"><a href="panelArrendatario.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a></div>

	<div class="mb-3">
		<label for="tipo_servicio" class="form-label">Tipo de Servicio:</label><br>
		<select id="tipo_servicio" name="tipo_servicio" required class="form-select" onchange="updateSubservices()">
			<option value="">Seleccione el tipo de servicio</option>
			<optgroup label="Reparaciones de Fontanería">
				<option value="Reparación de grifos y llaves de paso">Reparación de grifos y llaves de paso</option>
				<option value="Reparación de inodoros">Reparación de inodoros</option>
				<option value="Desatranco de tuberías y desagües">Desatranco de tuberías y desagües</option>
				<option value="Reparación de fugas en tuberías">Reparación de fugas en tuberías</option>
				<option value="Instalación o reparación de calentadores de agua">Instalación o reparación de calentadores de agua</option>
				<option value="Instalación o reparación de sistemas de filtración de agua">Instalación o reparación de sistemas de filtración de agua</option>
				<option value="Reparación o reemplazo de bombas de agua">Reparación o reemplazo de bombas de agua</option>
				<option value="Reparación de duchas y bañeras">Reparación de duchas y bañeras</option>
			</optgroup>
			<optgroup label="Servicios Eléctricos">
				<option value="Reparación de interruptores y enchufes">Reparación de interruptores y enchufes</option>
				<option value="Instalación o reparación de sistemas de iluminación">Instalación o reparación de sistemas de iluminación</option>
				<option value="Solución de problemas de cableado">Solución de problemas de cableado</option>
				<option value="Instalación o reemplazo de paneles eléctricos">Instalación o reemplazo de paneles eléctricos</option>
				<option value="Instalación o reparación de sistemas de seguridad eléctrica">Instalación o reparación de sistemas de seguridad eléctrica</option>
				<option value="Reparación de cortocircuitos">Reparación de cortocircuitos</option>
				<option value="Instalación o reparación de ventiladores de techo">Instalación o reparación de ventiladores de techo</option>
				<option value="Instalación o reparación de sistemas de alarma contra incendios">Instalación o reparación de sistemas de alarma contra incendios</option>
			</optgroup>
			<optgroup label="Servicios de Mantenimiento">
    <option value="Servicio de mantenimiento preventivo de HVAC">Servicio de mantenimiento preventivo de HVAC</option>
    <option value="Reparación de sistemas de calefacción">Reparación de sistemas de calefacción</option>
    <option value="Reparación de sistemas de aire acondicionado">Reparación de sistemas de aire acondicionado</option>
    <option value="Instalación o reemplazo de termostatos">Instalación o reemplazo de termostatos</option>
    <option value="Servicio de limpieza y mantenimiento de conductos de aire">Servicio de limpieza y mantenimiento de conductos de aire</option>
    <option value="Reparación de radiadores y calderas">Reparación de radiadores y calderas</option>
    <option value="Recarga de refrigerante para sistemas de aire acondicionado">Recarga de refrigerante para sistemas de aire acondicionado</option>
    <option value="Instalación de sistemas de calefacción y refrigeración de alta eficiencia">Instalación de sistemas de calefacción y refrigeración de alta eficiencia</option>
</optgroup>

<optgroup label="Reparación de Electrodomésticos">
<option value="Reparación de lavadoras y secadoras">Reparación de lavadoras y secadoras</option>
<option value="Reparación de lavavajillas">Reparación de lavavajillas</option>
<option value="Reparación de hornos y estufas">Reparación de hornos y estufas</option>
<option value="Reparación de microondas">Reparación de microondas</option>
<option value="Reparación de frigoríficos y congeladores">Reparación de frigoríficos y congeladores</option>
<option value="Reparación de sistemas de ventilación de cocina">Reparación de sistemas de ventilación de cocina</option>
<option value="Instalación o reemplazo de electrodomésticos de cocina">Instalación o reemplazo de electrodomésticos de cocina</option>
<option value="Servicio de mantenimiento preventivo de electrodomésticos">Servicio de mantenimiento preventivo de electrodomésticos</option>
</optgroup>

<!-- Reparaciones menores y pintura -->
<optgroup label="Reparaciones menores y pintura">
<option value="Reparación de grietas en paredes y techos">Reparación de grietas en paredes y techos</option>
<option value="Pintura interior y exterior">Pintura interior y exterior</option>
<option value="Reparación de daños por agua">Reparación de daños por agua</option>
<option value="Reparación de daños por humedad y moho">Reparación de daños por humedad y moho</option>
<option value="Instalación de papel pintado y molduras">Instalación de papel pintado y molduras</option>
<option value="Reparación de azulejos y baldosas">Reparación de azulejos y baldosas</option>
<option value="Restauración de suelos de madera">Restauración de suelos de madera</option>
<option value="Sellado de juntas y grietas">Sellado de juntas y grietas</option>
</optgroup>

<!-- Arreglos de puertas y ventanas -->
<optgroup label="Arreglos de puertas y ventanas">
<option value="Reparación de bisagras y cerrojos">Reparación de bisagras y cerrojos</option>
<option value="Reparación o reemplazo de cerraduras">Reparación o reemplazo de cerraduras</option>
<option value="Ajuste de puertas y ventanas que no cierran correctamente">Ajuste de puertas y ventanas que no cierran correctamente</option>
<option value="Reemplazo de cristales rotos">Reemplazo de cristales rotos</option>
<option value="Instalación de persianas y cortinas">Instalación de persianas y cortinas</option>
<option value="Reparación de marcos de puertas y ventanas">Reparación de marcos de puertas y ventanas</option>
<option value="Sellado de ventanas y puertas para mejorar la eficiencia energética">Sellado de ventanas y puertas para mejorar la eficiencia energética</option>
<option value="Instalación de sistemas de seguridad en puertas y ventanas">Instalación de sistemas de seguridad en puertas y ventanas</option>
</optgroup>

<!-- Servicios de limpieza -->
<optgroup label="Servicios de limpieza">
<option value="Limpieza profunda de la propiedad">Limpieza profunda de la propiedad</option>
<option value="Limpieza de alfombras y tapicería">Limpieza de alfombras y tapicería</option>
<option value="Limpieza de ventanas y persianas">Limpieza de ventanas y persianas</option>
<option value="Limpieza de conductos de aire">Limpieza de conductos de aire</option>
<option value="Eliminación de moho y humedad">Eliminación de moho y humedad</option>
<option value="Limpieza de áreas comunes y exteriores">Limpieza de áreas comunes y exteriores</option>
<option value="Servicios de limpieza después de eventos especiales">Servicios de limpieza después de eventos especiales</option>
<option value="Limpieza de piscinas y jacuzzis">Limpieza de piscinas y jacuzzis</option>
</optgroup>

<!-- Mantenimiento del jardín o áreas exteriores -->
<optgroup label="Mantenimiento del jardín o áreas exteriores">
<option value="Corte de césped y mantenimiento de jardines">Corte de césped y mantenimiento de jardines</option>
<option value="Poda de árboles y arbustos">Poda de árboles y arbustos</option>
<option value="Control de malezas y aplicación de fertilizantes">Control de malezas y aplicación de fertilizantes</option>
<option value="Riego y mantenimiento de sistemas de riego">Riego y mantenimiento de sistemas de riego</option>
<option value="Diseño y paisajismo de jardines">Diseño y paisajismo de jardines</option>
<option value="Instalación de iluminación exterior">Instalación de iluminación exterior</option>
<option value="Mantenimiento de piscinas y estanques">Mantenimiento de piscinas y estanques</option>
<option value="Limpieza y mantenimiento de terrazas y patios">Limpieza y mantenimiento de terrazas y patios</option>
</optgroup>

<!-- Reparación de daños causados por el clima -->
<optgroup label="Reparación de daños causados por el clima">
<option value="Reparación de techos dañados por la lluvia o el granizo">Reparación de techos dañados por la lluvia o el granizo</option>
<option value="Reemplazo de tejas y materiales de cubierta">Reemplazo de tejas y materiales de cubierta</option>
<option value="Reparación de estructuras exteriores dañadas">Reparación de estructuras exteriores dañadas</option>
<option value="Sellado de juntas y grietas para evitar filtraciones de agua">Sellado de juntas y grietas para evitar filtraciones de agua</option>
<option value="Reparación de daños en ventanas y puertas causados por vientos fuertes">Reparación de daños en ventanas y puertas causados por vientos fuertes</option>
<option value="Reparación de daños en el revestimiento exterior">Reparación de daños en el revestimiento exterior</option>
<option value="Reforzamiento de estructuras para resistir condiciones climáticas extremas">Reforzamiento de estructuras para resistir condiciones climáticas extremas</option>
<option value="Reparación de daños causados por inundaciones, tormentas y otros desastres naturales">Reparación de daños causados por inundaciones, tormentas y otros desastres naturales</option>
</optgroup>

		</select>
	</div>

 

	<div class="mb-3">
		<label for="descripcion" class="form-label">Descripción del Problema:</label><br>
		<textarea id="descripcion" name="descripcion" rows="4" cols="50" required class="form-control"></textarea>
	</div>

	<div class="mb-3">
		<label for="fecha_preferida" class="form-label">Fecha Preferida para la Reparación:</label><br>
		<input type="date" id="fecha_preferida" name="fecha_preferida" class="form-control">
	</div>

	<div class="mb-3">
		<label for="hora_preferida" class="form-label">Hora Preferida para la Reparación:</label><br>
		<input type="time" id="hora_preferida" name="hora_preferida" class="form-control">
	</div>

	<div class="mb-3">
		<label for="imagen" class="form-label">Adjuntar imagen del problema (opcional):</label><br>
		<input type="file" id="imagen" name="imagen" class="form-control">
	</div>

	<button type="submit" id="enviar" name="enviar" class="btn btn-primary">Enviar Solicitud</button>
</form>';

	echo "</div>";


	echo "</div>";
	echo "</div>";
  }
  public function formatoNumerico($num){
	$n=number_format($num, 0,",",".");
	return($n);
}
 

public function cambioContra(){

	
	if(isset($_POST["action"])){
		
		$currentPassword = $_POST["currentPassword"];
		$newPassword = $_POST["newPassword"];
		$idUser=$_SESSION["auth"]["idReg"];
		$sql="update mm_propietarios set contra='".$newPassword."' where idPropietario='".$idUser."'";   
	       
		mysqli_query($this->link, $sql);
			echo "<script>document.location='panelPropietario.php?mod=panel&op=100&msg=1';</script>";
		exit;            
	}
	echo '<div align="left" style="margin-top:20px;">';
	if(isset($_GET["msg"])){
		echo '<div class="alert alert-primary" role="alert">
		Contraseña ha sido modificada con exito
	  </div>';
	}
	echo '<form method="post" name="form1" id="form1">
	<div class="container">
	  <div class="row">
		<div class="col-md-6" align="left">
		  <h5>Modificar Contraseña</h5>
		  <form method="POST" action="actualizar_contrasena.php">
			<input type="hidden" name="action" value="updatePassword">
		 
			<div class="mb-3">
			  <label for="newPassword" class="form-label">Nueva Contraseña</label>
			  <input type="password" class="form-control" id="newPassword" name="newPassword" required>
			</div>
			<div class="mb-3">
			  <label for="confirmPassword" class="form-label">Confirmar Nueva Contraseña</label>
			  <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
			</div>
			<button type="submit" class="btn btn-primary">Modificar</button>
		  </form>
		</div>
	  </div>
	</div>
  </form>
  </div>
  ';
}

public function cambioContraArrendatario(){

	
	if(isset($_POST["action"])){
		
		$currentPassword = $_POST["currentPassword"];
		$newPassword = $_POST["newPassword"];
		$idUser=$_SESSION["auth"]["idReg"];
		$sql="update mm_arrendatario set contra='".$newPassword."' where idArrendatario='".$idUser."'";   
	       
		mysqli_query($this->link, $sql);
			echo "<script>document.location='panelArrendatario.php?mod=panel&op=1001&msg=1';</script>";
		exit;            
	}
	echo '<div align="left" style="margin-top:20px;">';
	if(isset($_GET["msg"])){
		echo '<div class="alert alert-primary" role="alert">
		Contraseña ha sido modificada con exito
	  </div>';
	}
	echo '<form method="post" name="form1" id="form1">
	<div class="container">
	  <div class="row">
		<div class="col-md-6" align="left">
		  <h5>Modificar Contraseña</h5>
		  <form method="POST" action="actualizar_contrasena.php">
			<input type="hidden" name="action" value="updatePassword">
		 
			<div class="mb-3">
			  <label for="newPassword" class="form-label">Nueva Contraseña</label>
			  <input type="password" class="form-control" id="newPassword" name="newPassword" required>
			</div>
			<div class="mb-3">
			  <label for="confirmPassword" class="form-label">Confirmar Nueva Contraseña</label>
			  <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
			</div>
			<button type="submit" class="btn btn-primary">Modificar</button>
		  </form>
		</div>
	  </div>
	</div>
  </form>
  </div>
  ';
}

 public function perfilPropietario(){
	$this->link=$this->conectar();
 
	$id=$_SESSION["auth"]["idReg"];
	$sql="select* from mm_propietarios where idPropietario='".$id."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	if(isset($_POST["guardar"])){	
		$d=$_POST;		
		$sql1="update mm_propietarios set 
			nombre='".$d["nombre"]."',apellido='".$d["apellido"]."',rut='".$d["rut"]."'
			,email='".$d["email"]."',telefono='".$d["telefono"]."' where idPropietario='".$id."'";
			mysqli_query($this->link,$sql1);
				echo "<script>document.location='panelPropietario.php?op=999&msg=1';</script>";
			exit;
	}
	echo "<div class='container'>";
	echo "<div class='row'>";
	echo "<div class='col-md-6'>";
	if(isset($_GET["msg"])){
		echo '<div class="alert alert-primary" role="alert">
		Cambios han sido guardados con exito !!!
	  </div>';
	}
	echo "<div><h5>Edita tu perfil de propietario</h5></div>";
	echo ' <form action="" id="form" name="form" method="POST">
	<div class="mb-3">
	  <label for="nombre" class="form-label">Nombre</label>
	  <input type="text" class="form-control" id="nombre" name="nombre" value="'.$r["nombre"].'" required>
	</div>
	<div class="mb-3">
	  <label for="apellido" class="form-label">Apellido</label>
	  <input type="text" class="form-control" id="apellido" name="apellido" value="'.$r["apellido"].'" required>
	</div>	
	<div class="mb-3">
	  <label for="rut" class="form-label">RUT</label>
	  <input type="text" class="form-control" id="rut" name="rut" value="'.$r["rut"].'">
	</div>
	<div class="mb-3">
	  <label for="email" class="form-label">Email</label>
	  <input type="email" class="form-control" id="email" name="email" value="'.$r["email"].'" required>
	</div>
	<div class="mb-3">
	  <label for="telefono" class="form-label">Teléfono</label>
	  <input type="text" class="form-control" id="telefono" name="telefono" value="'.$r["telefono"].'">
	</div>
	
	
	
	<button type="submit" id="guardar" name="guardar" class="btn btn-primary">Guardar cambios</button>
  </form>';
  echo "</div></div></div>";
 }


 public function perfilArrendatario(){
	$this->link=$this->conectar();
 
	$id=$_SESSION["auth"]["idReg"];
	$sql="select* from mm_arrendatario where idArrendatario='".$id."'";
	
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	if(isset($_POST["guardar"])){	
		$d=$_POST;		
		$sql1="update mm_arrendatario set 
			nombre='".$d["nombre"]."',rut='".$d["rut"]."'
			,email='".$d["email"]."',telefono='".$d["telefono"]."' where idArrendatario='".$id."'";
		 
			mysqli_query($this->link,$sql1);
				echo "<script>document.location='panelArrendatario.php?op=9991&msg=1';</script>";
			exit;
	}
	echo "<div class='container'>";
	echo "<div class='row'>";
	echo "<div class='col-md-6'>";
	if(isset($_GET["msg"])){
		echo '<div class="alert alert-primary" role="alert">
		Cambios han sido guardados con exito !!!
	  </div>';
	}
	echo "<div><h5>Edita tu perfil de arrendatario</h5></div>";
	echo ' <form action="" id="form" name="form" method="POST">
	<div class="mb-3">
	  <label for="nombre" class="form-label">Nombre</label>
	  <input type="text" class="form-control" id="nombre" name="nombre" value="'.$r["nombre"].'" required>
	</div>	
	<div class="mb-3">
	  <label for="rut" class="form-label">RUT</label>
	  <input type="text" class="form-control" id="rut" name="rut" value="'.$r["rut"].'">
	</div>
	<div class="mb-3">
	  <label for="email" class="form-label">Email</label>
	  <input type="email" class="form-control" id="email" name="email" value="'.$r["email"].'" required>
	</div>
	<div class="mb-3">
	  <label for="telefono" class="form-label">Teléfono</label>
	  <input type="text" class="form-control" id="telefono" name="telefono" value="'.$r["telefono"].'">
	</div>
	
	
	
	<button type="submit" id="guardar" name="guardar" class="btn btn-primary">Guardar cambios</button>
  </form>';
  echo "</div></div></div>";
 }

public function ingresar_mm_arriendos(){
	$this->link=$this->conectar();
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Arriendo ingresado con exito!!!");}}
	$this->miForm->abrirForm(1,true,"form1","post","proceso.php",2);
	$this->controles("Ingresar Arriendo");
	
	echo "<div style='margin-bottom:20px;'><span style='font-size:18px; font-weight:500;margin-top:30px;'>Información general</span></div>";
	
	$sql="select* from mm_propiedad1 order by idProp desc";
	$q=mysqli_query($this->link,$sql);
	
	while($r=mysqli_fetch_array($q)){
		$arrSel2[$r["idProp"]]=ucfirst($r["direccionProp"]).",&nbsp;".$this->devolverComuna($r["idComuna"]).",&nbsp; ".$this->devolverRegion($r["idRegion"]);
		
	}
	
	$this->miForm->addSelect("idPropiedad",$arrSel2,"Seleccione...",false,"Propiedad",false);		

	$this->miForm->initCalendar();
	$sql="select* from mm_arrendatario order by idArrendatario desc";
	$q=mysqli_query($this->link,$sql);
 
	while($r=mysqli_fetch_array($q)){
		$arrSel21[$r["idArrendatario"]]=ucfirst($r["nombre"])." / ".$r["rut"];
	}
	$this->miForm->addSelect("idArrendatarios",$arrSel21,"Seleccione...",false,"Arrendatarios",false);		

	$arrSel23=array(1=>"Mensual",2=>"Trimestral",3=>"Semestral",4=>"Anual");
	$this->miForm->addSelect("frecuenciaPago",$arrSel23,"Frecuencia de pago",false,"Frecuencia de pago",false);		


	$sql11="select* from mm_codeudor order by idCodeudor desc";
	$q11=mysqli_query($this->link,$sql11);
 
	while($r11=mysqli_fetch_array($q11)){
		$arrSel211[$r11["idCodeudor"]]=ucfirst($r11["nombre"])." / ".$r11["rut"];
	}
	$this->miForm->addSelect("idCodeudor",$arrSel211,"Seleccione...",false,"Codeudor",false);		


 
	echo "<div style='margin-bottom:20px;margin-top:20px;'><span style='font-size:18px; font-weight:500;margin-bottom:100px !important;margin-top:30px;'>Datos de Contrato</span></div>";
	$dateActual=date("m-d-Y");
	$this->miForm->addCalendar("fechaInicio",550,"Fecha de Inicio","Fecha de Inicio:",false);

	$this->miForm->addCalendar("fechaCancelacion",550,"Fecha de Termino","Fecha de Termino:",false);

	$arrSel11=array(1=>"1 mes",2=>"3 meses",3=>"6 meses",4=>"1 año");
	$this->miForm->addSelect("duracionContrato",$arrSel11,"Seleccione...",false,"Duración del contrato:",false);		

	

	$this->miForm->addPrecio("montoArriendo",550,"Precio","Precio:",false);

	$arrSel23=array(1=>"Peso","UF");
	$this->miForm->addSelect("moneda",$arrSel23,"Moneda",false,"Moneda",false);		

	$arrSel24=array(1=>"Si","No");
	$this->miForm->addSelect("garantiaPropi",$arrSel24,"¿Pagar garantia al propietario?",false,"¿Pagar garantia al propietario?",false);		


	echo '<div>Garantia</div>
	<div class="input-group">
	  <input type="text" id="garantia" name="garantia" class="form-control" aria-label="Monto en garantia">	  
	  <span class="input-group-text">Pesos</span>
	</div>';


	

 
	echo "<div style='margin-bottom:20px;margin-top:30px;'><span style='font-size:18px; font-weight:500;margin-bottom:100px !important;margin-top:30px;'>Datos de reajuste</span></div>";
	$arrSel24=array(1=>"Sin Reajuste","IPC","Fijo Porcentual","Fijo en Pesos");
	echo "<div style='margin-bottom:7px;'>Tipo de Reajuste</div>";
	echo "<div style='margin-bottom:7px;'>
	<select name='reajuste' id='reajuste' class='form-select from-mb'>";
	echo "<option value='0'>Seleccione tipo de reajuste</option>";
	foreach($arrSel24 as $c1=>$v1){		
		echo "<option value='".$c1."'>".$v1."</option>";
	}
	echo "</select>";
	echo "</div>";

	
	 

	echo '<div style="margin-bottom:7px;">Cantidad de Reajuste</div>
	<div class="input-group">
	  <input type="text" id="porcentaje" name="porcentaje" disabled class="form-control" aria-label="Monto en garantia">	  
	  <span class="input-group-text">%</span>
	</div>';


 
 
	echo "<div style='margin-bottom:20px;margin-top:30px;'><span style='font-size:18px; font-weight:500;margin-bottom:100px !important;margin-top:30px;'>Comisión de arriendo</span></div>";
	$arrSel241=array(1=>"Si","No");
	$this->miForm->addSelect("comisionArriendo",$arrSel241,"¿Cobrar comisión de arriendo?",false,"¿Cobrar comisión de arriendo?",false);

	$this->miForm->addText("valorComision",550,"Comision de arriendo","Comisión de arriendo:",false);

	
	$arrSel245=array(1=>"UF","Pesos","Porcentaje");
	$this->miForm->addSelect("monedaComision",$arrSel245,"Moneda Comisión",false,"Moneda Comisión",false);		

 

	
	echo "<div style='margin-bottom:20px;margin-top:30px;'><span style='font-size:18px; font-weight:500;margin-bottom:100px !important;margin-top:30px;'>Comisión a propietario</span></div>";
	$arrSel241=array(1=>"Si","No");
	$this->miForm->addSelect("comisionPropietario",$arrSel241,"¿Cobrar comisión a propietario?",false,"¿Cobrar comisión a propietario?",false);

	$this->miForm->addText("valorComisionPropietario",550,"Comision a propietario","Comisión a propietario:",false);

	
	$this->miForm->addSelect("monedaComisionPropietario",$arrSel245,"Moneda Comisión Propietario",false,"Moneda Comisión Propietario",false);	





	echo "<div style='margin-top:30px; margin-bottom:20px;'><span style='font-size:18px; font-weight:500;margin-bottom:100px !important;margin-top:30px;'>Comisión de administración</span></div>";

 

	$arrSel241=array(1=>"Si","No");
	$this->miForm->addSelect("cobrarComisionAdmin",$arrSel241,"¿Cobrar comisión de administración?",false,"¿Cobrar comisión de administración?",false);
	$this->miForm->addText("valorComisionAdmin",550,"Comision de administración","Comisión de administración:",false);
	
	$arrSel245=array(1=>"no activo","Pesos","Porcentaje");
	$this->miForm->addSelect("monedaComisionAdmin",$arrSel245,"Moneda Comisión de administración",false,"Moneda Comisión de administración",false);		

  
	
	$this->miForm->addHidden("action","true");
	$this->miForm->addButton("Enviar","Guardar Arriendo",false,false);
	$this->miForm->procesar();
	 
	if($_POST["action"]){ $d=$_POST;
		$fechaConvertida =(string)preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '$3-$1-$2', $d["fechaCancelacion"]);


	
	$sql="insert into mm_arriendos (
		idProp,
		idArrendatarios,	
	 
		idCodeudor,			
		fechaInicio,
		fechaCancelacion,
		frecuencia_pago,
		duracionContrato,
		montoArriendo,
		moneda,
		garantiaPropi,
		garantia,
		reajuste,
		porcentaje,
		comisionArriendo,
		valorComision,
		monedaComision,
		cobrarComisionAdmin,
		valorComisionAdmin,
		monedaComisionAdmin,
		comisionPropietario,
		valorComisionPropietario,
		monedaComisionPropietario
		
		 ) values (
			'".$d["idPropiedad"]."',
			'".$d["idArrendatarios"]."',		
			 
			'".$d["idCodeudor"]."',	
			'".strtotime($d["fechaInicio"])."',
			'".$fechaConvertida."',
			'".$d["frecuenciaPago"]."',
			'".$d["duracionContrato"]."',
			'".$d["montoArriendo"]."',
			'".$d["moneda"]."',
			'".$d["garantiaPropi"]."',			
			'".$d["garantia"]."',
			'".$d["reajuste"]."',
			'".$d["porcentaje"]."',
			'".$d["comisionArriendo"]."',
			'".$d["valorComision"]."',
			'".$d["monedaComision"]."',
			'".$d["cobrarComisionAdmin"]."',
			'".$d["valorComisionAdmin"]."',		
			'".$d["monedaComisionAdmin"]."',
			'".$d["comisionPropietario"]."',
			'".$d["valorComisionPropietario"]."',
			'".$d["monedaComisionPropietario"]."'			
			)";
 
			 
	mysqli_query($this->link,$sql);

	$idArriendo = mysqli_insert_id($this->link);

	$fecha = date("Y-m-d");
	 
	$sql2="insert into mm_garantia (idArriendo,saldo,fecha) values ('".$idArriendo."','".$d["garantia"]."','".$fecha."');";
	mysqli_query($this->link,$sql2);

	$idGarantia=mysqli_insert_id($this->link);
			if(empty($valorGarantia)){
				$valorGarantia=0;
			}
			$sql3="insert into mm_detalleGarantia (idGarantia, fecha,concepto,abono,descuento) ";
			$sql3.=" values ('".$idGarantia."','".$fecha."','Mes de Garantia','".$valorGarantia."','0');";
		 
			mysqli_query($this->link,$sql3);


		echo "<script>document.location='panel.php?op=16&msg=1';</script>";
	exit;
	}
	$this->miForm->cerrarForm();
	return($sql);
	}

 

	public function devolverPropiedad($idProp){
		$this->link=$this->conectar();
		$sql="select* from mm_propiedad1 where idProp='".$idProp."'";
	 
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$m=ucfirst($r["direccionProp"]).",&nbsp;".$this->devolverComuna($r["idComuna"]).",&nbsp; ".$this->devolverRegion($r["idRegion"]);
		return($m);
	}
	public function modificar_mm_arriendos($id=false){
	$this->link=$this->conectar();
	if(isset($_GET["idq"])){ $id=htmlentities($_GET["idq"]); }
	$sql1="select* from mm_arriendos where idArriendo='".$id."'";
	$q1=mysqli_query($this->link,$sql1);
	$r1=mysqli_fetch_array($q1);
 
 
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Cambios Guardados con exito!!!");}}
	$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
	echo "<h3>Editar Arriendos</h3>";
 
	echo "<div><h5>Información general</h5></div>";
	$sql="select* from mm_propiedad1 order by idProp desc";
	$q=mysqli_query($this->link,$sql);
	
	
	while($r4=mysqli_fetch_array($q)){
		$arrSel2[$r4["idProp"]]=ucfirst($r4["direccionProp"]).",&nbsp;".$this->devolverComuna($r4["idComuna"]).",&nbsp; ".$this->devolverRegion($r4["idRegion"]);
	}
	$this->miForm->addSelect("idPropiedad",$arrSel2,"Seleccione...",false,"Propiedad",$r1["idProp"]);		
	
	$this->miForm->initCalendar();
	$sql="select* from mm_arrendatario order by idArrendatario desc";
	$q=mysqli_query($this->link,$sql);
 
	while($r5=mysqli_fetch_array($q)){
		$arrSel21[$r5["idArrendatario"]]=ucfirst($r5["nombre"])." / ".$r5["rut"];
	}
	$this->miForm->addSelect("idArrendatarios",$arrSel21,"Seleccione...",false,"Arrendatarios",$r1["idArrendatarios"]);		

 
	$sql="select* from mm_codeudor order by idCodeudor desc";
	$q=mysqli_query($this->link,$sql);
 
	while($r2=mysqli_fetch_array($q)){
		$arrSel21[$r2["idCodeudor"]]=ucfirst($r2["nombre"])." / ".$r2["rut"];
	}
	
	$this->miForm->addSelect("idCodeudor",$arrSel21,"Seleccione...",false,"Codeudor",$r1["idCodeudor"]);		




	echo "<div style='margin-top:20px;margin-bottom:20px;'><h5>Datos de contacto</h5></div>";
	
	$this->miForm->addCalendar("fechaInicio",550,"Fecha de Inicio","Fecha de Inicio:",date("d/m/Y",$r1["fechaInicio"]));
	
 

	$this->miForm->addCalendar("fechaCancelacion",550,"Fecha de Termino","Fecha de Termino:",$r1["fechaCancelacion"]);

	
	$arrSel11=array(1=>"1 mes",2=>"3 meses",3=>"6 meses",4=>"1 año");
	$this->miForm->addSelect("duracionContrato",$arrSel11,"Seleccione...",false,"Duración del contrato:",$r1["duracionContrato"]);		
 

	$this->miForm->addPrecio("montoArriendo",550,"Precio","Precio:",$r1["montoArriendo"]);

	$arrSel23=array(1=>"Peso","UF");
	$this->miForm->addSelect("moneda",$arrSel23,"Moneda",false,"Moneda",$r1["moneda"]);		

	$arrSel24=array(1=>"Si","No");
	$this->miForm->addSelect("garantiaPropi",$arrSel24,"¿Pagar garantia al propietario?",false,"¿Pagar garantia al propietario?",$r1["garantiaPropi"]);		


	echo '<div>Garantia</div>
	<div class="input-group">
	  <input type="text" id="garantia" name="garantia" class="form-control" aria-label="Monto en garantia" value="'.$r1["garantia"].'">	  
	  <span class="input-group-text">Pesos</span>
	</div>';


	

 
	echo "<div style='margin-top:20px;margin-bottom:20px;'><h5>Datos de reajuste</h5></div>";
	

	$arrSel24=array(1=>"Sin Reajuste","IPC","Fijo Porcentual","Fijo en Pesos");
	$this->miForm->addSelect("reajuste",$arrSel24,"Tipo de reajuste",false,"Tipo de reajuste",$r1["reajuste"]);		

	echo '<div>Cantidad de Reajuste</div>
	<div class="input-group">
	  <input type="text" id="porcentaje" name="porcentaje" class="form-control" aria-label="Monto en garantia" value="'.$r1["porcentaje"].'">	  
	  <span class="input-group-text">%</span>
	</div>';


 
 
	echo "<div style='margin-top:20px;margin-bottom:20px;'><h5>Comisión de arriendo</h5></div>";
	$arrSel241=array(1=>"Si","No");
	$this->miForm->addSelect("comisionArriendo",$arrSel241,"¿Cobrar comisión de arriendo?",false,"¿Cobrar comisión de arriendo?",$r1["comisionArriendo"]);

	$this->miForm->addText("valorComision",550,"Comision de arriendo","Comisión de arriendo:",$r1["valorComision"]);

	$arrSel245=array(1=>"UF","Pesos","Porcentaje");
	$this->miForm->addSelect("monedaComision",$arrSel24,"Moneda Comisión",false,"Moneda Comisión",$r1["monedaComision"]);		



	


	echo "<div style='margin-top:20px;margin-bottom:20px;'><h5>Comision de administracion</h5></div>";

 

	$arrSel241=array(1=>"Si","No");
	$this->miForm->addSelect("cobrarComisionAdmin",$arrSel241,"¿Cobrar comisión de administración?",false,"¿Cobrar comisión de administración?",$r1["cobrarComisionAdmin"]);
	$this->miForm->addText("valorComisionAdmin",550,"Comision de administración","Comisión de administración:",$r1["valorComisionAdmin"]);
	
	$arrSel245=array(1=>"UF","Pesos","Porcentaje");
	$this->miForm->addSelect("monedaComisionAdmin",$arrSel245,"Moneda Comisión de administración",false,"Moneda Comisión de administración",$r1["monedaComisionAdmin"]);		

  



	$this->miForm->addHidden("action","true"); $this->miForm->addButton("Enviar","Guardar Cambios",false,false);
	$this->miForm->procesar();
	
	if($_POST["action"]){ $d=$_POST;
		$fechaConvertida =(string)preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '$3-$1-$2', $d["fechaCancelacion"]);


		
	
	$sql="update mm_arriendos set idProp='".$d["idPropiedad"]."',	 
	idArrendatarios='".$d["idArrendatarios"]."',		
	idCodeudor='".$d["idCodeudor"]."',		
	fechaInicio='".strtotime($d["fechaInicio"])."',		
	fechaCancelacion='".$fechaConvertida."',		
	duracionContrato='".$d["duracionContrato"]."',		
	montoArriendo='".$d["montoArriendo"]."',		
	moneda='".$d["moneda"]."',		
	garantiaPropi='".$d["garantiaPropi"]."',		

	garantia='".$d["garantia"]."',		
	reajuste='".$d["reajuste"]."',		
	porcentaje='".$d["porcentaje"]."',		
	comisionArriendo='".$d["comisionArriendo"]."',		
	valorComision='".$d["valorComision"]."',		
	monedaComision='".$d["monedaComision"]."',		
	cobrarComisionAdmin='".$d["cobrarComisionAdmin"]."',		
	valorComisionAdmin='".$d["valorComisionAdmin"]."',		
	monedaComisionAdmin='".$d["monedaComisionAdmin"]."' where idArriendo='".$id."'";
 
 	mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
 		echo "<script>document.location='panel.php?op=2&mq=editar&idq=".$id."&msg=1';</script>";
	exit;
	}
	$this->miForm->cerrarForm();
	return($sql);
	}
	
	public function tablaLiquidaciones(){
		
		 if(isset($_GET["act"]) && $_GET["act"]=="generar"){
				$this->accionaLiquidacion();
		 }else{		 
		$campoIndice="idLiquidaciones";
		$index="panel.php?m=1";
		$nomTabla="mm_liquidaciones";
		$sql="select* from mm_liquidaciones where idPropietario='".$idPropi."'";		
		$campos=array(
					  'fecha'=>'fecha',
					  'propiedad'=>'propiedad',
					  'montoPagar'=>'montoPagar',
					  'estado'=>'estado'
					  );
		
		$tamCol=array(10,60,10);
		$campoFoto=array("foto"=>true);
		$filtrar1=array('buscador'=>'nombre');
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_liquidaciones";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
		echo "<div style='margin-top:30px; margin-bottom:30px;'><a href='panel.php?op=23&act=generar' class='btn btn-primary btn-sm'> <i class='fas fa-file-invoice-dollar'></i> Generar Liquidaciones</a></div>";
		$grid->desplegarDatos();
		return($sql);
		 }
	}

	public function tablaLiquidacionesPropietario($idPropi){
		 
		$campoIndice="idLiquidaciones";
		if($_SESSION["auth"]["tipo"]=="admin"){
			$index="panel.php?m=1";
		}else{
			$index="panelPropietario.php?m=1";
		}
		
		$nomTabla="mm_liquidaciones";
		$sql="select* from mm_liquidaciones where idPropietario='".$idPropi."'";		
		$campos=array(
					  'propiedad'=>'propiedad',
					  'montoPagar'=>'montoPagar',
					  'pagoRecibido'=>'pagoRecibido',
					  'comision'=>'comision',
					  'iva'=>'iva',
					  'descuento'=>'descuento');


		
		
		$tamCol=array(40,10,20);
		$campoFoto=array("foto"=>true);
		$filtrar1=array('buscador'=>'nombre');
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_liquidaciones";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
		$grid->desplegarDatos();
		return($sql);
		
	}
	public function tablaLiquidacionesPropiedad($idProp){
		  
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==2){$this->msgBox=new msgBox(1,"Agente ha sido eliminado con exito!!");}}
	if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
	
	
	
	if($op=="editar"){$id=htmlentities($_GET["idq"]);
		$this->controles("Editar Propietarios","Crear Propietario","panel.php?op=12","fas fa-user-plus");
		$this->modificar_mm_liquidacionArriendo($id);
	}else if($op=="borrar"){
		$id=htmlentities($_GET["idq"]);
		$sql3="delete from mm_liquidacionArriend where idliquidacion='".$id."'";
		//mysqli_query($this->link,$sql3);
		//	echo "<script>document.location='panel.php?op=3&msg=1';</script>";
		exit;
	}else{
		//$this->controles("Listado liquidación de arriendo");
		$campoIndice="idLiquidacion";
		if($_SESSION["auth"]["tipo"]=="admin"){
			$index="panel.php?op=15";
		}else{
			$index="panelPropietario.php?ks=1&idProp=".$_GET["idProp"];
		}
		

		$campoFoto=array("ruta"=>true);
		//$nomTablaFoto="mm_cape_fotos";
		
		// debe cruzar la informacion con el detalle de la liquidacion para sacar la suma de las liquidaciones en los montos
		$campos=array(			
					'fecha'=>'fecha',										
					 
					'fechaCancelacion'=>"Fecha de Pago",
					
					'monto'=>'monto',
					'estado'=>"estado",		
					);

	 $sql="SELECT 
    mm_liquidacionArriendo.idLiquidacion,
    mm_liquidacionArriendo.idArriendo,
    mm_liquidacionArriendo.idProp,
    mm_liquidacionArriendo.idPropietario,
    mm_liquidacionArriendo.idArrendatario,
    mm_liquidacionArriendo.fechaLiqui,
    mm_liquidacionArriendo.estado,
	mm_liquidacionArriendo.rutaPdf,
    mm_liquidacionArriendo.idDatosDep,
    SUM(mm_detalleLiquidacion.abono) AS totalAbono,
    SUM(mm_detalleLiquidacion.descuento) AS totalDescuento,
    SUM(mm_detalleLiquidacion.abono) - SUM(mm_detalleLiquidacion.descuento) AS monto
FROM
    mm_liquidacionArriendo
JOIN
    mm_detalleLiquidacion ON mm_liquidacionArriendo.idLiquidacion = mm_detalleLiquidacion.idLiquidacion	
WHERE   mm_liquidacionArriendo.idProp=$idProp 
GROUP BY
    mm_liquidacionArriendo.idLiquidacion, mm_liquidacionArriendo.idArriendo, mm_liquidacionArriendo.idProp, mm_liquidacionArriendo.idPropietario, mm_liquidacionArriendo.idArrendatario, mm_liquidacionArriendo.fechaLiqui, mm_liquidacionArriendo.idDatosDep 
ORDER BY  mm_liquidacionArriendo.idLiquidacion desc
";
		 
 
		
		$tamCol=array(25,25,20,10);
		$campoFoto=array("foto"=>true);
		$filtrar1=array('buscador'=>'nombrePack');
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_liquidacionArriendo";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);			 
		$grid->desplegarDatosLiquidacionArriendo();
		return($sql);
	} 

	
	}

	
	public function tablaGastos($idProp){
		 
		$campoIndice="idgastos";
		$index="panel.php?m=1";
		$nomTabla="mm_gastos";
		$sql="select* from mm_gastos where idPropiedad='".$idProp."'";	
			
		$campos=array( 
					  'idenCuenta'=>'idenCuenta',
					  'tipo'=>'tipo',
					  'empresa'=>'empresa',
					  'infoContacto'=>'infoContacto');


		
		
		$tamCol=array(15,30,30,30);
		$campoFoto=array("foto"=>true);
		$filtrar1=array('buscador'=>'nombre');
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_gastos";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
		$grid->desplegarDatos();
		return($sql);
		
	}

	public function ingresarGastos(){
		if(isset($_GET["idProp"])){
			$idProp=htmlentities($_GET["idProp"]);
		}
		if(isset($_GET["idServ"])){
			$idServ=htmlentities($_GET["idServ"]);
		}		
		$this->miForm=new form();
		if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Se ha ingresado con exito!!!");}}
	
	
		$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
		$this->miForm->initCalendar();
		if($idServ==1){
			$this->controles("Nueva Cuenta de Agua");
		}else if($idServ==2){
			$this->controles("Nueva Cuenta de Luz");
		}else if($idServ==3){
			$this->controles("Nueva Cuenta de Gas");
		}
		
		//$this->miForm->addText("idArrendatario",550,"idArrendatario","idArrendatario:",false);
		
		$arrSel2=array(1=>"Agua","Luz","Gas","Gastos Comunes");


		
	//	$this->miForm->addSelect("tipo",$arrSel2,"Seleccione...",false,"Tipo",false);		
		
		if($idServ==1){
			$arraySel5 = array(1=>
				"Aguas Andinas",
				"Aguas Antofagasta",
				"Aguas Araucanía",
				"Aguas Chacabuco",
				"Aguas Cordillera",
				"Aguas Decima",
				"Aguas Los Guaicos",
				"Aguas Magallanes",
				"Aguas Manquehue",
				"Aguas Patagonia",
				"Aguas San Isidro",
				"Aguas San Pedro",
				"Aguas Santiago Poniente",
				"Aguas Sepra",
				"Aguas del Altiplano",
				"Aguas del Valle",
				"Essal",
				"Essbio",
				"Esval",
				"Nueva Atacama",
				"Nuevo Sur",
				"Otro",
				"Sacyr Agua Santiago",
				"Smapa"
			);
		}else if($idServ==2){
			$arraySel5 = array(1=>
				"CEC",
				"CGE",
				"Chilquinta",
				"Codiner",
				"Coopelan",
				"EEPA",
				"Edelaysen",
				"Edelmag",
				"Eléctrica Colina",
				"Enel",
				"Frontel",
				"Luz Casablanca",
				"Luz Linares",
				"Luz Litoral",
				"Luz Osorno",
				"Luz Parral",
				"Otro",
				"Saesa"
			);
		// luz
		}else if ($idServ==3){
		// gas
		$arraySel5 = array(1=>
			"Abastible",
			"Energas",
			"Gas Sur",
			"GasValpo",
			"Gasco",
			"Lipigas",
			"Metrogas",
			"Otro"
		);
		}else if($idServ==4){
		// gastos comunes
	}
	$this->miForm->addText("identificador",550,"Identificador de Cuenta","Identificador de cuenta:",false);
		$this->miForm->addSelect("empresa",$arraySel5,"Seleccione...",false,"Empresa",false);	
		$this->miForm->addTextarea("infoContacto",550,5,"infoContacto","InfoContacto:",false,0);		
		$this->miForm->addHidden("action","true");
		$this->miForm->addButton("Enviar","Agregar",false,false);
		$this->miForm->procesar();
		
		if($_POST["action"]){ $d=$_POST;
 
		$sql="insert into mm_gastos (
			idenCuenta,
			idPropiedad,
			tipo,
			empresa,			
			infoContacto
			) values (
				'".$d["identificador"]."',
				'".$idServ."',
				'".$idProp."',
				'".$d["empresa"]."',
				'".$d["infoContacto"]."')";
		 
		mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
			echo "<script>document.location='panel.php?op=22&idProp=".$idProp."&idServ=".$idServ."&msg=1';</script>";
		
		exit;
		}
		$this->miForm->cerrarForm();
		return($sql);

	}
	public function modificarGastos($idGasto){
		
	}


	public function tabla_mm_arriendos(){
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==2){$this->msgBox=new msgBox(1,"Agente ha sido eliminado con exito!!");}}
	if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
	
	if($op=="editar"){$id=htmlentities($_GET["idq"]);
		$this->modificar_mm_arriendos($id);
	}else if($op=="borrar"){
		$this->controles("Modificar Arriendos");
		$id=htmlentities($_GET["idq"]);
		
	 
		$sql3="delete from mm_arriendos where idArriendo='".$id."' ";	
		mysqli_query($this->link,$sql3);
		echo '<script>
		document.location="panel.php?op=2&msg=1";
		</script>';
		
		exit;
	}else{
		$this->controles("Lista de Arriendos");
		if(isset($_GET["msg"])){
			$m=$_GET["msg"];
			if($m==1){
				echo '<div class="alert alert-primary" style="padding:5px;font-size:14px;" role="alert" id="alert-message">
				<strong>¡Éxito!</strong> Arriendo se ha borrado con éxito.
       
			  </div>
			  <style>
				  #alert-message {
					  transition: opacity 1s ease;
					  opacity: 1;
					  display: block; /* Asegúrate de que el elemento esté visible */
				  }
				  #alert-message.fade {
					  opacity: 0;
					  height: 0; /* Elimina la altura */
					  padding: 0; /* Elimina el relleno */
					  margin: 0; /* Elimina el margen */
					  overflow: hidden; /* Oculta cualquier contenido */
				  }
			  </style>
			  <script>
				  document.addEventListener("DOMContentLoaded", function() {
					  setTimeout(function() {
						  document.getElementById("alert-message").classList.add("fade");
					  }, 3000); // 3000 ms = 3 segundos
				  });
			  </script>';
		
		

			}
		}
		$campoIndice="idArriendo";
		$index="panel.php?op=2";
		$nomTabla="mm_arriendos";
		$campoFoto=array("ruta"=>true);
		//$nomTablaFoto="mm_cape_fotos";
		$sql="select * from mm_arriendos order by idArriendo desc";
		$campos=array('Ficha'=>'idProp','Propiedad'=>'titulo','Arrendatario'=>'idArrendatarios','Propietario'=>'idPropietarios','fechaInicio'=>'fechaInicio','Precio'=>'montoArriendo');
		
		$tamCol=array(8,30,15,0,10,10);
		$campoFoto=array("foto"=>true);
		$filtrar1=array('buscador'=>'nombrePack');
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_arriendos";
		
		
	if(isset($_POST["action"])){
		$col=$_POST["columna"];
		$buscador=$_POST["buscador"];
		if($col==1){
			if (preg_match('/#(\d+)/', $buscador, $matches)) {
				$numero = $matches[1];
				echo $numero;  // Esto imprimirá "8"
			}
			$numero = str_replace("Ficha #", "", $buscador);			
			$sql="select * from mm_arriendos where idArriendo='".$numero."' order by idArriendo desc";
		}else if($col==2){
			// arrendatario
			$sql="select * from mm_arriendos where nombre='".$buscador."' order by idArriendo desc";
		}else if($col==3){
			//propietario
			$sql="select * from mm_arriendos order by idArriendo desc";
		}else{
			$sql="select * from mm_arriendos order by idArriendo desc";
		 
		}
	}else{
		
		$sql="select * from mm_arriendos order by idArriendo desc";
	 
	}
	$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
	echo "<form method='post' name='formBuscar' id='formBuscar'>";
	echo "<input type='hidden' name='action' id='action' value='true'/>";
	echo "<div style='margin-bottom:5px;'>";
	echo "<div class='row'>";
	
	echo "<div class='col-md-8'>
	 
	<div class='row'>
	<div class='col-md-1'>
	<span style='font-size:14px;'>Buscar:</span>
	</div>
	<div class='col-md-3'>
	<select name='columna' id='columna' class='form-select form-select-sm'>
			<option value='0' selected>Seleccione...</option>
			<option value='1'>Ficha</option>
	  
	</select>
	</div>
	<div class='col-md-3'>
	<input type='text' name='buscador' id='buscador' class='form-control form-control-sm' placeholder='Ingresa una palabra'/>
	</div>
	
	<div class='col-md-5'>
	<button id='buscarPropi' name='buscarPropi' class='btn btn-primary btn-sm' role='submit'> <i class='fas fa-search'></i> Buscar</button>
	</div>
	</div>
	</div>";
	echo "<div class='col-md-4'>&nbsp;</div>";
	echo "</div>";
	echo "</div>";
	echo "</form>";

		$grid->desplegarDatosArriendo();
	 
		return($sql);
	}

}





 
public function ingresar_mm_codeudor(){
	
	$this->miForm=new form();
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Codeudor ingresado con exito!!!");}}


	$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
	$this->miForm->initCalendar();
	$this->controles("Ingresar Codeudor","Arrendatarios","panel.php?op=4","fas fa-table");
	//$this->miForm->addText("idArrendatario",550,"idArrendatario","idArrendatario:",false);
	
	$arrSel2=array(1=>"Persona Natural","Persona Juridica");
	$this->miForm->addSelect("tipo",$arrSel2,"Seleccione...",false,"Tipo",false);		

	$this->miForm->addText("nombre",550,"Nombre","Nombre:",false);
	$this->miForm->addText("rut",550,"Rut","Rut:",false);
	$this->miForm->addEmail("email",550,"Email","Email:",false);
	$this->miForm->addTelefono("telefono",550,"Telefono","Telefono:",false);
	$this->miForm->addText("direccion",550,"Direccion","Direccion:",false);
 
 
//	$this->miForm->addCalendar("fechaInicio",550,"Fecha de inicio","Fecha de Inicio:",false);
//	$this->miForm->addCalendar("fechaTermino",550,"Fecha de Termino","Fecha de Termino:",false);
 
	$this->miForm->addTextarea("comentarios",550,5,"Comentarios","Comentarios:",false,0);
	
	$this->miForm->addHidden("action","true");
	$this->miForm->addButton("Enviar","Agregar",false,false);
	$this->miForm->procesar();
	
	if($_POST["action"]){ $d=$_POST;
 
	
	$sql="insert into mm_codeudor (
		tipo,
		nombre,
		rut,
		email
		,telefono,
		direccion,
	 
		comentarios) values (
			'".$d["tipo"]."',
			'".$d["nombre"]."',
			'".$d["rut"]."',
			'".$d["email"]."',
			'".$d["telefono"]."',
			'".$d["direccion"]."',
	 
			'".$d["comentarios"]."')";
	
	mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		echo "<script>document.location='panel.php?op=17&msg=1';</script>";
	exit;
	}
	$this->miForm->cerrarForm();
	return($sql);
	}
	
	
	public function modificar_mm_codeudor($id=false){
	$this->link=$this->conectar();
	if(isset($_GET["idq"])){ $id=htmlentities($_GET["idq"]); }
	$sql="select* from mm_codeudor where idCodeudor='".$id."'";
	 
	$q=mysqli_query($this->link,$sql);
	$row=mysqli_fetch_array($q);
 	
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Cambios guardados con  exito!!!");}}
 
	$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
	$this->miForm->initCalendar();
	
	$arrSel2=array(1=>"Persona Natural","Persona Juridica");
	$this->miForm->addSelect("tipo",$arrSel2,"Seleccione...",false,"Tipo",$row["tipo"]);		

	$this->miForm->addText("nombre",550,"Nombre","Nombre:",$row["nombre"]);
	$this->miForm->addText("rut",550,"Rut","Rut:",$row["rut"]);
	$this->miForm->addEmail("email",550,"Email","Email:",$row["email"]);
	$this->miForm->addTelefono("telefono",550,"Telefono","Telefono:",$row["telefono"]);
	$this->miForm->addText("direccion",550,"Direccion","Direccion:",$row["direccion"]);
  
	$this->miForm->addTextarea("comentarios",550,5,"Comentarios","Comentarios:",$row["comentarios"],0);

 
	$this->miForm->addHidden("action","true"); $this->miForm->addButton("Enviar","Guardar Cambios",false,false);
	$this->miForm->procesar();
	
	if($_POST["action"]){ $d=$_POST;
	 
	if(isset($_POST["borrar"])){
	$d["foto"]="";
	}
	
	$sql="update mm_codeudor set 
		tipo='".$d["tipo"]."',
		nombre='".$d["nombre"]."',
		rut='".$d["rut"]."',
		email='".$d["email"]."',
		telefono='".$d["telefono"]."',
		direccion='".$d["direccion"]."',
	 
		comentarios='".$d["comentarios"]."' where idcodeudor='".$id."'";
	
		mysqli_query($this->link,$sql) or die(mysql_error($this->link));
			echo "<script>document.location='panel.php?op=18&mq=editar&idq=".$id."&msg=1';</script>";
		exit;
	}
	$this->miForm->cerrarForm();
	return($sql);
	}
	
	
	
	public function tabla_mm_codeudor(){ 
	$this->link=$this->conectar();
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==2){$this->msgBox=new msgBox(1,"Agente ha sido eliminado con exito!!");}}
	if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
	
	if($op=="editar"){$id=htmlentities($_GET["idq"]);
		$this->controles("Modificar codeudores");
	$this->modificar_mm_codeudor($id);
	}else if($op=="borrar"){
	$id=htmlentities($_GET["idq"]);
	$sql3="delete from mm_codeudor where idCodeudor='".$id."'";
	
	mysqli_query($this->link,$sql3);
		echo "<script>document.location='panel.php?op=18&msg=1';</script>";
	exit;
	}else{
		$this->controles("Listado de codeudores");
	$campoIndice="idCodeudor";
	$index="panel.php?op=18";
	
	$campoFoto=array("ruta"=>true);
	//$nomTablaFoto="mm_cape_fotos";
	$sql="select * from mm_codeudor order by idCodeudor desc";
	
	$campos=array(
				'nombre'=>'nombre',
				'rut'=>'rut',
				'email'=>'email',
				'telefono'=>'telefono',
				'direccion'=>'direccion'
				//'fechaPago'=>'fechaPago',
			//	'montoArriendo'=>'montoArriendo'
			);

	//$tipo=array(1=>"Casas",2=>"Departamentos",3=>"Parcelas",4=>"Sitios",5=>"Oficina Comercial",6=>"Propiedad Industrial",7=>"Terreno");
	$tamCol=array(39,10,20,20);
	$campoFoto=array("foto"=>true);
	$filtrar1=array('buscador'=>'nombrePack');
	
	$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
	$tabla="mm_codeudor";
	

	if(isset($_POST["action"])){
		$col=$_POST["columna"];
		$buscador=$_POST["buscador"];
		if($col==1){
	// rut
			$sql="select * from mm_codeudor where rut='".$buscador."' order by idCodeudor desc";
		}else if($col==2){
			// email
			$sql="select * from mm_codeudor where email='".$buscador."' order by idCodeudor desc";
		}else if($col==3){
			//telefono
			$sql="select * from mm_codeudor where telefono='".$buscador."' order by idCodeudor desc"; 
		}else{
			$sql="select * from mm_codeudor order by idCodeudor desc";
		 
		}
	}else{
		
		$sql="select * from mm_codeudor order by idCodeudor desc";
	 
	}
	$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
	echo "<form method='post' name='formBuscar' id='formBuscar'>";
	echo "<input type='hidden' name='action' id='action' value='true'/>";
	echo "<div style='margin-bottom:5px;'>";
	echo "<div class='row'>";
	
	echo "<div class='col-md-8'>
	 
	<div class='row'>
	<div class='col-md-1'>
	<span style='font-size:14px;'>Buscar:</span>
	</div>
	<div class='col-md-3'>
	<select name='columna' id='columna' class='form-select form-select-sm'>
			<option value='0' selected>Seleccione...</option>
			<option value='1'>Rut</option>
	 
			<option value='2'>Email</option>
			<option value='3'>Telefono</option>
	</select>
	</div>
	<div class='col-md-3'>
	<input type='text' name='buscador' id='buscador' class='form-control form-control-sm' placeholder='Ingresa una palabra'/>
	</div>
	
	<div class='col-md-5'>
	<button id='buscarPropi' name='buscarPropi' class='btn btn-primary btn-sm' role='submit'> <i class='fas fa-search'></i> Buscar</button>
	</div>
	</div>
	</div>";
	echo "<div class='col-md-4'>&nbsp;</div>";
	echo "</div>";
	echo "</div>";
	echo "</form>";

	$grid->desplegarDatosCodeudor();

	return($sql);
	} }
 
	public function ingresar_mm_arrendatario(){
	
	$this->miForm=new form();
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Arrendatario ingresado con exito!!!");}}


	$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
	$this->miForm->initCalendar();
	$this->controles("Ingresar Arrendatario","Arrendatarios","panel.php?op=4","fas fa-table");
	//$this->miForm->addText("idArrendatario",550,"idArrendatario","idArrendatario:",false);
	
	$arrSel2=array(1=>"Persona Natural","Persona Juridica");
	$this->miForm->addSelect("tipo",$arrSel2,"Seleccione...",false,"Tipo",false);		

	$this->miForm->addText("nombre",550,"Nombre","Nombre:",false);
	$this->miForm->addText("rut",550,"Rut","Rut:",false);
	$this->miForm->addEmail("email",550,"Email","Email:",false);
	$this->miForm->addTelefono("telefono",550,"Telefono","Telefono:",false);
	$this->miForm->addText("direccion",550,"Direccion","Direccion:",false);
 
 
//	$this->miForm->addCalendar("fechaInicio",550,"Fecha de inicio","Fecha de Inicio:",false);
//	$this->miForm->addCalendar("fechaTermino",550,"Fecha de Termino","Fecha de Termino:",false);
	
	//$arrSel=array(1=>"Vigente","Finalizado","Cancelado");
	//$this->miForm->addSelect("estadoArriendo",$arrSel,"Seleccione...",false,"Estado",true);
	
	//$this->miForm->addCalendar("fechaPago",550,"Fecha de Pago","Fecha de Pago:",false);
	//$this->miForm->addText("montoArriendo",550,"Monto del arriendo","Monto del arriendo:",false);
	$this->miForm->addTextarea("comentarios",550,5,"Comentarios","Comentarios:",false,0);
	
	$this->miForm->addHidden("action","true");
	$this->miForm->addButton("Enviar","Agregar",false,false);
	$this->miForm->procesar();
	
	if($_POST["action"]){ $d=$_POST;
 
	
	$sql="insert into mm_arrendatario (
		tipo,
		nombre,
		rut,
		email
		,telefono,
		direccion,
		comentarios) values (
			'".$d["tipo"]."',
			'".$d["nombre"]."',
			'".$d["rut"]."',
			'".$d["email"]."',
			'".$d["telefono"]."',
			'".$d["direccion"]."',			 
			'".$d["comentarios"]."')";
 
	mysqli_query($this->link,$sql);
		echo "<script>document.location='panel.php?op=6&msg=1';</script>";
	exit;
	}
	$this->miForm->cerrarForm();
	return($sql);
	}
	
	
	public function modificar_mm_arrendatario($id=false){
	$this->link=$this->conectar();
	if(isset($_GET["idq"])){ $id=htmlentities($_GET["idq"]); }
	$sql="select* from mm_arrendatario where idArrendatario='".$id."'";
	
	$q=mysqli_query($this->link,$sql);
	$row=mysqli_fetch_array($q);
 	
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Cambios guardados con exito!!!");}}

	$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
	$this->miForm->initCalendar();
	
	$arrSel2=array(1=>"Persona Natural","Persona Juridica");
	$this->miForm->addSelect("tipo",$arrSel2,"Seleccione...",false,"Tipo",$row["tipo"]);		

	$this->miForm->addText("nombre",550,"Nombre","Nombre:",$row["nombre"]);
	$this->miForm->addText("rut",550,"Rut","Rut:",$row["rut"]);
	$this->miForm->addEmail("email",550,"Email","Email:",$row["email"]);
	$this->miForm->addTelefono("telefono",550,"Telefono","Telefono:",$row["telefono"]);
	$this->miForm->addText("direccion",550,"Direccion","Direccion:",$row["direccion"]);
 
 
	$this->miForm->addCalendar("fechaInicio",550,"Fecha de inicio","Fecha de Inicio:",date("d/m/Y",$row["fechaInicio"]));
	$this->miForm->addCalendar("fechaTermino",550,"Fecha de Termino","Fecha de Termino:",date("d/m/Y",$row["fechaTermino"]));
	
	$arrSel=array(1=>"Vigente","Finalizado","Cancelado");
	$this->miForm->addSelect("estadoArriendo",$arrSel,"Seleccione...",false,"Estado arriendo",$row["estadoArriendo"]);
	echo $row["estadoArriendo"];
	$this->miForm->addCalendar("fechaPago",550,"Fecha de Pago","Fecha de Pago:",date("d/m/Y",$row["fechaPago"]));
	$this->miForm->addText("montoArriendo",550,"Monto del arriendo","Monto del arriendo:",$row["montoArriendo"]);
	$this->miForm->addTextarea("comentarios",550,5,"Comentarios","Comentarios:",$row["comentarios"],0);

 
	$this->miForm->addHidden("action","true"); $this->miForm->addButton("Enviar","Guardar Cambios",false,false);
	$this->miForm->procesar();
	
	if($_POST["action"]){ $d=$_POST;
	 
	if(isset($_POST["borrar"])){
	$d["foto"]="";
	}
	
	$sql="update mm_arrendatario set 
		tipo='".$d["tipo"]."',
		nombre='".$d["nombre"]."',
		rut='".$d["rut"]."',
		email='".$d["email"]."',
		telefono='".$d["telefono"]."',
		direccion='".$d["direccion"]."',
		fechaInicio='".strtotime($d["fechaInicio"])."',
		fechaTermino='".strtotime($d["fechaTermino"])."',
		estadoArriendo='".$d["estadoArriendo"]."',
		fechaPago='".strtotime($d["fechaPago"])."',
		montoArriendo='".$d["montoArriendo"]."',
		comentarios='".$d["comentarios"]."' where idArrendatario='".$id."'";
	
		mysqli_query($this->link,$sql) or die(mysql_error($this->link));
			echo "<script>document.location='panel.php?op=4&mq=editar&idq=".$id."&msg=1';</script>";
		exit;
	}
	$this->miForm->cerrarForm();
	return($sql);
	}
	
	
	
	public function tabla_mm_arrendatario(){ 
	$this->link=$this->conectar();
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==2){$this->msgBox=new msgBox(1,"Agente ha sido eliminado con exito!!");}}
	if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}

	if($op=="editar"){
		$this->controles("Modificar Arrendatarios","Crear Arrendatario","panel.php?op=6","fas fa-user-plus");
		$id=htmlentities($_GET["idq"]);
		$this->modificar_mm_arrendatario($id);
	}else if($op=="borrar"){
	$id=htmlentities($_GET["idq"]);
	$sql3="delete from mm_arrendatario where idArrendatario='".$id."'";
	
	mysqli_query($this->link,$sql3);
		echo "<script>document.location='panel.php?op=4&msg=1';</script>";
	exit;
	}else{
		$this->controles("Listado de Arrendatarios","Crear Arrendatario","panel.php?op=6","fas fa-user-plus");
		$campoIndice="idArrendatario";
		$index="panel.php?op=4";	
		$campoFoto=array("ruta"=>true);
		//$nomTablaFoto="mm_cape_fotos";
	 
	  
 
	$campos=array(
				'Nombre'=>'Nombre',
				'Propiedades'=>'idProp',
				'Propietario'=>'idPropietarios',
				'Rut'=>'rut',
				'Email'=>'email',
				//'Telefono'=>'telefono',
				'Direccion'=>'direccion'
				//'fechaPago'=>'fechaPago',
			//	'montoArriendo'=>'montoArriendo'
			);

	//$tipo=array(1=>"Casas",2=>"Departamentos",3=>"Parcelas",4=>"Sitios",5=>"Oficina Comercial",6=>"Propiedad Industrial",7=>"Terreno");
	$tamCol=array(20,15,10,10,15);
	$campoFoto=array("foto"=>true);
	$filtrar1=array('buscador'=>'nombrePack');

	
	$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
	$tabla="mm_arrendatario";
		
	if(isset($_POST["action"])){
		$col=$_POST["columna"];
		$buscador=$_POST["buscador"];
		if($col==1){
			// nombre del arrendatario
			$sql=" SELECT mm_arriendos.idProp, mm_arriendos.idPropietarios, mm_arrendatario.idArrendatario, mm_arrendatario.Email, mm_arrendatario.Nombre, mm_arrendatario.Rut, mm_arrendatario.Telefono, mm_arrendatario.Direccion ";
			$sql.=" FROM mm_arriendos ";
			$sql.="	JOIN mm_arrendatario ON mm_arriendos.idArrendatarios = mm_arrendatario.idArrendatario where mm_arrendatario.Nombre ='".$buscador."' order by mm_arriendos.idProp desc ";
	 
		 
		}else if($col==2){
			// propiedad
		
		}else if($col==3){
			//propietario
		 
		}else if($col==4){
			//rut
			$sql=" SELECT mm_arriendos.idProp, mm_arriendos.idPropietarios, mm_arrendatario.idArrendatario, mm_arrendatario.Email, mm_arrendatario.Nombre, mm_arrendatario.Rut, mm_arrendatario.Telefono, mm_arrendatario.Direccion ";
			$sql.=" FROM mm_arriendos ";
			$sql.="	JOIN mm_arrendatario ON mm_arriendos.idArrendatarios = mm_arrendatario.idArrendatario where mm_arrendatario.Rut ='".$buscador."' order by mm_arriendos.idProp desc ";
			
		}else if($col==5){
			//email
			$sql=" SELECT mm_arriendos.idProp, mm_arriendos.idPropietarios, mm_arrendatario.idArrendatario, mm_arrendatario.Email, mm_arrendatario.Nombre, mm_arrendatario.Rut, mm_arrendatario.Telefono, mm_arrendatario.Direccion ";
			$sql.=" FROM mm_arriendos ";
			$sql.="	JOIN mm_arrendatario ON mm_arriendos.idArrendatarios = mm_arrendatario.idArrendatario where mm_arrendatario.Email ='".$buscador."' order by mm_arriendos.idProp desc ";
			
		}else{
	
			$sql=" SELECT mm_arriendos.idProp, mm_arriendos.idPropietarios, mm_arrendatario.idArrendatario, mm_arrendatario.Email, mm_arrendatario.Nombre, mm_arrendatario.Rut, mm_arrendatario.Telefono, mm_arrendatario.Direccion ";
			$sql.=" FROM mm_arriendos ";
			$sql.="	JOIN mm_arrendatario ON mm_arriendos.idArrendatarios = mm_arrendatario.idArrendatario order by mm_arriendos.idProp desc ";
			
		 
		}
	}else{
	/*	
		$sql=" SELECT mm_arriendos.idProp, mm_arriendos.idPropietarios, mm_arrendatario.idArrendatario, mm_arrendatario.Email, mm_arrendatario.Nombre, mm_arrendatario.Rut, mm_arrendatario.Telefono, mm_arrendatario.Direccion ";
		$sql.=" FROM mm_arriendos ";
		$sql.="	JOIN mm_arrendatario ON mm_arriendos.idArrendatarios = mm_arrendatario.idArrendatario order by mm_arriendos.idProp desc ";
	*/
	$sql="
	SELECT 
    mm_arriendos.idProp, 
    mm_arriendos.idPropietarios, 
    mm_arrendatario.idArrendatario, 
    mm_arrendatario.Email, 
    mm_arrendatario.Nombre, 
    mm_arrendatario.Rut, 
    mm_arrendatario.Telefono, 
    mm_arrendatario.Direccion 
FROM 
    mm_arrendatario 
LEFT JOIN 
    mm_arriendos ON mm_arriendos.idArrendatarios = mm_arrendatario.idArrendatario 
ORDER BY 
    mm_arrendatario.idArrendatario DESC ";	
	 
	}

	if($msg==1){
		echo '<div class="alert alert-primary" style="padding:5px;font-size:14px;" role="alert" id="alert-message">
		<strong>¡Éxito!</strong> Arrendatario borrado con exito.

	  </div>
	  <style>
		  #alert-message {
			  transition: opacity 1s ease;
			  opacity: 1;
			  display: block; /* Asegúrate de que el elemento esté visible */
		  }
		  #alert-message.fade {
			  opacity: 0;
			  height: 0; /* Elimina la altura */
			  padding: 0; /* Elimina el relleno */
			  margin: 0; /* Elimina el margen */
			  overflow: hidden; /* Oculta cualquier contenido */
		  }
	  </style>
	  <script>
		  document.addEventListener("DOMContentLoaded", function() {
			  setTimeout(function() {
				  document.getElementById("alert-message").classList.add("fade");
			  }, 3000); // 3000 ms = 3 segundos
		  });
	  </script>';
	   }
 
	$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
	echo "<form method='post' name='formBuscar' id='formBuscar'>";
	echo "<input type='hidden' name='action' id='action' value='true'/>";
	echo "<div style='margin-bottom:5px;'>";
	echo "<div class='row'>";
	
	echo "<div class='col-md-8'>
	 
	<div class='row'>
	<div class='col-md-1'>
	<span style='font-size:14px;'>Buscar:</span>
	</div>
	<div class='col-md-3'>
	<select name='columna' id='columna' class='form-select form-select-sm'>
			<option value='0' selected>Seleccione...</option>
			<option value='1'>Nombre del Arrendatario</option>
	 
			<option value='4'>Rut</option>
			<option value='5'>Email</option>
	</select>
	</div>
	<div class='col-md-3'>
	<input type='text' name='buscador' id='buscador' class='form-control form-control-sm' placeholder='Ingresa una palabra'/>
	</div>
	
	<div class='col-md-5'>
	<button id='buscarPropi' name='buscarPropi' class='btn btn-primary btn-sm' role='submit'> <i class='fas fa-search'></i> Buscar</button>
	</div>
	</div>
	</div>";
	echo "<div class='col-md-4'>&nbsp;</div>";
	echo "</div>";
	echo "</div>";
	echo "</form>";



	$grid->desplegarDatosArrendatarios();

	return($sql);
	} }
	
	
	
	public function devolverTipoPropi($id){
		$arrSel2=array(1=>"Persona Natural","Persona Juridica");
		return($arrSel2[$id]);
	}
	public function ingresar_mm_propietarios(){

		if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Propietario ingresado con exito!!!");}}
		$this->miForm->abrirForm(1,true,"form1","post","proceso.php",2);
		//$this->miForm->addText("idPropietario",550,"idPropietario","idPropietario:",false);
		$this->controles("Ingresar Propietarios","Propietarios","panel.php?op=3","fas fa-table");
		$this->miForm->initCalendar();
		$arrSel2=array(1=>"Persona Natural","Persona Juridica");
		$this->miForm->addSelect("tipo",$arrSel2,"Seleccione...",false,"Tipo",false);		

		$this->miForm->addText("nombre",550,"Nombre","Nombre:",false);
		$this->miForm->addText("apellido",550,"Apellido","Apellido:",$row["apellido"]);
		$this->miForm->addText("rut",550,"Rut","Rut:",false);
		$this->miForm->addEmail("email",550,"Email","Email:",false);
		$this->miForm->addTelefono("telefono",550,"telefono","telefono:",false);			
		$this->miForm->addText("empresa",550,"Empresa","Empresa:",false);
		$this->miForm->addText("direccion",550,"Direccion","Direccion:",false);				
		
		$this->miForm->addTextarea("comentarios",550,5,"Comentarios","Comentarios:",false,$row["comentarios"]);	
		$this->miForm->addHidden("action","true");
		$this->miForm->addButton("Enviar","Agregar",false,false);
		$this->miForm->procesar();	 
		if($_POST["action"]){ $d=$_POST;
			$sql="insert into mm_propietarios (
				nombre,
				apellido,
				tipo,
				rut,
				email,
				telefono,
				direccion,				
				comentarios,
				banco,
				tipoCuenta,
				empresa) values (
					'".$d["nombre"]."',
					'".$d["apellido"]."',
					'".$d["tipo"]."',
					'".$d["rut"]."',
					'".$d["email"]."',
					'".$d["telefono"]."',
					'".$d["direccion"]."',										
					'".$d["comentarios"]."',
					'".$d["banco"]."',
					'".$d["tipoCuenta"]."',
					'".$d["empresa"]."')";		

				mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
					echo "<script>document.location='panel.php?op=12&msg=1';</script>";
				exit;
		}

			$this->miForm->cerrarForm();
			return($sql);
		}
		
		
		public function devolverTipoPropietario($id){
			if($id==1){
				$k="Persona Natural";
			}else{
				$k="Persona Juridica";
			}
			return($k);
		}

		public function modificar_mm_cuentaBancaria($tipo){

			$this->link=$this->conectar();
			if($tipo==0){
				if(isset($_GET["idPropi"])){ $idPropi=htmlentities($_GET["idPropi"]); }
			}else if($tipo==1){
				if(isset($_GET["idArrendatario"])){ $idArrendatario=htmlentities($_GET["idArrendatario"]); }
			}else if($tipo==2){
				if(isset($_GET["idCodeudor"])){ $idCodeudor=htmlentities($_GET["idCodeudor"]); }
			}
			if(isset($_GET["idPropi"])){
				$sql="select* from mm_cuentaBancaria where idPropietario='".$idPropi."'";
				if($_GET["act"]=="edit"){
					$this->controles("Modificar cuenta bancaria propietario");
				}
				$q=mysqli_query($this->link,$sql);
				$r=mysqli_fetch_array($q);
			}else if(isset($_GET["idArrendatario"])){
				$sql="select* from mm_cuentaBancaria where idArrendatario='".$idArrendatario."'";
				$this->controles("Modificar cuenta bancaria arrendatario");
				$q=mysqli_query($this->link,$sql);
				$r=mysqli_fetch_array($q);
			}else if(isset($_GET["idCodeudor"])){

				$sql="select* from mm_cuentaBancaria where idCodeudor='".$idCodeudor."'";
				$this->controles("Modificar cuenta bancaria codeudor");
				$q=mysqli_query($this->link,$sql);
				$r=mysqli_fetch_array($q);
			}
			

			

	
			
			if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Cambios guardados con exito!!!");}}
			$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
			$arrSel = array(1=>
			"Banco De Chile - Edwards",
			"Banco Bice",
			"Banco Consorcio",
			"Banco del Estado de Chile",
			"Banco Do Brasil S.A.",
			"Banco Falabella",
			"Banco Internacional",
			"Banco Paris",
			"Banco Ripley",
			"Banco Santander",
			"Santander Office Banking",
			"Banco Security",
			"Banco Coopeuch",
			"Citibank",
			"BBVA",
			"BCI",
			"HSBC Bank",
			"Itau",
			"Itau-Corpbanca",
			"Scotiabank"
		);
		$this->miForm->addSelect("banco",$arrSel,"Seleccione...",false,"Banco",$r["banco"]);

		$arrSel1=array(1=>"Cuenta Corriente","Cuenta Vista","Cuenta de Ahorro");
		$this->miForm->addSelect("tipoCuenta",$arrSel1,"Seleccione...",false,"Tipo de Cuenta",$r["tipoCuenta"]);

		$this->miForm->addText("numeroCuenta",550,"Numero de cuenta","Numero de cuenta:",$r["numeroCuenta"]);
		$this->miForm->addText("email",550,"Email","Email:",$r["email"]);
		$this->miForm->addText("nomTitular",550,"Nombre del titular","Nombre del titular:",$r["nomTitular"]);
		$this->miForm->addText("rutTitular",550,"Rut del titular","Rut del titular:",$r["rutTitular"]);

			$this->miForm->addHidden("action","true"); 
			$this->miForm->addButton("Enviar","Guardar Cambios",false,false);
			$this->miForm->procesar();
			
			if($_POST["action"]){ $d=$_POST;
			
			if($tipo==0){
				$sql="update mm_cuentaBancaria set 				
				banco='".$d["banco"]."',
				tipoCuenta='".$d["tipoCuenta"]."',
				numeroCuenta='".$d["numeroCuenta"]."',
				email='".$d["email"]."',
				nomTitular='".$d["nomTitular"]."',
				rutTitular='".$d["rutTitular"]."' where idPropietario='".$idPropi."'";
				mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
				if($_GET["op"]==21){					
						echo "<script>document.location='panel.php?op=21&act=edit&idPropi=".$idPropi."&msg=1';</script>";
				}else{
						echo "<script>document.location='panel.php?op=1&msg=1';</script>";	
				}
				
			}else if($tipo==1){
				$sql="update mm_cuentaBancaria set 				
				banco='".$d["banco"]."',
				tipoCuenta='".$d["tipoCuenta"]."',
				numeroCuenta='".$d["numeroCuenta"]."',
				email='".$d["email"]."',
				nomTitular='".$d["nomTitular"]."',
				rutTitular='".$d["rutTitular"]."' where idArrendatario='".$idArrendatario."'";
				mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
				if($_GET["op"]==21){					
						echo "<script>document.location='panel.php?op=21&act=edit&idPropi=".$idPropi."&msg=1';</script>";
				}else{
						echo "<script>document.location='panel.php?op=4&msg=1';</script>";
				}
				
			}else if($tipo==2){
				$sql="update mm_cuentaBancaria set 				
				banco='".$d["banco"]."',
				tipoCuenta='".$d["tipoCuenta"]."',
				numeroCuenta='".$d["numeroCuenta"]."',
				email='".$d["email"]."',
				nomTitular='".$d["nomTitular"]."',
				rutTitular='".$d["rutTitular"]."' where idCodeudor='".$idCodeudor."'";
				mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
				
						echo "<script>document.location='panel.php?op=256&act=edit&idCodeudor=".$idCodeudor."&msg=1';</script>";
				
			}
			
			
			
			exit;
			}
			$this->miForm->cerrarForm();
			return($sql);
			}

		 

			public function tabla_mm_cuentaBancaria($tipo){			
				if($tipo==0){
					if(isset($_GET["idPropi"])){
						$idPropi=htmlentities($_GET["idPropi"]);
					}
				}else{
					if(isset($_GET["idArrendatario"])){
						$idArrendatario=htmlentities($_GET["idArrendatario"]);
					}
				}
				
				if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==2){$this->msgBox=new msgBox(1,"Agente ha sido eliminado con exito!!");}}
				if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
				if($op=="editar"){$id=htmlentities($_GET["idq"]);
				$this->modificar_mm_cuentaBancaria($id);
				}else if($op=="borrar"){
				$id=htmlentities($_GET["idq"]);
				$sql3="delete from mm_cuentaBancaria where idPropietario='".$idPropi."'";
				
				mysql_query($sql3);
					echo "<script>document.location='panel.php?m=1&msg=1';</script>";
				exit;
				}else{
				$campoIndice="idCuenta";
				$index="panel.php?m=1";
				$nomTabla="mm_cuentaBancaria";
				$campoFoto=array("ruta"=>true);
				//$nomTablaFoto="mm_cape_fotos";
				if($tipo==0){
					$sql="select * from mm_cuentaBancaria where idPropietario='".$idPropi."' and tipo=0";
				}else{
					$sql="select * from mm_cuentaBancaria where idArrendatario='".$idArrendatario."' and tipo=1";
				}
				
				
				$campos=array('banco'=>'Banco',
				'tipoCuenta'=>'Tipo de Cta.',
				'numeroCuenta'=>'N° de Cta.',
				'nomTitular'=>'Titular',
				'rutTitular'=>'Rut Titular');
				//$tipo=array(1=>"Casas",2=>"Departamentos",3=>"Parcelas",4=>"Sitios",5=>"Oficina Comercial",6=>"Propiedad Industrial",7=>"Terreno");
				$tamCol=array(79,20,20,20);
				$campoFoto=array("foto"=>true);
				$filtrar1=array('buscador'=>'nombre');
				$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
				$tabla="mm_cuentaBancaria";
				$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
				$grid->desplegarDatos();
				return($sql);
				} }

				


				public function ingresar_mm_cuentaBancaria($tipo){
					if(isset($_GET["idPropi"])){
						$idPropi=htmlentities($_GET["idPropi"]);
					}
					if($tipo==0){
						// propietario
						$this->controles("Ingresar Cuenta Bancaria Propietario");
					}else if($tipo==1){
						$this->controles("Ingresar Cuenta Bancaria Arrendatario");
					}else if($tipo==2){
						$this->controles("Ingresar Cuenta Bancaria Codeudor");
					}
					
					$this->link=$this->conectar();
					if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Cuenta ingresada con exito!!!");}}
					$this->miForm->abrirForm(1,true,"form1","post","proceso.php",2);
					//$this->miForm->addText("idCuenta",550,"idCuenta","idCuenta:",false);			
				 
					
					$arrSel = array(1=>
						"Banco De Chile - Edwards",
						"Banco Bice",
						"Banco Consorcio",
						"Banco del Estado de Chile",
						"Banco Do Brasil S.A.",
						"Banco Falabella",
						"Banco Internacional",
						"Banco Paris",
						"Banco Ripley",
						"Banco Santander",
						"Santander Office Banking",
						"Banco Security",
						"Banco Coopeuch",
						"Citibank",
						"BBVA",
						"BCI",
						"HSBC Bank",
						"Itau",
						"Itau-Corpbanca",
						"Scotiabank"
					);
					$this->miForm->addSelect("banco",$arrSel,"Seleccione...",false,"Banco",false);
		
					$arrSel1=array(1=>"Cuenta Corriente","Cuenta Vista","Cuenta de Ahorro");
					$this->miForm->addSelect("tipoCuenta",$arrSel1,"Seleccione...",false,"Tipo de Cuenta",false);
		
					$this->miForm->addText("numeroCuenta",550,"Numero de cuenta","Numero de cuenta:",false);
					$this->miForm->addText("email",550,"Email","Email:",false);
					$this->miForm->addText("nomTitular",550,"Nombre del titular","Nombre del titular:",false);
					$this->miForm->addText("rutTitular",550,"Rut del titular","Rut del titular:",false);
					$this->miForm->addHidden("action","true");
					$this->miForm->addButton("Enviar","Agregar cuenta bancaria",false,false);
					$this->miForm->procesar();
					 
					if($_POST["action"]){ $d=$_POST;
				 
					if($tipo==0){
					$sql="insert into mm_cuentaBancaria (
						idPropietario,
						banco,
						tipoCuenta,
						numeroCuenta,
						email,
						nomTitular,
						rutTitular,tipo) values ('".$idPropi."','".$d["banco"]."','".$d["tipoCuenta"]."','".$d["numeroCuenta"]."','".$d["email"]."','".$d["nomTitular"]."','".$d["rutTitular"]."','0')";
						mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
							echo "<script>document.location='panel.php?op=21&idPropi=".$idPropi."&msg=1';</script>";						
					}else if($tipo==1){
						if(isset($_GET["idArrendatario"])){
							$idArrendatario=htmlentities($_GET["idArrendatario"]);
						}
						$sql="insert into mm_cuentaBancaria (
							idArrendatario,
							banco,
							tipoCuenta,
							numeroCuenta,
							email,
							nomTitular,
							rutTitular,tipo) values ('".$idArrendatario."','".$d["banco"]."','".$d["tipoCuenta"]."','".$d["numeroCuenta"]."','".$d["email"]."','".$d["nomTitular"]."','".$d["rutTitular"]."','1')";
						 
							mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
								echo "<script>document.location='panel.php?op=225&idArrendatario=".$idArrendatario."&msg=1';</script>";
					}else if($tipo==2){
						if(isset($_GET["idCodeudor"])){
							$idCodeudor=htmlentities($_GET["idCodeudor"]);
						}
						$sql="insert into mm_cuentaBancaria (
							idCodeudor,
							banco,
							tipoCuenta,
							numeroCuenta,
							email,
							nomTitular,
							rutTitular,tipo) values ('".$idCodeudor."','".$d["banco"]."','".$d["tipoCuenta"]."','".$d["numeroCuenta"]."','".$d["email"]."','".$d["nomTitular"]."','".$d["rutTitular"]."','1')";
						 
							mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
								echo "<script>document.location='panel.php?op=226&idCodeudor=".$idCodeudor."&msg=1';</script>";
					}
				
					exit;
					}
					$this->miForm->cerrarForm();
					return($sql);
					}
					

		public function modificar_mm_propietarios($id=false){		
			$this->link=$this->conectar();
		if(isset($_GET["idq"])){ $id=htmlentities($_GET["idq"]); }
		$sql="select* from mm_propietarios where idPropietario='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$row=mysqli_fetch_array($q);
	
		
		if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Cambios guardados con exito!!!");}}
		$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);		
 
		$this->miForm->initCalendar();
		$arrSel2=array(1=>"Persona Natural","Persona Juridica");
		$this->miForm->addSelect("tipo",$arrSel2,"Seleccione...",false,"Tipo",$row["tipo"]);		

		$this->miForm->addText("nombre",550,"Nombre","Nombre:",$row["nombre"]);
		$this->miForm->addText("apellido",550,"Apellido","Apellido:",$row["apellido"]);
		$this->miForm->addText("rut",550,"Rut","Rut:",$row["rut"]);
		$this->miForm->addEmail("email",550,"Email","Email:",$row["email"]);
		$this->miForm->addTelefono("telefono",550,"Telefono Fijo","Telefono Fijo:",$row["telefono"]);			
		$this->miForm->addText("empresa",550,"Empresa","Empresa:",$row["empresa"]);
		$this->miForm->addText("direccion",550,"Direccion","Direccion:",$row["direccion"]);			
	 
		$this->miForm->addTextarea("comentarios",550,5,"Comentarios","Comentarios:",$row["comentarios"],0);	
		$this->miForm->addHidden("action","true");
		$this->miForm->addButton("Enviar","Guardar Cambios",false,false);
		$this->miForm->procesar();	 
		if($_POST["action"]){ $d=$_POST;			
			$sql="update mm_propietarios set 
			nombre='".$d["nombre"]."',
			apellido='".$d["apellido"]."',
			tipo='".$d["tipo"]."',
			rut='".$d["rut"]."',
			email='".$d["email"]."',
			telefono='".$d["telefono"]."',
			direccion='".$d["direccion"]."',			
			comentarios='".$d["comentarios"]."',
			banco='".$d["banco"]."',
			tipoCuenta='".$d["tipoCuenta"]."',		

			empresa='".$d["empresa"]."' where idPropietario='".$id."'";	
			
		 
			mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
				echo "<script>document.location='panel.php?op=3&mq=editar&idq=".$id."&msg=1';</script>";
			exit;
		}
			$this->miForm->cerrarForm();
			return($sql); 
		}

	public function controles($titulo,$nomBoton=false,$target=false,$icono=false,$banco=false,$target2=false,$icono2=false){
		echo '<div class="row">';
		if($banco==true){
			echo "<div class='col-md-8'>";
		}else{
			echo "<div class='col-md-10'>";
		}
	echo "<div style='margin-bottom:20px;'><h5>".$titulo."</h5></div>";
	echo "</div>";
	if($nomBoton!=false){
		if($banco==true){
			echo "<div class='col-md-2'>";
		}else{
			echo "<div class='col-md-2'>";
		}	
	echo "<a href='".$target."' style='width:100%;' class='btn btn-primary btn-sm' role='button'><i class='".$icono."'></i>&nbsp;".$nomBoton."</a>&nbsp;";	
	echo "</div>";
	if($banco==true){
	echo "<div class='col-md-2'>";
	echo "<a href='".$target2."' style='width:100%;' class='btn btn-primary btn-sm' role='button'><i class='".$icono2."'></i>&nbsp; Crear Cta. Bancaria</a>&nbsp;";	
	echo "</div>";
	}
	}
	echo "</div>";
	}
	public function tabla_mm_propietarios(){
	 
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==2){$this->msgBox=new msgBox(1,"Agente ha sido eliminado con exito!!");}}
	if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
	
	
	
	if($op=="editar"){$id=htmlentities($_GET["idq"]);
		$this->controles("Editar Propietarios","Crear Propietario","panel.php?op=12","fas fa-user-plus");
		$this->modificar_mm_propietarios($id);
	}else if($op=="borrar"){
		$id=htmlentities($_GET["idq"]);
		$sql3="delete from mm_propietarios where idPropietario='".$id."'";
	 	mysqli_query($this->link,$sql3);
 
	 
		echo '<script>document.location="panel.php?op=3&msg=1";</script>';		
		exit;
	}else{
		$this->controles("Listado de Propietarios","Crear Propietario","panel.php?op=12","fas fa-user-plus");
		$campoIndice="idPropietario";
		$index="panel.php?op=3";

		$campoFoto=array("ruta"=>true);
		//$nomTablaFoto="mm_cape_fotos";
		
		$campos=array(
					'nombre'=>'nombre',
					'rut'=>'rut',
					'email'=>'email',
					'telefono'=>'telefono',
					
					//'direccion'=>'direccion',										
					//'empresa'=>'empresa',
					//'banco'=>'banco',
					//'tipoCuenta'=>'tipoCuenta',
					//'codigo'=>'codigo'
					);
		
		$tamCol=array(30,15,20,10);
		$campoFoto=array("foto"=>true);
		$filtrar1=array('buscador'=>'nombrePack');
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_propietarios";
		
		if(isset($_POST["action"])){
			$col=$_POST["columna"];
			$buscador=$_POST["buscador"];
			if($col==1){
				// rut
				$sql="select * from mm_propietarios where rut='".$buscador."' order by idPropietario desc";
			}else if($col==2){
				// email
				$sql="select * from mm_propietarios where email='".$buscador."' order by idPropietario desc";
			}else if($col==3){
				//telefono
				$sql="select * from mm_propietarios where telefono='".$buscador."' order by idPropietario desc";
			}else{
				$sql="select * from mm_propietarios order by idPropietario desc";
			}
		}else{
			$sql="select * from mm_propietarios order by idPropietario desc";
		}
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
		if(isset($_GET["msg"]) && $_GET["msg"]==1){
			echo '<div class="alert alert-primary" style="padding:5px;font-size:14px;" role="alert" id="alert-message">
			<strong>¡Éxito!</strong> propietario borrado con éxito.
   
		  </div>
		  <style>
			  #alert-message {
				  transition: opacity 1s ease;
				  opacity: 1;
				  display: block; /* Asegúrate de que el elemento esté visible */
			  }
			  #alert-message.fade {
				  opacity: 0;
				  height: 0; /* Elimina la altura */
				  padding: 0; /* Elimina el relleno */
				  margin: 0; /* Elimina el margen */
				  overflow: hidden; /* Oculta cualquier contenido */
			  }
		  </style>
		 <script>
			  document.addEventListener("DOMContentLoaded", function() {
				  setTimeout(function() {
					  document.getElementById("alert-message").classList.add("fade");
				  }, 3000); // 3000 ms = 3 segundos
			  });
		  </script> 
		  ';
		}
		echo "<form method='post' name='formBuscar' id='formBuscar'>";
		echo "<input type='hidden' name='action' id='action' value='true'/>";
		echo "<div style='margin-bottom:5px;'>";
		echo "<div class='row'>";
		
		echo "<div class='col-md-8'>
		 
		<div class='row'>
		<div class='col-md-1'>
		<span style='font-size:14px;'>Buscar:</span>
		</div>
		<div class='col-md-3'>
		<select name='columna' id='columna' class='form-select form-select-sm'>
				<option value='0' selected>Seleccione...</option>
				<option value='1'>Rut</option>
				<option value='2'>Email</option>
				<option value='3'>Telefono</option>
		</select>
		</div>
		<div class='col-md-3'>
		<input type='text' name='buscador' id='buscador' class='form-control form-control-sm' placeholder='Ingresa una palabra'/>
		</div>
		
		<div class='col-md-5'>
		<button id='buscarPropi' name='buscarPropi' class='btn btn-primary btn-sm' role='submit'> <i class='fas fa-search'></i> Buscar</button>
		</div>
		</div>
		</div>";
		echo "<div class='col-md-4'>&nbsp;</div>";
		echo "</div>";
		echo "</div>";
		echo "</form>";
		$grid->desplegarDatosPropietarios();
		return($sql);
	} 
}



public function modificarAbono($idDet){
	$sql="select* from mm_detalleLiquidacion where idDet='".$idDet."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	$idLiq=$_GET["idLiq"];
 
	if(isset($_POST["abono"])){
		echo "<pre>";
		print_r($_POST);
		$sql1="update mm_detalleLiquidacion set concepto='".$_POST["razon"]."',
		abono='".$_POST["abono1"]."',		
		descuento='".$_POST["descuento"]."',		
		fecha='".strtotime($_POST["fecha"])."' where idDet='".$idDet."'";
 
		 mysqli_query($this->link,$sql1);		 
		 echo "<script>document.location='panel.php?op=26&idArriendo=".$idArriendo."&act=cobros&v=det&idLiq=".$idLiq."&mq=editar&idq=".$idDet."&msg=1';</script>";				 
		exit;
	}
if(isset($_GET["msg"])){
echo '<div class="alert alert-primary" role="alert">Cambios se han guardado con exito</div>';
}
	echo  ' <div class="container "> 
	<form method="post" name="form1" id="form1" action="">
		<div class="mb-3">
			<label for="razon" class="form-label">Concepto</label>
			<input type="text" class="form-control" name="razon" id="razon" value="'.$r["concepto"].'" placeholder="Ingrese el concepto" required>
		</div>
		<div class="mb-3">
			<label for="monto" class="form-label">Abono</label>
			<input type="number" class="form-control" name="abono1" id="abono1" value="'.$r["abono"].'" placeholder="Ingrese el monto" required>
		</div>

		<div class="mb-3">
			<label for="monto" class="form-label">Descuento</label>
			<input type="number" class="form-control" name="descuento" id="descuento" value="'.$r["descuento"].'" placeholder="Ingrese el monto" required>
		</div>

		<div class="mb-3">
			<label for="fecha" class="form-label">Fecha</label>
			<input type="date" name="fecha" class="form-control" value="'.date("Y-m-d", $r["fecha"]).'" id="fecha" required>

			

		</div>

		<button type="submit" id="abono" name="abono" class="btn btn-primary">Guardar Cambios</button>
	</form>
</div>';
}
public function verificaGastosFijos($idArriendo){
	// gastosfijosingresados 0 no 1 si
	$sql="select* from mm_liquidacionArriendo where idArriendo='".$idArriendo."' and gastosFijosIngresados=0";
	
	$q=mysqli_query($this->link,$sql);
 
	if(mysqli_num_rows($q)!=0){
		return(true);
	}else{
		return(false);
	}
}
public function sacarIPC($montoArriendo){
	$IPC=$this->devolverIPC();
	return($IPC);
}
public function sacarPorcentaje($montoArriendo,$por){
	$resultado = $montoArriendo * ($por / 100);
	return($resultado);
}
public function devolverIPC(){
	$apiUrl = 'http://mindicador.cl:80/api';
	if ( ini_get('allow_url_fopen') ) {
		$json = file_get_contents($apiUrl);
	}else{
		$curl = curl_init($apiUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($curl);
		curl_close($curl);
	} 
 
	$d = json_decode($json);
	$ipc=$d->ipc->valor;
	return($ipc);

}
public function devolverValorUf(){

	$apiUrl = 'http://mindicador.cl:80/api';
	if ( ini_get('allow_url_fopen') ) {
		$json = file_get_contents($apiUrl);
	}else{
		$curl = curl_init($apiUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($curl);
		curl_close($curl);
	} 
 
	$d = json_decode($json);
	$uf=$d->uf->valor;
	return($uf);
}

public function verificaMesGarantia($idArriendo) {
    // Obtener la fecha actual
    $fechaActual = date('Y-m-d');

    $sql = "SELECT fechaInicio FROM mm_arriendos WHERE idArriendo='" . $idArriendo . "'";
    $q = mysqli_query($this->link, $sql);
    $r = mysqli_fetch_array($q);
    
    $fechaInicioUnix = $r["fechaInicio"];    
    $fechaInicioContrato = date('Y-m-d', $fechaInicioUnix);

    
    if ($fechaInicioContrato == $fechaActual) {    
        return true;
    } else {        
        return false;
    }
}
public function ingresarGastosFijos($idArriendo,$idLiq){	
	


	if($this->verificaGastosFijos($idArriendo)){
 
		$sql="select* from mm_arriendos where idArriendo='".$idArriendo."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q); 
	$d["fechaCancelacion"]=$r["fechaCancelacion"];
	
	$d["garantia"]=$r["garantia"];
	$d["comisionArriendo"]=$r["comisionArriendo"];
	$d["valorComision"]=$r["valorComision"];
	$d["monedaComision"]=$r["monedaComision"];
	$d["cobrarComisionAdmin"]=$r["cobrarComisionAdmin"];
	$d["valorComisionAdmin"]=$r["valorComisionAdmin"];
	$d["monedaComisionAdmin"]=$r["monedaComisionAdmin"];

	$d["fechaInicio"]=$r["fechaInicio"];
	$d["montoArriendo"]=$r["montoArriendo"];
	$d["comisionPropietario"] = $r["comisionPropietario"];
	$d["valorComisionPropietario"] = $r["valorComisionPropietario"];
	$d["monedaComisionPropietario"] = $r["monedaComisionPropietario"];
	$d["pagoComProp"]=$r["pagoComProp"];
	$fecha=strtotime($this->fecha);
	
	$sql2="insert into mm_detalleLiquidacion (idLiquidacion,concepto,abono,descuento,fecha) ";
	$sql2.=" values ('".$idLiq."','Mes de Arriendo','".$d["montoArriendo"]."','0','".$fecha."')";
  


	
	mysqli_query($this->link,$sql2);


	// verifica si la comision del propietario se ha pagado por primera vez
 
	if ($d["pagoComProp"] == 0) {			
		// se debe poner como un descuento y es porcentual se debe sacar sobre el mes de arriendo
		$montoArriendo = $d["montoArriendo"];
		
		if($d["monedaComisionPropietario"]==1){
				// lo calcula con la uf
		}else if($d["monedaComisionPropietario"]==2){
				// queda en pesos
		}else if($d["monedaComisionPropietario"]==3){
			// en porcentaje
			$porcentajeComision = $d["valorComisionPropietario"];
			// Calcular el monto de la comisión al propietario
			$montoComisionPropietario = $montoArriendo * ($porcentajeComision / 100);				
			// ingresa en el detalle como descuento 
			$sql4="insert into mm_detalleLiquidacion (idLiquidacion,concepto,abono,descuento,fecha) ";
			$sql4.=" values ('".$idLiq."','Comision del propietario ".$porcentajeComision."%','0','".$montoComisionPropietario."','".$fecha."');";
			
			mysqli_query($this->link,$sql4);
			
			// Calcular el IVA sobre la comisión al propietario (19%) para elsiguiente cobro 
			$iva = $montoComisionPropietario * 0.19;
			// Almacenar el monto del IVA en una nueva variable
			$ivaComisionPropietario = $iva;

			$sql5="insert into mm_detalleLiquidacion (idLiquidacion,concepto,abono,descuento,fecha) ";
			$sql5.=" values ('".$idLiq."','IVA 19%','0','".$ivaComisionPropietario."','".$fecha."');";
			mysqli_query($this->link,$sql5);
		
			$sql6="update mm_arriendos set pagoComProp=1 where idArriendo='".$idArriendo."'";
			mysqli_query($this->link,$sql6);
		}
	 
} 


// verifica si se cobra el mes de garantia
	if($this->verificaMesGarantia($idArriendo)){
		// se cobra mes de garantia
			$sql3="insert into mm_detalleLiquidacion (idLiquidacion,concepto,abono,descuento,fecha) ";
			$sql3.=" values ('".$idLiq."','Mes de Garantia','".$d["garantia"]."','0','".$fecha."')";
			mysqli_query($this->link,$sql3);
	}
	
	

	// comision de arriendo
		if($d["comisionArriendo"]==1){
			
				if($d["monedaComision"]==1){
					$m=0;
				}else if($d["monedaComision"]==2){
					$m=$this->sacarIPC($d["montoArriendo"]);
					
					$sql4="insert into mm_detalleLiquidacion (idLiquidacion,concepto,abono,descuento,fecha) ";
					$sql4.=" values ('".$idLiq."','Comision de arriendo con IPC','0','".$m."','".$fecha."')";
					mysqli_query($this->link,$sql4);
					// Calcular el IVA sobre la comisión al propietario (19%) para elsiguiente cobro 
					$iva = $m * 0.19;
					// Almacenar el monto del IVA en una nueva variable
					$ivaComisionArriendo = $iva;

					$sql5="insert into mm_detalleLiquidacion (idLiquidacion,concepto,abono,descuento,fecha) ";
					$sql5.=" values ('".$idLiq."','IVA 19%','0','".$ivaComisionArriendo."','".$fecha."');";
					mysqli_query($this->link,$sql5);

				}else if($d["monedaComision"]==3){
					//$m=$this->sacarPorcentaje($d["montoArriendo"],$d["valorComision"]);
					$montoArriendo=$d["montoArriendo"];
					$porcentajeComision = $d["valorComision"];
					// Calcular el monto de la comisión al propietario
					$montoComisionArriendo = $montoArriendo * ($porcentajeComision / 100);


					$sql41="insert into mm_detalleLiquidacion (idLiquidacion,concepto,abono,descuento,fecha) ";
					$sql41.=" values ('".$idLiq."','Comision de arriendo ".$porcentajeComision." %','0','".$montoComisionArriendo."','".$fecha."')";
					mysqli_query($this->link,$sql41);

					$iva = $montoComisionArriendo * 0.19;
					$ivaComisionArriendo = $iva;

					$sql51="insert into mm_detalleLiquidacion (idLiquidacion,concepto,abono,descuento,fecha) ";
					$sql51.=" values ('".$idLiq."','IVA 19%','0','".$ivaComisionArriendo."','".$fecha."');";
					mysqli_query($this->link,$sql51);


				} 						
				
				
		 
				// sacar el iva comision arriendo

			 // Convertir el timestamp de la fecha de inicio del contrato a una fecha legible
$fechaInicioContrato = date('Y-m-d', $d["fechaInicio"]);

// Obtener la fecha actual en formato legible
//$fechaActual = date('Y-m-d');
$fechaActual=$this->fecha;


// Calcular la diferencia en meses entre la fecha de inicio y la fecha actual
$mesesTranscurridos = floor((strtotime($fechaActual) - strtotime($fechaInicioContrato)) / (30 * 24 * 60 * 60));

 
// Verificar si ha pasado al menos un mes desde el inicio del contrato
if ($mesesTranscurridos > 1) {

    // Si ha pasado al menos un mes, se debe cobrar la comisión de administración en el segundo mes
    // Almacenar el valor y la moneda de la comisión de administración
    $d["cobrarComisionAdmin"] = $r["cobrarComisionAdmin"];
    $d["valorComisionAdmin"] = $r["valorComisionAdmin"];
    $d["monedaComisionAdmin"] = $r["monedaComisionAdmin"];
    
    $montoArriendo = $d["montoArriendo"];
    if ($d["monedaComisionAdmin"] == 2) {
		// pesos 
		$montoComisionAdmin=$d["valorComisionAdmin"];
		 // Insertar la comisión de administración en la tabla de detalle de liquidación
		 $sql5 = "INSERT INTO mm_detalleLiquidacion (idLiquidacion, concepto, abono, descuento, fecha) ";
		 $sql5 .= "VALUES ('" . $idLiq . "', 'Comision de administracion', '0', '" . $montoComisionAdmin . "', '" . strtotime($fechaActual) . "');";
		 
  
		  mysqli_query($this->link, $sql5);
		 
		 // Calcular el IVA
		 $ivaPorcentaje = 0.19; // Porcentaje del IVA
		 $ivaComisionAdmin = $montoComisionAdmin * $ivaPorcentaje;
		 
		 // Insertar el IVA en la tabla de detalle de liquidación
		 $sql6 = "INSERT INTO mm_detalleLiquidacion (idLiquidacion, concepto, abono, descuento, fecha) ";
		 $sql6 .= "VALUES ('" . $idLiq . "', 'IVA 19%', '0', '" . $ivaComisionAdmin . "', '" . strtotime($fechaActual) . "');";
		  
		 
		mysqli_query($this->link, $sql6);


	}else 	if ($d["monedaComisionAdmin"] == 3) {
        // Es porcentual, calcular la comisión de administración
        $porcentajeComisionAdmin = $d["valorComisionAdmin"];
        $montoComisionAdmin = $montoArriendo * ($porcentajeComisionAdmin / 100);
        
        // Insertar la comisión de administración en la tabla de detalle de liquidación
		$sql5 = "INSERT INTO mm_detalleLiquidacion (idLiquidacion, concepto, abono, descuento, fecha) ";
		$sql5 .= "VALUES ('" . $idLiq . "', 'Comision de administracion " . $porcentajeComisionAdmin . "%', '0', '" . $montoComisionAdmin . "', '" . strtotime($fechaActual) . "');";
		
 
		 mysqli_query($this->link, $sql5);
		
		// Calcular el IVA
		$ivaPorcentaje = 0.19; // Porcentaje del IVA
		$ivaComisionAdmin = $montoComisionAdmin * $ivaPorcentaje;
		
		// Insertar el IVA en la tabla de detalle de liquidación
		$sql6 = "INSERT INTO mm_detalleLiquidacion (idLiquidacion, concepto, abono, descuento, fecha) ";
		$sql6 .= "VALUES ('" . $idLiq . "', 'IVA 19%', '0', '" . $ivaComisionAdmin . "', '" . strtotime($fechaActual) . "');";
		 
		
       mysqli_query($this->link, $sql6);
	 
		 
    }
} else {
    // Si no ha pasado al menos un mes, no se debe cobrar la comisión de administración
    $d["cobrarComisionAdmin"] = false;
}



 
		}
		// actualiza que se ingreso los gaastos fijos al detalle
		$sql1="update mm_liquidacionArriendo set gastosFijosIngresados=1 where idArriendo='".$idArriendo."'";
		mysqli_query($this->link,$sql1);
	
		return(true);
}

 
}









public function sacarIva($monto,$porcentaje){
	$resultado = $monto * ($porcentaje / 100);
	return($resultado);
}
public function tablaCobros($idLiq){

	 
		if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==2){$this->msgBox=new msgBox(1,"Se ha sido eliminado con exito!!");}}
		if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
		
		$id=htmlentities($_GET["idq"]);
		
		if($op=="editar"){$id=htmlentities($_GET["idq"]);
		//	$this->controles("Editar Propietarios","Crear Propietario","panel.php?op=12","fas fa-user-plus");
			$this->modificarAbono($id);
		}else if($op=="borrar"){
			$idLiq=$_GET["idLiq"];
			$sql3="delete from mm_detalleLiquidacion where idDet='".$id."'";
			mysqli_query($this->link,$sql3);
			$idArriendo=$_GET["idArriendo"];
			$this->pdf=new pdf();
			$this->pdf->generarPdf($idLiq);			
			echo "<script>document.location='panel.php?op=26&idArriendo=".$idArriendo."&act=cobros&v=det&idLiq=".$idLiq."&msg=2';</script>";				 
			exit;
		}else{
			//$this->controles("Listado liquidación de arriendo");
			$campoIndice="idDet";
			$index="panel.php?op=26&idArriendo=".$idArriendo."&act=cobros&v=det&idLiq=".$idLiq;
	
			$campoFoto=array("ruta"=>true);
			//$nomTablaFoto="mm_cape_fotos";
			
			// debe cruzar la informacion con el detalle de la liquidacion para sacar la suma de las liquidaciones en los montos
			$campos=array(			
						 		
						'concepto'=>'concepto',
						'abono'=>'abono',
						'descuento'=>'descuento' 
						 					 
						);
			$sql=" SELECT *
			FROM mm_detalleLiquidacion
			WHERE idLiquidacion = '".$idLiq."' order by idDet asc";		
		
			
			$tamCol=array(70,15,15);
			$campoFoto=array("foto"=>true);
			$filtrar1=array('buscador'=>'nombrePack');
			$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
			$tabla="mm_detalleLiquidacion";
			$grid->asignarCampos($campos,$tamCol,$sql,$tabla);			 
			$grid->desplegarCobros();
			return($sql);
		} 
	 
}

public function verificaInicioMes(){
	$fechaActual = strtotime($this->fecha);
// Extraer el mes y año de la fecha actual
$mesActual = date('m', $fechaActual);
$anioActual = date('Y', $fechaActual);

 
// Recorrer la tabla mm_arriendos
$consultaArriendos = "SELECT idArriendo FROM mm_arriendos";
$resultadoArriendos = mysqli_query($this->link, $consultaArriendos);

while ($arriendo = mysqli_fetch_array($resultadoArriendos)) {
    $idArriendo = $arriendo['idArriendo'];

    // Verificar cuántas liquidaciones existen para este arriendo, mes y año
	$consultaLiquidaciones = "SELECT COUNT(*) as totalLiquidaciones
	FROM mm_liquidacionArriendo
	WHERE idArriendo = $idArriendo
	AND YEAR(fechaLiqui) = $anioActual
	AND MONTH(fechaLiqui) = $mesActual";
 
    $q = mysqli_query($this->link, $consultaLiquidaciones);
    $resultadoLiquidaciones = mysqli_fetch_array($q);
    $totalLiquidaciones = $resultadoLiquidaciones['totalLiquidaciones'];
 
    if ($totalLiquidaciones == 0) {  
		       
        $this->generarLiquidacionArriendo($idArriendo, $mesActual, $anioActual);
    }  
} 

}

public function devolverDatosArriendo($idArriendo){
	$sql="select idArriendo,idProp,idArrendatarios,idPropietarios from mm_arriendos where idArriendo='".$idArriendo."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	$d["idArriendo"]=$r["idArriendo"];
	$d["idProp"]=$r["idProp"];
	$d["idArrendatarios"]=$r["idArrendatarios"];
	$d["idPropietarios"]=$this->devolverIdPropietario($r["idProp"]);
 
	return($d);
}
public function  generarLiquidacionArriendo($idArriendo, $mes, $anio) { 
	$d=$this->devolverDatosArriendo($idArriendo);
	$fecha = $this->fecha;
    $sql="insert into mm_liquidacionArriendo (idArriendo,idProp,idPropietario,idArrendatario,fechaLiqui) ";
	$sql.=" values ('".$d["idArriendo"]."','".$d["idProp"]."','".$d["idPropietarios"]."','".$d["idArrendatarios"]."','".$fecha."')";
 
	
	 mysqli_query($this->link,$sql);
	 $idLiq= mysqli_insert_id($this->link);
	 
	 if($this->ingresarGastosFijos($idArriendo,$idLiq)){
		$this->pdf=new pdf();
		$this->pdf->generarPdf($idLiq);
	 }
	return(true);
}
public function tabla_mm_LiquidacionArriendo($idArriendo){
	 $this->verificaInicioMes();

 	
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==2){$this->msgBox=new msgBox(1,"Agente ha sido eliminado con exito!!");}}
	if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
	
	
	
	if($op=="editar"){$id=htmlentities($_GET["idq"]);
		$this->controles("Editar Propietarios","Crear Propietario","panel.php?op=12","fas fa-user-plus");
		$this->modificar_mm_liquidacionArriendo($id);
	}else if($op=="borrar"){
		$id=htmlentities($_GET["idq"]);
		$sql3="delete from mm_liquidacionArriend where idliquidacion='".$id."'";
		mysqli_query($this->link,$sql3);
		echo "<script>document.location='panel.php?op=3&msg=1';</script>";				 
		 
		exit;
	}else{
		//$this->controles("Listado liquidación de arriendo");
		$campoIndice="idLiquidacion";
		$index="panel.php?op=3";

		$campoFoto=array("ruta"=>true);
		//$nomTablaFoto="mm_cape_fotos";
		
		// debe cruzar la informacion con el detalle de la liquidacion para sacar la suma de las liquidaciones en los montos
		$campos=array(			
					'fecha'=>'fecha',										
					 
					'fechaCancelacion'=>"Fecha de Pago",
					
					'monto'=>'monto',
					'estado'=>"estado",		
					);

	 $sql="SELECT 
    mm_liquidacionArriendo.idLiquidacion,
    mm_liquidacionArriendo.idArriendo,
    mm_liquidacionArriendo.idProp,
    mm_liquidacionArriendo.idPropietario,
    mm_liquidacionArriendo.idArrendatario,
    mm_liquidacionArriendo.fechaLiqui,
    mm_liquidacionArriendo.estado,
	mm_liquidacionArriendo.rutaPdf,
    mm_liquidacionArriendo.idDatosDep,
    SUM(mm_detalleLiquidacion.abono) AS totalAbono,
    SUM(mm_detalleLiquidacion.descuento) AS totalDescuento,
    SUM(mm_detalleLiquidacion.abono) - SUM(mm_detalleLiquidacion.descuento) AS monto
FROM
    mm_liquidacionArriendo
JOIN
    mm_detalleLiquidacion ON mm_liquidacionArriendo.idLiquidacion = mm_detalleLiquidacion.idLiquidacion	
WHERE   mm_liquidacionArriendo.idArriendo=$idArriendo 
GROUP BY
    mm_liquidacionArriendo.idLiquidacion, mm_liquidacionArriendo.idArriendo, mm_liquidacionArriendo.idProp, mm_liquidacionArriendo.idPropietario, mm_liquidacionArriendo.idArrendatario, mm_liquidacionArriendo.fechaLiqui, mm_liquidacionArriendo.idDatosDep 
ORDER BY  mm_liquidacionArriendo.idLiquidacion desc
";
		 
 
		
		$tamCol=array(20,25,25,5,5,5);
		$campoFoto=array("foto"=>true);
		$filtrar1=array('buscador'=>'nombrePack');
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_liquidacionArriendo";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);			 
		$grid->desplegarDatosLiquidacionArriendo();
		return($sql);
	} 
}
	
/*
public function ingresar_mm_comisiones(){
	
	$this->miForm=new form();
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Agente ingresada con exito!!!");}}
	$this->miForm->abrirForm(1,true,"form1","post","proceso.php",2);
	echo "<h1>Ingresar Comisiones</h1>";
	$this->miForm->addText("nuevoPorcentaje",550,"Nuevo Porcentaje","Nuevo Porcentaje:",false);
	$this->miForm->addText("motivoAjuste",550,"Motivo Ajuste","Motivo Ajuste:",false);
	$this->miForm->addHidden("action","true");
	$this->miForm->addButton("Enviar","Agregar",false,false);
	$this->miForm->procesar();
	
	if($_POST["action"]){ $d=$_POST;
	
	
	$sql="insert into mm_comisiones (idArriendo,fechaAjuste,nuevoPorcentaje,motivoAjuste) values ('".$d["idArriendo"]."','".$d["fechaAjuste"]."','".$d["nuevoPorcentaje"]."','".$d["motivoAjuste"]."')";
	
	mysql_query($sql) or die(mysql_error());
		echo "<script>document.location='panelUser.php?m=1&msg=1';</script>";
	exit;
	}
	$this->miForm->cerrarForm();
	return($sql);
	}
	*/
	
	
	public function modificar_mm_comisiones($id=false){
	
	if(isset($_GET["idq"])){ $id=htmlentities($_GET["idq"]); }
	$sql="select* from mm_comisiones where idComisiones='".$id."'";
	//$q=mysqli_query($this->link,$sql);
	//$r=mysqli_fetch_array($q);
	$this->miForm=new form();
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Cambios Guardados con exito!!!");}}
	$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
	echo "<h1>Modificar Comisiones</h1>";
	$this->miForm->addText("nuevoPorcentaje",550,"Nuevo Porcentaje","Nuevo Porcentaje:",$row["nuevoPorcenjate"]);
	$this->miForm->initCalendar();
	$this->miForm->addCalendar("fechaAjuste",550,"fecha del ajuste","Fecha del Ajuste:",false);	
	$this->miForm->addTextarea("motivoAjuste",550,5,"Motivo del ajuste","Motivo del ajuste:",$row["motivoAjuste"],0);	
	$this->miForm->addHidden("action","true"); $this->miForm->addButton("Enviar","Guardar Cambios",false,false);
	$this->miForm->procesar();
	
	if($_POST["action"]){ $d=$_POST;	
	
	$sql="update mm_comisiones set 
			
			fechaAjuste='".$d["fechaAjuste"]."',
			nuevoPorcentaje='".$d["nuevoPorcentaje"]."',
			motivoAjuste='".$d["motivoAjuste"]."' where idArriendo='".$id."'";
	
	mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
	echo "<script>document.location='panel.php?m=1&msg=1';</script>";				 	
	exit;
	}
	$this->miForm->cerrarForm();
	return($sql);
	}
	
	
	
	public function tabla_mm_comisiones(){
	/* www.LeonardoSystem.cl*/
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==2){$this->msgBox=new msgBox(1,"Agente ha sido eliminado con exito!!");}}
	if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
	$this->controles("Comisiones");
	if($op=="editar"){$id=htmlentities($_GET["idq"]);
	$this->modificar_mm_comisiones($id);
	}else if($op=="borrar"){
	$id=htmlentities($_GET["idq"]);
	$sql3="delete from mm_comisiones where idComisiones='".$id."'";
	
	mysql_query($sql3);
	echo "<script>document.location='panelUser.php?m=1&msg=1';</script>";				 	
	exit;
	}else{
	$campoIndice="idComisiones";
	$index="panelUser.php?m=1";

	$campoFoto=array("ruta"=>true);
	//$nomTablaFoto="mm_cape_fotos";
	$sql="select * from mm_comisiones";
	
	$campos=array(
				'idArriendo'=>'idArriendo',
				'fechaAjuste'=>'fechaAjuste',
				'nuevoPorcentaje'=>'nuevoPorcentaje',
				'motivoAjuste'=>'motivoAjuste');
	//$tipo=array(1=>"Casas",2=>"Departamentos",3=>"Parcelas",4=>"Sitios",5=>"Oficina Comercial",6=>"Propiedad Industrial",7=>"Terreno");
	$tamCol=array(1,49,20,50,10);
	$campoFoto=array("foto"=>true);
	$filtrar1=array('buscador'=>'nombrePack');
	$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
	$tabla="mm_comisiones";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
		$grid->desplegarDatos();
	return($sql);
	} }
	
	public function acercaDe(){
		echo "<div>";
		echo '<p><b>Acerca del Sistema de Administración de Arriendo 1.0</b><br>
		Nuestra solución de gestión de arriendos 1.0 es una herramienta eficiente y fácil de usar para propietarios y administradores de bienes raíces. Simplifica la gestión de propiedades, seguimiento de arrendamientos y facturación, promoviendo la transparencia y el crecimiento de tu negocio.</p>';
		echo "</div>";
	}
	
	 
	
	public function breadCrumb(){
		if(isset($_GET["op"])){$op=htmlentities($_GET["op"]);}
		echo '<nav aria-label="breadcrumb">
		<ol class="breadcrumb">';
		if($_SESSION["auth"]["tipo"]=="admin"){
			echo '<li class="breadcrumb-item"><a href="panel.php">Inicio</a></li>';
		}else if($_SESSION["auth"]["tipo"]=="propi"){
			echo '<li class="breadcrumb-item"><a href="panelPropietario.php">Inicio</a></li>';
		}else if($_SESSION["auth"]["tipo"]=="arrendatario"){
			echo '<li class="breadcrumb-item"><a href="panelArrendatario.php">Inicio</a></li>';
		} else{
			echo '<li class="breadcrumb-item"><a href="panelAgente.php">Inicio</a></li>';
		} 
		

		  if($op==1){
			echo '<li class="breadcrumb-item active" aria-current="page">Lista de propiedades</li>';
        }else if($op==2){
			echo '<li class="breadcrumb-item active" aria-current="page">Lista de arriendos</li>';     
        }else if($op==3){
			if($_GET["mq"]=="editar"){
				echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=3">Lista de Propietarios</a> / Editar Propietarios</li>';
			}else{
				echo '<li class="breadcrumb-item active" aria-current="page">Lista de Propietarios</li>';
			}
			
        }else if($op==4){
			echo '<li class="breadcrumb-item active" aria-current="page">Arrendatarios</li>';
        }else if($op==5){         
			echo '<li class="breadcrumb-item active" aria-current="page">Ingresar Propiedad</li>';
        }else if($op==6){
			echo '<li class="breadcrumb-item active" aria-current="page">Ingresar Arrendatario</li>';
        }else if($op==7){
			echo '<li class="breadcrumb-item active" aria-current="page">Lista de usuarios</li>';
        }else if($op==8){
			echo '<li class="breadcrumb-item active" aria-current="page">Configuración</li>';
        }else if($op==9){
			echo '<li class="breadcrumb-item active" aria-current="page">Editar Cuenta</li>';
        }else if($op==10){
			echo '<li class="breadcrumb-item active" aria-current="page">Modificar Contraseña</li>';
        }else if($op==11){
			echo '<li class="breadcrumb-item active" aria-current="page">Acerca de</li>';
        }else if($op==12){
			echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=3">Lista de propietarios</a> / Ingresar Propietarios</li>';
        }else if($op==13){
			if(isset($_GET["act"])){
				echo '<li class="breadcrumb-item active" aria-current="page">Modificar Cuenta Bancaria</li>';
			}else{
				echo '<li class="breadcrumb-item active" aria-current="page">Ingresar Cuenta Bancaria</li>';
			}			
		}else if($op==14){
			echo '<li class="breadcrumb-item active" aria-current="page">Ficha Propietario</li>';
		}else if($op==15){
			echo '<li class="breadcrumb-item active" aria-current="page">Ficha de la propiedad</li>';
		}else if($op==16){
			echo '<li class="breadcrumb-item active" aria-current="page">Ingresar nuevo arriendo</li>';
		}else if($op==17){
			echo '<li class="breadcrumb-item active" aria-current="page">Ingresar nuevo arriendo</li>';
		}else if($op==18){
			echo '<li class="breadcrumb-item active" aria-current="page">Lista de codeudores</li>';
		}else if($op==19){
			echo '<li class="breadcrumb-item active" aria-current="page">Lista de codeudores</li>';
		}else if($op==30){
			echo '<li class="breadcrumb-item active" aria-current="page">Solicitud de mantenimiento</li>';
		}else if($op==35){
			echo '<li class="breadcrumb-item active" aria-current="page">Detalle del mantenimiento</li>';
		}else if($op==36){
			echo '<li class="breadcrumb-item active" aria-current="page">Historial de pagos</li>';
		}else if($op==50){
			echo '<li class="breadcrumb-item active" aria-current="page">Solicitud de mensajes</li>';
		}else if($op==33){
			echo '<li class="breadcrumb-item active" aria-current="page">Propiedades Arrendadas</li>';
		}else if($op==9991){
			echo '<li class="breadcrumb-item active" aria-current="page">Editar perfil de arrendatario</li>';
		}else if($op==1001){
			echo '<li class="breadcrumb-item active" aria-current="page">Cambiar contraseña arrendatario</li>';
		}else if($op==40){
			echo '<li class="breadcrumb-item active" aria-current="page">Solicitud de mantención</li>';

		}else if($op==21){
			if($_GET["act"]=="edit"){
				echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=3">Lista de propietarios</a> / Editar cuenta bancaria propietario</li>';
			}else{
				echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=3">Lista de propietarios</a> / Ingresar cuenta bancaria propietario</li>';
			}
		
		}else if($op==26){
			
			if(isset($_GET["idArriendo"])){
				$idArriendo=$_GET["idArriendo"];
				if(isset($_GET["act"]) && $_GET["act"]=="cobros"){
					if(isset($_GET["v"]) && $_GET["v"]=="det"){
						$idLiq=$_GET["idLiq"];
						if(isset($_GET["z"]) && $_GET["z"]=="abono"){
							echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=2">Lista de arriendos</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'">Ficha del Arriendo</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=cobros">Cobros</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=cobros&v=det&idLiq='.$idLiq.'">Detalles del cobro</a> / Ingresar Abono</li>';				
						}else if(isset($_GET["z"]) && $_GET["z"]=="desc"){
							echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=2">Lista de arriendos</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'">Ficha del Arriendo</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=cobros">Cobros</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=cobros&v=det&idLiq='.$idLiq.'">Detalles del cobro</a> / Ingresar Descuento</li>';				
						}else{
							if(isset($_GET["mq"])){
								echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=2">Lista de arriendos</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'">Ficha del Arriendo</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=cobros">Cobros</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=cobros&v=det&idLiq='.$idLiq.'">Detalles del cobro</a> / Editar</li>';				
							}else{
								if(isset($_GET["z"])){
									echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=2">Lista de arriendos</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'">Ficha del Arriendo</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=cobros">Cobros</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=cobros&v=det&idLiq='.$idLiq.'">Detalles del cobro</a> / editar comisiones</li>';				
								}else{
									echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=2">Lista de arriendos</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'">Ficha del Arriendo</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=cobros">Cobros</a> / Detalles del cobro</li>';				
								}
								
							}
							
						}
						
					}else{
						echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=2">Lista de arriendos</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'">Ficha del Arriendo</a> / Cobros</li>';				
					}					
				}else if(isset($_GET["act"]) && $_GET["act"]=="garantia"){
					echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=2">Lista de arriendos</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'">Ficha del Arriendo</a> / Garantia</li>';				
				}else if(isset($_GET["act"]) && $_GET["act"]=="reajustes"){
					echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=2">Lista de arriendos</a> / <a href="panel.php?op=26&idArriendo='.$idArriendo.'">Ficha del Arriendo</a> / Reajustes</li>';				
				}else{
					echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=2">Lista de arriendos</a> / Ficha del Arriendo</li>';				
				}
				
			}
			

		}else if($op==27){
			echo '<li class="breadcrumb-item active" aria-current="page"><a href="panelPropietario.php?op=27&idPropi='.$_SESSION["auth"]["idReg"].'">Lista de propiedades</a> /</li>';			

		}else if($op==28){
			echo '<li class="breadcrumb-item active" aria-current="page"><a href="panelPropietario.php?op=28">Ingresar Propiedad</a> /</li>';			


		}else if($op==255){
			if($_GET["act"]=="edit"){
				echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=4">Lista de arrendatarios</a> / Editar cuenta bancaria arrendatario</li>';
			}else{
				echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=4">Lista de arrendatarios</a> / Ingresar cuenta bancaria arrendatario</li>';
			}
			
		}else if($op==256){
			if($_GET["act"]=="edit"){
				echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=18">Lista de codeudores</a> / Editar cuenta bancaria arrendatario</li>';
			}else{
				echo '<li class="breadcrumb-item active" aria-current="page"><a href="panel.php?op=18">Lista de codeudores</a> / Ingresar cuenta bancaria arrendatario</li>';
			}
			
		}
		echo '</ol>
	  </nav>';

	}
	public function devolverPropietario($id){
		$sql="select* from mm_propietarios where idPropietario='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$nombre=ucfirst($r["nombre"]."&nbsp;".$r["apellido"]);
		return($nombre);
	   }


	   public function devolverIdPropietario($id){
		$sql="select* from mm_propiedad1 where idProp='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$nombre=$r["idPropietario"];
		return($nombre);
	   }
	   
	   public function tablaArriendoCode($idCode=false){
		
		if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==2){$this->msgBox=new msgBox(1,"Agente ha sido eliminado con exito!!");}}
		if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
	
		if($op=="editar"){$id=htmlentities($_GET["idq"]);
			$this->modificar_mm_arriendos($id);
		}else if($op=="borrar"){
			$id=htmlentities($_GET["idq"]);
			$sql3="delete from mm_arriendos where idArriendo='".$id."'";	
			mysql_query($sql3);
			echo "<script>document.location='panelUser.php?m=1&msg=1';</script>";				 			
			exit;
		}else{
			$campoIndice="idArriendo";
			$index="panelUser.php?m=1";
			$nomTabla="mm_arriendos";
			$campoFoto=array("ruta"=>true);
			//$nomTablaFoto="mm_cape_fotos";
			if(isset($_GET["idCoud"])){
				$idCoud=htmlentities($_GET["idCoud"]);
			}
			$sql="select * from mm_arriendos where idCodeudor='".$idCoud."'";
			
			$campos=array('Ficha'=>'idProp','Propiedad'=>'tituloProp','fechaInicio'=>'fechaInicio','fechaTerminio'=>'fechaCancelacion');
			//$tipo=array(1=>"Casas",2=>"Departamentos",3=>"Parcelas",4=>"Sitios",5=>"Oficina Comercial",6=>"Propiedad Industrial",7=>"Terreno");
			$tamCol=array(29,40,20,10);
			$campoFoto=array("foto"=>true);
			$filtrar1=array('buscador'=>'nombrePack');
			$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_arriendos";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
			$grid->visualizarDatos();
	 
			return($sql);
		}
	
	}

	public function fichaCodeudor(){
		if(isset($_GET["idCoud"])){
		$idCoud=htmlentities($_GET["idCoud"]);
		$sql="select* from mm_codeudor where idCodeudor='".$idCoud."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		}
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<h3 style='float:left;'>Ficha Codeudor</h3><h4 style='color:gray;padding-top:5px;font-weight:500 !important;'>&nbsp;&nbsp;".utf8_encode($r["nombre"])."</h4>";
		echo "</div>";
		echo "</div>";
		echo "<div class='row' style='margin-top:30px;'>";
		echo "<div class='col-md-3'>";
			echo '<div class="list-group">
					<a href="panel.php?op=19&idCoud='.$idCoud.'" class="list-group-item list-group-item-action ';
					if($_GET["op"]==19 && !isset($_GET["act"])){echo "active";}
					echo '" aria-current="true">
					Información
					</a>	 
					<a href="panel.php?op=19&act=lista&idCoud='.$idCoud.'" class="list-group-item list-group-item-action ';
					if(isset($_GET["act"])){
						echo ' active ';
					}
					echo '">Arriendos</a>	
	  		</div>';
	  echo "</div>";
	  echo "<div class='col-md-9'>";
	  if(isset($_GET["act"])){
		echo "<div><h5>Arrriendos</h5></div>";
		$this->tablaArriendoCode($idCoud);
	  }else{

	  
	  echo "<h5>Información</h5>";
	  
	  echo "<div class='row'>";
	  
	  echo "<div class='col-md-4'>";
	  		
	  echo "<div style='margin-top:10px;'><b>Rut</b></div>";
	  echo "<div>".$r["rut"]."</div>";
		
	  echo "<div style='margin-top:10px;'><b>Tipo</b></div>";
	  echo "<div>".$this->devolverTipo($r["tipo"])."</div>";

		 
	  echo "</div>";
	 
	  echo "<div class='col-md-4'>";
	  echo "<div style='margin-top:10px;'><b>Telefono</b></div>";
	  echo "<div>".$r["telefono"]."</div>";


	  echo "</div>";

	  echo "<div class='col-md-4'>";
	  echo "<div style='margin-top:10px;'><b>Email</b></div>";
	  echo "<div>".$r["email"]."</div>";
	  echo "</div>";

	  
	  echo "</div>";
	  echo "<div class='row'>";
		
	  echo "<div class='col-md-12'>";
	  echo "<div style='margin-top:10px;'><b>Comentarios</b></div>";
	  echo "<div>".$r["comentarios"]."</div>";
	  echo "</div>";

	  echo "</div>";
		echo "</div>";
	  }
	}

	public function devolverCodeudor($id){
		$sql="select* from mm_codeudor where idCodeudor='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		return($r["nombre"]);
	}
	public function devolverReajuste($id){
		$arrSel24=array(1=>"Sin Reajuste","IPC","Fijo Porcentual","Fijo en Pesos");
		return($arrSel24[$id]);
	}
	public function devolverDatosArrendatario($id){
		$sql="select* from mm_arrendatario where idArrendatario='".$id."'";
		
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$m["nombre"]=$r["nombre"];
		$m["telefono"]=$r["telefono"];
		$m["correo"]=$r["email"];
		$m["rut"]=$r["rut"];
		return($m);
	}
	public function ingresarAbono(){
		if(isset($_GET["idArriendo"])){
			$idArriendo=$_GET["idArriendo"];
		}
		if(isset($_POST["abono"])){
			
			$idLiq=$_GET["idLiq"];
			$sql="insert into mm_detalleLiquidacion (idLiquidacion,concepto,abono,descuento,monto,fecha,fijo) ";
			$sql.=" values ('".$idLiq."','".$_POST["razon"]."','".$_POST["monto"]."','0','".$_POST["monto"]."','".strtotime($_POST["fecha"])."','1');";
			 
			mysqli_query($this->link,$sql);
			$this->pdf=new pdf();
			$this->pdf->generarPdf($idLiq);
			echo "<script>document.location='panel.php?op=26&idArriendo=".$idArriendo."&act=cobros&z=abono&v=det&idLiq=".$idLiq."&msg=1';</script>";				 		 	
			exit;
		}
if(isset($_GET["msg"])){
	echo '<div class="alert alert-primary" role="alert">Abono se ha ingresado con exito</div>';
}
$fecha=date("m-d-Y");
		echo  ' <div class="container "> 
        <form method="post" name="form1" id="form1" action="">
            <div class="mb-3">
                <label for="razon" class="form-label">Concepto</label>
                <input type="text" class="form-control" name="razon" id="razon" placeholder="Ingrese el concepto" required>
            </div>
            <div class="mb-3">
                <label for="monto" class="form-label">Monto</label>
                <input type="number" class="form-control" name="monto" id="monto" placeholder="Ingrese el monto" required>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" id="fecha" value="'.$fecha.'" required>
				
            </div>
    
            <button type="submit" id="abono" name="abono" class="btn btn-primary">Agregar a la liquidación</button>
        </form>
    </div>';
	}
	public function ingresarDescuento(){
		if(isset($_GET["idArriendo"])){
			$idArriendo=$_GET["idArriendo"];
		}
		if(isset($_POST["abono"])){
			
			$idLiq=$_GET["idLiq"];
			$sql="insert into mm_detalleLiquidacion (idLiquidacion,concepto,abono,descuento,monto,fijo) ";
			$sql.=" values ('".$idLiq."','".$_POST["razon"]."','0','".$_POST["monto"]."','".$_POST["monto"]."','1');";
			 
			mysqli_query($this->link,$sql);
			$this->pdf=new pdf();
		$this->pdf->generarPdf($idLiq);
		echo "<script>document.location='panel.php?op=26&idArriendo=".$idArriendo."&act=cobros&z=desc&v=det&idLiq=".$idLiq."&msg=1';</script>";				 		 	
		
			exit;
		}
if(isset($_GET["msg"])){
	echo '<div class="alert alert-primary" role="alert">Descuento se ha ingresado con exito</div>';
}
		echo  ' <div class="container "> 
        <form method="post" name="form1" id="form1" action="">
            <div class="mb-3">
                <label for="razon" class="form-label">Concepto</label>
                <input type="text" class="form-control" name="razon" id="razon" placeholder="Ingrese el concepto" required>
            </div>
            <div class="mb-3">
                <label for="monto" class="form-label">Monto</label>
                <input type="number" class="form-control" name="monto" id="monto" placeholder="Ingrese el monto" required>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" id="fecha" required>
            </div>
    
            <button type="submit" id="abono" name="abono" class="btn btn-primary">Agregar a la liquidación</button>
        </form>
    </div>';
	}
	public function editarComisiones($idArriendo,$idLiq){
		
		$this->link=$this->conectar();
		// editar comisiones de la tabla arriendos y de la tabla detalle liquidaciones y genera un nuevo pdf
		$sql="select* from mm_arriendos where idArriendo='".$idArriendo."'";
		$q=mysqli_query($this->link,$sql);
		$r1=mysqli_fetch_array($q);
		echo "<div>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Cambios Guardados con exito!!!");}}
	$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
	
	$sql="select* from mm_propiedad1 order by idProp desc";
	$q=mysqli_query($this->link,$sql);
	
	
	echo "<input type='text' name='idPropiedad' value='".$this->devolverPropiedad($r1["idProp"])."' class='form-control form-control-sm' disabled/>";

if($r1["monedaComisionPropietario"]==3){
	$this->miForm->addPor("valorComisionPropietario",550,"Comision a propietario","Comisión a propietario:",$r1["valorComisionPropietario"]);
	$this->miForm->addPor("valorComision",550,"Comision de arriendo","Comisión de arriendo:",$r1["valorComision"]);	
	$this->miForm->addPor("valorComisionAdmin",550,"Comision de administración","Comisión de administración:",$r1["valorComisionAdmin"]);
}else{
	$this->miForm->addText("valorComisionPropietario",550,"Comision a propietario","Comisión a propietario:",$r1["valorComisionPropietario"]);
	$this->miForm->addText("valorComision",550,"Comision de arriendo","Comisión de arriendo:",$r1["valorComision"]);	
	$this->miForm->addText("valorComisionAdmin",550,"Comision de administración","Comisión de administración:",$r1["valorComisionAdmin"]);
}
	
	
	$this->miForm->addHidden("action","true"); $this->miForm->addButton("Enviar","Guardar Cambios",false,false);
	$this->miForm->procesar();
	
	if($_POST["action"]){ $d=$_POST;

	$sql="update mm_arriendos set 
	valorComisionPropietario='".$d["valorComisionPropietario"]."',
	valorComision='".$d["valorComision"]."',		
	valorComisionAdmin='".$d["valorComisionAdmin"]."'		
	 where idArriendo='".$idArriendo."'";
 	mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
	 $sql1="delete from  mm_detalleLiquidacion where idLiquidacion='".$idLiq."'";
	 mysqli_query($this->link,$sql1);

	 $sql="select* from mm_liquidacionArriendo where idArriendo='".$idArriendo."' and gastosFijosIngresados=0";
	 $sql2="update mm_liquidacionArriendo set gastosFijosIngresados=0 where idArriendo='".$idArriendo."'";
	 mysqli_query($this->link,$sql2);

	 if($this->ingresarGastosFijos($idArriendo,$idLiq)){
		$this->pdf=new pdf();
		$this->pdf->generarPdf($idLiq);
		echo "<script>document.location='panel.php?op=26&idArriendo=".$idArriendo."&act=cobros&z=comisiones&v=det&idLiq=".$idLiq."&msg=1';</script>";				 		 			
		exit;
	 }else{
		echo "no se pudo modificar error";
	 }
	}




		echo "</div>";
		echo "</div>";
	}
	public function fichaArriendo(){
		// 0=propietario y 1=arrendatario
		if(isset($_GET["idArriendo"])){
			$idArriendo=htmlentities($_GET["idArriendo"]);
		}
		$sql="select* from mm_arriendos where idArriendo='".$idArriendo."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
 
		echo "<div>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<h3>Ficha Arriendo </h3><h5>".$this->devolverPropiedad($r["idProp"])."</h5>";
		echo "</div>";
		echo "</div>";

		echo "<div class='row' style='margin-top:20px;'>";
		
		echo "<div class='col-md-3'>";
		
		echo '<div class="list-group">
		<a href="panel.php?op=26&idArriendo='.$idArriendo.'" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && !isset($_GET["act"])){
			echo "active";
		}
		echo '" aria-current="true">
			Información
		</a>
	 	


		<a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=cobros" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && isset($_GET["act"]) && $_GET["act"]=="cobros"){
			echo "active";
		}
		echo '">Cobros</a>


		<a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=garantia&idProp='.$r["idProp"].'" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && isset($_GET["act"]) && $_GET["act"]=="garantia" ){
			echo "active";
		}
		echo '">Garantia</a>



		<a href="panel.php?op=26&idArriendo='.$idArriendo.'&act=reajustes&idProp='.$r["idProp"].'" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && isset($_GET["act"]) && $_GET["act"]=="reajustes"){
			echo "active";
		}
		echo '">Reajustes</a>



	  </div>';


		echo "</div>";

		echo "<div class='col-md-9'>";
		if(isset($_GET["act"])){
			$a=$_GET["act"];
			if($a=="cobros"){

				if(isset($_GET["z"]) && $_GET["z"]=="abono"){
					echo "<div><h4>Ingresar Abono</h4></div>";
					$this->ingresarAbono();
				}else if(isset($_GET["z"]) && $_GET["z"]=="comisiones"){
					echo "<div><h4>Editar Comisiones de arriendo</h4></div>";
					echo "<div style='margin-bottom:10px;'><span style='font-size:14px;'>Al cambiar esta información afectara a todas las liquidaciones generadas ya que se calcularan con estos nuevos valores, esta modificación va directamente en el arriendo creado</span></div>";
					$this->editarComisiones($_GET["idArriendo"],$_GET["idLiq"]);
				}else if(isset($_GET["z"]) && $_GET["z"]=="desc"){
					echo "<div><h4>Descuento</h4></div>";
					$this->ingresarDescuento();
				}else{
				if($_GET["v"]=="det"){
					echo "<div><h5>Detalle del cobro</h5></div>";
					if(isset($_GET["idLiq"])){
						$idLiq=htmlentities($_GET["idLiq"]);
					}
					$this->tablaCobros($idLiq);
				}else if($_GET["v"]=="pdf"){
					echo "<div><h5>Ver Pdf</h5></div>";
				}else{
					echo "<div><h5>Cobros generados</h5></div>";
					$this->tabla_mm_LiquidacionArriendo($r["idArriendo"]);
				}
			}
		 	
			}else if($a=="garantia"){
				
				$this->garantia($idArriendo);
			}else if($a=="reajustes"){
				if(isset($_GET["s"]) && $_GET["s"]=="crear"){
					echo "<div><h4>Crear Reajuste</h4></div>";
					$this->crearReajuste($idArriendo);
				}else{
					echo "<div><h4>Reajustes</h4></div>";
					$this->reajustes($idArriendo);
				}
				
			}
			
			
		}else{		
			echo "<div><h4>Información</h4></div>";
			echo '<div class="row">
				<div class="col-md-6">';
					
			
			echo "<p><strong>Dirección:</strong> <a href='panel.php?op=15&idProp=".$r["idProp"]."'>".$this->devolverPropiedad($r["idProp"])."</a></p>";
			echo "<p><strong>Codeudor:</strong> <a href='panel.php?op=19&idCoud=".$r["idCodeudor"]."'>" . $this->devolverCodeudor($r['idCodeudor']) . "</a></p>";
			
			echo "<p><strong>Fecha de inicio:</strong> " . date("d-m-Y",$r['fechaInicio']) . "</p>";
			echo "<p><strong>Fecha de termino:</strong> " . date("d-m-Y",strtotime($r['fechaCancelacion'])) . "</p>";
			echo "<p><strong>Duración del contrato:</strong> " . $r['duracionContrato'] . "</p>";
			echo "<p><strong>Cobrar mes calendario:</strong> " . ($r['cobrarMesCalendario'] ? 'Sí' : 'No') . "</p>";


			echo "<p><strong>Cobrar comisión de arriendo:</strong> " . ($r['comisionArriendo'] ? 'Sí' : 'No') . "</p>";

			if($r["monedaComision"]==3){
				echo "<p><strong>Valor Comisión de arriendo:</strong> " . $r['valorComision'] . "%</p>";
			}else{
				echo "<p><strong>Valor Comisión de arriendo:</strong> $" . $this->formatoNumerico($r['valorComision']) . "</p>";
			}


			echo "<p><strong>Tipo de Reajuste:</strong> " . $this->devolverReajuste($r["reajuste"]) . "</p>";
			
			
			echo '</div>
			
				<div class="col-md-6">
					';
			
			echo "<p><strong>Propietario:</strong> " . $this->devolverPropietario($r['idProp']) . "</p>";
			echo "<p><strong>Precio:</strong> $" . $this->formatoNumerico($r['montoArriendo']) . "</p>";
			echo "<p><strong>Garantía:</strong> ";
			
			if(empty($r["garantia"])){
				echo "Sin Garantia";
			}else{
				echo "$".$this->formatoNumerico($r['garantia']);
			}
			echo "</p>";
			echo "<p><strong>¿Pagar Garantia al propietario?:</strong> " . ($r['garantiaPropi'] ? 'Sí' : 'No'). "</p>";
			
			echo "<p><strong>¿Cobrar comisión de administración?:</strong> " . ($r['cobrarComisionAdmin'] ? 'Sí' : 'No'). "</p>";

			if($r["monedaComisionAdmin"]==3){
				echo "<p><strong>Valor Comisión de Administración:</strong> " . $r['valorComisionAdmin'] . "%</p>";
			}else{
				echo "<p><strong>Valor Comisión de Administración:</strong> $" . $this->formatoNumerico($r['valorComisionAdmin']) . "</p>";
			}
			


			
			echo "<p><strong>Cobrar comisión al propietario:</strong> " . ($r['comisionPropietario'] ? 'Sí' : 'No') . "</p>";

			if($r["monedaComisionPropietario"]==3){
				echo "<p><strong>Valor Comisión de arriendo:</strong> " . $r['valorComisionPropietario'] . "%</p>";
			}else{
				echo "<p><strong>Valor Comisión de arriendo:</strong> $" . $this->formatoNumerico($r['valorComisionPropietario']) . "</p>";
			}

			
			
			echo '
				</div>
			</div>';
			echo "<div class='row'>";
			echo "<div class='col-md-12'>";
			echo "<div style='margin-top:50px;'><h5>Arrendatario</h5></div>";
			echo "<div>";

			echo '<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Nombre</th>
						<th>Rut</th>
						<th>Correo Electrónico</th>
						<th>Teléfono Celular</th>
						
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>';

						$dar=$this->devolverDatosArrendatario($r["idArrendatarios"]);
						
						echo "<a href='panel.php?op=20&idArrendatario=".$r["idArrendatarios"]."'>".$dar["nombre"]."</a>";
						echo '</td>
						<td>'.$dar["rut"].'</td>
						<td>'.$dar["correo"].'</td>
						<td>'.$dar["telefono"].'</td>
						
					</tr>
				</tbody>
			</table>
		</div>';

			echo "</div>";


			echo "</div>";
			echo "</div>";
			
			

		}

		echo "</div>";
		echo "</div>";
	}
	
	public function recuperarMesGarantia($idArriendo){
		$this->link=$this->conectar();
		$sql="select * from mm_arriendos where idArriendo='".$idArriendo."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		return($r["garantia"]);
	}
	public function verificaIngresoGarantia($idArriendo){
		$this->link=$this->conectar();
		$sql="select* from mm_garantia where idArriendo='".$idArriendo."'";
		$q=mysqli_query($this->link,$sql);
		if(mysqli_num_rows($q)==0){
			$valorGarantia=$this->recuperarMesGarantia($idArriendo);
			$fecha=date("Y-m-d");
			$sql2="insert into mm_garantia (idArriendo,saldo,fecha) values ('".$idArriendo."','".$valorGarantia."','".$fecha."');";
			mysqli_query($this->link,$sql2);
			
			$idGarantia=mysqli_insert_id($this->link);
			
			$sql3="insert into mm_detalleGarantia (idGarantia, fecha,concepto,abono,descuento) ";
			$sql3.=" values ('".$idGarantia."','".$fecha."','Mes de Garantia','".$valorGarantia."','0');";
			mysqli_query($this->link,$sql3);


			return($valorGarantia);
		}else{
			return(false);
		}
	}

	public function tablaGarantiaGeneral(){
		// lista general de garantias 
		 
			   

	}


	public function detalleGarantia($idGarantia){
	
		if(isset($_GET["idProp"])){
			$idProp=htmlentities($_GET["idProp"]);
		}
		if(isset($_GET["mq"])){
			$op=$_GET["mq"];
		}
	 
		if($op=="editar"){
			if(isset($_GET["idq"])){
				$idq=$_GET["idq"];
			}
			 $this->editarAbonoGarantia($idq,$idGarantia);
		}else if($op=="borrar"){	
			echo "Aqui borrar";	 
		}else{
			

		$campoIndice="idDet";
		$index="panel.php?op=26&idArriendo=".$_GET["idArriendo"]."&s=editarAbono&act=garantia&idProp=".$_GET["idProp"]."&";
		$nomTabla="mm_garantia";
		
		
	
		$sql="select* from  mm_detalleGarantia where idGarantia='".$idGarantia."'";
	 

		$campos=array('fecha'=>'fecha','concepto'=>'concepto','abono'=>'abono','descuento'=>'descuento');
		
		
		$tamCol=array(20,20,20,20);
	 
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_detGarantia";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
		$grid->desplegarGarantias();
		return($sql);
		}
 
	}



	public function editarAbonoGarantia($idq,$idGarantia){
		if(isset($_GET["idq"])){
			$idDet=$_GET["idq"];
		}
		$idArriendo=$_GET["idArriendo"];
		$sql="select* from mm_detalleGarantia where idDet='".$idDet."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);

		if(isset($_POST["abono"])){		
			
			
			$sql1="update mm_detalleGarantia set concepto='".$_POST["razon"]."',abono='".$_POST["abono"]."',descuento='".$_POST["descuento"]."' where idDet='".$idDet."'";
			 
			 
			mysqli_query($this->link,$sql1);
			$this->pdf=new pdf();
			$this->pdf->generarPdfGarantia($idGarantia,$idArriendo);
			echo "<script>document.location='panel.php?op=26&idArriendo=".$_GET["idArriendo"]."&s=editarAbono&act=garantia&idProp=".$_GET["idProp"]."&&mq=editar&idq=".$idDet."&msg=1';</script>";				 		 			 	
			exit;
		}
if(isset($_GET["msg"])){
	echo '<div class="alert alert-primary" role="alert">Abono se ha ingresado con exito</div>';
}
$fecha=date("m-d-Y");
		echo  ' <div class="container "> 
        <form method="post" name="form1" id="form1" action="">
		
            <div class="mb-3" style="margin-top:10px;">
                <label for="razon" class="form-label">Concepto</label>
                <input type="text" class="form-control" name="razon" value="'.$r["concepto"].'" id="razon" placeholder="Ingrese el concepto" required>
            </div>
            <div class="mb-3">
                <label for="monto" class="form-label">Abono</label>
                <input type="number" class="form-control" name="abono" id="abono" value="'.$r["abono"].'" placeholder="Ingrese el monto" required>
            </div>
			<div class="mb-3">
                <label for="monto" class="form-label">Descuento</label>
                <input type="number" class="form-control" name="descuento" id="descuento" value="'.$r["descuento"].'" placeholder="Ingrese el monto" required>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" id="fecha" value="'.$r["fecha"].'" required>
				
            </div>
    
            <button type="submit" id="abono" name="abono" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>';
	}
	 


	public function ingresarAbonoGarantia($saldo){
		if(isset($_GET["idArriendo"])){
			$idArriendo=$_GET["idArriendo"];
		}
		if(isset($_POST["abono"])){
			
			$idGarantia=$_GET["idGarantia"];
			

			$sql="insert into  mm_detalleGarantia (idGarantia,concepto,abono,descuento,fecha) ";
			$sql.=" values ('".$idGarantia."','".$_POST["razon"]."','".$_POST["monto"]."','0','".date("Y-m-d",strtotime($_POST["fecha"]))."');";
			 
			mysqli_query($this->link,$sql);
			$this->pdf=new pdf();
			$this->pdf->generarPdfGarantia($idGarantia,$idArriendo);

			$s=$saldo+$_POST["monto"];
			$sql1="update mm_garantia set saldo='".$s."' where idGarantia='".$idGarantia."'";
			mysqli_query($this->link,$sql1);
			echo "<script>document.location='panel.php?op=26&idGarantia=".$_GET["idGarantia"]."&idArriendo=".$_GET["idArriendo"]."&s=abono&act=garantia&idProp=".$_GET["idProp"]."&msg=1';</script>";				 		 			 	
			exit;
		}
if(isset($_GET["msg"])){
	echo '<div class="alert alert-primary" role="alert">Abono se ha ingresado con exito</div>';
}
$fecha=date("m-d-Y");
		echo  ' <div class="container "> 
        <form method="post" name="form1" id="form1" action="">
		
            <div class="mb-3" style="margin-top:10px;">
                <label for="razon" class="form-label">Concepto</label>
                <input type="text" class="form-control" name="razon" id="razon" placeholder="Ingrese el concepto" required>
            </div>
            <div class="mb-3">
                <label for="monto" class="form-label">Monto</label>
                <input type="number" class="form-control" name="monto" id="monto" placeholder="Ingrese el monto" required>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" id="fecha" value="'.$fecha.'" required>
				
            </div>
    
            <button type="submit" id="abono" name="abono" class="btn btn-primary">Agregar a la garantia</button>
        </form>
    </div>';
	}
	public function ingresarDescuentoGarantia($saldo){
		if(isset($_GET["idArriendo"])){
			$idArriendo=$_GET["idArriendo"];
		}
		if(isset($_POST["abono"])){
			
		 $idGarantia=$_GET["idGarantia"];
		//	$this->pdf=new pdf();
		//$this->pdf->generarPdf($idLiq);
		$sql="insert into  mm_detalleGarantia (idGarantia,concepto,abono,descuento,fecha) ";
		$sql.=" values ('".$idGarantia."','".$_POST["razon"]."','0','".$_POST["monto"]."','".date("Y-m-d",strtotime($_POST["fecha"]))."');";
		 
		mysqli_query($this->link,$sql);
		$s=$saldo-$_POST["monto"];
		$sql1="update mm_garantia set saldo='".$s."' where idGarantia='".$idGarantia."'";
		mysqli_query($this->link,$sql1);
		$this->pdf=new pdf();
		$this->pdf->generarPdfGarantia($idGarantia,$idArriendo);
		echo "<script>document.location='panel.php?op=26&idGarantia=".$_GET["idGarantia"]."&idArriendo=".$_GET["idArriendo"]."&s=descuento&act=garantia&idProp=".$_GET["idProp"]."&msg=1';</script>";				 		 			 		
		exit;
		}
if(isset($_GET["msg"])){
	echo '<div class="alert alert-primary" role="alert">Descuento se ha ingresado con exito</div>';
}
		echo  ' <div class="container "> 
        <form method="post" name="form1" id="form1" action="">
            <div class="mb-3">
                <label for="razon" class="form-label">Concepto</label>
                <input type="text" class="form-control" name="razon" id="razon" placeholder="Ingrese el concepto" required>
            </div>
            <div class="mb-3">
                <label for="monto" class="form-label">Monto</label>
                <input type="number" class="form-control" name="monto" id="monto" placeholder="Ingrese el monto" required>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" id="fecha" required>
            </div>
    
            <button type="submit" id="abono" name="abono" class="btn btn-primary">Descontar a la garantia</button>
        </form>
    </div>';
	}

	public function recuperarSaldoGarantia($idGarantia){
		$sql="select* from  mm_detalleGarantia where idGarantia='".$idGarantia."'";
		$q=mysqli_query($this->link,$sql);
		while($r=mysqli_fetch_array($q)){
			$abono[]=$r["abono"];
			$des[]=$r["descuento"];
		}
		$a=array_sum($abono);
		$d=array_sum($des);
		$diff=$a-$d;
		return($diff);
	}
	public function descargarGarantia($idGarantia){
		$sql="select pdf from mm_garantia where idGarantia='".$idGarantia."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		echo "<script>document.location='https://clickcorredores.cl/adminArriendo/pdf/".$r["pdf"]."';</script>";				 		 			 			
		exit;
	}
	public function garantia($idArriendo){
		$sql="select idGarantia,saldo from mm_garantia where idArriendo='".$idArriendo."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);

		if(isset($_GET["s"]) && $_GET["s"]=="abono"){
			echo "<div><h5>Ingresar Abono</h5></div>";	
			$this->ingresarAbonoGarantia($r["saldo"]);
		}else if(isset($_GET["s"]) && $_GET["s"]=="pdfGarantia2"){
			$idGarantia=$r["idGarantia"];
			$this->descargarGarantia($idGarantia);
		}else if(isset($_GET["s"]) && $_GET["s"]=="descuento"){
			echo "<div><h5>Ingresar Descuento</h5></div>";	
			$this->ingresarDescuentoGarantia($r["saldo"]);

		}else if(isset($_GET["s"]) && $_GET["s"]=="pdfGarantia"){			
			$idGarantia=$r["idGarantia"];
			$idArriendo=$_GET["idArriendo"];
			$this->pdf=new pdf();
			if($this->pdf->generarPdfGarantia($idGarantia,$idArriendo)){
				echo "<script>document.location='panel.php?op=26&idArriendo=".$_GET["idArriendo"]."&act=garantia&idProp=".$_GET["idProp"]."&msg=2';</script>";				 		 	
				
				exit;
			}


		}else if(isset($_GET["s"]) && $_GET["s"]=="editarAbono"){
			if(isset($_GET["mq"]) && $_GET["mq"]=="borrar"){
				$idq=$_GET["idq"];
				$sql2="delete from mm_detalleGarantia where idDet='".$idq."'";
				mysqli_query($this->link,$sql2);
				echo "<script>document.location='panel.php?op=26&idArriendo=".$_GET["idArriendo"]."&act=garantia&idProp=".$_GET["idProp"]."&msg=2';</script>";				 		 	
				
				exit;
			}else{
				$idGarantia=$r["idGarantia"];
				$idArriendo=$_GET["idArriendo"];
				echo "<div><h5>Editar</h5></div>";	
				$this->editarAbonoGarantia();
			}
		}else{
			if(isset($_GET["msg"]) && $_GET["msg"]==1){
				echo '<div class="alert alert-primary" role="alert">
				Se ha eliminado con exito !!				
				</div>';
			}else if(isset($_GET["msg"]) && $_GET["msg"]==2){
				echo '<div class="alert alert-primary" role="alert">
				Liquidación de garantia creada con exito!!
			  </div>';
			}
			echo "<div><h5>Garantia</h5></div>";
			
		$this-> verificaIngresoGarantia($idArriendo);
	
		$idGarantia=$r["idGarantia"];
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		
 
		echo "<div style='margin-top:10px;'><span style='font-size:18px;'>Saldo: $".$this->formatoNumerico($this->recuperarSaldoGarantia($idGarantia))."</span></div>";

		echo "<div style='margin-top:20px;margin-bottom:10px;'><span style='font-size:20px; font-weight:600;'>Movimientos</span></div>";
		

	 
		echo "<div class='row' style='margin-bottom:10px;margin-top:20px;'>";
    
		echo "<div class='col-md-3'>";
		echo "<a href='panel.php?op=26&idGarantia=".$idGarantia."&idArriendo=".$_GET["idArriendo"]."&s=abono&act=garantia&idProp=".$_GET["idProp"]."' class='btn btn-primary btn-sm' role='button'><i class='fas fa-money-check-alt'></i> Ingresar abono</a>";
		echo "</div>";
	
		echo "<div class='col-md-3'>";
		echo "<a href='panel.php?op=26&idGarantia=".$idGarantia."&idArriendo=".$_GET["idArriendo"]."&s=descuento&act=garantia&idProp=".$_GET["idProp"]."' class='btn btn-primary btn-sm' role='button'><i class='fas fa-tags'></i> Ingresar descuento</a>";
		echo "</div>";
		echo "<div class='col-md-3'>";
		echo "<a href='panel.php?op=26&idArriendo=".$_GET["idArriendo"]."&s=pdfGarantia&act=garantia&idProp=".$_GET["idProp"]."' class='btn btn-info btn-sm' role='button'><i class='fas fa-sync'></i> Generar Liquidación</a>";
		echo "</div>";
		echo "<div class='col-md-3'>";
		echo "<a href='panel.php?op=26&idArriendo=".$_GET["idArriendo"]."&s=pdfGarantia2&act=garantia&idProp=".$_GET["idProp"]."' target='_blank' class='btn btn-danger btn-sm' role='button'><i class='fas fa-download'></i> Descargar Liquidación</a>";
		echo "</div>";
		echo "</div>";
	

		
		echo "<div>";
		$this->detalleGarantia($idGarantia);
		echo "</div>";

		echo "</div>";
		echo "</div>";
		}
	}
	public function verificarReajuste($idArriendo){		 
		 $mes_actual = date('m');
		 $anio_actual = date('Y');	 
		 // Consulta para verificar si hay algún reajuste para el mes y año actual
		 $sql = "SELECT * FROM  mm_reajustes
				 WHERE idArriendo = '$idArriendo' 
				 AND MONTH(fecha) = '$mes_actual'
				 AND YEAR(fecha) = '$anio_actual'";
	 
		 $q = mysqli_query($this->link, $sql);
	 
		 // Si no hay resultados, significa que no se ingresó ningún reajuste para el mes actual
		 if(mysqli_num_rows($q) == 0){
			 return false;
		 } else {
			 return true;
		 }
	}

	public function  devolverValorArriendo($idArriendo){
		$sql="select * from  mm_arriendos where idArriendo='".$idArriendo."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$m["montoArriendo"]=$r["montoArriendo"];
		$m["fechaInicio"]=date("Y-m-d",$r["fechaInicio"]);
		$m["tipoReajuste"]=$this->devolverReajuste($r["reajuste"]);
		$m["tReajuste"]=$r["reajuste"];
		$m["porcentaje"]=$r["porcentaje"];
		return($m);
	}
	public function insertarReajuste($idArriendo){
		$m=$this->devolverValorArriendo($idArriendo);
		$valorOriginal=$m["montoArriendo"];
		if($m["tReajuste"]==2){
			$ipc = $this->devolverIPC();
			 
			
			$incremento = $valorOriginal * ($ipc / 100);
			$valorAjustado = $valorOriginal + $incremento;	

			 
		}else if($m["tReajuste"]==2){
				// porcentual	
				$por=$m["porcentaje"];
				$incremento = $valorOriginal * ($por / 100);
				$valorAjustado = $valorOriginal + $incremento;	
		}else{
			// fijo en pesos
			$valorAjustado=$valorOriginal+$m["porcentaje"];
		}
		

		$sql="insert into mm_reajustes (idArriendo,fecha, precioOriginalArriendo, precioReajustado, tasaReajuste,tipoReajuste) ";
		$sql.="	VALUES ('".$idArriendo."','".$m["fechaInicio"]."','".$m["montoArriendo"]."','".$valorAjustado."','".$ipc."','".$m["tipoReajuste"]."');";
		mysqli_query($this->link,$sql);

		$sql2="update mm_arriendos set montoArriendo='".$valorAjustado."' where idArriendo='".$idArriendo."'";
		mysqli_query($this->link,$sql2);
		return(true);
	}
	public function mesAno($fecha){
		setlocale(LC_TIME, 'spanish');

		$fecha_actual = $fecha;
		$mes = strftime('%B', strtotime($fecha_actual));
		$año = date("Y", strtotime($fecha_actual));
		$cad=ucfirst($mes)."&nbsp;".$año;
		return($cad);
	  }
	  
	
	public function crearReajuste($idArriendo){
		
		$d=$this->devolverValorArriendo($idArriendo);
	 
		$montoArriendo=$d["montoArriendo"];
		$fecha2=$this->mesAno(date("Y-m-d"));
		if(isset($_POST["submit"])){
			if(!$this->verificarReajuste($idArriendo)){
					$this->insertarReajuste($idArriendo);
					echo "<script>document.location='panel.php?op=26&idArriendo=".$_GET["idArriendo"]."&act=garantia&idProp=".$_GET["idProp"]."&msg=2';</script>";				 		 	
						echo "<script>document.location='panel.php?op=26&s=crear&idArriendo=".$_GET["idArriendo"]."&act=reajustes&idProp=".$_GET["idProp"]."&msg=1';</script>";
					exit;
				}else{
					echo '<script>alert("Ya se ingreso un reajuste para este mes");</script>';
				}
		}
		if(isset($_GET["msg"])){
			echo '<div class="alert alert-primary" role="alert">
			Reajuste ingresado con exito !!!
		  </div>';
		}
		echo '<form action="" name="form1" id="form1" method="post">
	
		<div class="mb-3" style="margin-top:15px;">
			<label for="fecha" class="form-label">Mes del nuevo reajuste:</label>
			';
			echo "<div style='margin-top:2px;'><b>".$fecha2."</b></div>";
			echo '
		</div>
	
		<div class="mb-3">
			<label for="precio_original" class="form-label">Precio Original Arriendo:</label>
			<input type="text" class="form-control" id="precio_original" disabled value="$'.$this->formatoNumerico($montoArriendo).'" name="precio_original">
		</div>';

		$ipc=$this->devolverIPC();
		if($d["tReajuste"]==2){
			// ipc
			echo '
		<div class="mb-3">
			<label for="ipc" class="form-label">IPC:</label>
			<input type="text" value="'.$ipc.'" disabled class="form-control" id="ipc" name="ipc">
		</div>';
		}else if($d["tReajuste"]==3){
			// pporcentual
			echo '<div class="mb-3">
			<label for="porcentaje" class="form-label">Porcentaje %:</label>
			<input type="text"  value="'.$d["porcentaje"].'" class="form-control" id="porcentaje" name="porcentaje">
		</div>';

		}else if($d["tReajuste"]==4){
			// valor fijo
			echo '<div class="mb-3">
			<label for="valorFijo" class="form-label">valorFijo:</label>
			<input type="text" value="'.$d["porcentaje"].'" disabled class="form-control" id="valorFijo" name="valorFijo">
		</div>';
		}
	
	
 
	
	
		echo '<div class="mb-3">
			<label for="tipo_reajuste" class="form-label">Tipo de Reajuste:</label>
			<select class="form-select" disabled id="tipo_reajuste" name="tipo_reajuste">
				<option value="2" name="tipo_reajuste">'.$d["tipoReajuste"].'</option>
				
			</select>
		</div>
	 
	
		<button type="submit" class="btn btn-primary" id="submit" name="submit">Crear Reajuste</button>
	</form>';
	}

	public function reajustes($idArriendo){
		if(isset($_GET["msg"])){
	 
			$msg=htmlentities($_GET["msg"]);
			
			if($msg==1){
				$msgBox = new msgBox(1,"Cambios Guardados con exito!!!");
			}else if($msg==3){
				$msgBox = new msgBox(1,"Reajuste se ha eliminado con exito!!!");
			}
		}
		if(isset($_GET["idProp"])){
			$idProp=htmlentities($_GET["idProp"]);
		}
		if(isset($_GET["mq"])){
			$op=$_GET["mq"];
		}
	 		
		if($op=="borrar"){
			$id=htmlentities($_GET["idq"]);	 
			$sql1="delete from mm_reajustes where idReajuste='".$id."'";
			mysqli_query($this->link,$sql1);		
			
				echo "<script>document.location='panel.php?op=26&idArriendo=".$_GET["idArriendo"]."&act=reajustes&idProp=".$_GET["idProp"]."&msg=2';</script>";
			exit;

		}else if($op=="editar"){
			$id=htmlentities($_GET["idq"]);	 
			$sql="select* from mm_arriendos where idArriendo='".$id."'";
			$q=mysqli_query($this->link,$sql);
			$r=mysqli_fetch_array($q);
			if(isset($_POST["reajuste"])){
				
				$sql1="update mm_arriendos set porcentaje='".$_POST["por"]."' where idArriendo='".$id."'";
				mysqli_query($this->link,$sql1);
					echo "<script>document.location='panel.php?op=26&idArriendo=".$_GET["idArriendo"]."&act=reajustes&idProp=".$_GET["idProp"]."&mq=editar&idq=".$id."&msg=1';</script>";
				exit;
			}
		
			echo "<form method='post' action='' name='form1' id='form1'>";
			echo "<div class='row'>";
			echo "<div class='col-md-12'>";
			echo "<div> Fecha de inicio</div>";
			echo "<div><input type='text' class='form-control form-control-sm' name='por' id='por' value='".date("d-m-Y",$r["fechaInicio"])."' disabled/>";

			echo "<div>Reajuste</div>";
			echo "<div><input type='text' class='form-control form-control-sm' name='por' id='por' value='".$r["porcentaje"]."'/>";
			echo "</div>";

			echo "<div style='margin-top:10px;'>";
			echo "<input type='submit' name='reajuste' id='reajuste' class='btn btn-primary btm-sm' value='Guardar Cambios'>";
			echo "</div>";
			
			echo "</div>";
			echo "</form>";
		}else if($op=="borrar"){		 
		}else{
		$campoIndice="idReajuste";
		$index="panel.php?op=26&idArriendo=".$idArriendo."&act=reajustes&idProp=".$idProp;
		$nomTabla="mm_reajustes";
		$campoFoto=array("ruta"=>true);
		//$nomTablaFoto="mm_cape_fotos";
	 
	
		$sql="select* from mm_reajustes where idArriendo='".$idArriendo."'";
		
		
		
		$campos=array('fechaInicio'=>'FechaInicio',
		'precioOriginalArriendo'=>'precioOriginalArriendo',
		'precioReajustado'=>'precioReajustado',
		'tasaReajuste'=>'tasaReajuste',
		'tipoReajuste'=>'tipoReajuste',
		'porcentaje'=>'porcentaje');
		
		
		$tamCol=array(10,60,20);
		$campoFoto=array("foto"=>true);
		$filtrar1=array('buscador'=>'nombre');
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_reajustes";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
		echo "<div style='margin-bottom:10px;'>";
		echo "<button disabled class='btn btn-info btn-sm'><i class='far fa-calendar'></i> Asignar Meses</button>&nbsp;<a href='panel.php?op=26&s=crear&idArriendo=".$_GET["idArriendo"]."&act=reajustes&idProp=".$_GET["idProp"]."' class='btn btn-primary btn-sm'><i class='fas fa-edit'></i> Agregar Reajuste</a>";
		echo "</div>";
		$grid->desplegarDatos();
		return($sql);
		}
 
	

	}
	public function fichaArrendatario(){
		// 0=propietario y 1=arrendatario
		if(isset($_GET["idArrendatario"])){
			$idArrendatario=htmlentities($_GET["idArrendatario"]);
		}
		$sql="select* from mm_arrendatario where idArrendatario='".$idArrendatario."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
 
		echo "<div>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<h3>Arrendatario ".$r["nombre"]."&nbsp;".$r["apellido"]."</h3>";
		echo "</div>";
		echo "</div>";

		echo "<div class='row' style='margin-top:20px;'>";
		
		echo "<div class='col-md-3'>";
		
		echo '<div class="list-group">
		<a href="panel.php?op=20&idArrendatario='.$idArrendatario.'" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && !isset($_GET["act"])){
			echo "active";
		}
		echo '" aria-current="true">
			Información
		</a>
		<a href="panel.php?op=20&idArrendatario='.$idArrendatario.'&act=arriendo" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && isset($_GET["act"]) && $_GET["act"]=="arriendo"){
			echo "active";
		}
		echo '">Arriendos</a>		


		<a href="panel.php?op=20&idArrendatario='.$idArrendatario.'&act=solicitud" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && isset($_GET["act"]) && $_GET["act"]=="solicitud"){
			echo "active";
		}
		echo '">Solicitudes Mantención</a>	


	  </div>';


		echo "</div>";

		echo "<div class='col-md-9'>";
		if(isset($_GET["act"]) && $_GET["act"]=="arriendos"){
			echo "<div><h4>Arriendos</h4></div>";
			$this->mostrarArriendos($idArrendatario);
		}else if($_GET["act"]=="solicitud"){
			$this->tablaMantenimiento();
		}else{		
		echo "<div><h4>Información</h4></div>";
		echo "<div class='row'>";
		
		echo "<div class='col-md-4'>";
		echo "<div><b>Rut</b></div>";
		echo "<div>".$r["rut"]."</div>";

		echo "<div><b>Tipo de Propietario</b></div>";
		echo "<div>".$r["tipo"]."</div>";

		echo "</div>";
		
		
		echo "<div class='col-md-4'>";
		echo "<div><b>Telefono</b></div>";
		echo "<div>".$r["telefono"]."</div>";

		echo "<div style='margin-top:10px;'><b>Email</b></div>";
		echo "<div>".$r["email"]."</div>";


		echo "</div>";

		
		echo "<div class='col-md-4'>";
		echo "<div><b>Empresa</b></div>";
		echo "<div>".$r["empresa"]."</div>";

		echo "<div style='margin-top:10px;'><b>Dirección</b></div>";
		echo "<div>".$r["direccion"]."</div>";
		echo "</div>";


		echo "</div>";


		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<div style='margin-top:20px;margin-bottom:10px;'><b>Cuentas Bancarias</b></div>";
		
		echo "<div>";
		$this->tabla_mm_cuentaBancaria(1);
		echo "</div>";

		
		echo "</div>";
		echo "</div>";


		echo "<div class='row' style='margin-top:20px;'>";
		echo "<div class='col-md-12'>";
		echo "<div><b>Comentarios</b></div>";
		echo "<div>";
		echo $r["comentarios"];
		echo "</div>";

		echo "</div>";
		echo "</div>";
		


		echo "</div>";
		}

		echo "</div>";
		echo "</div>";
	}
	
	public function mostrarArriendos($idArrendatario){ 
		if($op=="editar"){
			$id=htmlentities($_GET["idq"]);	 
		}else if($op=="borrar"){		 
		}else{
		$campoIndice="idArrendatario";
		$index="panel.php?m=1";
		$nomTabla="mm_arrendatario";
		$campoFoto=array("ruta"=>true);
		//$nomTablaFoto="mm_cape_fotos";
	 
		$sql='select mm_arrendatario.*, mm_arriendos.idProp, mm_arriendos.fechaInicio ';
		$sql.=' FROM mm_arrendatario ';
		$sql.='	JOIN mm_arriendos ON mm_arrendatario.idArrendatario = mm_arriendos.idArrendatarios ';
		$sql.='	WHERE mm_arrendatario.idArrendatario = "'.$idArrendatario.'"'; 
		
		$campos=array('idProp'=>'Ficha','Direccion'=>'direccion','fechaInicio'=>'fechaInicio');
		
		
		$tamCol=array(10,60,20);
		$campoFoto=array("foto"=>true);
		$filtrar1=array('buscador'=>'nombre');
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_arrendatario";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
		$grid->desplegarDatos();
		return($sql);
		} }

 
	public function fichaPropiedad(){
		$this->link=$this->conectar();
		if(isset($_GET["idProp"])){
			$idProp=htmlentities($_GET["idProp"]);
			$sql="select* from mm_propiedad1 where idProp='".$idProp."'";
 
			$q=mysqli_query($this->link,$sql);
			$r=mysqli_fetch_array($q);
		 
		}
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<h3 style='float:left;'>Ficha de la propiedad</h3><h4 style='color:gray;padding-top:5px;font-weight:500 !important;'>&nbsp;&nbsp;".$r["titulo"]."</h4>";
		echo "</div>";
		echo "</div>";
		echo "<div class='row' style='margin-top:20px;'>";
		
		echo "<div class='col-md-3'>";
		if($_SESSION["auth"]["tipo"]=="admin"){
			$index="panel.php";
		}else{
			$index="panelPropietario.php";
		}
		echo '<div class="list-group">
		<a href="'.$index.'?op=15&idProp='.$idProp.'" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && isset($_GET["idProp"]) && !isset($_GET["act"])){
			echo "active";
		}
		echo '" aria-current="true">
			Información
		</a>
		<a href="'.$index.'?op=15&idProp='.$idProp.'&act=liquidaciones" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && isset($_GET["idProp"]) && isset($_GET["act"]) && $_GET["act"]=="liquidaciones"){
			echo "active";
		}
		echo '" aria-current="true">
		Liquidaciones
		</a>';
		if($_SESSION["auth"]["tipo"]=="admin"){
		echo '<a href="'.$index.'?op=15&idProp='.$idProp.'&act=ctaServicios" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && isset($_GET["idProp"]) && isset($_GET["act"]) && $_GET["act"]=="ctaServicios"){
			echo "active";
		}
	
		echo '" aria-current="true">
		Cuentas de servicio
		</a>';
	}
		echo '
	  </div>';

		echo "</div>";

		echo "<div class='col-md-9'>";
		 echo "<div class='row'>";
		 
		 if($_GET["act"]=="liquidaciones"){
			echo "<div class='col-md-12'>";
			echo "<div><h5>Liquidaciones</h5></div>";
				$this->tablaLiquidacionesPropiedad($idProp);
		 }else if($_GET["act"]=="ctaServicios"){
			echo "<div class='col-md-12'>";
			echo "<div><h5>Cuentas de Servicio</h5></div>";
			echo "<div>";
			$this->tablaGastos($idProp);
			echo "</div>";
			echo "<div style='margin-top:30px;'><a href='panel.php?op=22&idProp=".$idProp."&idServ=1' class='btn btn-primary btm-sm'>Crear Cuenta Agua</a>
			<a  href='panel.php?op=22&idProp=".$idProp."&idServ=2' class='btn btn-primary btm-sm'>Crear Cuenta Luz</a>&nbsp;<a  href='panel.php?op=22&idProp=".$idProp."&idServ=3' class='btn btn-primary btm-sm'>Crear Cuenta Gas</a>
			</div>";
		 }else{
			echo "<div class='col-md-6'>";
		 
		echo "<div><b>Propietario</b></div>";
		if($_SESSION["auth"]["tipo"]=="admin"){
			echo "<div><a href='".$index."?op=14&idPropi=".$r["idPropietario"]."'>".$this->devolverPropietario($r["idPropietario"])."</a></div>";
		}else{
			echo "<div>".$this->devolverPropietario($r["idPropietario"])."</div>";
		}
		

		echo "<div style='margin-top:10px;'><b>Tipo de Propiedad</b></div>";
		echo "<div>".$this->devolverTipoProp($r["tipoProp"])."</div>";
	 
		echo "<div style='margin-top:10px;'><b>Región</b></div>";
		echo "<div>".$this->devolverRegion($r["idRegion"])."</div>";

		echo "<div style='margin-top:10px;'><b>Ciudad</b></div>";
		echo "<div>".$this->devolverCiudad($r["idCiudad"])."</div>";

		echo "<div style='margin-top:10px;'><b>Comuna</b></div>";
		echo "<div>".$this->devolverComuna($r["idComuna"])."</div>";

	
		echo "<div style='margin-top:10px;'><b>Precio</b></div>";
		echo "<div>$ ".$this->formatoNumerico($r["precio"])."</div>";

		echo "<div style='margin-top:10px;'><b>Dirección de la propiedad</b></div>";
		echo "<div>".$r["direccionProp"]."</div>";

		echo "<div style='margin-top:20px;'>";
		echo ' <div id="map" style="width: 100%; height: 320px;"></div>';
		echo "</div>";

		echo "</div>";
		echo "<div class='col-md-6'>";
		
		

		echo "<div style='margin-top:10px;'><b>Logia</b></div>";
		echo "<div>".$r["logia"]."</div>";

		echo "<div style='margin-top:10px;'><b>Metros Construidos</b></div>";
		echo "<div>".$r["m2Construido"]."</div>";

		echo "<div style='margin-top:10px;'><b>Metros Totales</b></div>";
		echo "<div>".$r["mt2Totales"]."</div>";
		
		
		echo "<div style='margin-top:10px;'><b>Piscina</b></div>";
		echo "<div>".$r["piscina"]."</div>";
		
		echo "<div style='margin-top:10px;'><b>Baños</b></div>";
		echo "<div>".$r["banos"]."</div>";
		
		
		echo "<div style='margin-top:10px;'><b>Bodega</b></div>";
		echo "<div>".$r["bodega"]."</div>";

		echo "<div style='margin-top:10px;'><b>Cocina</b></div>";
		echo "<div>".$r["cocina"]."</div>";

		echo "<div style='margin-top:10px;'><b>Tipo de Cocina</b></div>";
		echo "<div>".$r["tipoCocina"]."</div>";
		 



		
		echo "<div style='margin-top:10px;'><b>Dormitorios</b></div>";
		echo "<div>".$r["dormitorios"]."</div>";

		 
		echo "<div style='margin-top:10px;'><b>Estacionamiento</b></div>";
		echo "<div>".$r["estacionamiento"]."</div>";

		echo "<div style='margin-top:10px;'><b>Conserjeria</b></div>";
		echo "<div>".$r["conser"]."</div>";

		echo "<div style='margin-top:10px;'><b>Quincho</b></div>";
		echo "<div>".$r["quincho"]."</div>";
		
		echo "<div style='margin-top:10px;'><b>Areas Comunes</b></div>";
		echo "<div>".$r["areasComunes"]."</div>";
	 
 
		echo "</div>";


		echo "</div>";

		echo "</div>";		

		echo "</div>";
		echo '<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDNpX_3El_MOS7bQnn3jPbDGXiPPnKIiV0"></script>';
		$r2=explode(",",$r["cordenadas"]);
		
		echo '<script>
		// Función para inicializar el mapa
		function initMap() {
		  const coordenadas = { lat: '.$r2[0].', lng: '.$r2[1].' };
  
		  // Crea un mapa en el elemento con id "map"
		  const map = new google.maps.Map(document.getElementById("map"), {
			center: coordenadas,
			zoom: 14, // Nivel de zoom (ajusta según tus necesidades)
		  });
  
		  // Agrega un marcador en las coordenadas especificadas
		  new google.maps.Marker({
			position: coordenadas,
			map: map,
			title: "Ubicación",
		  });
		}
	  </script>
  
	  <!-- Llama a la función initMap() cuando la página se carga -->
	  <script defer>
		google.maps.event.addDomListener(window, "load", initMap);
	  </script>';
	}
	}
	
	public function devolverTipoProp($id){
		$arrSel3=array(1=>"Casa",
		2=>"departamento",
	 3=>"Oficina",
	 4=>"Agrícola",
	 5=>"Bodega",
	 6=>"Comercial",
	 7=>"Estacionamiento",
	 8=>"Galpón",
	 9=>"Industrial",
	 10=>"Terreno",
	 11=>"Turístico"				
	);
	return($arrSel3[$id]);
}
	public function devolverRegion($id){

		$sql="select* from mm_region where idRegion='".$id."'";
        
		$q=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		$r=mysqli_fetch_array($q);
		mysqli_free_result($q);
		return($r["nombre"]);
	}
	public function devolverComuna($id){
		$sql="select* from mm_comuna where idComuna='".$id."'";	 
		$q=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		$r=mysqli_fetch_array($q); 
		mysqli_free_result($q);
		return($r["nombre"]);
	}
	public function devolverCiudad($id){
		$sql="select* from mm_ciudad where idCiudad='".$id."'";
		$q=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		$r=mysqli_fetch_array($q);
		mysqli_free_result($q);
		return($r["ciudad"]);
	}
	public function fichaPropietario(){
		// 0=propietario y 1=arrendatario
		
		if(isset($_GET["idPropi"])){
			$idPropi=htmlentities($_GET["idPropi"]);
		}
		$sql="select* from mm_propietarios where idPropietario='".$idPropi."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
 
		echo "<div>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<h3>Propietario ".$r["nombre"]."&nbsp;".$r["apellido"]."</h3>";
		echo "</div>";
		echo "</div>";

		echo "<div class='row' style='margin-top:20px;'>";
		
		echo "<div class='col-md-3'>";
		
		echo '<div class="list-group">
		<a href="panel.php?op=14&idPropi='.$idPropi.'" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && !isset($_GET["act"])){
			echo "active";
		}
		echo '" aria-current="true">
			Información
		</a>
		<a href="panel.php?op=14&idPropi='.$idPropi.'&act=propiedades" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && isset($_GET["act"]) && $_GET["act"]=="propiedades"){
			echo "active";
		}
		echo '">Propiedades</a>';		
/*
		echo '<a href="panel.php?op=14&idPropi='.$idPropi.'&act=liquidaciones" class="list-group-item list-group-item-action ';
		if(isset($_GET["op"]) && isset($_GET["act"]) && $_GET["act"]=="liquidaciones"){
			echo "active";
		}
		echo '">Liquidaciones</a>		';
*/
		
		
	  echo '</div>';


		echo "</div>";

		echo "<div class='col-md-9'>";
		if(isset($_GET["act"]) && $_GET["act"]=="propiedades"){
			echo "<div><h4>Información</h4></div>";
			echo "<div>";
			$this->mostrarPropiedades($idPropi);
			echo "</div>";
		}else if(isset($_GET["act"]) && $_GET["act"]=="liquidaciones"){
			echo "<div><h4>Liquidaciones</h4></div>";
			echo "<div>";
			 $this->tablaLiquidacionesPropietario($idPropi);
			echo "</div>";
			
		}else{
		echo "<div><h4>Información</h4></div>";
		echo "<div class='row'>";
		
		echo "<div class='col-md-4'>";
		echo "<div><b>Rut</b></div>";
		echo "<div>".$r["rut"]."</div>";

		echo "<div style='margin-top:10px;'><b>Tipo de Propietario</b></div>";
		echo "<div>".$this->devolverTipoPropi($r["tipo"])."</div>";

		echo "</div>";
		
		
		echo "<div class='col-md-4'>";
		echo "<div><b>Telefono</b></div>";
		echo "<div>".$r["telefono"]."</div>";

		echo "<div style='margin-top:10px;'><b>Email</b></div>";
		echo "<div>".$r["email"]."</div>";


		echo "</div>";

		
		echo "<div class='col-md-4'>";
		echo "<div><b>Empresa</b></div>";
		echo "<div>".$r["empresa"]."</div>";

		echo "<div style='margin-top:10px;'><b>Dirección</b></div>";
		echo "<div>".$r["direccion"]."</div>";
		echo "</div>";


		echo "</div>";

		
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<div class='row' style='margin-bottom:10px;margin-top:15px;'>";
		
		echo "<div class='col-md-9'>";
		echo "<div style='margin-top:0px;margin-bottom:0px;'><b>Cuentas Bancarias</b></div>";		
		echo "</div>";

		echo "<div class='col-md-3'>";
		echo "<a href='panel.php?op=21&idPropi=2' style='width:100%;' class='btn btn-primary btn-sm'><i class='fas fa-plus'></i> Agregar Cta.Bancaria</a>";
		echo "</div>";
			

		

		echo "</div>";
		
		echo "<div>";
		$this->tabla_mm_cuentaBancaria(0);
		echo "</div>";

		
		echo "</div>";
		echo "</div>";


		echo "<div class='row' style='margin-top:20px;'>";
		echo "<div class='col-md-12'>";
		echo "<div><b>Comentarios</b></div>";
		echo "<div>";
		echo $r["comentarios"];
		echo "</div>";

		echo "</div>";
		echo "</div>";
		


		echo "</div>";

		}
		echo "</div>";
		echo "</div>";
	}
	public function mostrarPropiedades($idPropi){
		 
		 
		if($op=="editar"){$id=htmlentities($_GET["idq"]);
	 
		}else if($op=="borrar"){
		 
		}else{
		$campoIndice="idProp";
		$index="panel.php?m=1";
		$nomTabla="mm_propiedad1";
		$campoFoto=array("ruta"=>true);
		//$nomTablaFoto="mm_cape_fotos";
		
			$sql="select * from mm_propiedad1 where idpropietario='".$idPropi."'";
		
		
		
		$campos=array(
		'direccionProp'=>'direccionProp');
		//$tipo=array(1=>"Casas",2=>"Departamentos",3=>"Parcelas",4=>"Sitios",5=>"Oficina Comercial",6=>"Propiedad Industrial",7=>"Terreno");
		$tamCol=array(100);
		$campoFoto=array("foto"=>true);
		$filtrar1=array('buscador'=>'nombre');
		$grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);	
		$tabla="mm_propiedad1";
		$grid->asignarCampos($campos,$tamCol,$sql,$tabla);	
		$grid->desplegarDatos();
		return($sql);
		} }
 
	public function modificar_mm_configuracion($id=false){
	$this->link=$this->conectar();
	if(isset($_GET["idq"])){ $id=htmlentities($_GET["idq"]); }
	$sql="select* from mm_configuracion where idConfig='1'";
	$q=mysqli_query($this->link,$sql);
	$row=mysqli_fetch_array($q);
	if($row["notiRecepcion"]==1){
		$notiRecepcion=true;
	}else{
		$notiRecepcion=false;
	}
	if($row["notiFechaVencimiento"]==1){
		$notiFechaVencimiento=true;
	}else{
		$notiFechaVencimiento=false;
	}
	if($row["notiFechaPago"]==1){		
		$notiFechaPago=true;
	}else{
		$notiFechaPago=false;
	}
	if($row["porcentajeComision"]==1){		
		$porcentajeComision=true;
	}else{
		$porcentajeComision=false;
	}
	if($row["notiConfirmacionProp"]==1){		
		$notiConfirmacionProp=true;
	}else{
		$notiConfirmacionProp=false;
	}

	if($row["sms"]==1){		
		$sms=true;
	}else{
		$sms=false;
	}
	if($row["email"]==1){
		$email=true;
	}else{
		$email=false;
	}
	if($row["notiPush"]==1){
		$notiPush=true;
	}else{
		$notiPush=false;
	}

	$this->miForm=new form();
	if(isset($_GET["msg"])){$msg=htmlentities($_GET["msg"]);if($msg==1){$msgBox = new msgBox(1,"Cambios Guardados con exito!!!");}}
	$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
	
	$this->controles("Configuración");
	
	$this->miForm->addSw("notiRecepcion","Notificar Recepción",$notiRecepcion);
	$this->miForm->addSw("notiFechaPago","Notificar fecha pago",$notiFechaPago);
	//$this->miForm->addText("porcentajeComision",550,"porcentajeComision","porcentajeComision:",false);
	$this->miForm->addSw("notiConfirmacionProp","Notficación de confirmación de propietario",$notiConfirmacionProp);
	$this->miForm->addSw("sms","Notificar via SMS",$sms);
	$this->miForm->addSw("email","Notificar via email",$email);	


	$this->miForm->addHidden("action","true"); $this->miForm->addButton("Enviar","Guardar Cambios",false,false);

	
	$this->miForm->procesar();
 
	if($_POST["action"]){ $d=$_POST;
	 
	if(isset($_POST["borrar"])){
	$d["foto"]="";
	}
	if($d["notiRecepcion"]=="on"){
		$d["notiRecepcion"]=1;
	}
	if($d["notiFechaVencimiento"]=="on"){
		$d["notiFechaVencimiento"]=1;
	}
	if($d["notiFechaPago"]=="on"){
		$d["notiFechaPago"]=1;
	}
	if($d["porcentajeComision"]=="on"){
		$d["porcentajeComision"]=1;
	}
	if($d["notiConfirmacionProp"]=="on"){
		$d["notiConfirmacionProp"]=1;
	}

	if($d["sms"]=="on"){
		$d["sms"]=1;
	}
	if($d["email"]=="on"){
		$d["email"]=1;
	}
	if($d["notiPush"]=="on"){
		$d["notiPush"]=1;
	}

	 
	$sql="update mm_configuracion set 
		notiRecepcion='".$d["notiRecepcion"]."',
		notiFechaVencimiento='".$d["notiFechaVencimiento"]."',
		notiFechaPago='".$d["notiFechaPago"]."',
	 
		notiConfirmacionProp='".$d["notiConfirmacionProp"]."',
		sms='".$d["sms"]."',
		email='".$d["email"]."',
		notiPush='".$d["notiPush"]."' where idConfig='1'";
 
		mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
			echo "<script>document.location='prueba2.php?m=1&msg=1';</script>";
		exit;
	}
	$this->miForm->cerrarForm();
	return($sql);
	}
	
	 

} 
?>