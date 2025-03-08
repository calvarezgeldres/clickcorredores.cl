<?php
/*
Clase: Framework Formularios 1.0
Revision :17/2/2014
autor: Prog Web Chile 
*/
 
  @ini_set( 'upload_max_size' , '512M' );
@ini_set( 'post_max_size', '512M');
@ini_set( 'max_execution_time', '6000' );
@ini_set( 'max_input_time', '6000' );
header('Content-Type: text/html; charset=UTF-8');

class xForm{
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
    public function __construct(){
        $this->jquery();       
    }

    public function getDataPost(){
        $this->datosPost=$_POST;
        return($this->datosPost);
    }
    public function getDataArch(){
        return($this->datosArch);
    }
    public function jquery(){      
   //    echo '<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />';
		echo '
            <script src="./js/jquery.min.js"></script>';
			 
    }
    public function enviarEmail(){
        
    }
    public function uploadFiles(){
    	 echo "Ok";
        require_once("./clases/class.upload.php");
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
    foreach ($files as $file) { 
        $handle = new Upload($file);
         
        if ($handle->uploaded) {            	
		$fecha=date("mdY-H_i_s");
		$_SESSION["fecha"]=$fecha;
        $handle->image_resize            = true;
		$handle->image_ratio_y           = true;
        $handle->image_x= 640;
		$handle->image_y =480;
		$handle->file_new_name_body = $_SESSION["fecha"];
		$handle->auto_create_dir = true;
		$handle->dir_auto_chmod = true;
		$handle->dir_chmod = 0777;
		$handle->image_convert = 'jpg';
		$handle->jpeg_quality = 100;	
	       $handle->Process("./upload/");
        if ($handle->processed) {
               $arch[]=$handle->file_dst_name; 
            }
        }
    } 
    $this->datosArch=$arch;
    return($arch);
    }
    public function procesarArch(){
     if(empty($_FILES["my_field"]["name"][0])){
        $this->error[]="Error.-Debe seleccionar algun archivo";
     }else{
 
        $this->uploadFiles();
     }
     return(true);
    }
    public function validarJquery($post=false){
          
              echo '<script>
              
              var filter = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
              var num=/([0-9])/;
            $(document).ready(function(){
                 $("#enviar").click(function(){
                     ';
             
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
                                                              
                        } else if($array["tipo"]=="radio"){
                            echo 'var op=$("input[name='.$clave.']:checked").val();';
                            //echo 'alert(op);';
                                                              
                        }else if($array["tipo"]=="texto"){
                            echo '
                            if('.$clave.'.length==0){
                                alert("ingresa el '.$this->arrayCampo[$clave].'");
                                $("#'.$clave.'").focus();
                                return(false);
                            }
                            ';    
                         
                        }else  if($array["tipo"]=="cumple"){
                            echo '
                            var dia=$("#dia").val();
                            var mes=$("#mes").val();
                            var ano=$("#ano").val();
                            if(dia==0){
                                alert("Seleccione dia");
                                $("#dia").focus();
                                return(false);
                            }else if(mes==0){                                
                                alert("Seleccione mes");
                                $("#mes").focus();
                                return(false);
                            }else if(ano==0){
                                alert("seleccione a�o");
                                $("#ano").focus();
                                return(false);
                            }
                            ';    
                        }else if($array["tipo"]=="select"){
                            echo '
                            if('.$clave.'==0){
                                alert("Selecciona el '.$this->arrayCampo[$clave].'");
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
                            }
                            ';
                        }else{
                            if($this->tipoForm=="ajax"){                                
                                echo "var cadena='';";
                                foreach($this->arrayValidate as $clave1=>$array1){
                                    if(!empty($clave1) && $clave1!="Enviar"){
                                        echo "var c=$('#".$clave1."').val();";                                        
                                        echo "if(c!=undefined){";
                                         echo "cadena+='".$clave1."='+$('#".$clave1."').val()+'&';";
                                         echo "}";
                                    }                                                    
                                }
                                    echo "cadena+='t=1';";
                                                
                                    echo '$.ajax({
                                          type: "POST",
                                          async:true,
                                          url: "'.$this->target.'",
                                           data: cadena,
                                          success: function(datos){
                                            if(datos.indexOf("ok")!=-1){
                                                $("#res").html("Se Guardo con exito!!");
                                            }
                                          }
                                    });';                                   
                            }else{  
                                echo '$("#form1").submit();';
                            }
                         }
                    }
                    echo '
                  
                  
                });                
                return(false);
            });
            </script>
            ';
    }
    public function abrirForm($modo,$upload=false,$nombre,$metodo,$target,$numCol,$tipoForm=false){
      
        $this->nombre=$nombre;
        $this->numCol=$numCol;
        $this->tipoForm=$tipoForm;
        $this->modo=$modo;
        $this->metodo=$metodo;
        $this->target=$target;
        $this->upload=$upload;
        echo '<form   name="'.$this->nombre.'" ';
        if($this->upload){
            echo 'enctype="multipart/form-data"';
        }
        echo ' method="'.$this->metodo.'"  id="'.$this->nombre.'"/>';
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
        // si el submit sera via ajax o submit      
     
       $this->validarJquery($_POST);                    
       
        return(true);
    }
    
    public function redirect($target){
        header("location:".$target);
        exit;
    }
    
    public function setDescripcion($des){        
        echo "<tr><td colspan='".$this->numCol."'>";
        echo "<span style='font-size:14px;'>".$des."</span>";
        echo "</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
    }
    
    public function setTitulo($titulo){
        $this->titulo=$titulo;
        echo "<tr><td colspan='".$this->numCol."'>";
        echo "<span style='font-size:14px; font-weight:bold;'>".$this->titulo."</span>";
        echo "</td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
    }
    public function setGoogle($url){
        echo "<tr><td colspan='".$this->numCol."'>";
        echo "<a href='".$url."'><img src='./imagen/google.jpg'/></a>";
        echo "</td></tr>";
                
    }
    public function separacion(){
        echo "<tr><td colspan='".$this->numCol."'><hr></td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";              
    }
    public function setFacebook($url){
        echo "<tr><td colspan='".$this->numCol."'>";
        echo "<a href='".$url."'><img src='./imagen/facebook.jpg'/></a>";
        echo "</td></tr>";
  
        
    }
     
    public function addTelefono($nombre,$tam,$placeHolder,$texto,$valor=false,$t=false){

	$this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;
		 if($t==false){$t=11;}
		 echo "<div class='row'>";
		echo '<div class="form-group col-md-'.$t.'">
    <label for="exampleInputAmount">';
	echo $texto;
	echo '</label>
    <div class="input-group">';
      echo '<span class="input-group-addon"><i class="fa fa-phone"></i></span>';
          echo '<input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeholder="'.$placeHolder.'" ';
	  if($this->modo=="editar"){
            echo "value='".$valor."'";
        }
	  echo '>
 
    </div></div>
  </div>';
  
	
		 
        return($text);   
	}
	public function addPrecio($nombre,$tam,$placeHolder,$texto,$valor=false,$t=false){
		
		$this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;
		 if($t==false){$t=11;}
		 echo "<div class='row'>";
		echo '<div class="form-group col-md-'.$t.'">
    <label for="exampleInputAmount">';
	echo $texto;
	echo '</label>
    <div class="input-group">';
      echo '<span class="input-group-addon">$</span>';
      echo '<input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeholder="'.$placeHolder.'" ';
	  if($this->modo=="editar"){
            echo "value='".$valor."'";
        }
	  echo '>
 
    </div></div>
  </div>';
   return(false);
		
		  
		 
	}
	public function addPorcentaje(){
		$this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;
			echo '<div class="form-group">
    <label   for="exampleInputAmount">';
	echo $texto;
	echo '</label>
    <div class="input-group">';
      echo '<span class="input-group-addon">%</span>';
      echo '<input type="text" class="form-control" name="'.$nombre.'" id="'.$nombre.'" placeholder="'.$placeHolder.'" ';
	  if($this->modo=="editar"){
            echo "value='".$valor."'";
        }
	  echo '>
 
    </div>
  </div>';
         return($text); 
	}
 
    public function addText($nombre,$tam,$placeHolder,$texto,$valor=false,$t=false){	
	    $this->arrayCampo[$nombre]=$texto;
		$this->arrayValidate[$nombre]["tipo"]="texto";
		echo "<div class='row'>	";
		if($t==false){$t=11;}
		echo '<div class="form-group col-xs-'.$t.'">';
		echo '<label for="exampleInputEmail1" class="control-label">';
		echo $texto;
		echo '</label>';
        echo '<input type="text" class="form-control input-md"   placeholder="'.ucfirst($texto).'"    name="'.$nombre.'" id="'.$nombre.'"';
        if($this->modo=="editar"){echo "value='".$valor."'";}
		echo '>';
		echo '</div> </div>';
        return($text);   
    }
	
	public function addText2($nombre,$tam,$placeHolder,$texto,$valor=false){
        $this->arrayCampo[$nombre]=$texto;
        if($this->numCol==1){echo "<tr><td align='left' width='1%'><span style='font-size:14px;'>".utf8_encode($texto)." :</span></td></tr>";}else{echo "<tr><td align='left' width='1%'><span style='font-size:14px;'>".utf8_encode($texto)."</font></td>";}  
        echo "<td>";
        echo '<input type="text" class="form-control input-sm" style="margin-top:2px; margin-bottom:2px; width:'.$tam.'px;" placeholder="'.ucfirst($texto).'"   size="'.$tam.'" name="'.$nombre.'" id="'.$nombre.'"';
        if($this->modo=="editar"){
            echo "value='".$valor."'";
        }
        echo '>';
        echo "</td></tr>";        
        return($text);   
    }
       public function addRedSocial($nombre,$tam,$placeHolder,$texto,$valor=false,$mostrarValor=false){
     
        $this->arrayValidate[$nombre]["tipo"]="redSocial";
        $this->arrayCampo[$nombre]=$texto;
        if($this->numCol==1){echo "<tr><td align='left' width='20%'><span style='font-size:12px;'>".utf8_encode($texto)." :</span></td></tr>";}else{echo "<tr><td align='right' width='20%'><span style='font-size:12px;'>".utf8_encode($texto)."</font></td>";}  
        echo "<td style='padding:2px; margin:8px;'>";
        echo '<input type="text" size="'.$tam.'" name="'.$nombre.'" id="'.$nombre.'"';
        if($this->modo!="editar" && $mostrarValor){
            echo "value='".$valor."'";
        }
        if($this->modo=="editar"){
            echo "value='".$valor."'";
        }
        echo '>';
        echo "</td></tr>";        
        return($text);   
    }
     public function addPassword($nombre,$tam,$placeHolder,$texto,$valor=false,$t=false){
         $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;
		 if($t==false){$t=11;}
		 echo "<div class='row'>";
		echo '<div class="form-group col-md-'.$t.'">';
		echo '<label for="exampleInputEmail1">';
		echo $texto;
		echo '</label>';
		echo ' <div class="input-group">';
      echo '<span class="input-group-addon"><i class="fa fa-lock"></i></span>';
        echo '<input type="password" class="form-control input-sm"  placeholder="'.ucfirst($texto).'"    name="'.$nombre.'" id="'.$nombre.'"';
        if($this->modo=="editar"){
            echo "value='".$valor."'";
        }
        echo '>';
		echo '</div></div></div>';

             
        return($text);   
    }
     public function addUrl($nombre,$tam,$placeHolder,$texto,$valor=false,$t=false){
		   $this->arrayCampo[$nombre]=$texto;
		$this->arrayValidate[$nombre]["tipo"]="texto";
		 if($t==false){$t=11;}
		 echo "<div class='row'>";
			echo '<div class="form-group col-sm-'.$t.'">
    <label   for="exampleInputAmount">';
	echo $texto;
	echo '</label>
    <div class="input-group">';
      echo '<span class="input-group-addon">http://</span>';
      echo '<input type="text" class="form-control"name="'.$nombre.'" id="'.$nombre.'" placeholder="'.$placeHolder.'" ';
	  if($this->modo=="editar"){
            echo "value='".$valor."'";
        }
	  echo '>
 
    </div></div>
  </div>';
   return($text);   
	}
      public function addNumerico($nombre,$tam,$placeHolder,$texto,$valor=false){
       $this->arrayValidate[$nombre]["tipo"]="numerico";
        $this->arrayCampo[$nombre]=$texto;      
       echo '<div class="form-group">';
		echo '<label for="exampleInputEmail1">';
		echo $texto;
		echo '</label>';
        echo '<input type="password" class="form-control input-sm" style="margin-top:2px; margin-bottom:2px; width:'.$tam.'px;" placeholder="'.ucfirst($texto).'"   size="'.$tam.'" name="'.$nombre.'" id="'.$nombre.'"';
        if($this->modo=="editar"){
            echo "value='".$valor."'";
        }
        echo '>';
		echo '</div>';      
        return($text);   
    }
      
    public function addTextarea($nombre,$cols,$rows,$texto,$placeholder,$valor=false,$t=false){
         $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto; 
		 if($t==false){$t=11;}
		 echo "<div class='row'>";
		echo '<div class="form-group col-md-'.$t.'">';
		echo '<label for="exampleInputEmail1">';
		echo $texto;
		echo '</label>';		
		echo '<textarea  placeholder="'.ucfirst(utf8_encode($texto)).'"  class="form-control input-sm"  name="'.$nombre.'" cols="'.$cols.'" rows="'.$rows.'" id="'.$nombre.'">';
            if($this->modo==0){
                echo $valor;
            }
        echo '</textarea>';
		echo "</div></div>";
        return(true);    
    }
	public function colorPicker4(){
		echo '<div class="form-group">
                <label>Color picker:</label>
                <input type="text" class="form-control my-colorpicker1 colorpicker-element">
              </div>';
	}
 
	public function selectMultiple(){
		echo '<div class="form-group">
                <label>Multiple</label>
                <select class="form-control select2 select2-hidden-accessible" multiple="" data-placeholder="Select a State" style="width: 100%;" tabindex="-1" aria-hidden="true">
                  <option>Alabama</option>
                  <option>Alaska</option>
                  <option>California</option>
                  <option>Delaware</option>
                  <option>Tennessee</option>
                  <option>Texas</option>
                  <option>Washington</option>
                </select><span class="select2 select2-container select2-container--default select2-container--above select2-container--focus" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--multiple" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="-1"><ul class="select2-selection__rendered"><li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="textbox" aria-autocomplete="list" placeholder="Select a State" style="width: 517.5px;"></li></ul></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
              </div>';
	}
	public function addCalendarSelect2(){
		echo '<div class="form-group">
                <label>Date range button:</label>

                <div class="input-group">
                  <button type="button" class="btn btn-default pull-right" id="daterange-btn">
                    <span>
                      <i class="fa fa-calendar"></i> Date range picker
                    </span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
              </div>';
	}
	public function addCalendarRange2(){
		echo '<div class="form-group">
                <label>Date range:</label>

                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="reservation">
                </div>
                <!-- /.input group -->
              </div>';
	}
    public function initCalendar(){
        echo '
          <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
          <link rel="stylesheet" href="/resources/demos/style.css">
          <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
          <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
          ';
    }
	public function addCalendar2($nombre){
		echo '<script>
        $( function() {
          $( "#'.$nombre.'" ).datepicker();
        } );
        </script>';

        echo "<div class='row'>	";
		
		
		echo '<label for="exampleInputEmail1" class="control-label">';
		echo $texto;
		echo '</label>';
        echo '<input type="text" class="form-control input-md"   placeholder="'.ucfirst($texto).'"    name="'.$nombre.'" id="'.$nombre.'"';
        if($this->modo=="editar"){echo "value='".$valor."'";}
		echo '>';
		echo '</div>';
	}
	public function addTime2(){
		echo '<div class="form-group">
                  <label>Time picker:</label>

                  <div class="input-group">
                    <input type="text" class="form-control timepicker">

                    <div class="input-group-addon">
                      <i class="fa fa-clock-o"></i>
                    </div>
                  </div>
                  <!-- /.input group -->
                </div>';
	}
	public function colorPicker2(){
		echo '<div class="form-group">
                <label>Color picker with addon:</label>

                <div class="input-group my-colorpicker2 colorpicker-element">
                  <input type="text" class="form-control">

                  <div class="input-group-addon">
                    <i></i>
                  </div>
                </div>
                <!-- /.input group -->
              </div>';
	}
	public function addSelectTLC(){
		echo '<select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true">
                  <option selected="selected">Alabama</option>
                  <option>Alaska</option>
                  <option>California</option>
                  <option>Delaware</option>
                  <option>Tennessee</option>
                  <option>Texas</option>
                  <option>Washington</option>
                </select>';
	}
    public function addEmail($nombre,$tam,$placeHolder,$texto,$valor=false){
          $this->arrayValidate[$nombre]["tipo"]="email";
        $this->arrayCampo[$nombre]=$texto;   
		 if($t==false){$t=11;}
		 echo "<div class='row'>";
			echo '<div class="form-group col-md-'.$t.'">
    <label class="sr-only" for="exampleInputAmount">';
	echo $texto;
	echo '</label>
    <div class="input-group">';
      echo '<span class="input-group-addon"><i class="fa fa-envelope"></i></span>';
      echo '<input type="text" class="form-control"name="'.$nombre.'" id="'.$nombre.'" placeholder="'.$placeHolder.'" ';
	  if($this->modo=="editar"){
            echo "value='".$valor."'";
        }
	  echo '>
 
    </div>
  </div></div>';
        return($text);   
    }   

    public function jTymce(){
        echo '
<script type="text/javascript">
tinymce.init({
   
     selector: "textarea",
        plugins: [
                "jbimages advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons template textcolor paste fullpage textcolor"
        ],

        toolbar1: "jbimages newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | inserttime preview | forecolor backcolor",
        toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",

        menubar: false,';
        
        echo "                        
        toolbar_items_size: 'small',

        style_formats: [
                {title: 'Bold text', inline: 'b'},
                {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                {title: 'Example 1', inline: 'span', classes: 'example1'},
                {title: 'Example 2', inline: 'span', classes: 'example2'},
                {title: 'Table styles'},
                {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
        ],
        relative_urls : false,
        templates: [
                {title: 'Test template 1', content: 'Test 1'},
                {title: 'Test template 2', content: 'Test 2'}
        ]
});
</script>;";
    }
    public function addTymce(){
          $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->jTymce();        
         if($this->numCol==1){echo "<tr><td align='left' width='20%'>".$texto." :</td></tr>";}else{echo "<tr><td align='right' width='20%'>".$texto."</td>";}  
        echo "<td>";
        echo '<textarea name="content" style="width:100%"></textarea>';
        echo "</td></tr>";        
        return($text);   
    }
 
 
    
    public function jDate($nombre){
        echo '<style>
        /*widget*/
        .ui-widget { font-family: Verdana,Arial,sans-serif; font-size: .9em; }

        /*datepicket*/
        .ui-datepicker { /*no width*/ padding: .2em .2em 0; }
        .ui-datepicker table {width: 100%; font-size: 0.8em; border-collapse: collapse; margin:0 0 .4em; }
        </style>';
        echo '<script>
        $(function() {';
        echo "  $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '<Ant',
        nextText: 'Sig>',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Mi�rcoles', 'Jueves', 'Viernes', 'S�bado'],
        dayNamesShort: ['Dom','Lun','Mar','Mi�','Juv','Vie','S�b'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S�'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);";
        echo '$('.$nombre.').datepicker();
        });
        </script>';
    }
 
    public function addCalendar($nombre,$placeHolder,$texto,$valor=false){
          $this->arrayValidate[$nombre]["tipo"]="texto";
          $this->arrayCampo[$nombre]=$texto;
        $this->jDate($nombre);
        if($this->numCol==1){echo "<tr><td align='left' width='20%'>".$texto." :</td></tr>";}else{echo "<tr><td align='right' width='20%'>".$texto."</td>";}  
        echo "<td>";
        echo '<input type="text" id="'.$nombre.'"';
        if($this->modo==0){
            echo " value='".$valor."' ";
        }
        echo '/>';
        echo "</td></tr>";
        
    }
    public function addCumple($texto){
          $this->arrayValidate["cumple"]["tipo"]="cumple";
        $this->arrayCampo[$nombre]=$texto;         
        if($this->numCol==1){echo "<tr><td align='left' width='20%'>".$texto." :</td></tr>";}else{echo "<tr><td align='right' width='20%'>".$texto."</td>";}  
        echo "<td>";
        echo "<table width='1%' border=0>";
        echo "<tr>";
        echo "<td>";
        echo "<select name='dia' id='dia'>";
        echo "<option value='0' selected='selected'>Dia</option>";
        for($d=1; $d<=31; $d++){
            echo "<option value='".$d."'>".$d."</option>";
        }
        $mes=array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        echo "</select>";
        echo "</td>";
        echo "<td>";
        echo "<select name='mes' id='mes'>";
        echo "<option value='0'>Mes</option>";
        for($m=1; $m<=count($mes); $m++){
            echo "<option value='".$m."'>".$mes[$m]."</option>";
        }        
        echo "</select>";
        echo "</td>";
        echo "<td>";
        echo "<select name='ano' id='ano'>";
        echo "<option value='0' selected='selected'>A�o</option>";
        for($i=1950; $i<=2013; $i++){
            echo "<option value='".$i."'>".$i."</option>";
        }
        echo "</select>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</td></tr>";        
        return($text);   
    }
   public function addSelect($nombre,$arr,$mensaje,$func,$texto,$seleccionado=false,$t=false){
          $this->arrayValidate[$nombre]["tipo"]="select";
        $this->arrayCampo[$nombre]=$texto;   
		echo "<div class='row'>";
		if($t==false){$t=11;}
		echo '<div class="form-group col-xs-'.$t.'">';
		echo '<label for="exampleInputName2">'.$texto.'</label>';
		   echo '<select  class="form-control input-md"   name="'.$nombre.'" id="'.$nombre.'"';
        if($func){
            echo ' onchange="ejecutar(this.value); ";';
        }
        echo '>';
        if($this->modo==0){
            echo "<option value='".$seleccionado."' selected='selected'>".$arr[$seleccionado]."</option>";
        }else{
            echo "<option value='0' selected='selected'>".$mensaje."</option>";
        }
        foreach($arr as $clave=>$valor){
            echo "<option value='".$clave."'>".$valor."</option>";
        }
        echo '</select>';
		echo '</div>';
		echo "</div>";
              
    }
    
    
    public function addCheck($arrOp,$pos,$nombre,$texto){
          $this->arrayValidate[$nombre]["tipo"]="check";
        $this->arrayCampo[$nombre]=$texto;        
        if($this->numCol==1){
            echo "<tr><td align='left' valign='top' width='20%'>".$texto." :</td></tr>";
        }else{
            echo "<tr><td align='right' valign='top' width='20%'><span style='font-size:12px;'>".$texto."</span></td>";
        }
        echo "<td>";
        echo "<table width='1%' border=0>";        
        if($pos==1){
            // hacia abajo
            foreach($arrOp as $clave=>$valor){                
                echo "<tr><td style='padding:2px; margin:8px;' width='1%'>";
                echo '<input type="checkbox" name="'.$nombre.'" id="'.$nombre.'" value="'.$clave.'" ';
                if($valor=="checked"){
                    echo 'checked="checked" ';
                }
                echo '>';
                echo "</td>";
                echo "<td>";
                echo $clave;
                echo "</td>";
                echo "</tr>";   
            }
        }else{
            // hacia los lados
            echo "<tr>";  
            foreach($arrOp as $clave=>$valor){                 
                echo "<td width='1%' style='padding:2px; margin:8px;'>";                    
                echo '<input type="checkbox" name="'.$nombre.'" id="'.$nombre.'" value="'.$clave.'" ';
                if($valor==1){                    
                    echo 'checked="checked"';
                }
                echo '>';
                                
                echo "</td>";
                echo "<td>".$clave."</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        echo "</td>";
        echo "</tr>";
    }
    public function addImagen($url,$texto){
          $this->arrayValidate[$nombre]["tipo"]="imagen";
        $this->arrayCampo[$nombre]=$texto;         
           if($this->numCol==1){echo "<tr><td align='left' width='20%'>".$texto." :</td></tr>";}else{echo "<tr><td align='right' width='20%'>".$texto."</td>";}  
        echo "<td style='padding:2px; margin:8px;'>";
        echo "<img src='".$url."'/>";
        echo "</td></tr>";        
        return($text);   
    }
     public function addRadio($arrOp,$pos,$nombre,$texto){
          $this->arrayValidate[$nombre]["tipo"]="radio";
        $this->arrayCampo[$nombre]=$texto;         
        if($this->numCol==1){
            echo "<tr><td align='left' valign='top' width='13%'>".$texto." :</td></tr>";
        }else{
            echo "<tr><td align='left' valign='top' width='13%'><span style='font-size:12px;'>".$texto."</span></td>";
        }
        echo "<td>";
        echo "<table width='1%' border=0>";        
        if($pos==1){
            // hacia abajo
            foreach($arrOp as $clave=>$valor){
      
                echo "<tr><td width='1%'>";
                if($valor==1){
                    echo 'checked="checked" ';
                }
                echo '>&nbsp;&nbsp;';
                echo "</td>";
                echo "<td>";
                echo "<span style='font-size:12px;'>".$clave."</span>";
                echo "</td>";
                    echo "</tr>";   
            }
        }else{
            // hacia los lados
            echo "<tr>";  
			 
            foreach($arrOp as $clave=>$valor){                 
                echo "<td width='1%'>";                    
                echo '<input type="radio" style="margin-left:5px; margin-right:5px;" name="'.$nombre.'" id="'.$nombre.'" value="'.$clave.'" ';
                
                if($valor==1){                    
                    echo 'checked="checked"';
                }
                echo '>';
                                
                echo "</td>";
                echo "<td><span style='font-size:12px;'>".$clave."</span></td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        echo "</td>";
        echo "</tr>";
    }
    public function jqueryMultiple(){
        echo '<script type="text/javascript">
              $(function() {';
        echo '$("#file_upload").uploadify({';         
        echo "'method'    : 'post',
        'multi'     : true,        
        'swf'           : './js/uploadify.swf',
        'uploader'      : './js/upload.php',
         'onUploadSuccess' : function(file, data, response) {";  

            echo '} 
    });
 
		});
	</script>';   
     
    return($arch);
    }
    public function addFileMultiple($numero,$texto,$archivos=false){        
       $arch=$this->jqueryMultiple();
        echo "<pre>";
        print_r($_SESSION);
        echo "<tr><td valign='top' align='right'><span style='font-size:12px;'>".$texto."</span>:</td><td>";
        echo '	<input id="file_upload" name="file_upload" type="file" multiple="true"><div id="file_upload"></div>
            <div id="img"></div>';
        echo "</td></tr>";
        return($arch);
    }

    public function addFile($numero,$texto,$archivos=false){
        if($this->modo==0 && $archivos!=false){            
           echo "<div>";
            $numCol=9;
            $k++;
            foreach($archivos as $clave=>$valor){
             
                echo "<img src='".$valor."' width='50' height='50'/>";
             
                $k++;
                if($k%$numCol==0){
                    echo "</tr>";
                }
            }
          
        }
       
         echo $texto;
               
        
        if($numero==0){
            echo '<div><input type="file" size="32" name="my_field" value="" /><input type="hidden" name="action" value="image" /></div>';    
        }else{
            for($i=0; $i<$numero; $i++){
                echo '<div><input type="file" size="32" name="my_field[]" value="" /></div>';
            }
        }
        
        
        return(true);
    }
    
    public function addHidden($nombre,$valor){
          if($this->numCol==1){echo "<tr><td align='left' width='20%'>".$texto." :</td></tr>";}else{echo "<tr><td align='right' width='20%'>".$texto."</td>";}  
        echo "<td>";
        echo '<input type="hidden" name="'.$nombre.'" id="'.$nombre.'" value="'.$valor.'">';
        echo "</td></tr>";        
        return($text);   
    }
    public function addButtonReset($nombre,$valor){        
        echo "<tr><td colspan='".$this->numCol."'>";
        echo '<input type="reset" name="'.$nombre.'" id="'.$nombre.'" value="'.$valor.'">';
        echo "</td></tr>";
        return(true);
    }
    
    public function addButton($nombre=false,$valor=false,$evento=false,$borrar=false,$icon=false){
        if($this->modo!=1){
            $valor="Guardar Cambios";
            
        } 
         $this->arrayValidate[$nombre][$texto]["texto"]=$validar;
		 echo "<div class='row'>";
		 echo "<div>&nbsp;</div>";
		 echo "</div>";
        echo "<div class='row'>";
		echo "<div class='col-md-12'>";
        echo '<button   name="enviar"   class="btn btn-primary btn-mb" id="enviar" ><i class="'.$icon.'"></i>  '.$valor.'</button>';
		echo "</div></div>";
        
        return(true);
    }
    public function addSubmit($nombre,$valor){
        echo "<tr><td style='padding:2px; margin:8px;' colspan='".$this->numCol."'>";
        echo '<input type="Submit" name="'.$nombre.'" id="'.$nombre.'" value="'.$valor.'">';
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
        return($form);
    }
}
  
?>