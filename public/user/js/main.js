(function($) {

	"use strict";

	$(window).stellar({
    responsive: true,
    parallaxBackgrounds: true,
    parallaxElements: true,
    horizontalScrolling: false,
    hideDistantElements: false,
    scrollProperty: 'scroll'
  });


	var fullHeight = function() {

		$('.js-fullheight').css('height', $(window).height());
		$(window).resize(function(){
			$('.js-fullheight').css('height', $(window).height());
		});

	};
	fullHeight();

	// loader
	var loader = function() {
		setTimeout(function() {
			if($('#ftco-loader').length > 0) {
				$('#ftco-loader').removeClass('show');
			}
		}, 1);
	};
	loader();

	var carousel = function() {
		$('.carousel-testimony').owlCarousel({
			center: true,
			loop: true,
			autoplay: true,
			autoplaySpeed:2000,
			items:1,
			margin: 30,
			stagePadding: 0,
			nav: false,
			navText: ['<span class="ion-ios-arrow-back">', '<span class="ion-ios-arrow-forward">'],
			responsive:{
				0:{
					items: 1
				},
				600:{
					items: 2
				},
				1000:{
					items: 3
				}
			}
		});

	};
	carousel();

	$('nav .dropdown').hover(function(){
		var $this = $(this);
		// 	 timer;
		// clearTimeout(timer);
		$this.addClass('show');
		$this.find('> a').attr('aria-expanded', true);
		// $this.find('.dropdown-menu').addClass('animated-fast fadeInUp show');
		$this.find('.dropdown-menu').addClass('show');
	}, function(){
		var $this = $(this);
			// timer;
		// timer = setTimeout(function(){
			$this.removeClass('show');
			$this.find('> a').attr('aria-expanded', false);
			// $this.find('.dropdown-menu').removeClass('animated-fast fadeInUp show');
			$this.find('.dropdown-menu').removeClass('show');
		// }, 100);
	});


	$('#dropdown04').on('show.bs.dropdown', function () {
	  console.log('show');
	});

	// scroll
	var scrollWindow = function() {
		$(window).scroll(function(){
			var $w = $(this),
					st = $w.scrollTop(),
					navbar = $('.ftco_navbar'),
					sd = $('.js-scroll-wrap');

			if (st > 150) {
				if ( !navbar.hasClass('scrolled') ) {
                    navbar.addClass('scrolled');
                    navbar.find('.logo-header span').show();
                    navbar.find('.container.welcome-mobile').css('display', 'flex');
				}
			}
			if (st < 150) {
				if ( navbar.hasClass('scrolled') ) {
					navbar.removeClass('scrolled sleep');
                    navbar.find('.logo-header span').hide();
                    navbar.find('.container.welcome-mobile').hide();
					// $('.logo-header a img').attr("src", window.location.origin + '/user/img/logo.png');
				}
			}
			if ( st > 150 ) {
				if ( !navbar.hasClass('awake') ) {
					navbar.addClass('awake');
					// $('.logo-header a img').attr("src", window.location.origin + '/user/img/logo_b.png');
				}

				if(sd.length > 0) {
					sd.addClass('sleep');
				}
			}
			if ( st < 150 ) {
				if ( navbar.hasClass('awake') ) {
					navbar.removeClass('awake');
					navbar.addClass('sleep');
				}
				if(sd.length > 0) {
					sd.removeClass('sleep');
				}
			}
		});
	};
	scrollWindow();

	var counter = function() {

		$('#section-counter, .wrap-about, .ftco-counter').waypoint( function( direction ) {

			if( direction === 'down' && !$(this.element).hasClass('ftco-animated') ) {

				var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',')
				$('.number').each(function(){
					var $this = $(this),
						num = $this.data('number');
					$this.animateNumber(
					  {
					    number: num,
					    numberStep: comma_separator_number_step
					  }, 7000
					);
				});

			}

		} , { offset: '95%' } );

	}
	counter();


	var contentWayPoint = function() {
		var i = 0;
		$('.ftco-animate').waypoint( function( direction ) {

			if( direction === 'down' && !$(this.element).hasClass('ftco-animated') ) {

				i++;

				$(this.element).addClass('item-animate');
				setTimeout(function(){

					$('body .ftco-animate.item-animate').each(function(k){
						var el = $(this);
						setTimeout( function () {
							var effect = el.data('animate-effect');
							if ( effect === 'fadeIn') {
								el.addClass('fadeIn ftco-animated');
							} else if ( effect === 'fadeInLeft') {
								el.addClass('fadeInLeft ftco-animated');
							} else if ( effect === 'fadeInRight') {
								el.addClass('fadeInRight ftco-animated');
							} else {
								el.addClass('fadeInUp ftco-animated');
							}
							el.removeClass('item-animate');
						},  k * 50, 'easeInOutExpo' );
					});

				}, 100);

			}

		} , { offset: '95%' } );
	};
	contentWayPoint();



	// magnific popup
	$('.image-popup').magnificPopup({
    type: 'image',
    closeOnContentClick: true,
    closeBtnInside: false,
    fixedContentPos: true,
    mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
     gallery: {
      enabled: true,
      navigateByImgClick: true,
      preload: [0,1] // Will preload 0 - before current, and 1 after the current image
    },
    image: {
      verticalFit: true
    },
    zoom: {
      enabled: true,
      duration: 300 // don't foget to change the duration also in CSS
    }
  });

  $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
    disableOn: 700,
    type: 'iframe',
    mainClass: 'mfp-fade',
    removalDelay: 160,
    preloader: false,

    fixedContentPos: false
  });

  $('[data-toggle="popover"]').popover()
	$('[data-toggle="tooltip"]').tooltip()

})(jQuery);



