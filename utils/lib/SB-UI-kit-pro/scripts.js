
/**
 * Start Bootstrap - SB UI Kit Pro v1.0.2 (https://shop.startbootstrap.com/product/sb-ui-kit-pro)
 * Copyright 2013-2021 Start Bootstrap
 * Licensed under SEE_LICENSE (https://github.com/BlackrockDigital/sb-ui-kit-pro/blob/master/LICENSE)
 * Altamente modificato da sean
 */
$(function() {
    // Enable Bootstrap tooltips via data-attributes globally
    $('[data-toggle="tooltip"]').tooltip();

    // Enable Bootstrap popovers via data-attributes globally
    $('[data-toggle="popover"]').popover();

    $(".popover-dismiss").popover({
        trigger: "focus"
    });

    // Activate Feather icons
    if(typeof feather != "undefined") feather.replace();
    
    // Scrolls to an offset anchor when a sticky nav link is clicked
    $('.nav-sticky a.nav-link[href*="#"]:not([href="#"])').click(function() {
        if (
            location.pathname.replace(/^\//, "") ==
            this.pathname.replace(/^\//, "") &&
            location.hostname == this.hostname
        ) {
            var target = $(this.hash);
            target = target.length ? target : $("[name=" + this.hash.slice(1) + "]");
            if (target.length) {
                $("html, body").animate(
                    {
                        scrollTop: target.offset().top - 81
                    }, 
                    200
                );
                return false;
            }
        }
    });

    // Collapse Navbar
    // Add styling fallback for when a transparent background .navbar-marketing is scrolled
    $(window).scroll((function() {
        const nav = $(".navbar-marketing.bg-transparent.fixed-top");
        if(!$("header").length)
            nav.addClass("navbar-scrolled");
        else if(nav.length)
            if (nav.offset().top > 0) nav.addClass("navbar-scrolled");
            else nav.removeClass("navbar-scrolled");
        return arguments.callee; // Restituisce se stessa dopo essere eseguita per non dover essere immagazzinata
    })());
});
