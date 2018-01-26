initDataTable('table');

$('.btn-create-update').click(function() {
  var id = $(this).data('id');
  $('input').removeClass('error');
  var data = {};
  data.role_id = $('select[name="role_id"]').val();
  data.name = $('input[name="name"]').val();
  data.phone = $('input[name="phone"]').val();
  data.email = $('input[name="email"]').val();

  if(!data.name) {
    toastr.error('Chưa nhập họ tên');
    $('input[name="name"]').addClass('error');
    return;
  }
  if(!data.email) {
    toastr.error('Chưa nhập email');
    $('input[name="email"]').addClass('error');
    return;
  }

  $(this).addClass('disabled');
  if (id) updateUser(id, data);
  else createUser(data);
});

function createUser(data) {
  $.ajax({
    type: 'POST',
    url: '/admin/user',
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Tạo thành công');
        reloadPage('/admin/user/' + json.id);
      } else if (json.code == -1) {
        toastr.error('Quản trị viên đã tồn tại');
      } else if (json.code == -4) {
        toastr.error(json.message);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

function updateUser(id, data) {
  $.ajax({
    type: 'PUT',
    url: '/admin/user/' + id,
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Cập nhật thành công');
        reloadPage();
      } else if (json.code == -1) {
        toastr.error('Quản trị viên đã tồn tại');
      } else if (json.code == -4) {
        toastr.error(json.message);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}


$(document).on('click', '.btn-remove', function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  popupConfirm("Xóa quản trị viên?", function(result) {
    if (result) {
      $.ajax({
        type: 'DELETE',
        url: '/admin/user/' + id,
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
