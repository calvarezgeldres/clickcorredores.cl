 
<?php
 
 ob_start();
/*
Autor: Luis Olguin  - Programación Web Chile 2015
Descripción : Pagina web autoadministrable para corretaje de propiedades
 
Fecha : 4/3/2015
Revisión:27/7/2017
Revisión:3/8/2015
Revisión: 15/05/2022
Framework; Bootstrap 3
Descripcion:CmsProp 4.1 Lite
 
*/
error_reporting(0);
 
require_once("./clases/class.coneccion.php");
require_once("./clases/class.form.php");
require_once("./clases/class.upload.php");
require_once("./clases/class.paginator.php");
require_once("./clases/class.miniGrid.php");
 
 
require_once("./clases/class.sqlPlus.php");
require_once("./clases/class.monitor.php");
require_once("./clases/class.rotador.php");
 require_once("./clases/class.phpmailer.php");
 require_once("./clases/class.smtp.php");
require_once("./clases/class.gMaps.php");
require_once("./clases/class.msgBox.php");
class orbis extends coneccion{    
    private $miForm;
	public $gMaps;
    private $paginator;
	private $geo;
	private $grid;
	public $titulo;
	private $sql;
	private $login;
	public $rotador;
	private $monitor;
	public $slider;
	public $link;
    public function __construct(){
        
        $this->link=$this->conectar();
 
        $this->miForm=new form();
		$this->geo=true;
        $this->paginator=new paginator(50,50);
	 	$this->sql=new sql();
	
	}
	public function verificaAws($foto){
		
		if(preg_match("/https/",$foto)){
			return(true);
		}else{
			return(false);
		}
	}
	public function desplegarProp3($id){
	 
		if(isset($_GET["km"])){
			$id=htmlentities($_GET["km"]);
		   //$this->detalleProp($id);
	  }else{
	   if(isset($_POST["buscador"])){
		   if( $_POST["operacion"]==0 && $_POST["tipo"]==0 && $_POST["ciudad"]==0 && empty($_POST["proyectos"])){
	 
			   $sql="select * from mm_propiedad  where papelera=0";
 
		   }else{
			   
			  $sql="select * from mm_propiedad where ";
			 
			if(isset($_POST["proyectos"])){
				
				$proyectos=htmlentities($_POST["proyectos"]);
				 if(!empty($proyectos)){
						 $sql.=" titulo='".$proyectos."' and ";
				 }
				}
			  if(isset($_POST["operacion"])){
				  $operacion=htmlentities($_POST["operacion"]);
				  if($operacion!=0){
					   $sql.=" operacion='".$operacion."' and ";
				  }
			  }
			  if(isset($_POST["tipo"])){
				  $tipo=htmlentities($_POST["tipo"]);
				  if($tipo!=0){
					$sql.=" tipoProp='".$tipo."' and ";
				  }
			  }
			  if(isset($_POST["ciudad"])){
				  $ciudad=htmlentities($_POST["ciudad"]);
				  if($ciudad!=0){
					$sql.=" ciudad='".$ciudad."' and ";
				  }
			  }
			
			
			 
				  $sql=substr($sql,0,-4);					
			   $sql.=" and papelera=0";
		   }
		  }else{
			  
			  if(isset($_GET["m"])){
				 if($_GET["m"]==1){
					// ventas en verde
					$sql="select * from mm_propiedad where operacion='1' and papelera=0";
				 }else if($_GET["m"]==2) {
					 // arriendo
					 $sql="select * from mm_propiedad where operacion='2' and papelera=0";				
				 }else{
					$sql="select * from mm_propiedad where papelera=0 order by idProp desc";
					
				 }
			  }else{				  
				$sql="select * from mm_propiedad  where papelera=0 order by idProp desc";
			  }
		  }
		 
		 
		  $this->paginator=new paginator(30,30);
		  
		   $this->paginator->agregarConsulta($sql);          
          $this->paginator->estableceIndex("index.php?k=1");
          $total=$this->paginator->obtenerTotalReg();         
          $query=mysqli_query($this->link,$sql);
         $numCol=$num;
	 
 
          $k=0;
		  if($total==0){
				echo "<div style='padding-top:50px;padding-left:20px; font-size:14px; color:gray;' > No se han encontrado resultados</div>";
		  }else{
			  if(!isset($_GET["m"])){
				  
		  if(isset($_POST["buscar"])){
			    echo "<div style='margin-bottom:10px;padding-left:40px;' >Se han encontrado ".$total." coincidencias</div>";
		  }
			  }
			  
		   while($row=$this->paginator->devolverResultados()){
        	$id=$row["idProp"];
			$sql1="select* from mm_cape_fotos  where idProp='".$id."' order by portada desc";
			
			$query1=mysqli_query($this->link,$sql1) or die(mysql_error($this->link));
			$row1=mysqli_fetch_array($query1);
			$rutaFoto=$row1["ruta"];
			if($this->verificaAws($rutaFoto)){
			$imagen = getimagesize($rutaFoto);
			}else{
				$imagen = getimagesize("./upload/".$rutaFoto);
			}
			$ancho = $imagen[0];          
			$alto = $imagen[1];	
			 
			echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-4 col-xxl-12">
			
			<div class="card" style="width:100%;margin-bottom:50px;">';
		 
			
		  
			 
			echo '
			<a href="index.php?idProp='.$id.'">';
 
			if($alto>700){
			
 
				if($this->verificaAws($rutaFoto)){
					echo '<div  align="center" style="background-color:#eee;"><img src="'.$rutaFoto.'" class="card-img-top" id="fotoAws" name="fotoAws" alt="'.$row["titulo"].'"></div>';
				}else{
					echo '<div   style="background-color:#eee;" align="center"><img src="./upload/'.$rutaFoto.'" style="width:40%;" class="card-img-top"alt="'.$row["titulo"].'"></div>';
				}
				echo '<div class="box-estado"  >
				<span class="estado">';
				
				if($row["estadoProp"]==1){
				   echo 'Se Vende';
			   }else if($row["estadoProp"]==2){
				   echo 'Se Arrienda';
			   }else if($row["estadoProp"]==3){
				   echo 'Arrendado';
			   }else if($row["estadoProp"]==4){
				   echo 'Vendido';
			   }else{
				   echo 'Reservada';
			   }	
				echo '</span>
				</div>
			   </a>';
			}else if($ancho<750){
				
				if($this->verificaAws($rutaFoto)){
					echo '<div align="center" style="background-color:#eee;"><img src="'.$rutaFoto.'" class="card-img-top" style="width:57%;"  alt="'.$row["titulo"].'"></div>';
				}else{
					echo '<div align="center"><img src="./upload/'.$rutaFoto.'" style="width:57%" class="card-img-top"  alt="'.$row["titulo"].'"></div>';
				}
				echo '<div class="box-estado"  >
				<span class="estado">';
				
				if($row["estadoProp"]==1){
				   echo 'Se Vende';
			   }else if($row["estadoProp"]==2){
				   echo 'Se Arrienda';
			   }else if($row["estadoProp"]==3){
				   echo 'Arrendado';
			   }else if($row["estadoProp"]==4){
				   echo 'Vendido';
			   }else{
				   echo 'Reservada';
			   }	
				echo '</span>
				</div>
			   </a>';
			}else{
			
				if($this->verificaAws($rutaFoto)){
					echo '<div align="center"><img src="'.$rutaFoto.'" class="card-img-top" style="width:100%;"  alt="'.$row["titulo"].'"></div>';
				}else{
					echo '<img src="./upload/'.$rutaFoto.'" class="card-img-top" style="width:40% !important;" alt="'.$row["titulo"].'">';
				}
				
		 		
			echo '<div class="box-estado">
                 <span class="estado">';
				 
				 if($row["estadoProp"]==1){
					echo 'Se Vende';
				}else if($row["estadoProp"]==2){
					echo 'Se Arrienda';
				}else if($row["estadoProp"]==3){
					echo 'Arrendado';
				}else if($row["estadoProp"]==4){
					echo 'Vendido';
				}else{
					echo 'Reservada';
				}	
				 echo '</span>
                 </div>
				</a>';
			}
				echo '
				<div class="card-body">';
				
				echo '<a href="index.php?idProp='.$id.'">
				  <h5 class="card-title" style="height: 40px !important;
				  line-height: 24px !important;
				  font-weight: 600;
				  color: #333 !important;
				  font-size: 17px;">'.utf8_encode(ucfirst($row["titulo"])).'</h5>
				  </a>';
				  echo '<div style="margin-top:20px;">';
				  
				  echo '<i class="fas fa-map-marker-alt" style="color:#999999;"></i> ';
				  echo "<span style='font-size:14px; color:#000;'>".$this->devolverRegion($row["idRegion"])."</span>";
				  
				
			   
				
			   echo '</div>';
				  echo '<div style="margin-top:10px;color:#4d4d4d; font-size:18px; font-weight: bold;  text-align: right;">';
				  echo $this->devolverOperacion($row["operacion"])." :&nbsp;";
				  echo "<span style='text-transform: uppercase;'>";
				  if($row["precioUf"]!=1){
					echo "UF ".$this->formatoNumerico($row["precio"]);
				}else{
					echo "$ ".$this->formatoNumerico($row["precio"]);
				}
				echo "</span>";
				 echo '</div>
				 <div style="margin-top:20px;margin-bottom:10px;">
				 	<table width="100%" border=0 style="border-color:white; border-style:none; border-width:0px;">
					 <tr>
					 <td width="20%">
					 <div><i class="fas fa-bed"></i></div>
					 <div><span style="font-size:15px !important; text-transform: uppercase;">'.$row["dormitorios"].'</span> <span style="font-size:12px !important;">DORM.</span></div> 
					 </td>
					 <td width="20%">
					 <div><i class="fas fa-bath"></i> </div>
					 <div><span style="font-size:15px !important; text-transform: uppercase;">'.$row["banos"].'</span> <span style="font-size:12px !important;">BAÑOS</span></div>
					 </td>
					 <td width="20%">
					 <div><img src="mt2.png" style="width:20%;"/> </div>
					 <div><span style="font-size:15px !important; text-transform: uppercase;">'.$this->formatoNumerico($row["m2Construido"]).'</span> <span style="font-size:12px !important;">Mts2</span></div> 
					 </td>
					 <td width="20%">
					 <div align="right">

				  <a href="index.php?idProp='.$id.'" role="button" type="button" class="btn btn-outline-warning btn-mb" style="flex-shrink: 0;
				  display: inline-block;				 
				  padding: 8px 15px;				   
				  font-size: 10px;
				  text-transform: uppercase;
				  border-radius: 40px;
				  transition: 0.3s all ease-out;">Detalles ></a>
				  </div>
					 </td>
					 </tr>
					</table>
				 </div>
				  <div>
				  
				  </div>
			 
				</div>
				 
			  </div>
			
			</div>';

 
	 

		}
		if($total>=6){
			echo "<div align='center'>";      
			$this->paginator->navegacion();
			echo "</div>";
	   }
		  }
		  }
	   }
	public function desplegarProp31($id){
	 
		if(isset($_GET["km"])){
			$id=htmlentities($_GET["km"]);
		   //$this->detalleProp($id);
	  }else{
	   if(isset($_POST["buscador"])){
		   if( $_POST["operacion"]==0 && $_POST["tipo"]==0 && $_POST["ciudad"]==0 && empty($_POST["proyectos"])){
	 
			   $sql="select * from mm_propiedad  where papelera=0";
 
		   }else{
			   
			  $sql="select * from mm_propiedad where ";
			 
			if(isset($_POST["proyectos"])){
				
				$proyectos=htmlentities($_POST["proyectos"]);
				 if(!empty($proyectos)){
						 $sql.=" titulo='".$proyectos."' and ";
				 }
				}
			  if(isset($_POST["operacion"])){
				  $operacion=htmlentities($_POST["operacion"]);
				  if($operacion!=0){
					   $sql.=" operacion='".$operacion."' and ";
				  }
			  }
			  if(isset($_POST["tipo"])){
				  $tipo=htmlentities($_POST["tipo"]);
				  if($tipo!=0){
					$sql.=" tipoProp='".$tipo."' and ";
				  }
			  }
			  if(isset($_POST["ciudad"])){
				  $ciudad=htmlentities($_POST["ciudad"]);
				  if($ciudad!=0){
					$sql.=" ciudad='".$ciudad."' and ";
				  }
			  }
			
			
			 
				  $sql=substr($sql,0,-4);					
			   $sql.=" and papelera=0";
		   }
		  }else{
			  
			  if(isset($_GET["m"])){
				 if($_GET["m"]==1){
					// ventas en verde
					$sql="select * from mm_propiedad where operacion='1' and papelera=0";
				 }else if($_GET["m"]==2) {
					 // arriendo
					 $sql="select * from mm_propiedad where operacion='2' and papelera=0";				
				 }else{
					$sql="select * from mm_propiedad where papelera=0 order by idProp desc";
					
				 }
			  }else{				  
				$sql="select * from mm_propiedad  where papelera=0 order by idProp desc";
			  }
		  }
		 
		 
		  $this->paginator=new paginator(30,30);
		  
		   $this->paginator->agregarConsulta($sql);          
          $this->paginator->estableceIndex("index.php?k=1");
          $total=$this->paginator->obtenerTotalReg();         
          $query=mysqli_query($this->link,$sql);
         $numCol=$num;
	 
 
          $k=0;
		  if($total==0){
				echo "<div style='padding-top:50px;padding-left:20px; font-size:14px; color:gray;' > No se han encontrado resultados</div>";
		  }else{
			  if(!isset($_GET["m"])){
				  
		  if(isset($_POST["buscar"])){
			    echo "<div style='margin-bottom:10px;padding-left:40px;' >Se han encontrado ".$total." coincidencias</div>";
		  }
			  }
		   while($row=$this->paginator->devolverResultados()){
        	$id=$row["idProp"];
			$sql1="select* from mm_cape_fotos  where idProp='".$id."' order by portada desc";
			
			$query1=mysqli_query($this->link,$sql1) or die(mysql_error($this->link));
			$row1=mysqli_fetch_array($query1);
			$rutaFoto=$row1["ruta"];
			if($this->verificaAws($rutaFoto)){
			$imagen = getimagesize($rutaFoto);
			}else{
				$imagen = getimagesize("./upload/".$rutaFoto);
			}
			$ancho = $imagen[0];          
			$alto = $imagen[1];	
			 
			echo '<div class="col-md-4">
			
			<div class="card" style="width:100%;margin-bottom:50px;">';
		 
			
		  
			 
			echo '
			<a href="index.php?idProp='.$id.'">';
	 
			if($alto>700){
			
				if($this->verificaAws($rutaFoto)){
					echo '<div  align="center" style="background-color:#eee;"><img src="'.$rutaFoto.'" class="card-img-top" style="width:60%;height:250px;" alt="'.$row["titulo"].'"></div>';
				}else{
					echo '<div   style="background-color:#eee;" align="center"><img src="./upload/'.$rutaFoto.'" style="width:60%;" class="card-img-top"alt="'.$row["titulo"].'"></div>';
				}
				echo '<div class="box-estado" style="background: #ff6600;">
				<span class="estado">';
				
				if($row["estadoProp"]==1){
				   echo 'Se Vende';
			   }else if($row["estadoProp"]==2){
				   echo 'Se Arrienda';
			   }else if($row["estadoProp"]==3){
				   echo 'Arrendado';
			   }else if($row["estadoProp"]==4){
				   echo 'Vendido';
			   }else{
				   echo 'Reservada';
			   }	
				echo '</span>
				</div>
			   </a>';
			}else{
				
				if($this->verificaAws($rutaFoto)){
					echo '<img src="'.$rutaFoto.'" class="card-img-top"  alt="'.$row["titulo"].'">';
				}else{
					echo '<img src="./upload/'.$rutaFoto.'" class="card-img-top"  alt="'.$row["titulo"].'">';
				}
				
		 		
			echo '<div class="box-estado" style="background: #ff6600;">
                 <span class="estado">';
				 
				 if($row["estadoProp"]==1){
					echo 'Se Vende';
				}else if($row["estadoProp"]==2){
					echo 'Se Arrienda';
				}else if($row["estadoProp"]==3){
					echo 'Arrendado';
				}else if($row["estadoProp"]==4){
					echo 'Vendido';
				}else{
					echo 'Reservada';
				}	
				 echo '</span>
                 </div>
				</a>';
			}
				echo '
				<div class="card-body">';
				
				echo '<a href="index.php?idProp='.$id.'">
				  <h5 class="card-title" style="line-height: 29px !important;font-weight:600;color:#414e62 !important;font-size:19px;">'.ucfirst($row["titulo"]).'</h5>
				  </a>';
				  echo '<div>';
				  
				  echo '<i class="fas fa-map-marker-alt"></i> ';
				  echo "<span style='color:#a31722;font-weight:600;'>".$this->devolverRegion($row["idRegion"])."</span>";
				  echo "&nbsp;-&nbsp;";
				
			   
				echo "<span style='color:#a31722;font-weight:600;'>".$this->devolverOperacion($row["operacion"])."</span>";
			   echo '</div>';
				  echo '<div style="margin-top:10px;"><span style="font-size:20px;"><b>';
				  
				  if($row["precioUf"]!=1){
					echo "U.F ".$this->formatoNumerico($row["precio"]);
				}else{
					echo "$ ".$this->formatoNumerico($row["precio"]);
				}
				 echo ' </b></span></span></div>
				  
				  
				</div>
				<div class="card-footer">
				  <table width="100%">
					<tr>
					  <td width="40%"><img src="mt2.png" style="width:15%;"/> '.$this->formatoNumerico($row["m2Construido"]).' mts2 </td>
					  <td><i class="fas fa-bath"></i> '.$row["banos"].' </td>
					  <td><i class="fas fa-bed"></i> '.$row["dormitorios"].' </td>
					  <td><i class="fas fa-car-side"></i> '.$row["estacionamiento"].' </td>
			
					</tr>
				  </table>
				  
				</div>
			  </div>
			
			</div>';

 
	 

		}
		if($total>=6){
			echo "<div align='center'>";      
			$this->paginator->navegacion();
			echo "</div>";
	   }
		  }
		  }
	   }
	public function setImagen($id){
		$this->link=$this->conectar();
		$sql="select* from mm_coti_contenido where idContenido='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$foto=$r["foto"];
		return($foto);
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
 
if (in_array($mobile_ua,$mobile_agents)) {
    $mobile_browser++;
}
 
if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
    $mobile_browser++;
    //Check for tablets on opera mini alternative headers
    $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
    if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
      $tablet_browser++;
    }
}
if ($tablet_browser > 0) {
	$k="tableta";
	return($k);
}
else if ($mobile_browser > 0) {
 $k="movil";
 return($k); 
 }
