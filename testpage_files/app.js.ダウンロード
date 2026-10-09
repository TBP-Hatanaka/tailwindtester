objectFitImages();
jQuery(function($) {

	var mediaQuery = window.matchMedia('(max-width: 768px)');
	handle(mediaQuery);
	mediaQuery.addListener(handle);

	function handle(mm) {
		if (mm.matches) {

			$('.hamburger').on('click', function() {
				$('#js-globalnav').stop().slideToggle();
				$(this).toggleClass('is-active');
				$('.sub_menu').toggleClass('is-passive');
			});

			$('.gNaviBox a[href^="#"]').on('click', function(event) {
				$('.hamburger').trigger('click');
				$(this).parents('.sub_menu').prev().trigger('click');
			});

			$('.fnavParts__inr .title').after('<button class="menu_btn"></button>');
			// SPANタグの場合
			$('.gNaviBox .menu > li > span').after('<button class="menu_btn"></button>');
			// Aタグの場合
			$('.gNaviBox .menu > li > a').after('<button class="menu_btn"></button>');

			$('.menu_btn').each(function() {
				$(this).on('click', function() {
					$(this).toggleClass('is-active');
					$(this).next('.sub-menu').toggleClass('is-open');
				});
			});
			
			var headerHeight = $('#site-header').height();
			console.log(headerHeight);

		} else {
			var headerHeight = $('#site-header').height();
			$('.pageTop').each(function() {
				var scroll = $(window).scrollTop();
				if (scroll > 400) {
					$(this).fadeIn();
				} else {
					$(this).fadeOut();
				}
			});
		}
	}
	var headerHeight = $('#site-header').height();
	$('#wrapper').css('padding-top', headerHeight);
	$(window).resize(function() {
		var headerHeight = $('#site-header').height();
		$('#wrapper').css('padding-top', headerHeight);
	});

	$(window).scroll(function() {
		$('#site-header').each(function() {
			var scroll = $(window).scrollTop();
			var headerHeight = $(this).height();
			if (scroll > headerHeight) {
				$(this).addClass('is-active');
			} else {
				//$(this).removeClass('is-active');
				$(this).addClass('is-active');
			}
		});

		$('.pageTop').each(function() {
			var scroll = $(window).scrollTop();
			if (scroll > 400) {
				$(this).fadeIn();
			} else {
				$(this).fadeOut();
			}
		});
	});

	$("#banner-swiper").hover(function() {
		(this).swiper.autoplay.stop();
	}, function() {
		(this).swiper.autoplay.start();
	});
	$('.disabled > a[href]').click(function() {
		return false;
	});
	$('a[href^="#"]').click(function() {
		var speed = 500;
		var href = $(this).attr("href");
		var target = $(
			href == "#" || href == ""
			? 'html'
			: href);
		var position = target.offset().top;
		$("html, body").animate({
			scrollTop: position
		}, speed, "swing");
		return false;
	});

	if (window.matchMedia("(max-width: 768px)").matches) {}
	let heroswiper = new Swiper('#banner-swiper', {
		loop: true,
		slidesPerView: 2,
		spaceBetween: 4,
		speed: 400,
		autoplay: {
			delay: 2000,
			disableOnInteraction: false
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev'
		},
		pagination: {
			el: '.swiper-pagination',
			type: 'bullets',
			clickable: true
		},
		breakpoints: {
			1200: {
				slidesPerView: 4,
				spaceBetween: 37
			},
			1024: {
				slidesPerView: 4,
				spaceBetween: 30
			},
			768: {
				slidesPerView: 3,
				spaceBetween: 20
			}
		}
	});
});
