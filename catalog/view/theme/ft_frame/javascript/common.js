jQuery.event.special.touchstart = { setup: function( _, ns, handle ){ this.addEventListener("touchstart", handle, { passive: true }) } };

function viewport() {
    var e = window, a = 'inner';
    if (!('innerWidth' in window )) {
        a = 'client';
        e = document.documentElement || document.body;
    }
    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
}

if(viewport().width > 992){
	$(document).on('focus','#ftSearch input.form-control', function(){
		$('.menu-box').addClass('d-none');
	});

	// $(document).on('blur','#ftSearch input.form-control', function(){
	//	$('.menu-box').removeClass('d-none');
	//});

	$(document).on('click', function(e) {
		// console.log(e.target);
		if ($(e.target).hasClass('search-backdrop')) {
			$('.menu-box').removeClass('d-none');
		}
	});
}

$(document).ready(function () {
	var navTop = $('#top');
	var navTopHeight = navTop.outerHeight();

	if ($(window).scrollTop() >= navTopHeight) {
		$('header').addClass('is-sticky');
	}

	$(window).scroll(function () {
		$('header').toggleClass('is-sticky', $(window).scrollTop() >= navTopHeight);
	});
});

function createCustomer(){
	$.ajax({
		url: 'index.php?route=common/login_modal/register',
		type: 'GET',
		dataType: 'html',
		success: function(data) {
			var html = '<div id="ft-register-modal" class="modal fade" role="dialog">' + data + '</div>'

			$('body').append(html);

			$('#ft-register-modal').modal('show');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function clearCart(){
	$.ajax({
        url: 'index.php?route=extension/module/frametheme/ft_cart/clearCart',
        type: 'POST',
        dataType: 'json',
        data: '&clearcart=true',
        success: function(data) {
             $('#ft_popup_cart').modal('hide');
             $('#ft_cart .cart-list').html();

             setTimeout(function () {
					$.ajax({
						url: 'index.php?route=extension/module/frametheme/ft_cart/info',
						type: 'post',
						dataType: 'html',
						complete: function() {
							$('#ft_cart').removeClass('loading');
							$('#ft_cart > button').removeAttr('disabled');
						},
						success: function(data){
							$('#ft_cart > button #ft_cart_total').html(0);
              			$('#ft_m_cart_total').html($('#ft_cart > button #ft_cart_total .products > b').text());
							$('#ft_cart .cart-list').html($(data).find('.cart-list').html());

						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				}, 100);
        },
        error: function(xhr, ajaxOptions, thrownError) {
        	alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
}




$(document).on('click', '#ft-login-open', function (e) {
    e.preventDefault();
    if( $('#ft-login-modal').length ){
    		$('#ft-login-modal').modal('show');
    		return true;
    }
    $.ajax({
        url: 'index.php?route=common/login_modal',
        type: 'GET',
        dataType: 'html',
        success: function(data) {
            var html = '<div id="ft-login-modal" class="modal fade" role="dialog">' + data + '</div>'

            $('body').append(html);

            $('#ft-login-modal').modal('show');
        },
        error: function(xhr, ajaxOptions, thrownError) {
        	alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});

$(document).on('hide.bs.modal', '#ft_popup_add_cart.modal.fade', function (e) {
	$('#ft_popup_add_cart').remove();
});

$(document).on('hide.bs.modal', '#login-form-popup.modal.fade', function (e) {
	$('#login-form-popup').remove();
});

$(document).undelegate('.agree', 'click');

$(document).on('click', '.openSearchMob', function(){
	$('header').addClass('active-search');
	$('.ft-search').addClass('active');
});

$(document).on('click', '#closeSearchMob', function(){
	$('header').removeClass('active-search');
	$('.ft-search').removeClass('active');
});

$(document).delegate('.agree', 'click', function(e) {

	e.preventDefault();



	$('#modal-agree').remove();



	var element = this;



	$.ajax({

		url: $(element).attr('href'),

		type: 'get',

		dataType: 'html',

		success: function(data) {

			html  = '<div id="modal-agree" class="modal">';

			html += '  <div class="modal-dialog">';

			html += '    <div class="modal-content">';

			html += '      <div class="modal-header no-gutters">';

			html += '        <div class="col"><h5 class="modal-title">' + $(element).text() + '</h5></div><div class="col-auto"><div class="ft-icon-24 text-gray-500 ml-2" data-dismiss="modal"><svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg></div></div>';

			html += '      </div>';

			html += '      <div class="modal-body">' + data + '</div>';

			html += '    </div>';

			html += '  </div>';

			html += '</div>';



			$('body').append(html);



			$('#modal-agree').modal('show');

		}

	});

});



// Autocomplete */

(function($) {

	$.fn.autocomplete = function(option) {

		return this.each(function() {

			this.timer = null;

			this.items = new Array();



			$.extend(this, option);



			$(this).attr('autocomplete', 'off');



			// Focus

			$(this).on('focus', function() {

				this.request();

			});



			// Blur

			$(this).on('blur', function() {

				setTimeout(function(object) {

					object.hide();

				}, 200, this);

			});



			// Keydown

			$(this).on('keydown', function(event) {

				switch(event.keyCode) {

					case 27: // escape

						this.hide();

						break;

					default:

						this.request();

						break;

				}

			});



			// Click

			this.click = function(event) {

				event.preventDefault();



				value = $(event.target).parent().attr('data-value');



				if (value && this.items[value]) {

					this.select(this.items[value]);

				}

			}



			// Show

			this.show = function() {

				var pos = $(this).position();



				$(this).siblings('ul.dropdown-menu').css({

					top: pos.top + $(this).outerHeight(),

					left: pos.left

				});



				$(this).siblings('ul.dropdown-menu').show();

			}



			// Hide

			this.hide = function() {

				$(this).siblings('ul.dropdown-menu').hide();

			}



			// Request

			this.request = function() {

				clearTimeout(this.timer);



				this.timer = setTimeout(function(object) {

					object.source($(object).val(), $.proxy(object.response, object));

				}, 200, this);

			}



			// Response

			this.response = function(json) {

				html = '';



				if (json.length) {

					for (i = 0; i < json.length; i++) {

						this.items[json[i]['value']] = json[i];

					}



					for (i = 0; i < json.length; i++) {

						if (!json[i]['category']) {

							html += '<li data-value="' + json[i]['value'] + '"><a class="dropdown-item" href="#">' + json[i]['label'] + '</a></li>';

						}

					}



					// Get all the ones with a categories

					var category = new Array();



					for (i = 0; i < json.length; i++) {

						if (json[i]['category']) {

							if (!category[json[i]['category']]) {

								category[json[i]['category']] = new Array();

								category[json[i]['category']]['name'] = json[i]['category'];

								category[json[i]['category']]['item'] = new Array();

							}



							category[json[i]['category']]['item'].push(json[i]);

						}

					}



					for (i in category) {

						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';



						for (j = 0; j < category[i]['item'].length; j++) {

							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a class="dropdown-item" href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';

						}

					}

				}



				if (html) {

					this.show();

				} else {

					this.hide();

				}



				$(this).siblings('ul.dropdown-menu').html(html);

			}



			$(this).after('<ul class="dropdown-menu"></ul>');

			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));



		});

	}

})(window.jQuery);

function addToCartWorker(element) {
	var product_option_id = $(element).data('po-id');
	var product_option_value_id = $(element).data('pov-id');
	var cg_id = $(element).data('cg-id');
	var product_id = $(element).data('product-id');
	var data = {
		'product_id': product_id,
		'quantity': 1,
		'cg_id': cg_id,
		['option[' + product_option_id + ']']: product_option_value_id
	};

	$.ajax({
		url: 'index.php?route=extension/module/frametheme/ft_cart/add',
		type: 'post',
		data: data,
		dataType: 'json',
		success: function(json) {
			$('#ft_popup_cart .alert').remove();

			if (toasts_timeout > 0) {
				if( $('#ft_popup_add_cart').length ){
					$('#ft_popup_add_cart').remove();
				}

				$.ajax({
					url: 'index.php?route=extension/module/frametheme/ft_cart/getModalSuccessAdd',
					type: 'GET',
					dataType: 'html',
					success: function(html) {
						var $html = $(html);

						$html.find('.modal-body').prepend('<div class="success-add-product">' + json['success'] + '</div>');

						$('body').append($html);

						setTimeout(function () {
							$('#ft_popup_add_cart').modal('show');
						}, 300);

					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});

				setTimeout(function () {
					$.ajax({
						url: 'index.php?route=extension/module/frametheme/ft_cart/info',
						type: 'post',
						dataType: 'html',
						beforeSend: function() {
							loading_text = $('#ft_cart > button').attr('data-loading');
							$('#ft_cart').addClass('loading');
							$('#ft_cart > button').attr('disabled', 'disabled');
						},
						complete: function() {
							$('#ft_cart').removeClass('loading');
							$('#ft_cart > button').removeAttr('disabled');
						},
						success: function(data){
							var data_alert 	= '<div class="alert alert-light mt-n3 mx-n3 px-3 border-bottom">';
							data_alert += 	'<div class="row no-gutters">';
							data_alert += 		'<div class="col-auto">';
							data_alert += 			'<i class="fa fa-fw fa-check mr-2"></i>';
							data_alert += 		'</div>';
							data_alert += 		'<div class="col">';
							data_alert += 			json['success'];
							data_alert += 		'</div>';
							data_alert += 		'<div class="col-auto">';
							data_alert += 			'<button type="button" class="close mr-1" data-dismiss="alert">&times;</button>';
							data_alert += 		'</div>';
							data_alert += 	'</div>';
							data_alert += '</div>';

							$('#ft_cart .cart-list').before(data_alert);
							$('#ft_cart > button #ft_cart_total').html(json['total']);
							$('#ft_m_cart_total').html($('#ft_cart > button #ft_cart_total .products > b').text());
							$('#ft_cart .cart-list').html($(data).find('.cart-list').html());
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				}, 100);
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

var cart = {

	'add': function(product_id, quantity) {

		$.ajax({
			url: 'index.php?route=extension/module/frametheme/ft_cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				// var loading_text = $('#ft_cart > button').attr('data-loading');
				// $('#ft_cart').addClass('loading');
				// $('#ft_cart > button').attr('disabled', 'disabled');
				// old_cart = $('#ft_cart > button #ft_cart_total').html();
				// $('#ft_cart > button #ft_cart_total').html('<span class="loading-wrapper">' + loading_text + '</span>');
			},
			success: function(json) {
				if (json['redirect']) {
					// location = json['redirect'];
					// $('#ft_cart').removeClass('loading');
					// $('#ft_cart > button #ft_cart_total').html(old_cart);
					// $('#ft_cart > button').removeAttr('disabled');
					setTimeout(function () {
						$.ajax({
							url: 'index.php?route=extension/module/frametheme/ft_qoptions&product_id=' + product_id,
							type: 'post',
							dataType: 'html',
							beforeSend: function() {
								$('#ft_modal_qoptions, .modal-backdrop').remove();

								html  = '<div id="ft_modal_qoptions" class="modal fade" tabindex="-1">';
								html += '  <div class="modal-dialog modal-dialog-centered">';
								html += '    <div class="modal-content" id="qoptions-product-' + product_id + '">';
								html += '      <div class="modal-load-mask text-center p-5 text-muted">';
								html += '        <div class="modal-load-mask text-muted d-flex justify-content-center align-items-center py-4">';
								html += '					 <div class="spinner-border text-gray-300"></div>';
								html += '    	   </div>';
								html += '    	 </div>';
								html += '    </div>';
								html += '  </div>';
								html += '</div>';

								$('body').append(html);

								if (typeof add_modal_listner == 'function') { add_modal_listner('#ft_modal_qoptions') }

								$('#ft_modal_qoptions').modal('show');
							},

							success: function(data) {
								$('#ft_modal_qoptions .modal-content').html(data);
							},

							error: function(xhr, ajaxOptions, thrownError) {
								alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						});
					}, 100);

				} else if (json['success']) {
					$('#ft_popup_cart .alert').remove();

          if (toasts_timeout > 0) {

            $('.toast').remove();

            let now = new Date(),
                mins = now.getMinutes() > 9 ? now.getMinutes() : '0' + now.getMinutes(),
                hours = now.getHours() > 9 ? now.getHours() : '0' + now.getHours(),
                current_time = hours + ':' + mins,
                toast = `
                        <div class="toast m-3 position-fixed t-0 r-0 z-index-max border-primary" role="alert" aria-live="assertive" aria-atomic="true" data-delay="${toasts_timeout}">
                          <div class="toast-header">
                            <svg class="d-block ft-icon-18 text-secondary mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                              <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6zM7.58 4.08L6.15 2.65C3.75 4.48 2.17 7.3 2.03 10.5h2c.15-2.65 1.51-4.97 3.55-6.42zm12.39 6.42h2c-.15-3.2-1.73-6.02-4.12-7.85l-1.42 1.43c2.02 1.45 3.39 3.77 3.54 6.42z"/>
                            </svg>
                            ${current_time}
                            <svg class="d-block ft-icon-18 text-secondary cursor-pointer ml-auto" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-dismiss="toast">
                              <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                          </div>
                          <div class="toast-body">
                            ${json['success']}
                          </div>
                        </div>
                        `;

            $('body').append(toast);
            $('.toast').toast('show');
          } else {
            $('#ft_popup_cart').modal('show');
          }

					setTimeout(function () {

						if ( document.querySelector('#checkout-cart, #checkout-checkout') != null) location = location.href;

						$.ajax({
						url: 'index.php?route=extension/module/frametheme/ft_cart/info',
						type: 'post',
						dataType: 'html',
						complete: function() {
							$('#ft_cart').removeClass('loading');
							$('#ft_cart > button').removeAttr('disabled');
						},

						success: function(data){
							var data_alert 	= '<div class="alert alert-light mt-n3 mx-n3 px-3 border-bottom">';
									data_alert += 	'<div class="row no-gutters">';
									data_alert += 		'<div class="col-auto">';
									data_alert += 			'<i class="fa fa-fw fa-check mr-2"></i>';
									data_alert += 		'</div>';
									data_alert += 		'<div class="col">';
									data_alert += 			json['success'];
									data_alert += 		'</div>';
									data_alert += 		'<div class="col-auto">';
									data_alert += 			'<button type="button" class="close mr-1" data-dismiss="alert">&times;</button>';
									data_alert += 		'</div>';
									data_alert += 	'</div>';
									data_alert += '</div>';

							$('#ft_cart .cart-list').before(data_alert);
							$('#ft_cart > button #ft_cart_total').html(json['total']);

             			//$('#ft_m_cart_total').html($('#ft_cart > button #ft_cart_total .products > b').text());

							$('#ft_cart .cart-list').html($(data).find('.cart-list').html());
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
					}, 100);
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},

	'update': function(key, quantity) {

		$.ajax({

			url: 'index.php?route=extension/module/frametheme/ft_cart/edit',

			type: 'post',

			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),

			dataType: 'json',

			beforeSend: function() {

			setTimeout(function () {

					// var loading_text = $('#ft_cart > button').attr('data-loading');

					// $('#ft_cart').addClass('loading');

					// $('#ft_cart > button').attr('disabled', 'disabled');

					// $('#ft_cart > button #ft_cart_total').html('<span class="loading-wrapper">' + loading_text + '</span>');

					$('#ft_popup_cart').find('.alert').remove();

				}, 99);

			},

			success: function(json) {

				setTimeout(function () {

					if ( document.querySelector('#checkout-cart, #checkout-checkout') != null) location = location.href;



					$.ajax({

						url: 'index.php?route=extension/module/frametheme/ft_cart/info',

						type: 'post',

						dataType: 'html',

						complete: function() {

							$('#ft_cart').removeClass('loading');

							$('#ft_cart > button').removeAttr('disabled');

						},

						success: function(data){

							$('#ft_cart > button #ft_cart_total').html(json['total']);



              			$('#ft_m_cart_total').html($('#ft_cart > button #ft_cart_total .products > b').text());



							$('#ft_cart .cart-list').html($(data).find('.cart-list').html());

						},

						error: function(xhr, ajaxOptions, thrownError) {

							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

						}

					});



				}, 100);

			},

			error: function(xhr, ajaxOptions, thrownError) {

				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

			}

		});

	},

	'updatePopup': function(key, quantity) {
		if( $('#ft_popup_add_cart').length ){
			$('#ft_popup_add_cart .loading-icon').removeClass('d-none');
		}

		$.ajax({

			url: 'index.php?route=extension/module/frametheme/ft_cart/editPopup',

			type: 'post',

			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),

			dataType: 'json',

			beforeSend: function() {

				setTimeout(function () {

					// var loading_text = $('#ft_cart > button').attr('data-loading');

					// $('#ft_cart').addClass('loading');

					// $('#ft_cart > button').attr('disabled', 'disabled');

					// $('#ft_cart > button #ft_cart_total').html('<span class="loading-wrapper">' + loading_text + '</span>');

					$('#ft_popup_cart').find('.alert').remove();

				}, 99);

			},

			success: function(json) {

				setTimeout(function () {

					if ( document.querySelector('#checkout-cart, #checkout-checkout') != null) location = location.href;



					$.ajax({

						url: 'index.php?route=extension/module/frametheme/ft_cart/info',

						type: 'post',

						dataType: 'html',

						complete: function() {

							$('#ft_cart').removeClass('loading');

							$('#ft_cart > button').removeAttr('disabled');

						},

						success: function(data){

							$('#ft_cart > button #ft_cart_total').html(json['total']);



             			$('#ft_m_cart_total').html($('#ft_cart > button #ft_cart_total .products > b').text());



							$('#ft_cart .cart-list').html($(data).find('.cart-list').html());


							if( $('#ft_popup_add_cart').length ){
								$('#ft_popup_add_cart .success-add-product').remove();
								$('#ft_popup_add_cart .cart-totals').load('index.php?route=extension/module/frametheme/ft_cart/getModalSuccessAdd .cart-totals > *');
								$('#ft_popup_add_cart #details-products').load('index.php?route=extension/module/frametheme/ft_cart/getModalSuccessAdd #details-products > *');
								$('#ft_popup_add_cart .loading-icon').addClass('d-none');
							}

						},

						error: function(xhr, ajaxOptions, thrownError) {

							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

						}

					});



				}, 100);

			},

			error: function(xhr, ajaxOptions, thrownError) {

				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

			}

		});

	},

	'remove': function(key) {
		if( $('#ft_popup_add_cart').length ){
			$('#ft_popup_add_cart .loading-icon').removeClass('d-none');
		}

		$.ajax({

			url: 'index.php?route=extension/module/frametheme/ft_cart/remove',

			type: 'post',

			data: 'key=' + key,

			dataType: 'json',

			beforeSend: function() {

				setTimeout(function () {

					var loading_text = $('#ft_cart > button').attr('data-loading');

					$('#ft_cart').addClass('loading');

					$('#ft_cart > button').attr('disabled', 'disabled');

					$('#ft_cart > button #ft_cart_total').html('<span class="loading-wrapper">' + loading_text + '</span>');

					$('#ft_popup_cart').find('.alert').remove();

				}, 99);

			},

			success: function(json) {

				if( $('#ft_popup_add_cart').length ){
					$('#ft_popup_add_cart .success-add-product').remove();
					$('#ft_popup_add_cart .cart-totals').load('index.php?route=extension/module/frametheme/ft_cart/getModalSuccessAdd .cart-totals > *');
					$('#ft_popup_add_cart #details-products').load('index.php?route=extension/module/frametheme/ft_cart/getModalSuccessAdd #details-products > *');
					$('#ft_popup_add_cart .loading-icon').addClass('d-none');
				}

				setTimeout(function () {



					if ( document.querySelector('#checkout-cart, #checkout-checkout') != null) {

            location = location.href

          } else {

            $.ajax({

  						url: 'index.php?route=extension/module/frametheme/ft_cart/info',

  						type: 'post',

  						dataType: 'html',

  						complete: function() {

  							$('#ft_cart').removeClass('loading');

  							$('#ft_cart > button').removeAttr('disabled');

  						},

  						success: function(data){

  							$('#ft_cart > button #ft_cart_total').html(json['total']);



                		$('#ft_m_cart_total').html($('#ft_cart > button #ft_cart_total .products > b').text());



  							$('#ft_cart .cart-list').html($(data).find('.cart-list').html());

  						},

  						error: function(xhr, ajaxOptions, thrownError) {

  							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

  						}

  					});

          }



				}, 100);

			},

			error: function(xhr, ajaxOptions, thrownError) {

				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

			}

		});

	}

}



var voucher = {

	'add': function() {},

	'remove': function(key) {

		$.ajax({

			url: 'index.php?route=extension/module/frametheme/ft_cart/remove',

			type: 'post',

			data: 'key=' + key,

			dataType: 'json',

			beforeSend: function() {

				setTimeout(function () {

					var loading_text = $('#ft_cart > button').attr('data-loading');

					$('#ft_cart').addClass('loading');

					$('#ft_cart > button').attr('disabled', 'disabled');

					$('#ft_cart > button #ft_cart_total').html('<span class="loading-wrapper">' + loading_text + '</span>');

					$('#ft_popup_cart').find('.alert').remove();

				}, 99);

			},

			success: function(json) {

				setTimeout(function () {

					if ( document.querySelector('#checkout-cart, #checkout-checkout') != null) location = location.href;



					$.ajax({

						url: 'index.php?route=extension/module/frametheme/ft_cart/info',

						type: 'post',

						dataType: 'html',

						complete: function() {

							$('#ft_cart').removeClass('loading');

							$('#ft_cart > button').removeAttr('disabled');

						},

						success: function(data){

							$('#ft_cart > button #ft_cart_total').html(json['total']);



              $('#ft_m_cart_total').html($('#ft_cart > button #ft_cart_total .products > b').text());



							$('#ft_cart .cart-list').html($(data).find('.cart-list').html());

						},

						error: function(xhr, ajaxOptions, thrownError) {

							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

						}

					});



				}, 100);

			},

			error: function(xhr, ajaxOptions, thrownError) {

				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

			}

		});

	}

}



var wishlist = {

	'add': function(product_id) {

		$.ajax({

			url: 'index.php?route=account/wishlist/add',

			type: 'post',

			data: 'product_id=' + product_id,

			dataType: 'json',

			success: function(json) {

				$('.alert-dismissible').remove();



				if (json['redirect']) {

					location = json['redirect'];

				}



				if (json['success']) {

					$('body').prepend('<div style="width: 300px;position: fixed;z-index:9999; top: 10px;right: 10px;"><div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div></div>');

				}



				$('#wishlist-total').html(json['total']);

				$('#wishlist-total').attr('title', json['total']);



				// $('html, body').animate({ scrollTop: 0 }, 'slow');

			},

			error: function(xhr, ajaxOptions, thrownError) {

				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

			}

		});

	},

	'remove': function() {



	}

}



var compare = {

	'add': function(product_id) {

		$.ajax({

			url: 'index.php?route=product/compare/add',

			type: 'post',

			data: 'product_id=' + product_id,

			dataType: 'json',

			success: function(json) {

				$('.alert-dismissible').remove();



				if (json['success']) {

					$('body').prepend('<div style="width: 300px;position: fixed;z-index:9999; top: 10px;right: 10px;"><div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div></div>');



					$('#compare-total').html(json['total']);



					// $('html, body').animate({ scrollTop: 0 }, 'slow');

				}

			},

			error: function(xhr, ajaxOptions, thrownError) {

				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

			}

		});

	},

	'remove': function() {



	}

}



var ftSearch = {

	'search': function(button) {

			//if ($('html').attr('lang') == '') {
			//	var lang = '';
			//} else if ($('html').attr('lang') == 'uk') {
			//	var lang = 'ua';
			//} else {
			//	var lang = '';
			//}
			var lang = $('html').attr('lang');

			var url = lang+'/index.php?route=product/search',

					request = button.parentNode.parentNode.querySelector('[name=\'search\']'),

					category = button.parentNode.parentNode.querySelector('[name=\'category_id\']'),

					filter_description = button.parentNode.parentNode.querySelector('[name=\'description\']'),

					sub_category = button.parentNode.parentNode.querySelector('[name=\'sub_category\']');





			if (request != null) url += '&search=' + encodeURIComponent(request.value);



			if (category != null && category.value > 0) url += '&category_id=' + encodeURIComponent(category.value);



      if (sub_category != null && sub_category.value) url += '&sub_category=' + encodeURIComponent(sub_category.value);



			if (filter_description != null && filter_description.value) url += '&description=' + encodeURIComponent(filter_description.value);


			if (request.value.length) location = url;



	},

	'key_enter': function(e) {



		if (e.keyCode == 13) {

			var button = e.target.parentNode.parentNode.querySelector('.search-button');

			ftSearch.search(button);

		}



	},

	'category_select': function(e, category_id) {



		var items = e.target.parentNode.querySelectorAll('.dropdown-item'),

				category = e.target.parentNode.parentNode.parentNode.querySelector('[name=\'category_id\']'),

				select_label = e.target.parentNode.parentNode.parentNode.querySelector('.select-text');



		if (select_label != null) select_label.innerHTML =  e.target.textContent;

		items.forEach(function(item, i) { item.classList.remove('active') });

		e.target.classList.add('active');

		category.value = category_id;



	}

}



var ft_countupd = (step, minimum, field) => {



	var count = parseInt($(field).val()) + Number(step);



	count = count < Number(minimum) ? Number(minimum) : count;



	$(field).val(count);

	$(field).change();



	return false;

}



var ft_qview = (product_id) => {



	$.ajax({

		url: 'index.php?route=extension/module/frametheme/ft_qview&product_id=' + product_id,

		type: 'post',

		dataType: 'html',

		headers: {

      Accept: x_http_accept

    },

		beforeSend: function() {



			$('#ft_modal_qview, .modal-backdrop').remove();



			html  = '<div id="ft_modal_qview" class="modal fade" tabindex="-1">';

			html += '  <div class="modal-dialog modal-dialog-centered modal-lg">';

			html += '    <div class="modal-content" id="qview-product-' + product_id + '">';

			html += '      <div class="modal-load-mask text-muted d-flex justify-content-center align-items-center py-5 my-5">';

			html += '					<div class="spinner-border text-gray-300"></div>';

			html += '    	 </div>';

			html += '    </div>';

			html += '  </div>';

			html += '</div>';



			$('body').append(html);



			if (typeof add_modal_listner == 'function') add_modal_listner('#ft_modal_qview', 'quick-view-' + product_id );



			$('#ft_modal_qview').modal('show');



		},

		success: function(data) {

			$('#ft_modal_qview .modal-content').html(data);

		},

		error: function(xhr, ajaxOptions, thrownError) {

			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

		}

	});

}



var ft_fastorder = (product_id) => {



	$.ajax({

		url: 'index.php?route=extension/module/frametheme/ft_fastorder&product_id=' + product_id,

		type: 'post',

		dataType: 'html',

		headers: {

      Accept: x_http_accept

    },

		beforeSend: function() {



			$('#ft_modal_fastorder, .modal-backdrop').remove();



			html  = '<div id="ft_modal_fastorder" class="modal fade" tabindex="-1" role="dialog">';

			html += '  <div class="modal-dialog modal-dialog-centered" role="document">';

			html += '    <div class="modal-content" id="fastorder-product-' + product_id + '">';

			html += '      <div class="modal-load-mask text-muted d-flex justify-content-center align-items-center py-5 my-5">';

			html += '					<div class="spinner-border text-gray-300"></div>';

			html += '    	 </div>';

			html += '    </div>';

			html += '  </div>';

			html += '</div>';



			$('body').append(html);



			if (typeof add_modal_listner == 'function') add_modal_listner('#ft_modal_fastorder', 'quick-order-' +  + product_id);



			if ($('#ft_modal_qview').is('.show')) {

				$('#ft_modal_qview').modal('hide');

				$('#ft_modal_qview').on('hidden.bs.modal', function (e) {

					$('#ft_modal_fastorder').modal('show');

				});

			} else {

				$('#ft_modal_fastorder').modal('show');

			}



		},

		success: function(data) {

			$('#ft_modal_fastorder .modal-content').html(data);

		},

		error: function(xhr, ajaxOptions, thrownError) {

			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

		}

	});

}



var ft_scrlltop = (duration) => {

	step = window.pageYOffset / (duration / 10);

	var id = setInterval(function() {

		if (window.pageYOffset <= 0) {

			clearInterval(id)

		}

		window.scrollBy(0, -step);

	}, 10);

}



var listened_modal_is_open = false;



var add_modal_listner = (id, url = 'dialog') => {



	$(id).on('shown.bs.modal', function (e) {

		history.pushState(null, null, location.href + '#' + url);

		listened_modal_is_open = true;

	});



	$(id).on('hidden.bs.modal', function (e) {

		if ( listened_modal_is_open ) {

			window.history.back();

			listened_modal_is_open = false;

		}

	});



}



// var change_color_button_cart = () => {



// 	var targets = document.querySelectorAll('.product-item');



// 	targets.forEach(function(target, i) {



// 		var buttons = target.querySelectorAll('.btn-cart-add')



// 		target.addEventListener('mouseover', function() {

// 			buttons.forEach(function(button, i) {

// 				if (button != null) {

// 					button.classList.remove('btn-light');

// 					button.classList.add('btn-danger');

// 				}

// 			});

// 		}, false);



// 		target.addEventListener('mouseout', function() {

// 			buttons.forEach(function(button, i) {

// 				if (button != null) {

// 					button.classList.remove('btn-danger');

// 					button.classList.add('btn-light');

// 				}

// 			});

// 		}, false);



// 	});

// }



var lazyImgObserver = new IntersectionObserver((entries, observer) => {

  entries.forEach(entry => {

    if (entry.isIntersecting) {

      const lazyImg = entry.target;



      if (lazyImg.hasAttribute('data-src')) lazyImg.setAttribute('src', lazyImg.getAttribute('data-src'));

      if (lazyImg.hasAttribute('data-srcset')) lazyImg.setAttribute('srcset', lazyImg.getAttribute('data-srcset'));



      lazyImg.onload = () => {

        let spinner = lazyImg.parentNode.querySelector('.ft-lazy-spinner');

        if (spinner != null) spinner.remove();

      }



      observer.unobserve(lazyImg);

    }

  })

}, {

    root: null,

    rootMargin: '0px',

    threshold: 0.5

});



var lazyImgObserve = (parent) => {

    const arr = parent.querySelectorAll('.ft-lazy-img')

    arr.forEach(image => {

        lazyImgObserver.observe(image);

    });

}



window.addEventListener('DOMContentLoaded', function(e) {



	document.body.classList.remove('loading');



	if (typeof change_color_button_cart == 'function') change_color_button_cart();

	if (typeof add_modal_listner == 'function') add_modal_listner('#ft_cart', 'cart');

	if (typeof add_modal_listner == 'function') add_modal_listner('#ft_header_contacts', 'contacts');

  if (typeof lazyImgObserve == 'function') lazyImgObserve(document);



});



window.addEventListener('resize', function(e) {

	if (typeof menu_holder_height == 'function')  menu_holder_height();

  if (typeof recombinateMenuDebounce == 'function')  recombinateMenuDebounce();

});



window.addEventListener('scroll', function(e) {



	var scrll_btn = document.querySelector('#scrll-on-top');



	if (scrll_btn != null) {

		if ( window.pageYOffset > 200 ) {

			scrll_btn.classList.remove('d-none');

		} else {

			scrll_btn.classList.add('d-none');

		}

	}

});



window.addEventListener("popstate",function(e){



	if ( listened_modal_is_open ) {

		$('.modal').modal('hide');

		listened_modal_is_open = false;

	}



});

