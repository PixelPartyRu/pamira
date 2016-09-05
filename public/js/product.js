function ProductPage() {

    this.slider = function() {

        $(".image_slider .img").click(function() {

            $(".image_slider .img").removeClass("active");
            $(this).addClass("active");
            var src = $(this).find("img").attr("src");
            $(".base_image img").attr("src",src);
            $(".base_image a").attr("href",src);

        });
    }
    this.init = function() {
        this.slider();

    }

}
$(document).ready(function() {
    var productPage = new ProductPage();
    productPage.init();
    var lb = $(".lightbox").simpleLightbox();
    $(".image_slider .lightbox").unbind();
    $(".image_slider .lightbox").click(function (event) {
        event.preventDefault();
    });
    $('.base_image a').on('click', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        lb.each(function(i, el){
            if($(el).attr('href') === link)
                lb.open($(el));
        });
    });



});

