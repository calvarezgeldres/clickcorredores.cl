<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendarios Bootstrap</title>
  <!-- Agrega los estilos de Bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Agrega estilos del Datepicker -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
</head>
<body>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-6">
        <label for="fechaInicio">Fecha de Inicio:</label>
        <input type="text" id="fechaInicio" class="form-control">
      </div>
      <div class="col-md-6">
        <label for="fechaCancelacion">Fecha de Término:</label>
        <input type="text" id="fechaCancelacion" class="form-control">
      </div>
    </div>
  </div>

  <!-- Agrega las librerías de jQuery y Bootstrap JavaScript -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- Agrega el script de Bootstrap Datepicker -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <!-- Agrega el script para el idioma español -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.es.min.js"></script>

  <script>
    // Inicializa los datepickers
    $('#fechaInicio, #fechaCancelacion').datepicker({
      format: 'dd/mm/yyyy', // Establece el formato de la fecha
      autoclose: true, // Cierra automáticamente después de seleccionar una fecha
      language: 'es' // Establece el idioma español
    });
  </script>
</body>
</html>
