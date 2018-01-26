initDataTable('table');
var modelName = 'location';
var stt = 1;

$(document).on('click','.add-item-loction', function () {
  var list_location = $('.modal .list-location');
  var html = tmpl('item-location',stt++);
  list_location.append(html);
})

$(document).on('click','.btn-remove-item-location', function () {
  var row = $(this).closest('.row');
  row.remove();
})

$('select[name="region"]').on('change', function () {
  var region_id = $(this).val();
  var selectSubregion = $('select[name="subregion"]')
  $.get('/admin/location/getSubregion/' + region_id, function (json) {
    if (!json.code){
      var data  = json.data;
      var html = '';
      data.forEach(function (t, number) {
        var obj = {
          id: t.id,
          name: t.name
        }
        html += tmpl('subregion-option', obj);
      })
      selectSubregion.html('');
      selectSubregion.html(html);
    }
  })
})

$('select[name="subregion"]').on('change', function () {
  var modal = $(this).closest('.modal');
  var id = modal.attr('id');
  if (id != 'modal-update'){
    var list_location = modal.find('.list-location');
    list_location.html('');
  }
})

$(document).on('click', '.btn-create-location', function () {
  var modal = $(this).closest('.modal');
  var list_location = modal.find('.list-location');
  list_location.find('input[name="name"]').each(function () {
    var self = $(this)
    if (!self.val()){
      toastr.error('Chưa nhập tên địa điểm')
      self.addClass('error');
      return;
    }
  })
  var data = {};
  data.region_id = modal.find('select[name="region"]').val()
  data.subregion_id = modal.find('select[name="subregion"]').val()
  data.listLocation = [];
  list_location.find('.row').each(function () {
    var self = $(this);
    var temp = {};
    temp.name = self.find('input[name="name"]').val()
    temp.phone = self.find('input[name="phone"]').val()
    temp.address = self.find('input[name="address"]').val()
    data.listLocation.push(temp);
  })
  $.ajax({
    type: 'POST',
    url: '/admin/location',
    data: data,
    success: function (json) {
      if (!json.code){
        toastr.success('Tạo thành công');
        reloadPage('/admin/location')
        return;
      }
      else toastr.error('Có lỗi xảy ra, vui lòng thử lại')
    }
  })
})

$(document).on('click', '.btn-update-location', function () {
  var modal = $(this).closest('.modal');
  var id = $(this).data('id')
  var data = {}
  data.region_id = modal.find('select[name="region"]').val()
  data.subregion_id = modal.find('select[name="subregion"]').val()
  data.name = modal.find('input[name="name"]').val();
  if (!data.name){
    toastr.error('Tên cửa hàng chưa nhập');
      modal.find('input[name="name"]').addClass('error')
    return;
  }
  data.phone = modal.find('input[name="phone"]').val();
  data.address = modal.find('input[name="address"]').val();
  $.ajax({
    type: 'PUT',
    url: '/admin/location/' + id,
    data: data,
    success: function (json) {
      if (!json.code){
        toastr.success('Sửa thành công');
        reloadPage('/admin/location')
      }
      else toastr.error('Có lỗi xảy ra, vui lòng thử lại')
    }
  })
})

$(document).on('click', '.show-location', function () {
  var id = $(this).data('id');
  var modal = $('#modal-update')
  var selectSubregion = $('select[name="subregion"]')
  $.get('/admin/location/' + id, function (json) {
    if (!json.code){
      var data = json.data;
      modal.find('select[name="region"]').val(data.region_id)
      var html = '';
      json.subregion.forEach(function (t, number) {
        var obj = {
          id: t.id,
          name: t.name
        }
        html += tmpl('subregion-option', obj);
      })
      selectSubregion.html('');
      selectSubregion.html(html);

      modal.find('select[name="subregion"]').val(data.subregion_id)
      modal.find('input[name="name"]').val(data.name)
      modal.find('input[name="phone"]').val(data.phone_number)
      modal.find('input[name="address"]').val(data.address)
      modal.find('.btn-update-location').attr('data-id', data.id);
    }
  })
})