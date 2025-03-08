<?php
require_once("./clases/class.coneccion.php");
$miCon=new coneccion();
$miCon->prueba();
// Array para almacenar las claves primarias insertadas por cada tabla
$relations = [
    'mm_propietarios' => [],
    'mm_propiedad1' => [],
    'mm_arrendatario' => [],
    'mm_arriendos' => [],
];

// Si se ha enviado el formulario, procesar los archivos CSV
if (isset($_POST['submit'])) {
    // Recorre las tablas y procesa sus archivos CSV en orden
    $tables = ['mm_propietarios', 'mm_propiedad1', 'mm_arrendatario', 'mm_arriendos'];

    foreach ($tables as $table) {
        if (isset($_FILES["csv_file_$table"]) && $_FILES["csv_file_$table"]['error'] == 0) {
            $file = $_FILES["csv_file_$table"]['tmp_name'];

            // Verificar la extensión del archivo CSV
            $fileInfo = pathinfo($_FILES["csv_file_$table"]['name']);
            $fileExtension = strtolower($fileInfo['extension']);

            if ($fileExtension != 'csv') {
                echo "El archivo debe ser de tipo CSV. Por favor, cargue un archivo con extensión .csv.<br>";
                continue;
            }

            // Abre el archivo CSV
            if (($handle = fopen($file, "r")) !== FALSE) {
                // Lee la primera línea del archivo CSV (cabecera)
                $columns = fgetcsv($handle);

                // Prepara la consulta INSERT INTO
                $insertQuery = "INSERT INTO `$table` (" . implode(", ", array_map(function($col) { return "`$col`"; }, $columns)) . ") VALUES\n";

                // Prepara las filas de datos
                $values = [];
                while (($dataRow = fgetcsv($handle)) !== FALSE) {
                    // Asegura que no haya valores vacíos en la fila
                    if (empty(array_filter($dataRow, 'strlen'))) {
                        continue; // Si la fila está vacía, la salta
                    }

                    // Escapa los datos y prepara las filas
                    $escapedData = array_map(function($value) {
                        return "'" . addslashes($value) . "'"; // Escapa las comillas simples
                    }, $dataRow);

                    // Genera las filas de datos para el INSERT
                    $values[] = "(" . implode(", ", $escapedData) . ")";
                }

                // Si hay datos para insertar, agrega las filas al INSERT
                if (!empty($values)) {
                    $insertQuery .= implode(",\n", $values) . ";";

                    // Muestra el código MySQL para insertar los datos
                    echo "<pre>" . htmlspecialchars($insertQuery) . "</pre>";
                } else {
                    echo "No hay datos válidos en el archivo CSV para la tabla '$table'.<br>";
                }

                fclose($handle); // Cierra el archivo CSV
            } else {
                echo "Error al abrir el archivo CSV de la tabla '$table'.<br>";
            }
        } else {
            echo "No se ha cargado el archivo CSV para la tabla '$table'.<br>";
        }
    }

} else {
    // Si no se ha enviado un archivo, muestra el formulario para cada tabla
    echo '<h5>Generar Código MySQL</h5>';
    echo '<form method="POST" enctype="multipart/form-data">';

    // Formulario para cargar los archivos CSV
    echo "<label for='csv_file_mm_propietarios'>Selecciona un archivo CSV para la tabla 'mm_propietarios':</label>";
    echo "<input type='file' name='csv_file_mm_propietarios' id='csv_file_mm_propietarios' required><br><br>";

    echo "<label for='csv_file_mm_propiedad1'>Selecciona un archivo CSV para la tabla 'mm_propiedad1':</label>";
    echo "<input type='file' name='csv_file_mm_propiedad1' id='csv_file_mm_propiedad1' required><br><br>";

    echo "<label for='csv_file_mm_arrendatarios'>Selecciona un archivo CSV para la tabla 'mm_arrendatarios':</label>";
    echo "<input type='file' name='csv_file_mm_arrendatario' id='csv_file_mm_arrendatario' required><br><br>";

    echo "<label for='csv_file_mm_arriendos'>Selecciona un archivo CSV para la tabla 'mm_arriendos':</label>";
    echo "<input type='file' name='csv_file_mm_arriendos' id='csv_file_mm_arriendos' required><br><br>";

    echo '<input type="submit" name="submit" value="Generar Código MySQL">';
    echo '</form>';
}
?>
