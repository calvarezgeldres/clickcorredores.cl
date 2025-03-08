<?php
/*
 * Desarrollado por Programación Web Chile.cl
 * Autor: Luis Olguin - Analista Programador
 * Fecha: 26/06/2015
 * */
ob_start();
 
session_start();

require_once("./clases/class.coneccion.php");
class rotador extends coneccion{
	public $link;
	public function __construct(){
		   $this->link=$this->conectar();	
	} 
    public function procesarForm(){
     
        require_once("./clases/class.upload.php");
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
         $handle->image_x= 1350;
		$handle->image_y =586;
		$handle->file_new_name_body = $_SESSION["fecha"];
		$handle->auto_create_dir = true;
		$handle->dir_auto_chmod = true;
		$handle->dir_chmod = 0777;
		$handle->image_convert = 'jpg';
		$handle->jpeg_quality = 100;	
	 $handle->Process("./data1/images/");
	 
 
        if ($handle->processed) {
        	$arch[$clave]=$handle->file_dst_name;
              // $this->ingresarImagenes($handle->file_dst_name,$_POST["text"][$k],$clave); 
            }
            	/* SEGUNDA IMAGEN LA THUMBAILS*/
        // we now process the image a second time, with some other settings
		
        $handle->image_resize            = true;
		$handle->image_ratio_y           = true;
         $handle->image_x= 1350;
		$handle->image_y =586;
		$handle->file_new_name_body =$_SESSION["fecha"];
		$handle->auto_create_dir = true;
		$handle->dir_auto_chmod = true;
		$handle->dir_chmod = 0777;
		$handle->image_convert = 'jpg';
		$handle->jpeg_quality=100;
        $handle->Process('./data1/tooltips/');
		
	 
        }
    }
 
