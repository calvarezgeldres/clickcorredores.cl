jQuery(document).ready(function () {

    adaptacionImagenVH();
    $(window).resize(function () {
        adaptacionImagenVH();
    });

})

function adaptacionImagenVH() {
    $(window).load(function () {
        var heightImg = 0;
        $(".img-ficha-horizontal").filter(function () {
            var $this = $(this);
            if ($this.width() > $this.height()) {
                heightImg = $(".owl-stage-outer").height();
                $this.css("height", heightImg + "px").css("width", "auto");
            } else if ($this.width() <= $this.height()) {
                heightImg = $(".owl-stage-outer").height();
                $this.removeClass("img-ficha-horizontal").addClass("img-ficha-vertical");
                $(".img-ficha-vertical").css("height", heightImg + "px");
            }
        });
    });
}