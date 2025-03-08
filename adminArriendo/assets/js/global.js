$(function () {
    //LOADER
    $(window).on("load", function () {
        "use strict";
        $(".loader").fadeOut(800);
    });
});

function getQuerystring(key, default_) {
    if (default_ == null) default_ = "";
    key = key.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + key + "=([^&#]*)");
    var qs = regex.exec(window.location.href);
    if (qs == null)
        return default_;
    else
        return qs[1];
}

/*****Cookies*****/
function readCookie(name, nameCookie) {
    var valor = "";
    var cok = $.cookie(nameCookie);
    if (cok != null) {
        var nameEQ = name + "=";
        var ca = cok.split('@@');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) valor = c.substring(nameEQ.length, c.length);
        }
    }
    return valor;
}
/*----End cookies----*/

/****Validations****/
function validaciones(campo, tipo) {

    var resultadoValidacion = "";

    $("#" + campo).closest('.form-group').removeClass('has-error');
    $("#" + campo).next('span').remove();

    switch (tipo) {
        case "obligatorio":
            resultadoValidacion += validarCampoObligatorio(campo);
            break;

        case "mailObligatorio":
            resultadoValidacion += validarCampoObligatorio(campo);
            if (resultadoValidacion == "") { resultadoValidacion = validarCampoCorreo(campo); }
            break;

        case "fechaObligatoria":
            resultadoValidacion += validarCampoObligatorio(campo);
            if (resultadoValidacion == "") { resultadoValidacion = validarCampoFecha(campo); }
            break;

        case "fechaNoObligatoria":
            if ($("#" + campo).val() != "") { resultadoValidacion += validarCampoFecha(campo); }
            break;

        case "numeroObligatorio":
            resultadoValidacion += validarCampoObligatorio(campo);
            if (resultadoValidacion == "") { resultadoValidacion = validarCampoNumero(campo); }
            break;

        case "numeroNoObligatorio":
            if ($("#" + campo).val() != "") { resultadoValidacion += validarCampoNumero(campo); }
            break;

        case "numeroDecimalObligatorio":
            resultadoValidacion += validarCampoObligatorio(campo);
            if (resultadoValidacion == "") { resultadoValidacion = validarCampoNumeroDecimal(campo); }
            break;

        case "numeroDecimalNoObligatorio":
            if ($("#" + campo).val() != "") { resultadoValidacion += validarCampoNumeroDecimal(campo); }
            break;

        case "selectObligatorio":
            resultadoValidacion += validarSelectObligatorio(campo);
            break;

        case "rut":
            resultadoValidacion += validarCampoObligatorio(campo);
            if ($("#" + $("#" + campo).attr("relValDoc")).attr("checked") == "checked") {
                if (resultadoValidacion == "") { resultadoValidacion = validarCampoRUT(campo); }
            }
            break;
    }

    return resultadoValidacion;

}

function validarCampoObligatorio(campo) {

    var mensaje = "";

    if ($("#" + campo).val() == "") {
        mensaje = "Campo obligatorio";
        $("#" + campo).closest('.form-group').addClass('has-error');
        $("<span class='help-block'>" + mensaje + "</span>").insertAfter("#" + campo);
    }
    return mensaje;

}

function validarSelectObligatorio(campo) {

    var mensaje = "";

    if ($("#" + campo).val() == "" || $("#" + campo).val() == "0") {
        mensaje = "Campo obligatorio";
        $("#" + campo).closest('.form-group').addClass('has-error');
        $("<span class='help-block'>" + mensaje + "</span>").insertAfter("#" + campo);
    }
    return mensaje;

}

function validarCampoCorreo(campo) {

    var mensaje = "";
    if (!validarCorreo($("#" + campo).val())) {
        mensaje = "El correo ingresado no es correcto";
        $("#" + campo).closest('.form-group').addClass('has-error');
        $("<span class='help-block'>" + mensaje + "</span>").insertAfter("#" + campo);
    }
    return mensaje;

}

