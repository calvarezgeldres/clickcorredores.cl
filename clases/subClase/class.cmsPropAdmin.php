<?php
ob_start();
/*
require_once "aws/vendor/autoload.php";
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
*/
 /*
 Nombre : CmsProp 4.1 - Programación Web Chile
 Autor: Luis Olguin - Programador
 Sitio Web: www.clickcorredores.cl 
 Fecha de Creación: 20/7/2017
 Modificada 12/05/2022
 Descripción: Sistema basico de corretaje con amazon cloud
 */
 
 
 
 
require_once("./clases/class.coneccion.php");
require_once("./clases/class.form.php");
require_once("./clases/class.upload.php");
require_once("./clases/class.paginator.php");
require_once("./clases/class.miniGrid.php");
  
require_once("./clases/class.sqlPlus.php");
require_once("./clases/class.monitor.php");
require_once("./clases/class.rotador.php");
//require_once("./clases/class.slider4.php");
require_once("./clases/class.gMaps.php");
require_once("./clases/class.msgBox.php");
 
class cmsPropAdmin extends coneccion{    
    private $miForm;
	public $gMaps;
    private $paginator;
	private $geo;
	private $grid;
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
		 $this->paginator=new paginator(24,24);
		  $this->sql=new sql();
		
	}

	public function modificarEntrada($id){
		$this->link=$this->conectar();
	
		$sql="select* from agro_blog where idBlog='".$id."'";
		
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		 
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
	 
		if(isset($_GET["msg"])){

			if($_GET["msg"]==2){
				$this->msgBox=new msgBox(1,"Cambios se han guardado con exito!!!");
			}else{
				$this->msgBox=new msgBox(1,"Entrada se ha eliminado con exito!!!");
				
			}
			
		}
		$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
		$this->miForm->addText("titulo",550,"Titulo","Titulo :",$r["titulo"],"editar");
		if(!empty($r["imagen"])){
			echo "<div>Foto</div>";
			echo "<div>";
			echo "<img src='./upload/".$r["imagen"]."' style='width:130px; height:90px;'/>";
			echo "</div>";
		}	
		
		echo "<div>Subir otra imagen</div>";
		$this->miForm->addFile(1,"Foto",$editarImagen);

 
		echo "<div>Descripción</div>";
		echo "<div>";

		echo ' <textarea name="des">'.$r["des"].'</textarea>
		<script>
				CKEDITOR.replace( "des",{
					uiColor: "#9AB8F3"
				}
			
			);
		</script>';

		echo "</div>";

		$this->miForm->addHidden("action","true");   
      //  $this->miForm->addFile(1,"Foto",$editarImagen);
		echo "<div style='margin-top:10px;'>";
		$this->miForm->addButton("enviar","Guardar Cambios",false,$borrar=false,"primary","far fa-check-circle",true,false,"lg");            
		echo "</div>";
        $this->miForm->procesar();
         if($this->miForm->procesarArch()){
            $arch=$this->miForm->getDataArch();                     
        }        
        if($_POST["action"]){   
			 
			$fecha=strtotime(date("d-m-Y H:i:s"));  		
			$v=htmlentities($_POST["video"]);
			 
			$k=explode("=",$v);
			
			if(count($k)==2){
				$video1=$k[1];	 
			}else{
			 
				$m=explode("/",$v);				
				$video1=$m[3];
			 
				
			}
			 
			$sql1="update agro_blog set titulo='".$_POST["titulo"]."'";
			if(!empty($arch[0])!=0){
				$sql1.=",imagen='".$arch[0]."'";
			}
			$sql1.=", des='".trim($_POST["des"])."', video='".$video1."' where idBlog='".$id."'";
		 
			 mysqli_query($this->link,$sql1);
			 header("location:panel.php?mod=panel&op=445&mq=editar&idq=".$id."&msg=2");
			 exit;			
		}
		echo "</div></div>";
		$this->miForm->cerrarForm();
 
	
	 }
	 public function ingresarEntrada(){
		$this->link=$this->conectar();
		echo "<div class='row'>";
		echo "<div class='col-md-12' style='padding:25px;'>";
	 
		
			if($_GET["msg"]==1){				
				$this->msgBox=new msgBox(1,"Entrada se ha ingresado con exito!!!");
			}			
		
		$this->miForm->abrirForm(1,true,"form1","post","proceso.php",2);
		$this->miForm->addText("titulo",550,"Titulo","Titulo :",$row["titulo"]);
		$this->miForm->addFile(1,"Imagen",$editarImagen);
		
		
		
	 
		echo "<div>Descripción</div>";
		echo "<div>";
		echo ' <textarea name="des"></textarea>
		<script>
				CKEDITOR.replace( "des" );
		</script>';
		echo "</div>";
		$this->miForm->addHidden("action","true");   
     
		echo "<div style='margin-top:10px;'>";
		$this->miForm->addButton("enviar","Agregar Entrada",false,$borrar=false,"primary","far fa-check-circle",true,false,"lg");            
		echo "</div>";
        $this->miForm->procesar();
         if($this->miForm->procesarArch()){
            $arch=$this->miForm->getDataArch();                     
        }        
        if($_POST["action"]){   
			
			$fecha=strtotime(date("d-m-Y H:i:s"));  		
			 $v=htmlentities($_POST["video"]);
			 
			 $k=explode("=",$v);
			 if(count($k)==2){
				 $video=$k[1];	 
			 }else{
				 $m=explode("/",$v);
				 $video=$m[3];
			 }

				$sql1="insert into agro_blog (fecha,titulo,des,imagen,video) ";
				$sql1.=" values('".$fecha."','".$_POST["titulo"]."','".$_POST["des"]."','".$arch[0]."','".$video."')";
			 
			mysqli_query($this->link,$sql1);
			 header("location:panel.php?mod=panel&op=445&m=add&msg=1");
			 exit;
			 		
		}
		echo "</div></div>";
		$this->miForm->cerrarForm();
	 
	 }
	public function tablaEntradas(){
		echo "<div class='row'>";
		echo "<div class='col-md-12' style='padding:25px;'>";
	 
		if(isset($_GET["msg"])){
			if($_GET["msg"]==1){
				$this->msgBox=new msgBox(1,"Entrada se ha eliminado con exito!!!");
			}else if($_GET["msg"]==3){
				$this->msgBox=new msgBox(1,"Entrada se ha ingresado con exito !!!");
			}			
		}
		 

	 		$this->paginator=new paginator(100,100);
	 	/* 1=avisos pendientes a revision 2 avisos publicados*/
	  	$sql="select* from agro_blog order by idBlog desc";
	 	$index="panel.php?mod=panel&op=44";
      
        $campos=array(                 
                       "fecha"=>"fecha","titulo"=>"titulo"); 
          /* FALTA LA OPCION MODIFICAR*/
          if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
        if($op=="editar"){
            $id=htmlentities($_GET["idq"]);   
            
         $this->modificarEntrada($id);
        }else if($op=="borrar"){
            $id=htmlentities($_GET["idq"]);            
			$cad="delete from  agro_blog where idBlog='".$id."'";
            mysqli_query($this->link,$cad);
			header("Status: 301 Moved Permanently");
			header("Location:panel.php?mod=panel&op=445&msg=1");
			echo"<script language='javascript'>window.location='panel.php?mod=panel&op=445&msg=1'</script>;";
			exit();
            
        }else{
        $index="panel.php?mod=panel&op=445";      
        $tamCol=array(1,100,100);
        
     //   $opciones=array("numClick"=>array(0=>"santiago",2=>"peru"));
        
       
       // $campoFoto=array("foto"=>true);        
        $campoIndice="idBlog";            
        
        $grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);
       
		$tabla="agro_blog";
   
        $grid->asignarCampos($campos,$tamCol,$sql,$tabla);
		 
		 
        
        $grid->desplegarDatos();
        
        }
		echo "</table>";
		 
        return(true);
		echo "</div>";
		echo "</div>";
	}
	
	public function fotoPerfil(){
		$this->link=$this->conectar();
		
		$sql="select* from admin where idAdmin=1";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$foto=$r["foto"];
		echo '<img src="./upload/'.$foto.'" alt="Profile image" class="avatar-rounded">';
	}
	public function verPerfilAdmin(){ 
		$this->link=$this->conectar();
	 
		if(isset($_GET["msg"])){
            $msg=htmlentities($_GET["msg"]);
            if($msg==1){
                  $msgBox = new msgBox(1,"Agente ingresada con exito!!!");}
        }     
		$sql="select* from admin where idAdmin=1";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
 
		$foto=$r["foto"];
        echo "<form name='form1' id='form1' enctype='multipart/form-data' method='post' action=''>";   
    	$this->miForm->addHidden("action","true"); 		
      	echo "<form method='post' name='form1' id='form1'  enctype='multipart/form-data'>";
		echo "<div class='row'>";
		echo "<div class='col-md-4'>";
		echo "<div>";
		echo '<img class="thumb-image" style="width: 100%; display: block;" src="./upload/'.$foto.'" alt="image">';
		echo "</div>";
		
		echo "<div>";
		$this->miForm->addFile(1,"Foto",$editarImagen);    
		echo "</div>";
		
		echo "</div>";
		echo "<div class='col-md-8'>";
		 echo '<div class="card-block">
                            <!-- <div class="row"> -->
                                  
                                <h4 class="card-title">Información personal</h4>
                            <form id="demo-form2" data-parsley-validate="" class="form-horizontal form-label-left" action="action/upd_profile.php" method="post">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Nombre</label>
                                    <div class="col-md-8">
                                        <input type="text" name="name" id="first-name" value="'.$r["nombre"].'" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Correo Electrónico</label>
                                    <div class="col-md-8">
										<div class="input-group">
									
                                        <input type="text" name="email" value="'.$r["email"].'" class="form-control">
										</div>
                                    </div>
                                </div>
                                <br>
                              

                                

                                 
                                 
                                <hr>

                                <div class="text-center">
                                    <button type="submit" name="token" class="btn btn-primary">Actualizar Datos</button>
                                </div>
                            </form>
                                
                            </div>';
		
		 
		echo "</div>";
		
		echo "</div>";
		 
				 
        $this->miForm->procesar();
        if($this->miForm->procesarArch3()){
            $arch=$this->miForm->getDataArch();                     
        }
		 
		if($_POST["action"]){    
			
			$d=$_POST;
			$sql="update  admin set ";
		
			$sql.="nombre='".$d["name"]."',
				   email='".$d["email"]."',
				   pass='".$d["password"]."' ";
				    if(!empty($arch[0])){
					$sql.=", foto='".$arch[0]."'";
				   }
				   if(isset($d["status"])){
					   if($d["status"]=="on"){
						   $m=1;
					   }else{
						   $m=0;
					   }
					$sql.=",estado='".$m."'";
				   }
				   $sql.=" where idAdmin='1'";
				   
				   mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
				  
				   header("location:panel.php?mod=panel&op=144&msg=1");
				   exit;
		}
	
		
		   
		$this->miForm->cerrarForm();
				
	}
	public function seguridad(){
		$this->link=$this->conectar();
		$sql="select* from admin";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		
		if(isset($_POST["boton"])){
			$pass1=htmlentities($_POST["pass1"]);
			if(isset($_POST["usuario"]) && isset($_POST["pass"])){
				$usuario=htmlentities($_POST["usuario"]);
				$pass=htmlentities($_POST["pass"]);
				 
				if(!empty($_POST["usuario"]) && !empty($_POST["pass"])) {
						if(strlen($pass)>=8){
								$p=md5($pass);
								$sql2="update admin set nick='".$usuario."', pass='".$p."' where idAdmin=1";
								$q=mysqli_query($this->link,$sql2);
								header("location:panel.php?mod=panel&op=245&msg=1");
								exit;
						}else{
							$this->msgBox=new msgBox(1,"La contraseña debe tener minimo 8 caracteres");		
						}
				}else{
					$this->msgBox=new msgBox(1,"Ingrese su nueva contraseña");
				}
			}
		 
		}
		echo "<div class='row'>";
		echo "<div class='col-md-6'>";

		if(isset($_GET["msg"])){
			$this->msgBox=new msgBox(1,"Contraseña ha sido creada y encriptada con exito  !!");
		}

		echo "<div>";

		echo "<div>";
		echo "<div><h4><i class='fas fa-lock'></i> &nbsp; Contraseña de administrador</h4></div>";
		echo "<div style='margin-top:10px;'>Cambie aqui su contraseña del panel de administración de su página web</div>";
		echo "<div style='margin-top:10px;'>";
		echo '<form method="post" name="form1" id="form1" value=""/>
		<div><span>Nombre de usuario</span></div>
			<div class="input-group mb-3">
			
			 <span class="input-group-text" id="inputGroup-sizing-default"><i class="fas fa-user"></i></span>
			 <input type="text" value="'.$r["nick"].'" class="form-control form-control input-mb" name="usuario" id="usuario" placeholder="Nombre de usuario" aria-label="Su nombre de usuario" aria-describedby="inputGroup-sizing-default" value="admin">
		   </div>
		   <div><span>Para crear una nueva contraseña debe nuevamente ingresarla</span></div>
		   <div class="input-group mb-3">
			 <span class="input-group-text" id="inputGroup-sizing-default"><i class="fas fa-lock"></i></span>
			 <input type="password" value="" placeholder="Nueva contraseña"  name="pass" id="pass" class="form-control" aria-describedby="inputGroup-sizing-default">
		   </div>

		   <div><span>Su contraseña actual encriptada</span></div>
		   <div class="input-group mb-3">
		   <span class="input-group-text" id="inputGroup-sizing-default"><i class="fas fa-lock"></i></span>
		   <input type="password" value="'.$r["pass"].'" name="pass1" readonly id="pass1" class="form-control" aria-label="Contraseña Actual" aria-describedby="inputGroup-sizing-default">
		 </div>


		   <div class="input-group mb-3">
			 
			 <button class="btn btn-primary btn-sm" style="width:100%;" name="boton" id="boton">Cambiar contraseña</button>
		   </div>

		   </form></div>';
		echo "</div>";



		echo "</hr>";
		echo "<div><i class='fas fa-lock'></i> &nbsp; <span style='font-size:20px;'> Seguridad en su contraseña </span></div>";
		echo "<div style='margin-top:20px;'>
		<ul>
		   <li>Cambiar la contraseña por defecto a una nueva clave que usted pueda recordar.</li>
		   <li>Asegurarse que la contraseña tenga minimo de 8 a 12 caracteres.</li>
		   <li>Asegurarse que la contraseña tenga Mayusculas y minusculas ademas de numeros o simbolos.</li>
	   
		   <li>Cambiar sus claves cada 6 meses o bien cuando usted crea que la contraseña este comprometida</li>		
		</ul>
		
		</div>";
	


		echo "</div>";
		echo "</div>";

		echo "<div class='col-md-6'>";
		echo "<div><i class='fas fa-lock'></i> &nbsp; <span style='font-size:20px;'> Seguridad con sus correos </span></div>";
		echo "<div style='margin-top:20px;'>
		<ul>
		   <li>Si usted va hacer uso de sus correos corporativos, asegurarse primeramente en cambiar las claves temporales por claves nuevas que usted pueda recordar.</li>
		   <li>Si usted no sabe como cambiar las claves de sus correos contactarse con el webmaster al email <a href='mailto:contacto@clickcorredores.cl'>Solicitar asistencia</a></li>
		   <li>Por seguridad se recomienda vincular sus correos hacia una cuenta gmail que usted use.</li>			
		   <li>En caso de no saber como vincular sus correos a una cuenta gmail puede contactarse con el webmaster <a href='mailto:contacto@clickcorredores.cl'>Solicitar asistencia</a></li> 			
		   <li>Anotar todas las contraseñas de su página en alguna libreta de contraseñas</li>	
		</ul>

		
		</div>";
		echo "</div>";

		echo "</div>";
	}
	public function verLiscencia(){
		
		echo "<div class='row'>";
		
		echo "<div class='col-md-6'>";
		echo 'Licencia Sistema Corretaje de Propiedades Cms-Prop 4.1 lite
		
				La mayoría de nuestros sistemas están cubiertos por nuestras Licencias estándar. A continuación se detalla que incluye nuestra licencia estándar:
				<br><br><b>Que está permitido hacer con el sistema.</b>
				<ul>
				<li>Usar el sistema en 1 dominio para uso personal.</li>
				<li>Se permite modificar el sistema, para poder añadir nuevas funcionalidades.</li>
				</ul> 
				<b>Que no está permitido hacer con el sistema</b><br>
				<ul>
				<li>No se permite usar partes del código o en su totalidad para crear otro sistema o sitio web.</li>
				<li>No puedes dar, vender, distribuir, sub-licenciar, alquilar o prestar cualquier parte del Software o documentación a nadie.</li>
				<li>No puedes colocar el Software en un servidor para que sea accesible a través de una red pública como Internet para propósitos de distribución.</li>
				<li>No se permite remover la información de derechos de autor; esto incluye el texto / enlace en la parte inferior.</li>
								
				
				</ul>

				<b>Mejoras de la versión 4.1 lite - Actualización: 15/05/2022</b><br>
				<ul>
				<li>Conección con los servidores de Amazon cloud</li>				
				</ul>


				<b>Mejoras de la versión 4.0 - Actualización: 3/11/2021</b><br>
				<ul>
				<li>Subida de fotos via FTP/Web</li>
				<li>Marca de agua</li>				
				<li>Subida de distintos formatos de fotos (jpg,png,etc)</li>				
				<li>Incorporación opcional mapa Openstreet por zonas</li>	
				</ul>

				<b>Mejoras de la versión 3.0 - Actualización: 4/4/2020</b><br>
				<ul>
 
	 
	 
				<li>Actualización de panel de administración</li>
				<li>Orientación y lectura de Metadatos Exif en fotografias</li>				
				<li>Rotación automatica de fotos</li>				
				<li>Redimención automatica de fotos</li>	
				</ul>';

		echo "</div>";
		
		echo "<div class='col-md-6'>";
		echo '<div class="box box-solid limit-p-width">
								<div class="box-body affiliate">
									<div class="heading">
										<h2>Información de licencia</h2>
									</div>
									
									<hr>
									<div class="caption">
										<div class="row">
											<div class="col-md-12">
												<label>Licencia Nº</label>
												<input type="tex" class="form-control" value="PHPMYLICENSE-F8S9-M74G-GWPP-VL48-E926-Y75Q" readonly="">
											</div>
											
											<div class="col-md-12">
												<label>Nombre de dominio</label>
												<input type="tex" class="form-control" value="www.clickcorredores.cl" readonly="">
											</div>
											 
											<div class="col-md-12">
											<label>Conectado al servidor cloud</label>
											<input type="tex" class="form-control" value="América del Sur (São Paulo) sa-east-1  (no activado)" readonly="">
											</div>
											<div class="col-md-12">
												<label>E-mail Soporte Técnico</label>
												<input type="tex" class="form-control" value="contacto@clickcorredores.cl" readonly="">
											</div>
											
										
											
											<div class="col-md-12">
												<label>Acerca del autor</label>
												<textarea name="comentarios" id="comentarios"  readonly="" style="height:200px;" class="form-control input-sm" >
												Fecha de Actualización: 15/05/2022 - Versión sistema corretaje: Versión 4.1 lite- Desarrollado por: www.clickcorredores.cl </textarea>
												 
											</div>
										
										</div>
									</div>

								</div>
							</div>';
		echo "</div>";
		
		echo "</div>";
		
	}
	public function totalVisitantes(){
		$this->link=$this->conectar();
		$sql="select count(*) as total from coti_monitor";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		echo $this->formatoNumerico($total);	
	}
	public function totalVisitantesChile(){
		$this->link=$this->conectar();
		$sql="select count(*) as total from coti_monitor where pais='Chile'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		echo $this->formatoNumerico($total);
	}
	public function otrosPaises(){
		$this->link=$this->conectar();
		$sql="select count(*) as total from coti_monitor where pais!='Chile'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		echo $this->formatoNumerico($total);
	}
	public function totalOperaciones(){
		$this->link=$this->conectar();
		$sql="select count(*) as total from mm_propiedad where  operacion=0 or operacion=1 and papelera=0";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		echo $this->formatoNumerico($total);	
	}
	public function totalArriendos(){
		$this->link=$this->conectar();
		$sql="select count(*) as total from mm_propiedad where operacion!=1";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		echo $this->formatoNumerico($total);	
	}
	public function totalVentas(){
		$this->link=$this->conectar();
		$sql="select count(*) as total from mm_propiedad where operacion=1";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		echo $this->formatoNumerico($total);	
	}
	public function totalPropDesc(){
		$this->link=$this->conectar();
		$sql="select count(*) as total from mm_propiedad  where papelera=0 and estadoProp=1";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		echo $this->formatoNumerico($total);
	}
	public function totalPropNoDesc(){
		$this->link=$this->conectar();
	$sql="select count(*) as total from mm_propiedad where where papelera=0 and estadoProp!=1";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		echo $this->formatoNumerico($total);	
	}
	public function totalProp(){
		$this->link=$this->conectar();
		$sql="select count(*) as total from mm_propiedad where papelera=0";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		echo $this->formatoNumerico($total);
		 
	}
    public function agregarContenido(){
		$this->link=$this->conectar();
		if(isset($_GET["msg"])){
            $msg=htmlentities($_GET["msg"]);
            if($msg==1){
				$this->miForm->addAlert("Información del sistema","Se ha agregado contenido con exito !!",1);
			}
        }    
        $this->miForm->abrirForm(1,true,"form1","post","proceso.php",2);
       
        $this->miForm->addText("titulo",550,"Titulo","Titulo :",$row["titulo"]);
		 
	
		 
        //$this->miForm->addTextarea("descripcion",100,18,"Texto","Texto :",$row["texto"]);
		echo "<div>Descripción</div>";
		echo ' <textarea name="descripcion"></textarea>
		<script>
				CKEDITOR.replace( "descripcion" );
		</script>';	   
 
		echo "</textarea>";
		if(!empty($row["foto"])){
        echo "<tr><td align='right' valign='top'>Foto:</td><td>";
		echo "<img src='./upload/".$row["foto"]."' width='190'/>";
		echo "</tr></td>";
		}
        $this->miForm->addHidden("action","true");   
		$this->miForm->addFile(1,"Foto",$editarImagen);
		echo "<div>&nbsp;</div>";
		$this->miForm->addHidden("action","true");  
		$this->miForm->addButton("enviar","Agregar Contenido",false,$borrar=false,"primary","far fa-check-circle",true,false,"lg");            
       
	   
        $this->miForm->procesar();
         if($this->miForm->procesarArch()){
            $arch=$this->miForm->getDataArch(); 
                    
        }
        
        if($_POST["action"]){
         	$fecha=strtotime(date("Y-m-d h:m:s"));
        	$sql="insert into mm_coti_contenido (titulo,fecha,texto,foto) ";	
			$sql.="value ('".$_POST["titulo"]."','".$fecha."','".$_POST["descripcion"]."','".$arch[0]."');";	 
			mysqli_query($this->link,$sql) or die(mysqli_error($this->link));		 
		 	header("location:panel.php?mod=panel&op=17&msg=1");
			exit;	
        }
        $this->miForm->cerrarForm();
       return(true);
	}
 
    
	public function devolverMenu($id){
		$this->link=$this->conectar();
		$sql="select* from  mm_coti_categoria where idCategoria='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		 
		return($r["nombre"]);
	}
	public function modificaSubMenu($id){
		$this->link=$this->conectar();
		$sql="select* from mm_submenu where idSub='".$id."'";
		$q3=mysqli_query($this->link,$sql);
		$r3=mysqli_fetch_array($q3);
	 
	  $this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
    
 		 echo "<div style='margin-bottom:20px;'>";
		echo "<span style='font-size:18px;'>Modificar Sub Menu</span>";
		echo "</div>";
		echo "<tr><td><span style='font-size:14px;'>Seleccione Menu:</span></td>";
		echo "<td><select name='m' id='m'>";
		$sql="select* from  mm_coti_categoria";
		$q=mysqli_query($this->link,$sql);
		echo "<option value='".$r3["idCategoria"]."' selected='selected' style='font-size:14px;  '>".$this->devolverMenu($r3["idCategoria"])."</option>";
		while($r=mysqli_fetch_array($q)){
			echo "<option style='padding-left:10px; font-size:14px;padding-right:10px;' value='".$r["idCategoria"]."'>";
			echo "<span style='font-size:14px;'>".$r["nombre"]."</span>";
			echo "</option>";
		}
		echo "</select>";
		
		echo "</td></tr>";
        $this->miForm->addText("categoria",350,utf8_decode("Nombre")." :",utf8_decode("Nombre"),$r3["nombre"]);
		 $this->miForm->addText("url",350,utf8_decode("Url")." :",utf8_decode("Url"),$r3["url"]);
	 
	 
        $this->miForm->addHidden("action","true");   
        $this->miForm->addButton("Enviar","Agregar Sub Menu",false,false);
        $this->miForm->procesar();
		 if($this->miForm->procesarArch()){
            $arch=$this->miForm->getDataArch();           
        }  
        $post=$this->miForm->getDataPost();
        if($_POST["action"]){
           	 $sql="update mm_submenu  set idCategoria='".$_POST["m"]."', nombre='".$_POST["categoria"]."',url='".$_POST["url"]."' where idSub='".$id."'";	
			 
			  mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
			  $this->cerrar();
			   header("location:panel.php?mod=panel&op=18&mq=editar&sb=menu&idq=".$_GET["idq"]."&msg=6");
			  exit;
        }
		
	 
        $this->miForm->cerrarForm();
	}
       public function agregaSubMenu(){
		$this->link=$this->conectar();
	     $this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
    
 		 echo "<div style='margin-bottom:20px;'>";
		echo "<span style='font-size:18px;'>Agregar Sub Menu</span>";
		echo "</div>";
		echo "<tr><td><span style='font-size:14px;'>Seleccione Menu:</span></td>";
		echo "<td><select name='m' class='form-control input-sm' style='margin-bottom:3px; margin-top:3px;height:38px; width:350px;' id='m'>";
		$sql="select* from  mm_coti_categoria ";
		$q=mysqli_query($this->link,$sql);
		echo "<option value='0' selected='selected' style='font-size:14px;'>Seleccione Opción</option>";
		while($r=mysqli_fetch_array($q)){
			echo "<option style='padding-left:10px; font-size:14px;padding-right:10px;' value='".$r["idCategoria"]."'>";
			echo "<span style='font-size:14px;'>".$r["nombre"]."</span>";
			echo "</option>";
		}
		echo "</select>";
		
		echo "</td></tr>";
        $this->miForm->addText("categoria",350,utf8_decode("Nombre")." :",utf8_decode("Nombre"),false);
		$this->miForm->addText2("url",350,utf8_decode("url")." :",utf8_decode("url"),false);
	 
        $this->miForm->addHidden("action","true");   
        $this->miForm->addButton("Enviar","Agregar Sub Menu",false,false);
        $this->miForm->procesar();
		 if($this->miForm->procesarArch()){
            $arch=$this->miForm->getDataArch();           
        }  
        $post=$this->miForm->getDataPost();
        if($_POST["action"]){
           	 $sql="insert into mm_submenu(idCategoria,nombre,url) values('".$_POST["m"]."','".$_POST["categoria"]."','".$_POST["url"]."')";	
			 
			  mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
			  $this->cerrar();
			   header("location:panel.php?mod=panel&op=92&msg=1");
			  exit;
        }
        $this->miForm->cerrarForm();
	}
	
    public function agregarCategoriaOpcion(){
		$this->link=$this->conectar();
        $this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
        
 		 echo "<div style='margin-bottom:20px;'>";
		echo "<span style='font-size:18px;'>Agregar Menu</span>";
		echo "</div>";
        $this->miForm->addText("categoria",350,utf8_decode("Nombre del Menu")." :",utf8_decode("Nombre del Menu:"),false);
		 $this->miForm->addText2("url",350,utf8_decode("Enlace")." :",utf8_decode("Enlace:"),false);
		
	 
        $this->miForm->addHidden("action","true");   
        $this->miForm->addButton("Enviar","Agregar Sección",false,false);
        $this->miForm->procesar();
		 if($this->miForm->procesarArch()){
            $arch=$this->miForm->getDataArch();           
        }  
        $post=$this->miForm->getDataPost();
        if($_POST["action"]){
           	 $sql="insert into mm_coti_categoria (nombre,url) values('".$_POST["categoria"]."','".$_POST["url"]."')";			
			  mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
			   header("location:panel.php?mod=panel&op=18&msg=1");
			  exit;
        }
        $this->miForm->cerrarForm();
	}
	public function tablaContenido(){
		$this->link=$this->conectar();
		if(isset($_GET["msg"])){
			$msg=htmlentities($_GET["msg"]);
			if($msg==2){
				$this->miForm->addAlert("Información del sistema","Contenido se ha guardado con exito !!",1);
			}else{			
				$this->miForm->addAlert("Información del sistema","Propiedad se ha eliminado con exito!!",1);
				 
			}
		}
        $sql="select* from mm_coti_contenido order by idContenido desc";
         $campos=array("foto"=>"foto",
      				  
                      "titulo"=>"Titulo",                  
                      "autor"=>"Autor",
					  
					  "estado"=>"Estado",
                      "fecha"=>"Fecha"
					  
                    ); 
          /* FALTA LA OPCION MODIFICAR*/
          if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
        if($op=="editar"){
            $id=htmlentities($_GET["idq"]);   
            //$this->miAvisos->modificarCarroEmpresa($id);
           $this->modificarContenido($id);
        }else if($op=="borrar"){
            $id=htmlentities($_GET["idq"]);            
          //  $cad=$this->sql1->sqlEliminarUsuario($id);
          //$cad=$this->sql->sqlBorrarContenido($id);
          $cad="delete from  mm_coti_contenido where idContenido='".$id."'";
            mysqli_query($this->link,$cad);
            header("location:panel.php?mod=panel&op=19&msg=2");
            exit;
        }else{
        $index="panel.php?op=4";      
        $tamCol=array(1,30,10,30,15);
        
     //   $opciones=array("numClick"=>array(0=>"santiago",2=>"peru"));
        
       
        $campoFoto=array("foto"=>true);        
        $campoIndice="idContenido";    
		$index="panel.php?mod=panel&op=19";        
        $grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);
        
        
        $tabla="mm_coti_contenido";
        $grid->asignarCampos($campos,$tamCol,$sql,$tabla);
	 
		
        $grid->desplegarDatos();
         
        }
        return(true);
	}
	public function devolverCategoriaOpcion($id){
		$this->link=$this->conectar();
		$sql="select* from mm_coti_categoria where idCategoria='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		return($r["nombre"]);
	}
	 
