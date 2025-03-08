var re = ""; var op = ""; var tp = ""; var co = "";
var pd = ""; var ph = ""; var sd = ""; var sh = "";
var dd = ""; var dh = ""; var bd = ""; var bh = "";
var div = ""; var bn = ""; var dor = ""; var inv = "1";
//Variales globales de todos los listados//
var orden = ""; var pagina = ""; var tipoListado = "1";
//Constantes en caso de que las variables lleguen vacías en una primera instancia
var ordenInicial = "1"; var paginaInicial = "1";
var $priceSlider = $("#price-input");

$(document).ready(function () {
    botonesBuscador();
});

$(function () {

    //Cambio de región
    $('#ddlRegion').change(function (e) {
        e.preventDefault();
        region = $(this).val();
        completarComunasDeLaRegion("ddlComunaCont", region);
    });

    //Click actualizar listado
    $("#botBuscar").click(function (e) {
        e.preventDefault();
        asignacionDeVariablesDelListado();
        pagina = "1";
        generarListado();
    });

    //Ordenar listado
    $("#ddlOrden").change(function (e) {
        e.preventDefault();
        orden = $("#ddlOrden").val();
        generarListado();
    });

    $("#lnkTL1 a, #lnkTL2 a").click(function (e) {
        e.preventDefault();
        tipoListado = $(this).attr("data-list");
        generarListado();
    });


    recepcionInicialDeDatos(); //Recepción de los datos por querystring
    condicionesPrimeraCarga(); //Condiciones de formateo de los datos si es la primera carga de la página
    generarListado(); // Listado
});

function asignacionDeVariablesDelListado() {

    op = "1";

    op = $("#ddlOp").val();
    tp = $("#ddlTP").val();
    co = $("#ddlComuna").val();
    re = $("#ddlRegion").val();

    div = $("#ddlDiv").val();
    pd = $("#tbpd").val();
    ph = $("#tbph").val();
    dor = $("#ddlDorm").val();
    bn = $("#ddlBano").val();
}

function recepcionInicialDeDatos() {

    re = getQuerystring("re");
    co = getQuerystring("co");
    tp = getQuerystring("tp");
    op = getQuerystring("op");
    pd = getQuerystring("pd");
    ph = getQuerystring("ph");
    dor = getQuerystring("do");
    bn = getQuerystring("bn");
    div = getQuerystring("div");
    
    pagina = getQuerystring("pa");
    orden = getQuerystring("or");
    tipoListado = getQuerystring("tl");
    //inv = getQuerystring("inv");

}

function condicionesPrimeraCarga() {

    if (op != "2" && op != "1") { op = "1"; }
    if (tipoListado != "2") { tipoListado = "1"; }
    if (pagina == "" || pagina == "0") { pagina = paginaInicial; }
    if (orden == "" || (orden != "1" && orden != "2" && orden != "3" && orden != "4")) { orden = ordenInicial; }
    if (bn == "") { bn = "0"; }
    if (dor == "") { dor = "0"; }
    if (pd != "") { $("#tbpd").val(pd); }
    if (ph != "") { $("#tbph").val(ph); }

    $('#ddlOp').val(op);
    $("#ddlDorm").val(dor);
    $("#ddlBano").val(bn);
    $("#ddlDiv").val(div);
    $("#ddlOrden").val(orden);

}

function cadenaQueryStringDeFiltros() {

    var strCadena = "&pa=" + pagina + "&or=" + orden;
    strCadena = strCadena + "&op=" + op + "&re=" + re + "&co=" + co + "&tp=" + tp;
    strCadena = strCadena + "&pd=" + pd + "&ph=" + ph + "&bn=" + bn + "&do=" + dor + "&div=" + div + "&tl=" + tipoListado + "&inv=" + inv;

    return strCadena;

}

function paginar(pag) {
    pagina = pag;
    inicio = 1;
    generarListado();
    $('html, body').animate({ scrollTop: 100 }, 'slow');
    return false;
}

function generarListado() {

    $(".loader").show();
   
    $.getJSON("recursos/publico.ashx", {
        ac: "listadoPropiedades",
        op: op,
        tp: "2",
        re: re,
        co: co,
        or: orden,
        pa: pagina,
        tl: tipoListado,
        pd: pd,
        ph: ph,
        bn: bn,
        do: dor,
        div: div,
        inv: inv,
        cache: Math.random() * 10
    },
    function (json) {

        if (json[0].error != "si") {

            $("#numRegistros").html(json[0].numRegistros);
            $(".pagination").html(json[0].paginador);
            $("#propListado").html(json[0].listing);
            document.title = json[0].title;

            //Paginador
            $('.pagination li').click(function (e) {
                e.preventDefault();
                if ($(this).attr("rel")) {
                    pagina = $(this).attr("rel");
                    generarListado();
                    $("html, body").animate({ scrollTop: 100 }, "slow");
                }
            });

            //Click a ficha de propiedad
            $('.default-property-box').click(function (e) {
                e.preventDefault();
                window.location = "fichaPropiedad.aspx?i=" + $(this).attr("data-id") + cadenaQueryStringDeFiltros();
            });

            $(".loader").hide();

        } else {
            window.location = "index.aspx"
        }
    });
}

function completarComunasDeLaRegion(campoDestino, regionSeleccionada) {

    $("#" + campoDestino).html("");

    $.get("../recursos/publico.ashx", {
        ac: "completarComunasDeLaRegion",
        idr: regionSeleccionada,
        _: Math.random() * 10
    },
    function (data) {

        $("#" + campoDestino).html("<select id='ddlComuna' data-placeholder='Comuna'>" + data + "</select>");

        var select = $('#ddlComuna');
        $(select).trigger('chosen:updated');

        
    });

}
function botonesBuscador() {
    $("#open-opnes-advans-list").on('click', function () {

        $(this).toggleClass("js-hide");

        if ($(this).hasClass("js-hide")) {
            $(this).html("<i class='fa fa-cogs' aria-hidden='true'></i> Cerrar B\u00fasqueda");
        } else {
            $(this).html("<i class='fa fa-cogs' aria-hidden='true'></i> B\u00fasqueda Avanzada");
        }

        $("#row-opnes-advans-list").slideToggle();

        return false;
    });
}