function validarCampoFecha(campo) {

    var mensaje = "";
    if (!validarFecha($("#" + campo).val())) {
        mensaje = "La fecha no es válida";
        $("#" + campo).closest('.form-group').addClass('has-error');
        $("<span class='help-block'>" + mensaje + "</span>").insertAfter("#" + campo);
    }
    return mensaje;

}

function validarCampoNumero(campo) {

    var mensaje = "";
    if (!validarNumero($("#" + campo).val())) {
        mensaje = "El valor no es válido";
        $("#" + campo).closest('.form-group').addClass('has-error');
        $("<span class='help-block'>" + mensaje + "</span>").insertAfter("#" + campo);
    }
    return mensaje;

}

function validarCampoNumeroDecimal(campo) {

    var mensaje = "";
    if (!validarNumeroDecimal($("#" + campo).val())) {
        mensaje = "El valor no es válido";
        $("#" + campo).closest('.form-group').addClass('has-error');
        $("<span class='help-block'>" + mensaje + "</span>").insertAfter("#" + campo);
    }
    return mensaje;

}

function validarCampoRUT(campoInput) {

    var mensaje = "";
    var campo = $("#" + campoInput).val();

    if (campo.length == 0) { mensaje = "El RUT no es válido"; }
    if (campo.length < 6) { mensaje = "El RUT no es válido"; }

    if (mensaje == "") {

        campo = campo.replace('-', '')
        campo = campo.replace(/\./g, '')

        var suma = 0;
        var caracteres = "1234567890kK";
        var contador = 0;
        for (var i = 0; i < campo.length; i++) {
            u = campo.substring(i, i + 1);
            if (caracteres.indexOf(u) != -1)
                contador++;
        }
        if (contador == 0) { mensaje = "El RUT no es válido"; }

        if (mensaje == "") {
            var rut = campo.substring(0, campo.length - 1)
            var drut = campo.substring(campo.length - 1)
            var dvr = '0';
            var mul = 2;

            for (i = rut.length - 1 ; i >= 0; i--) {
                suma = suma + rut.charAt(i) * mul
                if (mul == 7) mul = 2
                else mul++
            }
            res = suma % 11
            if (res == 1) dvr = 'k'
            else if (res == 0) dvr = '0'
            else {
                dvi = 11 - res
                dvr = dvi + ""
            }
            if (dvr != drut.toLowerCase()) {
                mensaje = "El RUT no es válido";
                $("#" + campoInput).closest('.form-group').addClass('has-error');
                $("<span class='help-block'>" + mensaje + "</span>").insertAfter("#" + campoInput);
            }
        } else {
            $("#" + campoInput).closest('.form-group').addClass('has-error');
            $("<span class='help-block'>" + mensaje + "</span>").insertAfter("#" + campoInput);
        }
    } else {
        $("#" + campoInput).closest('.form-group').addClass('has-error');
        $("<span class='help-block'>" + mensaje + "</span>").insertAfter("#" + campoInput);
    }

    return mensaje;

}

function validarCorreo(correo) { if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(correo)) { return true; } else { return false; } }
function validarFecha(fecha) { if (/^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{2}|\d{4})$/.test(fecha)) { return true; } else { return false; } }
function validarNumero(numero) { if (/^\d+(\.\d+)?(\.\d+)?(\.\d+)?(\.\d+)?(\.\d+)?$/.test(numero)) { return true; } else { return false; } }
function validarNumeroDecimal(numero) { if (/^\d+(\.\d+)?(\.\d+)?(\.\d+)?(\.\d+)?(\.\d+)?(\,\d+)?$/.test(numero)) { return true; } else { return false; } }
function validarTexto(texto) { if (/^[a-zA-Z\ ]+$/.test(texto)) { return true; } else { return false; } }
/*----End Validations----*/