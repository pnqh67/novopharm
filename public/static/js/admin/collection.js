initTinymce('#content');
initTinymce('#content_en');
initDataTable('table');
var modelName = 'collection'

$('.btn-create-update').click(function() {
  var id = $(this).data('id');
  var self = $(this);
  $('input').removeClass('error');
  var data = {};
  data.parent_id = $('select[name="parent_id"]').val();
  data.title = $('input[name="title"]').val();
  data.title_en = $('input[name="title_en"]').val();
  data.handle = $('input[name="handle"]').val();
  data.handle_en = $('input[name="handle_en"]').val();
  data.description = $('textarea[name="description"]').val();
	data.description_en = $('textarea[name="description_en"]').val();
  data.content = tinyMCE.get('content').getContent();
  data.content_en = tinyMCE.get('content_en').getContent();
  data.image = $('input[name="image"]').val();
  data.status = $('select[name="status"]').val();

  if(!data.title) {
    toastr.error('Chưa nhập tiêu đề');
    $('input[name="title"]').addClass('error');
    return;
  }
  if(!data.handle) {
    toastr.error('Chưa nhập đường dẫn');
    $('input[name="handle"]').parent().addClass('error');
    return;
  }

  self.addClass('disabled');
  if (id) updateCollection(id, data);
  else createCollection(data);
});

function createCollection(data) {
  $.ajax({
    type: 'POST',
    url: '/admin/collection',
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Tạo thành công');
        createOrUpdateSEO('collection', json.id);
        reloadPage('/admin/collection/' + json.id);
      } else if (json.code == -1) {
        toastr.error('Nhóm sản phẩm đã tồn tại');
      } else if (json.code == -4) {
        toastr.error(json.message);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

function updateCollection(id, data) {
  $.ajax({
    type: 'PUT',
    url: '/admin/collection/' + id,
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        createOrUpdateSEO('collection', id);
        toastr.success('Cập nhật thành công');
        reloadPage();
      } else if (json.code == -1) {
        toastr.error('Nhóm sản phẩm đã tồn tại');
      } else if (json.code == -4) {
        toastr.error(json.message);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}


$(document).on('click', '.btn-remove', function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  popupConfirm("Xóa nhóm sản phẩm?", function(result) {
    if (result) {
      $.ajax({
        type: 'DELETE',
        url: '/admin/collection/' + id,
        success: function(json) {
          if(!json.code) {
            toastr.success('Xóa thành công');
            tbl.row(tr).remove().draw();
          } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        }
      });
    }
  });
});

function changeTitle(element) {
  var title = element.val();
  var id = '';
  var parent_id = '';
  if (element.data('id')) id = element.data('id');
  if ($('select[name="parent_id"]').val()) parent_id = $('select[name="parent_id"]').val();
  if (title) {
    var tab = element.closest('.tab-pane');
    var handle = createHandle(title);
    $.get('/admin/api/checkHandle?handle=' + handle + '&type=collection&id='+id + '&parent_id=' + parent_id, function(result) {
      tab.find('.handle').val(result);
    });
  }
}

$(document).on('change', '.title', function() {
  changeTitle($(this));
});

$(document).on('change', 'select[name="parent_id"]', function() {
  $(document).find('.tab-pane .title').each(function() {
    changeTitle($(this));
  });
});

$(document).on('change', '.handle', function() {
  $(document).find('.tab-pane .title').each(function() {
    var input = $(this);
  });
  var handle = $(this).val();
  var id = '';
  if ($(this).data('id')) id = $(this).data('id');
  $.get('/admin/api/checkHandle?handle=' + handle + '&type=collection&id=' + id, function(result) {
    input.val(result);
  });
});

$(document).on('click','.sort-product',function () {
  var self = $(this);
  var li = self.closest('li');
  var id = self.data('id');
  $.ajax({
    type: 'POST',
    url: '/admin/collection/sortProduct/' + id,
    success: function (code) {
      if (!code){
        toastr.success('Sắp xếp thành công');
        li.prependTo(self.closest('ul'));
        li.find('a').addClass('red');
        setTimeout(function () {
          li.find('a').removeClass('red');
        },3000)
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  })
})

$(document).on('click','.btn-remove-product-icon',function () {
  var self = $(this);
  var li = self.closest('li');
  var product_id = self.data('id');
  var productName = li.find('a').text();
  var collection_id = $('.btn-create-update').data('id');
  popupConfirm('Bạn có muốn xóa sản phẩm ' + productName + ' khỏi nhóm sản phẩm không?',function (result) {
    if (result){
      $.ajax({
        type: 'DELETE',
        url: '/admin/collection/removeProduct',
        data:{
          'product_id' : product_id,
          'collection_id' : collection_id
        },
        success: function (code) {
          if (!code){
            toastr.success('Xóa sản phẩm ' + productName + ' khỏi nhóm thành công');
            li.remove();
          } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        }
      })
    }
  })
})