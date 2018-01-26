$('.btn-update-order').click(function() {
  var id = $(this).data('id');
  var self = $(this);
  var data = {};
  data.order_status = $('select[name="order_status"]').val();
  data.payment_status = $('select[name="payment_status"]').val();
  data.shipping_status = $('select[name="shipping_status"]').val();
  data.reason_cancel = $('textarea[name="reason_cancel"]').val();
  self.addClass('disabled');
  $.ajax({
    type: 'PUT',
    url: '/admin/order/' + id,
    data: data,
    success: function(json) {
      self.removeClass('disabled');
      if(!json.code) {
        toastr.success('Cập nhật thành công');
        reloadPage();
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('select[name="order_status"]').change(function() {
  var status = $(this).val();
  $('.reason_cancel').addClass('hidden');
  if ((status == 'cancel') || (status == 'return')) {
    $('.reason_cancel').removeClass('hidden');
  }
});
