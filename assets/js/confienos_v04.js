﻿var actSoloForm = 0;
 

$(function () {

    //Envío del formulario
    $("#form-contact-submit").click(function (e) {
        e.preventDefault();
        enviarForm();
    });

    $("#contact-form input").blur(function (e) {
        validaciones($(this).attr("id"), $(this).attr("data-val"));
    });

});
function enviarForm() {
   
    var nombre = $("#nombre").val();
    var email = $("#correo").val();
    var comentarios = $("#comentarios").val();
    var ciudad = $("#ciudad").val();

    var mensaje = "";

    if (nombre == "") { mensaje = " - Nombre\n"; }
    if (email == "") { mensaje += " - Correo electrónico\n"; }
    if (comentarios == "") { mensaje += " - Comentarios\n"; }

    var validacionOK = validaciones($("#nombre").attr("id"), $("#nombre").attr("data-val"));
    validacionOK += validaciones($("#telefono").attr("id"), $("#telefono").attr("data-val"));
    validacionOK += validaciones($("#correo").attr("id"), $("#correo").attr("data-val"));
 
    if (validacionOK == "" && mensaje == "") {

        $.get("recursos/publico.ashx", {
            ac: "confienos",
            rfield: $("#g-recaptcha-response").val(),
            no: nombre,
            em: email,
            ci: ciudad,
            co: comentarios,
            te: $("#telefono").val(),
            _: Math.random() * 10
        },
        function (data) {
            if (data != "ok") {
                alert("Hubo un problema, el formulario no pudo ser enviado.\nPor favor, inténtelo de nuevo.");
            } else {
                alert("Nos pondremos en contacto con usted")
                window.location = "contacto_gracias.aspx";
            }
        });
    } else {
        alert("Hubo un problema, por favor revise los campos del formulario:\n" + mensaje);
    }
}