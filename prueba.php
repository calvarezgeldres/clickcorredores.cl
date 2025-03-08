<?php
// Verifica si la extensión exif está habilitada
if (extension_loaded('exif')) {
    echo 'La extensión exif está habilitada.';
} else {
    echo 'La extensión exif no está habilitada.';
}
?>