public function modificarCategoriaOpcion($id){
	$this->link=$this->conectar();
		if(isset($_GET["idq"])){
			$idq=htmlentities($_GET["idq"]);
		}
		 $sql="select* from mm_coti_categoria where idCategoria='".$idq."'";
 
		 $q=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		 $r=mysqli_fetch_array($q);
        $this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
         echo "<div style='margin-bottom:20px;'>";
		echo "<span style='font-size:18px;'>Modificar Menu</span>";
		echo "</div>";
 		
        $this->miForm->addText("nombre",450,"Menu",utf8_decode("Menu:"),$r["nombre"]);
		 $this->miForm->addText("url",350,utf8_decode("Enlace")." :",utf8_decode("Enlace:"),$r["url"]);
		
        $this->miForm->addHidden("action","true");   
        $this->miForm->addButton("Enviar","Agregar Sección",false,false);
        $this->miForm->procesar();
		 if($this->miForm->procesarArch()){
            $arch=$this->miForm->getDataArch();           
        }  
        $post=$this->miForm->getDataPost();
        if($_POST["action"]){
           	 $sql="update mm_coti_categoria set nombre='".$_POST["nombre"]."',url='".$_POST["url"]."' where idCategoria='".$id."'";			
			  mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
			   header("location:panel.php?mod=panel&op=18&idq=".$id."&msg=11");
			  exit;
        }
        $this->miForm->cerrarForm();
	}
    
	public function agregarCategoria(){
		$this->link=$this->conectar();
		 if(isset($_GET["msg"])){
          $this->msgBox=new msgBox(1,"Se ha creado con exito!!!");
        }
		 
        $this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);
        $this->miForm->setTitulo("Ingresar nueva familia de Productos");
 		
        $this->miForm->addText("familia",350,"familia","familia:",false);
		
	 
        $this->miForm->addHidden("action","true");   
        $this->miForm->addButton("Enviar","Agregar Familia",false,false);
        $this->miForm->procesar();
		 if($this->miForm->procesarArch()){
            $arch=$this->miForm->getDataArch();           
        }  
        $post=$this->miForm->getDataPost();
        if($_POST["action"]){
           	 $sql="insert into mm_coti_familia (nombre) values('".$_POST["familia"]."')";			
			  mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
			   header("location:panel.php?mod=panel&op=11&msg=1");
			  exit;
        }
        $this->miForm->cerrarForm();
	}
	public function agregarPagina(){
		$this->link=$this->conectar();
		if(isset($_GET["msg"])){
            $msg=htmlentities($_GET["msg"]);
            if($msg==1){
				$this->miForm->addAlert("Información del sistema","Se ha agregado contenido con exito !!",1);
			}
        }    
        $this->miForm->abrirForm(1,true,"form1","post","proceso.php",2);
        $this->miForm->setTitulo("Ingresar Contenido");
        $this->miForm->addText("titulo",550,"Titulo","Titulo :",$row["titulo"]);
		$sql1="select* from mm_coti_categoria order by idCategoria desc";
		$query1=mysqli_query($this->link,$sql1);
		$sql2="select idCate from mm_coti_contenido ";
		$q1=mysqli_query($this->link,$sql2);
		while($r1=mysqli_fetch_array($q1)){
			$p[$r1["idCategoria"]]=$r1["idCategoria"];
		}
		 
		while($row1=mysqli_fetch_array($query1)){
			if($p[$row1["idCategoria"]]!=$row1["idCategoria"]){
				$arrSel[$row1["idCategoria"]]=$row1["nombre"];	
			}
		}		
        $this->miForm->addSelect("idCate",$arrSel,"Seleccione...","func();","Categoria:",false);	
        $this->miForm->addTextarea("descripcion",100,18,"Texto Noticia","Texto Noticia :",$row["texto"]);
		if(!empty($row["foto"])){
        echo "<tr><td align='right' valign='top'>Foto:</td><td>";
		echo "<img src='./upload/".$row["foto"]."' width='190'/>";
		echo "</tr></td>";
		}
       
        $this->miForm->addFile(1,"Foto",$editarImagen);
		$this->miForm->addHidden("action","true");  
		$this->miForm->addButton("enviar","Agregar Página",false,$borrar=false,"primary","far fa-check-circle",true,false,"lg");            
       
	   
        $this->miForm->procesar();
         if($this->miForm->procesarArch()){
            $arch=$this->miForm->getDataArch(); 
                    
        }
        
        if($_POST["action"]){
         	$fecha=strtotime(date("Y-m-d h:m:s"));
        	$sql="insert into mm_coti_contenido (titulo,idCate,fecha,texto,foto) ";	
			$sql.="value ('".$_POST["titulo"]."','".$_POST["idCate"]."','".$fecha."','".$_POST["descripcion"]."','".$arch[0]."');";	 
			mysqli_query($this->link,$sql) or die(mysqli_error($this->link));		 
		 	header("location:panel.php?mod=panel&op=88&msg=1");
			exit;	
        }
        $this->miForm->cerrarForm();
       return(true);
	}
	public function modificarContenido($id){
		$this->link=$this->conectar();
	  
		$cadena1="select* from mm_coti_contenido where idContenido='".$id."'";
		   
		$query1=mysqli_query($this->link,$cadena1) or mysqli_error($this->link);
		$row=mysqli_fetch_array($query1);
 
        $this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);        
   
		
		$this->miForm->addText("titulo",550,"titulo","Titulo de la Propiedad :",$row["titulo"]);  
		 

     //   $this->miForm->addTextarea("des",60,20,"Descripción","Descripción :",$row["texto"]);	
	 	echo "<div>Descripcion</div>";
		echo ' <textarea name="des">'.$row["texto"].'</textarea>
		<script>
				CKEDITOR.replace( "des" );
		</script>';
		if(!empty($row["foto"])){
		 echo "<div>Imagen</div>";
		 echo "<div><img src='./upload/".$row["foto"]."' class='img-thumbnail' width='200'/></div>";
		 echo "<div><input type='checkbox' id='f' name='f' value='true'/>Borrar</div>";
		}
		 $this->miForm->addFile(1,"Subir Imagenes",$editarImagen);     
		echo "<div>&nbsp;</div>";
		 $this->miForm->addHidden("action","true");  
		 $this->miForm->addButton("enviar","Guardar Cambios",false,$borrar=false,"primary","far fa-check-circle",true,false,"lg");            
		
		
		  if($this->miForm->procesarArch3()){
            $arch=$this->miForm->getDataArch(); 
                    
        }
		$this->miForm->procesar(); 
 

        if($_POST["action"]){         
			if(isset($_POST["f"])){
					$s="update mm_coti_contenido set foto=''  where idContenido='".$id."'";
					mysqli_query($this->link,$s);					
			}
		
			$cadena ="update mm_coti_contenido set";
			if(isset($arch) && count($arch)!=0){            
            	$cadena.=" foto='".$arch[0]."',";
            } 
            $cadena.=" titulo='".$_POST["titulo"]."',texto='".$_POST["des"]."' where idContenido='".$id."'";
			  
			mysqli_query($this->link,$cadena) or die(mysqli_error($this->link));
			header("location:panel.php?mod=panel&op=19&mq=editar&idq=".$id."&msg=1");
			exit;
        }
        $this->miForm->cerrarForm();
        return(true);
	}
	public function devolverSub($id){
		$this->link=$this->conectar();
		$sql="select* from mm_submenu where idSub='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		return($r["nombre"]);
	}
	public function ingresarContenido(){
		$this->link=$this->conectar();
		if(isset($_GET["msg"])){
            $msg=htmlentities($_GET["msg"]);
            if($msg==1){
				$this->miForm->addAlert("Información del sistema","Contenido ha sido ingresada con exito !!",1);
			}
        }     
        $this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);        
         echo "<div style='margin-bottom:20px;'>";
		echo "<span style='font-size:18px;'>Ingresar Contenido</span>";
		echo "</div>";    
		$this->miForm->addText("titulo",20,"titulo","Titulo :",false);   
        $arrSel=array(1=>"Empresa",2=>"Servicios");
        $this->miForm->addSelect("seccion",$arrSel,"Seleccione...",false,"Sección");
        //$this->miForm->addTextarea("des",70,20,"descripcion","Descripci�n :",false);	
		echo ' <textarea name="des">'.$row["texto"].'</textarea>
		<script>
				CKEDITOR.replace( "des" );
		</script>';
		 $this->miForm->addFile(1,"Subir Imagenes",$editarImagen);     
		 $this->miForm->addHidden("action","true");  
		 $this->miForm->addButton("enviar","Ingresar contenido",false,$borrar=false,"primary","far fa-check-circle",true,false,"lg");                
		
		  if($this->miForm->procesarArch3()){
            $arch=$this->miForm->getDataArch(); 
                    
        }
        $this->miForm->procesar(); 
        if($_POST["action"]){     
           
           $cadena=$this->sql->sqlIngresarContenido($_POST,$arch);
		   
		   mysqli_query($this->link,$cadena) or die(mysqli_error($this->link));
		    header("location:?msg=1");
		   exit;
        }
        $this->miForm->cerrarForm();
        return(true);
	}
	  
	 
	 
	public function modificarCiudad($id){
		$this->link=$this->conectar();
		if(isset($_GET["msg"])){
            $msg=htmlentities($_GET["msg"]);
            if($msg==1){
				$this->miForm->addAlert("Información del sistema","Cambios han sido guardados con exito !!",1);
			}
        }     
		$cadena=$this->sql->sqlConsultarIdCiudad2($id);		 
		$query1=mysqli_query($this->link,$cadena);
		$row=mysqli_fetch_array($query1);
		$this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);  
                	
        $this->miForm->addText("ciudad",350,"ciudad","Ciudad :",$row["ciudad"]); 
        $this->miForm->addHidden("action","true");         
        $this->miForm->addButton("enviar","Guardar Cambios",false,$borrar=false,"primary","far fa-check-circle",true,false,"lg");            
		$this->miForm->procesar();     
 
        if($_POST["action"]){
           $cadena=$this->sql->sqlActualizarCiudad2($_POST,$id);		 
           mysqli_query($this->link,$cadena);
		   header("location:panel.php?mod=panel&op=6&mq=editar&idq=".$id."&msg=1");		   
		   exit;
        }
        $this->miForm->cerrarForm();
        return(true);
	}
	public function ingresarCiudad(){
		$this->link=$this->conectar();
		if(isset($_GET["msg"])){
            $msg=htmlentities($_GET["msg"]);
            if($msg==1){
				$this->miForm->addAlert("Información del sistema","Ciudad ha sido ingresada con exito !!",1);
			}
        }     
        $this->miForm->abrirForm(1,true,"form1","post","proceso.php",2);
		$this->miForm->addText("ciudad",350,"ciudad","Ciudad :",false); 
        $this->miForm->addHidden("action","true");  
		$this->miForm->addButton("enviar","Agregar Ciudad",false,$borrar=false,"primary","far fa-check-circle",true,false,"lg");            
		$this->miForm->procesar();
        if($_POST["action"]){
           $cadena=$this->sql->sqlIngresarCiudad2($_POST);
           mysqli_query($this->link,$cadena);
		   header("location:panel.php?mod=panel&op=5&msg=1");		   
		   exit;
        }
        $this->miForm->cerrarForm();
        return(true);
	}
	 
	
	public function monitorVisitas(){
			$this->monitor=new monitor();
			$this->monitor->desplegar();
	}
 	
 	
	 public function procesarForm(){
		require_once("./clases/class.upload.php");
		$this->link=$this->conectar();
        
        // retrieve eventual CLI parameters
        if ((isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '')) == 'multiple') {
		
    // ---------- MULTIPLE UPLOADS ----------

    // as it is multiple uploads, we will parse the $_FILES array to reorganize it into $files
    $files = array();
    foreach ($_FILES['my_field'] as $k => $l) {
        foreach ($l as $i => $v) {
            if (!array_key_exists($i, $files))
                $files[$i] = array();
            $files[$i][$k] = $v;
        }
    }
 
        $dir_dest="./upload/";
           
    // now we can loop through $files, and feed each element to the class
    $k=0;
    foreach ($files as $clave=>$file) {
    	 
 	 $k++;
        // we instanciate the class for each element of $file
		$handle = new Upload($file);

		

        // then we check if the file has been uploaded properly
        // in its *temporary* location in the server (often, it is /tmp)
        if ($handle->uploaded) {

            
        // yes, the file is on the server
        // below are some example settings which can be used if the uploaded file is an image.
		/* PRIMAERA IMAGEN LA GRANDE */
		
		$fecha=date("mdY-H_i_s");
		$_SESSION["fecha"]=$fecha;
        $handle->image_resize            = true;
		$handle->image_ratio_y           = true;
        $handle->image_x= 422;
		$handle->image_y = 161;
		$handle->file_new_name_body = $_SESSION["fecha"];
		$handle->auto_create_dir = true;
		$handle->dir_auto_chmod = true;
		$handle->dir_chmod = 0777;
		$handle->image_convert = 'jpg';
		$handle->jpeg_quality = 90;	
	 $handle->Process("./imagen/");
	 
 	$arch[$clave]=$handle->file_dst_name;
        }
    }
 
return($arch);
} 
    }
