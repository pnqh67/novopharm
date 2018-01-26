jQuery(document).ready(function () { 
	
	jQuery('ul#main-menu li.dropdown').hover(
		function() {
			jQuery(this).addClass('open'); 
		}, 
		function() { 
			jQuery(this).removeClass('open'); 
		}
		); 

	if(jQuery.isFunction(jQuery.fn.slick)) {
		jQuery('.product-image').slick({dots: true, arrows: false, customPaging: 
			function(slick,index) {
				return slick.$slides.eq(index).find('img').prop('outerHTML');
			}});
	}
	jQuery(".banner-owl-carousel").owlCarousel({
		loop:true,
		nav: false,
		dots: true,
		responsiveClass:true,
		autoplay:true,
		autoplayTimeout: 5000,
		items: 1,
		responsive:{
			responsive:{
				0:{
					items:1
				},
				600:{
					items:3
				},
				1000:{
					items:5
				}
			}
		}
	});
	$('.form-order .btn-number').click(function(e) {
		e.preventDefault();

		fieldName = $(this).attr('data-field');
		type = $(this).attr('data-type');
		var input = $("input[name='" + fieldName + "']");
		var currentVal = parseInt(input.val());
		if (!isNaN(currentVal)) {
			if (type == 'minus') {

				if (currentVal > input.attr('min')) {
					input.val(currentVal - 1).change();
				}
				if (parseInt(input.val()) == input.attr('min')) {
					$(this).attr('disabled', true);
				}

			} else if (type == 'plus') {

				if (currentVal < input.attr('max')) {
					input.val(currentVal + 1).change();
				}
				if (parseInt(input.val()) == input.attr('max')) {
					$(this).attr('disabled', true);
				}
			}
		} else {
			input.val(1);

		}
    var total = parseInt($('.form-order .input-number').val()) * parseInt($('.form-order .product_price').val());
    $(".total-amount .text").text(total);
    $('.total_amount').val(total);
	});
	$('.input-number').focusin(function() {
		$(this).data('oldValue', $(this).val());
	});
	$('.input-number').change(function() {

		minValue = parseInt($(this).attr('min'));
		maxValue = parseInt($(this).attr('max'));
		valueCurrent = parseInt($(this).val());

		name = $(this).attr('name');
		if (valueCurrent >= minValue) {
			$(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
		} else {
			alert('Sorry, the minimum value was reached');
			$(this).val($(this).data('oldValue'));
		}
		if (valueCurrent <= maxValue) {
			$(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
		} else {
			alert('Sorry, the maximum value was reached');
			$(this).val($(this).data('oldValue'));
		}
	});
	$(".input-number").keydown(function(e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
        // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
        // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39)) {
        // let it happen, don't do anything
      return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
    	e.preventDefault();
    }
  });
  $(document).ready(function() {
    $('.main-wrapper .section2 .content-s2 .panel .panel-heading .panel-title a.show .fa-plus').css('display','none');
    $('.main-wrapper .section2 .content-s2 .panel .panel-heading .panel-title a.show .fa-minus').css('display','block');
    $('.main-wrapper .section2 .content-s2 .panel .panel-heading .panel-title a').click(function(){
      if($(this).hasClass('show')) {
        $(this).removeClass('show');
        $(this).find('.fa-plus').css('display','block');
        $(this).find('.fa-minus').css('display','none');
      } else {
        $(this).addClass("show");
        $(this).find('.fa-plus').css('display','none');
        $(this).find('.fa-minus').css('display','block');
      }
    });
  });
});
$('.form-contact .btn-send').on('click', function(e) {
	e.preventDefault();
  var form = $(this).closest('.form-contact');
  var data = {};
  data.name = $(form).find('input[name="name"]').val();
  if (!data.name) {
    toastr.error('Vui lòng nhập tên đầy đủ của bạn');
    return;
  }
  data.phone = $(form).find('input[name="phone"]').val();
  console.log(isNaN(data.phone));
  if (!data.phone) {
    toastr.error('Vui lòng nhập số điện thoại');
    return;
  }
  if (isNaN(data.phone)) {
    toastr.error('Vui lòng nhập số điện thoại hợp lệ');
    return;
  }
  data.email = $(form).find('input[name="email"]').val();
  if (!data.email) {
    toastr.error('Vui lòng nhập Email');
    return;
  }
  if (data.email.indexOf("@") == -1) {
    toastr.error('Vui lòng nhập Email hợp lệ');
    return;
  }
  data.content = $(form).find('textarea[name="content"]').val();
  $.ajax({
    type: 'POST',
    url: '/api/contact',
    data: data,
    success: function(json) {
      if (json.code == -3) {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
      else {
        toastr.success('Thông tin liên hệ của quý khách đã được ghi nhận');
        $(form).find('input').each(function(i,e){
          $(e).val('');
        });
        $(form).find('textarea').val('');
      }
    }
  });
});
// form-order
$('.form-order .btn-order').on('click', function(e) {
	e.preventDefault();
  var form = $(this).closest('.form-order');
  var data = {};
  data.name = $(form).find('input[name="name"]').val();
  if (!data.name) {
    toastr.error('Vui lòng nhập tên đầy đủ của bạn');
    return;
  }
  data.phone = $(form).find('input[name="phone"]').val();
  console.log(isNaN(data.phone));
  if (!data.phone) {
    toastr.error('Vui lòng nhập số điện thoại');
    return;
  }
  if (isNaN(data.phone)) {
    toastr.error('Vui lòng nhập số điện thoại hợp lệ');
    return;
  }
  data.email = $(form).find('input[name="email"]').val();
  
  if (data.email.indexOf("@") == -1) {
    toastr.error('Vui lòng nhập Email hợp lệ');
    return;
  }
  data.address = $(form).find('textarea[name="address"]').val();
  if (!data.address) {
    toastr.error('Vui lòng nhập địa chỉ nhận hàng');
    return;
  }
  data.quantity = parseInt($(form).find('input[name="quantity"]').val());
  if(isNaN(data.quantity)){
  	toastr.error('Vui lòng nhập số lượng sản phẩm hợp lệ');
    return;
  }
  data.total = parseInt($(form).find('input[name="total"]').val());
  
  $.ajax({
    type: 'POST',
    url: '/api/order',
    data: data,
    success: function(json) {
      if (json.code == -3) {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
      else {
        toastr.success('Thông tin đặt hàng của quý khách đã được ghi nhận');
        $(form).find('input').each(function(i,e){
          $(e).val('');
        });
        $(form).find('textarea').val('');
      }
    }
  });
});
//diemban-wrapper
$(document).on("click", ".diemban-wrapper .region-item:not(.active)", function(){
  var currentAction = $(this);
  var regionId = $(this).data('id');
  $.ajax({
    type: 'GET',
    url: '/api/location/'+regionId,
    // dataType: "json",
    success: function(json) {
      if (json.code == -3) {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
      else {
        var html = "";
        for(i = 0; i< json.data.length; i++ )
        {
          html += '<li class="col-xs-6">'+
                    '<a href="diem-ban/chi-tiet?subregion='+json.data[i]['id']+'">'+json.data[i]['name']+'</a>'+
                  '</li>';
        }
        currentAction.parents('.panel-body').find('.subregion-wrapper').empty().append(html);
        currentAction.parents('.panel-body').find('.region-item').removeClass('active');
        currentAction.addClass('active');
      }
    }
  });
});