$('.detalhes').on("click", ".Remove", function(evt){
    evt.stopPropagation();
    var Remove_item     = $(this).attr("Remove_item");
});

$('.add-to-cart').on('click', function (evt) {
    evt.stopPropagation();
    const product_id = $(this).attr('product-id');
    let qty = 1;
    let iten;
    if($('.qty-prdduct').length) qty = $('.qty-prdduct').val();

    if(qty <= 0){
        Toast.fire({
            icon: 'error',
            title: "A quantidade deve ser igual ou maior que zero"
        });
        return false;
    }
    if(qty > 99){
        Toast.fire({
            icon: 'error',
            title: "A quantidade deve ser igual ou menos que 99"
        });
        return false;
    }

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: window.location.origin + "/queries/ajax/addCart",
        data: { product_id, qty },
        dataType: 'json',
        success: response => {

            iten = response[1].arrItems;
            console.log(iten);

            if(response[0] != false) {
                qty_items = response[1].qty_items;
                $('.qty_cart_all').text(qty_items);

                if($(`.cart-iten[product-id="${product_id}"]`).length) {
                    $(`.cart-iten[product-id="${product_id}"] .quantity span`).text(iten.qty);
                    $(`.cart-iten[product-id="${product_id}"] a.price`).text('R$ ' + iten.value);
                } else if($('.cart-items .cart-iten').length <= 3) {
                    if($('.cart-items .no-items').length) {
                        $('.btn-open-products-index')
                            .toggleClass('btn-open-products-index btn-open-cart-index')
                            .attr('href', window.location.origin + '/carrinho')
                            .html('Abrir Carrinho <span class="ion-ios-arrow-round-forward"></span>');
                        $('.cart-items .content-items').empty();
                    }

                    $('.cart-items .content-items').append(`
                    <div class="dropdown-item d-flex align-items-start cart-iten" product-id="${iten.id}">
                        <div class="img" style="background-image: url('${window.location.origin}/user/img/products/${iten.path_image}');"></div>
                        <div class="text pl-3">
                            <h4><a href="${window.location.origin}/produtos/${iten.id}">${iten.name}</a></h4>
                            <p class="mb-0">
                                <a class="price">R$ ${iten.value}</a>
                            <span class="quantity ml-3">Quantidade: <span>${iten.qty}</span></span>
                            </p>
                        </div>
                    </div>`);
                }

                Toast.fire({
                    icon: 'success',
                    title: 'Produto adicionado ao carrinho!'
                })
            }
            else{
                qty_items = response[2].qty_items;
                $('.qty_cart_all').text(qty_items);
                Toast.fire({
                    icon: 'error',
                    title: response[1]
                })
            }
        }, error: () => {
            Toast.fire({
                icon: 'error',
                title: "Acorreu um problema, caso o problema persistir contate o suporte"
            })
        }
    });
    return false;
})

