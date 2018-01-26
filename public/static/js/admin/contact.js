initDataTable('table');
var modelName = 'contact';

$(document).on('click','.contact-status',function () {
  var type = $(this).data('value');
  var type_status = '';
  var arrId = [];
  $('tbody input:checkbox:checked').each(function () {
    arrId.push($(this).val());
  })
  if (type == 'reply' || type == 'unreply')
    ajaxUpdateStatus(arrId,'reply',type);
  if (type == 'read')
    ajaxUpdateStatus(arrId,'read',type);
  if (type == 'delete'){
    var numSelected = $('tbody input:checkbox:checked').length;
    popupConfirm('Bạn có muốn xóa ' + numSelected + ' mục đã chọn không?',function (result) {
      if (result) ajaxUpdateStatus(arrId,'display',type);
    })
  }
})

$(document).on('click','.contact-status-detail',function () {
  var type = $(this).data('value');
  var type_status = $(this).data('status');
  var id = $(this).data('id');
  $.ajax({
    type: 'PUT',
    url: '/admin/contact/updateStatus',
    data: {
      arrId: id,
      type_status: type_status,
      status: type
    },
    success: function (json) {
      if (!json.code){
        toastr.success('Thành công')
        if (type == 'delete'){
          reloadPage('/admin/contact')
        }
        if (type == 'reply'){
          $('.contact-title-detail').find('label').remove();
          $('.contact-title-detail').append('<label class="label label-default">Đã phản hồi</label>')
        }
        if (type == 'unreply'){
          $('.contact-title-detail').find('label').remove();
          $('.contact-title-detail').append('<label class="label label-warning">Chưa phản hồi</label>')
        }
      }
      else toastr.error('Có lỗi xảy ra, vui lòng thử lại')
    }
  })
})

function ajaxUpdateStatus(arrId, type_status, type) {
  $.ajax({
    type: 'PUT',
    url: '/admin/contact/updateStatus',
    data: {
      arrId: arrId,
      type_status: type_status,
      status: type
    },
    success: function (json) {
      if (type == 'delete'){
        $('tbody input:checkbox:checked').each(function () {
          $(this).closest('tr').remove();
        })
      }
      if (type == 'read'){
        $('tbody input:checkbox:checked').each(function () {
          $(this).closest('tr').find('td:last-of-type').html('<label class="label label-primary">Đã đọc</label>')
        })
      }
      if (type == 'reply'){
        $('tbody input:checkbox:checked').each(function () {
          $(this).closest('tr').find('td:last-of-type').html('<label class="label label-info">Đã phản hồi</label>')
        })
      }
      if (type == 'unreply'){
        $('tbody input:checkbox:checked').each(function () {
          $(this).closest('tr').find('td:last-of-type').html('<label class="label label-warning">Chưa phản hồi</label>')
        })
      }

    }
  })
}
