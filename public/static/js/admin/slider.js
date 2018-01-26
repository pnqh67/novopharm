initDataTable('table');
initTinymce('#description')
initTinymce('#description_en')
var modelName = 'slider';

$(document).ready(function () {
  if ($('[name="parent_id"]').val() != -1){
    $('.upload').removeAttr('disabled');
  }
  $('select[name="parent_id"]').change(function () {
    if($(this).val()==-1){
      $('.upload').attr("disabled", "disabled");
    }
    else{
      $('.upload').removeAttr('disabled');
    }
  })
})

$('.btn-create').click(function(){
  var data = {};
  data.image = $('input[name="image"]').val();
  if ($('select[name="parent_id"]').val() != -1)
    if(!data.image) {
      toastr.error('Chưa chọn hình ảnh');
      return;
    }
  data.title = $('input[name="title"]').val();
  if(!data.title) {
    toastr.error('Chưa nhập tiêu đề');
    return;
  }
  data.handle = createHandle(data.title);
  data.parent_id = $('select[name="parent_id"]').val();
  data.title_en = $('input[name="title_en"]').val();
  data.description = tinyMCE.get('description').getContent();
  data.description_en = tinyMCE.get('description_en').getContent();
  data.link = $('input[name="link"]').val();
  $.ajax({
    type: 'POST',
    url: '/admin/slider',
    data: data,
    success: function(json) {
      if(!json.code) {
        toastr.success('Thêm thành công');
        reloadPage('/admin/slider');
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.btn-update').click(function(){
  var id = $(this).data('id');
  console.log(id);
  var data = {};
  data.image = $('input[name="image"]').val();
  if ($('select[name="parent_id"]').val() != -1)
    if(!data.image) {
      toastr.error('Chưa chọn hình ảnh');
      return;
    }
  data.title = $('input[name="title"]').val();
  if(!data.title) {
    toastr.error('Chưa nhập tiêu đề');
    return;
  }
  data.handle = createHandle(data.title);
  data.parent_id = $('select[name="parent_id"]').val();
  data.title_en = $('input[name="title_en"]').val();
  data.description = tinyMCE.get('description').getContent();
  data.description_en = tinyMCE.get('description_en').getContent();
  data.link = $('input[name="link"]').val();
  console.log(data);
  $.ajax({
    type: 'PUT',
    url: '/admin/slider/' + id,
    data: data,
    success: function(json) {
      if(!json.code) {
        toastr.success('Cập nhật thành công');
        reloadPage('/admin/slider');
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$(document).on('change', '.upload', function(){
  if(checkExtImage($(this).val())) {
    var form = $(this).closest('form');
  	var formData = new FormData(form[0]);
    $.ajax({
      type: 'POST',
      url: '/admin/api/uploadImage',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: function(json) {
  			if(!json.code) {
  				var image = json.data[0];
  				var resize = resizeImage(image, 240);
          var timestamp = new Date() - 0;
  				form.find('img').attr('src', '/uploads/' + resize + '?v=' + timestamp);
  				form.find('input[name="image"]').val(image);
  			} else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});

$('.modal').on('hide.bs.modal', function(){
  $(this).find('input').val('');
  $(this).find('.form-upload-image').find('img').attr('src', '/static/img/default_image.png');
});
