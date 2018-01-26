initTinymce('#content');
initTinymce('#content_en');
initDataTable('table');
var modelName = 'blog';

$(document).ready(function () {
  $('select[name="type"]').on('change', function () {
    var value = $(this).val();
    if (value == 'article'){
      $('.box-respiratory').removeClass('hidden');
    }
    else{
      $('.box-respiratory').addClass('hidden');
    }
  })
})

$('.btn-create-update').click(function() {
  $(document).find('.error').removeClass('error');
  var data = {};
  data.title = $('input[name="title"]').val();
  data.title_en = $('input[name="title_en"]').val();
  data.handle = $('input[name="handle"]').val();
  data.handle_en = $('input[name="handle_en"]').val();
  data.image = $('input[name="image"]').val();
  data.description = $('textarea[name="description"]').val();
  data.description_en = $('textarea[name="description_en"]').val();
  data.content = tinyMCE.get('content').getContent();
  data.content_en = tinyMCE.get('content_en').getContent();
  data.status = $('select[name="status"]').val();
  data.type = $('select[name="type"]').val();
  data.type_blog = $('select[name="type_blog"]').val();

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
  $(this).addClass('disabled');

  var id = $(this).data('id');
  if (id) updateBlog(id, data);
  else createBlog(data);
});

function createBlog(data) {
  $.ajax({
    type: 'POST',
    url: '/admin/blog',
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Tạo thành công');
        createOrUpdateSEO('blog', json.id);
        reloadPage('/admin/blog/' + json.id);
      } else if (json.code == -1) {
        toastr.error('Nhóm bài viết đã tồn tại');
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

function updateBlog(id, data) {
  $.ajax({
    type: 'PUT',
    url: '/admin/blog/' + id,
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Cập nhật thành công');
        createOrUpdateSEO('blog', id);
        reloadPage();
      } else if (json.code == -1) {
        toastr.error('Nhóm bài viết đã tồn tại');
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

$(document).on('click', '.btn-remove', function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  popupConfirm('Xóa nhóm bài viết', function (result) {
    if (result) {
      $.ajax({
  			type: 'DELETE',
  			url: '/admin/blog/' + id,
  			success: function(json) {
  				if(!json.code) {
            toastr.success('Xóa blog thành công');
            tbl.row(tr).remove().draw();
          } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
  			}
  		});
    }
  });
});

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

