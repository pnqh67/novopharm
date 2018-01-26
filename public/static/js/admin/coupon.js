initTinymce('#description')
initTinymce('#description_en')
initDataTable('table');
var modelName = 'coupon';

$(document).ready(function () {
  $('select[name="type"]').on('change',function () {
    var self = $(this);
    var value = self.val();
    if (value=='percent'){
      $('.max-value-percent').removeClass('hidden');
    }
    else{
      $('.max-value-percent').addClass('hidden');
    }
  })
})

$('.btn-create-update').click(function() {
  $(document).find('.error').removeClass('error');
  var data = {};
  data.title = $('input[name="title"]').val();
  data.title_en = $('input[name="title_en"]').val();
  data.code = $('input[name="code"]').val();
  data.type = $('select[name="type"]').val();
  data.value = $('input[name="value"]').val();
  data.min_value_order = $('input[name="min_value_order"]').val();
  data.usage_left = $('input[name="usage_left"]').val();
  data.description = tinyMCE.get('description').getContent();
  data.description_en = tinyMCE.get('description_en').getContent();
  data.start_date = $('input[name="start_date"]').val();
  data.end_date = $('input[name="end_date"]').val();
  data.status = $('select[name="status"]').val();
  if(!data.title) {
    toastr.error('Chưa nhập tiêu đề');
    $('input[name="title"]').addClass('error');
    return false;
  }
  if(!data.min_value_order) {
    toastr.error('Chưa nhập giá trị đơn hàng tối thiểu');
    $('input[name="min_value_order"]').addClass('error');
    return false;
  }
  if(!data.start_date) {
    toastr.error('Chưa nhập ngày bắt đầu');
    $('input[name="start_date"]').addClass('error');
    return false;
  }
  if(!data.end_date) {
    toastr.error('Chưa nhập ngày kết thúc');
    $('input[name="end_date"]').addClass('error');
    return false;
  }
  if (data.type == 'percent'){
    if (!$('input[name="max_value_percent"]').val()){
      toastr.error('Chưa nhập giá trị tối đa');
      $('input[name="max_value_percent"]').addClass('error');
      return false;
    }
    else{
      data.max_value_percent = $('input[name="max_value_percent"]').val();
    }
  }
  data.start_date = dmy2ymd(data.start_date);
  data.end_date = dmy2ymd(data.end_date);
  if (new Date(data.end_date) < new Date(data.start_date)) {
    toastr.error('Ngày kết thúc không được bé hơn ngày bắt đầu');
    $('input[name="end_date"]').addClass('error');
    return false;
  }
  if(!data.code) {
    toastr.error('Chưa nhập mã');
    $('input[name="code"]').addClass('error');
    return false;
  }
  if(!data.usage_left) {
    toastr.error('Chưa nhập số lượng mã');
    $('input[name="usage_left"]').addClass('error');
    return false;
  }
  if(!data.value) {
    toastr.error('Chưa nhập giá trị');
    $('input[name="value"]').addClass('error');
    return false;
  }

  $(this).addClass('disabled');
  var id = $(this).data('id');
  if (id) updateCoupon(id, data);
  else createCoupon(data);
});

function createCoupon(data) {
  $.ajax({
    type: 'POST',
    url: '/admin/coupon',
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Tạo mã giảm giá thành công');
        reloadPage('/admin/coupon/' + json.id);
      } else if(json.code == -1) toastr.error('Coupon đã tồn tại');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

function updateCoupon(id, data) {
  $.ajax({
    type: 'PUT',
    url: '/admin/coupon/' + id,
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Cập nhật thành công');
        reloadPage();
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

$(document).on('click', '.btn-remove-coupon', function() {
    var id = $(this).data('id');
    var tr = $(this).closest('tr');
    popupConfirm('Xóa mã giảm giá',function (result) {
        if (result){
            $.ajax({
                type: 'DELETE',
                url: '/admin/coupon/' + id,
                success: function(json) {
                    if(!json.code) {
                        toastr.success('Xóa mã giảm giá thành công');
                        tbl.row(tr).remove().draw();
                    } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
                }
            });
        }
    })
})