return($arch);
} 
    }
    public function borrar($lista){
		$this->link=$this->conectar();
        $sql="update rotator set imagen=''  where idRotator in (".$lista.")";	
	 
		 
        mysqli_query($this->link,$sql) or die(mysqli_error($this->link)); 
        return(true);
    }
    public function tablaRotador3(){
		$this->link=$this->conectar();
        $sql="select* from rotator limit 0,6";
		
        $query=mysqli_query($this->link,$sql);  
			$row=mysqli_fetch_array($query);
		echo "<pre>";
		print_r($row);
		exit;
		echo mysqli_num_rows($query);
        $numCol=2;
        if(isset($_POST["update"])){
           foreach($_POST["texto"] as $clave=>$valor){
            $sql="update rotator set texto='".$valor."' where idRotador='".$clave."';";
            $s[]=$sql;
           }
		   
           foreach($s as $clave=>$valor){
            mysqli_query($this->link,$valor);            
           }
           header("location:index.php?mod=admin&op=5&act=add&msg=1");
            exit;
        }
        if(isset($_POST["borrar"])){   
      
            if(isset($_POST["check"])){
                $lista=implode(",",$_POST["check"]);
                if($this->borrar($lista)){
                    header("location:index.php?mod=admin&op=5&act=add&msg=1");
                    exit;
                }
            }
        }
        echo "<form method='post'  enctype='multipart/form-data' name='form1' id='form1'><table width='100%' cellspacing=4 cellpadding=4 border=0>";
        echo "<tr><td>Imagenes disponibles</td><td><input type='submit' name='borrar' id='borrar' class='btn btn-primary' value='Borrar Fotos'/>&nbsp;&nbsp;<input type='submit' name='update' id='update' class='btn btn-primary' value='Guardar Cambios'/></td></tr>";
        if(isset($_GET["m"])){
            echo "<tr><td>Imagenes fueron eliminadas con exito!!!</td></tr>";
        }
        if(isset($_GET["msg"])){
            echo "<tr><td>Se han actualizado con exito!!!</td></tr>";
        }
        $k=0;
        if(mysqli_num_rows($query)==0){
            echo "<tr><td colspan=2>Rotador no contiene imagenes</td></tr>";   
        }else{
        echo "<tr>";
	
        while($row=mysqli_fetch_array($query)){
            $k++;          
            echo "<td><table width='100%' border=0>";
            echo "<tr><td><img src='./data1/images/".$row["imagen"]."' class='img-thumbnail' width='300'/></td></tr>";
             
            echo "<tr><td><input type='checkbox' name='check[]' value='".$row["idRotator"]."'/> Eliminar</td></tr>";
            echo "</table></td>";
            if($k%$numCol==0){
                echo "</tr>";
            }    
        }
        
        }        
        echo "</table></form>";
    }
    public function ingresarImagenes($nombre,$texto,$clave){
		$this->link=$this->conectar();
        $fecha=date("Y-m-d H:i:s");      	
		$sql="update  rotator set imagen='".$nombre."', texto='".$texto."' where idRotator='".$clave."'";	
		echo $sql;
		exit;	
        mysqli_query($this->link,$sql) or die(mysqli_error($this->link));          
        return(true);
    }
 
	public function subirRotador(){
		$this->link=$this->conectar();
		$sql="select* from rotator order by idRotator asc ";
		$query=mysqli_query($this->link,$sql);
		$numCol=2;
		$k=0;
		while($row=mysqli_fetch_array($query)){
			$a["arch"][]=array($row["idRotator"],$row["url"],$row["imagen"]);
		} 		
		 
 
		if(isset($_GET["msg"]))	{
			$msg=htmlentities($_GET["msg"]);
			if($msg==1){
				$msg=new msgBox(1,"Cambios guardados con exito!!");
			}
		} 
		 
		if(isset($_POST["subir"])){	
	
            $arch=$this->procesarForm(); 
			  echo "<pre>";
			  print_r($arch);
		
			$m=1;
			foreach($arch as $clave1=>$valor1){
				$b[$clave1+1]=$valor1;
				$m++;
			}	
			
            $k=0;
			$k1=1;
		 
		     	 
			foreach($b as $clave=>$valor){
				 $k++;
					 $sql="update rotator set ";
					 if(!empty($b[$clave])){
					 	$sql.="imagen='".$b[$clave]."'";
					 }
				 	$sql.=" where idRotator='".$clave."'";

			 
					mysqli_query($this->link,$sql) or die(mysqli_error($this->link));
			}			 
			if(isset($_POST["check"])){
				foreach($_POST["check"] as $clave2=>$valor2){
					$sql2="update rotator set imagen='' where idRotator='".$valor2."'";
			 
					mysqli_query($this->link,$sql2);
				}
				
			}
		

	 
               header("location:panel.php?mod=panel&op=61&msg=1");
              exit;
		}		
		$sql="select* from rotator order by idRotator asc";
		$q=mysqli_query($this->link,$sql) or die(mysqli_error($this->link)); 
		echo ' <form name="form3" enctype="multipart/form-data" method="post" action="">'; 
			echo " <table width='100%' border=0>";
 
	 echo "<div class='row'>";
	 $col=3;
	 $k=0;
	 
	 while($row=mysqli_fetch_array($q)){
			$k++;
			echo "<div class='col-md-4'>";
			 
			
		 
			echo "<div>";
			if(empty($row["imagen"])){
				echo "<img src='./imagen/demo.png' width='331' height='161'/>";
			}else{
				echo "<img src='./data1/images/".$row["imagen"]."'   width='331' height='161'/>";
			}
			echo "</div>";
/*
			echo "<div>";
			echo "<h5>".$row["texto"]."</h5>";
			echo "</div>";

			echo "<div>";
			echo "<input type='text' name='text[]' id='text[]'   value='".$row["texto"]."' placeholder='Texto en las imagenes' class='form-control form-control-sm' />";
			echo "</div>";
*/
			echo "<div style='margin-top:10px;'>";
			echo '<p><input type="file" size="32" name="my_field['.$id.']" value="" /></p>';
					echo "<input type='hidden' name='subir' value='true'/>";
						echo '<input type="hidden" name="action" value="multiple" />';
			echo "</div>";
			echo "<div>";
			echo "<input type='checkbox' style='margin-left:10px;' name='check[]' value='".$row["idRotator"]."'/> Eliminar";
			echo "</div>";
			


			echo "</div>";
			
		 
			
		 
		
		
		 
		 
			
			
			
	 }
		echo "</div>";
		echo "</form>"; 
		echo "<div align='left' style='margin-top:50px;'>";
		echo " <input type='submit' name='subir' id='subir' class='btn btn-primary btn-sm' value='Guardar Cambios'>";
		echo "</div>";
	 
	 
	} 
} 
?>