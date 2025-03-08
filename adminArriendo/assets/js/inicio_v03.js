jQuery(document).ready(function () {

    posicionBuscador();
    botonesBuscador();

    //Cambio de región
    $('#ddlRegion').change(function (e) {
        e.preventDefault();
        region = $(this).val();
        completarComunasDeLaRegion("ddlComunaCont", region);
    });

    $('#botBuscar').click(function (e) {
        e.preventDefault();
        validarBuscador();
    });

    $('#botBuscar2').click(function (e) {
        e.preventDefault();
        validarBuscador();
    });

    $('#btnBuscarCodigo').click(function (e) {
        e.preventDefault();
        validarBuscadorCodigo();
    });
})

$(window).resize(function () {
    posicionBuscador();
});

function completarComunasDeLaRegion(campoDestino, regionSeleccionada) {

    $("#" + campoDestino).html("");

    $.get("../recursos/publico.ashx", {
        ac: "completarComunasDeLaRegion",
        idr: regionSeleccionada,
        _: Math.random() * 10
    },
    function (data) {

        $("#" + campoDestino).html("<select id='ddlComuna'>" + data + "</select>");
    });
}

//Validación del envío del formulario del buscador principal
function validarBuscador() {

    var mensaje = "";

    if ($("#tbpd").val() != "" && !validarNumero($("#tbpd").val())) { mensaje = "El valor del Precio Desde no es válido\n"; }
    if ($("#tbph").val() != "" && !validarNumero($("#tbph").val())) { mensaje += "El valor del Precio Hasta no es válido"; }

    if (mensaje == "") {
        $.get("recursos/publico.ashx", {
            ac: "agregarBusqueda",
            tp: $("#ddlTP").val(),
            op: $("#ddlOp").val(),
            re: $("#ddlRegion").val(),
            co: $("#ddlComuna").val(),
            pd: $("#tbpd").val(),
            ph: $("#tbph").val(),
            div: $("#ddlDiv").val(),
            dor: $("#ddlDorm").val(),
            bn: $("#ddlBano").val(),
            se: ses,
            _: Math.random() * 10
        },
        function (data) {
            window.location = "listado.aspx?op=" + $("#ddlOp").val() + "&tp=" + $("#ddlTP").val() + "&re=" + $("#ddlRegion").val() + "&co=" + $("#ddlComuna").val() + "&pd=" + $("#tbpd").val() + "&ph=" + $("#tbph").val() + "&div=" + $("#ddlDiv").val() + "&do=" + $("#ddlDorm").val() + "&bn=" + $("#ddlBano").val() + "&tl=2&or=2";
        });
    } else {
        alert(mensaje);
    }

}

//Validación del envío del formulario por código
function validarBuscadorCodigo() {

    var codigo = "";
    codigo = $("#tbCodigo").val();

    if (codigo != "") {
        $.get("recursos/publico.ashx", {
            ac: "buscCodigo",
            co: codigo,
            _: Math.random() * 10
        },
        function (data) {
            if (data == "ok") {
                window.location = "fichaPropiedad.aspx?i=" + codigo;
            } else {
                alert("El código ingresado no existe o no está activo.");
            }
        });
    } else {
        alert("Por favor, ingrese un código de propiedad");
        return false;
    }

}

$(window).keypress(function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
        if ($("#tbCodigo").val() != "") {
            validarBuscadorCodigo();
        } else {
        validarBuscador();
        }
    }
});

function posicionBuscador() {

    var posicionFinal; var mitadContenedor; var mitadBuscador;
    var alturaCabecera = $("header").height();
    var alturaBuscador = $("#buscContainer").height();

    mitadContenedor = (600 - alturaCabecera) / 2;
    mitadBuscador = alturaBuscador / 2;

    posicionFinal = mitadContenedor + alturaCabecera - mitadBuscador + 100;

    if ($(window).width() >= 992) {
        $("#bscIncio").css("top", posicionFinal + "px");
    } else {
        $("#bscIncio").css("top", 0 + "px");
    }
}

function botonesBuscador() {

    $("#open-opnes-advans").on('click', function () {

        $(this).toggleClass("js-hide");

        if ($(this).hasClass("js-hide")) {
            $(this).html("<i class='fa fa-cogs' aria-hidden='true'></i> Cerrar B\u00fasqueda");
        } else {
            $(this).html("<i class='fa fa-cogs' aria-hidden='true'></i> B\u00fasqueda Avanzada");
        }

        $("#row-opnes-advans").slideToggle();

        return false;
    });

}