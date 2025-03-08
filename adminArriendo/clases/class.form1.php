<?php
/*
Autor: ProgWebChile.cl - Soluciones para su proyecto web
Nombre: Clase Form 3.0
Fecha: 4/12/2022
Revision:4/12/2022
Version:3.0
Descripción: Actualización a la version 3.0 incluye actualizacion a la ultima version del framework de diseño bootstrap 5.2
*/
session_start();
 ob_start();
 ini_set('display_errors', '1');
 require_once('./clases/class.upload.php');
 require_once("./clases/class.coneccion.php");
class form extends coneccion{
    private $modo;
    private $titulo;
    private $metodo;
    private $nombre;
    private $target;
    private $upload;
    private $numCol;
    private $tipoForm;
    private $arrayValidate;
    private $mensaje;
    private $redirect;
    private $error;
    private $arrayCampo;
    private $datosPost;
    private $datosArch;
    private $datosArchPdf;
    private $arch;
    private $archPdf;
    public $datosErr;
    public $link;
    public function __construct(){
        $this->link=$this->conectar();
    }

    public function subirImagen($alto,$ancho){
		$fecha=date("mdY-H_i_s");
		$_SESSION["fecha"]=$fecha;
		$arch = array();		
		$dir_dest = "./upload";
		$dir_pics = (isset($_GET['pics']) ? $_GET['pics'] : $dir_dest);	
        
    	$handle = new Upload($_FILES['my_field']);
		$handle->image_auto_rotate = true;		
		$handle->image_resize            = true;
        $handle->image_ratio_y           = true;        
		$handle->image_x= $alto;
		$handle->image_y =$ancho;			
		$handle->auto_create_dir = true;
		$handle->dir_auto_chmod = true;
		$handle->dir_chmod = 0777;
		$handle->image_convert = 'jpg';
		$handle->jpeg_quality = 100;	
		$handle->file_new_name_body = $_SESSION["fecha"];
        
    	if ($handle->uploaded) {       
             
        	$handle->process($dir_dest);        	
        	if ($handle->processed) {            	            	
				$arch[]=$handle->file_dst_name; 
				$this->datosArch=$arch;
        	}
        $handle-> clean();
    } 
	}
	public function subirArchivoSimple(){
		$fecha=date("mdY-H_i_s");
		$_SESSION["fecha"]=$fecha;
		$arch = array();		
		$dir_dest = "./upload";
		$dir_pics = (isset($_GET['pics']) ? $_GET['pics'] : $dir_dest);
    	$handle = new Upload($_FILES['my_field']);
		$handle->file_new_name_body = $_SESSION["fecha"];
    	if ($handle->uploaded) {        
        	$handle->process($dir_dest);        	
        	if ($handle->processed) {            	            	
				$arch[]=$handle->file_dst_name; 
				$this->datosArch=$arch;
        	}
        $handle-> clean();
    } 	
	}
	public function subirMultiple($alto,$ancho){
		$fecha=date("mdY-H_i_s");
		$_SESSION["fecha"]=$fecha;
		$arch = array();		
		$dir_dest = "./upload";
		$dir_pics = (isset($_GET['pics']) ? $_GET['pics'] : $dir_dest);		

		$files = array();
		foreach ($_FILES['my_field'] as $k => $l) {
			foreach ($l as $i => $v) {
				if (!array_key_exists($i, $files))
					$files[$i] = array();
				$files[$i][$k] = $v;
			}
		}
		foreach ($files as $file) {				
			$handle = new Upload($file);	
			$handle->image_auto_rotate = true;		
			$handle->image_resize            = true;
        	$handle->image_ratio_y           = true;        
			$handle->image_x= $alto;
			$handle->image_y =$ancho;			
			$handle->auto_create_dir = true;
			$handle->dir_auto_chmod = true;
			$handle->dir_chmod = 0777;
			$handle->image_convert = 'jpg';
			$handle->jpeg_quality = 100;	
			$handle->file_new_name_body = $_SESSION["fecha"];				
			if ($handle->uploaded) {	
				$handle->process($dir_dest);	
				
				if ($handle->processed) {
					$arch[]=$handle->file_dst_name; 
					$this->datosArch=$arch;
				} 
			} 
			
		}	 
	}	public function getDataArch(){		
        return($this->datosArch);
    }
	public function procesarArchSimple(){       
		if(!empty($_FILES["my_field"]["name"])){	   
			$this->subirArchivoSimple();
		}
		return (true);
	}
	public function procesarImagen($alto,$ancho){       
		if(!empty($_FILES["my_field"]["name"])){	
            
			$this->subirImagen($alto,$ancho);
		}
		return (true);
	}
	public function procesarMultiple($alto,$ancho){       
		if(!empty($_FILES["my_field"]["name"])){	   
			$this->subirMultiple($alto,$ancho);
		}
		return (true);
	}
  