else { 
 $k="desktop";
 return($k);
}  
	}
	 
	public function buscador2(){
		$this->link=$this->conectar();
			$sql="select* from mm_ciudad order by ciudad asc";
			$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
			echo '<form name="form1" id="form1" action="index.php?search=1" role="form" method="post">';
			echo "<div class='row'>";
			echo "<div class='col-md-12'>";
	 
			echo ' <input type="hidden" name="action1" id="action1" value="true"/>';
		 
			echo '<div>';
			echo '<select class="form-select form-select-sm" style="margin-top:5px;margin-bottom:5px;" name="operacion" 
			id="operacion" aria-label=".form-select-mb example">
			<option value="0" selected="selected">Operación</option>
			<option value="1">Venta</option>								 
			<option value="1">Arriendo</option>		
			</select>';
			echo "</div>";
		 
			echo '<div>';
			echo '	<select name="tipo"  style="margin-top:5px;margin-bottom:5px;" id="tipo" class="form-select form-select-mb" aria-label=".form-select-mb example">
			<option value="0" selected="selected">Tipo de Inmueble</option>
			<option value="1">Casas</option>
			<option value="2">Departamento</option>
			<option value="3">Parcelas</option><option value="4">Sitios</option>
			<option value="5">Oficina</option>
			<option value="6">Propiedad Industrial</option>
			<option value="7">Terreno</option>
			<option value="8">Local comercial</option>
			<option value="9">Estacionamiento</option>			 
			<option value="10">Bodegas</option>	
			</select>';
			echo '</div>';

			echo '<input type="hidden" name="action" id="action" value="true"/>';
	
			echo '<div>';
		 
			echo '<select class="form-select form-select-mb" name="region" id="region"  style="margin-top:5px;margin-bottom:5px;" aria-label=".form-select-lg example">';
			echo '<option value="0" selected="selected">Región</option>';
			   $sql="select* from mm_region order by idRegion asc";
			   $q=mysqli_query($this->link,$sql);
			   while($r=mysqli_fetch_array($q)){
				   echo '<option value="'.$r["idRegion"].'">'.utf8_encode($r["nombre"]).'</option>';
			   }
			   
			echo '</select>';
			echo '</div>';

		 
			echo '<div>';
			echo '<select name="ciudad" id="ciudad" style="margin:0px !important;" class="form-select form-select-sm sm-3" title="Seleccione el tipo de propiedad">';
			echo "<option value=0 selected='selected'>Comuna</option>";
			 
			echo '</select>';
			echo '</div>';

			

			echo '<div>';
			echo '<button role="button" role="submit" id="buscador" name="buscador" style="width:100%;margin-top:10px;" class="btn btn-primary btn-sm " ><i class="fas fa-search"></i> Buscar</button>';				
			echo '</div>';

			echo "</div>";
			echo "</div>";
			echo "</form>";
	}
	
    public function buscador(){
		$this->link=$this->conectar();
			$sql="select* from mm_ciudad order by ciudad asc";
			$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
			echo '<form name="form1" id="form1" action="index.php?search=1" role="form" method="post">';
			echo "<div class='row' style='padding:20px;'>";
			echo "<div class='col-md-12'>";
			echo "<div style='margin-bottom:10px;'><h5>Buscador</h5></div>";
			echo ' <input type="hidden" name="action1" id="action1" value="true"/>';
			echo '<div>Operación</div>';
			echo '<div>';
			echo '<select class="form-select form-select-sm" style="margin-top:5px;margin-bottom:5px;" name="operacion" 
			id="operacion" aria-label=".form-select-mb example">
			<option value="0" selected="selected">Operación</option>
			<option value="1">Venta</option>								 
			<option value="1">Arriendo</option>		
			</select>';
			echo "</div>";
			echo '<div style="margin-top:0px !important; padding-top:0px !important;">Tipo de inmueble</div>';
			echo '<div>';
			echo '	<select name="tipo"  style="margin-top:5px;margin-bottom:5px;" id="tipo" class="form-select form-select-sm" aria-label=".form-select-lg example">
			<option value="0" selected="selected">Tipo de Inmueble</option>
			<option value="1">Casas</option>
			<option value="2">Departamento</option>
			<option value="3">Parcelas</option><option value="4">Sitios</option>
			<option value="5">Oficina</option>
			<option value="6">Propiedad Industrial</option>
			<option value="7">Terreno</option>
			<option value="8">Local comercial</option>
			<option value="9">Estacionamiento</option>			 
			<option value="10">Bodegas</option>	
			</select>';
			echo '</div>';

			echo '<input type="hidden" name="action" id="action" value="true"/>';
	
			echo '<div>';
			echo '<div style="margin-top:0px !important; padding-top:0px !important;">Tipo de inmueble</div>';
			echo '<select class="form-select" name="ciudad" id="ciudad"  style="margin-top:5px;margin-bottom:5px;" aria-label=".form-select-lg example">';
			echo '<option value="0" selected="selected">Región</option>';
			   $sql="select* from mm_ciudad order by ciudad asc";
			   $q=mysqli_query($this->link,$sql);
			   while($r=mysqli_fetch_array($q)){
				   echo '<option value="'.$r["idCiudad"].'">'.$r["ciudad"].'</option>';
			   }
			   
			echo '</select>';
			echo '</div>';

			echo '<div style="margin-top:0px !important; padding-top:0px !important;">Comuna</div>';
			echo '<div>';
			echo '<select name="ciudad" id="ciudad" style="margin:0px !important;" class="form-select form-select-sm sm-3" title="Seleccione el tipo de propiedad">';
			echo "<option value=0 selected='selected'>Comuna</option>";
			 
			echo '</select>';
			echo '</div>';

			

			echo '<div>';
			echo '<button role="button" role="submit" id="buscador" name="buscador" style="width:100%;margin-top:10px;" class="btn btn-primary btn-sm " ><i class="fas fa-search"></i> Buscar</button>';				
			echo '</div>';

			echo "</div>";
			echo "</div>";
			echo "</form>";
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
	
	 public function  emailContactoProp($tipo,$operacion,$precio,$direccion,$mtsCon,$mtsTerreno,$ano,$detalle,$nombre,$rut,$email,$telefono,$files){
		$this->link=$this->conectar();
			$d=$this->datosPag();			
			$c=$d["correo"];
			$p="oa_*z}1ep^fK";
		$mail = new PHPMailer();
 
		$mail->From = "sistema@erinversionesinmobiliarias.cl";	
		$mail->IsSMTP();
		$mail->CharSet="UTF-8";
		$mail->SMTPSecure = 'ssl';
		$mail->Host = 'mail.erinversionesinmobiliarias.cl';
		$mail->Port = 465;
		$mail->Username ="sistema@erinversionesinmobiliarias.cl";
		$mail->Password = $p;
		$mail->SMTPAuth = true;		
		$mail->FromName =utf8_encode($nombre);
		$mail->SMTPDebug=0;	 
		$mail->IsHTML(true);
		$mail->Subject    =utf8_decode("Formulario Entreguenos su propiedad");
		
		$mail->AltBody    = utf8_decode("Formulario Entréguenos su propiedad");
		$email="luisalbchile22@gmail.com"; 
		
		$mail->AddAddress($email); // send the mail to yourself;
		$mensaje="<div><h3>Formulario Entréguenos su propiedad</h3></div>";
 
		$mensaje.="<div>Nombre: ".$nombre."</div>";
		$mensaje.="<div>Rut: ".$rut."</div>";
		$mensaje.="<div>Email: ".$email."</div>";
		$mensaje.="<div>Telefono: ".$telefono."</div>";
		  
		$mensaje.="<br><br>";
		$mensaje.="<div>Tipo: ".$tipo."</div>";
		$mensaje.="<div>Operación: ".$operacion."</div>";
		$mensaje.="<div>Precio: ".$precio."</div>";
		$mensaje.="<div>Dirección: ".$direccion."</div>";
		$mensaje.="<div>Mts Construido: ".$mtsCon."</div>";
		$mensaje.="<div>Mts Terreno: ".$mtsTerreno."</div>";
 
		$mensaje.="<div>Año: ".$ano."</div>";
		$mensaje.="<div>Detalle: ".$detalle."</div>";
		
		$mensaje.="<br><br>";
		$name = $files['file']['name'];
		$tmp_name = $files['file']['tmp_name'];
		$mail -> AddAttachment ($tmp_name, $name);	
	 
	 
	 
		$mensaje.="<div>Enviado desde el formulario Entréguenos su propiedad de erinversionesinmobiliarias.cl</div>";
		
		$mail->Body    = $mensaje;
		if($mail->send()){
			return(true);
		}else{
			return(false);
		}
		$mail->ClearAllRecipients();
		
	}
	   
	   public function contactoProp(){
	
		if(isset($_POST["action"])){
		 
			$tipo=htmlentities($_POST["tipo"]);
			$operacion=htmlentities($_POST["operacion"]);
			$precio=htmlentities($_POST["precio"]);
			 $direccion=htmlentities($_POST["direccion"]);
			 $mtsCon=htmlentities($_POST["mtsCon"]);
			 $mtsTerreno=htmlentities($_POST["mtsTerreno"]);
			 $ano=htmlentities($_POST["ano"]);
			 $detalle=htmlentities($_POST["detalle"]);
			 $nombre=htmlentities($_POST["nombre"]);
			 $rut=htmlentities($_POST["rut"]);
			 $email=htmlentities($_POST["email"]);
			 $telefono=htmlentities($_POST["telefono"]); 
			 
			 
		if($this->emailContactoProp($tipo,$operacion,$precio,$direccion,$mtsCon,$mtsTerreno,$ano,$detalle,$nombre,$rut,$email,$telefono,$_FILES)){
			 echo '<div class="contenido" style="margin-top:20px;">';
			echo "<div>";
			echo "<span style='font-size:16px; color:gray;'>Gracias por confiar con nosotros, una ejecutivo se comunicara a la brevedad con usted</span>";
			echo "</div>";
			echo "<div align='center' style='margin-top:20px;'>";
			
			echo "<a href='index.php'>Volver al inicio</a>";			
			echo "</div>";
			
			echo "</div>";
		} 
	
		}else{				 
 
		
		 
			echo '<div  style="text-transform: uppercase; font-size:15px!important;font-family: \'Roboto Condensed\', sans-serif; margin-left:0px;margin-top:8px; padding-top:8px;margin-bottom:20px;padding-left:0px; ">';
			echo "<div>";
			echo "<div style='margin-bottom:20px;'><span style='font-size:14px;color:gray;'>";
			echo 'Complete el siguiente formulario para contratar nuestro servicio de corretaje para la venta o arriendo de su propiedad.';
			echo "</span></div>";
			
			echo "<table width='100%' class='table-responsive' border=0 style='border-width:0px; border-style:none; border-color:none;'>";		 
			echo "<tr>";
			echo "<td width='30%' style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>Tipo de Inmueble</span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<select style="magin-top:6px; margin-bottom:6px; width:265px;" class="form-control" input-sm" name="tipo" id="tipo">
			<option value=0>Seleccione</option>
			<option value="casa">Casa</option>
			<option value="departamento">Departamento</option>
			<option value="oficina">Oficina</option>
			<option value="terreno">Terreno</option>
			<option value="galpon">Galpon</option>
			<option value="parcela">Parcela</option>
			<option value="sitio"  selected="selected">Sitio</option>
			<option value="local">Local</option>
			<option value="otro">Otro</option>
			
		</select>';
			echo "</td>";
			echo "</tr>";
			
			
					echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>Tipo de Operación</span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<select  style="width:265px;magin-top:6px; margin-bottom:6px;" class="form-control input-sm" name="operacion" id="operacion" >
			<option value=0 >Seleccione</option>
			<option value="venta" selected="selected">Venta</option>
			<option value="arriendo">Arriendo</option>
		</select>';
			echo "</td>";
			echo "</tr>";
			
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>Precio : $</span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<input  value=""   style="width:250px;magin-top:6px; margin-bottom:6px;" type="text" class="form-control input-sm" id="precio" name="precio">';
			echo "</td>";
			echo "</tr>";
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>Dirección : </span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<input  value=""  style="width:250px;magin-top:6px; margin-bottom:6px;" type="text" class="form-control input-sm" id="direccion" name="direccion">';
			echo "</td>";
			echo "</tr>";
			
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>M2 Construidos :</span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<input  value=""   style="width:250px;magin-top:6px; margin-bottom:6px;" type="text" class="form-control input-sm" id="mtsCon" name="mtsCon">';
			echo "</td>";
			echo "</tr>";
			
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>M2 Terreno :</span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<input  value="" style="magin-top:6px; margin-bottom:6px;"  type="text" class="form-control input-sm" id="mtsTerreno" name="mtsTerreno">';
			echo "</td>";
			echo "</tr>";
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>Año de Contrucción :</span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<input  value=""  style="magin-top:6px; margin-bottom:6px;" type="text" class="form-control input-sm" id="ano" name="ano">';
			echo "</td>";
			echo "</tr>";
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>Detalle de la propiedad :</span></td>";
			
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<textarea rows="6" cols="4000" class="form-control input-sm" style="width:250px;magin-top:6px; margin-bottom:6px;" name="detalle" id="detalle" ></textarea>';
			echo "</td>";
			echo "</tr>";
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>Subir Propiedad :</span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '	<input class="jffile" name="file" id="file" style="width:250px;" type="file">
			<input type="hidden" name="action" id="action" value="true">
			';
			echo "</td>";
			echo "</tr>";
			
			echo "</table>";
			
			echo "</div>";
			
			
			
			echo "<div style='margin-top:20px; margin-bottom:20px;'>";
			echo "<h2>";
			echo '<p><strong>';
			echo "<span style='color:gray; font-size:16px;'>";
			echo 'Datos del Propietario';
			echo "</span>";
			echo '</strong></p>';
			echo "</h2>";
			echo "<table width='100%' border=0 style='border-width:0px; border-style:none; border-color:none;'>";
			
			
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;' width='30%'><span style='color:gray;'>Nombre :</span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<input  name="nombre" id="nombre" style="magin-top:6px; margin-bottom:6px;width:250px;" type="text" class="form-control input-sm"  >';
			echo "</td>";
			echo "</tr>";
			
			
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>R.U.T. :</span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<input  name="rut" id="rut" style="magin-top:6px; margin-bottom:6px;width:250px;" type="text" class="form-control input-sm"  >';
			echo "</td>";
			echo "</tr>";
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>Email :</span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<input id="email" name="email" style="magin-top:6px; margin-bottom:6px;width:250px;" type="text" class="form-control input-sm"  >';
			echo "</td>";
			echo "</tr>";
			
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'><span style='color:gray;'>Telefono :</span></td>";
			echo "<td style='border-width:0px; border-style:none; border-color:none;'>";
			echo '<input name="telefono" id="telefono" style="magin-top:6px; width:250px;margin-bottom:6px;" type="text" class="form-control input-sm"  >';
			echo "</td>";
			echo "</tr>";
			
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'><td style='border-width:0px; border-style:none; border-color:none;'>&nbsp;</td></tr>";
			echo "<tr style='border-width:0px; border-style:none; border-color:none;'><td style='border-width:0px; border-style:none; border-color:none;' colspan=2>";
			echo '<input name="submit2" id="submit2" style="magin-top:6px;width:100px; padding-left:10px; padding-right:10px; margin-bottom:6px;" value="Enviar" type="button">';
			
			echo "</td></tr>";
			
			echo "</table>";
		
			echo "</div>";
			 
			echo "</div>";
			 
			 
		}
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
	public function mensaje2(){
		$msgBox=new msgBox(1,"Su mensaje se ha enviado con exito!!!");
	}
	public function contacto2($id=false){
		$d=$this->datosPag();
	
	 if(isset($_POST["action2"])){
	
		 $nombre=htmlentities($_POST["nombre"]);
		 $email=htmlentities($_POST["email"]);
		 $tel=htmlentities($_POST["telefono"]);
		 $msg=htmlentities($_POST["msg"]);
		 
		 if($this->emailContacto($nombre,$email,$tel,$msg)){
			 if($_GET["mod"]=="contacto"){
				 echo '<script>';
				 echo 'document.location="index.php?mod=contacto&msg=1";';
				 echo '</script>';
			 				 
			 }else{
				echo '<script>';
				echo 'document.location="https://www.eihenriquez.cl/index.php?idProp='.$id.'&msg=1";';
				echo '</script>';
				
			 }
			 
			 
		 }
		 
	 }else{
 
			 			if(isset($_GET["mod"])){
							echo '<section id="contact-form" style="margin-top:0px;padding:15px;">';
						 }else{
							echo '<section id="contact-form" style="margin-top:120px;padding:15px;">';
						 }
						
						
					 
						echo '<div class="card" style=" padding:10px;">
						<form method="post" name="form12" id="form12" action="">

							  <h4 style="margin-bottom:15px;color:gray;">Contactenos</h4>
							  
							  <div style="margin-bottom:30px;">Complete sus datos y lo contactamos</div>';
							if(isset($_GET["msg"])){
								echo '<div class="alert alert-primary" role="alert">
								Su mensaje se ha enviado con exito !!
							  </div>';
							}
									echo '<div>
										<label for="name">Nombre</label>
										<input type="text" name="nombre" id="nombre" style="width:100% !important;" placeholder="Ingrese su nombre" class="form-control form-control-sm" title="* Ingrese su nombre" >
										<input type="hidden" name="action2" id="action2" value="true"/>
									</div>
									<div>
										<label for="email">Email</label>
										<input type="text" name="email" id="email" style="width:100% !important;" placeholder="Ingrese su email" class="form-control form-control-sm"  title="* Ingrese su dirección de Email">
									</div>
									<div>
										<label for="number">Telefono</label>
										<input type="text" name="telefono" id="telefono" style="width:100% !important;" placeholder="Ingrese su telefono" class="form-control form-control-sm">
									</div>
									<div>
										<label for="comment">Mensaje</label>
										<textarea  name="msg" id="msg" rows="5" cols="30" style="font-size:14px; width:100% !important;" class="form-control form-control-sm" title="* Please provide your message"></textarea>
									</div>
									<div>
										<button role="button" style="margin-top:30px;width:99% !important;padding-top:10px; padding-bottom:10px;" class="btn btn-primary  btn-sm"  id="boton13"  name="boton13"><i class="far fa-hand-point-up"></i>  Enviar Mensaje</button>
									 </div>
 

							 </form>
								 </div>
							</section>';
	 }
	}
	
	public function contacto2a(){
		
		$d=$this->datosPag();
		 
	 if(isset($_POST["action"])){
		 $nombre=htmlentities($_POST["nombre"]);
		 $email=htmlentities($_POST["email"]);
		 $tel=htmlentities($_POST["telefono"]);
		 $msg=htmlentities($_POST["msg"]);
		 if(isset($_GET["idProp"])){
			 $id=htmlentities($_GET["idProp"]);
			 
		 }
		 if($this->emailContacto($nombre,$email,$tel,$msg)){
			 header("location:detallePropiedad.php?idProp='.$id.'&msg=1");
			 exit;
		 }
		 
	 }else{
 
			 
						
						echo '<section id="contact-form" style="padding:15px;">
						<form method="post" name="form12" id="form12" action="">
							  <h4 style="color:gray;">Envienos un mensajesss</h4>
							
									<div>
										<label for="name">Nombre</label>
										<input type="text" name="nombre" id="nombre" style="width:100% !important;" placeholder="Ingrese su nombre" class="form-control form-control-lg" title="* Ingrese su nombre" >
										<input type="hidden" name="action" id="action" value="true"/>
									</div>
									<div>
										<label for="email">Email</label>
										<input type="text" name="email" id="email" style="width:100% !important;" placeholder="Ingrese su email" class="form-control form-control-lg"  title="* Ingrese su dirección de Email">
									</div>
									<div>
										<label for="number">Telefono</label>
										<input type="text" name="telefono" id="telefono" style="width:100% !important;" placeholder="Ingrese su telefono" class="form-control form-control-lg">
									</div>
									<div>
										<label for="comment">Mensaje</label>
										<textarea  name="msg" id="msg" rows="5" cols="30" style="font-size:14px; width:100% !important;" class="form-control form-control-lg" title="* Please provide your message"></textarea>
									</div>
									<div>
										<input type="submit" style="width:99% !important;" class="btn btn-warning btn-lg"  id="boton13" value="Enviar Mensaje"   name="boton13">
									 </div>
 

							 </form>
								 
							</section>';
	 }
	}
	
	public function contacto3(){
		$d=$this->datosPag();
		
 
 
			 
						
						echo '<section id="contact-form" style="padding-left:20px;padding-right:20px; padding-top:10px;">
																 
									
								<form name="form1" class="contact-form" method="post" action="">
									<p>
										<label for="name">Nombre</label>
										<input type="text" name="nombre" id="nombre" class="required" title="* Ingrese su nombre" >
									</p>

									<p>
										<label for="email">Email</label>
										<input type="text" name="email" id="email" class="email required" title="* Ingrese su dirección de Email">
									</p>

									<p>
										<label for="number">Telefono</label>
										<input type="text" name="telefono" id="telefono">
									</p>

									<p>
										<label for="comment">Message</label>
										<textarea name="message" id="comment" class="required" title="* Please provide your message"></textarea>
									</p>

								 

									<p>
										<input type="submit" id="submit-button" value="Enviar Mensaje" class="real-btn" name="submit">
									 
									</p>

								 
								</form>
							</section>';
	}
	public function leerTitulo($id){
		$this->link=$this->conectar();
		$sql="select* from mm_coti_contenido where idContenido='".$id."'";
	 
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
		$row=mysqli_fetch_array($query);
		echo $row["titulo"];
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
			echo "<div style='margin-top:5px;margin-bottom:40px;'>";					
			echo '<img src="./upload/'.$foto.'" class="img-fluid rounded float-start" style="max-width:100%; width:100%;"/>';
			echo "</div>";
		}else{
			echo "<div style='margin-top:20px;margin-bottom:40px;'>";		
			echo '<img src="./upload/'.$foto.'" class="img-fluid rounded float-start" style="max-width:100%; width:100%;"/>';
			echo "</div>";
		}
	}
	}
	public function devolverProyectos($id){
		$this->link=$this->conectar();
		$sql="select* from mm_propiedad where titulo='".$id."'";
	 
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		return($r["titulo"]);
	}
	public function mapa($id){
		$this->link=$this->conectar();
		if(isset($_POST["action"]) || isset($_POST["action1"])){
			
			echo "<div style='margin-top:10px;'><i class='fas fa-home'></i><span style='font-size:15px !important;'><a href='index.php' style='font-size:15px !important;color:gray !important;'> Inicio </a> / Resultado de la busqueda </a> </span>";
			if(!empty($_POST["proyectos"])){
				echo "/ <span style='font-size:15px;'>".$this->devolverProyectos($_POST["proyectos"])."</span>";
			}
			if($_POST["operacion"]!=0){
				echo "/ <span style='font-size:15px;'>". $this->devolverOperacion($_POST["operacion"])."</span>";;
			}
			if($_POST["tipo"]!=0){
				echo "/ <span style='font-size:15px;'>".$this->devolverTipoProp($_POST["tipo"])."</span>";;
			}
			if($_POST["ciudad"]!=0){
				echo "/ <span style='font-size:15px;'>".$this->devolverCiudad($_POST["ciudad"])."</span>";;
			}
			
			echo "</div>"; 
		}else{

		$sql="select* from mm_coti_contenido where idContenido='".$id."'";	 
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
		$row=mysqli_fetch_array($query);		
		echo "<div style='margin-top:10px;'><i class='fas fa-home'></i> <a href='index.php' style='color:gray !important;'> Inicio </a> / ".$row["titulo"]."</a></div>"; 
		}
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
	public function registrarVisita(){
		$this->monitor=new monitor();
		$this->monitor->registrarVisita();
	}
	public function recuperarContra(){
		$this->login=new superLogin();
		$this->login->recuperarPass();
	}
	public function login(){

		// llama supermasterlogin.php
		$this->login=new superLogin();
		$this->login->loginBoot();
	}
 
 public function otras(){
	$this->link=$this->conectar();
		 $sql="select * from mm_propiedad  order by idProp desc  limit 1,3"; 
		 $q=mysqli_query($this->link,$sql);   
         $numCol=$num;
		 $total=mysqli_num_rows($q); 
          $k=0;
		  if($total==0){
				echo "<div style='padding-top:50px;padding-left:20px; font-size:14px; color:gray;' > No se han encontrado resultados</div>";
		  }else{
			  
		   while($row=mysqli_fetch_array($q)){
        	$id=$row["idProp"];
			$sql1="select* from mm_cape_fotos  where idProp='".$id."' order by idFoto asc";
			$query1=mysqli_query($this->link,$sql1) or die(mysql_error($this->link));
			$row1=mysqli_fetch_array($query1);
			$rutaFoto=$row1["ruta"];
		
		echo '<article  style="margin-bottom:35px;" class="property-item clearfix post-8561 property type-property status-publish has-post-thumbnail hentry property-feature-2-stories property-feature-central-heating property-feature-dual-sinks property-feature-electric-range property-feature-fire-alarm property-feature-fire-place property-feature-home-theater property-feature-laundry-room property-feature-lawn property-feature-marble-floors property-feature-swimming-pool property-type-single-family-home property-city-pinecrest property-status-for-sale">
<div STYLE="border-color:#f0f0ef; border-width:1px; border-style:solid">
	<figure>
		<a href="detallePropiedad.php?idProp='.$row["idProp"].'">
		<img width="246" height="200" style="height:175px;" src="./upload/'.$rutaFoto.'" class="attachment-grid-view-image size-grid-view-image wp-post-image" alt="" /></a>
		<figcaption class="for-sale">';
		if($row["operacion"]==2){
			echo "ARRIENDO";
		}else{
			echo "VENTA";
		};		
		echo '</figcaption>
		</figure>
	<h4 style="padding-left:10px;height:45px;">
	<a href="detallePropiedad.php?idProp='.$row["idProp"].'">'.ucfirst(strtolower($row["titulo"])).'</a></h4>
	<p style="height:50px;padding:10px;">';
	
	echo substr(ucfirst(strtolower($row["descripcion"])),0,60)."...";
	echo '<a class="more-details" href="detallePropiedad.php?idProp='.$row["idProp"].'">
	<i class="fa fa-caret-right"></i></a></p>
	 <table width="100%" style="border-color:#17375e; border-width:0px; border-style:none;">
												 <tr>
												 <td style="border-color:#17375e; border-width:0px; border-style:none;"><span style="font-size:15px;padding-left:7px;color:gray;font-weight:bold;">
												';
												if($row["precioUf"]==2){
														echo "U.F ".$this->formatoNumerico($row["precio"]);
													}else{
														echo "$ ".$this->formatoNumerico($row["precio"]);
													}
												
												echo '
												  </span> </td>
												 <td style="border-color:#17375e; border-width:0px; border-style:none;" align="right"><a href="detallePropiedad.php?idProp='.$row["idProp"].'" class="btn  btn-primary btn-sm" style="padding-top:2px; padding-bottom:2px; padding-right:10px; padding-left:10px;background-color:#5cbc74; font-size:12px;" role="button">Ver Propiedad</a></td>
												 </tr>
												 </table>  </div>
</article>';
		}
		   
		  }
	   }
	public function desplegarProp(){
		$this->link=$this->conectar();
		 if(isset($_GET["km"])){
             $id=htmlentities($_GET["km"]);
            $this->detallePropiedad($id);
       }else{
		   if(isset($_POST["action"])){
			   $sql="select * from mm_propiedad where ";
			   if(isset($_POST["operacion"])){
				   $operacion=htmlentities($_POST["operacion"]);
				   if($operacion!=0){
						$sql.=" operacion='".$operacion."' or ";
				   }
			   }
			   if(isset($_POST["tipo"])){
				   $tipo=htmlentities($_POST["tipo"]);
				   if($tipo!=0){
				     $sql.=" tipoProp='".$tipo."' or ";
				   }
			   }
			   if(isset($_POST["ciudad"])){
				   $ciudad=htmlentities($_POST["ciudad"]);
				   if($ciudad!=0){
				     $sql.=" ciudad='".$ciudad."' or ";
				   }
			   }
			 
			   if(isset($_POST["codigo"])){ 
					$codigo=htmlentities($_POST["codigo"]);
					if($codigo!=0){
					  $sql.=" codigo='".$codigo."' or ";
					}
			   }
			   if(isset($_POST["orden"]) && $_POST["orden"]!=0){
				  $sql=substr($sql,0,-6);
				  if($_POST["orden"]==1){
						$sql.=" order by precio asc";
					}else{
						$sql.=" order by precio desc";
					}
			   }else{
				    $sql=substr($sql,0,-3);					
			   } 
		 
		   }else{
			   if(isset($_GET["mod"]) && $_GET["mod"]=="arriendo" || $_GET["mod"]=="ventas"){
				  if($_GET["mod"]=="arriendo"){
					 $sql="select * from mm_propiedad where operacion='2'"; 
				  }else{
					  $sql="select * from mm_propiedad where operacion='1'";
				  }
			   }else{
					$sql=$this->sql->sqlConsultarViviendaDestacadas();
			   }
		   }
		  
          $this->paginator->agregarConsulta($sql);          
          $this->paginator->estableceIndex("index.php?k=1");
          $total=$this->paginator->obtenerTotalReg();         
          $query=mysqli_query($this->link,$sql);
         $numCol=$num;
          $k=0;
		  if($total==0){
				echo "<div style='padding-top:50px;padding-left:20px; font-size:14px; color:gray;' > No se han encontrado resultados</div>";
		  }else{
		   while($row=$this->paginator->devolverResultados()){
        	$id=$row["idProp"];
        	$sql1=$this->sql->sqlConsultarIdFotos1($id);
        	$query1=mysqli_query($this->link,$sql1) or die(mysql_error($this->link));
			$row1=mysqli_fetch_array($query1);
			$rutaFoto=$row1["ruta"];
			
		echo '<div class="box-propiedad">
				      				<a title="'.$row["titulo"].'" href="index.php?mod=det&idProp='.$row["idProp"].'">
				      				<img class="imagen" src="./upload/'.$rutaFoto.'" alt="'.$row["titulo"].'" width="235" height="177" />';
										echo '<div class="box-estado"  style="z-index:0; position:relative; background:url(http://www.proventarpropiedades.cl/imagen/sevende.png) no-repeat;">&nbsp;</div>'; 					
									 echo '</a>
		        					
		        					<div class="datos">
		        						<div class="datos-texto">'.$this->devolverOperacion($row["operacion"]).'<span> | </span>
										<a href="propiedades/concepcion/40/1.html">';
										
										echo $this->devolverCiudad($row["ciudad"]);
										echo '</a>
		        						</div>
		        					</div>
									<div class="titulo"><h3><a title="'.$row["titulo"].'" href="index.php?mod=det&idProp='.$row["idProp"].'">'.$row["titulo"].'</a></h3></div>
		   <span class="precio">$';
		   echo $this->formatoNumerico($row["precioUf"]);
		   echo '</span></div>';
		   }
		   
		  }
	   }
	   if($total>=6){
			echo "<div align='center'>";      
			$this->paginator->navegacion();
			echo "</div>";
	   }
	}	 
	public function leerDatos(){
		$sql="select* from coti_datos";
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
		$row=mysqli_fetch_array($query);
		return($row);
	}
	 public function devolverTelefono(){
	 	$sql="select* from datoscontacto order by idContacto desc";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$telefono=$r["telefono"];
		 
	return($telefono);
		
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
	public function add_dashes( $data ) {
    $data = trim(strtolower( $data ));
    $data = explode(' ', $data);
    $data = implode('-', $data );
    return $data;
    }
   	public function formatoNumerico($num){
		$n=number_format($num, 0,",",".");
		return($n);
	}
	public function accesos(){
		$this->link=$this->conectar();
		$sql="select* from coti_categoria order by idCategoria asc";
		$q=mysqli_query($this->link,$sql);
		while($r=mysqli_fetch_array($q)){
			$idCat=$r["idCategoria"];
			$sql2="select* from subMenu where idCategoria='".$idCat."'";
			$q2=mysqli_query($this->link,$sql2);
			$total=mysqli_num_rows($q2);
			if($total==0){
		 
			if(empty($r["url"])){  
				$idPag=$this->devolverPagina($r["idCategoria"]);									
				echo '<li><a class="active" href="index.php?mod=pag&rd='.$idPag.'&m=1">'.ucfirst(strtolower($r["nombre"])).'</a></li>';
			}else{
				echo '<li><a class="active" ';
								
			if(!empty($r["nombre"])){
				echo 'href="'.$r["url"].'"';
			}else{									 
				echo 'href="index14.php?mod='.$r["nombre"].'"';
			}									
				echo '>'.ucfirst(strtolower($r["nombre"])).'</a></li>';
			}							
			}
		}
	 
	}
	public function serviciosBloque(){
		$this->link=$this->conectar();
		echo ' <div class="categorias servicios">
						<div class="titulo s-titulo">
							<h4>Servicios</h4>
						</div>
	        			<ul>';
						$sql="select* from coti_categoria order by idCategoria asc";
	 
							$q=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
						 
							while($r=mysqli_fetch_array($q)){
								$idCat=$r["idCategoria"];
								$sql2="select* from subMenu where idCategoria='".$idCat."'";
								$q2=mysqli_query($this->link,$sql2);
								if($r["nombre"]=="SERVICIOS"){
									$idCat=$r["idCategoria"];
									$sql2="select* from subMenu where idCategoria='".$idCat."'";
									$q2=mysqli_query($this->link,$sql2);
									while($k=mysqli_fetch_array($q2)){
										$idPag=$k["idSub"];
										if(empty($k["url"])){
											echo '<li><a href="index.php?mod=pag&rd='.$idPag.'">'.ucfirst(strtolower($k["nombre"])).'</a></li>';
										}else{
											echo '<li><a href="'.$k["url"].'">'.utf8_encode(ucfirst(strtolower($k["nombre"]))).'</a></li>';
									
										}
									}
								}
							  
							}							
						  					
						
	        			echo '</ul></div>	';
	}
	public function servicios(){
		$this->link=$this->conectar();
		$sql="select* from coti_categoria order by idCategoria asc";
	 
							$q=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
						 
							while($r=mysqli_fetch_array($q)){
								$idCat=$r["idCategoria"];
								$sql2="select* from subMenu where idCategoria='".$idCat."'";
								$q2=mysqli_query($this->link,$sql2);
								if($r["nombre"]=="SERVICIOS"){
									$idCat=$r["idCategoria"];
									$sql2="select* from subMenu where idCategoria='".$idCat."'";
									$q2=mysqli_query($this->link,$sql2);
									while($k=mysqli_fetch_array($q2)){
										$idPag=$k["idSub"];
										if(empty($k["url"])){
											echo '<li><a href="index.php?mod=pag&rd='.$idPag.'">'.ucfirst(strtolower($k["nombre"])).'</a></li>';
										}else{
											echo '<li><a href="'.$k["url"].'">'.utf8_encode(ucfirst(strtolower($k["nombre"]))).'</a></li>';
									
										}
									}
								}
							  
							}							
						 	
 
	}
	public function menu(){
		$this->link=$this->conectar();
		$sql="select* from coti_categoria order by idCategoria asc";
							$q=mysqli_query($this->link,$sql);
							echo "<ul>";
							while($r=mysqli_fetch_array($q)){
								$idCat=$r["idCategoria"];
								$sql2="select* from subMenu where idCategoria='".$idCat."'";
								$q2=mysqli_query($this->link,$sql2);
							 
								echo "<li>";
								echo '<li class="inicio">'; 
								$total=mysqli_num_rows($q2);
								 if($total!=0){
									echo '	<a id="propiedades"  href="javascript:void(0);">'.$r["nombre"].'</a>';
									echo '<ul class="sub">';
									while($k=mysqli_fetch_array($q2)){
										$idPag=$k["idSub"];
										if(empty($k["url"])){
											echo '<li><a href="index.php?mod=pag&rd='.$idPag.'">'.$k["nombre"].'</a></li>';
										}else{
											echo '<li><a href="'.$k["url"].'">'.$k["nombre"].'</a></li>';
									
										}
									}
									echo '</ul>';
								}else{
									if(empty($r["url"])){  
										$idPag=$this->devolverPagina($r["idCategoria"]);									
										echo '<a class="active" href="index.php?mod=pag&rd='.$idPag.'&m=1">'.$r["nombre"].'</a>';
									}else{
										echo '<a class="active" ';
								
									if(!empty($r["nombre"])){
										echo 'href="'.$r["url"].'"';
									}else{									 
										echo 'href="index14.php?mod='.$r["nombre"].'"';
									}									
									echo '>'.$r["nombre"].'</a>';
									}									 
								}
								echo '</li>';
								echo "</li>";
							}							
							echo "</ul>";	
							
	}
	public function devolverPagina($id){
		$this->link=$this->conectar();
		$sql="select* from mm_coti_contenido where idCate='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$id=$r["idContenido"];
	 
		return($id);
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
	public function  emailContacto($nombre=false,$email2=false,$tel=false,$des=false){
		$this->link=$this->conectar();
		$d=$this->datosPag();			
			$c=$d["correo"];			
			$p="oa_*z}1ep^fK";
			$mail = new PHPMailer();
 
		 
		
		

		$mail->From = "sistema@erinversionesinmobiliarias.cl";	
		$mail->IsSMTP();
		$mail->CharSet="UTF-8";
		$mail->SMTPSecure = 'ssl';
		$mail->Host = 'mail.erinversionesinmobiliarias.cl';
		$mail->Port = 465;
		$mail->Username ="sistema@erinversionesinmobiliarias.cl";
		$mail->Password = $p;
		$mail->SMTPAuth = true;		
		$mail->FromName =utf8_encode($nombre);
		$mail->SMTPDebug=0;	 
		$mail->IsHTML(true);
		$mail->Subject    ="Formulario de contacto";
		$mail->AltBody    = "Formulario de contacto";
	 
	 	$email="luisalbchile22@gmail.com"; 
		
		$mail->AddAddress($email); // send the mail to yourself;
		$mensaje="<div><h3>Formulario de Contacto</h3></div>";

		$mensaje.="<div>Nombre:<b>".$nombre."</b></div>";
		$mensaje.="<div>Telefono:<b>".$tel."</b></div>";
		$mensaje.="<div>Email:<b>".$email2."</b></div>"; 
		$mensaje.="<div>Descripción:<b><br><br>".$des."</b></div>";
		$mensaje.="<br><br>";
		
	 
		$mensaje.="<div>Enviado desde el formulario de contacto de erinversionesinmobiliarias.cl</div>";

		
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
		echo "<div style='margin-top:120px;'><i class='fas fa-home'></i> <a href='index.php' style='color:gray !important;'>Inicio</a> / ".$row["titulo"]."</div>";
		echo "<div><h3>".$row["titulo"]."</h3></div>";
		echo "<div style='margin-bottom:30px;'><i class='fas fa-map-marker-alt'></i> Región :".$this->devolverRegion($row["idRegion"])." | Ciudad: ".$this->devolverCiudad($row["idCiudad"])." | Comuna: ".$this->devolverComuna($row["idComuna"])."</div>";
		echo "<div>";
		$this->sliderYapo($arch);
		echo "</div>";
 
		echo "<div style='margin-top:20px;margin-bottom:30px;'>";
		echo "<div class='row'>";
		echo "<div class='col-md-7'>";
		echo '<div><h3>';
		if($row["precioUf"]==2){
			echo "UF ".$this->formatoNumerico($row["precio"]);
		}else{
			echo "$ ".$this->formatoNumerico($row["precio"]);
		}
		echo '</h3></div>';
		
		echo '<div>'.$this->devolverTipoProp($row["tipoProp"]).'</div>';
		echo "</div>";
		echo "<div class='col-md-5'>";
		echo '<table width="100%" border="0">';
		echo "<tr>";

		echo "<td width='40%'><div style='font-size:16px;'><img src='mt2.png' style='width:15%;'/> ".$row["mt2Totales"]." mt2</div></td>";
		echo "<td><div style='font-size:16px;'><i class='fas fa-bath' aria-hidden='true'></i> ".$row["banos"]." </div></td>";
		echo "<td><div style='font-size:16px;'><i class='fas fa-bed' aria-hidden='true'></i> ".$row["dormitorios"]."</div></td>";
		echo "<td><div style='font-size:16px;'><i class='fas fa-car-side' aria-hidden='true'></i> ".$row["estacionamiento"]."</div></td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";

		echo "</div>";
		
		echo "</div>";
		echo '<div><hr/></div>';
		echo "<div style='margin-top:20px;'><h5>Descripción</h5></div>";
		echo "<div>";
		echo nl2br($row["descripcion"]);
		echo "</div>";
		echo "<div><hr/></div>";
		echo "<div style='margin-top:10px;margin-bottom:20px;'>";
		echo "<h5>Caracteristicas</h5>";
		echo "</div>";

		echo "<div class='row'>";

		echo "<div class='col-md-6'>
		<div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Precio :</b> ";
			  if($row["precioUf"]==2){
				echo "UF ".$this->formatoNumerico($row["precio"]);
			}else{
				echo "$ ".$this->formatoNumerico($row["precio"]);
			}
			  echo "</div>
			  <div><i class='fas fa-check' style='font-size:12px;'></i> <b>Operación :</b> ".$this->devolverOperacion($row["operacion"])."</div>
			  
			  <div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Tipo de Propiedad :</b> ".$this->devolverTipoProp($row["tipoProp"])."</div>
			  <div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Dormitorios :</b> ";
			  if($row["dormitorios"]==1){
				  echo $row["dormitorios"]." dormitorio";
			  }else{
				  echo $row["dormitorios"]." dormitorios";
			  }			  
			  echo " </div>
			  <div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Baños :</b> ";
			  if($row["banos"]==1){
				echo $row["banos"]." baño ";
			  }else{
				echo $row["banos"]." baños ";
			  }
			  echo "</div>
			  <div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Tipo de Cocina :</b> ".$this->devolverTipoCocina($row["tipoCocina"])."</div>
			  <div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Logia :</b> ".$row["logia"]."</div>
			  <div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Estacionamiento :</b> ".$this->devolverEstacionamientos($row["estacionamiento"])."</div>
			

		</div>";
			  echo "<div class='col-md-6'>";
					echo "<div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Bodega :</b>  ".$row["bodega"]."</div>";
					if($row["tipoProp"]==1){	
							echo "<div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Mt2 Totales :</b> ".$row["mt2Totales"]." m²</div>";
					}else{
  							echo "<div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Mt2 útiles :</b> ".$row["mt2Totales"]." m² </div>";
					}
					echo "<div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Mt2 construidos :</b> ".$row["m2Construido"]." m²</div>
					<div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Conserjería :</b> ".$row["conser"]."</div>
					<div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Quincho :</b> ".$row["quincho"]."</div>
					<div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Áreas comunes :</b> ".$row["areasComunes"]."</div>
					<div style='margin-top:12px; margin-bottom:12px;'><i class='fas fa-check' style='font-size:12px;'></i> <b>Piscina :</b> ".$row["piscina"]."</div>	";
			  		echo "</div>";
	 echo "</div>";
		echo "<div>";
		echo "<hr/>";
		echo "</div>";
		 
		echo '<div id="detalle-mapa" style="margin-left:0px; margin-right:0px;height:400px;margin-bottom:20px;"></div>';
	    echo "<div style='margin-bottom:60px;'>&nbsp;</div>";
		

	}
	public function devolverTipoCocina($id){
		$arrSel122=array(1=>"Cerrada",2=>"Americana",3=>"Integrada");
		return($arrSel122[$id]);
	}
	public function buscadorHorizontal(){
		
		$this->link=$this->conectar();
		echo '<div><form method="post" name="form1" id="form1" action="">
		<div class="container">
			<div class="row">
			<div class="col-md-2">
			<select class="form-select" style="margin-top:5px;margin-bottom:5px;" name="operacion" id="operacion" aria-label=".form-select-lg example">
			<option value="0" selected="selected">Operación</option>
			<option value="1">Venta</option>								 
			<option value="2">Arriendo</option>		
			</select>
		</div>
 
		<div class="col-md-2">
				<select name="tipo"  style="margin-top:5px;margin-bottom:5px;" id="tipo" class="form-select" aria-label=".form-select-lg example">
				<option value="0" selected="selected">Tipo de Inmueble</option>
				<option value="1">Casas</option>
				<option value="2">Departamento</option>
				<option value="3">Oficina</option>
				<option value="4">Agrícola</option>
				<option value="5">Bodega</option>
				<option value="6">Comercial</option>
				<option value="7">Estacionamiento</option>
				<option value="8">Galpón</option>
				<option value="9">Industrial</option>			 
				<option value="10">Terreno</option>	
				<option value="11">Turistico</option>	
				</select>
			</div>
			<div class="col-md-2">
				<select class="form-select" name="region" id="region"  style="margin-top:5px;margin-bottom:5px;" aria-label=".form-select-lg example">';
				echo '<option value="0" selected="selected">Región</option>';
				   $sql="select distinct idRegion from mm_propiedad where papelera=0 order by idRegion asc";
				   $q=mysqli_query($this->link,$sql);
				   while($r=mysqli_fetch_array($q)){
					   echo '<option value="'.$r["idRegion"].'">'.$this->devolverRegion($r["idRegion"]).'</option>';
				   }
				   
				echo '</select>
			</div>
			<div class="col-md-2">
				<select name="ciudad"  style="margin-top:5px;margin-bottom:5px;" id="ciudad"
				 class="form-select" aria-label=".form-select-lg example">
				<option value="0" selected="selected">Ciudad</option>
				 
				</select>
			</div>
			<div class="col-md-2">
				<select   style="margin-top:5px;margin-bottom:5px;" name="comuna" id="comuna"
				 class="form-select" aria-label=".form-select-lg example">
				<option value="0" selected="selected">Comuna</option>
				 
				</select>
			</div>
			 <input type="hidden" name="action1" id="action1" value="true"/>
			<div class="col-md-2">
				<button class="btn btn-info btn-mb"  style="width:100%;margin-top:5px;margin-bottom:5px;" role="submit" type="submit" name="buscador" id="buscador" style="width:100%;">
				<i class="fas fa-search"></i> Buscar</button>
			</div>
		</div>
		</div>
	</form></div>';
	}
	public function buscador44(){
		$this->link=$this->conectar();
		echo '<section id="advance_search_widget-2" class="widget advance-search clearfix Advance_Search_Widget"><h4 class="title search-heading">Buscar Propiedad<i class="fa fa-search"></i></h4>	<div class="as-form-wrap">
		<form class="advance-search-form clearfix" method="post" action="propiedades.php">
		<div class="option-bar large">
                <label for="location">Ciudad</label>
                <span class="selectwrap">
                    
				   <input type="hidden" name="action" id="action" value="true"/>
				   <select name="ciudad" id="ciudad" class="search-select" style="display: none;">
				   <option value="any" selected="selected">Todos</option>';
				  
				   $sql="select* from mm_ciudad ";
				   $q=mysqli_query($this->link,$sql);
				   while($r=mysqli_fetch_array($q)){
					   echo '<option value="'.$r["idCiudad"].'">'.$r["ciudad"].'</option>';
				   }
				   
				   
				   
				   echo '</select>
                </span>
            </div>
		 <div class="option-bar large">
            <label for="select-status">Tipo de Operación</label>
            <span class="selectwrap">
                
				
				<select name="operacion" id="operacion" class="search-select" style="display: none;">
				 
					<option value="1">Venta</option>
				 
					<option value="any" selected="selected">Todos</option>                </select>
            </span>
        </div>
		
		
		
			 <div class="option-bar large">
            <label for="select-property-type">Tipo de Propiedad</label>
            <span class="selectwrap">
			  
			  <select name="tipo" id="tipo" class="search-select" style="display: none;">';
			  $m=array(1=>"Casas",2=>"Departamentos",3=>"Parcelas",4=>"Sitios",5=>"Oficina Comercial",6=>"Propiedad Industrial",7=>"Terreno",8=>"Cabañas");                   
					foreach($m as $clave=>$valor){
						echo '<option value="'.$clave.'">'.$valor.'</option>';
					}
					 
					
					echo '<option value="any" selected="selected">Todos</option>                
					
					</select>
            </span>
        </div>
                <div class="option-bar small">
            <label for="select-bedrooms">Dormitorios</label>
            <span class="selectwrap">
               <div id="select-bedrooms_container" class="selectbox-wrapper" style="display: none; height: 200px;">
			   </div>
			   <select name="bedrooms" id="select-bedrooms" class="search-select" style="display: none;">
                    <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="any" selected="selected">Todos</option>                </select>
            </span>
        </div>
                <div class="option-bar small">
            <label for="select-bathrooms">Baños</label>
            <span class="selectwrap">
               <div id="select-bathrooms_container" class="selectbox-wrapper" style="display: none; height: 200px;"><ul><li id="select-bathrooms_input_1">1</li><li id="select-bathrooms_input_2">2</li><li id="select-bathrooms_input_3">3</li><li id="select-bathrooms_input_4">4</li><li id="select-bathrooms_input_5">5</li><li id="select-bathrooms_input_6">6</li><li id="select-bathrooms_input_7">7</li><li id="select-bathrooms_input_8">8</li><li id="select-bathrooms_input_9">9</li><li id="select-bathrooms_input_10">10</li><li id="select-bathrooms_input_any" class="selected">Todos</li></ul></div><select name="bathrooms" id="select-bathrooms" class="search-select" style="display: none;">
                    <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="any" selected="selected">Todos</option>                </select>
            </span>
        </div>
 

     
               
        
    <div class="option-bar">
        <input value="Buscar" class=" real-btn btn" type="submit">
    </div>

  
			</form>
	</div>
	</section>';
	}
	public function boxSearch(){
		$this->link=$this->conectar();
		$sql="select* from mm_ciudad order by ciudad desc";
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
		echo '<div class="box" role="search" style="margin-top:0px !important;padding:0px; margin-bottom:0px;margin-left:0px; margin-right:0px;">
		<div style="padding: 20px 0 18px 0 !important;
		overflow: hidden;
		border: 0;
		background-color: #f8f8f8 !important;
		margin-bottom: 10px;">
					  <div class="box-content">     
					   
  
				 <form name="frm-buscar" id="frm-buscar" class="frm-buscar" action="index.php" method="post"> 
								  <div class="opcion operacion">
				
								  <div class="styled-select">
								  <select name="operacion" id="operacion" title="Seleccione el tipo de operación">
									  <option value="0" selected="selected">Operación</option>
									 <option value="1">Ventas</option>										</select>
							  </div>
								  </div>
								  
								  <div class="opcion tipo">
									  <div class="styled-select">
										  <select name="tipo" id="tipo" title="Seleccione el tipo de propiedad">											
											  <option value="0">Tipo de propiedad</option>
									  <option value="1">Casa</option>
									  <option value="2">Departamentos</option>
									  <option value="3">Parcelas</option>
									  <option value="4">Sitios</option>
									  <option value="5">Oficina Comercial</option>
									  <option value="6">Propiedad Industrial</option>
									  <option value="7">Terreno</option> 		</select>
									  </div>
								  </div>
								  
																	  <div class="opcion region">	
										  <div class="styled-select">
											  <select id="ciudad" name="ciudad" title="Seleccione Ciudad">';
											  echo "<option value=0 selected='selected'>Ciudad</option>";
											  while($row=mysqli_fetch_array($query)){
												  echo "<option value='".$row["idCiudad"]."'>".$row["ciudad"]."</option>";
											  }
											  echo '</select>
										  </div>
									  </div>
						  <input type="hidden" name="action" id="action" value="true">
						  <div class="frm-text">	
									 
						  <input type="text" name="codigo" id="codigo" style="width:100%; height:55px;" class="" placeholder="ingrese un texto" value="">
					   
				  </div>
								  
								  <div class="frm-input">
								  
									  
							 <button name="buscar" id="buscar" role="submit" class="btn btn-success btn-lg" style="padding: .10rem 3rem;
  padding: 0px !important;
  font-size: 1rem !important;
  line-height: 1.5 !important;
  border-radius: 0px !important;
  height: 52px !important;
  width: 100% !important;"><i class="fas fa-search" aria-hidden="true"></i>&nbsp;&nbsp;Buscar</button>
								  </div>
							  </form>
						  </div>
					  
						   </div>
				  </div>';
	}
	public function contacto22(){
		$d=$this->datosPag();
		if(isset($_POST["action"])){
			$nombre=htmlentities($_POST["nombre"]);
			$email=htmlentities($_POST["email"]);
			$telefono=htmlentities($_POST["telefono"]);
			$mensaje=htmlentities($_POST["mensaje"]);
			$idProp=htmlentities($_GET["idProp"]);
			if($this->emailContacto($nombre,$email,$telefono,$mensaje,$idProp)){
				
				header("location:index.php?mod=det&idProp=".$idProp."&msg=1");
				exit;
			}
		}
	 
		if(isset($_GET["msg"])){
			echo '<div class="alert alert-success" style="margin-top:200px;" role="alert">
			Mensaje se ha enviado con exito, se contactara a la brevedad uno de nuestros ejecutivos
		  </div>';
		}

						echo '<div class="card" style="background-color:white;margin-top:50px;padding:20px;">';        					
						echo "<div style='margin-bottom:20px;'><h4>Solicitar Visita</h4></div>";
						echo '<input type="hidden" name="codigo" value="">						
						<div>
									<label for="nombre">Nombre:</label> 
						</div>
						<div>
									<input type="text" name="nombre" id="nombre" class="form-control form-control-sm" placeholder="Nombre" tabindex="1">
						</div>
						<div>								
									<label for="email">E-mail:</label>
						</div>
						<div>
									<input type="text" name="email" id="email" class="form-control form-control-sm" placeholder="Email" tabindex="2">
						</div>
						<div>
									<label for="telefono">Teléfono:</label>

									
						</div>
						<div>
									<input type="text" name="telefono" id="telefono" class="form-control form-control-sm" placeholder="Telefono" tabindex="3">
						</div>
						<div>	
									<label for="mensaje=">Mensaje:</label>
						</div>
						<div>
									<textarea name="mensaje" id="mensaje" class="form-control form-control-sm" placeholder="mensaje" tabindex="5"></textarea>
						<input type="hidden" name="action" id="action" true="value"/>
						</div>
						<div style="margin-top:20px; ">
						<button class="btn btn-success btn-sm" name="en1" id="en1" style="width:100%;padding-top:10px; padding-bottom:10px;"><i class="far fa-hand-point-up"></i> Solicitar Visita</button>
						
						</div>
							
				</div> ';
	}
	public function info(){
		$this->link=$this->conectar();
		if(isset($_GET["idProp"])){
			$id=htmlentities($_GET["idProp"]);
		}
		$sql=$this->sql->sqlConsultarUnaVivienda($id);
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));		
		$row=mysqli_fetch_array($query);
		
		echo "<div style='background-color:white; padding:10px;'>";		
		echo "<div style='padding-top:17px; padding-bottom:17px;margin-left:20px;'><h3>";
		if($row["precioUf"]==2){
			echo "UF ".$this->formatoNumerico($row["precio"]);
		}else{
			echo "$ ".$this->formatoNumerico($row["precio"]);
		}
		echo "</h3></div>";
		echo '<ul class="list-group list-group-flush" style="margin:10px !important;">
		<li class="list-group-item">Operación: '.$this->devolverOperacion($row["operacion"]).'</li>
		<li class="list-group-item">Dormitorios: '.$row["dormitorios"].'</li>
		<li class="list-group-item">Baños: '.$row["banos"].'</li>
		<li class="list-group-item">Mt2 Construido: '.$row["m2Construido"].'</li>
		<li class="list-group-item">Mt2 Totales: '.$row["mt2Totales"].'</li>
		
		<li class="list-group-item">Piscina: '.$row["piscina"].'</li>
		<li class="list-group-item">Cocina: '.$row["cocina"].'</li>
		<li class="list-group-item">Living: '.$row["living"].'</li>
		<li class="list-group-item">Comedor: '.$row["comedor"].'</li>
		<li class="list-group-item">Bodega: '.$row["bodega"].'</li>
		<li class="list-group-item">Logia: '.$row["logia"].'</li>
		<li class="list-group-item">Estacionamiento: '.$row["estacionamiento"].'</li>
		<li class="list-group-item"><a style="margin-top:10px; width:100%;padding-top:10px; padding-bottom:10px;color:white !important;" role="button" href="contacto.php"  class="btn btn-success btn-sm"><i class="far fa-hand-point-up"></i> Contactar</a></li>
		

	  </ul>';
	 
	}
	public function tituloProp($id=false){
		$this->link=$this->conectar();
		$sql=$this->sql->sqlConsultarUnaVivienda($id);
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));		
		$row=mysqli_fetch_array($query);
		
		echo "<div class='container'>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<div style='margin-bottom:15px;padding-left:20px;'><h3 style='font-family: 'Roboto', sans-serif !important;color:#555 !important;'>";
		echo $row["titulo"];		
		echo '</h3></div>';
		echo '<div class="navegador" name="searchBox" id="searchBox" style="padding-top:10px; padding-bottom:20px;">
		<span><span style="font-weight:bold;">Operación:</span> '.$this->devolverOperacion($row["operacion"]).'</span>&nbsp;&nbsp;|&nbsp;&nbsp;

		<span><span style="font-weight:bold;">Tipo:</span> '.$this->devolverTipoProp($row["tipoProp"]).'</span>&nbsp;&nbsp;|&nbsp;&nbsp;

		<span><span style="font-weight:bold;">Sector:</span> '.$this->devolverCiudad($row["ciudad"]).'</span>&nbsp;&nbsp;|&nbsp;&nbsp;

		<span>
		<span style="font-weight:bold;">Estado:</span> '.$this->devolverEstadoProp($row["estadoProp"]).' </span>&nbsp;&nbsp;|&nbsp;&nbsp;
		</div>';
		
		echo "</div>";
		echo "</div>";
		echo "</div>";

		
	}
	public function mostrarCordenadas(){
		$this->link=$this->conectar();
		if(isset($_GET["idProp"])){
			$id=htmlentities($_GET["idProp"]); 
		}
		$sql=$this->sql->sqlConsultarUnaVivienda($id);
		$q=mysqli_query($this->link,$sql);
		$row=mysqli_fetch_array($q);
		$cor=$row["cordenadas"];
		 
		return($cor);
	}
	public function detalleProp($id=false){
		$this->link=$this->conectar();
		if(isset($_GET["idProp"])){
			$id=htmlentities($_GET["idProp"]); 
		}
		$sql=$this->sql->sqlConsultarUnaVivienda($id);
	 
		 
		 	 if(isset($_GET["mod"])){
								
								 }
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
		echo '<div role="main" class="right-content">
					
					<div class="titulo p-detalle">
						<div class="content">
		        			<h1>'.$row["titulo"].'</h1>
		        		</div>
		        	</div>
					
					<div class="navegador">
							        					<span><span>Operaci&oacute;n:</span>'.$this->devolverOperacion($row["operacion"]).'</span>
        					        					
        												<span><span>Tipo:</span> '.$this->devolverTipoProp($row["tipoProp"]).'</span>
														
														<span><span>Ciudad:</span>'.$this->devolverCiudad($row["ciudad"]).'</span>
														
														<span>
																	<span>Estado:</span> '.$this->devolverEstadoProp($row["estadoProp"]).'															</span>
													</div>
	        		
					<div class="contenido">
						
						<div class="prop-imagen">
        												
														
															<div id="products_example">
        							<div id="products">
		        						<div class="slides_container">';
		        							
											foreach($arch as $clave=>$valor){
												echo '<a rel="gall" href="./upload/'.$valor.'">';
												echo '<img src="./upload/'.$valor.'" width="300" alt="." /></a>';
											}
											echo '</div>
										
										<ul class="pagination">';
										foreach($arch as $clave=>$valor){
											echo '<li><a href="javascript:void(0)"><img src="./upload/'.$valor.'" height="50" width="65" alt="."></a></li>';
										}
										echo '</ul>										
									</div>
								</div>
							        				</div>
        				
        				<div class="prop-datos">
        					
        					<div class="clear"></div>
        					<div class="addthis_toolbox addthis_default_style addthis_16x16_style share">
	<a class="addthis_button_facebook"></a>
	<a class="addthis_button_whatsapp"></a>
	<a class="addthis_button_twitter"></a>
	<a class="addthis_button_linkedin"></a>
	<a class="addthis_button_google_plusone_share"></a>
	<a class="addthis_button_compact"></a><a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
<script type="text/javascript" src="./../s7.addthis.com/js/300/addthis_widget.js#pubid=ra-523290af0f69c66d"></script>
<script>var addthis_config={data_track_clickback:false}</script>        					<div class="clear"></div>
        					
        					        						<div class="dato">C&oacute;digo:</div><div class="valor">'.$row["codigo"].'</div>
        					        					
        					        						<div class="dato">Operaci&oacute;n:</div><div class="valor">'.$this->devolverOperacion($row["operacion"]).'</div>
        					        					
        					        						<div class="dato">Precio:</div><div class="valor">$ ';
															
															echo $this->formatoNumerico($row["precio"]);
															if($row["precioUf"]==2){
																echo " U.F.";
															}
															
															echo '</div>
        						        					        					
        					        						<div class="dato">Mts. totales:</div><div class="valor">'.$this->formatoNumerico($row["mt2Totales"]).'</div>
        					        					
        					        						<div class="dato">Mts. construidos:</div><div class="valor">'.$this->formatoNumerico($row["m2Construido"]).'</div>
        					        					
        					        					
        					        					
        					        						<div class="dato">Dormitorios:</div><div class="valor">'.$row["dormitorios"].'</div>
        					        					
        					        						<div class="dato">Ba&ntilde;os:</div><div class="valor">'.$row["banos"].'</div>
        					        					
        					        						<div class="dato">Cocina:</div><div class="valor">'.$row["cocina"].'</div>
        					        					
        					        						<div class="dato">Piscina:</div><div class="valor">'.$row["piscina"].'</div>
        					        					
        					        						<div class="dato">Bodega:</div><div class="valor">'.$row["bodega"].'</div>
        					        					
        					        					    <div class="dato">Logia:</div><div class="valor">'.$row["logia"].'</div>
        					        					
        					        				 
        					        					
        					        					
        					        					<div class="cotizar">
        						<p><input type="button" onclick="location.href="./contacto/index.html"" class="boton" value="Contactar" /></p>
        						<br /><br />	
        					</div>
        				</div>
        				
        				<div class="clear"></div>
        				
        				        				<div class="general">
	        				<h3>Descripci&oacute;n</h3>
	        				
	        				<p>'.nl2br($row["descripcion"]).'</p>
 		</div>
						
												
													<div class="clear"></div>
							<br>
							<h3>Mapa de Ubicaci&oacute;n</h3>
							<div id="detalle-mapa"></div>
							        		</div>
	        		<div class="clear"></div>
	        	</div>';
				return($row["cordenadas"]);
	}
	 
	 
	public function sqlCreaCadena($operacion,$tipo,$ciudad,$moneda){
		if($operacion==1 && $tipo==1 && $ciudad==1 && $moneda==1){
			$sql="select* from mm_propiedad ";
		}else{ 
		$sql="select* from mm_propiedad where "; 
		if($operacion!=1){
			$sql.=" operacion='".$operacion."' or";
		}
		if($tipo!=1){
		$sql.=" tipoProp='".$tipo."' or";
			}
		if($ciudad!=1){
		$sql.=" ciudad='".$ciudad."' or";
		}
		if($moneda!=1){
		$sql.=" moneda='".$moneda."'";	
		}
		$sql=substr($sql,0,-3);
		}
		$sql.=" order by idProp desc";
		return($sql);
	}
	public function mostrarResultados2(){
		$this->link=$this->conectar();
			if(isset($_POST["Buscar"])){
			 
			$operacion=htmlentities($_POST["operacion"]);
			$tipo=htmlentities($_POST["tipo"]);
			$ciudad=htmlentities($_POST["ciudad"]);
			$moneda=htmlentities($_POST["moneda"]);
			$sql=$this->sqlCreaCadena($operacion, $tipo, $ciudad, $moneda);
			$this->desplegarPropBuscador(3,$sql);
			 
		}
	}  
	public function rotadorDet(){
		$this->link=$this->conectar();
			$sql="select* from rotator order by idRotator desc";
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
		 
		while($row=mysqli_fetch_array($query)){
			echo '<li>
			<div class="desc-wrap">
			<div class="slide-description">
			<h3>Custom Slide Without Target URL</h3>
			<p>Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>									</div>
			</div>
			<img alt="." src="./upload/slide/'.$row["imagen"].'" />
			</li>';
							
							
 
		}
	}
	public function det(){
		$this->link=$this->conectar();
		if(isset($_GET["idProp"])){$id=htmlentities($_GET["idProp"]);}
		echo '<div class="slide-desc"><h3>';
		if(isset($_GET["mod"]) && $_GET["mod"]=="arriendo"){
			echo "Arriendo";
		}else if(isset($_GET["mod"]) && $_GET["mod"]=="ventas"){ 
			echo "Ventas";
		}else if(isset($_GET["mod"]) && $_GET["mod"]=="contacto"){ 
			echo "Contacto";
		} else if(isset($_GET["mod"]) && $_GET["mod"]=="pag"){
			if(isset($_GET["m"])){
				if(isset($_GET["rd"])){
				$id=htmlentities($_GET["rd"]);
				$sql="select* from mm_coti_contenido where idContenido='".$id."'";
			 $q=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
			$r=mysqli_fetch_array($q);
		 
			echo $r["titulo"];	
				}
			}else{
				if(isset($_GET["rd"])){
					 
					$id=htmlentities($_GET["rd"]);
					$sql="select* from submenu where idSub='".$id."'";
				 
					$q1=mysqli_query($this->link,$sql);
					$r1=mysqli_fetch_array($q1);
					echo $r1["nombre"];
				}
					
			} 
			
			}else{			 
				$sql="select titulo from mm_propiedad where idProp='".$id."'"; 
				$q=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
			$r=mysqli_fetch_array($q);
		 
			echo $r["titulo"];	
		}	
			  
			
				
		echo '</h3></div>';
	 
	}
	public function rotador(){
		$this->link=$this->conectar();
		$sql="select* from rotator order by idRotator asc";
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));	
		$i=0;		
		while($row=mysqli_fetch_array($query)){
			$i++;
			if($i==1){
				echo '<div class="carousel-item active">
				<img src="./data1/images/'.$row["imagen"].'" class="d-block w-100" alt="...">
				<div class="carousel-caption d-none d-md-block">
				<h2>Buscas un nuevo hogar</h2>
				<p style="font-size:20px;">Tenemos las mejores propiedades para ti.</p>
			
				</div>
			  </div>';  
			}else{
				echo '<div class="carousel-item">
				<img src="./data1/images/'.$row["imagen"].'" class="d-block w-100" alt="...">
				<div class="carousel-caption d-none d-md-block">
			
			
				</div>
			  </div>';  
			}
			
		}
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
		} 
		
		return($k);
	}
	public function devolverEstadoProp($id){
		if($id==1){
			$k="Se Vende";
		}else if($id==2){
			$k="Se Arrienda";
		}else if($id==3){
			$k="Arrendada";
		}else if($id==4){
			$k="Vendida";
		}else if($id==5){
			$k="Reservada";
		}else if($id==6){
			$k="Arriendo por dias";
		}else if($id==7){
			$k="Arriendo marzo a diciembre";
		}else if($id==8){
			$k="Arriendo año corrido";
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
		}
		return($k);
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
	 function indicadoresEconomicos2(){
		$datos=$this->datosPag();
		/* visualiza los indicadores economicos pie pagina*/
		
		
		  $apiUrl =  $datos["enlaceIndicador"];//'';
//Es necesario tener habilitada la directiva allow_url_fopen para usar file_get_contents
if ( ini_get('allow_url_fopen') ) {
    $json = file_get_contents($apiUrl);
} else {
    //De otra forma utilizamos cURL
    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($curl);
    curl_close($curl);
}
 
$dailyIndicators = json_decode($json);
 
	 
	 
	 echo "<div style='font-weight:bold; margin-bottom:5px; font-size:14px; color:white;'>INDICADORES : <span STYLE='color:white;font-size:15px;'>UF:</span> <span style='color:white;font-size:15px;'>$".$this->formatoNumerico2($dailyIndicators->uf->valor)."</span>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "IPC: <span style='color:white;font-size:15px;'>".$dailyIndicators->ipc->valor." %</span>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "UTM: <span style='color:white;font-size:15px;'>$".$this->formatoNumerico2($dailyIndicators->utm->valor)."</span>&nbsp;&nbsp;|&nbsp;&nbsp;";
 
	echo "Dolar: <span style='color:white;font-size:15px;'>$".$dailyIndicators->dolar->valor."</span>&nbsp;&nbsp;|&nbsp;&nbsp;";
	
	echo "Euro: <span style='color:white;font-size:15px;'>$".$dailyIndicators->euro->valor."</span></div>";


	  }
	public function menuCategorias(){
		$this->link=$this->conectar();
		echo "<table width='100%' border=1>";
		echo "<tr><td><h3>Categorias</h3></td></tr>";
		$sql=$this->sql->sqlConsultarCiudad();
		$res=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
		while($row=mysqli_fetch_array($res)){
			
			echo "<tr><td><a href='?mod=cat&p=".$row["idCiudad"]."'>".$row["ciudad"]."</a></td></tr>";
		}
		echo "</table>";
	}
	 
	public function setGeo($valor){
		$this->geo=$valor;
		return(true);
	}
	public function getGeo($valor){
		$geo=$this->geo();
		return($geo);
	}
	  
	 function indicadoresEconomicos(){
		$datos=$this->datosPag();
		/* visualiza los indicadores economicos pie pagina*/
		
		$url = $datos["enlaceIndicador"];
		$xml = simplexml_load_file($url);
 
	echo "<div style='font-weight:bold; margin-bottom:5px; font-size:14px;'>UF: ".$xml->indicador->uf."</div>";
	echo "<div>IPC: ".$xml->indicador->ipc."</div>";
	echo "<div style='font-size:14px;margin-bottom:5px;'> UTM: ".$xml->indicador->utm."</div>";
 
	echo "<div style=' font-size:14px;margin-bottom:5px;'>Dolar: ". $xml->moneda->dolar."</div>";
	echo "<div>Dolar CLP: ".$xml->moneda->dolar_clp."</div>";
	echo "<div style='font-size:14px;margin-bottom:5px;'>Euro: ".$xml->moneda->euro."</div>";
  
	  
}
    public function jScript(){
        
        // echo ' <script src="http://code.jquery.com/jquery-1.9.1.js"></script>';
		    echo ' <script src="../js/jquery-1.9.1.js"></script>';
        echo "<script>
       
        ";        
        echo 'function ejecutar(){
     var res=new Array();
            var idPais=$("#Ciudad").val();                 
            $("#comuna").empty();
            $.ajax({ 
                  type: "POST",
                  url: "./clases/proceso.php",
                  data:{id:idPais},
                  dataType: "json",
                  success: function(datos){
                    res=datos;                   
                    for(var k in res){
                        for(var i in res[k]){  
                           
                          $("#comuna").append("<option value="+i+">"+res[k][i]+"</option>");
                        }
                    }                    
                  }
            });
                 
        }';
        echo "</script>";
    } 
    
}  
?> 