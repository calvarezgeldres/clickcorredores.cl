<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <?php 
                if(isset($_GET["msg"])){
                    echo '<div class="alert alert-primary" role="alert">
                    Formulario de evaluacion comercial de arrendatario se ha enviado con exito
                  </div>';
                }
                ?>
                <div style="margin-bottom:100px;">
                <div style="margin-top:30px;"><h4>Formulario de evaluación comercial</h4></div>
                <div style="margin-bottom:20px;margin-top:20px;"><h5>Formulario Arrendatario</h5></div>
               
                <form id="form1" name="form1" action="proceso.php" method="post" >
                <div class="mb-3">
    <input type="text" class="form-control" id="primer-nombre1" name="primer-nombre1" placeholder="Primer nombre">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="segundo-nombre1" name="segundo-nombre1" placeholder="Segundo nombre">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="apellido-paterno1" name="apellido-paterno1" placeholder="Apellido paterno">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="apellido-materno1" name="apellido-materno1" placeholder="Apellido materno">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="rut1" name="rut1" placeholder="RUT">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="nacionalidad1" name="nacionalidad1" placeholder="Nacionalidad">
</div>
<div class="mb-3">
    <select class="form-select" id="estado-civil1" name="estado-civil1">
        <option value="">Estado civil</option>
        <option value="Soltero">Soltero</option>
        <option value="Casado">Casado</option>
        <option value="Divorciado">Divorciado</option>
        <option value="Separado">Separado</option>
        <option value="Viudo">Viudo</option>
    </select>
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="profesion-oficio1" name="profesion-oficio1" placeholder="Profesión / Oficio">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="empresa1" name="empresa1" placeholder="Empresa">
</div>
<div class="mb-3">
    <select class="form-select" id="antiguedad-laboral1" name="antiguedad-laboral1">
        <option value="">Antigüedad laboral</option>
        <option value="1 año">1 año</option>
        <option value="2 años">2 años</option>
        <option value="3 o más años">3 o más años</option>
    </select>
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="banco1" name="banco1" placeholder="Banco">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="cuenta-corriente1" name="cuenta-corriente1" placeholder="Cuenta corriente">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="renta-liquida1" name="renta-liquida1" placeholder="Renta líquida">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="direccion-particular1" name="direccion-particular1" placeholder="Dirección particular">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="direccion-laboral1" name="direccion-laboral1" placeholder="Dirección laboral">
</div>
<div class="mb-3">
    <input type="tel" class="form-control" id="fono-particular1" name="fono-particular1" placeholder="Fono particular">
</div>
<div class="mb-3">
    <input type="tel" class="form-control" id="fono-celular1" name="fono-celular1" placeholder="Fono celular">
</div>
<div class="mb-3">
    <input type="email" class="form-control" id="email1" name="email1" placeholder="Email">
</div>
<div class="mb-3">
    <input type="text" class="form-control" id="propiedad-postulacion1" name="propiedad-postulacion1" placeholder="Propiedad a la que postula">
</div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="acceptance-7211" >
                        <label class="form-check-label" for="acceptance-7211">Acepto enviar esta información para evaluación comercial.</label>
                    </div>
                    <button type="button" role="button" name="enviar-btn" class="btn btn-primary" id="enviar-btn" disabled>Enviar</button>
                </form>
            </div>
        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const checkbox = $('#acceptance-7211');
    const enviarBtn = $('#enviar-btn');

    checkbox.on('change', function () {
        if (this.checked) {
            enviarBtn.prop('disabled', false);
        } else {
            enviarBtn.prop('disabled', true);
        }
    });
    $("#enviar-btn").click(function(){
        const primerNombre = $('#primer-nombre1').val().trim();
        const segundoNombre=$("#segundo-nombre1").val().trim();
        const apellidoPaterno = $('#apellido-paterno1').val().trim();
        const apellidoMaterno = $('#apellido-materno1').val().trim();
        const rut = $('#rut1').val().trim();
        const nacionalidad = $('#nacionalidad1').val().trim();
        const estadoCivil = $('#estado-civil1').val();
        const profesionOficio = $('#profesion-oficio1').val().trim();
        const empresa = $('#empresa1').val().trim();
        const antiguedadLaboral = $('#antiguedad-laboral1').val();
        const banco = $('#banco1').val().trim();
        const rentaLiquida = $('#renta-liquida1').val().trim();
        const direccionParticular = $('#direccion-particular1').val().trim();
        const direccionLaboral = $('#direccion-laboral1').val().trim();
        const fonoCelular = $('#fono-celular1').val().trim();
        const email = $('#email1').val().trim();
        const propiedadPostulacion = $('#propiedad-postulacion1').val().trim();
        const acceptance = checkbox.prop('checked');


       // Validación de campos obligatorios
if (primerNombre === '') {
    alert('Por favor, ingrese el primer nombre.');
    $('#primer-nombre1').focus();
    return;
}
if (segundoNombre === '') {
    alert('Por favor, ingrese el segundo nombre.');
    $('#segundo-nombre1').focus();
    return;
}
if (apellidoPaterno === '') {
    alert('Por favor, ingrese el apellido paterno.');
    $('#apellido-paterno1').focus();
    return;
}

if (apellidoMaterno === '') {
    alert('Por favor, ingrese el apellido materno.');
    $('#apellido-materno1').focus();
    return;
}

if (rut === '') {
    alert('Por favor, ingrese el RUT.');
    $('#rut1').focus();
    return;
}

if (nacionalidad === '') {
    alert('Por favor, ingrese la nacionalidad.');
    $('#nacionalidad1').focus();
    return;
}

if (estadoCivil === '') {
    alert('Por favor, seleccione el estado civil.');
    $('#estado-civil1').focus();
    return;
}

if (profesionOficio === '') {
    alert('Por favor, ingrese la profesión / oficio.');
    $('#profesion-oficio1').focus();
    return;
}

if (empresa === '') {
    alert('Por favor, ingrese el nombre de la empresa.');
    $('#empresa1').focus();
    return;
}

if (antiguedadLaboral === '') {
    alert('Por favor, seleccione la antigüedad laboral.');
    $('#antiguedad-laboral1').focus();
    return;
}

if (banco === '') {
    alert('Por favor, ingrese el banco.');
    $('#banco1').focus();
    return;
}

if (rentaLiquida === '') {
    alert('Por favor, ingrese la renta líquida.');
    $('#renta-liquida1').focus();
    return;
}

if (direccionParticular === '') {
    alert('Por favor, ingrese la dirección particular.');
    $('#direccion-particular1').focus();
    return;
}

if (direccionLaboral === '') {
    alert('Por favor, ingrese la dirección laboral.');
    $('#direccion-laboral1').focus();
    return;
}

if (fonoCelular === '') {
    alert('Por favor, ingrese el teléfono celular.');
    $('#fono-celular1').focus();
    return;
}

if (email === '') {
    alert('Por favor, ingrese el correo electrónico.');
    $('#email1').focus();
    return;
} else if (!validateEmail(email)) {
    alert('Por favor, ingrese un correo electrónico válido.');
    $('#email1').focus();
    return;
}

if (propiedadPostulacion === '') {
    alert('Por favor, ingrese la propiedad de postulación.');
    $('#propiedad-postulacion1').focus();
    return;
}

$("#form1").submit();

// Función para validar el formato de correo electrónico
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}


       
            
    

        // Validación adicional si es necesario

        // Enviar formulario
        

return(false);

    });
    return(false);
    
});
</script>
 







</body>
</html>


