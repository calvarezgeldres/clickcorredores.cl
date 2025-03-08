var idProp; var cod;

$(document).ready(function () {

    contactoPropiedad();
    $('#btnCompartiWhatsapp').click(function (e) {
        insertarClickCompartir($(this).attr("data-id"));
    });

    $('#btnContactoWhatsApp').click(function (e) {
        insertarClickContacto($(this).attr("data-id"));
    });
});

$(function () {

    recepcionInicialDeDatos(); //Recepción de los datos por querystring

});

function envioContacto(idPropiedad) {

    var fcnombre = $("#nombre").val();
    var fcemail = $("#correo").val();
    var fccomentarios = $("#comentarios").val();
    var fcmensaje = "";

    if (fcnombre == "") { fcmensaje = " - Nombre y Apellidos\n"; }
    if (fcemail == "") { fcmensaje += " - E-mail\n"; }
    if (fccomentarios == "") { fcmensaje += " - Comentarios\n"; }

    var validacionOK = validaciones($("#nombre").attr("id"), $("#nombre").attr("data-val"));
        validacionOK += validaciones($("#correo").attr("id"), $("#correo").attr("data-val"));

    if (validacionOK == "" && fcmensaje == "") {

        $.get("recursos/publico.ashx", {
            ac: "contactoPropiedad",
            rfield: $("#g-recaptcha-response").val(),
            no: fcnombre,
            em: fcemail,
            co: fccomentarios,
            id: idPropiedad,
            _: Math.random() * 10
        },
        function (data) {
            if (data == "false") {
                alert("Hubo un problema, el Contacto no pudo ser enviado.\nPor favor, inténtelo de nuevo.");
            } else {
                alert("Su solicitud fue enviada con exito.\nUno de nuestros ejecutivos se pondra en contacto con usted.");
                location.reload();
            }
        });
    } else {
        alert("Hubo un problema, por favor revise los campos del formulario:\n" + fcmensaje);
    }
}

function contactoPropiedad() {

    $('#envioContactoPropiedad').click(function (e) {
        e.preventDefault();
        envioContacto($(this).attr("data-id"));
    });

    $("#contact-form input").blur(function (e) {
        validaciones($(this).attr("id"), $(this).attr("data-val"));
    });

}

function recepcionInicialDeDatos() {

    idProp = getQuerystring("i");
    cod = getQuerystring("cod");
    
}
function verMapaPropiedad(n, t) {
    $("#img__map").hide();
    $("#mapContainer").html("<iframe width='100%' height='400' frameborder='0' style='border:0;' src='" + t + "' allowfullscreen><\/iframe>")

}
function insertarClickCompartir(idPropiedad) {
    $.post("/recursos/publico.ashx", {
        ac: "insertarClickCompartir",
        id: idProp,
        _: Math.random() * 10
    },
        function (data) {
            if (data != "ok") {


            }
        });

}
function insertarClickContacto(idPropiedad) {
    $.post("/recursos/publico.ashx", {
        ac: "insertarClickContacto",
        id: idProp,
        _: Math.random() * 10
    },
        function (data) {
            if (data != "ok") {


            }
        });

} 