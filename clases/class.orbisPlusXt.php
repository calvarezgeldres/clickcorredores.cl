 <?php
 
 ob_start();
/*
Autor: Luis Olguin  - Programación Web Chile 2015
Descripción : Pagina web autoadministrable para corretaje de propiedades
 
Fecha : 4/3/2015
Revisión:27/7/2017
Revisión:3/8/2015
Revisión: 15/05/2022
Revision: 24/6/2022
Framework; Bootstrap 3
Descripcion:CmsProp 4.1 Lite
 
*/
error_reporting(0); 

require_once("./clases/class.coneccion.php");
require_once("./clases/class.paginator.php");
require_once("./clases/class.rotador.php"); 
require_once("./clases/class.msgBox.php");
require_once("./clases/class.sqlPlus.php");
class orbis extends coneccion{        
    private $paginator;			
	private $sql;	
	public $rotador;	
	public $link;
    public function __construct(){        
        $this->link=$this->conectar();
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
		$this->link=$this->conectar();
		$this->paginator=new paginator(21,8);
		if(isset($_GET["km"])){
			$id=htmlentities($_GET["km"]);
		   $this->detallePropiedad($id);
	   }else{				   
			  if(isset($_GET["action"])){		
				
				   if($_GET["operacion"]!=0 || $_GET["tipo"]!=0 || $_GET["ciudad"]!=0 || $_GET["codigo"]!=0 || $_GET["orden"]!=0){

						  $sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where papelera=0 and activar=0  and ";


						 
						  if(isset($_GET["operacion"])){		   
							  $operacion=htmlentities($_GET["operacion"]);
							  if($operacion!=0){
							   $sql.=" mm_propiedad.operacion='".$operacion."' and ";
							  }
						  }
						  if(isset($_GET["tipo"])){
							  $tipo=htmlentities($_GET["tipo"]);
								  if($tipo!=0){	
									$sql.=" mm_propiedad.tipoProp='".$tipo."' and ";
								  }		
						  }
						  if(isset($_GET["ciudad"])){
							  $ciudad=htmlentities($_GET["ciudad"]);
							  if($ciudad!=0){
								$sql.=" mm_propiedad.ciudad='".$ciudad."' and ";
							  }
						  }			 
						  if(isset($_GET["codigo"])){ 
							   $codigo=htmlentities($_GET["codigo"]);
							   if($codigo!=0){
									 $sql.=" mm_propiedad.codigo='".$codigo."' and ";
							   }
					   }
						  if(isset($_GET["orden"]) && $_GET["orden"]!=0){
							 $sql=substr($sql,0,-6);
							 if($_GET["orden"]==1){
								   $sql.=" order by mm_propiedad.precio asc";
						   }else{
								   $sql.=" order by mm_propiedad.precio desc";
						   }
						  }else{
							  
						   $sql=substr($sql,0,-4);					
						   $sql.=" GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";
						  } 
				   }else{
					$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where papelera=0 and activar=0  GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";
				   }
				   $cadena="operacion=".$operacion."&action=".$action="&tipo=".$tipo."&ciudad=".$ciudad."&orden=".$orden."&codigo=".$codigo;
				   $this->paginator->estableceIndex("index.php?k=6&".$cadena);	
				   }else{
				   $this->paginator->estableceIndex("index.php?k=5");
						  if(isset($_GET["mod"]) && $_GET["mod"]=="arriendo" || $_GET["mod"]=="ventas"){
							 if($_GET["mod"]=="arriendo"){
									$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where papelera=0 and activar=0 and operacion='2'  GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc ";									
							  }else{
								$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where  papelera=0 and activar=0 and operacion='1'  GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";									
							  }
						  }else{
							$sql="select * from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where activar=0 and papelera=0 GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";
						  }
				  }  
				$this->paginator->agregarConsulta($sql);          
			   	if(isset($_GET["mod"])){
					   $mod=htmlentities($_GET["mod"]);
					   if(isset($_GET["reg"])){
							   $reg=htmlentities($_GET["reg"]);
								  $this->paginator->estableceIndex("index.php?k=1&mod=".$mod."&reg=".$reg);
					   }else{
							   $this->paginator->estableceIndex("index.php?k=1&mod=".$mod);
					   }
			   }else{
						 if(isset($_GET["action"])){				
						   if(isset($_GET["operacion"]) &&  isset($_GET["action"]) && isset($_GET["tipo"]) && isset($_GET["ciudad"]) && isset($_GET["orden"]) && isset($_GET["codigo"])){
	   
							   if(isset($_GET["operacion"])){
								   $operacion=htmlentities($_GET["operacion"]);
									
							   }
							   if(isset($_GET["action"])){
								   $action=htmlentities($_GET["action"]);					
							   }
							   if(isset($_GET["tipo"])){
								   $tipo=htmlentities($_GET["tipo"]);
									
							   }
							   if(isset($_GET["ciudad"])){
								   $ciudad=htmlentities($_GET["ciudad"]);
										
							   }
							   if(isset($_GET["orden"])){
								   $orden=htmlentities($_GET["orden"]);
									
							   }
							   if(isset($_GET["codigo"])){
									   $codigo=htmlentities($_GET["codigo"]);					 
							   }
							   $cadena="operacion=".$operacion."&action=".$action="&tipo=".$tipo."&ciudad=".$ciudad."&orden=".$orden."&codigo=".$codigo;
							   $this->paginator->estableceIndex("index.php?k=2&".$cadena);		
						   } 		
						}  

			   }	

			}
		$this->paginator->agregarConsulta($sql); 
		$total=$this->paginator->obtenerTotalReg();   			
		echo "<div class='row'>";
		while($row=$this->paginator->devolverResultados()){			
			$rutaFoto=$row["ruta"]; 			
			$ancho = "70%";          
			$alto = "70%";	
			echo ' <div class="col-md-4" style="padding-left:15px; padding-right:15px;">
			<div class="card" style="width:100%;margin-top:40px;">';    
			echo "<div class='img-container'>";
			echo '<a title="'.$row["titulo"].'" href="index.php?mod=det&idProp='.$row["idProp"].'" class="titulo" >';
			if($row["estadoProp"]==3 || $row["estadoProp"]==4){
				if($alto>=245){
					if($this->verificaAws($rutaFoto)){
						echo '<div  align="center"><img src="'.$rutaFoto.'" class="card-img-top" style="height:260px; width:100%;filter: grayscale(1);-webkit-filter: grayscale(1);-moz-filter: grayscale(1);-ms-filter: grayscale(1);-o-filter: grayscale(1);width:100%; max-width:100%;" alt="'.$row["titulo"].'"></div>';
					}else{
						echo '<div  align="center"><img src="./upload/'.$rutaFoto.'" class="card-img-top" style="height:260px; width:100%;filter: grayscale(1);-webkit-filter: grayscale(1);-moz-filter: grayscale(1);-ms-filter: grayscale(1);-o-filter: grayscale(1);width:100%; max-width:100%;" alt="'.$row["titulo"].'"></div>';
					}
				}else{
					if($this->verificaAws($rutaFoto)){
						echo '<img src="'.$rutaFoto.'" class="card-img-top" style="filter: grayscale(1);-webkit-filter: grayscale(1);-moz-filter: grayscale(1);-ms-filter: grayscale(1);-o-filter: grayscale(1);width:100%; max-width:100%;" alt="'.$row["titulo"].'">';
					}else{
						echo '<img src="./upload/'.$rutaFoto.'" class="card-img-top" style="filter: grayscale(1);-webkit-filter: grayscale(1);-moz-filter: grayscale(1);-ms-filter: grayscale(1);-o-filter: grayscale(1);width:100%; max-width:100%;" alt="'.$row["titulo"].'">';
					}
					
				}				
			}else{
				if($alto>=245){
					if($this->verificaAws($rutaFoto)){
						echo '<img class="imagen" src="'.$rutaFoto.'" alt="'.$row["titulo"].'" style="height:260px;width:100%; max-width:100%;"/>';
					}else{
						echo '<img class="imagen" src="./upload/'.$rutaFoto.'" alt="'.$row["titulo"].'" style="height:260px;width:100%; max-width:100%;"/>';
					}	
					
				}else{
					if($this->verificaAws($rutaFoto)){
						echo '<img class="imagen" src="'.$rutaFoto.'" alt="'.$row["titulo"].'" style="width:100%; max-width:100%;"/>';
					}else{
						echo '<img class="imagen" src="./upload/'.$rutaFoto.'" alt="'.$row["titulo"].'" style="width:100%; max-width:100%;"/>';
					}						
				}				
			}			
			echo '
			<div class="box-estado" ';		 
				if($row["estadoProp"]==1){
					echo 'style="background:url(https://www.saulpoozapata.com/imagen/sevende.png) no-repeat;"';
				}else if($row["estadoProp"]==2){
					echo 'style="background:url(https://www.saulpoozapata.com/imagen/searrienda.png) no-repeat; top:10px;"';
				}else if($row["estadoProp"]==3){
					echo 'style="background:url(https://www.saulpoozapata.com/imagen/searrendo.png) no-repeat; top:10px;"';
				}else if($row["estadoProp"]==4){
					echo 'style="background:url(https://www.saulpoozapata.com/imagen/sevendio.png) no-repeat; top:10px;"';
				}else{
					echo 'style="background:url(https://www.saulpoozapata.com/imagen/reservada.png) no-repeat; top:10px;"';
				}
			echo ">&nbsp;</div>	";
			echo "</a>";
			echo "</div>";
					echo '<div class="card-body">';
					echo "<div>";
					echo '<p><i class="fas fa-map-marker-alt"  aria-hidden="true"></i>';
					echo $row["tipo"]."&nbsp;".$this->devolverCiudad($row["ciudad"])." | ".$this->devolverOperacion($row["operacion"]);
				    echo '</p>';
					echo "</div>";
						echo '<h5 class="card-title">';
						echo '<a title="'.$row["titulo"].'" href="index.php?mod=det&idProp='.$row["idProp"].'" class="titulo">'.$row["titulo"].'</a>';
						echo '</h5>
						<p class="card-text" style="font-size:20px; color:#2088D3;font-weight:bold;">';
						if($row["precioUf"]==2){
							if(preg_match("/\./i",$row["precio"])){
								   echo $row["precio"]." U.F";
							}else{
								echo $this->formatoNumerico($row["precio"])." UF";
							}
						  }else 	if($row["precioUf"]==3){
							  echo "US Dollar: ".$this->formatoNumerico($row["precio"]);
						  }else{
							  echo "$ ".$this->formatoNumerico($row["precio"]);
						  }
						echo '</p>						
					</div>
					<div class="card-footer">
				  <table width="100%">
					<tbody><tr>
					  <td width="40%"><img src="https://www.cacciuttolopropiedades.cl/mt2.png" style="width:15%;margin-right:10px;" data-pagespeed-url-hash="294195115" > '.$row["mt2Totales"].' mts2 </td>
					  <td><i class="fas fa-bath" aria-hidden="true"></i> '.$row["banos"].' </td>
					  <td><i class="fas fa-bed" aria-hidden="true"></i> '.$row["dormitorios"].' </td>
					  <td><i class="fas fa-car-side" aria-hidden="true"></i> '.$row["estacionamiento"].' </td>
			
					</tr>
				  </tbody></table>
				  
				</div>
			</div>
	</div>';
		}
		echo "</div>";
		if($total>=6){
			echo "<div align='center'>";      
			$this->paginator->navegacion();
			echo "</div>";
	   }	  
	   mysqli_free_result($q);
	   mysqli_close($this->link);
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
	
    public function devolverRegion($idRegion){
		$this->link=$this->conectar();
		$sql="select* from mm_region where idRegion='".$idRegion."'";
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
		$row=mysqli_fetch_array($query);	 
		return(utf8_encode($row["nombre"]));
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
	function indicadoresEconomicos2(){
		$datos=$this->datosPag();
		$apiUrl =  $datos["enlaceIndicador"];//'';
		if ( ini_get('allow_url_fopen') ) {
    		$json = file_get_contents($apiUrl);
		}else{
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
		$url = $datos["enlaceIndicador"];
		$xml = simplexml_load_file($url); 
		echo "<div style='font-weight:bold; margin-bottom:5px; font-size:14px;'>UF: ".$xml->indicador->uf."</div>";
		echo "<div>IPC: ".$xml->indicador->ipc."</div>";
		echo "<div style='font-size:14px;margin-bottom:5px;'> UTM: ".$xml->indicador->utm."</div>"; 
		echo "<div style=' font-size:14px;margin-bottom:5px;'>Dolar: ". $xml->moneda->dolar."</div>";
		echo "<div>Dolar CLP: ".$xml->moneda->dolar_clp."</div>";
		echo "<div style='font-size:14px;margin-bottom:5px;'>Euro: ".$xml->moneda->euro."</div>";
	}  
}  
?> 