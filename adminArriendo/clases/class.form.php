<?php
/*
Clase: Framework Formularios 1.0
Revision :17/2/2014
autor: Prog Web Chile 
*/
 
 
header('Content-Type: text/html; charset=UTF-8');


class form{
    private $modo;
    private $aulo;
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
		 
			 
    }
    public function enviarEmail(){
        
    }
    public function getDataErr(){
        foreach($this->datosErr as $clave=>$valor){
            if(!empty($valor)){
               $err[]=$valor; 
            }
        }
        return($err);
    }
    public function uploadFiles2(){ 
        
        require_once("./clases/class.upload.php");        
       $files = array();
       // elimina fotos sin orientacion
       $this->datosErr=array();
       
       
    foreach ($_FILES['my_field'] as $k => $l) {
        foreach ($l as $i => $v) {
            if (!array_key_exists($i, $files))
                $files[$i] = array();
            $files[$i][$k] = $v;
        }
    }
        $dir_dest="./upload/";
           
    // now we can loop through $files, and feed each element to the class
    $i=0;
    foreach ($files as $file) { 
        $handle = new Upload($file);         
        if ($handle->uploaded) {   
       
            if(isset($_POST["giro"])){
                $giro=$_POST["giro"];
                if($giro==1){
                    $handle->image_rotate          = '180';
                }else if($giro==2){
                    $handle->image_rotate          = '270';
                }else if($giro==3){
                    $handle->image_rotate          = '90';
                }
            }
        
        //$handle->image_rotate          = '90';

		$fecha=date("mdY-H_i_s");
		$_SESSION["fecha"]=$fecha;
        $handle->image_resize            = true;
        //$handle->image_ratio_y           = true;
        $imageinfo = array();
        $my_files =  $_FILES['my_field']['tmp_name'];
        foreach($my_files as $single_file) {
			if(!empty($single_file)) {
				$imageinfo[$single_file] = getimagesize($single_file);
			}
		}
        $p=$imageinfo;	
        foreach($p as $clave=>$valor){ 		
			if($p[$clave][0]<=640 && $p[$clave][1]>=500){			
				$handle->image_ratio_fill= true;
				$handle->image_background_color = '#f3f3f3';
			}else if($p[$clave][0]<=350){			
				$handle->image_ratio_fill= true;
				$handle->image_background_color = '#f3f3f3';
			}
        }
        
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
        $i++;
    } 
    
    $this->datosArch=$arch;
  
    return($arch);
    }

    public function procesarArch2(){
        if(empty($_FILES["my_field"]["name"][0])){
           $this->error[]="Error.-Debe seleccionar algun archivo";
        }else{ 
           $this->uploadFiles2();
        }
        return(true);
       }
    public function uploadFiles(){ 
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
    $i=0;
    foreach ($files as $file) { 
        $handle = new Upload($file);         
        if ($handle->uploaded) {   
        
       
        
        $handle->image_auto_rotate = true;

		$fecha=date("mdY-H_i_s");
		$_SESSION["fecha"]=$fecha;
        $handle->image_resize            = true;
        //$handle->image_ratio_y           = true;
        $imageinfo = array();
        $my_files =  $_FILES['my_field']['tmp_name'];
        foreach($my_files as $single_file) {
			if(!empty($single_file)) {
				$imageinfo[$single_file] = getimagesize($single_file);
			}
		}
        $p=$imageinfo;	
        foreach($p as $clave=>$valor){ 		
			if($p[$clave][0]<=640 && $p[$clave][1]>=500){			
				$handle->image_ratio_fill= true;
				$handle->image_background_color = '#f3f3f3';
			}else if($p[$clave][0]<=350){			
				$handle->image_ratio_fill= true;
				$handle->image_background_color = '#f3f3f3';
			}
        }
        
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
        $i++;
    } 
    
    $this->datosArch=$arch;
 
    return($arch);
    }

    public function uploadFiles3(){ 
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
    $i=0;
    foreach ($files as $file) { 
        $handle = new Upload($file);         
        if ($handle->uploaded) {   
        
       
        
        $handle->image_auto_rotate = true;

		$fecha=date("mdY-H_i_s");
		$_SESSION["fecha"]=$fecha;
        $handle->image_resize            = true;
        $handle->image_ratio_y           = true;
        $imageinfo = array();
        
        $handle->image_x= 880;
		$handle->image_y =1115;
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
        $i++;
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
    public function procesarArch3(){
        if(empty($_FILES["my_field"]["name"][0])){
           $this->error[]="Error.-Debe seleccionar algun archivo";
        }else{
    
           $this->uploadFiles3();
        }
        return(true);
       }
    public function validarJquery($post=false){
          
              echo '<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
              <script>
              
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
                        } 
                    }                     
                    echo 'else {
                        $("#enviar").attr("disabled","true");';
                    echo '$("#enviar").html("<span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span> Cargando..."); ';                                   
                    echo '$("#form1").submit();
                    }
                    ';
                 
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
        echo "<form name='form1' id='form1' enctype='multipart/form-data' method='post' action=''>";
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
    public function setArray($array,$nomVar){
        foreach($array as $clave=>$valor){
            $cadena.="'".$valor."',";
        }
       echo ' <script>
  $(function() {
    var availableTags = [';
    echo $cadena;
    echo '];    
    function split( val ) {
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }
 
    $('.$nomVar.') 
      .bind( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).data( "ui-autocomplete" ).menu.active ) {
          event.preventDefault();
        }
      })
      .autocomplete({
        minLength: 0,
        source: function( request, response ) {
          // delegate back to autocomplete, but extract the last term
          response( $.ui.autocomplete.filter(
            availableTags, extractLast( request.term ) ) );
        },
        focus: function() {
          // prevent value inserted on focus
          return false;
        },
        select: function( event, ui ) {
          var terms = split( this.value );
          // remove the current input
          terms.pop();
          // add the selected item
          terms.push( ui.item.value );
          // add placeholder to get the comma-and-space at the end
          terms.push( "" );
          this.value = terms.join( ", " );
          return false;
        }
      });
  });
  </script>';
    }
    public function addAutocompletar($nombre,$tam,$placeHolder,$texto,$valor=false,$array){
          $this->arrayValidate[$nombre]["tipo"]="texto";
       $this->arrayCampo[$nombre]=$texto;
        $this->setArray($array,$nombre);
        if($this->numCol==1){echo "<tr><td align='left' width='20%'>".$texto." :</td></tr>";}else{echo "<tr><td align='right' width='20%'>".$texto."</td>";}  
        echo "<td>";
        echo '<input id="'.$nombre.'" name="'.$nombre.'" size="50" ';
        if($this->modo==0){
            echo ' value="'.$valor.'" ';
        }
        echo ' />';
        echo "</td></tr>";        
    }
    public function addText($nombre,$tam,$placeHolder,$texto,$valor=false){
     
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;
        echo "<div>".ucfirst($nombre)."</div>";
        echo "<div>";
        echo '<input type="text" class="form-control input-sm" style="margin-top:2px; margin-bottom:2px; width:'.$tam.'px;" placeholder="'.ucfirst($texto).'"   size="'.$tam.'" name="'.$nombre.'" id="'.$nombre.'"';
        if($this->modo==0){
            echo "value='".$valor."'";
        }
        echo '>';
        echo "</div>";
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
     public function addPassword($nombre,$tam,$placeHolder,$texto,$valor=false){
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;
        if($this->numCol==1){echo "<tr><td align='left' width='13%'>".$texto." :</td></tr>";}else{echo "<tr><td align='left' width='13%'>".$texto."</td>";}  
        echo "<td>";
        echo '<input type="password" class="form-control input-sm" style="margin-top:2px; margin-bottom:2px; width:'.$tam.'px;" placeholder="'.ucfirst($texto).'"  size="'.$tam.'" name="'.$nombre.'" id="'.$nombre.'" placeholder="'.$placeHolder.'"';
        if($this->modo=="editar"){
            echo "value='".$valor."'";
        }
        echo '>';
        echo "</td></tr>";        
        return($text);   
    }
     public function addUrl($nombre,$tam,$placeHolder,$texto,$valor=false){
        $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;
        if($this->numCol==1){echo "<tr><td align='left' width='20%'>".$texto." :</td></tr>";}else{echo "<tr><td align='right' width='20%'>".$texto."</td>";}  
        echo "<td>";
        echo '<input type="text" size="'.$tam.'" name="'.$nombre.'" id="'.$nombre.'" placeholder="'.$placeHolder.'"';
        if($this->modo=="editar"){
            echo "value='".$valor."'";
        }else{
            echo "value='http://' ";
        }
        echo '>';
        echo "</td></tr>";        
        return($text);   
    }
      public function addNumerico($nombre,$tam,$placeHolder,$texto,$valor=false){
       $this->arrayValidate[$nombre]["tipo"]="numerico";
        $this->arrayCampo[$nombre]=$texto;      
        if($this->numCol==1){echo "<tr><td align='left' width='20%'>".$texto." :</td></tr>";}else{echo "<tr><td align='right' width='20%'>".$texto."</td>";}  
        echo "<td  style='padding:2px; margin:8px;'>";
        echo '<input type="text" size="'.$tam.'" name="'.$nombre.'" id="'.$nombre.'" placeholder="'.$placeHolder.'"';
        if($this->modo=="editar"){
            echo "value='".$valor."'";
        }
        echo '>';
        echo "</td></tr>";        
        return($text);   
    }
      
    public function addTextarea($nombre,$cols,$rows,$texto,$placeholder,$valor=false){
         $this->arrayValidate[$nombre]["tipo"]="texto";
        $this->arrayCampo[$nombre]=$texto;        
        echo "<div>".$nombre."</div>";
            echo "<div>";
            echo '<textarea  placeholder="'.ucfirst(utf8_encode($texto)).'"  class="form-control input-sm" style="margin-top:2px; margin-bottom:2px; width:550px;" name="'.$nombre.'" cols="'.$cols.'" rows="'.$rows.'" id="'.$nombre.'">';
            if($this->modo==0){
                echo $valor;
            }
            echo '</textarea>';
            echo "</div>";
        return(true);    
    }
    public function addEmail($nombre,$tam,$placeHolder,$texto,$valor=false){
          $this->arrayValidate[$nombre]["tipo"]="email";
        $this->arrayCampo[$nombre]=$texto;        
        if($this->numCol==1){echo "<tr><td align='left' width='20%'>".$texto." :</td></tr>";}else{echo "<tr><td align='right' width='20%'>".$texto."</td>";}  
        echo "<td style='padding:2px; margin:8px;'>";
        echo '<input type="text" size="'.$tam.'" name="'.$nombre.'" id="'.$nombre.'" placeholder="'.$placeHolder.'"';
        if($this->modo==0){
            echo "value='".$valor."'";
        }
        echo '>';
        echo "</td></tr>";        
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
    public function addAlert($titulo,$mensaje,$tipo){        
        echo '<div class="alert alert-';
         if($tipo==1){
             echo "primary";
         }else if($tipo==2){
             echo "secondary";
         }else if($tipo==3){
             echo "success ";
         }else if($tipo==4){
             echo "danger";
         }else if($tipo==5){
             echo " warning";
         }else if($tipo==6){
             echo "info";
         }else if($tipo==7){
             echo "light ";
         }else if($tipo==8){
             echo "dark";
         }
        echo ' alert-dismissible fade show"  data-dismiss="alert" aria-label="Close" role="alert">
        <strong>'.$titulo.'!</strong> '.$mensaje.'
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
            </div>';
        echo "<script>            
                 $(document).ready(function(){                  
                     $('.alert').alert();                             
                 });
                window.setTimeout(function(){                                      
                     $('.alert').fadeTo(1500,0).slideDown(1000,function(){
                         $(this).remove();
                     });
                  }, 1000);
             </script>";
     }
    public function toast($titulo,$mensaje,$pos=false,$delay){
        echo '<div aria-live="assertive" aria-atomic="true" lass="d-flex justify-content-center align-items-center" style="height: 200px;" >
        <div class="toast" id="miToast" name="miToast" style="position: absolute; top: '.$pos.'; right: 0;" data-delay="'.$delay.'">
          <div class="toast-header">
          <svg class="bd-placeholder-img rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img"><rect width="100%" height="100%" fill="#007aff"></rect></svg>
            <strong class="mr-auto">';
            echo $titulo;
            echo '</strong>
            <small>Justo ahora</small>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="toast-body">';
         echo $mensaje;
          echo '</div>
        </div>
      </div>';
      echo '<script> 
      $(document).ready(function(){
          $(".toast").toast("show");
          return(false);
      });
     </script>';
    }
   public function addSelect($nombre,$arr,$mensaje,$func,$texto,$seleccionado=false){
          $this->arrayValidate[$nombre]["tipo"]="select";
        $this->arrayCampo[$nombre]=$texto;    
          
        echo "<div>".ucfirst($nombre)."</div>";
        echo "<div>";
        echo '<select  class="form-control input-sm" style="margin-top:2px; margin-bottom:2px; width:250px;" name="'.$nombre.'" id="'.$nombre.'"';
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
            echo "Imagenes :";
            echo "</div>";
            echo "<div>";
 
            echo "<table width='1%' border=0>";
            echo "<tr>";
            $numCol=9;
            $k++;
            foreach($archivos as $clave=>$valor){
                echo "<td style='padding:2px; margin:8px;'>";
                echo "<img src='".$valor."' width='50' height='50'/>";
                echo "</td>";
                $k++;
                if($k%$numCol==0){
                    echo "</tr>";
                }
            }
            
            echo "</table>";
            echo "</div>";
        }
        echo "<div>".$texto."</div>";
        echo "<div>";
        
        
        if($numero==0){
            echo '<div><input type="file" size="32" name="my_field" value="" />
            <input type="hidden" name="action" value="image" /></div>';    
        }else{
            for($i=0; $i<$numero; $i++){
                echo '<div><input type="file" size="32" name="my_field[]" value="" /></div>';
            }
        }
       
        echo "</div>";
        
        return(true);
    }
    
    public function addHidden($nombre,$valor){
        $texto=false;
          if($this->numCol==1){echo "<tr><td align='left' width='20%'>".$texto." :</td></tr>";}else{echo "<tr><td align='right' width='20%'>".$texto."</td>";}  
        echo "<td>";
        echo '<input type="hidden" name="'.$nombre.'" id="'.$nombre.'" value="'.$valor.'">';
        echo "</td></tr>";        
         
    }
    public function addButtonReset($nombre,$valor){        
        echo "<tr><td colspan='".$this->numCol."'>";
        echo '<input type="reset" name="'.$nombre.'" id="'.$nombre.'" value="'.$valor.'">';
        echo "</td></tr>";
        return(true);
    }
    public function addButton1($nombre=false,$valor=false,$evento=false,$borrar=false){
        if($this->modo!=1){
            $valor="Guardar Cambios";
            
        } 
       //  $this->arrayValidate[$nombre][$texto]["texto"]=$validar;
    
     
         echo '<input type="button" name="enviar" id="enviar" value="'.$valor.'">';

    
        return(true);
    }
public function addButton($nombre=false,$valor=false,$evento=false,$borrar=false,$tipoBoton,$icon=false,$spin=false,$disable=false,$tam,$block=false){
        //$this->arrayValidate[$nombre][$texto]["texto"]=$validar;        
        echo '<button type="button" ';
        if($evento!=false){ echo "onclick='".$evento."';"; }        
        echo ' id="'.$nombre.'" name="'.$nombre.'" class="btn btn-'.$tipoBoton;
        if($block!=false){echo " btn-block ";}
        echo '"  ';
        
        if($disable!=false){ echo ' disabled ';}
        echo '>';
        if($spin!=false){
        echo "<span id='".$nombre."' name='".$nombre."'>";        
        if($icon!=false){ echo '<i class="far fa-check-circle"></i> ';}
        echo $valor;
        echo "</span>";
        }else{
        if($icon!=false){ echo '<i class="far fa-check-circle"></i> ';}
        echo $valor;
        }
        echo '</button>';
       
        
    }
    public function addSubmit($nombre,$valor){
        echo "<div>";
        echo '<input type="Submit" name="'.$nombre.'" id="'.$nombre.'" value="'.$valor.'">';
       echo "</div>";
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