public function configGeneral(){
	$this->link=$this->conectar();
	if(isset($_GET["msg"])){
		$msg=htmlentities($_GET["msg"]);
		if($msg==1){
			$this->miForm->addAlert("Información del sistema","Cambios han sido guardado con exito !!",1);
		}

	}     
		$sql="select* from  mm_coti_datos where idDatos=1";
		$q=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		$row=mysqli_fetch_array($q);
 
		if(isset($_POST["subir"])){
				 $arch=$this->procesarForm(); 
			 
				$sql="update mm_coti_datos set 
				nombreEmpresa='".$_POST["autor"]."',
				direccion='".addslashes($_POST["direccion"])."',
				telefono='".$_POST["telefono"]."',
				telefono1='".$_POST["telefono1"]."',
				email='".$_POST["email"]."',
				email1='".$_POST["email1"]."',
				ftp='".$_POST["ftp"]."',
				discado='".$_POST["discado"]."',
				enlaceIndicador='".$_POST["enlaceIndicador"]."',
				titulo='".$_POST["titulo"]."',
				des='".$_POST["des"]."',
				whatsApp ='".$_POST["wasap"]."',
				facebook='".$_POST["facebook"]."',
				twitter='".$_POST["twitter"]."',
				youtube='".$_POST["youtube"]."',
				metatag='".$_POST["metatag"]."'";
				
			
				
				if(!empty($arch[0])){
					$sql.=", logo='".$arch[0]."'";
				}
			$sql.=" where idDatos=1";
	 
		 mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		 
		 
		 header("location:panel.php?mod=panel&op=9&msg=1");
		  exit;
		}
		echo "<form method='post'  enctype='multipart/form-data' name='form1' id='form1'>";
		echo "<div class='row' style=' margin-bottom:50px;'>";
		echo "<div class='col-md-7'>";
		echo "<div style='margin-bottom:20px;'>";
		echo '<span class="glyphicon glyphicon-edit"></span><span style="font-size:20px;"> Información de su empresa</span>';
		echo "</div>";
		echo "<div>";
		echo "Nombre Comercial";
		echo "</div>";
		echo "<div>";		
		echo '<input placeholder="Nombre Comercial" style="margin-top:5px; margin-bottom:5px;" name="autor" class="form-control input-sm"   type="text" id="autor"  value="'.$row["nombreEmpresa"].'" size="60">';
		echo "</div>";
		echo "<div>Dirección</div>";		
		echo "<div>";
		echo '<textarea class="form-control input-sm" style="margin-top:5px; margin-bottom:5px;" name="direccion" id="direccion" >'.$row["direccion"].'</textarea>';
		echo "</div>";
		
		echo "<div>Email 1</div>";
		echo "<div>";		
		echo '<input placeholder="Email" name="email" class="form-control input-sm" style="margin-top:5px; margin-bottom:5px;"  type="text" id="email"  value="'.$row["email"].'" size="60">';
		echo "</div>";

		echo "<div>Email 2</div>";
		echo "<div>";
		echo '<input placeholder="Email" name="email1" class="form-control input-sm" style="margin-top:5px; margin-bottom:5px;"  type="text" id="email1"  value="'.$row["email1"].'" size="60">';
		echo "</div>";


		echo "<div>Telefono 1</div>";
		echo "<div>";
		echo '<input placeholder="Telefono" name="telefono" style="margin-top:5px; margin-bottom:5px;" class="form-control input-sm"   type="text" id="telefono"  value="'.$row["telefono"].'" size="60">';
		echo "</div>";

		echo "<div>";
		echo "Telefono 2";
		echo "</div>";
		echo "<div>";
		echo '<input placeholder="Telefono" name="telefono1" style="margin-top:5px; margin-bottom:5px;" class="form-control input-sm"   type="text" id="telefono1"  value="'.$row["telefono1"].'" size="60">';
		echo "</div>";	
		
		echo "<div>";
		echo "Facebook";
		echo "</div>";
		echo "<div>";
		echo '<input placeholder="Facebook" name="facebook" class="form-control input-sm"  style="margin-top:5px; margin-bottom:5px;" type="text" id="facebook"  value="'.$row["facebook"].'" size="60">';
		echo "</div>";
		
		echo "<div>";
		echo "Twitter";
		echo "</div>";
		echo "<div>";
		echo '<input placeholder="Twitter" name="twitter" class="form-control input-sm"  style="margin-top:5px; margin-bottom:5px;" type="text" id="twitter"  value="'.$row["twitter"].'" size="60">';
		echo "</div>";
		
		echo "<div>";
		echo "Telefono botón de whatsApp";
		echo "</div>";

		echo "<div>";
		echo '<input placeholder="Telefono botón WhatsApp" name="wasap" class="form-control input-sm"  style="margin-top:5px; margin-bottom:5px;" type="text" id="wasap"  value="'.$row["whatsApp"].'" size="60">';
		echo "</div>";

		echo "<div>";
		echo "Telefono botón de discado";
		echo "</div>";

		echo "<div>";
		echo '<input placeholder="Telefono botón de discado" name="discado" class="form-control input-sm"  style="margin-top:5px; margin-bottom:5px;" type="text" id="discado"  value="'.$row["discado"].'" size="60">';
		echo "</div>";



		echo "<div>";
		echo "Instagram";
		echo "</div>";
		echo "<div>";
		echo '<input placeholder="Instagram" name="youtube" class="form-control input-sm"  style="margin-top:5px; margin-bottom:5px;" type="text" id="youtube"  value="'.$row["youtube"].'" size="60">';
		echo "</div>";

		echo "<div>";
		echo "Titulo de la página web";
		echo "</div>";
		echo "<div>";
		echo '<input placeholder="Titulo de la página web" name="titulo" class="form-control input-sm"  style="margin-top:5px; margin-bottom:5px;" type="text" id="titulo"  value="'.$row["titulo"].'" size="60">';
		echo "</div>";
		
		echo "<div>Descripción de la página</div>";
		echo "<div>";		
		echo '<textarea rows="4" style="margin-top:5px; margin-bottom:5px;" class="form-control input-sm" name="des" id="des" >'.$row["des"].'</textarea>';
		echo "</div>";



		echo "<div>Metatags</div>";
		echo "<div>";		
		echo '<textarea rows="10" style="margin-top:5px; margin-bottom:5px;" class="form-control input-sm" name="metatag" id="metatag" >'.$row["metatag"].'</textarea>';
		echo "</div>";
	
		echo "<div>";
		echo "Subir fotos vía";
		echo "</div>";


		echo "<div>";
		echo '<select name="ftp" id="ftp" class="form-control form-control-sm">';
		if($row["ftp"]==0){
			echo "<option value='0' selected='selected'>VIA WEB</option>";
			echo "<option value='1'>VIA FTP (File Transport Protocol)</option>";
			echo "<option value='2'>AMAZON AWS S2 CLOUD</option>";
		}else if($row["ftp"]==2){
			echo "<option value='1'>VIA FTP (File Transport Protocol)</option>";
			echo "<option value='0'>VIA WEB</option>";
			echo "<option value='2' selected='selected'>AMAZON AWS S2 CLOUD</option>";
		}else{
			echo "<option value='1' selected='selected'>VIA FTP (File Transport Protocol)</option>";
			echo "<option value='0'>VIA WEB</option>";
			echo "<option value='2'>AMAZON AWS S2 CLOUD</option>";
		}	
		echo "</select>";
		echo "</div>";

		echo "<div>";
		echo "Feed indicadores economicos";
		echo "</div>";
		echo "<div>";
		echo '<input placeholder="Feed indicadores economicos" name="enlaceIndicador" class="form-control input-sm"  style="margin-top:5px; margin-bottom:5px;" type="text" id="enlaceIndicador"  value="'.$row["enlaceIndicador"].'" size="60">';
		echo "</div>";	
		
		
	
		echo "<div>";
		echo "<input type='submit'  style='margin-top:10px; margin-bottom:10px;' name='guardar' id='guardar' value='Guardar Cambios' class='btn btn-primary btn-sm'/>";
		echo "</div>";

		echo "</div>";
		
		
		echo "<div class='col-md-5'>";
		echo "<div style='margin-bottom:20px;'>";
		echo '<span class="glyphicon glyphicon-picture"></span> <span style="font-size:20px;">Logo</span>';
		echo "</div>";
		
		echo "<div class='thumbnail'>";
		echo "<img src='./imagen/".$row["logo"]."' class='img-responsive'/>";
		echo "</div>";
		echo "<div>";
		echo '<p><input type="file" size="32" name="my_field['.$id.']" value="" /></p>';
					echo "<input type='hidden' name='subir' value='true'/>";
						echo '<input type="hidden" name="action" value="multiple" />';
		echo "</div>";
		echo "</div>";
		
		echo "</div></form>";
	}	 
	
	public function tablaCiudad(){
		$this->link=$this->conectar();
		if(isset($_GET["msg"])){
			$msg=htmlentities($_GET["msg"]);
		   if($msg==1){
			  $this->miForm->addAlert("Información del sistema","Propiedad se ha eliminado con exito!!",1);
		   }
		}
		 /* FALTA LA OPCION MODIFICAR*/
          if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
        if($op=="editar"){
            $id=htmlentities($_GET["idq"]);   
            //$this->miAvisos->modificarCarroEmpresa($id);
            $this->modificarCiudad($id);
        }else if($op=="borrar"){
            $id=htmlentities($_GET["idq"]);            
        
          $cad=$this->sql->sqlBorrarCiudad($id);		  
            mysqli_query($this->link,$cad);
            header("location:panel.php?mod=panel&op=6&msg=2");
            exit;
        }else{
        $index="panel.php?mod=panel&op=6";
         
        $sql="select* from mm_ciudad order by idCiudad desc";
  
		$campos=array("idCiudad"=>"id",
		"ciudad"=>"ciudad"); 
        $tamCol=array(1,100);
        
     //   $opciones=array("numClick"=>array(0=>"santiago",2=>"peru"));
        
       
       // $campoFoto=array("ruta"=>true);        
        $campoIndice="idCiudad";            
        $grid=new miniGrid(10,$index,$campoIndice,false,$opciones);
        $tabla="mm_ciudad";
        $grid->asignarCampos($campos,$tamCol,$sql,$tabla);
     
        $grid->desplegarDatos();
        
        }
        return(true);
	}
	public function totalFotos($idProp){
		$this->link=$this->conectar();
		$sql=$this->sql->sqlTotalFotos($idProp);
		$query=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		$row=mysqli_fetch_array($query);
		return($row["total"]);
		
	}
	 
	
	public function devolverFoto($idProp){
		$this->link=$this->conectar();
		$s="select* from mm_coti_fotos where idProp='".$idProp."'";
		$q=mysqli_query($this->link,$s);
		$r=mysqli_fetch_array($q);
		$foto=$r["ruta"];
		return($foto);
	}



	public function edicionFoto($id){
		$this->link=$this->conectar();
		echo "<form name='form1' id='form1' enctype='multipart/form-data' method='post' action=''>";   
		if(isset($_GET["idFoto"])){
			$idFoto=$_GET["idFoto"];
		}
		$sql="select* from mm_cape_fotos where idFoto='".$idFoto."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		if(isset($_GET["msg"]) && $_GET["msg"]==3){
			$this->msgBox=new msgBox(1,"Imagen se ha actualizado con exito !!!");
		}
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<div><h5>Fotografia</h5></div>";
		echo "<div>";
		echo "<div><img src='./upload/".$r["ruta"]."' style='width:30%;max-width:70%'/></div>";
		echo "</div>";
		echo "<div style='margin-top:10px;'>";
		echo "<span><b>Orientar Foto original</b></span>";		
		echo "</div>";
		echo "<div>";
		echo "<select name='giro' id='giro' class='form-control input-sm'>";
		echo "<option value='0' selected='selected'>Seleccione Orientación para que la foto quede derecha</option>";
		echo "<option value='1'>Girar 180° a la izquierda</option>";
		echo "<option value='2'>Girar 270° hacia arriba</option>";		
		echo "<option value='3'>Girar 90° a la izquierda abajo</option>";		
		echo "</select>";
		echo "</div>";
		echo "<div style='margin-top:10px;'><b>Subir nuevamente la misma fotografia</b></div>";
		echo "<div><input type='hidden' name='action' id='action' value='true'>";

		echo "<div>";
		$this->miForm->addFile(1,"Foto",$editarImagen);    
		echo "</div>"; 
		echo "<div style='margin-top:20px; margin-bottom:50px;'>";
		echo '  <button type="submit" name="action" id="action" class="btn btn-primary">Guardar Cambios</button>';
		echo "</div>";
        $this->miForm->procesar();
        if($this->miForm->procesarArch2()){
            $arch=$this->miForm->getDataArch();                     
		}
		if(isset($_POST["action"])){			 
			$sql="update mm_cape_fotos set ruta='".$arch[0]."' where idFoto='".$idFoto."'";
			mysqli_query($this->link,$sql);
			header("location:panel.php?mq=galeria&mp=ed&mod=panel&op=4&idq='.$id.'&idFoto=".$idFoto."&msg=3");
			exit;
			
		}
		echo "</div>";

		echo "</div>";
		echo "</div>";
		echo "</form>";
		 
	}
	public function galeria($id){
		
		$this->link=$this->conectar();
		echo "<form name='form1' id='form1' enctype='multipart/form-data' method='post' action=''>";  
		if(isset($_GET["mp"])){
			$this->edicionFoto($id);
		}else{
		$sql="SELECT * FROM `mm_propiedad` where idProp='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);

		$sql2=$this->sql->sqlConsultarIdFotos($id);
		
		$q1=mysqli_query($this->link,$sql2);
		$total=mysqli_num_rows($q1);
		$err=$this->miForm->getDataErr();

		if(count($err)!=0){
			$this->msgBox=new msgBox(3,"Tiene fotografias no aptas para la página web, para correjir las fotos vaya a galeria y edite las imagenes");
		}
		if(isset($_GET["msg"]) || $_GET["msg"]==1){
			$this->msgBox=new msgBox(1,"Se han guardado los cambios con exito !!!");
		}
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<div><h3>".$r["titulo"]."</h3></div>";
		echo "<div style='margin-bottom:20px; '>Se han publicado ".$total." fotos</div>";
		echo "</div>";
		echo "</div>";
		echo "<div class='row'>";
	
		while($r2=mysqli_fetch_array($q1)){
			echo "<div class='col-md-2'>";
			echo "<div style='margin-bottom:20px;'>";
			echo "<div><img src='./upload/".$r2["ruta"]."' style='max-width:100%;'/></div>";
			echo "<div><input type='checkbox' name='c1[]' id='c1[]' value='".$r2["idFoto"]."'/>Borrar</div>";
			echo "<div><a href='panel.php?mq=galeria&mp=ed&mod=panel&op=4&idq=".$id."&idFoto=".$r2["idFoto"]."' role='button' class='btn btn-primary btn-sm'>Editar Foto</a></div>";
			echo "</div>";
			echo "</div>";
		}
		echo "</div>";

		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<div><input type='hidden' name='action' id='action' value='true'>";
		echo "<div><hr></div>";
		echo "<div><h3>Subir Fotos a esta propiedad </h3></div>";
		echo "<div>";
		$this->miForm->addFile(12,"Foto",$editarImagen);    
		echo "</div>"; 
	

		echo "<div style='margin-top:20px; margin-bottom:50px;'>";
		echo '  <button type="submit" name="action" id="action" class="btn btn-primary">Guardar Cambios</button>';
		echo "</div>";
        $this->miForm->procesar();
        if($this->miForm->procesarArch()){
            $arch=$this->miForm->getDataArch();                     
		}
		if(isset($_POST["action"])){			 
			if(isset($_POST["c1"])){
				$lista=implode(",",$_POST["c1"]);
				$sql1="delete from mm_cape_fotos where idFoto in (".$lista.")";
			 
				mysqli_query($this->link,$sql1) or die(mysqli_error($this->link));
			}
			if(count($arch)!=0){
			 foreach($arch as $clave=>$valor){
				$sql="insert into mm_cape_fotos (idProp,ruta) values('".$id."','".$valor."');";				
				mysqli_query($this->link,$sql);
			 }
		
			}
			header("location:panel.php?mq=galeria&mod=panel&op=4&idq=".$id."&msg=1");		 	
			exit;
		}
		echo "</div>";
		echo "</div>";
		echo "</form>";
	}
 
	}
 
	public function agregarFotos($id){
		
		$this->link=$this->conectar();
		$sql="select titulo from mm_propiedad where idProp='".$id."'";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
	 	echo '<form method="post" action="" enctype="multipart/form-data">';
	

		echo "<div><h3>".$r["titulo"]."</h3></div>";
		echo "<div><hr></div>";
		echo "<div>";
		echo "<div><h5>Seleccione las fotos que desea subir</h5></div>";
		echo "<div><span style='font-size:12px;'>Para seleccionar varias fotos seleccione con el puntero del mouse las fotos manteniendo presionada la tecla ctrl de su teclado</span></div>";
		echo "<div style='margin-top:40px;'>";
		echo '
			  <input type="hidden" name="MAX_FILE_SIZE" value="2000000000" />
			  Subir imagen: <input type="file" name="file[]" multiple />		
			  
		 ';
			  echo "</div>";
		echo "</div>";
		echo "<hr>";
		echo "<div style='margin-top:40px;'>";
		echo "<div style='margin-top:20px; margin-bottom:20px;'><h5>Fotos de la propiedad</h5></div>";
		if(isset($_POST["subir"])){
 
			if(isset($_POST["c1"])){
				$lista=implode(",",$_POST["c1"]);
				$sql1="delete from mm_cape_fotos where idFoto in (".$lista.")";
				mysqli_query($this->link,$sql1) or die(mysqli_error());
			}
			
			if(!empty($_FILES["file"]["name"][0])){			
			if (isset($_FILES["file"]))
		  {
			 $reporte = null;
			   for($x=0; $x<count($_FILES["file"]["name"]); $x++)
			  {
				$file = $_FILES["file"];
				$nombre = $file["name"][$x];
				$tipo = $file["type"][$x];
				$ruta_provisional = $file["tmp_name"][$x];
				$size = $file["size"][$x];
				$dimensiones = getimagesize($ruta_provisional);
				$width = $dimensiones[0];
				$height = $dimensiones[1];
				$carpeta = "./upload/";
	 
				 
					$src = $carpeta.$nombre;
					move_uploaded_file($ruta_provisional, $src);  
					$sql="insert into mm_cape_fotos (idProp,ruta) values ('".$id."','".$nombre."')";

					mysqli_query($this->link,$sql);

				
			  }
		
		  }
		  }
		  header("location:panel.php?mq=galeria&mod=panel&op=4&idq=".$id."&msg=2");
		  exit;
		  if(isset($_GET["msg"])){
		  		echo '<div class="alert alert-primary" role="alert">
		  				Los Cambios se han realizado con exito !!</div>';		  
		  		}
			}
		 	 
		  $sql2="select * from mm_cape_fotos where idProp='".$id."' order by idFoto desc";
	 
		  
		  $q2=mysqli_query($this->link,$sql2);
		  $numCol=5;
		  $k=0;
		  echo "<table width='80%' border=0>";
		  echo "<tr>";
		  while($r=mysqli_fetch_array($q2)){
			$k++;
			echo "<td>";
			echo "<div>";
			echo "<img src='./upload/".$r["ruta"]."' style='width:180px; height:150px;'/>";
			echo "</div>";
			echo "<div>";
			echo "<input type='checkbox' onclick='k();' name='c1[]' id='c1a' value='".$r["idFoto"]."'/><span style='font-family:arial; font-size:12px;'>&nbsp;Borrar</span>";
			echo "</div>";
			
			echo "</td>";
			if($k%$numCol==0){
			  echo "</tr>";
			}
		  }
		  echo "</table>";
		  
		echo "</div>";
		echo "<div style='margin-top:40px;'>";
		echo '<input type="submit" id="subir" name="subir" value="Guardar Cambios" class="btn btn-primary btn-sm" />';
		echo "</div>";
		echo "</form>";
	}
	public function agregarFotosFtp($id,$a,$b){
	 
		$temp=new propTemp();
		$temp->agregarFotos($id,$a,$b);
	}


	
 	public function tablaPropiedades(){ 
		
		$this->link=$this->conectar();	 
 		 if(isset($_GET["msg"])){
 		 	$msg=htmlentities($_GET["msg"]);
			 if($msg==1){
				$this->miForm->addAlert("Información del sistema","Propiedad se ha eliminado con exito!!",1);
			 }else{
				$this->miForm->addAlert("Información del sistema","Cambios se han realizado con exito!!",1);
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
            header("location:panel.php?mod=panel&op=4&msg=2");
            exit;
        }else if($op=="editarFotos"){
        	if(isset($_GET["idq"])){$idq=htmlentities($_GET["idq"]);}
        	$this->tablaFotos($idq);
		 
			 
        }else{
        $index="panel.php?mod=panel&op=4";
		
		if(isset($_POST["palabra"]) && !empty($_POST["palabra"])){
			$sql='SELECT * FROM `mm_propiedad` WHERE titulo like "%prueba%" and papelera=0 order by idProp desc';			
		}else if(isset($_POST["filtrar"]) && !empty($_POST["filtrar"])){
			$idFiltrar=htmlentities($_POST["filtrar"]);
			if($idFiltrar==1){
				$sql="SELECT * FROM `mm_propiedad` where papelera=0 order by idProp desc";
			}else{
				$sql='SELECT * FROM `mm_propiedad` WHERE idCorredora="'.$idFiltrar.'"and papelera=0 order by idProp desc';
			}
			
		}else{
			if(isset($_GET["v"]) && $_GET["v"]==1){
				$sql="SELECT * FROM `mm_propiedad` where papelera=1 order by idProp desc";
			 }else{
				$sql="SELECT * FROM `mm_propiedad` where papelera=0 order by idProp desc";
			 }
		}
  
      
 
     
        $campos=array("ruta"=>"foto",
        			  "titulo"=>"Titulo",
           		      
        			  "precio"=>"Precio" 

		); 
		$tamCol=array(1,65,3,5,2,15,55);
        
        
        
					 
        
       
        $campoFoto=array("ruta"=>true);        
        $campoIndice="idProp";            
        $grid=new miniGrid(10,$index,$campoIndice,$campoFoto,$opciones);
       
		$tabla="mm_propiedad";
   
        $grid->asignarCampos($campos,$tamCol,$sql,$tabla);
		 
     
		
		 
		echo "<form name='form1' id='form1' method='post' action=''>";
        $grid->desplegarDatos();
		echo "</form>";
     
        }
        return(true); 
 	}
	 public function agregarFotosAws($idProp){
		$aws=new aws();
		$aws->subirFotos();

	 }
	 public function panelBoot(){
			 if(isset($_GET["mod"]) && isset($_GET["op"])){
	 	 $modo=htmlentities($_GET["modo"]);
		 $op=htmlentities($_GET["op"]);
		 switch($op){
		 	case 1:		 
				if(isset($_GET["id"])){
					$id=htmlentities($_GET["id"]);
				}
				if($id==1){
					$this->modificarContenido(1);
				}else if($id==2){
					$this->modificarContenido(2);
				}			 
				break;
			case 2:
				$this->tablaContenido();
				break;			
			case 4:
			if(isset($_GET["m"])){
			 echo "<div style='margin-left:20px;margin-top:20px; margin-bottom:20px;'>";
				$this->ingresarPropiedad();
				echo "</div>";
			}else{
				$this->tablaPropiedades();
			}
				break;
			case 5:
				$this->ingresarCiudad();				
				break;
			case 6:
			if(isset($_GET["mq"]) && $_GET["mq"]=="editar"){
				$id=$_GET["idq"];
				 echo "<div style='margin-top:20px; margin-bottom:50px;'>";
				$this->modificarCiudad($id);
				echo "</div>";
			}else if(isset($_GET["mq"]) && $_GET["mq"]=="borrar"){
				 $id=htmlentities($_GET["idq"]);            
        
				$cad=$this->sql->sqlBorrarCiudad($id);		  
				mysqli_query($this->link,$cad);
				header("location:panel.php?mod=panel&op=6&msg=2");
				exit;
			}else{
				if(isset($_GET["m"])){
					echo "<div style='margin-top:20px; margin-bottom:50px;'>";
					$this->ingresarCiudad();
					echo "</div>";
				}else{
					echo "<div style='margin-top:20px; margin-bottom:50px;'>";
					$this->tablaCiudad();
					echo "</div>";
				}
			}
				break;			
			case 9:
				
				$this->configGeneral();
				break;
			
			case 11:
				$this->rotador=new rotador();
				$this->rotador->tablaRotador();
				break;
			case 61:
				$this->rotador=new rotador();
				$this->rotador->subirRotador();
				break;
			
			case 14:
			echo "<div style='margin-top:30px;margin-bottom:30px;'>";
				$this->monitorVisitas();
				echo "</div>";
				break;	
			
			case 17:
				$this->agregarContenido();
				break;
			case 18:
			 if(isset($_GET["msg"])){
				if($_GET["msg"]==1){
				$this->msgBox=new msgBox(1,"Se agregado opción al menu !!!");
				}else if($_GET["msg"]==11){
					$this->msgBox=new msgBox(1,"Opción del menu se ha modificado con exito!!!");
				}else if($_GET["msg"]==6){
					$this->msgBox=new msgBox(1,"Se han guardado los cambios con exito!!!");
				}else if($_GET["msg"]==7){
					$this->msgBox=new msgBox(1,"Opción se ha borrado con exito!!!");
				}
			}
			if(isset($_GET["sb"])){	
			
				$idSub=$_GET["idq"];
				if($_GET["mq"]=="editar"){
				echo '<div class="panel panel-default"><div class="panel-body">';
				$this->modificaSubMenu($idSub);
				echo "</div></div>";
				}else{
					$sql1="delete from mm_submenu where idSub='".$idSub."'";
				
					mysqli_query($this->link,$sql1) or die(mysqli_error($this->link));
				 
					header("location:panel.php?mod=panel&op=18&msg=7");
					exit;
				}
			}else{
			if(!isset($_GET["mq"]) || $_GET["mq"]!="editar"){
				
				echo '<div class="panel panel-default" style="margin-top:20px;margin-bottom:20px;"><div class="panel-body">';
				$this->agregarCategoriaOpcion();
				echo "</div></div>";
			}
				echo "<div>";
				$this->tablaCategoriaOpcion();
				echo "</div>";
			}
				break;	
			case 19:
		 	echo "<div style='margin-top:20px; margin-bottom:50px;'>";
			if(isset($_GET["m"])){
				$this->agregarContenido();
			}else{
				$this->tablaContenido();
			}
			echo "</div>";
				break;
			case 20:
				$this->tablaCategoriaOpcion();
				break;
			case 89:
				$id=$_GET["idq"];
				$this->modificarContenido($id);
				break;	
			case 91:
				$id=$_GET["idq"];
				$this->modificarCategoriaOpcion($id);
				break;	
				case 244:
					$this->verLiscencia();
					break;	
			case 144:				 
				$this->verPerfilAdmin();
				break;
				case 445:
					if(isset($_GET["m"]) && $_GET["m"]=="add")		{
						$this->ingresarEntrada();
					}else{
						$this->tablaEntradas();
					}
					break;
			case 245:				 
					$this->seguridad();
					break;
			case 14451:
					if(isset($_GET["m"])){
						$m=htmlentities($_GET["m"]);
						if($m=="add"){
							$this->listaAgentes();
						}else{
							echo "lista";
						}
					}
					break;
			case 92:
				$id=$_GET["idq"];
				    if(isset($_GET["msg"])){
						$msg=htmlentities($_GET["msg"]);
						if($msg==1){
							$this->msgBox=new msgBox(1,"Se han guardado los cambios con exito!!");
						}
					}
					echo "<div style='margin-top:40px; margin-bottom:40px;'>";
					 echo '<section class="content-header">
			<ol class="breadcrumb">
			  <li><a href="panel.php">Inicio</a></li>
			  <li><a href="panel.php?mod=panel&op=18">menu</a></li>
			  <li class="active">Agregar SubMenu</li>
			</ol>
			</section>';
					echo '<div class="panel panel-default"><div class="panel-body">';
				 $this->agregaSubMenu();
				 echo "</div></div>";
				 echo "</div>";
				break;	
		 }
	 }else{
		if(!isset($_SESSION["auth"]["usuario"])){
			header("location:login.php");
			exit;
		}
		 
	}
 }
 
 public function devolverCorredora($id){
	$this->link=$this->conectar();
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
public function devolverFecha($idReg){
	$sql="select fecha from registro where idReg='".$idReg."'";
	$q=mysqli_query($this->link,$sql);
	$r=mysqli_fetch_array($q);
	
	if($r["fecha"]==0){
		$f="";
	}else{
		$f=date("d-m-Y H:i:s",$r["fecha"]);
	}
	return($f);
}
public function listaAgentes() {
    $this->link = $this->conectar();
    echo "<div class='col-md-12'>";
    echo "<table class='table table-bordered'>";
    echo "<thead class='thead-light'>";
    echo "<tr><th style='padding: 0.3rem;'>Nombre</th><th style='padding: 0.3rem;'>Ultima Modificación</th><th style='padding: 0.3rem;'>Acciones</th></tr>";
    echo "</thead>";
    echo "<tbody>";

    $sql77 = "SELECT DISTINCT idCorredora FROM mm_propiedad ORDER BY idCorredora DESC";
    $q77 = mysqli_query($this->link, $sql77);

    while ($r99 = mysqli_fetch_array($q77)) {
        $idCorredora = $r99["idCorredora"];
	 
        $nombreCorredora = $this->devolverCorredora($idCorredora);
        if (!empty($idCorredora)) {
            // Fila principal de la corredora
            echo "<tr style='padding: 0.3rem;'>";
            echo "<td style='padding: 0.3rem;'>$nombreCorredora</td>";
			echo "<td>".$this->devolverFecha($idCorredora)."</td>";
            // Agrupación de botones
            echo "<td style='padding: 0.3rem;'>";
            echo "<div class='btn-group' role='group' aria-label='Acciones'>";
            echo "<button class='btn btn-sm btn-primary' id='toggle-$idCorredora' onclick='toggleProperties(this, \"$idCorredora\")'>";
            echo "<i class='fas fa-eye'></i> Ver publicaciones";
            echo "</button>";

            echo "<a href='#' class='btn btn-sm btn-warning' onclick='showPasswordModal(\"$idCorredora\")'>";
            echo "<i class='fas fa-lock'></i> Asignar contraseña";
            echo "</a>";
            echo "</div>";
            echo "</td>";
		 

            echo "</tr>";

            // Subtabla con las propiedades
            echo "<tr class='collapse' id='properties-$idCorredora'>";
            echo "<td colspan='2'>";
            echo "<div class='loading' id='loading-$idCorredora' style='display:none;'>Cargando propiedades...</div>";
            echo "<table class='table table-sm'>";
            echo "<thead><tr><th style='padding: 0.3rem;'>Propiedad</th></tr></thead>";
            echo "<tbody>";

            // Consulta para obtener las propiedades de la corredora
            $sqlPropiedades = "SELECT tipoProp,operacion,fecha,titulo,descripcion FROM mm_propiedad WHERE idCorredora = '$idCorredora'";
            $qPropiedades = mysqli_query($this->link, $sqlPropiedades);

            while ($prop = mysqli_fetch_array($qPropiedades)) {
                echo "<tr style='padding: 0.3rem;'>";
                
                echo "<td>
				<div style='padding:10px;'>
				<div style='font-size:14px;'><b>".$prop["titulo"]."</b></div>
				<div><span style='font-size:14px;'><i class='fas fa-calendar-alt'></i> ".date("d-m-Y H:i:s",$prop["fecha"])." | Tipo :".$this->devolverTipoProp($prop["tipoProp"])." | Operación : ".$this->devolverOperacion($prop["operacion"])."</span></div>
				<div style='padding-top:10px;'>".$prop["descripcion"]."</div>
				</div>
				</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
            echo "</td>";
            echo "</tr>";
        }
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    // JavaScript para manejar el botón y la visibilidad de la subtabla
    echo "<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>";
    echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js'></script>"; // Asegúrate de incluir Bootstrap JS

    echo "<script>
        function toggleProperties(button, idCorredora) {
            const propertiesRow = document.getElementById('properties-' + idCorredora);
            const loading = document.getElementById('loading-' + idCorredora);
            const isExpanded = propertiesRow.classList.contains('show');

            if (isExpanded) {
                propertiesRow.classList.remove('show');
                button.innerHTML = '<i class=\"fas fa-eye\"></i> Ver publicaciones';
                loading.style.display = 'none';
            } else {
                loading.style.display = 'block';
                setTimeout(() => {
                    propertiesRow.classList.add('show');
                    button.innerHTML = '<i class=\"fas fa-times\"></i> Cerrar';
                    loading.style.display = 'none';
                }, 500);
            }
        }

        function showPasswordModal(idCorredora) {
            const modal = document.getElementById('passwordModal');
            modal.style.display = 'block';
            document.getElementById('corredoraId').value = idCorredora;
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

        function assignPassword() {
            const idCorredora = document.getElementById('corredoraId').value;
            const newPassword = document.getElementById('newPassword').value;

            if (newPassword) {
                $.ajax({
                    type: 'POST',
                    url: './include/proceso.php',
                    data: {
                        idCorredora: idCorredora,
                        newPassword: newPassword
                    },
                    success: function(response) {
                        var toastEl = document.getElementById('liveToast');
                        var toast = new bootstrap.Toast(toastEl);
                        
                        // Cambiar el contenido del toast según la respuesta
                        if (response.indexOf('true') !== -1) {
                           alert('Contraseña se ha guardado con exito !!!');
                        } else {
                            alert('Contraseña no ha podido ser guardada');
                        }
                        
                        closePasswordModal();
                    },
                    error: function() {
                        var toastEl = document.getElementById('liveToast');
                        var toast = new bootstrap.Toast(toastEl);
                        toastEl.querySelector('.toast-body').innerText = 'Error al actualizar la contraseña. Inténtalo de nuevo.';
                        toast.show();
                    }
                });
            } else {
                alert('Por favor, ingresa una nueva contraseña.');
            }
        }
    </script>";

 

    // Estilos CSS para efectos visuales
    echo "<style>
        .collapse.show {
            transition: max-height 0.5s ease-in-out;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .loading {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        #passwordModal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 300px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>";

    // Modal para asignar contraseña
    echo "<div id='passwordModal'>
        <div class='modal-content'>
            <span class='close' onclick='closePasswordModal()'>&times;</span>
            <h3>Asignar Contraseña</h3>
            <input type='hidden' id='corredoraId'>
            <label for='newPassword'>Nueva Contraseña:</label>
            <input type='password' id='newPassword' placeholder='Ingrese nueva contraseña'>
            <button onclick='assignPassword()' class='btn btn-sm btn-primary'>Actualizar Contraseña</button>
        </div>
    </div>";
}



	 public function leerDatos(){
		$this->link=$this->conectar();
		$sql="select* from mm_coti_datos";
		$query=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		$row=mysqli_fetch_array($query);
		return($row);
	}
	 public function devolverTelefono(){
		$this->link=$this->conectar();
	 	$sql="select* from mm_datoscontacto order by idContacto desc";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$telefono=$r["telefono"];
		 
	return($telefono);
		
	 }
	 public function devolverDatosPagina(){
		$this->link=$this->conectar();
		$sql="select* from mm_coti_datos";
		$q=mysqli_query($this->link,$sql);
		  
		while($r=mysqli_fetch_array($q)){
			$d["nombrePag"]=$r["nomEmpresa"];
		 	$d["celular"]=$r["celular"];
			$d["telefono"]=$r["telefono"]; 
			$d["email"]=$r["email"];
			$d["direccion"]=$r["direccion"];
			$d["facebook"]=$r["facebook"];
			$d["twitter"]=$r["twitter"];
			$d["google"]=$r["google"];
			$d["paint"]=$r["paint"];
			$d["linkedin"]=$r["linkedin"];
			$d["youtube"]=$r["youtube"];
		 	$d["nomEmpresa"]=$r["nombreEmpresa"]; 
		 	$d["horario"]=$r["horario"];
		 }
		 return($d);
	}
	
public function tablaCategoriaOpcion(){
	$this->link=$this->conectar();
        $sql="select* from mm_coti_categoria order by idCategoria asc";  
        $campos=array("nombre"=>"Menu Opción"); 
          /* FALTA LA OPCION MODIFICAR*/
          if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
	  
        if($op=="editar"){
        	 $this->modificarCategoriaOpcion($_GET["idq"]);
            $id=htmlentities($_GET["idq"]);   
            //$this->miAvisos->modificarCarroEmpresa($id);
             
        }else if($op=="borrar"){
            $id=htmlentities($_GET["idq"]);           
             $cad="delete from mm_coti_categoria where idCategoria='".$id."'";
             mysqli_query($this->link,$cad);
             header("location:panel.php?mod=panel&op=20&msg=2");
            exit;
        }else{
 
        $tamCol=array(5,90);
        
     //   $opciones=array("numClick"=>array(0=>"santiago",2=>"peru"));
        
       
       // $campoFoto=array("foto"=>true);        
        $campoIndice="idCategoria";       
		$index="panel.php?mod=panel&op=18";     
        $grid=new miniGrid(10,$index,$campoIndice,false,$opciones);
        $grid->jquery();
        $grid->setColor(array("#f6f6f6","#efeded"));
        $grid->asignarConsulta($sql);
	 
        $grid->asignarCampos($campos,$tamCol);
         echo "<div style='margin-bottom:20px;'>";
		 echo "<table width='100%' border=0>";
		 echo "<tr><td>";
		echo "<span style='font-size:18px;'>Menu Página Web</span>";
		echo "</td><td align='right'>";
		echo "<input type='button' name='sub1' id='sub1' onclick='agregarSubMenu();' style='padding-left:10px; padding-right:10px; font-size:12px;' class='btn btn-primary btn-sm' value='Agregar SubMenu'>";
		echo "</td></tr>";
		echo "</table>";
		echo "</div>";
        $grid->desplegarDatosMenu();
        $grid->cerrarTabla();
        }
        return(true);
	}
	public function add_dashes( $data ) {
    $data = trim(strtolower( $data ));
    $data = explode(' ', $data);
    $data = implode('-', $data );
    return $data;
    }
    public function dolar(){
        $url = "http://www.terra.cl/valores/";
        $palabra = "DOLAR OBSERVADO";
        $x = 1; //evita tags <! (invisibles)
        
        $fd = @fopen($url, "r"); //abre la url y comienza desde el principio para solo lectura. Apertura para solo lectura; ubica el apuntador de archivo al comienzo del mismo.
        while ($line=@fgets($fd,1000)){
            $pos = strpos ($line, $palabra);
            if ($pos){
                $glosa = " ";
                $line2=fgets($fd,1000);
                echo "$ ".strip_tags($glosa.trim($line2));
            }           
        }   
        @fclose ($fd);
    }
    public function utm(){
        $url = "http://www.terra.cl/valores/";
        $palabra = "UTM :";
        $x = 1; //evita tags <! (invisibles)
        $fd = @fopen($url, "r"); //abre la url y comienza desde el principio para solo lectura. Apertura para s?lo lectura; ubica el apuntador de archivo al comienzo del mismo.
        while ($line=@fgets($fd,1000)){
            $pos = strpos ($line, $palabra);
            if ($pos){
                $glosa = " ";
                $line2=fgets($fd,1000);
                echo "$ ".strip_tags($glosa.trim($line2));
            }
        }
        @fclose ($fd);
    }
    public function uf(){
        $url = "http://www.terra.cl/valores/";
        $palabra = "UF : ";
        $x = 1; //evita tags <! (invisibles)
        
        $fd = @fopen($url, "r"); //abre la url y comienza desde el principio para solo lectura. Apertura para s?lo lectura; ubica el apuntador de archivo al comienzo del mismo.
        while ($line=@fgets($fd,1000)){
        $pos = strpos ($line, $palabra);
        if ($pos){
            $glosa = " ";
            $line2=fgets($fd,1000);
            echo "$ ".strip_tags($glosa.trim($line2));
        }
        }
        @fclose ($fd);
    }
	
	public function formatoNumerico($num){
		$n=number_format($num, 0,",",".");
		return($n);
	}
   
	public function tablaCategorias(){
		$this->link=$this->conectar();
		if(isset($_GET["msg"])){
			$this->msgBox=new msgBox(1,"Los cambios se han guardado con exito!!!");
		}
        $sql=$this->sql->sqlConsultarCoti_familia();   
        $campos=array("idFam"=>"Familia"); 
          /* FALTA LA OPCION MODIFICAR*/
          if(isset($_GET["mq"])){$op=htmlentities($_GET["mq"]);}
        if($op=="editar"){
        	 $this->modificarCategoria($_GET["idq"]);
            $id=htmlentities($_GET["idq"]);   
            //$this->miAvisos->modificarCarroEmpresa($id);
           // $this->modificarContenido($id);
        }else if($op=="borrar"){
            $id=htmlentities($_GET["idq"]);            
         
          $cad="delete from mm_coti_familia where idFam='".$id."'";
            mysqli_query($this->link,$cad);
             header("location:panel.php?mod=panel&op=13&msg=2");
            exit;
        }else{
        $index="panel.php?mod=panel&op=13";      
        $tamCol=array(60,90);
        
     //   $opciones=array("numClick"=>array(0=>"santiago",2=>"peru"));
        
       
       // $campoFoto=array("foto"=>true);        
        $campoIndice="idFam";       
		$index="panel.php?mod=panel&op=13";     
        $grid=new miniGrid(10,$index,$campoIndice,false,$opciones);
        $grid->jquery();
        $grid->setColor(array("#f6f6f6","#efeded"));
        $grid->asignarConsulta($sql);
        $grid->asignarCampos($campos,$tamCol);
        $grid->abrirTabla("Familias",true);
        $grid->desplegarDatos();
        $grid->cerrarTabla();
        }
        return(true);
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
		echo "<form method='post' name='form1' id='form1' action=''>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		$this->gMaps=new miniGmaps();
		$this->gMaps->jqueryMaps();
	   if(isset($_GET["msg"])){
		   $msg=htmlentities($_GET["msg"]);
		   if($msg==1){
			   $this->miForm->addAlert("Información del sistema","Propiedad ha sido ingresada con exito !!",1);
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
			  echo '<option value="'.$r["idRegion"].'">'.utf8_encode($r["nombre"]).'</option>';
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
	 
	 
	   $arrSel12=array(1=>"1 estacionamiento",2=>"2 o mas");
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
		<div id="map_canvas" style="margin-left:0px; margin-top:5px; margin-bottom:5px; width:100%; height:200px;"></div>';
	   echo "</div>";

	   
	   echo "</div></div>";
	 
	    echo "<div style='margin-top:10px;'>Descripción</div>";
	   echo "<div>";
	   echo ' <textarea name="des"></textarea>
	   <script>
			   CKEDITOR.replace( "des" );
	   </script>';	   

	   echo "</textarea>";
	   echo "</div>";


	   echo "<div><input type='hidden' name='action' id='action' value='true' class='form-control form-control-mb'/></div>";
	   echo "<div style='margin-top:10px;'><button role='button' id='s1' name='s1' class='btn btn-primary btn-sm'>Agregar Propiedad</button></div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
		echo "</div>";
		echo "</div>";
		echo "</form>";


		if(isset($_POST["action"])){	
		 	
			$sql=$this->sql->sqlIngresarVivienda($_POST);				 
		 	mysqli_query($this->link,$sql) or die(mysqli_error($this->link));	
			 $this->generaArrayXml();		
		    header("location:panel.php?op=4&m=add&mod=panel&msg=1");			 
			exit;		 	
		}
	}

	public function crearXml($urls){
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
		<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
	foreach ($urls as $url) {
    $xml .= "\n<url>";
    $xml .= "\n  <loc>" . htmlspecialchars($url['loc']) . "</loc>";
    $xml .= "\n  <lastmod>" . $url['lastmod'] . "</lastmod>";
    $xml .= "\n  <priority>" . $url['priority'] . "</priority>";
    $xml .= "\n</url>";
	}

	$xml .= "\n</urlset>";

	$file = 'sitemap.xml';
	file_put_contents($file, $xml); 
	}
	public function generaArrayXml(){
		$sql="select idProp from mm_propiedad where papelera=0 order by idProp desc";
		$q=mysqli_query($this->link,$sql);
		$fecha_actual = date('Y-m-d\TH:i:sP');	 
		$url=array(
			array('loc' => 'https://clickcorredores.cl/',
			'lastmod' => '2020-11-11T03:27:16+00:00',
			'priority' => '1.00'),
			array('loc' => 'https://clickcorredores.cl/index.php',
			'lastmod' => '2020-11-11T03:27:16+00:00',
			'priority' => '1.00') 
		);		
		while ($r = mysqli_fetch_array($q)) {
			// Agregar cada URL de propiedad al array $url
			$url[] = array(
				'loc' => 'https://clickcorredores.cl/index.php?mod=det&idProp=' . $r["idProp"],
				'lastmod' => $fecha_actual,
				'priority' => '1.00'
			);
		}	 
		$this->crearXml($url);
	return($url);
	}

	public function menuCategorias(){
		$this->link=$this->conectar();
		echo "<table width='100%' border=1>";
		echo "<tr><td><h3>Categorias</h3></td></tr>";
		$sql=$this->sql->sqlConsultarCiudad();
		$res=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		while($row=mysqli_fetch_array($res)){
			
			echo "<tr><td><a href='?mod=cat&p=".$row["idCiudad"]."'>".$row["ciudad"]."</a></td></tr>";
		}
		echo "</table>";
	}
	public function devolverRegion($idRegion){
		$this->link=$this->conectar();
		$sql="select* from mm_region where idRegion='".$idRegion."'";
		$query=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		$row=mysqli_fetch_array($query);	 
		return($row["nombre"]);
	}
	public function devolverCiudad($idCiudad){
		$this->link=$this->conectar();
		$sql="select* from mm_ciudad where idCiudad='".$idCiudad."'";
		$query=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		$row=mysqli_fetch_array($query);	 
		return($row["ciudad"]);
	}
	public function devolverComuna($idComuna){
		$this->link=$this->conectar();
		$sql="select* from mm_comuna where idComuna='".$idComuna."'";
		$query=mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
		$row=mysqli_fetch_array($query);	 
		return($row["nombre"]);
	}
	public function devolverEstacionamientos($id){
		$arrSel12=array(1=>"1 estacionamiento",2=>"2 o mas");
		return($arrSel12[$id]);
	}
	public function modificarPropiedad($id){
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
	   echo "<form method='post' name='form111' id='form111' action=''>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		 
	   if(isset($_GET["msg"])){
		   $msg=htmlentities($_GET["msg"]);
		   if($msg==1){
			   $this->miForm->addAlert("Información del sistema","Cambios guardados con exito !!",1);
		   }
	   }     
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
	   echo ' <textarea name="des">'.$row["descripcion"].'</textarea>
	   <script>
			   CKEDITOR.replace( "des" );
	   </script>';	   

	   echo "</textarea>";
	   echo "</div>";


	   echo "<div><input type='hidden' name='action' id='action' value='true' class='form-control form-control-mb'/></div>";
	   echo "<div style='margin-top:10px;'><button role='button' id='s11' name='s11' class='btn btn-primary btn-sm'>Guardar Cambios</button></div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
	   echo "<div>&nbsp;</div>";
		echo "</div>";
		echo "</div>";
		echo "</form>";


		if(isset($_POST["action"])){	
		 				
			if(empty($_POST["cordenada"]) || $_POST["cordenada"]==""){			
				$c=$row["cordenadas"];
 			}else{
 				$c1=$_POST["cordenada"];
				$c=substr($c1,0);
				$c=substr($c1,1,-1);
			}
	 
			$sql=$this->sql->sqlModificarVivienda($_POST,$id,$c);
		 
 
		 	mysqli_query($this->link,$sql) or die(mysqli_error($this->link));		
			 if(isset($_GET["idq"])){
				$idq=htmlentities($_GET["idq"]);
			}	
	 	 header("location:panel.php?mod=panel&op=4&mq=editar&idq=".$idq."&msg=11");
			 exit;		 	
		}
	}
	public function devolverTipoCocina($id){
		$arrSel122=array(1=>"Cerrada",2=>"Americana",3=>"Integrada");
		return($arrSel122[$id]);
	}
	public function modificarPropiedad2($id){
		$this->link=$this->conectar();
		$cadena=$this->sql->sqlConsultarUnaVivienda($id);	
		$query=mysqli_query($this->link,$cadena) or die(mysqli_error($this->link));
		$row=mysqli_fetch_array($query); 
 		 $this->gMaps=new miniGmaps();
		$this->gMaps->jqueryMaps();		 
		$this->gMaps->jMaps($row["cordenadas"]); 	 
		echo "<div style='margin-top:0px; margin-left:20px;padding:0px;'>";
		 // $this->miForm->addText("codigo",250,"codigo","Codigo:",$row["codigo"]);
		 echo "<form name='form1' id='form1' enctype='multipart/form-data' method='post' action=''>";   
		 $this->miForm->abrirForm(0,true,"form1","post","proceso.php",2);        	   
		$this->miForm->addText("titulo",550,"titulo","Titulo de la Propiedad :",$row["titulo"]);  
        $arrSel=array(1=>"Venta",2=>"Arriendo",3=>"Ventas en verde",4=>"Ventas en blanco",5=>"Ventas entrega inmediata");
        $this->miForm->addSelect("operacion",$arrSel,"Seleccione...",false,"Operacion",$row["operacion"]);
        
		$arrSel1=array(1=>"Se Vende", 2=>"Se Arrienda",3=>"Arrendada",4=>"Vendido",5=>"Reservada");
        $this->miForm->addSelect("estadoProp",$arrSel1,"Seleccione...",false,"Estado Propiedad",$row["estadoProp"]);

        $arrSel3=array(1=>"Casas",2=>"departamento",3=>"Parcelas",4=>"Sitios",5=>"Oficina Comercial",6=>"Propiedad Industrial",7=>"Terreno",8=>"Local comercial",9=>"Estacionamientos",10=>"Bodegas");
        $this->miForm->addSelect("tipoProp",$arrSel3,"Seleccione...",false,"Tipo",$row["tipoProp"]);
 
		echo "<div>Dirección </div>";
		  
		echo "<div>";
		echo '<input type="text"  style="margin-left:0px;margin-top:2px; width:450px; margin-bottom:2px;" class="form-control input-sm"  maxlength="100" id="address" name="address"  value="'.$row["direccionProp"].'" placeholder="Direcci�n" /> 
  		<input type="button"  id="search" value="Buscar" style="padding-left:10px; padding-right:10px;" />(ej:republica 400)';
		echo "</div>";


		echo "<div>";
		echo "Mapa de Ubicación";           
		echo '<div>
		 <input type="hidden" name="cordenada" id="cordenada" value="'.$row["coordenadas"].'"/></div>

		 <div id="map_canvas" style="margin-left:0px; margin-top:5px; margin-bottom:5px; width:600px; height:200px;"></div>';
        echo "</div>";
       	$arrSel1=array("Si"=>"Si","No"=>"No");        
        $this->miForm->addSelect("destacar",$arrSel1,"Seleccione...",true,"Destacar",$row["destacar"]); 
        $sql2="select* from mm_ciudad order by idCiudad desc";
 
        $query2=mysqli_query($this->link,$sql2) or die(mysqli_error($this->link));
        while($row2=mysqli_fetch_array($query2)){
            $arrSel11[$row2["idCiudad"]]=$row2["ciudad"];
        }
      
        
        $this->miForm->addSelect("ciudad",$arrSel11,"Seleccione...",true,"Ciudad",$row["ciudad"]);
        
        echo "<input type='hidden' name='action' id='action' value='true'/>";
       

        $this->miForm->addText("m2Totales",250,"Metros Totales","M2 Totales:",$row["mt2Totales"]);
        $this->miForm->addText("m2Construidos",250,"Metros Construidos","M2 Construidos :",$row["m2Construido"]);
      
		
        $this->miForm->addText("precio",250,"precio","Precio :",$row["precio"]);
           $arrSel12=array("1"=>"Pesos","2"=>"U.F.");
        
        $this->miForm->addSelect("precioUf",$arrSel12,"Seleccione...",true,"Precio en :",$row["precioUf"]);
        
       	$arrSel1=array("Si"=>"Si","No"=>"No");        
		$arrSel17=array(1=>"Si",2=>"No");   
        $this->miForm->addSelect("piscina",$arrSel1,"Seleccione...",true,"Piscina",$row["piscina"]);         
        $this->miForm->addSelect("bodega",$arrSel1,"Seleccione...",true,"Bodega",$row["bodega"]);        
        $this->miForm->addSelect("logia",$arrSel1,"Seleccione...",true,"Logia",$row["logia"]);
		$this->miForm->addSelect("numEstacionamientos",$arrSel17,"Seleccione...",true,"Estacionamiento",$row["estacionamiento"]); 		
         
		$this->miForm->addText("numDor",90,"numDormitoriose","Numero de Dormitorios:",$row["dormitorios"]);
		$k=utf8_decode("Numero de Baños :");
        $this->miForm->addText("numBanos",90,"nBanos",$k,$row["banos"]);
		$this->miForm->addText("cocina",90,"cocina","Cocina:",$row["cocina"]);
	 
        
		//$this->miForm->addTextarea("des",50,5,"descripcion","Descripción :",$row["descripcion"]);
		echo ' <textarea name="des">'.$row["descripcion"].'</textarea>
		<script>
				CKEDITOR.replace( "des" );
		</script>';
		echo "<div>Fotos</div>";
        echo "<div style='padding-left:20px;'>";
       
        echo "<table width='1%' style='margin:0px; padding:0px;' border=0 >";
		$cadena1=$this->sql->sqlConsultarIdFotos($id);
	 
		$query1=mysqli_query($this->link,$cadena1) or die(mysqli_error($this->link));
		echo "<tr>";
		$k=0; 
		$numCol=7;
		
		while($row1=mysqli_fetch_array($query1)){
			echo "<td  valign='top' align='left'>";
			echo "<table width='100%' style='margin:0px; padding:0px;' height='130' border=0 style='border-width:0px; border-style:solid; border-color:gray;'>";
			echo "<tr><td>";
			echo "<img src='./upload/".$row1["ruta"]."' style='border-width:1px; border-style:solid; border-color:gray; margin:5px; padding:2px;' width='100' height='98'/>";
			echo "</td></tr>";
			echo "<tr><td>&nbsp;&nbsp;<input type='checkbox' onclick='k();' name='c1[]' id='c1a' value='".$row1["idFoto"]."'/><span style='font-family:arial; font-size:12px;'>&nbsp;Borrar</span></td></tr>";
			echo "</table>";
			echo "</td>";
			$k++;
			if($k%$numCol==0){
				echo "</tr>";				
			}
		}
		echo "</table>";
		echo "</div>";
        
		echo "<div style='padding-left:20px;'>";
		echo "Tamaño aproximado por cada foto 1150x810";
		echo "</div>";
		 
		echo "<div style='padding-left:20px;'>";
		echo "<input type='hidden' name='action' id='action' value='true'/>";
		$this->miForm->addFile(10,"Subir Imagenes",$editarImagen);    
		
		echo "</div>";
		echo "<div style='padding-left:20px;margin-top:30px;margin-bottom:50px;'>";
        
		$this->miForm->addButton("enviar","Guardar Cambios",false,$borrar=false,"primary","far fa-check-circle",true,false,"lg");            
		echo "</div>";
       
       
	   
        
        
        $this->miForm->procesar();
        if($this->miForm->procesarArch()){
            $arch=$this->miForm->getDataArch(); 
                    
		}
	

        if($_POST["action"]){
		 
		 
			if(empty($_POST["cordenada"]) || $_POST["cordenada"]==""){			
				$c=$row["cordenadas"];
 			}else{
 				$c1=$_POST["cordenada"];
				$c=substr($c1,0);
				$c=substr($c1,1,-1);
				 
				 
			}
			$sql=$this->sql->sqlModificarVivienda($_POST,$id,$c);
		  
			if(isset($_POST["c1"])){
				$lista=implode(",",$_POST["c1"]);
				$sql1="delete from mm_cape_fotos where idFoto in (".$lista.")";
				mysqli_query($this->link,$sql1) or die(mysqli_error($this->link));

			}
			foreach($arch as $clave=>$valor){
				$sql2="insert into mm_cape_fotos (idProp,ruta) value('".$id."','".$valor."')";
				 
				mysqli_query($this->link,$sql2) or die(mysqli_error($this->link));
			}
			mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
			if(isset($_GET["idq"])){
				$idq=htmlentities($_GET["idq"]);
			}
		 
			 header("location:panel.php?mq=editar&mod=panel&op=4&idq=".$idq."&msg=11");
			exit;
        }
		echo "</div></FORM>";
        $this->miForm->cerrarForm();
        return(true);
	}
	public function setGeo($valor){
		$this->geo=$valor;
		return(true);
	}
	public function getGeo($valor){
		$geo=$this->geo();
		return($geo);
	}
	  
	public function indicadoresEconomicos(){
		/* visualiza los indicadores economicos pie pagina*/
		
$url = "http://www.averigualo.cl/feed/indicadores.xml";

$contenido_xml = "";

if($d = fopen($url, "r")) {

  while ($aux= fgets($d, 1024)){
    $contenido_xml .= $aux;
  }

  fclose($d);

}else{
  //echo "No se ha podido abrir XML";
}



$xml = simplexml_load_string($contenido_xml);
 	echo "Indicadores Economicos-";
	echo "Miercoles 1 de Abril del 2015<br>";
	echo "<b>&nbsp;UF:&nbsp;</b> $24.622,78";
	echo "<b>&nbsp;Dolar:&nbsp;</b> $626 -";
	echo "<b>&nbsp;Euro:&nbsp;</b>$628";
	echo "<b>&nbsp;IPC:&nbsp;</b>$0,40";
	echo "<b>&nbsp;UTM:&nbsp;</b> $43.068,00";
		//echo 'Indicadores: UF: '.$xml->uf.'  USD: $619,61  UTM: '.$xml->utm;
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



class uploadFtp{
	public $conn_id;
	public $destination_file;
	public function __construct(){
		
	}
	public function pruebaFTP(){
		if(!$this->conectaFTP()){
			echo "No se ha podido conectar via Ftp";
		}else{
			echo "Coneccion via ftp establecida";
		}
	}
	public function encriptar($string, $key) {
		$result ="";
		for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
		}
		return base64_encode($result);
		}
	
	 public function desencriptar($string, $key) {
			$result ="";
			$string = base64_decode($string);
			for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
			}
			return $result;
	}
	public function conectaFTP(){
		$ftp_server = "173.249.41.145";
		$ftp_user_name = "fotos2@clickcorredores.cl";
		$ftp_user_pass = "X%XHpxT]I7=I";

		$this->destination_file = "./"; /*directorio raiz /upload/ */
		/* debe coincidir con la cuenta ftp creada /home7/cle59...l/ftp/upload*/	
					
		$this->conn_id = ftp_connect($ftp_server);
		ftp_pasv($this->conn_id, true);
		$login_result = ftp_login($this->conn_id, $ftp_user_name, $ftp_user_pass);
		if ((!$this->conn_id) || (!$login_result)) { 
			echo "no conectado";
	 
			return(false);
		} else {
			echo "conectado";
 		 
			return(true);
		}
		return($true);			
	}
	public function putFile($file,$nombre){
		$source_file = $file;
		$upload = ftp_put($this->conn_id, $this->destination_file . $nombre, $source_file, FTP_BINARY);
		if (!$upload) { 
			echo "No se ha subido el archivo";
		}else{
			echo "archivo subido";
		}	
	}
	public function cerrarFTP(){
		ftp_close($this->conn_id);
	}
}

class propTemp extends coneccion{
	public $ancho_max;
	public $alto_max;
	public function __construct(){
		$this->conectar(); 
	}
	public function agregarFotos($id,$a,$al){
		$this->link=$this->conectar();
		$this->ancho_max=$a;
		$this->alto_max=$al;
	 
		if(isset($_POST["subir"])){

		 $arch=$this->procesarFile();
		 if(isset($_POST["portada"][0])){
			$idFoto=$_POST["portada"][0];
			$sql2="update mm_cape_fotos set portada=0 where idProp='".$id."'";
			mysqli_query($this->link,$sql2) or die(mysqli_error($this->link));

			$sql3="update mm_cape_fotos set portada=1 where idFoto='".$idFoto."'";
			mysqli_query($this->link,$sql3) or die(mysqli_error($this->link));
		}
	 
		if(isset($_POST["c1"])){
			$lista=implode(",",$_POST["c1"]);
			$sql1="delete from mm_cape_fotos where idFoto in (".$lista.")";
			mysqli_query($this->link,$sql1) or die(mysqli_error($this->link));
		}
		foreach($arch as $clave=>$nombre){
			$imagen = getimagesize("./upload/".$nombre);		
			$ancho = $imagen[0];          
			$alto = $imagen[1];	
			$sql="insert into mm_cape_fotos (idProp,ruta,ancho,alto) values ('".$id."','".$nombre."','".$ancho."','".$alto."')";
			mysqli_query($this->link,$sql);
		 }
		  
		 header("location:panel.php?mod=panel&op=4&mq=agregarFtp&idq=".$id."&msg=2");
		  exit;
	  		if(isset($_GET["msg"])){
			  echo '<div class="alert alert-primary" role="alert">
					  Los Cambios se han realizado con exito !!</div>';		  
			 }
		}
		echo '<form method="post" action="" enctype="multipart/form-data">';
		echo '<div>Para subir varias fotos mantenga presionada la tecla ctrl del teclado mientras selecciona las fotografias
		</div>
		
	 

		</div>';
		echo '<div class="input-group mb-3" style="padding:20px;">
		   <div>
			  <input type="file" name="archivo[]" id="archivo[]" type="file" multiple="" class="form-control" id="inputGroupFile01">
			  </div>
			  </div>';    

			echo "<div class='row'>";
			echo "<div class='col-md-12' style='padding:30px;'>";
			$sql="select titulo from mm_propiedad where idProp='".$id."'";
			$q=mysqli_query($this->link,$sql);
			$r=mysqli_fetch_array($q);
			
			echo "<div><h3>".$r["titulo"]."</h3></div>";
			echo "<div><hr></div>";
   
			$sql2="select * from mm_cape_fotos where idProp='".$id."' order by portada desc";
	 
		  
			 $q2=mysqli_query($this->link,$sql2);
			 $numCol=5;
			 $k=0;
			echo "<div class='row'>";
			
			 while($r=mysqli_fetch_array($q2)){
			   echo "<div class='col-md-2' style='padding:10px;'>";
			   $k++;
			   
			   if($r["portada"]==1){
				   echo "<div style='border-color:gray; border-style:solid; border-width:3px;' align='center'>";
				   echo "<img src='./upload/".$r["ruta"]."' style='height:100px; width:100% !important;'/>";
				   echo "</div>";
			   }else{
				   echo "<div style='background-color:#ddd;' align='center'>";
				   echo "<img src='./upload/".$r["ruta"]."' style='height:100px; width:100% !important;'/>";
				   echo "</div>";
			   }
			   
			   echo "<div>";
			   echo "<table width='100%'>";
			   echo "<tr>";
			   echo "<td>";
			   echo "<input type='checkbox' onclick='k();' name='c1[]' id='c1a' value='".$r["idFoto"]."'/><span style='font-family:arial; font-size:12px;'>&nbsp;Borrar</span>";
			   echo "</td>";
			   echo "<td>";
			   if($r["portada"]==1){
				   echo "<input type='radio' checked='checked' onclick='k();' name='portada[]' id='portada[]' value='".$r["idFoto"]."'/>			
			   <span style='font-family:arial; font-size:12px;'>&nbsp;En Portada
			   </span>";
			   }else{
				   echo "<input type='radio' onclick='k();' name='portada[]' id='portada[]' value='".$r["idFoto"]."'/>			
				   <span style='font-family:arial; font-size:12px;'>&nbsp;En Portada
				   </span>";
			   }
			   
			   echo "</td></tr></table>";
			   echo "</div>";
			   
				echo "</div>";
			 }
			echo "</div>";
			echo "</div>";
			  echo "<div class='row'>";
			  echo "<div class='col-md-12' style='padding:40px;'>";
			  echo '<input type="submit" id="subir" name="subir" value="Guardar Cambios" class="btn btn-primary btn-sm" />';          
			  echo "</div>";
			  echo "</div>";
		echo '</form>';
	}
	public function procesarFile(){
		
		$ftp=new uploadFtp();
		$ftp->conectaFTP();

		foreach($_FILES["archivo"]['tmp_name'] as $key => $tmp_name){
				$nom = $_FILES["archivo"]["name"][$key];   
			   
				if (isset($nom) && $nom != "") {
					 $this->correctImageOrientation($_FILES["archivo"]["tmp_name"][$key]);
					  $temp =$_FILES["archivo"]["tmp_name"][$key];
					 $tipo = $_FILES['archivo']['type'][$key];
	 
					 if (!((strpos($tipo, "gif") || strpos($tipo, "jpeg") || strpos($tipo, "jpg") || strpos($tipo, "png")) && ($tamano < 200000000))) {
						 echo '<div><b>Error. La extensión o el tamaño de los archivos no es correcta.<br/>
							 - Se permiten archivos .gif, .jpg, .png. y de 200 kb como máximo.</b></div>';
					 }else {
						 if(strpos($tipo,"jpg")){
							$archivo=$key.date("dmY_h_m_s").".jpg";
							$tipo="jpg";
						 }else if(strpos($tipo,"gif")){
							$archivo=$key.date("dmY_h_m_s").".gif";
							$tipo="gif";
						 }else if(strpos($tipo,"jpeg")){
							$archivo=$key.date("dmY_h_m_s").".jpeg";
							$tipo="jpeg";
						 }else if(strpos($tipo,"png")){
							$archivo=$key.date("dmY_h_m_s").".png";
							$tipo="png";
						 }
						 	   $src = "./temp/".$archivo;
							 
							 if ($ftp->putFile($temp, $src)) {
								echo "El archivo se ha subido correctamente.";
							} else {
								echo "<br>Error al subir el archivo:<br> ";
						 
							}							 
							   $def = $archivo;
						   if($this->redimensionarJPEG ($archivo,"./upload/".$archivo, 650, 650, "alto",$tipo)){
							  unlink("./upload/temp/".$archivo);
						   
							   $arch[]=$def;         
						   }else{       
								echo '<div><b>Ocurrió algún error al subir el fichero. No pudo guardarse.</b></div>';       
						   }
					  }
				   }   
		   }
	   
		 
		 return($arch);
	}

	public function redimensionarJPEG ($archivo, $destino, $ancho_max, $alto_max, $fijar,$tipo) {
	 
		$origen="./upload/temp/".$archivo;
		 
		$info_imagen= getimagesize($origen);
		 	
		$ancho=$info_imagen[0];
		$alto=$info_imagen[1];
 

		if ($ancho>=$alto)
		{
			$nuevo_alto= round($alto * $ancho_max / $ancho,0);
			$nuevo_ancho=$ancho_max;
		}
		else
		{
			$nuevo_ancho= round($ancho * $alto_max / $alto,0);
			$nuevo_alto=$alto_max;
		}
		switch ($fijar)
		{
			case "ancho":
				$nuevo_alto= round($alto * $ancho_max / $ancho,0);
				$nuevo_ancho=$ancho_max;
				break;
			case "alto":
				$nuevo_ancho= round($ancho * $alto_max / $alto,0);
				$nuevo_alto=$alto_max;
				break;
			default:
				$nuevo_ancho=$nuevo_ancho;
				$nuevo_alto=$nuevo_alto;
				break;
		}
		$imagen_nueva= imagecreatetruecolor($nuevo_ancho,$nuevo_alto);
		if($tipo=="jpeg"){
		  $imagen_vieja= imagecreatefromjpeg($origen);
		}else if($tipo=="jpg"){
		  $imagen_vieja= imagecreatefromjpeg($origen);
		}else if($tipo=="png"){
		  $imagen_vieja= imagecreatefrompng($origen);
		}else if($tipo=="gif"){
		  $imagen_vieja= imagecreatefromgif($origen);
		}else{
		  $imagen_vieja= imagecreatefrompng($origen);
		}
		 
		  imagecopyresampled($imagen_nueva, $imagen_vieja, 0, 0, 0, 0,$nuevo_ancho, $nuevo_alto, $ancho, $alto);
		 
		if($tipo=="jpeg"){
		  imagejpeg($imagen_nueva,$destino);
		}else if($tipo=="jpg"){
		  imagejpeg($imagen_nueva,$destino);
		}else if($tipo=="png"){
		  imagepng($imagen_nueva,$destino);
		}else if($tipo=="gif"){
		  imagegif($imagen_nueva,$destino);
		}else{
		  imagejpeg($imagen_nueva,$destino);
		}
	  
		
		imagedestroy($imagen_nueva);
		imagedestroy($imagen_vieja);
		return(true);
		}
	  public function correctImageOrientation($filename) {
	   
		  $exif = @exif_read_data($filename);
	   
		  if($exif && isset($exif['Orientation'])) {
			$orientation = $exif['Orientation'];
			
			if($orientation != 1){
			  $img = imagecreatefromjpeg($filename);
			  $deg = 0;
			  switch ($orientation) {
				case 3:
				  $deg = 180;
				  break;
				case 6:
				  $deg = 270;
				  break;
				case 8:
				  $deg = 90;
				  break;
			  }
			  if ($deg) {					
				$tmp = imagerotate($img, $deg, 0);        				  
			  }
			  
			  // then rewrite the rotated image back to the disk as $filename 
			   imagejpeg($tmp, $filename, 95);
			   imagedestroy($img);
			} // if there is some rotation necessary
		  } // if have the exif orientation info
			   
	  }
}



class aws extends coneccion {
	public function __construct(){		
		 
	}
	public function s3_upload_put_object($file_name,$file_path){     
   
		$options=[ 
			 'region'=>'sa-east-1',
			 'version'=>'2006-03-01',
			 'credentials'=> [
			   'key'=>'AKIA5TAWZLSQW5ZMBEV4',
			   'secret'=>'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'
			 ]
			 ];
			 try{
				 $s3Client=new S3Client($options);
				 $result=$s3Client->putObject([
					 'Bucket'=>'petabyteplus',
					 'Key'=>$file_name,
					 'SourceFile'=>$file_path
				 ]);
			   return($result['@metadata']['effectiveUri']);
			 }catch(S3Exception $e){
			   echo $e->getMessage()."\n";
			 }
			 
	  }
	public function guardarFotos($arch,$id){
		$this->link=$this->conectar();
		if(count($arch)!=0){
		foreach($arch as $clave=>$nombre){
			$imagen = getimagesize($nombre);		
			$ancho = $imagen[0];          
			$alto = $imagen[1];	
			$sql="insert into mm_cape_fotos (idProp,ruta,ancho,alto) values ('".$id."','".$nombre."','".$ancho."','".$alto."')";			 
		 
		 	mysqli_query($this->link,$sql);
		 }
		}
	}
	public function subirFotos(){	
		
		$this->link=$this->conectar();
		if(isset($_GET["idq"])){
			$id=htmlentities($_GET["idq"]);
		}	
		 
 		if(isset($_POST["btn"]) ){
		 
			if(isset($_POST["orden"])){
				foreach($_POST["orden"] as $c=>$v){		 
					$sql77="update mm_cape_fotos set orden='".$v."' where idFoto='".$c."'";
					mysqli_query($this->link,$sql77);
				}			 
			 }
			 if(isset($_POST["portada"][0])){
				$idFoto=$_POST["portada"][0];
				$sql2="update mm_cape_fotos set portada=0 where idProp='".$id."'";
				mysqli_query($this->link,$sql2) or die(mysqli_error($this->link));
	
				$sql3="update mm_cape_fotos set portada=1 where idFoto='".$idFoto."'";
				mysqli_query($this->link,$sql3) or die(mysqli_error($this->link));
				 
			}
		 
			if(isset($_POST["c1"])){
				$lista=implode(",",$_POST["c1"]);
				$sql1="delete from mm_cape_fotos where idFoto in (".$lista.")";
				mysqli_query($this->link,$sql1) or die(mysqli_error($this->link));
			}

			if(!empty($_FILES["file"]["name"][0])){
         		foreach($_FILES["file"]["tmp_name"] as $key=>$tmp_name){        
					if(!empty($_FILES["file"]["name"][$key])){
						$file_name=$_FILES["file"]["name"][$key];
        				$file_tmp=$_FILES["file"]["tmp_name"][$key];        
       					$fotos[]= $this->s3_upload_put_object($file_name,$file_tmp);   
					}
    	 		} 
		 		$this->guardarFotos($fotos,$id);   
		}
    	 header("location:panel.php?mod=panel&op=4&op=4&mq=agregarAws&idq=".$id."&msg=1");
		 
 		}
		 
		echo "<div class='container'>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		 
		   
		echo "<div style='margin-top:30px;'><h4>Subir fotos vía AWS Cloud </h4></div>";
		
		echo '<div style="margin-bottom:30px;margin-top:10px;">Para subir varias fotos mantenga presionada la tecla ctrl del teclado mientras selecciona las fotografias</div>';
		echo "<div>";
		echo '<form action="" method="post" name="form1" id="form1" enctype="multipart/form-data">		
		<div>
		<input class="form-control" type="file" name="file[]" id="file[]"  multiple="multiple">
		</div>';



		echo "<div class='row'>";
			echo "<div class='col-md-12' style='padding-left:10px;padding-right:10px;'>";
			$sql="select titulo from mm_propiedad where idProp='".$id."'";
			
			$q=mysqli_query($this->link,$sql);
			$r=mysqli_fetch_array($q);
	 
			echo "<div style='margin-top:40px;margin-left:10px;'><h5>".$r["titulo"]."</h5></div>";
			echo "<div><hr></div>";
   
			$sql22="select * from mm_cape_fotos where idProp='".$id."' order by orden asc";	  
 
			$q22=mysqli_query($this->link,$sql22);
		 
			$t=mysqli_num_rows($q22);
	 
			echo "<div style='margin-left:10px; margin-bottom:20px;'>Total de fotos: ".$t."</div>";


			$sql2="select * from mm_cape_fotos where idProp='".$id."' order by orden asc";
	 
		  
			 $q2=mysqli_query($this->link,$sql2);
			 $numCol=5;
			 $k=0;
			echo "<div class='row'>";
		 
			 while($r=mysqli_fetch_array($q2)){
			   echo "<div class='col-md-2' style='padding:10px;'>";
			   $k++;
			   
			   if($r["portada"]==1){
				   echo "<div style=' ' align='center'>";
				   if(preg_match("/https/",$r["ruta"])){
					echo "<img src='".$r["ruta"]."' style='height:100px; width:100% !important;'/>";
				   }else{
					echo "<img src='./upload/".$r["ruta"]."' style='height:100px; width:100% !important;'/>";
				   }
				   
				   echo "</div>";
			   }else{
				   echo "<div style='background-color:#ddd;' align='center'>";
				   if(preg_match("/https/",$r["ruta"])){
					echo "<img src='".$r["ruta"]."' style='height:100px; width:100% !important;'/>";
				   }else{
					echo "<img src='./upload/".$r["ruta"]."' style='height:100px; width:100% !important;'/>";
				   }
				   
				   echo "</div>";
			   }
			   
			   echo "<div>";
			   echo "<table width='100%'>";
			   echo "<tr>";
			   echo "<td>";
			   echo "<input type='checkbox' onclick='k();' name='c1[]' id='c1a' value='".$r["idFoto"]."'/><span style='font-family:arial; font-size:12px;'>&nbsp;Borrar</span>";
			   echo "</td>";
			   echo "<td>";
			   if($r["portada"]==1){
				   echo "<input type='radio' checked='checked' onclick='k();' name='portada[]' id='portada[]' value='".$r["idFoto"]."'/>			
			   <span style='font-family:arial; font-size:12px;'>&nbsp;En Portada
			   </span>";
			   }else{
				   echo "<input type='radio' onclick='k();' name='portada[]' id='portada[]' value='".$r["idFoto"]."'/>			
				   <span style='font-family:arial; font-size:12px;'>&nbsp;En Portada
				   </span>";
			   }
			   
			   echo "</td></tr></table>";
			   echo "</div>";
			   echo "<div>Orden</div>";
		
		echo "<div>";
		echo "<select name='orden[".$r["idFoto"]."]' class='form-control form-control-sm' id='orden[]'>";
		if($r["orden"]!=0){
			echo "<option value='".$r["orden"]."' selected='selected'>".$r["orden"]."</option>";			
		}else{
			echo "<option value='0' selected='selected'>Orden</option>";
		}		
		for($i=1; $i<=$t; $i++){
			if($r["orden"]!=$i){
			echo "<option value='".$i."'>".$i."</option>";
			}
		}
		
		echo "</select>";
		echo "</div>";
			   
				echo "</div>";
			 }



		echo '</div>
		<div style="margin-top:10px;margin-left:10px;">
		<button type="submit" id="btn" name="btn" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> Guardar Cambios</button>
		</div>';

		echo '
		</form>';
		echo "</div>";
		echo "</div></div></div>";


		echo "<div align='right' style='margin-top:20px;'><img src='aws.png' style='width:15%;'/></div>";

	}
}
?>




