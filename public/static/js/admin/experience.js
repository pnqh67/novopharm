initDataTable('table');
var modelName = 'experience';

$('.btn-create-update').click(function(event) {
  $(document).find('.error').removeClass('error');
  var data = {};
  data.title = $('input[name="title"]').val();
  data.title_en = $('input[name="title_en"]').val();
  data.handle = $('input[name="handle"]').val();
  data.handle_en = $('input[name="handle_en"]').val();
  data.description = $('textarea[name="description"]').val();
  data.description_en = $('textarea[name="description_en"]').val();
  data.blog_id = $('select[name="blog_id"]').val();
  data.link = $('input[name="link"]').val();
  data.status = $('select[name="status"]').val();
  data.image = $('input[name="image"]').val();

  if(!data.title.trim().length) {
    toastr.error('Chưa nhập tiêu đề');
    $('input[name="title"]').addClass('error');
    return;
  }
  if(!data.handle) {
    toastr.error('Chưa nhập đường dẫn');
    $('input[name="handle"]').parent().addClass('error');
    return;
  }
  if(!data.link) {
    toastr.error('Chưa nhập đường dẫn youtube');
    $('input[name="link"]').addClass('error');
    return;
  }

  $(this).addClass('disabled');
  var id = $(this).data('id');
  if (id) updateExperience(id, data);
  else createExperience(data);
});

function createExperience(data) {
  $.ajax({
    type: 'POST',
    url: '/admin/experience',
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Tạo thành công');
        createOrUpdateSEO('experience', json.id);
        reloadPage('/admin/experience/' + json.id);
      } else if (json.code == -1) {
        toastr.error('Kinh nghiệm đã tồn tại');
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

function updateExperience(id, data) {
  $.ajax({
    type: 'PUT',
    url: '/admin/experience/' + id,
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Cập nhật thành công');
        createOrUpdateSEO('experience', id);
        reloadPage();
      } else if (json.code == -1) {
        toastr.error('Kinh nghiệm đã tồn tại');
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

$(document).on('click', '.btn-remove', function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  popupConfirm('Xóa kinh nghiệm', function (result) {
      if (result) {
          $.ajax({
              type: 'DELETE',
              url: '/admin/experience/' + id,
              success: function(json) {
                  if(!json.code) {
                      toastr.success('Xóa bài viết thành công');
                      tbl.row(tr).remove().draw();
                  } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
              }
          });
      }
  })
})


$(document).on('change', '.title', function() {
  var id = '';
  if ($(this).data('id')) id = $(this).data('id');
  var title = $(this).val();
  var handle = createHandle(title);
  var tab = $(this).closest('.tab-pane');
  tab.find('.handle').val(handle);
  $.get('/admin/api/checkHandle?handle=' + handle + '&type=blog&id=' + id, function(result) {
    tab.find('.handle').val(result);
  });
});

$(document).on('change', '.handle', function() {
  var input = $(this);
  var handle = $(this).val();
  var id = '';
  if ($(this).data('id')) id = $(this).data('id');
  $.get('/admin/api/checkHandle?handle=' + handle + '&type=blog&id=' + id, function(result) {
    input.val(result);
  });
});