	public function addFile(){
        echo '<label for="exampleFormControlInput1" class="form-label">subir archivo</label>';
        echo '<div class="input-group">
        <input type="file" class="form-control"   name="my_field"  id="my_field"  aria-describedby="Imagen" aria-label="Upload">        
        </div>';                
        return(true);
    }
	public function addFileMultiple(){
 
        echo '<div class="input-group">
		<p><input type="file" name="my_field[]"  class="form-control"  id="my_field[]" value="" multiple="multiple"/></p>        
        </div>';                
        return(true);
    }

    public function addFileMultiple2(){
        echo '<label for="exampleFormControlInput1" class="form-label">Subir Fotos</label>';

        echo '
		<div><input type="file" name="my_field[]"  class="form-control"  id="my_field[]" value="" /></div>
        <div style="margin-top:10px;"><input type="file" name="my_field[]"  class="form-control"  id="my_field[]" value="" /></div>
        <div style="margin-top:10px;"><input type="file" name="my_field[]"  class="form-control"  id="my_field[]" value="" /></div>
        <div style="margin-top:10px;"><input type="file" name="my_field[]"  class="form-control"  id="my_field[]" value="" /></div>
        <div style="margin-top:10px;"><input type="file" name="my_field[]"  class="form-control"  id="my_field[]" value="" /></div>
        <div style="margin-top:10px;"><input type="file" name="my_field[]"  class="form-control"  id="my_field[]" value="" /></div>        
        ';
      
        return(true);
    }
	public function addTextarea1($nombre,$cols,$rows,$texto,$placeHolder,$valor=false){     
        $this->arrayCampo[$nombre]=$texto;        
        echo '<div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>
        <textarea class="form-control" id="'.$nombre.'" name="'.$nombre.'" cols="'.$cols.'" rows="'.$rows.'">';
        if($this->modo==0){
          echo $valor;
        }
        echo '</textarea>
        </div>';		  
        return(true);    
    }
    public function addSelect1($nombre,$arr,$mensaje,$func,$texto,$seleccionado=false){
        echo '<div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">'.$texto.'</label>';
        echo '<select class="form-select" name="'.$nombre.'" id="'.$nombre.'" aria-label="'.$texto.'">';
        if($this->modo==0){
            echo "<option value='".$seleccionado."'  selected>".$arr[$seleccionado]."</option>";
        }else{
            echo "<option value='0' selected='selected'>".$mensaje."</option>";
        }
        foreach($arr as $clave=>$valor){
            echo "<option value='".$clave."'>".$valor."</option>";
        }
        echo '</select>';
        echo "</div>";          
    }
    public function getDataPost(){
        $this->datosPost=$_POST;
        return($this->datosPost);
    }    
   
