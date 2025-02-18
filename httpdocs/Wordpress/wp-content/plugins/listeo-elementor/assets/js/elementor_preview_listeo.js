(function ($) {
    "use stict";
    $(window).on('elementor/frontend/init', function () {

        // elementorFrontend.hooks.addAction('frontend/element_ready/coco-portfolio.default', function () {
        //     isotopeSetUp();
        // });

        elementorFrontend.hooks.addAction('frontend/element_ready/listeo-taxonomy-carousel.default', function () {
            runSlickSlider();
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/listeo-taxonomy-grid.default', function () {
            runSlickSlider();
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/listeo-imagebox.default', function () {
            runImageBoxes();
        }); 

        elementorFrontend.hooks.addAction('frontend/element_ready/listeo-listings-carousel.default', function () {
            runListingCarousel();
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/listeo-flip-banner.default', function () {
            parallaxBG();
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/listeo-testimonials.default', function () {
            runTestimonials();
        });    

        elementorFrontend.hooks.addAction('frontend/element_ready/listeo-logo-slider.default', function () {
            runLogoSlider();
        }); 

        elementorFrontend.hooks.addAction('frontend/element_ready/listeo-homebanner.default', function () {
            inlineCSS();
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/listeo-homesearchslider.default', function () {
            homecarousel();
        });
    });

    function homecarousel(){

        // New Carousel Nav With Arrows
        $('.home-search-carousel, .simple-slick-carousel').append(""+
        "<div class='slider-controls-container'>"+
          "<div class='slider-controls'>"+
            "<button type='button' class='slide-m-prev'></button>"+
            "<div class='slide-m-dots'></div>"+
            "<button type='button' class='slide-m-next'></button>"+
          "</div>"+
        "</div>");

        // New Homepage Carousel
        $('.home-search-carousel').slick({
          slide: '.home-search-slide',
          centerMode: true,
          centerPadding: '15%',
          slidesToShow: 1,
            dots: true,
            arrows: true,
            appendDots: $(".home-search-carousel .slide-m-dots"),
            prevArrow: $(".home-search-carousel .slide-m-prev"),
            nextArrow: $(".home-search-carousel .slide-m-next"),

          responsive: [
          {
            breakpoint: 1940,
            settings: {
              centerPadding: '13%',
              slidesToShow: 1,
            }
          },
          {
            breakpoint: 1640,
            settings: {
              centerPadding: '8%',
              slidesToShow: 1,
            }
          },
          {
            breakpoint: 1430,
            settings: {
              centerPadding: '50px',
              slidesToShow: 1,
            }
          },
          {
            breakpoint: 1370,
            settings: {
              centerPadding: '20px',
              slidesToShow: 1,
            }
          },
          {
            breakpoint: 767,
            settings: {
              centerPadding: '20px',
              slidesToShow: 1
            }
          }
          ]
        });
        // New Homepage Carousel Positioning
     
          $(".home-search-slider-headlines").each(function() {
            var carouselHeadlineHeight = $(this).height();
            $(this).css('padding-bottom', carouselHeadlineHeight + 30);
          });
          $('.home-search-carousel').removeClass('carousel-not-ready');
      

    }

    
    function runLogoSlider(){
      $('.logo-slick-carousel').slick({
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 4,
        dots: true,
        arrows: true,
        responsive: [
            {
              breakpoint: 992,
              settings: {
                slidesToShow: 3,
                slidesToScroll: 3
              }
            },
            {
              breakpoint: 769,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1
              }
            }
        ]
      });
    }
    // function isotopeSetUp() {
    //     $('.grid').imagesLoaded(function () {
    //         $('.grid').isotope({
    //             itemSelector: '.grid-item',
    //             transitionDuration: 0,
    //             masonry: {
    //                 columnWidth: '.grid-sizer'
    //             }
    //         });
    //         $('.grid').isotope('layout');
    //     });
    // }
    // 
    // 
    function runImageBoxes(){
          /*----------------------------------------------------*/
            /*  Image Box
            /*----------------------------------------------------*/
          $('.category-box').each(function(){

            // add a photo container
            $(this).append('<div class="category-box-background"></div>');

            // set up a background image for each tile based on data-background-image attribute
            $(this).children('.category-box-background').css({'background-image': 'url('+ $(this).attr('data-background-image') +')'});

            
          });


            /*----------------------------------------------------*/
            /*  Image Box
            /*----------------------------------------------------*/
          $('.img-box').each(function(){
            $(this).append('<div class="img-box-background"></div>');
            $(this).children('.img-box-background').css({'background-image': 'url('+ $(this).attr('data-background-image') +')'});
          });


    }

    function runListingCarousel() {
      $('.simple-fw-slick-carousel').slick({
          infinite: true,
          slidesToShow: 5,
          slidesToScroll: 1,
          dots: true,
          arrows: false,

          responsive: [
          {
            breakpoint: 1610,
            settings: {
            slidesToShow: 4,
            }
          },
          {
            breakpoint: 1365,
            settings: {
            slidesToShow: 3,
            }
          },
          {
            breakpoint: 1024,
            settings: {
            slidesToShow: 2,
            }
          },
          {
            breakpoint: 767,
            settings: {
            slidesToShow: 1,
            }
          }
          ]
        }).on("init", function(e, slick) {

          console.log(slick);
                  //slideautplay = $('div[data-slick-index="'+ slick.currentSlide + '"]').data("time");
                  //$s.slick("setOption", "autoplaySpeed", slideTime);
          });


        $('.simple-slick-carousel').slick({
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 3,
            dots: true,
            arrows: true,
            responsive: [
                {
                  breakpoint: 992,
                  settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                  }
                },
                {
                  breakpoint: 769,
                  settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                  }
                }
            ]
          }).on("init", function(e, slick) {
            
            console.log(slick);
                    //slideautplay = $('div[data-slick-index="'+ slick.currentSlide + '"]').data("time");
                    //$s.slick("setOption", "autoplaySpeed", slideTime);
            });
          

    }


    function parallaxBG() {

      $('.parallax,.vc_parallax').prepend('<div class="parallax-overlay"></div>');

      $('.parallax,.vc_parallax').each(function() {
        var attrImage = $(this).attr('data-background');
        var attrColor = $(this).attr('data-color');
        var attrOpacity = $(this).attr('data-color-opacity');

            if(attrImage !== undefined) {
                $(this).css('background-image', 'url('+attrImage+')');
            }

            if(attrColor !== undefined) {
                $(this).find(".parallax-overlay").css('background-color', ''+attrColor+'');
            }

            if(attrOpacity !== undefined) {
                $(this).find(".parallax-overlay").css('opacity', ''+attrOpacity+'');
            }

      });
    }

  

    function runSlickSlider() {
      $('.fullwidth-slick-carousel').slick({
          centerMode: true,
          centerPadding: '20%',
          slidesToShow: 3,
          dots: true,
          arrows: false,
          responsive: [
            {
              breakpoint: 1920,
              settings: {
                centerPadding: '15%',
                slidesToShow: 3
              }
            },
            {
              breakpoint: 1441,
              settings: {
                centerPadding: '10%',
                slidesToShow: 3
              }
            },
            {
              breakpoint: 1025,
              settings: {
                centerPadding: '10px',
                slidesToShow: 2,
              }
            },
            {
              breakpoint: 767,
              settings: {
                centerPadding: '10px',
                slidesToShow: 1
              }
            }
          ]
        });
        // $(".image-slider").each(function () {
        //     var speed_value = $(this).data('speed');
        //     var auto_value = $(this).data('auto');
        //     var hover_pause = $(this).data('hover');
        //     if (auto_value === true) {
        //         $(this).owlCarousel({
        //             loop: true,
        //             autoHeight: true,
        //             smartSpeed: 1000,
        //             autoplay: auto_value,
        //             autoplayHoverPause: hover_pause,
        //             autoplayTimeout: speed_value,
        //             responsiveClass: true,
        //             items: 1
        //         });
        //         $(this).on('mouseleave', function () {
        //             $(this).trigger('stop.owl.autoplay');
        //             $(this).trigger('play.owl.autoplay', [auto_value]);
        //         });
        //     } else {
        //         $(this).owlCarousel({
        //             loop: true,
        //             autoHeight: true,
        //             smartSpeed: 1000,
        //             autoplay: false,
        //             responsiveClass: true,
        //             items: 1
        //         });
        //     }
        // });
    }


    function runTestimonials(){

        $('.testimonial-carousel').slick({
            centerMode: true,
            centerPadding: '34%',
            slidesToShow: 1,
            dots: true,
            arrows: false,
            responsive: [
            {
              breakpoint: 1025,
              settings: {
                centerPadding: '10px',
                slidesToShow: 2,
              }
            },
            {
              breakpoint: 767,
              settings: {
                centerPadding: '10px',
                slidesToShow: 1
              }
            }
            ]
          });

      }


        function inlineCSS() {

          // Common Inline CSS
          $(".main-search-container, section.fullwidth, .listing-slider .item, .listing-slider-small .item, .address-container, .img-box-background, .image-edge, .edge-bg").each(function() {
            var attrImageBG = $(this).attr('data-background-image');
            var attrColorBG = $(this).attr('data-background-color');

                if(attrImageBG !== undefined) {
                    $(this).css('background-image', 'url('+attrImageBG+')');
                }

                if(attrColorBG !== undefined) {
                    $(this).css('background', ''+attrColorBG+'');
                }
          });

        }


})(jQuery);