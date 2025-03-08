<?php 
ob_start();
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/autoload.php';  
require_once("./clases/class.coneccion.php");
require_once("./clases/class.gMaps.php");
require_once("./clases/class.msgBox.php");
require_once("./clases/class.paginator.php");
require_once("./clases/class.form1.php");
require_once("./clases/class.miniGrid2.php");
require_once("./clases/class.sqlPlus.php");
class publicar extends coneccion{
    public $link;
    public $miForm;
    public $sql;
    public function __construct(){
        $this->link=$this->conectar();   
        $this->miForm=new form();    
        $this->sql=new sql();
    }
    public function devolverRegion($id){

		$sql="select* from mm_region where idRegion='".$id."'";
        
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
	public function devolverComuna($id){
		$sql="select* from mm_comuna where idComuna='".$id."'";	 
		$q=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		$r=mysqli_fetch_array($q); 
		mysqli_free_result($q);
		return($r["nombre"]);
	}
    public function devolverTipo($tipo){
		if($tipo==1){
			$k="Particular";
		}else if($tipo==2){
			$k="Inmobiliaria";
		}else if($tipo==3){
			$k="Corredora";
		}
		return($k);		
	}
    public function editarCuenta(){
	 
		$idReg=$_SESSION["auth"]["idUser"];		
		$sql="select* from registro where idReg='".$idReg."'";		 
		$q=mysqli_query($this->link,$sql);
	
        
 
		echo "<form method='post' enctype='multipart/form-data' name='form1' id='form1'>";
		$_SESSION["auth"]["nick"]=$r["nombre"];
		if(isset($_GET["msg"])){
            echo '<div class="alert alert-primary" role="alert">
		Cambios guardados con exito !!!
		  </div>
		  ';
        }	
        $r=mysqli_fetch_array($q);
 
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
		if(!empty($r["rutaFoto"])){
			echo "<img style='width;100%;' src='./upload/".$r["rutaFoto"]."'/>";
		}else{			
			echo "<img src='https://www.planwebinmobiliario.cl/demoProp/imagen/nouser.jpg'/>";
		}
		echo "</div>";
		echo "</div>";
		echo "<div>";
		$this->miForm->addFile(1,"Foto Perfil :",$editarImagen);
		echo "</div>";
		echo "<div>250px x 250px</div>";
		
 

		echo "</div>";

		echo "</form>";
	 

		$this->miForm->procesar();
		if($this->miForm->procesarImagen(250,150)){
			$arch=$this->miForm->getDataArch();  		               
		}	 	  
        $post=$this->miForm->getDataPost();	 
        if($_POST["action"]){
	 
        	$d["tipo"]=htmlentities($_POST["tipo"]);	
            $d["nombre"]=htmlentities($_POST["nombre"]);	
			$d["nomEmpresa"]=htmlentities($_POST["nomEmpresa"]);	
			$d["apellido"]=htmlentities($_POST["apellido"]);	
			$d["email"]=htmlentities($_POST["email"]);	
			$d["telefono"]=htmlentities($_POST["telefono"]);	
			$d["celular"]=htmlentities($_POST["celular"]);	
			$d["wasap"]=htmlentities($_POST["wasap"]);	
			$d["website"]=htmlentities($_POST["website"]);	
			$d["pass"]=htmlentities($_POST["contra"]);	
			$d["condicion"]=htmlentities($_POST["condiciones"]);	
			$d["w"]=htmlentities($_POST["w"]);	
			$d["region"]=$_POST["region5x"];
			$d["ciudad"]=$_POST["ciudad3"];
			$d["comuna"]=$_POST["comuna3"];

			$d["facebook"]=htmlentities($_POST["facebook"]);	
			$d["twitter"]=htmlentities($_POST["twitter"]);	
			$d["instagram"]=htmlentities($_POST["instagram"]);	
			$d["linkedin"]=htmlentities($_POST["linkedin"]);	

			$d["direccion"]=htmlentities($_POST["addr"]);	
			$cor=$_POST["lat"].",".$_POST["lon"];	
 

 
	 		$sql="update registro set tipo='".$d["tipo"]."',
			 						  nomEmpresa='".$d["nomEmpresa"]."',
									  nombre='".$d["nombre"]."',
									  apellido='".$d["apellido"]."',
									  email='".$d["email"]."',
									  celular='".$d["celular"]."',
									  wasap='".$d["wasap"]."',
									  website='".$d["website"]."',
									  region='".$d["region"]."',
									  ciudad='".$d["ciudad"]."',
									  comuna='".$d["comuna"]."',
									  
									  facebook='".$d["facebook"]."',
									  twitter='".$d["twitter"]."',
									  instagram='".$d["instagram"]."',
									  linkedin='".$d["linkedin"]."',
									  direccion='".$d["direccion"]."',
									  cordenadas='".$cor."',
									  telefono='".$d["telefono"]."' ";
									  if(!empty($_POST["contra"])){
										$sql.=" , pass='".md5($d["contra"])."'";
									  }									 
									  if(!empty($arch[0])){
									  	  $sql.=", rutaFoto='".$arch[0]."'";
									  }
			$sql.=" where idReg='".$idReg."'";
		 
     
			mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
			header("location:panelUser.php?mod=panel&op=2&msg=1");
			exit;
	    }
        $this->miForm->cerrarForm();
		mysqli_free_result($q);
		mysqli_free_result($q1);
	} 
    public function editarPropiedad($id){
		$this->link=$this->conectar();
        
		echo '<script>';
		echo '
		$(document).ready(function(){
		
			$("#tipoProp").change(function(){				
				var tipo=$("#tipoProp").val();
				if(tipo==1){				
					$("#res1").html("<span style=\"font-size:14px;\">Mt2 Totales</span>");
				}else if(tipo==2){
					$("#res1").html("<span style=\"font-size:14px;\">Mt2 Utiles</span>");
				}else{
					$("#res1").html("<span style=\"font-size:14px;\">Mt2 Totales</span>");
			});
			$("#s11").click(function(){
				
				var titulo=$("#titulo").val();
				if(titulo.length==0){
					alert("Ingrese el titulo de la propiedad");
					$("#titulo").focus();
				}else{
					$("#form111").submit();
				}
				 
				return(false);
			});
			return(false);
		});
		';
		echo '</script>';
		$cadena=$this->sql->sqlConsultarUnaVivienda($id);	
		$query=mysqli_query($this->link,$cadena) or die(mysqli_error($this->link));
		$row=mysqli_fetch_array($query); 
	 
 		 $this->gMaps=new miniGmaps();
		$this->gMaps->jqueryMaps();		 
		$this->gMaps->jMaps($row["cordenadas"]); 	
        
        
		echo "<div style='padding-right:20px; padding-left:0px;'>";
	   echo "<form method='post' name='form111' enctype='multipart/form-data' id='form111' action=''>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		 
	   if(isset($_GET["msg"])){
		   $msg=htmlentities($_GET["msg"]);
		   if($msg==11){
                echo '<div class="alert alert-primary" role="alert">
                Cambios se han guardado con exito
              </div>';
		   }
	   }     
       echo "<div style='margin-top:20px;margin-bottom:20px;'>";
       echo "<h5>Editar Propiedad</h5>";
       echo "</div>";
	   echo "<div>Titulo</div>";
	   echo "<div><input type='text' placeholder='Titulo de la propiedad' name='titulo' id='titulo' class='form-control from-control-sm' value='".$row["titulo"]."'/></div>";
	   $arrSel=array(1=>"Venta",2=>"Arriendo",3=>"Arriendo por dias",4=>"Arriendo marzo a diciembre",5=>"Arriendo año corrido",6=>"División Usados");
	   
	   echo "<div class='row'>";
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Operación</div>";
	   echo "<div>";
	   $arrSel=array(1=>"Venta",2=>"Arriendo",3=>"Ventas en verde",4=>"Ventas en blanco",5=>"Ventas entrega inmediata");
	   echo "<select style='width:100%;' name='operacion' id='operacion' class='form-control form-control-mb'>";
	   if($row["operacion"]==0){
		   echo "<option value='0' selected='selected'>Operación</option>";
	   }else{
		echo "<option value='".$row["operacion"]."' selected='selected'>".$arrSel[$row["operacion"]]."</option>";
	   }
	   
	   foreach($arrSel as $c=>$v){
		   echo "<option value='".$c."'>".$v."</option>";
	   }
	   echo "</select>";
	   echo "</div>";
	   /* fin col md-3*/
	   echo "</div>";

	   echo "<div class='col-md-3'>";
	    

	   $arrSel1=array(1=>"Se Vende", 2=>"Se Arrienda",3=>"Arrendada",4=>"Vendido",5=>"Reservada");
	   
	   echo "<div style='margin-top:10px;'>Estado de la propiedad</div>";
	   echo "<div>";
	   echo "<select name='estadoProp' id='estadoProp' class='form-control form-control-mb'>";
	   if($row["estadoProp"]==0){
			echo "<option value='0' selected='selected'>Estado</option>";
		}else{
	 		echo "<option value='".$row["estadoProp"]."' selected='selected'>".$arrSel1[$row["estadoProp"]]."</option>";
		}
	   
	   foreach($arrSel1 as $c1=>$v1){
		   echo "<option value='".$c1."'>".$v1."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	   echo "</div>";


	   echo "<div class='col-md-3'>";

	   $arrSel3=array(1=>"Casas",
	   2=>"departamento",
	3=>"Oficina",
	4=>"Agrícola",
	5=>"Bodega",
	6=>"Comercial",
	7=>"Estacionamiento",
	8=>"Galpón",
	9=>"Industrial",
	10=>"Terreno",
	11=>"Turístico",
	12=>"Parcelas"				
  );
	   echo "<div style='margin-top:10px;'>Tipo de propiedad</div>";
	   echo "<div>";
	   echo "<select name='tipoProp' id='tipoProp' class='form-control form-control-mb'>";

	    if($row["tipoProp"]==0){
			echo "<option value='0' selected='selected'>Tipo de propiedad</option>";
		}else{
			 echo "<option value='".$row["tipoProp"]."' selected='selected'>".$arrSel3[$row["tipoProp"]]."</option>";
		}

	 
	   foreach($arrSel3 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	   echo "</div>";

	   echo "<div class='col-md-3'>";

	   $arrSel21=array("Si"=>"Si","No"=>"No");        
	   echo "<div style='margin-top:10px;'>Destacar</div>";
	   echo "<div>";
	   echo "<select name='destacar' id='destacar' class='form-control form-control-mb'>";

	   if(empty($row["destacar"])){
			echo "<option value='0' selected='selected'>Destacar Propiedad</option>";
		}else{
		 	echo "<option value='".$row["destacar"]."' selected='selected'>".$arrSel21[$row["destacar"]]."</option>";
		}

	 
	   foreach($arrSel21 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";

	   echo "</div>";

	   /*fin row*/
	   echo "</div>";

	   echo "<div class='row'>";
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Región</div>";
	   
	   echo '<select  class="form-control form-control-mb" name="region" id="region"  style="margin-top:5px;margin-bottom:5px;" aria-label=".form-select-lg example">';
	   if($row["idRegion"]!=0){
			echo '<option value="'.$row["idRegion"].'" selected="selected">'.utf8_encode($this->devolverRegion($row["idRegion"])).'</option>';
	   }else{
		echo '<option value="0" selected="selected">Región</option>';
	   }
	   
		  $sql="select* from mm_region order by idRegion asc";
		  $q=mysqli_query($this->link,$sql);
		  while($r=mysqli_fetch_array($q)){
			  echo '<option value="'.$r["idRegion"].'">'.utf8_encode($r["nombre"]).'</option>';
		  }
		  
	   echo '</select>';
	   echo "</div>";
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Ciudad</div>";
	   echo '	<select name="ciudad"  style="margin-top:5px;margin-bottom:5px;" id="ciudad"
	   class="form-control form-control-mb" aria-label=".form-select-lg example">';
	   if($row["idCiudad"]!=0){
			echo '<option value="'.$row["idCiudad"].'" selected="selected">'.$this->devolverCiudad($row["idCiudad"]).'</option>';
   	   }else{
			echo '<option value="0" selected="selected">Ciudad</option>';
   	   }
	  
	   
	  echo '</select>';
	   echo "</div>";
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Comuna</div>";
	   echo '<select   style="margin-top:5px;margin-bottom:5px;" name="comuna" id="comuna"
	   class="form-control form-control-mb" aria-label=".form-select-lg example">';
	   if($row["idComuna"]!=0){
		echo '<option value="'.$row["idComuna"].'" selected="selected">'.$this->devolverComuna($row["idComuna"]).'</option>';
	  }else{
		echo '<option value="0" selected="selected">Comuna</option>';
	  }
	   
	   
	  echo '</select>';
	   echo "</div>";
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'><div name='res1' id='res1'>";
 
	   if($row["tipoProp"]==1){
		echo "Mt2 Totales";
	   }else if($row["tipoProp"]==2){
		echo "Mt2 Utiles";
	   } 
		 echo "</div></div>";
	   echo "<div><input type='text' placeholder='Mt2 Totales' value='".$row["mt2Totales"]."' name='m2Totales' id='m2Totales' class='form-control from-control-mb'/></div>";
	
	   
	   echo "</div>";
	   echo "</div>";

	   echo "<div class='row'>";
	 

	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Mt2 Construidos</div>";
	   echo "<div><input type='text' placeholder='Mt2 Construidos' value='".$row["m2Construido"]."' name='m2Construidos' id='m2Construidos' class='form-control from-control-mb'/></div>";
	 
	   
	   echo "</div>";

	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Precio</div>";
	   echo "<div><input type='text' placeholder='Precio' value='".$row["precio"]."' name='precio' id='precio' class='form-control from-control-mb'/></div>";

	   
	   echo "</div>";

	   echo "<div class='col-md-3'>";


	   $arrSel12=array("1"=>"Pesos","2"=>"U.F.");
	   echo "<div style='margin-top:10px;'>Precio en:</div>";
	   echo "<div>";
	   echo "<select name='precioUf' id='precioUf' placeholder='Precio en' class='form-control form-control-mb'>";
	   
	   if($row["precioUf"]==0){
		echo "<option value='0' selected='selected'>Seleccione UF/Peso</option>";
		}else{
		 	echo "<option value='".$row["precioUf"]."' selected='selected'>".$arrSel12[$row["precioUf"]]."</option>";
		}
	   
	   foreach($arrSel12 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	   echo "</div>";

	   echo "<div class='col-md-3'>";

	   $arrSel1=array("Si"=>"Si","No"=>"No");
	   echo "<div style='margin-top:10px;'>Piscina</div>";
	   echo "<div>";
	   echo "<select name='piscina' id='piscina' class='form-control form-control-mb'>";

	   if($row["piscina"]==0){
		echo "<option value='0' selected='selected'>Piscina</option>";
		}else{
		 	echo "<option value='".$row["piscina"]."' selected='selected'>".$arrSel1[$row["piscina"]]."</option>";
		}

	   
	   foreach($arrSel1 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";

	   
	   echo "</div>";
	   /*fin row */
	   echo "</div>";

	
	   echo "<div class='row'>";



	   echo "<div class='col-md-3'>";

	   $arrSel1=array("Si"=>"Si","No"=>"No");
	   echo "<div style='margin-top:10px;'>Bodega</div>";
	   echo "<div>";
	   echo "<select name='bodega' id='bodega' class='form-control form-control-mb'>";
	   if($row["bodega"]==0){
		echo "<option value='0' selected='selected'>Bodega</option>";
		}else{
		 	echo "<option value='".$row["bodega"]."' selected='selected'>".$arrSel1[$row["bodega"]]."</option>";
		}

	  
	   foreach($arrSel1 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	   
	   echo "</div>";


	   echo "<div class='col-md-3'>";

	   $arrSel1=array("Si"=>"Si","No"=>"No");
	   echo "<div style='margin-top:10px;'>Logia</div>";
	   echo "<div>";
	   echo "<select name='logia' id='logia' class='form-control form-control-mb'>";
	   if($row["logia"]==0){
		echo "<option value='0' selected='selected'>Logia</option>";
		}else{
		 	echo "<option value='".$row["logia"]."' selected='selected'>".$arrSel1[$row["logia"]]."</option>";
		}
	   
	   foreach($arrSel1 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";

	   
	   echo "</div>";


	   echo "<div class='col-md-3'>";
	   
	   echo "<div style='margin-top:10px;'>Estacionamientos</div>";
	 
	 
	   $arrSel12=array(1=>"1 estacionamiento",2=>"2 o mas");
	   echo "<div>";
	   echo "<select name='numEstacionamientos' id='numEstacionamientos' class='form-control form-control-mb'>";
	   if($row["estacionamiento"]!=0){
			echo "<option value='".$row["estacionamiento"]."' selected='selected'>".$this->devolverEstacionamientos($row["estacionamiento"])."</option>";
	   }else{
			echo "<option value='0' selected='selected'>Estacionamientos</option>";
	   }	   
	   foreach($arrSel12 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";
	   
	  
	  
	 
	   echo "</div>";
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Numero de dormitorios</div>";
	   echo "<div><input type='text' placeholder='Numero de dormitorios' value='".$row["dormitorios"]."' name='numDor' id='numDor' class='form-control from-control-mb'/></div>";
	   echo "</div>";
	  
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Cocina</div>";
	   
	   $arrSel12=array("Si"=>"Si","No"=>"No");
	   echo "<div>";
	   echo "<select name='cocina' id='cocina' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Cocina</option>";
	   if(!empty($row["cocina"])){
		   echo "<option value='".$row["cocina"]."' selected='selected'>".$row["cocina"]."</option>";
	   }
	   foreach($arrSel12 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	  
	   echo "</div>";

	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Tipo de cocina</div>";
	   
	   $arrSel122=array(1=>"Cerrada",2=>"Americana",3=>"Integrada");
	   echo "<div>";
	   echo "<select name='tipoCocina' id='tipoCocina' class='form-control form-control-mb'>";
	   if($row["tipoCocina"]!=0){
			echo "<option value='".$row["tipoCocina"]."' selected='selected'>".$this->devolverTipoCocina($row["tipoCocina"])."</option>";
	   }else{
			echo "<option value='0' selected='selected'>Tipo de Cocina</option>";
	   }
	   

	   foreach($arrSel122 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	  
	   echo "</div>";

	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Numero de baños</div>";
	   echo "<div><input type='text' placeholder='Baños' name='numBanos' value='".$row["banos"]."' id='numBanos' class='form-control from-control-mb'/></div>";
	
	   echo "</div>";

	   $arrSel122a=array("Si"=>"Si","No"=>"No");
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Conserjería</div>";
	   echo "<select name='conser' id='conser' class='form-control form-control-mb'>";
	   if(!empty($row["conser"])){
		echo "<option value='0' selected='selected'>".$row["conser"]."</option>";
	   }else{
		echo "<option value='0' selected='selected'>Conserjería</option>";
	   }
	   
	   foreach($arrSel122a as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	
	   echo "</div>";
	   /*fin row */
	   echo "</div>";
	  
	   echo "<div class='row'>";
	 
	   $arrSel122a=array("Si"=>"Si","No"=>"No");
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Quincho</div>";
	   echo "<select name='quincho' id='quincho' class='form-control form-control-mb'>";
	   if(!empty($row["quincho"])){
			echo "<option value='".$row["quincho"]."' selected='selected'>".$row["quincho"]."</option>";
	   }else{
	   		echo "<option value='0' selected='selected'>Quincho</option>";
	   }
	   foreach($arrSel122a as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	
	   echo "</div>";

	   $arrSel122a=array("Si"=>"Si","No"=>"No");
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Áreas comunes</div>";
	   echo "<select name='areasComunes' id='areasComunes' class='form-control form-control-mb'>";
	   if(!empty($row["areasComunes"])){
		echo "<option value='".$row["areasComunes"]."' selected='selected'>".$row["areasComunes"]."</option>";
	   }else{
	   		echo "<option value='0' selected='selected'>Áreas comunes</option>";
	   }
	   foreach($arrSel122a as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	
	   echo "</div>";


		/*fin del row */
	   echo "</div>";

	    


	   echo "<div class='row'>";
	   echo "<div class='col-md-12'>";

	   echo "<div style='margin-top:10px;'>Dirección</div>";
	   echo "<div>";
	   
	   echo '<input type="text"  style="margin-left:0px;margin-top:2px;padding-left:10px; padding-right:10px; width:100%; margin-bottom:2px;" class="form-control input-sm"  maxlength="100" id="address" name="address"  value="'.$row["direccionProp"].'" placeholder="Direcci�n" /></div> 
 <input type="button"  style="padding-left:10px; padding-right:10px;" id="search" value="Buscar"  />(ej:republica 400)';
	   echo "</div>";
	  
	   
	   echo "<div style='margin-left:10px;margin-top:10px;' >Mapa de ubicación</div>";
		echo '<input type="hidden" name="cordenada" id="cordenada" value="'.$row["cordenadas"].'"/>
		<div id="map_canvas" style="margin-left:20px; margin-top:5px; margin-bottom:5px; width:100%; height:200px;"></div>';
	   echo "</div>";

	   
	   echo "</div></div>";
	 
	    echo "<div style='margin-top:10px;'>Descripción</div>";
	   echo "<div>";
	   echo ' <textarea name="des" class="form-control form-control-sm" cols="10" rows="10">'.$row["descripcion"].'</textarea>';	   

	   echo "</textarea>";
	   echo "</div>";

       echo "<div style='margin-top:10px;'>Fotos</div>";
	   echo "<div>";
       $idq=$_GET["idq"];
       $sql4="select* from mm_cape_fotos where idProp='".$idq."' order by portada desc";     
       $q4=mysqli_query($this->link,$sql4);
       
 
        echo "<div class='row'>";
        $s=0;
        while($r4=mysqli_fetch_array($q4)){
            echo "<div class='col-md-3'>";
            echo "<div class='card' style='margin-bottom:10px;'>";
            echo "<img src='./upload/".$r4["ruta"]."' style='width:100%;'/>";
            echo "<table width='100%' border='0'>";
            echo "<tr>";
            echo "<td>";
            echo '<input type="checkbox" name="opcion[]" id="opcion[]" value="'.$r4["idFoto"].'"/> Borrar';
            echo "</td>";
            echo "<td>";
            if($r4["portada"]==1){
                echo "<input type='radio' checked='checked' onclick='k();' name='portada[]' id='portada[]' value='".$r4["idFoto"]."'/>En Portada";
            }else{
                if($s==0){
                    echo "<input type='radio' onclick='k();' checked='checked' name='portada[]' id='portada[]' value='".$r4["idFoto"]."'/> En Portada";
                }else{
                    echo "<input type='radio' onclick='k();' name='portada[]' id='portada[]' value='".$r4["idFoto"]."'/> En Portada";
                }
                
            }
            echo "</td>";
            echo "</tr>";
            echo "</table>";
            echo "</div>";
            echo "</div>";
            $s++;
        }
        echo "</div>";
       echo "</div>";

       echo "<div style='margin-top:10px;margin-bottom:5px;'>Subir Imagen </div>";
       for($i=0; $i<=10; $i++){
        $this->miForm->addFileMultiple(3,"Foto Perfil :",$editarImagen);
       }
       

	   echo "<div><input type='hidden' name='action' id='action' value='true' class='form-control form-control-mb'/></div>";
	   echo "<div style='margin-top:10px;'><button role='button' id='s11' name='s11' class='btn btn-primary btn-sm'>Guardar Cambios</button></div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
		echo "</div>";
		echo "</div>";
		echo "</form>";
        $this->miForm->procesar();
		if($this->miForm->procesarMultiple(550,550)){
			$arch=$this->miForm->getDataArch();  		               
		}	 	

		if(isset($_POST["action"])){	         	
                        if(isset($_POST["opcion"])){                         
                            $lista = implode(",", $_POST["opcion"]);                            
                            $sql7 = "DELETE FROM mm_cape_fotos WHERE idFoto IN (".$lista.")";                           
                            mysqli_query($this->link,$sql7);
                        }

                         if(isset($_POST["portada"][0])){
			                    $idFoto=$_POST["portada"][0];
			                    $sql2="update mm_cape_fotos set portada=0 where idProp='".$id."'";
			                    mysqli_query($this->link,$sql2) or die(mysqli_error($this->link));                                
                    			$sql3="update mm_cape_fotos set portada=1 where idFoto='".$idFoto."'";                                
			                  mysqli_query($this->link,$sql3) or die(mysqli_error($this->link));
		                }
                        
                   
			if(empty($_POST["cordenada"]) || $_POST["cordenada"]==""){			
				$c=$row["cordenadas"];
 			}else{
 				$c1=$_POST["cordenada"];
				$c=substr($c1,0);
				$c=substr($c1,1,-1);
			}
	 
			$sql=$this->sql->sqlModificarVivienda($_POST,$id,$c);
		 
 
		 	if(mysqli_query($this->link,$sql)){
                foreach($arch as $c=>$v){
                    $sql55="insert into mm_cape_fotos(idProp,ruta,portada) values ('".$idq."','".$v."','1');";                    
                    mysqli_query($this->link,$sql55);
                }
            }
       
			 if(isset($_GET["idq"])){
				$idq=htmlentities($_GET["idq"]);
			}	
	 	     header("location:panelUser.php?mod=panel&mq=editar&op=5&idq=".$idq."&msg=11");
			 exit;		 	
		}
	}
    public function devolverTipoCocina($id){
		$arrSel122=array(1=>"Cerrada",2=>"Americana",3=>"Integrada");
		return($arrSel122[$id]);
	}
    public function devolverEstacionamientos($id){
		$arrSel12=array(1=>"1 estacionamiento",2=>"2 o mas");
		return($arrSel12[$id]);
	}

    public function ingresarPropiedad(){		
		$this->link=$this->conectar();	 
		echo '<script>';
		echo '
		$(document).ready(function(){
           
			$("#tipoProp").change(function(){
				var tipo=$("#tipoProp").val();
				if(tipo==1){
				
					$("#res1").html("<span style=\"font-size:14px;\">Mt2 Totales</span>");
				}else if(tipo==2){
					$("#res1").html("<span style=\"font-size:14px;\">Mt2 Utiles</span>");
				}else{
					$("#res1").html("<span style=\"font-size:14px;\">Mt2 Totales</span>");
				}
			});
			$("#s1").click(function(){
				var titulo=$("#titulo").val();
				if(titulo.length==0){
					alert("Ingrese el titulo de la propiedad");
					$("#titulo").focus();
				}else{
					$("#form1").submit();
				}
				 
				return(false);
			});
			return(false);
		});
		';
		echo '</script>';

		echo "<div style='padding-right:20px; padding-left:0px;'>";
        
		echo "<form method='post' name='form1' enctype='multipart/form-data' id='form1' action=''>";
        
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
        echo "<div style='margin-top:20px;padding-bottom:20px;'>";
        echo "<h5>Publicar Propiedad</h5>";
        echo "</div>";
       
		$this->gMaps=new miniGmaps();
		$this->gMaps->jqueryMaps();
 	   
	   if(isset($_GET["msg"])){
		   $msg=htmlentities($_GET["msg"]);
		   if($msg==1){
			echo '<div class="alert alert-primary" role="alert">
			La propiedad ha sido ingresada con éxito. Actualmente se encuentra en estado de revisión y será activada en las próximas horas para su visualización en la portada.
		  </div>';
	
		   }
	   }     
	   echo "<div>Titulo</div>";
	   echo "<div><input type='text' placeholder='Titulo de la propiedad' name='titulo' id='titulo' class='form-control from-control-sm'/></div>";
	   $arrSel=array(1=>"Venta",2=>"Arriendo",3=>"Arriendo por dias",4=>"Arriendo marzo a diciembre",5=>"Arriendo año corrido",6=>"División Usados");
	   
	   echo "<div class='row'>";
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Operación</div>";
	   echo "<div>";
	   echo "<select style='width:100%;' name='operacion' id='operacion' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Seleccione operación</option>";
	   foreach($arrSel as $c=>$v){
		   echo "<option value='".$c."'>".$v."</option>";
	   }
	   echo "</select>";
	   echo "</div>";
	   /* fin col md-3*/
	   echo "</div>";

	   echo "<div class='col-md-3'>";

	   $arrSel1=array(1=>"Se Vende", 2=>"Se Arrienda",3=>"Arrendada",4=>"Vendido",5=>"Reservada");
	   echo "<div style='margin-top:10px;'>Estado de la propiedad</div>";
	   echo "<div>";
	   echo "<select name='estadoProp' id='estadoProp' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Estado</option>";
	   foreach($arrSel1 as $c1=>$v1){
		   echo "<option value='".$c1."'>".$v1."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	   echo "</div>";


	   echo "<div class='col-md-3'>";
	    
	   $arrSel3=array(1=>"Casas",
	   				  2=>"departamento",
					  3=>"Oficina",
					  4=>"Agrícola",
					  5=>"Bodega",
					  6=>"Comercial",
					  7=>"Estacionamiento",
					  8=>"Galpón",
					  9=>"Industrial",
					  10=>"Terreno",
					  11=>"Turístico",
					  12=>"Parcelas"					
					);
	   echo "<div style='margin-top:10px;'>Tipo de propiedad</div>";
	   echo "<div>";
	   echo "<select name='tipoProp' id='tipoProp' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Tipo de propiedad</option>";
	   foreach($arrSel3 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	   echo "</div>";

	   echo "<div class='col-md-3'>";

	   $arrSel21=array("Si"=>"Si","No"=>"No");        
	   echo "<div style='margin-top:10px;'>Destacar</div>";
	   echo "<div>";
	   echo "<select name='destacar' id='destacar' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Destacar Propiedad</option>";
	   foreach($arrSel21 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";

	   echo "</div>";

	   /*fin row*/
	   echo "</div>";

	   echo "<div class='row'>";

	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Región</div>";
	   echo '<select  class="form-control form-control-mb" name="region" id="region"  style="margin-top:5px;margin-bottom:5px;" aria-label=".form-select-lg example">';
	   echo '<option value="0" selected="selected">Región</option>';
		  $sql="select* from mm_region order by idRegion asc";
		  $q=mysqli_query($this->link,$sql);
		  while($r=mysqli_fetch_array($q)){
			  echo '<option value="'.$r["idRegion"].'">'.$r["nombre"].'</option>';
		  }
		  
	   echo '</select>';
	   echo "</div>";
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Ciudad</div>";
	   echo '	<select name="ciudad"  style="margin-top:5px;margin-bottom:5px;" id="ciudad"
	   class="form-control form-control-mb" aria-label=".form-select-lg example">
	  <option value="0" selected="selected">Ciudad</option>
	   
	  </select>';
	   echo "</div>";
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Comuna</div>";
	   echo '<select   style="margin-top:5px;margin-bottom:5px;" name="comuna" id="comuna"
	   class="form-control form-control-mb" aria-label=".form-select-lg example">
	  <option value="0" selected="selected">Comuna</option>
	   
	  </select>';
	   echo "</div>";

	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'><div id='res1' name='res1'>Mt2 Totales</div></div>";
	   echo "<div><input type='text' placeholder='Mt2 Totales' value='0' name='m2Totales' id='m2Totales' class='form-control from-control-mb'/></div>";
	
	   
	   echo "</div>";
	   echo "</div>";

	   echo "<div class='row'>";
	   
	 

	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Mt2 Construidos</div>";
	   echo "<div><input type='text' placeholder='Mt2 Construidos' value='0' name='m2Construidos' id='m2Construidos' class='form-control from-control-mb'/></div>";
	 
	   
	   echo "</div>";

	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Precio</div>";
	   echo "<div><input type='text' placeholder='Precio' value='0' name='precio' id='precio' class='form-control from-control-mb'/></div>";

	   
	   echo "</div>";
	   echo "<div class='col-md-3'>";


	   $arrSel12=array("1"=>"Pesos","2"=>"U.F.");
	   echo "<div style='margin-top:10px;'>Precio en:</div>";
	   echo "<div>";
	   echo "<select name='precioUf' id='precioUf' placeholder='Precio en' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Seleccione UF/Peso</option>";
	   foreach($arrSel12 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	   echo "</div>";

	   echo "<div class='col-md-3'>";

	   $arrSel1=array("Si"=>"Si","No"=>"No");
	   echo "<div style='margin-top:10px;'>Piscina</div>";
	   echo "<div>";
	   echo "<select name='piscina' id='piscina' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Piscina</option>";
	   foreach($arrSel1 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";

	   
	   echo "</div>";
	   /*fin row */
	   echo "</div>";

	
	   echo "<div class='row'>";

	  


	   echo "<div class='col-md-3'>";

	   $arrSel1=array("Si"=>"Si","No"=>"No");
	   echo "<div style='margin-top:10px;'>Bodega</div>";
	   echo "<div>";
	   echo "<select name='bodega' id='bodega' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Bodega</option>";
	   foreach($arrSel1 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	   
	   echo "</div>";


	   echo "<div class='col-md-3'>";

	   $arrSel1=array("Si"=>"Si","No"=>"No");
	   echo "<div style='margin-top:10px;'>Logia</div>";
	   echo "<div>";
	   echo "<select name='logia' id='logia' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Logia</option>";
	   foreach($arrSel1 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";

	   
	   echo "</div>";
	   echo "<div class='col-md-3'>";
	   
	   echo "<div style='margin-top:10px;'>Estacionamientos</div>";
	 
	 
	   $arrSel12=array(1=>"1 estacionamiento",2=>"2 o mas",3=>"Si",4=>"No");
	   echo "<div>";
	   echo "<select name='numEstacionamientos' id='numEstacionamientos' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Estacionamientos</option>";
	   foreach($arrSel12 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";
	   
	  
	  
	 
	   echo "</div>";
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Numero de dormitorios</div>";
	   echo "<div><input type='text' placeholder='Numero de dormitorios' value='0' name='numDor' id='numDor' class='form-control from-control-mb'/></div>";
	   echo "</div>";



	   /*fin row */
	   echo "</div>";
	   
	   
	   echo "<div class='row'>";

	  
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Cocina</div>";
	   
	   $arrSel12=array("Si"=>"Si","No"=>"No");
	   echo "<div>";
	   echo "<select name='cocina' id='cocina' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Cocina</option>";
	   foreach($arrSel12 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	  
	   echo "</div>";

	   	  
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Tipo de cocina</div>";
	   
	   $arrSel122=array(1=>"Cerrada",2=>"Americana",3=>"Integrada");
	   echo "<div>";
	   echo "<select name='tipoCocina' id='tipoCocina' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Tipo de Cocina</option>";
	   foreach($arrSel122 as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	   echo "</div>";


	  
	   echo "</div>";



	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Numero de baños</div>";
	   echo "<div><input type='text' placeholder='Baños' name='numBanos' value='0' id='numBanos' class='form-control from-control-mb'/></div>";
	
	   echo "</div>";
	   $arrSel122a=array("Si"=>"Si","No"=>"No");
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Conserjería</div>";
	   echo "<select name='conser' id='conser' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Conserjería</option>";
	   foreach($arrSel122a as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	
	   echo "</div>";

	   /* fin de row*/
	   echo "</div>";

	   echo "<div class='row'>";
	 
	   $arrSel122a=array("Si"=>"Si","No"=>"No");
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Quincho</div>";
	   echo "<select name='quincho' id='quincho' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Quincho</option>";
	   foreach($arrSel122a as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	
	   echo "</div>";

	   $arrSel122a=array("Si"=>"Si","No"=>"No");
	   echo "<div class='col-md-3'>";
	   echo "<div style='margin-top:10px;'>Áreas comunes</div>";
	   echo "<select name='areasComunes' id='areasComunes' class='form-control form-control-mb'>";
	   echo "<option value='0' selected='selected'>Áreas comunes</option>";
	   foreach($arrSel122a as $c2=>$v2){
		   echo "<option value='".$c2."'>".$v2."</option>";
	   }
	   echo "</select>";
	
	   echo "</div>";


		/*fin del row */
	   echo "</div>";

	  

	   echo "<div class='row'>";
	   echo "<div class='col-md-12'>";

	   echo "<div style='margin-top:10px;'>Dirección</div>";
	   echo "<div>";
	   
	   echo '<input type="text"  style="margin-left:0px;margin-top:2px;padding-left:10px; padding-right:10px; width:100%; margin-bottom:2px;" class="form-control input-sm"  maxlength="100" id="address" name="address"  value="republica 799" placeholder="Direcci�n" /></div> 
 <input type="button"  style="padding-left:10px; padding-right:10px;" id="search" value="Buscar"  />(ej:republica 400)';
	   echo "</div>";
	   echo "<div style='margin-top:10px;' >Mapa de ubicación</div>";
		echo '<input type="hidden" name="cordenada" id="cordenada" value="'.$row["cordenadas"].'"/>
		<div id="map_canvas" style="margin-left:10px; margin-top:5px; margin-bottom:5px; width:100%; height:200px;"></div>';
	   echo "</div>";

	   
	   echo "</div></div>";
	 
	    echo "<div style='margin-top:10px;'>Descripción</div>";
	   echo "<div>";
	   echo ' <textarea name="des" class="form-control form-control-sm" style="width:100%;" rows="8"></textarea>';
	 

	   echo "</textarea>";
	   echo "</div>";
       echo "<div style='margin-top:10px;margin-bottom:5px;'>Subir Imagen </div>";
       for($i=0; $i<=10; $i++){
        $this->miForm->addFileMultiple(3,"Foto Perfil :",$editarImagen);
       }
       

	   echo "<div><input type='hidden' name='action' id='action' value='true' class='form-control form-control-mb'/></div>";
	   echo "<div style='margin-top:10px;'><button role='button' id='s1' name='s1' class='btn btn-primary btn-sm'>Agregar Propiedad</button></div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
		echo "</div>";
		echo "</div>";
		echo "</form>";
        $this->miForm->procesar();
		if($this->miForm->procesarMultiple(550,550)){
			$arch=$this->miForm->getDataArch();  		               
		}	 	  

		if(isset($_POST["action"])){
			$sql = $this->sql->sqlIngresarVivienda($_POST);
			
			
            if(mysqli_query($this->link, $sql)) {
                    $idRegistroIng = mysqli_insert_id($this->link);
                    
                    foreach($arch as $c=>$v){
                        $sql="insert into mm_cape_fotos (idProp,ruta,portada) values ('".$idRegistroIng."','".$v."','1')";            
                        mysqli_query($this->link,$sql);               
                    }                    
					$this->enviarNotificacion('mabenite@gmail.com', 'Nueva publicación clickcorredores.cl');
            }
		    header("location:panelUser.php?mod=panel&op=1&msg=1");			 
			exit;		 	
			
		}
	}


public function enviarNotificacion($destinatario, $nombreDestinatario) {
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host       = 'mail.clickcorredores.cl'; // Cambia esto por el servidor SMTP que vayas a usar
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@clickcorredores.cl'; // Tu correo electrónico
        $mail->Password   = 'MHSAD=AcDaRX'; // Tu contraseña
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // O PHPMailer::ENCRYPTION_SMTPS para SSL
        $mail->Port       = 587; // O 465 para SSL
		$mail->SMTPDebug = 0;
        // Destinatarios
        $mail->setFrom('noreply@clickcorredores.cl', 'ClickCorredores.cl');
        $mail->addAddress($destinatario, $nombreDestinatario); // Agrega el destinatario

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = 'Nueva propiedad publicada clickcorredores.cl';
        $mail->Body    = '¡Hola!<br><br>Se ha publicado una nueva propiedad en el sitio. Revisa los detalles en tu panel de administración.<br><br>Saludos,<br>ClickCorredores.cl';
        $mail->AltBody = '¡Hola! Se ha publicado una nueva propiedad en el sitio. Revisa los detalles en tu panel de administración. Saludos, Clickcorredores.cl';

        $mail->send();
       return(true);	 
    } catch (Exception $e) {
        return(false);
    }
}
    public function loginUser(){
    
        if(isset($_POST["action"])){
            $email = htmlentities($_POST['email']);
            $pass = htmlentities($_POST['pass']);
            $query = "SELECT * FROM registro WHERE email = '$email' AND pass = '$pass'";
            $result = mysqli_query($this->link, $query);
            if(mysqli_num_rows($result) > 0) {
                $r=mysqli_fetch_array($result);
        
                $_SESION["auth"]["user"]="usuario";
                $_SESSION["auth"]["idUser"]=$r["idReg"];
                $_SESSION["auth"]["foto"]=$r["rutaFoto"];
                $_SESSION["auth"]["nombre"]=$r["nombre"];
                $_SESSION["auth"]["email"]=$r["email"];
                  header("Location: panelUser.php");
                  exit;
            } else {
              echo '<script>
                alert("Usuario o contraseña no valido, intente nuevamente");
                window.location.href = "loginUser.php";
                    </script>';
                exit;
            }

        }
        echo "<div>";
        echo "<form method='post' name='form1' id='form1' action=''>";
        echo '<div class="card shadow" id="sombra">
        <div class="card-header" style="font-size: 18px;">Iniciar sesión a su Cuenta de usuario</div>
        <div class="card-body" style="margin-top: 20px;">
          <div class="mb-3">
            <div class="input-group">
              <span class="input-group-text" id="basic-addon1"><i class="fas fa-envelope" aria-hidden="true"></i></span>
              <input type="text" class="form-control" name="email" id="email" placeholder="Email" aria-label="Email" aria-describedby="Email">
            </div>
          </div>
          <div class="mb-3">
            <div class="input-group">
              <span class="input-group-text" id="basic-addon1"><i class="fas fa-key" aria-hidden="true"></i></span>
              <input type="password" class="form-control" name="pass" id="pass" placeholder="Contraseña" aria-label="Contraseña" aria-describedby="Contraseña">
            </div>
          </div>
          <input type="hidden" name="action" id="action" value="true">
          <button class="btn btn-danger" id="login" name="login" style="width: 100%;">Ingresar</button>
          
          <div style="margin-top: 20px;">¿No tienes cuenta? <a href="index.php?mod=crearCuenta" class="rec" id="rec" name="rec">Registrarse acá</a></div>
          <div style="margin-top: 10px;"><a href="index.php?mod=rec" class="rec" id="rec" name="rec">¿Necesitas recuperar tu contraseña?</a></div>
        </div>
      </div>
      ';
        echo "</div>";
        echo "</form>";
    }
	public function recuperarContrasena() {
		if (isset($_POST["action"])) {
			$email = htmlentities($_POST['email']);
			
			// Verificar si el email existe en la base de datos
			$query = "SELECT * FROM registro WHERE email = '$email'";
			$result = mysqli_query($this->link, $query);
			
			if (mysqli_num_rows($result) > 0) {
				// Generar una nueva contraseña
				$newPassword = bin2hex(random_bytes(4)); // Generar una contraseña aleatoria de 8 caracteres
				
				$fecha=strtotime(date("d-m-Y H:i:s"));
				$updateQuery = "UPDATE registro SET fecha='$fecha', pass = '$newPassword' WHERE email = '$email'";
				mysqli_query($this->link, $updateQuery);
				
				// Enviar correo con la nueva contraseña
				$subject = "Recuperación de Contraseña";
				$message = "Tu nueva contraseña es: $newPassword\nPor favor, cámbiala después de iniciar sesión.";
				$headers = "From: no-reply@tudominio.com\r\n"; // Cambia esto a tu dominio
				
				if (mail($email, $subject, $message, $headers)) {
					echo '<script>
						alert("Se ha enviado un correo con la nueva contraseña, si no se encuentra en la bandeja de entrada revisar el correo en la carpeta spam.");
						window.location.href = "recuperarContrasena.php";
					</script>';
				} else {
					echo '<script>
						alert("Error al enviar el correo. Inténtalo de nuevo más tarde.");
						window.location.href = "recuperarContrasena.php";
					</script>';
				}
			} else {
				echo '<script>
					alert("El correo ingresado no está registrado.");
					window.location.href = "recuperarContrasena.php";
				</script>';
			}
			exit;
		}
	
		echo "<div>";
		echo "<form method='post' name='formRecover' id='formRecover' action=''>";
		echo '<div class="card shadow" id="sombra">
			<div class="card-header" style="font-size: 18px;">Recuperar Contraseña</div>
			<div class="card-body" style="margin-top: 20px;">
				<div class="mb-3">
					<div class="input-group">
						<span class="input-group-text" id="basic-addon1"><i class="fas fa-envelope" aria-hidden="true"></i></span>
						<input type="text" class="form-control" name="email" id="email" placeholder="Email" aria-label="Email" aria-describedby="Email">
					</div>
				</div>
				<div class="g-recaptcha" data-sitekey="6LdRq_ImAAAAAEUYVVKhD2cl-yKuVlJOy1FJIy2q" data-callback="correctCaptcha"></div>
				<input type="hidden" name="action" id="action" value="true">
				<button role="button" type="button" class="btn btn-danger" id="recover" name="recover" style="width: 100%;">Enviar Enlace de Recuperación</button>
				
				<div style="margin-top: 20px;">¿Ya tienes una cuenta? <a href="loginUser.php" class="rec" id="rec" name="rec">Iniciar sesión acá</a></div>
			</div>
		</div>';
		echo "</div>";
		echo "</form>";
	}
	
	
  
	public function totalArriendos($id){
		$this->link=$this->conectar();
		$sql="select count(*) as total from mm_propiedad where idCorredora='".$id."' and operacion!=1";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		echo $this->formatoNumerico($total);	
	}
	public function totalVentas($id){
		$this->link=$this->conectar();
		$sql="select count(*) as total from mm_propiedad where idCorredora='".$id."' and operacion=1";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		echo $this->formatoNumerico($total);	
	}
    public function formatoNumerico($num){
		$n=number_format($num, 0,",",".");
		return($n);
	}
   
 
	public function totalProp($id){
		$this->link=$this->conectar();
		$sql="select count(*) as total from mm_propiedad where idCorredora='".$id."' and papelera=0";
        
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
        
		$total=$r["total"];
		echo $this->formatoNumerico($total);
		 
	}
	public function modificarPass(){
        if(isset($_POST["action"])){
            $currentPassword = $_POST["currentPassword"];
            $newPassword = $_POST["newPassword"];
            $idUser=$_SESSION["auth"]["idUser"];
            $sql="update registro set pass='".$newPassword."' where idReg='".$idUser."'";            
            mysqli_query($this->link, $sql);
            header("location:panelUser.php?mod=panel&op=3&msg=1");
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
	 

		  
        if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
		  if($op=="agregar") {
			$id=htmlentities($_GET["idq"]);   			
			$this->agregarFotos($id);
		}else if($op=="agregarAws"){			  	 
			$id=htmlentities($_GET["idq"]);    						
			$this->agregarFotosAws($id);
		}else if($op=="agregarFtp"){
			  	 
			$id=htmlentities($_GET["idq"]);    						
			$this->agregarFotosFtp($id,100,100);
		}else if($op=="editar"){
            $id=htmlentities($_GET["idq"]);   
            //$this->miAvisos->modificarCarroEmpresa($id);
            $this->modificarPropiedad($id);
        }else if($op=="borrar"){
         
            $id=htmlentities($_GET["idq"]);            
            $cad=$this->sql->sqlEliminarVivienda($id);
		    $cad1="select* from mm_cape_fotos where idProp='".$id."'";
     
            mysqli_query($this->link,$cad) or die(mysqli_error($this->link));
			mysqli_query($this->link,$cad1) or die(mysqli_error($this->link));
      
            header("location:panelUser.php?mod=panel&msg=1");
            exit;
        }else if($op=="editarFotos"){
        	if(isset($_GET["idq"])){$idq=htmlentities($_GET["idq"]);}
        	$this->tablaFotos($idq);
		 
			 
        }else{
        $index="panelUser.php?mod=panel";
		
		if(isset($_POST["palabra"])){
			$sql='SELECT * FROM `mm_propiedad` WHERE titulo like "%prueba%" and papelera=0  order by idProp desc';
			
		}else{
			if(isset($_GET["v"]) && $_GET["v"]==1){
				$sql="SELECT * FROM `mm_propiedad` where papelera=1 and idCorredora='".$idCorredora."' order by idProp desc";
			 }else{
				$sql="SELECT * FROM `mm_propiedad` where papelera=0 and idCorredora='".$idCorredora."' order by idProp desc";
			 }
		}
  
        
 
        $campos=array("ruta"=>"foto",
        			  "titulo"=>"Titulo"
        		 
        			 

		); 
        $tamCol=array(2,55,10,10);
        
        
					 
        
       
        $campoFoto=array("ruta"=>true);        
        $campoIndice="idProp";            
        $grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);
       
		$tabla="mm_propiedad";
   
        $grid->asignarCampos($campos,$tamCol,$sql,$tabla);
		 
        if(isset($_GET["msg"])){
            echo '<div class="alert alert-primary" role="alert">
            Propiedad eliminada con exito
          </div>';
        }
		
		 
		echo "<form name='form1' id='form1' method='post' action=''>";
        $grid->desplegarDatos();
		echo "</form>";
     
        }
        return(true); 
 	}

  public function avatar(){
    $idUser=$_SESSION["auth"]["idUser"];
    $sql="select rutaFoto from registro where idReg='".$idUser."'";
    $q=mysqli_query($this->link,$sql);
    $r=mysqli_fetch_array($q);
 
    echo '<img src="./upload/'.$r["rutaFoto"].'" style="width:50%;">';
 }
public function registro(){
        
      
      if(isset($_POST["action"])){
        $nombre = htmlentities($_POST['nombre']);
        $telefono = htmlentities($_POST['telefono']);
        $tipo = htmlentities($_POST['tipo']);
        $region5x = htmlentities($_POST['region5x']);
        $ciudad3 = htmlentities($_POST['ciudad3']);
        $comuna3 = htmlentities($_POST['comuna3']);
        $email = htmlentities($_POST['email']);
        $contra = htmlentities($_POST['contra']);
        $confirmar = htmlentities($_POST['confirmar']);
        $query = "INSERT INTO registro (nombre, telefono, tipo, region, ciudad, comuna, email, pass) ";
        $query.=" VALUES ('$nombre', '$telefono', '$tipo', '$region5x', '$ciudad3', '$comuna3', '$email', '$contra')";  
		
        mysqli_query($this->link,$query) or die(mysqli_error($this->link));		
		echo '<script>
		document.location="registrar.php?msg=1";
		</script>';
        
        
      }
      if(isset($_GET["msg"])){
        echo '<div class="alert alert-primary" role="alert">
        Se ha registrado con exito en nuestro portal de avisos inmobiliarios, puede ingresar a su cuenta haciendo click en este enlace <a href="loginUser.php">Acceso a Usuarios</a>
      </div>';
      }
      
            echo "<form method='post' name='form1' id='form1' ACTION=''>";
            echo '<div class="card p-4">
      <div>
        
        <div style="font-size:22px;">Registrate y publica gratis</div>
      </div>
      <div class="mb-4">
        <span style="font-size:14px;">Ingresa tus datos para completar el registro, inicia sesión <a href="index.php?mod=login">Aquí</a></span>
      </div>
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre:</label>
        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" value="">
      </div>
      <label for="telefono" class="form-label">Teléfono:</label>
      <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-phone" aria-hidden="true"></i></span>
        <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Teléfono" aria-label="Teléfono" aria-describedby="basic-addon1" value="">
      </div>
      <div style="font-size:16px;">Registrarse como</div>
      <div>
        <select class="form-select mt-2 mb-2" name="tipo" id="tipo" aria-label=".form-select-lg example">
          <option value="1">Particular</option>
          <option value="2">Inmobiliaria</option>
          <option value="3">Corredora</option>
        </select>
      </div>
      <div style="font-size:16px;">Región</div>
      <div>
        <select class="form-select mt-2 mb-2" name="region5x" id="region5x" aria-label=".form-select-lg example">
          <option value="0" selected="selected">Región</option>
          <option value="1">Región Metropolitana</option>
          <option value="2">XV Arica Y Parinacota</option>
          <option value="3">I Tarapacá</option>
          <option value="4">II Antofagasta</option>
          <option value="5">III Atacama</option>
          <option value="6">IV Coquimbo</option>
          <option value="7">V Valparaíso</option>
          <option value="8">VI OHiggins</option>
          <option value="9">VII Maule</option>
          <option value="10">XVI Ñuble</option>
          <option value="11">VIII Biobío</option>
          <option value="12">IX Araucanía</option>
          <option value="13">XIV Los Ríos</option>
          <option value="14">X Los Lagos</option>
          <option value="15">XI Aisén</option>
          <option value="16">XII Magallanes y Antártica</option>
        </select>
      </div>
      <div style="font-size:16px;">Ciudad</div>
      <div>
        <select class="form-select mt-2 mb-2" name="ciudad3" id="ciudad3" aria-label=".form-select-lg example">
          <option value="0" selected="selected">Ciudad</option>
        </select>
      </div>
      <div style="font-size:16px;">Comuna</div>
      <div>
        <select class="form-select mt-2 mb-2" name="comuna3" id="comuna3" aria-label=".form-select-lg example">
          <option value="0" selected="selected">Comuna</option>
        </select>
      </div>
      <label for="email" class="form-label">Email</label>
      <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-envelope" aria-hidden="true"></i></span>
        <input type="text" class="form-control" name="email" id="email" placeholder="Email" aria-label="Email" aria-describedby="basic-addon1" value="">
      </div>
      <label for="contra" class="form-label">Contraseña:</label>
      <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-key" aria-hidden="true"></i></span>
        <input type="password" class="form-control" name="contra" id="contra" placeholder="Contraseña" aria-label="Contraseña" aria-describedby="basic-addon1" value="">
      </div>
      <label for="confirmar" class="form-label">Confirmar contraseña:</label>
      <div class="input-group mb-3">
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-key" aria-hidden="true"></i></span>
        <input type="password" class="form-control" name="confirmar" id="confirmar" placeholder="Confirmar contraseña" aria-label="Confirmar contraseña" aria-describedby="basic-addon1" value="">
      </div>
      
      <div class="g-recaptcha" data-sitekey="6LdRq_ImAAAAAEUYVVKhD2cl-yKuVlJOy1FJIy2q" data-callback="correctCaptcha"></div>
      <div>
        <input type="checkbox" style="margin-top: 0px;" name="condiciones" id="condiciones" value="">
        <span style="font-size:16px;">Estoy de acuerdo con las <a href="index.php?mod=pag&amp;idPag=7" target="_blank">Condiciones del servicio</a></span>
      </div>
      <input type="hidden" name="action" id="action" value="true"> 
      
      
      <div>&nbsp;</div>
      <div>
        <button type="button" name="enviar" style="width: 100%;" id="enviar" class="btn btn-primary btn-mb">Crear Cuenta</button>
      </div>
    </form>
    <script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      var filter = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
      var num = /([0-9])/;
      $(document).ready(function () {
        $("#enviar").click(function () {
          var c = $("#g-recaptcha-response").val();
          var nombre = $("#nombre").val();
          if (nombre.length == 0) {
            alert("Ingresa el Nombre:");
            $("#nombre").focus();
            return false;
          }
          var telefono = $("#telefono").val();
          if (telefono.length == 0) {
            alert("Ingresa el Teléfono:");
            $("#telefono").focus();
            return false;
          }
          var email = $("#email").val();
          if (email.length == 0) {
            alert("Ingresa el Email");
            $("#email").focus();
            return false;
          }
          var contra = $("#contra").val();
          if (contra.length == 0) {
            alert("Ingresa la contraseña:");
            $("#contra").focus();
            return false;
          }
          var confirmar = $("#confirmar").val();
          if (confirmar.length == 0) {
            alert("Ingresa la Confirmar contraseña:");
            $("#confirmar").focus();
            return false;
          }
          var condiciones = $("#condiciones").val();
          var op = $("input[name=condiciones]:checked").val();
          if (op == null) {
            alert("Seleccione una Condiciones del Servicio : ");
            return false;
          }
          var enviar = $("#enviar").val();
          if (c.length == 0) {
            alert("Debe seleccionar el recuadro no soy robot del captcha de seguridad");
          } else {
            $("#form1").submit();
          }
        });
        return false;
      });
    </script>';
    
        }
}

?>