    public function jquery6(){            
		             
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
              $img = imagerotate($img, $deg, 0);        
            }
            // then rewrite the rotated image back to the disk as $filename 
            imagejpeg($img, $filename, 95);
          } // if there is some rotation necessary
        } // if have the exif orientation info            
    }
    public function validarJquery1($post=false){
        echo ' <script src="//code.jquery.com/jquery-1.10.2.js"></script>';
       echo '		
       <script>
        var filter = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
        
             var num=/([0-9])/;
       $(document).ready(function(){
           
           $("#enviar").click(function(){';
       echo 'var c=$("#g-recaptcha-response").val();';
        
                foreach($this->arrayValidate as $clave=>$array){
                       echo 'var '.$clave.'=$("#'.$clave.'").val();';
                       if($array["tipo"]=="numerico"){
                           echo '
                           if('.$clave.'.length==0){
                               alert("ingresa el '.$this->arrayCampo[$clave].'");
                               $("#'.$clave.'").focus();
                               return(false);
                           }else if(num.test('.$clave.')==false){
                               alert("El texto ingresado debe ser solo numero");
                               $("#'.$clave.'").focus();
                               return(false);
                           }
                           ';    
                       } else if($array["tipo"]=="check"){
                           echo 'var op=$("input[name='.$clave.']:checked").val();';                            
                           echo 'if(op==null){
                               alert("Seleccione una '.$this->arrayCampo[$clave].'");
                               return(false);
                           }';
                                                             
                     
                                                             
                       }else if($array["tipo"]=="texto"){
                           echo '
                           if('.$clave.'.length==0){
                               alert("ingresa el '.$this->arrayCampo[$clave].'");
                               $("#'.$clave.'").focus();
                               return(false);
                           }
                           ';    
                     }else if($array["tipo"]=="email"){
                            echo '
                           if('.$clave.'.length==0){
                               alert("ingresa el '.$this->arrayCampo[$clave].'");
                               $("#'.$clave.'").focus();
                               return(false);
                           }else if(filter.test('.$clave.')==false){
                               alert("'.$clave.' mal ingresado");
                               $("#'.$clave.'").focus();
                               return(false);
                           }
                           
                           ';                            
                                    
                     }else if($array["tipo"]=="select"){
                           echo '
                           if('.$clave.'==0){
                               alert("Selecciona el '.$this->arrayCampo[$clave].'");
                               $("#'.$clave.'").focus();
                               return(false);
                           }';                            
                     }else{
                echo '
                if(c.length==0){
                    alert("Debe seleccionar el recuadro no soy robot del catcha de seguridad");
                }else{';
                    echo '$("#form1").submit();';
                echo '}';                           
                       }
                   }
           echo '});
           return(false);
       });		
       </script>';
       return(true);
   } 
	public function validarJquery($post=false){
 
		echo '		
		<script>
		 var filter = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
         
              var num=/([0-9])/;
		$(document).ready(function(){
            
			$("#enviar").click(function(){';
        echo 'var c=$("#g-recaptcha-response").val();';
         
				 foreach($this->arrayValidate as $clave=>$array){
                        echo 'var '.$clave.'=$("#'.$clave.'").val();';
                        if($array["tipo"]=="numerico"){
                            echo '
                            if('.$clave.'.length==0){
                                alert("ingresa el '.$this->arrayCampo[$clave].'");
                                $("#'.$clave.'").focus();
                                return(false);
                            }else if(num.test('.$clave.')==false){
                                alert("El texto ingresado debe ser solo numero");
                                $("#'.$clave.'").focus();
                                return(false);
                            }
                            ';    
                        } else if($array["tipo"]=="check"){
                            echo 'var op=$("input[name='.$clave.']:checked").val();';                            
                            echo 'if(op==null){
                                alert("Seleccione una '.$this->arrayCampo[$clave].'");
                                return(false);
                            }';
                                                              
                      
                                                              
                        }else if($array["tipo"]=="texto"){
                            echo '
                            if('.$clave.'.length==0){
                                alert("ingresa el '.$this->arrayCampo[$clave].'");
                                $("#'.$clave.'").focus();
                                return(false);
                            }
                            ';    
                      }else if($array["tipo"]=="email"){
                             echo '
                            if('.$clave.'.length==0){
                                alert("ingresa el '.$this->arrayCampo[$clave].'");
                                $("#'.$clave.'").focus();
                                return(false);
                            }else if(filter.test('.$clave.')==false){
                                alert("'.$clave.' mal ingresado");
                                $("#'.$clave.'").focus();
								return(false);
                            }
                            
                            ';                            
                                     
                      }else if($array["tipo"]=="select"){
                            echo '
                            if('.$clave.'==0){
                                alert("Selecciona el '.$this->arrayCampo[$clave].'");
                                $("#'.$clave.'").focus();
                                return(false);
                            }';                            
                      }else{
                  
                            echo '$("#form1").submit();';
                     
                            
                        }
					}
			echo '});
			return(false);
		});		
		</script>';
		return(true);
	} 
   public function abrirForm($modo,$upload=false,$nombre,$metodo,$target,$numCol,$tipoForm=false){      
        $this->nombre=$nombre;
        $this->numCol=$numCol;
        $this->tipoForm=$tipoForm;
        $this->modo=$modo;
        $this->metodo=$metodo;
        $this->target=$target;
        $this->upload=$upload;
        echo '<form method="post" action=""  enctype="multipart/form-data"  name="form1" id="form1">';
        if($this->tipoForm=="ajax"){
           echo '<div id="res">';
        }
        $this->abrirTabla();
        return(true);
    }      
    public function abrirTabla(){
        echo "<table width='100%' border=0>";
    }
    public function cerrarTabla(){
        echo "</table>";
    }
    public function procesar(){
       $this->validarJquery($_POST);                           
        return(true);
    }    
    public function procesar11(){
        $this->validarJquery1($_POST);                           
         return(true);
     }    
    public function setTitulo($titulo){
        $this->titulo=$titulo;
        echo "<div><h3>" . $this->titulo . "</h3></div>";            
    }     

    public function addPor($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false){
        $this->modo=$modo;
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;      
      echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
      echo '<div class="input-group mb-3">        
      
      <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';

      if($this->modo==0){echo " value='".$valor."'"; }        
      echo '>
      <span class="input-group-text" id="basic-addon1"><i class="fas fa-percent"></i></span>
      </div>';       
      return($texto);   
  }

    public function addText($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false,$tab=false){
    
		    $this->modo=$modo;
            $this->arrayValidate[$nombre]["tipo"]="texto";
            $this->arrayCampo[$nombre]=$texto;      
            echo '<div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">'.$texto.'</label>
                <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'"   placeHolder="'.$placeHolder.'"';
                if($this->modo==0){echo " value='".$valor."'"; }
                    echo ' ></div>'; 
                return($texto);   
    } 
    public function addDireccion($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false,$tab=false){
        
        $this->modo=$modo;
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;      
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-map-marker-alt"></i></span>
        <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';       
            return($texto);   
} 
    public function addText1($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false,$tab=false){
    
        $this->modo=$modo;
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;      
        echo '<div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">'.$texto.'</label>
            <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'"   ';
            if($this->modo==0){echo " value='".$valor."'"; }
                echo ' ></div>'; 
            return($texto);   
} 
    public function addSw($nombre,$placeHolder, $active){
        
        echo '<div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" name="'.$nombre.'" id="'.$nombre.'" ';
        if($active==true){
            echo ' checked ';
        }
        echo '>
        <label class="form-check-label" for="flexSwitchCheckDefault">'.$placeHolder.'</label>
        </div>';
    }
    public function addTextBoty($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false,$tab=false){
    
    $this->modo=$modo;
    
    $this->arrayCampo[$nombre]=$texto;      
    echo '<div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">'.$texto.'</label>
        <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'"   placeHolder="'.$placeHolder.'"';
        if($this->modo==0){echo " value='".$valor."'"; }
            echo ' ></div>'; 
        return($texto);   
} 
	public function addTextBoty2($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false){
		$this->modo=$modo;
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;
        echo '<div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">'.$texto.'</label>
        <input type="text" class="form-control" id="'.$nombre.'" placeHolder="'.$placeHolder.'"';
        if($this->modo==0){echo " value='".$valor."'"; }
            echo ' ></div>';         
        return($texto);   
    }
	public function addTextBotyPrecio($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false){
		$this->modo=$modo;
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;      
        echo '<div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">'.$texto.'</label>
        <input type="text" class="form-control" id="'.$nombre.'" placeHolder="'.$placeHolder.'"';
        if($this->modo==0){echo " value='".$valor."'"; }
            echo ' ></div>'; 
        
        return($texto);   
    }
    public function addText3($nombre,$tam,$placeHolder,$texto,$valor=false){     
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;
        echo '<div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">'.$texto.'</label>
        <input type="text" class="form-control" id="'.$nombre.'" placeHolder="'.$placeHolder.'"';
        if($this->modo==0){echo " value='".$valor."'"; }
            echo ' ></div>';       
        return($texto);   
    }	 
    public function addText2($nombre,$tam,$placeHolder,$texto,$valor=false){
        $this->arrayCampo[$nombre]=$texto;
        echo '<div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">'.$texto.'</label>
                <input type="text" class="form-control" id="'.$nombre.'" placeHolder="'.$placeHolder.'"';
                if($this->modo==0){echo " value='".$valor."'"; }
                    echo ' ></div>';      
        return($texto);   
    }
    public function addTextareaBoty($nombre,$cols,$rows,$texto,$placeHolder,$valor=false){
         $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;        
        
        echo '<div class="mb-3">
              <label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>
              <textarea class="form-control" id="'.$nombre.'" name="'.$nombre.'" cols="'.$cols.'" rows="'.$rows.'">';
              if($this->modo==0){
                echo $valor;
            }
              echo '</textarea>
              </div>';		  
        return(true);    
    }
	  public function addPassword($nombre,$tam,$placeHolder,$texto,$valor=false){
        $this->modo=$modo;
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto; 
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-key"></i></span>
        <input type="password" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';
        return($texto);   
    }
     public function addPassword2($nombre,$tam,$placeHolder,$texto,$valor=false){
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fad fa-lock-alt"></i></span>
        <input type="password" class="form-control" name="'.$texto.'" id="'.$texto.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';       
        return($texto);   
    }
    

    public function addShop($nombre,$tam,$placeHolder,$texto,$valor=false){        
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;     
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-shopping-basket"></i></span>
        <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';       
        return($texto);   
    }


     public function addUrl($nombre,$tam,$placeHolder,$texto,$valor=false){        
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;     
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-network-wired"></i></span>
        <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';       
        return($texto);   
    }      
    public function initCalendar(){
        echo '   <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
      
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>';
        
    }
     
    public function addLinkedin($nombre,$tam,$placeHolder,$texto,$valor=false){        
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;     
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fab fa-linkedin"></i></span>
        <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';       
        return($texto);   
    }      
    public function addCheck1Boty($arrOp,$pos,$nombre,$texto){
        $this->arrayValidate[$nombre]["tipo"]="check";
        $this->arrayCampo[$nombre]=$texto;        
              echo '<div align="center"><input type="checkbox" style="margin-top:0px;" name="'.$nombre.'" id="'.$nombre.'" value="'.$clave.'">';
              echo " Estoy de acuerdo con las <a href='index.php?mod=pag&idPag=7' target='_blank'>Condiciones del servicio</a></div>";
    }
    public function addInstagram($nombre,$tam,$placeHolder,$texto,$valor=false){        
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;     
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fab fa-instagram"></i></span>
        <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';       
        return($texto);   
    }      

    public function addTwitter($nombre,$tam,$placeHolder,$texto,$valor=false){        
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;     
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fab fa-twitter"></i></span>
        <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';       
        return($texto);   
    }      


    public function addFace($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false){

        $this->modo=$modo;
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto; 
            echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
            echo '<div class="input-group mb-3">        
            <span class="input-group-text" id="basic-addon1"><i class="fab fa-facebook"></i></span>
            <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
            if($this->modo==0){echo " value='".$valor."'"; }        
            echo '>
            </div>';         
            return($texto);   
        }   	
 
   public function addHidden($t=false,$m=false){
        echo "<input type='hidden' name='action' id='action' value='true'/>";
   }
   public function addRegion(){
    $sql="select* from mm_region order by idRegion asc";
    $q=mysqli_query($this->link,$sql);    
    echo "<div style='margin-top:10px;'>Región</div>";
    echo '<select  class="form-control form-control-mb" name="region" id="region"  style="margin-top:5px;margin-bottom:5px;" aria-label=".form-select-lg example">';
    echo '<option value="0" selected="selected">Región</option>';
       while($r=mysqli_fetch_array($q)){
           echo '<option value="'.$r["idRegion"].'">'.utf8_encode($r["nombre"]).'</option>';
       }
       
    echo '</select>';
   }
   public function addCiudad(){
    echo "<div style='margin-top:10px;'>Ciudad</div>";
    echo '	<select name="ciudad"  style="margin-top:5px;margin-bottom:5px;" id="ciudad"
    class="form-control form-control-mb" aria-label=".form-select-lg example">
   <option value="0" selected="selected">Ciudad</option>
    
   </select>';
   }
   public function addComuna(){
    echo "<div style='margin-top:10px;'>Comuna</div>";
    echo '<select   style="margin-top:5px;margin-bottom:5px;" name="comuna" id="comuna"
    class="form-control form-control-mb" aria-label=".form-select-lg example">
   <option value="0" selected="selected">Comuna</option>
    
   </select>';
   }
    public function addTextarea($nombre,$cols,$rows,$texto,$placeHolder,$valor=false,$modo){
        $this->modo = $modo;
         $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;        
        echo '<div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">'.ucfirst($texto).'</label>
        <textarea class="form-control" id="'.$nombre.'" name="'.$nombre.'" cols="'.$cols.'" rows="'.$rows.'">';
        if($this->modo==0){
          echo $valor;
      }
        echo '</textarea>
        </div>';		  
        return(true);    
    }
    public function addPrecio($nombre,$tam,$placeHolder,$texto,$valor=false){
        $this->arrayValidate[$nombre]["tipo"]="texto";
      $this->arrayCampo[$nombre]=$texto;        
      echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
      echo '<div class="input-group mb-4">        
      <span class="input-group-text" id="basic-addon1"><i class="fas fa-dollar-sign"></i></span>
      <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
      if($this->modo==0){echo " value='".$valor."'"; }        
      echo '>
      </div>';
      return($texto);   
  } 

  public function addUser($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false){
    $this->modo=$modo;
    $this->arrayValidate[$nombre]["tipo"]="texto";
    $this->arrayCampo[$nombre]=$texto; 
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
        <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';         
        return($texto);   
    }   

  public function addEmail($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false){
    $this->modo=$modo;
    $this->arrayValidate[$nombre]["tipo"]="texto";
    $this->arrayCampo[$nombre]=$texto; 
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-envelope"></i></span>
        <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';         
        return($texto);   
    }   	
   
    public function addTelefono($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false){
        $this->modo=$modo;
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;      
      echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
      echo '<div class="input-group mb-3">        
      <span class="input-group-text" id="basic-addon1"><i class="fas fa-phone"></i></span>
      <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
      if($this->modo==0){echo " value='".$valor."'"; }        
      echo '>
      </div>';       
      return($texto);   
  }
  public function addCelular($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false){
    $this->modo=$modo;
    $this->arrayValidate[$nombre]["tipo"]="texto";
    $this->arrayCampo[$nombre]=$texto;      
  echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
  echo '<div class="input-group mb-3">        
  <span class="input-group-text" id="basic-addon1"><i class="fas fa-mobile"></i></span>
  <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
  if($this->modo==0){echo " value='".$valor."'"; }        
  echo '>
  </div>';       
  return($texto);   
}


public function addCalendar($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false){
    $this->modo=$modo;
    $this->arrayValidate[$nombre]["tipo"]="texto";
    $this->arrayCampo[$nombre]=$texto;      
  echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
  echo '<div class="input-group mb-3">        
  <span class="input-group-text" id="basic-addon1"><i class="fas fa-calendar"></i></span>
  <input type="date" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
  if($this->modo==0){echo " value='".$valor."'"; }        
  echo '>
  </div>';       
  return($texto);   
}
public function addWebSite($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false){
    $this->modo=$modo;
    $this->arrayValidate[$nombre]["tipo"]="texto";
    $this->arrayCampo[$nombre]=$texto;      
  echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
  echo '<div class="input-group mb-3">        
  <span class="input-group-text" id="basic-addon1"><i class="fas fa-globe-americas"></i></span>
  <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
  if($this->modo==0){echo " value='".$valor."'"; }        
  echo '>
  </div>';       
  return($texto);   
}

public function addWasap($nombre,$tam,$placeHolder,$texto,$valor=false,$modo=false){
    $this->modo=$modo;
    $this->arrayValidate[$nombre]["tipo"]="texto";
    $this->arrayCampo[$nombre]=$texto;      
  echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
  echo '<div class="input-group mb-3">        
  <span class="input-group-text" id="basic-addon1"><i class="fab fa-whatsapp"></i></span>
  <input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
  if($this->modo==0){echo " value='".$valor."'"; }        
  echo '>
  </div>';       
  return($texto);   
}
  public function addEmailBoty($nombre,$tam,$placeHolder,$texto,$valor=false){
        $this->arrayValidate[$nombre]["tipo"]="email";
        $this->arrayCampo[$nombre]=$texto;                
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-envelope"></i></span>
        <input type="text" class="form-control" name="'.$texto.'" id="'.$texto.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';             
        return($texto);   
    }   		
    public function addEmail2($nombre,$tam,$placeHolder,$texto,$valor=false){
          $this->arrayValidate[$nombre]["tipo"]="email";
        $this->arrayCampo[$nombre]=$texto;        
        echo '<label for="exampleFormControlTextarea1" class="form-label">'.$texto.'</label>';
        echo '<div class="input-group mb-3">        
        <span class="input-group-text" id="basic-addon1"><i class="fas fa-envelope"></i></span>
        <input type="text" class="form-control" name="'.$texto.'" id="'.$texto.'" placeHolder="'.$placeHolder.'" aria-label="'.$texto.'" aria-describedby="basic-addon1" ';
        if($this->modo==0){echo " value='".$valor."'"; }        
        echo '>
        </div>';
        return($texto);   
    }   
    public function addSelect($nombre,$arr,$mensaje,$func,$texto,$seleccionado=false){
          $this->arrayValidate[$nombre]["tipo"]="select";
        $this->arrayCampo[$nombre]=$texto;
        
        echo '<div class="mb-3">';
        if($nombre=="idCodeudor"){
            echo '<label for="exampleFormControlInput1" class="form-label">'.$texto.' &nbsp;&nbsp;<a href="panel.php?op=17" class="btn btn-primary btn-sm" style="padding-top:1px; padding-bottom:1px;font-size:12px;">Crear Codeudor</a></label>';
        }else if($nombre=="idArrendatarios"){
            echo '<label for="exampleFormControlInput1" class="form-label">'.$texto.' &nbsp;&nbsp;<a href="panel.php?op=6" class="btn btn-primary btn-sm" style="padding-top:1px; padding-bottom:1px;font-size:12px;">Crear Arrendatario</a></label>';
        }else if($nombre=="idPropiedad"){
            echo '<label for="exampleFormControlInput1" class="form-label">'.$texto.' &nbsp;&nbsp;<a href="panel.php?op=5" class="btn btn-primary btn-sm" style="padding-top:1px; padding-bottom:1px;font-size:12px;">Crear Propiedad</a></label>';
        }else{
            echo '<label for="exampleFormControlInput1" class="form-label">'.$texto.'</label>';
        }
        


        echo '<select class="form-select" name="'.$nombre.'" id="'.$nombre.'" aria-label="'.$texto.'">';
        echo $seleccionado;
        echo "<pre>";
        print_r($arr);
        if($this->modo==0){
            echo "<option value='".$seleccionado."'  selected>".$arr[$seleccionado]."</option>";
        }else{
            echo "<option value='0' selected='selected'>".$mensaje."</option>";
        }
        foreach($arr as $clave=>$valor){
            echo "<option value='".$clave."'>".$valor."</option>";
        }
        echo '</select>';
        echo "</div>";
    }
    public function addButton3($nombre=false,$valor=false,$evento=false,$borrar=false){
        if($this->modo!=1){
            $valor="Guardar Cambios";
            
        } 
         $this->arrayValidate[$nombre][$texto]["texto"]=$validar;
        echo "<div style='margin-top:30px;'>";
        echo '<button type="button" name class="btn btn-primary btn-mb">Guardar Cambios</button>';
        echo "</div>";
        return(true);
    }
     public function addImagen($url,$texto){
          $this->arrayValidate[$nombre]["tipo"]="imagen";
        $this->arrayCampo[$nombre]=$texto;         
           if($this->numCol==1){echo "<tr><td align='left' width='13%'>".$texto." :</td></tr>";}else{echo "<tr><td align='right' width='13%'>".$texto."</td>";}  
        echo "<td>";
        echo "<img src='".$url."'/>";
        echo "</td></tr>";        
      
    }
    public function addButton($nombre=false,$valor=false,$evento=false,$borrar=false,$por=false){
        
         $this->arrayValidate[$nombre][$texto]["texto"]=$validar;
 
         echo "<div >";
         echo '<button type="button" name="enviar" style="width:'.$por.'%;" id="enviar" class="btn btn-primary btn-mb">'.$valor.'</button>';
         echo "</div>";
        return(true);
    }
	
	 public function addButtonBoty22($nombre=false,$valor=false,$evento=false,$borrar=false,$t){ 
        if($this->modo!=1){
            $valor="Publicar Aviso";
            
        } 
         $this->arrayValidate[$nombre][$texto]["texto"]=$validar;
         echo "<div style='margin-top:30px;'>";
         echo '<button type="button" name class="btn btn-primary btn-mb">Guardar Cambios</button>';
         echo "</div>";
        return(true);
    }	
	 public function addButton22($nombre=false,$valor=false,$evento=false,$borrar=false){
        if($this->modo!=1){
            $valor="Publicar Aviso";            
        } 
        $this->arrayValidate[$nombre][$texto]["texto"]=$validar;
        echo "<div style='margin-top:30px;'>";
        echo '<button type="button" name="enviar" id="enviar" class="btn btn-primary btn-mb">Guardar Cambios</button>';
        echo "</div>";
        return(true);
    }	
    public function addButton2($nombre=false,$valor=false,$evento=false,$borrar=false){
        if($this->modo!=1){
            $valor="Guardar Cambios";            
        } 
         $this->arrayValidate[$nombre][$texto]["texto"]=$validar;
         echo "<div style='margin-top:30px;'>";
         echo '<button type="button" name="enviar" id="enviar" class="btn btn-primary btn-mb">Guardar Cambios</button>';
         echo "</div>";
        return(true);
    }
	public function addButton2Boty($nombre=false,$valor=false,$evento=false,$borrar=false){
        if($this->modo!=1){
            $valor="Guardar Cambios";            
        } 
        $this->arrayValidate[$nombre][$texto]["texto"]=$validar;     
        echo "<div style='margin-top:30px;'>";
        echo '<button type="button" name="enviar" id="enviar" class="btn btn-primary btn-mb">Guardar Cambios</button>';
        echo "</div>";      
        return(true);
    }
    public function addButton2Boty2($nombre=false,$valor=false,$evento=false,$borrar=false){
        if($this->modo!=1){
            $valor="Guardar Cambios";            
        } 
         $this->arrayValidate[$nombre][$texto]["texto"]=$validar;      
         echo "<div style='margin-top:30px;'>";
         echo '<button type="button" name="enviar" id="enviar" class="btn btn-primary btn-mb">Guardar Cambios</button>';
         echo "</div>";      
        return(true);
    }
	public function addButton22Boty($nombre=false,$valor=false,$evento=false,$borrar=false){
        if($this->modo!=1){
            $valor="Guardar Cambios";            
        } 
         $this->arrayValidate[$nombre][$texto]["texto"]=$validar;      
         echo "<div style='margin-top:30px;'>";
         echo '<button type="submit" name="enviar" id="enviar" class="btn btn-primary btn-mb">Guardar Cambios</button>';
         echo "</div>";      
        return(true);
    }
    public function addSubmit($nombre,$valor){
        echo "<tr><td>&nbsp;</td><td>";
        echo '<input type="Submit"  style="margin-top:20px; margin-bottom:20px;" class="btn btn-primary btn-md" name="enviar" id="enviar" value="'.$valor.'">';
        echo "</td></tr>";
        return(true);        
    }  
    public function setMensaje($mensaje,$redirect){
        $this->mensaje=$mensaje;
        $this->redirect=$redirect;
        return(true);
    }
    public function cerrarForm(){ 
        $this->cerrarTabla();
        if($this->tipoForm=="ajax"){
            echo '</div>';
        }
        echo "</form>";     
    }
}
 ?>