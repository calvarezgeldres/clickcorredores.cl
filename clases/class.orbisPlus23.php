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
 require_once("./clases/class.monitor.php");

class orbis extends coneccion{        
    private $paginator;			
	private $sql;	
	public $rotador;	
	public $link;
    public function __construct(){        
     
	 	$this->sql=new sql();		
	}
	public function red($dat) {
		$this->link = $this->conectar();	
		if ($dat && isset($_GET["idProp"])) {
			$idProp = $this->sanitizeInput($_GET["idProp"]);  	 
			$sql = "SELECT * FROM mm_propiedad WHERE idProp = ?";
			$stmt = mysqli_prepare($this->link, $sql);
			mysqli_stmt_bind_param($stmt, 'i', $idProp);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$r = mysqli_fetch_array($result);
			if ($r) {
				$titulo = $r['titulo'];
				$des = $r['des'];
			} else {
				$titulo = "Default Title";
				$des = "Default Description";
			}				
			$sql1 = "SELECT * FROM mm_cape_fotos WHERE idProp = ?";
			$stmt1 = mysqli_prepare($this->link, $sql1);
			mysqli_stmt_bind_param($stmt1, 'i', $idProp);
			mysqli_stmt_execute($stmt1);
			$result1 = mysqli_stmt_get_result($stmt1);
			$r1 = mysqli_fetch_array($result1);
	
			echo '<meta property="og:url" content="https://clickcorredores.cl/index.php?mod=det&idProp=' . htmlspecialchars($idProp, ENT_QUOTES, 'UTF-8') . '" />';
			echo '<meta property="og:type" content="website" />';
			echo '<meta property="og:title" content="' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '" />';
			echo '<meta property="og:description" content="' . htmlspecialchars($des, ENT_QUOTES, 'UTF-8') . '" />';
			echo '<meta property="og:image" content="https://clickcorredores.cl/upload/' .$r1['ruta']. '" />'; // Asegúrate de que 'image' sea el campo correcto
		} else {			
			echo '<meta property="og:url" content="https://clickcorredores.cl/" />';
			echo '<meta property="og:type" content="website" />';
			echo '<meta property="og:title" content="Click Corredores | Vende o Arrienda tu propiedad" />';
			echo '<meta property="og:description" content="Portal Inmobiliario Click Corredores -  Vende o arrienda tu propiedad" />';
			echo '<meta property="og:image" content="https://clickcorredores.cl/logoClick.png" />';
		}
	}
	private function sanitizeInput($input) {
		return mysqli_real_escape_string($this->link, trim($input));
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
		echo "<span style='font-size:12px;'>Página generada en : " . round($tiempo_fin - $this->tInicio, 4) ." segundos</span>"; 
	} 
	public function registrarVisita(){
		$this->monitor=new monitor();
		$this->monitor->registrarVisita();
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
		  // $this->detallePropiedad($id);
	   }else{				   		
			    if(isset($_GET["action"])){						
				  if($_GET["operacion"]!=0 || $_GET["tipo"]!=0 || $_GET["ciudad"]!=0 || $_GET["codigo"]!=0 || $_GET["orden"]!=0){
					$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where papelera=0 and mm_cape_fotos.portada=1  and ";
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
					   // action
					$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where papelera=0 and mm_cape_fotos.portada=1  GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";
				   }

				

				   $cadena="operacion=".$operacion."&action=".$action="&tipo=".$tipo."&ciudad=".$ciudad."&orden=".$orden."&codigo=".$codigo;
				   $this->paginator->estableceIndex("index.php?k=6&".$cadena);	
			    }else{
					
				   		  $this->paginator->estableceIndex("index.php?k=5");
						    if(isset($_GET["m"])){
								 if($_GET["m"]==2){
									$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where papelera=0 and mm_cape_fotos.portada=1 and mm_propiedad.operacion=2  GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc ";									
							  	 }else  if($_GET["m"]==1){
									$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where  papelera=0 and mm_cape_fotos.portada=1 and mm_propiedad.operacion=1  GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";									
								}else  if($_GET["m"]==6){
									$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where  papelera=0 and mm_cape_fotos.portada=1 and mm_propiedad.operacion=6  GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";									
								}
						    }else{								
								$sql="select * from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where mm_cape_fotos.portada=1 and papelera=0 GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";
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
			$ancho =$row["ancho"];
			$alto = $row["alto"];		
			 
			echo '
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-3 col-xxl-12">
			<div class="card" style="margin-bottom:30px;width:100%;box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.39),0 4px 6px -2px rgba(0,0,0,.05) !important;">
			<a href="index.php?mod=det&idProp='.$row["idProp"].'">	
			<img src="'.$rutaFoto.'" style="width:100%;height:200px;"  class="card-img-top" alt="...">
			</a>
				<div style="background-color:#eb9c19; padding:0px; height:5px;">&nbsp;</div>
				<div class="card-body" style="padding:0px;">
					<h5 class="card-title" style="font-weight: 500;font-size:14px;padding-left:25px; padding-right:25px;padding-top:20px;"><i class="fas fa-map-marker-alt" style="font-size:12px;"></i> '.$this->devolverRegion($row["idRegion"]).'</h5>
					<p class="card-text" style="font-size:14px;padding-left:25px;padding-right:25px;color:#5d5d5d;height:50px;"><a href="index.php?mod=det&idProp='.$row["idProp"].'" class="mt1">	'.utf8_encode($row["titulo"]).'</a></p>
					<ul class="list-group list-group-flush">
			<li class="list-group-item"><div style="float:left;color:#5d5d5d;font-weight:bold;font-size:18px;">';
			if($row["precioUf"]!=1){
				echo "UF ";
			}else{
				echo "$ ";
			}
			echo '</div><div style="float:right;color:#5d5d5d;font-weight:bold;font-size:18px;">';
			if($row["precioUf"]!=1){
				echo $this->formatoNumerico($row["precio"]);
			}else{
				echo $this->formatoNumerico($row["precio"]);
			}
			echo '</div></li>
		 
		  </ul>
				</div>
				<div class="card-footer" style="font-size:14px;background-color:#fb2750;">
		   <div style="color:white;font-weight:bold;" align="center"><img src="http://www.propiedadestroncoso.cl/images/area-icon.png"/>&nbsp;'.$this->formatoNumerico($row["m2Construido"]).'<sup>&nbsp;Mt2</sup> &nbsp; &nbsp;
		   <img src="http://www.propiedadestroncoso.cl/images/rooms-icon.png"/> &nbsp; '.$row["dormitorios"].' &nbsp;&nbsp;<img src="http://www.propiedadestroncoso.cl/images/bathrooms-icon.png"/>&nbsp;  &nbsp;'.$row["banos"].'&nbsp;</div>
		  </div>
			</div>
		
			</div>
			';
		 
			
		}
		echo "</div>";
		if($total>=6){
			echo "<div align='center'>";      
			$this->paginator->navegacion();
			echo "</div>";
	   }	  
	   
	   }





	   public function desplegarProp4(){
		$this->link=$this->conectar();
		$this->paginator=new paginator(216,216);
		
		if(isset($_GET["km"])){
			$id=htmlentities($_GET["km"]);
		  // $this->detallePropiedad($id);
	   }else{					
			if(isset($_GET["action1"])){	
	 
				  if($_GET["action1"]==true){
					
					$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where papelera=0 and mm_propiedad.estadoPublicacion=0 and mm_cape_fotos.portada=1  and ";
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

				 	 	
					if(isset($_GET["region"])){
						$region=htmlentities($_GET["region"]);
						if($region!=0){
						  $sql.=" mm_propiedad.idRegion='".$region."' and ";
						}
					}		
					if(isset($_GET["region33"])){
						$region=htmlentities($_GET["region33"]);
						if($region!=0){
						  $sql.=" mm_propiedad.idRegion='".$region."' and ";
						}
					}		

					if(isset($_GET["ciudad33"])){
						$ciudad=htmlentities($_GET["ciudad33"]);
						if($ciudad!=0){
						  $sql.=" mm_propiedad.idCiudad='".$ciudad."' and ";
						}
					}		
					if(isset($_GET["ciudad"])){
						$ciudad=htmlentities($_GET["ciudad"]);
						if($ciudad!=0){
						  $sql.=" mm_propiedad.idCiudad='".$ciudad."' and ";
						}
					}			 
					if(isset($_GET["comuna33"])){ 
						 $comuna=htmlentities($_GET["comuna33"]);
						 if($comuna!=0){
							   $sql.=" mm_propiedad.idComuna='".$comuna."' and ";
						 }
					 }
					 if(isset($_GET["comuna"])){ 
						$comuna=htmlentities($_GET["comuna"]);
						if($comuna!=0){
							  $sql.=" mm_propiedad.idComuna='".$comuna."' and ";
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
					   // action
					$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where papelera=0 and mm_cape_fotos.portada=1 and mm_propiedad.estadoPublicacion=0  GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";
				   }

				

				   $cadena="operacion=".$operacion."&action=".$action="&tipo=".$tipo."&ciudad=".$ciudad."&orden=".$orden."&codigo=".$codigo;
				   $this->paginator->estableceIndex("index.php?k=6&".$cadena);	
			    }else{
					
				   		  $this->paginator->estableceIndex("index.php?k=5");
						    if(isset($_GET["m"])){
								 if($_GET["m"]==2){
									$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where papelera=0 and mm_cape_fotos.portada=1 and mm_propiedad.operacion=2 and mm_propiedad.estadoPublicacion=0 GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc ";									
							  	 }else  if($_GET["m"]==1){
									$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where  papelera=0 and mm_cape_fotos.portada=1 and mm_propiedad.operacion=1 and mm_propiedad.estadoPublicacion=0 GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";									
								}else  if($_GET["m"]==6){
									$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where  papelera=0 and mm_cape_fotos.portada=1 and mm_propiedad.operacion=6 and mm_propiedad.estadoPublicacion=0  GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";									
								}
						    }else{
								if($id==2){
									$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where papelera=0 and mm_cape_fotos.portada=1 and mm_propiedad.operacion=2 and mm_propiedad.estadoPublicacion=0  GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc ";									
							  	 }else  if($id==1){
									$sql="select* from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where  papelera=0 and mm_cape_fotos.portada=1 and mm_propiedad.operacion=1  and mm_propiedad.estadoPublicacion=0 GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";									
								}else{
									$sql="select * from mm_propiedad INNER JOIN mm_cape_fotos ON mm_propiedad.idProp = mm_cape_fotos.idProp where mm_cape_fotos.portada=1 and papelera=0 and mm_propiedad.estadoPublicacion=0 GROUP by mm_propiedad.idProp order by mm_propiedad.idProp desc";
								}
								
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
		if(isset($_GET["action1"])){
			echo "<div align='center' style='font-size:16px;margin-top:10px;'>Se han encontrado ".$total." resultados</div>";			
		}
		
		echo "<div class='row'>";
 
		
 

		while($row=$this->paginator->devolverResultados()){			
				$rutaFoto=$row["ruta"];
				$info = getimagesize("./upload/".$rutaFoto);
    			$ancho = $info[0]; // Ancho de la imagen
    			$alto = $info[1]; // Alto de la imagen
		 
		 
			echo '
			<div class="col-md-4">
			<div class="card" style="margin-top:30px;">';
			
			echo '<a href="index.php?mod=det&idProp='.$row["idProp"].'">';
			if($ancho<500){
				echo '<div style="background-color:#eee;" align="center"><img src="./upload/'.$rutaFoto.'" style="width:55%;height:250px;"  class="card-img-top" alt="..."></div>';
			}else{
				echo '<div><img src="./upload/'.$rutaFoto.'" style="width:100%;height:250px;"  class="card-img-top" alt="..."></div>';	
			}
			
			
			echo '<div class="box-estado" align="center" STYLE="color:white !important;font-size:13px;font-weight:bold;">
			<div align="center" style="padding-top:2px;float:left;width:50%;height:26px;position:relative;   background-color: #000 !important;">'.strtoupper($this->devolverOperacion($row["operacion"])).'</div>
			
			</div>

			</a>
				 
				<div class="card-body" style="padding:0px;">
					<h5 class="card-title" style="font-weight: 500;font-size:14px;padding-left:25px; padding-right:25px;padding-top:20px;"><i class="fas fa-map-marker-alt" style="font-size:12px;"></i> '.$this->devolverRegion($row["idRegion"]).' | '.$this->devolverComuna($row["idComuna"]).'</h5>
					<p class="card-text" style="font-size:14px;padding-left:25px;padding-right:25px;color:#5d5d5d;height:50px;"><a href="index.php?mod=det&idProp='.$row["idProp"].'" class="mt1">	'.$row["titulo"].'</a></p>
					
					<ul class="list-group list-group-flush">
			<li class="list-group-item"><div style="float:left;color:#5d5d5d;font-weight:bold;font-size:20px;">';
			if($row["precioUf"]!=1){
				echo "UF ";
			}else{
				echo "$ ";
			}
 
			if($row["precioUf"]!=1){
				echo $this->formatoNumerico($row["precio"]);
			}else{
				echo $this->formatoNumerico($row["precio"]);
			}
			echo '</div></li>
		 
		  </ul>
		  <div style="padding-top:10px; padding-bottom:10px;color:black;font-size:14px !important;" align="center"><img src="https://www.cacciuttolopropiedades.cl/mt2.png" style="width:5%;"/>&nbsp;'.$this->formatoNumerico($row["m2Construido"]).'<sup>&nbsp;Mt2</sup> &nbsp; &nbsp;
		   <i class="fas fa-bed" aria-hidden="true"></i> &nbsp; '.$row["dormitorios"].' &nbsp;&nbsp;<i class="fas fa-bath" aria-hidden="true"></i>&nbsp;  &nbsp;'.$row["banos"].'&nbsp;&nbsp;&nbsp;<i class="fas fa-car-side" aria-hidden="true"></i>&nbsp;&nbsp;'.$row["estacionamiento"].'</div>
				</div>';


				
					$nom=trim($this->devolverCorredora($row["idCorredora"]));
					$ima=$this->devolverImagen($row["idCorredora"]);

					echo '<div class="card-footer" >';
					echo '<div style="display: flex; align-items: center;">';

					if($row["idCorredora"]==0){
						$d=$this->devolverDatosPagina();
						$logo=$d["logo"];
						$emp=$d["nomEmpresa"];
				 
						
						echo '<span style="font-size:14px;">Publicado por: ' . $emp. '</span>';	
					 
					}else{
					if(!empty($ima)){
						echo '<img src="./upload/' . $ima . '" alt="Foto de corredora" style="width: 35px; height: 35px; margin-right: 10px;">';
					}else{
						echo '<img src="https://www.planwebinmobiliario.cl/demoProp/imagen/nouser.jpg" alt="Foto de corredora" style="width: 50px; height: 50px; margin-right: 10px;">';						
					}
						
					
					echo '<span style="font-size:14px;">' . $nom . '</span>';
					}
					echo '</div>';
					echo '</div>';

			echo '</div>
		
			</div>
			';
		 
			
		}
		echo "</div>";
	 
	   
	   }
	   public function devolverDatosPagina(){
		$this->link=$this->conectar();
		$sql="select* from mm_coti_datos";
		$q=mysqli_query($this->link,$sql);
	 
	 
		while($r=mysqli_fetch_array($q)){
			 
		 	$d["nomEmpresa"]=$r["nombreEmpresa"]; 
		 	$d["logo"]=$r["logo"];
		 }
		 return($d);
	}
	public function devolverImagen($id){
		$sql="select * from registro where idReg='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
	 
		$n=$r["rutaFoto"];
		return($n);
	}

	public function devolverCorredora($id){
		$sql="select nomEmpresa,nombre from registro where idReg='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
	 
		if(empty($r["nomEmpresa"])){
			$n=$r["nombre"]."&nbsp;".$r["apellido"];			
		}else{
			$n=$r["nomEmpresa"];
		}
		return($n);
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
	
	public function leerTitulo($id){
		$this->link=$this->conectar();
		$sql="select* from mm_coti_contenido where idContenido='".$id."'";
	 
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
		$row=mysqli_fetch_array($query);
		echo $row["titulo"];
	}
	
	public function formatoNumerico2($num){

		$n=number_format($num, 2,",",".");
		return($n);
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
	
    public function devolverRegion($idRegion){
		$this->link=$this->conectar();
		$sql="select* from mm_region where idRegion='".$idRegion."'";
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
		$row=mysqli_fetch_array($query);	 
		return($row["nombre"]);
	}
	public function devolverComuna($idComuna){
		$this->link=$this->conectar();
		$sql="select* from mm_comuna where idComuna='".$idComuna."'";
		$query=mysqli_query($this->link,$sql) or die(mysql_error($this->link));
		$row=mysqli_fetch_array($query);	 
		return(utf8_decode($row["nombre"]));
	}
	public function buscadorHorizontal(){
		
		$this->link=$this->conectar();
		echo '<div><form method="get" name="form1" id="form1" action="">
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
				<option value="2">Departamentos</option>
				<option value="3">Oficinas</option>
				<option value="4">Parcelas</option>
				<option value="5">Bodegas</option>				
				<option value="6">División Usados</option>	
				<option value="7">Estacionamientos</option>
				<option value="8">Galpón</option>
				<option value="9">Industrial</option>			 
				<option value="10">Terrenos</option>	

				</select>
			</div>
			<div class="col-md-2">
				<select class="form-select" name="region33" id="region33"  style="margin-top:5px;margin-bottom:5px;" aria-label=".form-select-lg example">';
				echo '<option value="0" selected="selected">Región</option>';
				   $sql="select distinct idRegion from mm_propiedad where papelera=0 order by idRegion asc";
				   $q=mysqli_query($this->link,$sql);
				   while($r=mysqli_fetch_array($q)){
					   echo '<option value="'.$r["idRegion"].'">'.$this->devolverRegion($r["idRegion"]).'</option>';
				   }
				   
				echo '</select>
			</div>
			<div class="col-md-2">
				<select name="ciudad33"  style="margin-top:5px;margin-bottom:5px;" id="ciudad33"
				 class="form-select" aria-label=".form-select-lg example">
				<option value="0" selected="selected">Ciudad</option>
				 
				</select>
			</div>
			<div class="col-md-2">
				<select   style="margin-top:5px;margin-bottom:5px;" name="comuna33" id="comuna33"
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
	public function buscador2(){
		
		$this->link=$this->conectar();
		echo '<div><form method="get" name="form1" id="form1" action="">
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
				<option value="2">Departamentos</option>
				<option value="3">Oficinas</option>
				<option value="4">Parcelas</option>
				<option value="5">Bodegas</option>				
				<option value="6">División Usados</option>	
				<option value="7">Estacionamientos</option>
				<option value="8">Galpón</option>
				<option value="9">Industrial</option>			 
				<option value="10">Terrenos</option>	

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
	 
	public function blog(){		
		$sql="select * from agro_blog  order by idBlog desc limit 0,3";
 
		$q1=mysqli_query($this->link,$sql);
	 
		echo "<div class='row'>";
		
		while($r=mysqli_fetch_array($q1)){
			echo "<div class='col-md-4'>";
			echo '<div class="card" style="width: 100%;">';
			echo '<a href="index.php?mod=blog&id='.$r["idBlog"].'" class="fill">';
		   	if(empty($r["imagen"])){
				echo '<img src="./sinFoto.png"  class="img-fluid alt="Sin Foto" class="img-fluid">';
		   	}else{
				echo '<img src="./upload/'.$r["imagen"].'" alt="'.$r["titulo"].'" class="img-fluid">';
		   	}		   
		   echo '</a>';
			echo '<div class="card-body">
			  <h5 class="card-title">';
			  echo $r["titulo"];
			 echo  '</h5>
			  <p class="card-text" style="font-size:14px;">';
			  echo strip_tags(nl2br(substr($r["des"],0,250)));
			 echo '</p>			
			 <a href="index.php?mod=blog&id='.$r["idBlog"].'" class="btn btn-primary2 btn-sm" style="padding-bottom:3px; padding-left:15px;font-size:14px !important; padding-right:15px;padding-top:3px !important;">VER MAS</a>
			</div>
		  </div></div>';		
		}
		echo "</div>";
	}
	public function tituloBlog($id){
		$this->link=$this->conectar();
		$sql="select titulo from agro_blog where idBlog='".$id."'";
		
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		return($r["titulo"]);
	}
	public function listadoBlog(){
		$this->link=$this->conectar();
		$sql="select * from agro_blog order by idBlog desc";
		$q=mysqli_query($this->link,$sql);
		echo "<div class='row'>";
		while($r=mysqli_fetch_array($q)){
			echo "<div class='col-md-4' style='margin-bottom:20px;'>";
			echo '<div class="card" style="width:100%;">';
			if(file_exists("./upload/".$r["imagen"])){
				echo "<div style='margin-top:20px;'><img src='./upload/".$r["imagen"]."' style='width:100%;max-width:100%;'/></div>";
			}
			echo '<div class="card-body">
			  <h5 class="card-title">'.$r["titulo"].'</h5>
			  <p class="card-text">';
			  echo '<p>'.strip_tags(substr(nl2br($r["des"]),0,158)).'....</p>';
			 echo '</p>
			  <a href="index.php?mod=blog&id='.$r["idBlog"].'" class="btn btn-primary">Ver Mas</a>
			</div>
		  </div>';
			echo "</div>";
		}
		echo "</div>";

	}
	public function leerBlog($id){
		$this->link=$this->conectar();
		$sql="select * from agro_blog where idBlog='".$id."' order by idBlog desc";
		
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q); 
		echo '<div class="row">';		
		echo '<div class="col-md-9">';	
		echo "<div style='font-size:14px !important; margin-top:30px;'><i class='fas fa-home'></i>&nbsp;<a href='index.php' style='font-size:14px !important;'>Inicio</a> / ".$r["titulo"]."</div>";
		echo "<div>";
		if(file_exists("./upload/".$r["imagen"])){
			echo "<div style='margin-top:20px;'><img src='./upload/".$r["imagen"]."' style='max-width:100% !important; width:100%;'/></div>";
		}
		echo "</div>";
		echo "<div style='margin-top:30px;'><h4>".$r["titulo"]."</h4></div>";
		echo '<span style="font-size:12px;">'.date("d-m-Y",$r["fecha"]).'</span>';
		echo '<div style="font-size:16px;">'.nl2br($r["des"]).'</div>';
		echo '</div>';
		echo "<div class='col-md-3'>";
		
		echo "</div>";
		echo '</div>';
	   }
	public function rotador(){
		$this->link=$this->conectar();
		$sql="select* from rotator order by idRotator asc";
		$query=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));	
		$i=0;		
		while($row=mysqli_fetch_array($query)){
			$i++;
			if($i==1){
				echo '<div class="carousel-item active">
				<img src="./data1/images/'.$row["imagen"].'" class="d-block w-100" alt="...">
				 
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
		}else if($id==6){
			$k="División Usados";
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
		}else if($id==11){
			$k="Turistico";
		}else if($id==12){
			$k="Parcelas";
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
	 		echo "<div style=' margin-bottom:5px; font-size:14px; color:white;'>INDICADORES : <span STYLE='color:white;font-size:15px;'>UF:</span> <span style='color:white;font-size:15px;'>$".$this->formatoNumerico2($dailyIndicators->uf->valor)."</span>&nbsp;&nbsp;|&nbsp;&nbsp;";
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
		echo "<div style=' margin-bottom:5px; font-size:14px;'>UF: ".$xml->indicador->uf."</div>";
		echo "<div>IPC: ".$xml->indicador->ipc."</div>";
		echo "<div style='font-size:14px;margin-bottom:5px;'> UTM: ".$xml->indicador->utm."</div>"; 
		echo "<div style=' font-size:14px;margin-bottom:5px;'>Dolar: ". $xml->moneda->dolar."</div>";
		echo "<div>Dolar CLP: ".$xml->moneda->dolar_clp."</div>";
		echo "<div style='font-size:14px;margin-bottom:5px;'>Euro: ".$xml->moneda->euro."</div>";
	}  
}  
?> 