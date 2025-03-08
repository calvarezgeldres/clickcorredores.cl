var actSoloForm = 0;


$(function () {

    //Envío del formulario
    $("#form-contact-submit").click(function (e) {
        e.preventDefault();
        if ($('#condiciones').is(':checked')) {
            enviarForm();
        } else {
            alert("Debes aceptar las condiciones")
        }      
    });

});


function enviarForm() {

    var nombre = $("#nombre").val();
    var telefono = $("#telefono").val();
    var email = $("#correo").val();
    var region = $("#region").val();
    var comuna = $("#comuna").val();
    var operacion = $("#operacion").val();
    var tipoProp = $("#tipoProp").val();
    var estadoProp = $("#estadoProp").val();
    var divisa = $("#divisa").val();
    var precioDesde = $("#precioDesde").val();
    var precioHasta = $("#precioHasta").val();
    var dormitorios = $("#dormitorios").val();
    var banos = $("#banos").val();
    var comentarios = $("#comentarios").val();

    var mensaje = "";
    if (nombre == "") { mensaje = " - Nombre\n"; }
    if (email == "") { mensaje += " - Email\n"; }
    if (comentarios == "") { mensaje += " - Comentarios\n"; }

    if (mensaje == "") {
        $.get("recursos/publico.ashx", {
            ac: "avanzada",
            rfield: $("#g-recaptcha-response").val(),
            no: nombre,
            telefono: telefono,
            em: email,
            comuna:comuna,
            region: region,
            tipoProp: tipoProp,
            operacion: operacion,
            estadoProp: estadoProp,
            divisa: divisa,
            precioDesde: precioDesde,
            precioHasta: precioHasta,
            dormitorios: dormitorios,
            banos: banos,
            comentarios: comentarios,
            _: Math.random() * 10
        },
        function (data) {
            if (data != "ok") {
                alert("Hubo un problema, el formulario no pudo ser enviado.\nPor favor, inténtelo de nuevo.");
            } else {
                alert("Su solicitud fue enviada con éxito.\nEn breve uno de nuestros asociados se pondrá en contacto con usted.");
                window.location = "confianos.aspx";
            }
        });
    } else {
        alert("Por favor, complete los campos:\n" + mensaje);
    }
}