const somenteNumeros = num => {
    var er = /[^0-9.]/;
    er.lastIndex = 0;
    var campo = num;
    if (er.test(campo.value)) {
        campo.value = "";
    }
}

var Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true,
    onOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})

$('div[class^="star-rating"] label i.fa').on('click mouseover',function(){
    // remove classe ativa de todas as estrelas
    const el = $(this).closest('div[class^="star-rating"]');
    el.find('label i.fa').removeClass('active');
    // pegar o valor do input da estrela clicada
    var val = $(this).prev('input').val();
    //percorrer todas as estrelas
    el.find('label i.fa').each(function(){
        /* checar de o valor clicado é menor ou igual do input atual
        *  se sim, adicionar classe active
        */
        var $input = $(this).prev('input');
        if($input.val() <= val){
            $(this).addClass('active');
        }
    });
});
//Ao sair da div star-rating
$('div[class^="star-rating"]').mouseleave(function(){
    //pegar o valor clicado
    const el = $(this).closest('div[class^="star-rating"]');
    var val = $(this).find('input:checked').val();
    //se nenhum foi clicado remover classe de todos
    if(val == undefined ){
        el.find('label i.fa').removeClass('active');
    } else {
        //percorrer todas as estrelas
        el.find('label i.fa').each(function(){
            /* Testar o input atual do laço com o valor clicado
            *  se maior, remover classe, senão adicionar classe
            */
            var $input = $(this).prev('input');
            if($input.val() > val){
                $(this).removeClass('active');
            } else {
                $(this).addClass('active');
            }
        });
    }
});

var input = document.getElementById("picture");

$('#insert-testimony #picture').on("change", function(){
    var nome = "Selecione uma imagem sobre o depoimento";
    if($('#insert-testimony #picture').prop('files')[0] !== undefined) nome = $('#insert-testimony #picture').prop('files')[0].name;
    $('#insert-testimony label[for="exampleInputFile"]').text(nome);
});

const validCpf = cpf => {
    let soma = 0
    let resto;
    for (let i = 1; i <= 9; i++) soma = soma + parseInt(cpf.substring(i-1, i)) * (11 - i)

    resto = (soma * 10) % 11
    if ((resto == 10) || (resto == 11)) resto = 0
    if (resto != parseInt(cpf.substring(9, 10)) ) return false

    soma = 0
    for (let i = 1; i <= 10; i++) soma = soma + parseInt(cpf.substring(i-1, i)) * (12 - i)

    resto = (soma * 10) % 11
    if ((resto == 10) || (resto == 11))  resto = 0
    if (resto != parseInt(cpf.substring(10, 11) ) ) return false

    return true
}
// TRANSFORMAR NUMERO EM MOEDA REAL
const numberToReal = numero => {
    numero = parseFloat(numero);
    numero = numero.toFixed(2).split('.');
    numero[0] = numero[0].split(/(?=(?:...)*$)/).join('.');
    return numero.join(',');
}

// TRANSFORMAR MOEDA REAL EM NUMERO COMPUTÁVEL
const realToNumber = numero => {
    if(numero === undefined) return false;
    numero = numero.toString();
    numero = numero.replace(".", "").replace(",", ".");
    return parseFloat(numero);
}

const validDate = (date) => {
    var RegExPattern = /^((((0?[1-9]|[12]\d|3[01])[\.\-\/](0?[13578]|1[02])      [\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|[12]\d|30)[\.\-\/](0?[13456789]|1[012])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|1\d|2[0-8])[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|(29[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00)))|(((0[1-9]|[12]\d|3[01])(0[13578]|1[02])((1[6-9]|[2-9]\d)?\d{2}))|((0[1-9]|[12]\d|30)(0[13456789]|1[012])((1[6-9]|[2-9]\d)?\d{2}))|((0[1-9]|1\d|2[0-8])02((1[6-9]|[2-9]\d)?\d{2}))|(2902((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00))))$/;

    if (!((date.match(RegExPattern)) && (date != '')))
        return false;
    else
        return true
}

$('#insert-testimony form').on('submit', function (){
    $('button[type="submit"]', this).attr('disabled', true);
})
