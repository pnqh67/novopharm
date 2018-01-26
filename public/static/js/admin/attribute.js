initDataTable('table');

$('#modal-add').on('click', '.btn-create', function() {
  var self = $(this);
  var modal = $('#modal-add');
  var data = {};
  data.name = modal.find('input[name="name"]').val();
  if (!data.name) {
    toastr.error("Chưa nhập tiêu đề");
    modal.find('input[name="name"]').addClass('error');
    return;
  }
  data.name_en = modal.find('input[name="name_en"]').val();
  self.addClass('disabled');
  $.ajax({
      type: 'POST',
      url: '/admin/attribute',
      data: data,
      success: function(json) {
          self.removeClass('disabled');
          if(!json.code) {
              toastr.success('Thêm thành công');
              reloadPage();
          } else if (json.code == -1) toastr.error("Thuộc tính đã tồn tại");
          else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
  });
});

$('.edit-attribute').click(function() {
  var id = $(this).data('id');
  $.ajax({
    type : 'GET',
    url : '/admin/attribute/' + id,
    success : function(json) {
      if (!json.code) {
        var modal = $('#modal-edit');
        modal.find('input[name="name"]').val(json.data.name);
        modal.find('input[name="name_en"]').val(json.data.name_en);
        modal.find('.btn-update').attr('data-id', json.data.id);
        $('#modal-edit').modal('show');
      } else toastr.error('Không tìm thấy');
    }
  })
});

$('#modal-edit').on('click', '.btn-update', function() {
  var self = $(this);
  var modal = $('#modal-edit');
  var id = $(this).attr('data-id');
  var data = {};
  data.name = modal.find('input[name="name"]').val();
  if (!data.name) {
    toastr.error("Chưa nhập tiêu đề");
    modal.find('input[name="name"]').addClass('error');
    return;
  }
  data.name_en = modal.find('input[name="name_en"]').val();
  self.addClass('disabled');
  $.ajax({
      type: 'PUT',
      url: '/admin/attribute/' + id,
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

$('.add-new-option').click(function() {
  var group = $(this).closest('.group-add-new-option');
  var data = {};
  data.parent_id = $(this).data('parent_id');
  data.name = group.find('input[name="name"]').val();
  if (!data.name) {
    toastr.error("Chưa nhập tiêu đề");
    group.find('input[name="name"]').addClass('error');
    return;
  }
  data.name_en = group.find('input[name="name_en"]').val();
  data.option = group.find('input[name="option"]').val();
  data.option_en = group.find('input[name="option_en"]').val();
  $.ajax({
      type: 'POST',
      url: '/admin/attribute',
      data: data,
      success: function(json) {
          if(!json.code) {
              toastr.success('Thêm thành công');
              reloadPage();
          } else if (json.code == -1) toastr.error("Option đã tồn tại");
          else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
  });
});

$('.edit-option').click(function() {
  var id = $(this).data('id');
  $.ajax({
    type: 'GET',
    url: '/admin/attribute/' + id,
    success: function(json) {
      if (!json.code) {
        var modal = $('#modal-edit-option');
        modal.find('input[name="name"]').val(json.data.name);
        modal.find('input[name="name_en"]').val(json.data.name_en);
        modal.find('input[name="option"]').val(json.data.option);
        modal.find('input[name="option_en"]').val(json.data.option_en);
        modal.find('.btn-update').attr('data-id', json.data.id);
        $('#modal-edit-option').modal('show');
      } else toastr.error('Không tìm thấy');
    }
  })
});

$('#modal-edit-option').on('click', '.btn-update', function() {
  var self = $(this);
  var modal = $('#modal-edit-option');
  var id = $(this).attr('data-id');
  var data = {};
  data.name = modal.find('input[name="name"]').val();
  if (!data.name) {
    toastr.error("Chưa nhập tiêu đề");
    modal.find('input[name="name"]').addClass('error');
    return;
  }
  data.name_en = modal.find('input[name="name_en"]').val();
  data.option = modal.find('input[name="option"]').val();
  data.option_en = modal.find('input[name="option_en"]').val();

  self.addClass('disabled');
  $.ajax({
      type: 'PUT',
      url: '/admin/attribute/' + id,
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


$('.delete-attribute').click(function() {
  var card = $(this).closest('.attribute-card');
  if ($(this).data('option')) card = $(this).closest('.clearfix');
  var id = $(this).data('id');
  popupConfirm("Xóa thuộc tính?", function(result) {
    if (result) {
      $.ajax({
        type: 'DELETE',
        url: '/admin/attribute/' + id,
        success: function(json) {
          if (!json.code) {
            toastr.success('Xóa thành công');
            card.remove();
            reloadPage();
          } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        }
      })
    }
  });
})

$('.delete-option').click(function() {
  var id = $(this).data('id');
  var card = $(this).closest('.clearfix');
  popupConfirm("Xóa thuộc tính?", function(result) {
    if (result) {
      $.ajax({
        type: 'DELETE',
        url: '/admin/attribute/' + id,
        success: function(json) {
          if (!json.code) {
            toastr.success('Xóa thành công');
            card.remove();
            reloadPage();
          } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        }
      })
    }
  });
})
