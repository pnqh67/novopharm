initTinymce('#description');
initTinymce('#description_en');
initDataTable('table');
var addProduct = '';
var modelName = 'sale';

$(".chosen-select-product").chosen({no_results_text: "Không tìm thấy sản phẩm"});
$(".chosen-select").chosen({width: "100%"});

$(document).on('click', '.btn-remove-product', function () {
    $(this).closest('.row').remove();
})

$('.btn-add-product').click(function () {
    if (addProduct == '') {
      $.ajax({
          type: "POST",
          url: "/admin/sale/getproduct",
          success: function (json) {
              if (!json.code) {
                  var html = tmpl('sale-item-product-content', json);
                  var option = '';
                  json.id.forEach(function (element, index) {
                      var obj = {
                          id: element.id,
                          title: element.title
                      };
                      option += tmpl('sale-item-product-option', obj);
                  })
                  addProduct = html.substring(0, html.indexOf('</select>')) + option + html.substring(html.indexOf('</select>'), html.length);
                  $('.list-product-content').append(addProduct);
                  $(".chosen-select-product").chosen({no_results_text: "Không tìm thấy sản phẩm"});
              }
          }
      })
    }
    else{
      $('.list-product-content').append(addProduct);
      $(".chosen-select-product").chosen({no_results_text: "Không tìm thấy sản phẩm"});
    }
})

$('.btn-create-update').click(function () {
  var id = $(this).data('id');
  var data = {};
  data.title = $('input[name="title"]').val();
  data.title_en = $('input[name="title_en"]').val();
  var product = [];
  $('.chosen-select-product').each(function (t, number) {
    product.push($(this).val());
  })
  data.products = product;
  data.description = tinyMCE.get('description').getContent();
  data.description_en = tinyMCE.get('description_en').getContent();
  data.value = $('input[name="value"]').val();
  data.type = $('select[name="type"]').val();
  data.start_date = $('input[name="start_date"]').val();
  data.end_date = $('input[name="end_date"]').val();
  data.status = $('select[name="status"]').val();
  if (!data.title) {
    toastr.error('Chưa nhập tiêu đề');
    $('input[name="title"]').addClass('error');
    return;
  }
  if (!data.value) {
    toastr.error("Chưa nhập giá trị");
    $('input[name="value"]').addClass('error');
    return;
  }
  data.start_date = dmy2ymd(data.start_date);
  data.end_date = dmy2ymd(data.end_date);
  if (new Date(data.end_date) < new Date(data.start_date)) {
    toastr.error('Ngày kết thúc không được bé hơn ngày bắt đầu');
    $('input[name="end_date"]').addClass('error');
    return false;
  }
  $(this).addClass('disabled');
  if (id) updateSale(id,data);
  else createSale(data);
})

function createSale(data) {
  $.ajax({
    type: 'POST',
    url: '/admin/sale',
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Tạo thành công');
        reloadPage('/admin/sale/' + json.id);
      } else if (json.code == -1) {
        toastr.error('Chương trình khuyến mãi đã tồn tại');
      } else if (json.code == -4) {
        toastr.error(json.message);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

function updateSale(id, data) {
  $.ajax({
    type: 'PUT',
    url: '/admin/sale/' + id,
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Sửa thành công');
        reloadPage();
      } else if (json.code == -1) {
        toastr.error('Chương trình khuyến mãi đã tồn tại');
      } else if (json.code == -4) {
        toastr.error(json.message);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

$('.btn-remove').click(function () {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  popupConfirm("Xóa chương trình khuyến mãi",function (result) {
    if(result){
      $.ajax({
        type: 'DELETE',
        url: '/admin/sale/' + id,
        success: function (json) {
          if(!json.code) {
            toastr.success('Xóa chương trình khuyến mãi thành công');
            tbl.row(tr).remove().draw();
          } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        }
      });
    }
  })
})
