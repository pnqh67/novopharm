initTinymce('#content');
initTinymce('#content_en');
initDataTable('.table-product');
var modelName = 'product';

$(".chosen-select").chosen({width: "100%"});

$('select[name="inventory-management"]').on('change', disableInventory);

$(window).on('load', function() {
  disableInventory();
});

$(document).on('click','.btn-remove-product-buy-together',function () {
    var row = $(this).closest('.row')
    var titleProduct = $(this).closest('.row').find('.pull-left a').text();
    var id = $(this).data('id');
    popupConfirm('Bạn muốn xóa ' + titleProduct + ' mua kèm không ?',function (result) {
        if (result){
          $.ajax({
              type: 'DELETE',
              url: '/admin/product_buy_together/' + id,
              success: function (json) {
                  row.remove();
              }
          })
        }
    })
})

$('.btn-create-update').click(function() {
  $(document).find('.error').removeClass('error');
  var self = $(this);
  var data = {};
  data.title = $('input[name="title"]').val();
  if (!data.title) {
    toastr.error("Chưa nhập tiêu đề");
    $('input[name="title"]').addClass('error');
    return;
  }
  data.title_en = $('input[name="title_en"]').val();
  data.handle = $('input[name="handle"]').val();
  if (!data.handle) {
    toastr.error("Chưa nhập đường dẫn");
    $('input[name="handle"]').addClass('error');
    return;
  }
  data.handle_en = $('input[name="handle_en"]').val();

  data.description = $('textarea[name="description"]').val();
  data.description_en = $('textarea[name="description_en"]').val();

  data.content = tinyMCE.get('content').getContent();
  data.content_en = tinyMCE.get('content_en').getContent();

  data.inventory_management = parseInt($('select[name="inventory-management"]').val());

  var check = true;
  // $('.list-variant').find('.variant-item').each(function(index, elem) {
  //   var variant = $('.list-variant').find('.variant-item').eq(index);
  //   var variant_title = variant.find('input[name="variant-title"]').val();
  //   var variant_price = variant.find('input[name="variant-price"]').val();
  //   if (!variant_title || !variant_price) {
  //     toastr.error("Tên và giá của phiên bản không được trống");
  //     variant.addClass('error');
  //     check = false;
  //     return false;
  //   }
  // });

  if (!$('.list-all-variant>.row').length || !$('.list-all-variant .check-variant:checked').length) check = false

  if (!check){
    toastr.error("Phải tạo ít nhất một phiên bản");
    return;
  }

  data.arrOption = [];
  (function () {
    var addAttribute = $('.add-attribute>.row');
    addAttribute.each(function (index) {
      var self = $(this);
      if (self.find('.chosen-select').val().length){
        data.arrOption[index] = self.find('.select-attribute').val();
      }
      else return false;
    })
  }())

  data.featured_image = $('.featured-image').val();

  data.collections = $('.chosen-collection').val();
  data.tags = $("input[name='tags']").tagsinput('items');
  data.status = $('select[name="status"]').val();
  self.addClass('disabled');
  var id = $(this).data('id');
  if (id) updateProduct(id, data);
  else createProduct(data);

});

function createProduct(data) {
  $.ajax({
    type: 'POST',
    url: '/admin/product',
    data: data,
    success: function(json) {
      if (json.code == -1) {
        toastr.error("Sản phẩm đã tồn tại");
        $(document).find('.btn-create-update').removeClass('disabled');
      } else if (json.code == -3) {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        $(document).find('.btn-create-update').removeClass('disabled');
      } else if (json.code == -4) {
        toastr.error(json.message);
        $(document).find('.btn-create-update').removeClass('disabled');
      } else {
        var product_id = json.id;
        createOrUpdateSEO('product', product_id);

        var list_variant = $('.list-all-variant>.row');
        var count = 0;
        createVariant();
        function createVariant() {
          if (count == list_variant.length) {
            $(document).find('.btn-create-update').removeClass('disabled');
            updateProductStock(product_id);
            toastr.success("Tạo sản phẩm thành công");
            reloadPage('/admin/product/' + product_id);
            return false;
          }
          var obj = {};
          var variant = list_variant.eq(count);
          if (!variant.find('.check-variant:checked').length){
            count++;
            createVariant();
          }
          else{
            obj.product_id = product_id;
            obj.option = variant.find('.control-label').text();
            obj.price = variant.find('input[name="variant-price"]').val();
            obj.price_compare = variant.find('input[name="variant-price-compare"]').val();
            obj.inventory = variant.find('input[name="variant-inventory"]').val();
            var inventory_management = parseInt($('select[name="inventory-management"]').val());
            if (!inventory_management && !obj.inventory) obj.inventory = 1;
            var imgs = [];
            variant.find('.list-image .image').each(function() {
              imgs.push($(this).attr('data-name'));
            });
            obj.list_image = imgs;
            $.ajax({
              type: 'POST',
              url: '/admin/variant',
              data: obj,
              success: function(json) {
                if(!json.code) {
                  count++;
                  createVariant();
                } else toastr.error(json.message);
              }
            });
          }
        }

        (function (product_id) { //create product buy together
          var data = [];
          var product_id = product_id;
          $('.add-product-buy-together').find('.row').each(function (index, value) {
            var self = $(this);
            var temp = {};
            temp.id = self.data('product_buy_together')
            temp.title = self.data('title')
            temp.price_sale = self.data('price_sale')
            temp.promotion = self.data('promotion')
            temp.status = self.data('status')
            data.push(temp);
          })
          $.ajax({
            type: 'POST',
            url: '/admin/product_buy_together',
            data: {
              data: data,
              product_id: product_id
            },
            success: function (json) {

            }
          })
        }(product_id))
      }
    }
  });
}

function updateProduct(id, data) {
  $.ajax({
    type: 'PUT',
    url: '/admin/product/' + id,
    data: data,
    success: function(json) {
      if (json.code == -1) {
        toastr.error("Sản phẩm đã tồn tại");
        $(document).find('.btn-create-update').removeClass('disabled');
      } else if (json.code == -3) {
        toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        $(document).find('.btn-create-update').removeClass('disabled');
      } else if (json.code == -4) {
        toastr.error(json.message);
        $(document).find('.btn-create-update').removeClass('disabled');
      } else {
        var product_id = id;
        if (list_variant_deleted.length) {
          list_variant_deleted.forEach(function(elem, index) {
            deleteVariant(elem);
          });
        }
        createOrUpdateSEO('product', product_id);

        var list_variant = $('.variant-item');
        var count = 0;
        createVariant();
        function createVariant() {
          if (count == list_variant.length) {
            $(document).find('.btn-create-update').removeClass('disabled');
            updateProductStock(product_id);
            toastr.success("Cập nhật sản phẩm thành công");
            reloadPage();
            return false;
          }
          var obj = {};
          var variant = list_variant.eq(count);
          var variant_id = variant.data('id');
          obj.product_id = product_id;
          obj.title = variant.find('input[name="variant-title"]').val();
          obj.title_en = variant.find('input[name="variant-title-en"]').val();
          obj.price = variant.find('input[name="variant-price"]').val();
          obj.price_compare = variant.find('input[name="variant-price-compare"]').val();
          obj.inventory = variant.find('input[name="variant-inventory"]').val();
          var inventory_management = parseInt($('select[name="inventory-management"]').val());
          if (!inventory_management && !obj.inventory) obj.inventory = 1;
          var imgs = [];
          variant.find('.list-image .image').each(function() {
            imgs.push($(this).attr('data-name'));
          });
          obj.list_image = imgs;
          if (variant_id) {
            $.ajax({
              type: 'PUT',
              url: '/admin/variant/' + variant_id,
              data: obj,
              success: function(json) {
                if(!json.code) {
                  count++;
                  createVariant();
                }
              }
            });
          } else {
            $.ajax({
              type: 'POST',
              url: '/admin/variant',
              data: obj,
              success: function(json) {
                if(!json.code) {
                  count++;
                  createVariant();
                }
              }
            });
          }
        }

        (function (product_id) { //update product buy together
          var data = [];
          var product_id = product_id;
          $('.add-product-buy-together').find('.row').each(function (index, value) {
            var self = $(this);
            var temp = {};
            temp.id = self.data('product_buy_together')
            temp.title = self.data('title')
            temp.price_sale = self.data('price_sale')
            temp.promotion = self.data('promotion')
            temp.status = self.data('status')
            data.push(temp);
          })
          $.ajax({
            type: 'POST',
            url: '/admin/product_buy_together',
            data: {
              data: data,
              product_id: product_id
            },
            success: function (json) {

            }
          })
        }(product_id))

      }
    }
  });
}

$('.btn-add-variant').click(function() {
  var variant = tmpl("add-variant");
  $('.list-variant').append(variant);
  disableInventory();
});

$(document).on('click', '.btn-rotate-image', function(e){
  e.stopPropagation();
  var img = $(this).parent().find('img');
  var src = $(img).attr('src');
  src = src.replace('_240', '');
  var filename = src.split('/').pop();
  filename = filename.split('?')[0];
  $.get('/admin/api/rotate?filename=' + filename, function(res) {
    var timestamp = new Date() - 0;
    $(img).attr('src', '/uploads/' + resizeImage(filename, 240) + '?v=' + timestamp);
  });
});

$(document).on('click', '.btn-remove-product', function() {
  var id = $(this).attr('data-id');
  var tr = $(this).closest('tr');
  if(confirm("Xóa sản phẩm " + tr.find('td:first-child a').html() + " ?")) {
    $.ajax({
      type: 'DELETE',
      url: '/admin/product/' + id,
      success: function(json) {
        if(!json.code) {
          toastr.success('Xóa sản phẩm '+tr.find('td:first-child a').html()+' thành công');
          tblProduct.row(tr).remove().draw();
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});

$(document).on('click','.btn-remove-collection',function () {
    var data = [];
    var collectionId = [];
    var productId = [];
    $('#modal-remove-collection').find('.modal-body :checked').each(function () {
        collectionId.push($(this).val());
    })
    if (!collectionId){
      toastr.error('Vui lòng chọn ít nhất một danh mục cần xóa!');
      return;
    }
    $('table tbody :checked').each(function () {
        productId.push($(this).val());
    })
    data['collectionId'] = collectionId;
    data['productId'] = productId;
    $.ajax({
        type: 'POST',
        url: '',
        data: data,
        success: function (json) {
            if (!json.code){
              $('#modal-remove-collection').modal('hide');
            }
        }
    })
})

var list_variant_deleted = [];

$(document).on('click', '.btn-remove-variant', function() {
  var itemVariant = $(this).closest('tr');
  var variant_id = $(this).attr('data-id');
  if(variant_id) deleteVariant(variant_id, itemVariant);
});

function deleteVariant(variant_id, row) {
  popupConfirm('Bạn có muốn xóa không?',function (result) {
    if (result){
      $.ajax({
        type: 'DELETE',
        url: '/admin/variant/' + variant_id,
        success: function(json) {
          if(!json.code) toastr.success("Delete variant: " + variant_id);
          row.remove();
        }
      });
    }
  })
}

$(document).on('click', '.move-next', function(e) {
  e.stopPropagation();
  $(document).find('.moving').removeClass('moving');
  var item = $(this).closest('.image').addClass('moving');
  var index = $(document).find('.list-image').find(item).index();
  if($(document).find('.list-image').find('.image').eq(index+1).html()) {
    var html = $(document).find('.list-image').find(item).get(0).outerHTML;
    $(document).find('.list-image').find('.image').eq(index+1).after(html);
    item.remove();
  }
});

$(document).on('click', '.move-prev', function(e) {
  e.stopPropagation();
  $(document).find('.moving').removeClass('moving');
  var item = $(this).closest('.image').addClass('moving');
  var index = $(document).find('.list-image').find(item).index();
  if(index) {
    if($(document).find('.list-image').find('.image').eq(index-1).html()) {
      var html = $(document).find('.list-image').find(item).get(0).outerHTML;
      $(document).find('.list-image').find('.image').eq(index-1).before(html);
      item.remove();
    }
  }
});

$('.list-tags').on('click', '.tag-name', function() {
  var text = $(this).html();
  $("input[name='tags']").tagsinput('add', text);
});

$(document).on('change', '.title', function() {
  var id = '';
  if ($(this).data('id')) id = $(this).data('id');
  var title = $(this).val();
  var handle = createHandle(title);
  var tab = $(this).closest('.tab-pane');
  tab.find('.handle').val(handle);
  $.get('/admin/api/checkHandle?handle=' + handle + '&type=product&id=' + id, function(result) {
    tab.find('.handle').val(result);
  });
});

$(document).on('change', '.handle', function() {
  var input = $(this);
  var handle = $(this).val();
  var id = '';
  if ($(this).data('id')) id = $(this).data('id');
  $.get('/admin/api/checkHandle?handle=' + handle + '&type=product&id=' + id, function(result) {
    input.val(result);
  });
});

function updateProductStock(product_id) {
  $.get('/admin/product/updateStock?product_id=' + product_id, function(json) {
    if (!json.code) console.log("Update stock");
  });
}

$('.list-variant').on('click', 'span.remove', function() {
  $(this).parent().remove();
});

$(document).on('click','.btn-add-tag',function () {
    var data = $(this).closest('#modal-add-tag').find('input[name="tags"]').tagsinput('items');
    var arrId = [];
    $('tbody input:checkbox:checked').each(function () {
        arrId.push($(this).val());
    })
    $.ajax({
        type: "POST",
        url: '/admin/product/tag',
        data:{
          'data': data,
          'arrId': arrId
        },
        success: function (json) {
          $('#modal-add-tag').modal('hide');
          toastr.success('Thêm vào tag thành công');
        }
    })
})
$(document).on('click','.btn-remove-tag',function () {
    var data = $(this).closest('#modal-remove-tag').find('input[name="tags"]').tagsinput('items');
    var arrId = [];
    $('tbody input:checkbox:checked').each(function () {
        arrId.push($(this).val());
    })
    $.ajax({
        type: "DELETE",
        url: '/admin/product/tag',
        data: {
          'data': data,
          'arrId': arrId
        },
        success: function (json) {
          $('#modal-remove-tag').modal('hide');
          toastr.success('Xóa khỏi tag thành công');
        }
    })
})
$(document).on('click','.btn-add-collection',function () {
    var arrIdCollection = [];
    $('#modal-add-collection :checked').each(function () {
        arrIdCollection.push($(this).val());
    })
    var arrId = [];
    $('tbody input:checkbox:checked').each(function () {
        arrId.push($(this).val());
    })
    $.ajax({
        type: 'POST',
        url: '/admin/api/collection/addMuch',
        data:{
          'arrIdCollection': arrIdCollection,
          'arrId': arrId
        },
        success: function (json) {
          $('#modal-add-collection').modal('hide');
          toastr.success('Thêm vào nhóm sản phẩm thành công');
        }
    })
})
$(document).on('click','.btn-remove-collection',function () {
    var arrIdCollection = [];
    $('#modal-remove-collection :checked').each(function () {
        arrIdCollection.push($(this).val());
    })
    var arrId = [];
    $('tbody input:checkbox:checked').each(function () {
        arrId.push($(this).val());
    })
    $.ajax({
        type: 'DELETE',
        url: '/admin/api/collection/deleteMuch',
        data:{
            'arrIdCollection': arrIdCollection,
            'arrId': arrId
        },
        success: function (json) {
          $('#modal-remove-collection').modal('hide');
          toastr.success('Xóa khỏi nhóm sản phẩm thành công');
        }
    })
})

$(document).on('click','.btn-create-product-buy-together',function () {
  var modal = $(this).closest('.modal')
  var data = {};
  data.product_buy_together = modal.find('select[name="product_buy_together"]').val();
  if (!data.product_buy_together){
    toastr.error('Chưa chọn sản phẩm mua kèm');
    modal.find('select[name="product_buy_together"]').addClass('error');
    return;
  }
  data.product_buy_together_title = modal.find('[name="product_buy_together"] option:selected').text();
  data.price_sale = modal.find('input[name="price_sale"]').val();
  if (!data.price_sale){
    toastr.error('Chưa nhập giá sản phẩm');
    modal.find('input[name="price_sale"]').addClass('error');
    return;
  }
  data.promotion = modal.find('input[name="promotion"]').val();
  data.status = modal.find('select[name="status"]').val();
  if (modal.find('.btn-create-product-buy-together').data('id')){
    $('.add-product-buy-together .row').each(function () {
      var self = $(this);
      if (modal.find('.btn-create-product-buy-together').data('id') == self.data('product_buy_together')){
        self.attr('data-product_buy_together',data.product_buy_together);
        self.attr('data-price_sale',data.price_sale);
        self.attr('data-promotion',data.promotion);
        self.attr('data-status',data.status);
        self.attr('data-title',data.product_buy_together_title);
        self.find('.product_buy_together').text(data.product_buy_together_title);
        self.find('.price_sale').text(data.price_sale);

        return;
      }
    })
  }
  else{
    $('.add-product-buy-together').append('' +
      '<div style="padding-left: 10px" class="row" data-product_buy_together="' + data.product_buy_together + '" data-price_sale="' + data.price_sale + '" data-promotion="' + data.promotion + '" data-status="' + data.status + '" data-title="' + data.product_buy_together_title + '">\n' +
      '  <div class="pull-left">' +
      '    <a data-toggle="modal" data-target="#modal-edit-product-buy-together" class="product_buy_together">'+ data.product_buy_together_title +'</a>\n' +
      '    <p class="price_sale">' + data.price_sale + '</p>\n' +
      '  </div>\n' +
      '  <div class="pull-right"><a class="btn text-danger btn-remove-product-buy-together"><i class="fa fa-trash"></i></a></div>\n' +
      '</div>'
    );
  }
  modal.modal('hide');
})

// $(document).on('click','.product_buy_together', function (e) {
//   e.stopPropagation();
//   var self = $(this);
//   var row = self.closest('.row');
//   var product_buy_together = row.data('product_buy_together');
//   var price_sale = row.data('price_sale');
//   var promotion = row.data('promotion');
//   var status = row.data('status');
//   console.log(product_buy_together);
//   var modal = $('#modal-edit-product-buy-together');
//   modal.find('select[name="product_buy_together"]').val(product_buy_together);
//   modal.find('input[name="price_sale"]').val(price_sale);
//   modal.find('input[name="promotion"]').val(promotion);
//   modal.find('.btn-create-product-buy-together').attr('data-id',product_buy_together);
// })

//variant
var numAttribute = 1;
$('.btn-add-attribute').on('click',function () {
  var optionAttribute = $('.select-attribute:first').html();
  var addAttribute = $('.add-attribute');
  var temp = tmpl("add-row-attribute", optionAttribute);
  addAttribute.append(temp);
  $(".chosen-select").chosen({width: "100%"});
  checkSelectAttribute();
  numAttribute++;
  if (numAttribute == 6) $('.btn-add-attribute').addClass('hidden');
})

$(document).on('click','.btn-remove-attribute',function () {
  var row = $(this).closest('.row');
  row.remove();
  numAttribute--;
  checkSelectAttribute();
  $('.btn-add-attribute').removeClass('hidden');
})

$(document).on('click','.select-attribute',function () {
  var chosen = $(this).closest('.row').find('.chosen-select');
  var id = $(this).val();
  if (id != -1){
    $.get('/admin/api/getChildAttribute?parent_id=' + id, function (json) {
      if (!json.code){
        var option = '';
        json.data.forEach(function (element, index) {
          option += tmpl('attribute-option', element.name);
        })
        chosen.html(option);
        chosen.trigger("chosen:updated");
      }
    })
  }
})

$(document).on('change','.chosen-select',function () {
  loadVariant();
})

$(document).on('click','.btn-remove-attribute',function () {
  loadVariant();
})

function checkSelectAttribute() {
  var $selects = $('.select-attribute');
  $selects.on('change', function () {
    $("option", $selects).prop("disabled", false);
    $selects.each(function () {
      var $select = $(this),
      $options = $selects.not($select).find('option'),
      selectedText = $select.children('option:selected').text();
      $options.each(function () {
        if ($(this).text() == selectedText) $(this).prop("disabled", true);
      });
    });
  });
  $selects.eq(0).trigger('change');
}

function getDataAttribute() {
  var valueAttribute = $('.add-attribute .chosen-select')
  var value = []
  valueAttribute.each(function (index) {
    var self = $(this)
    if (self.val().length == 0) {
      return false;
    }
    value[index] = self.val()
  })
  // valueAttribute.each(function (index) {
  //   var self = $(this)
  //   if (self.val().length > 0) {
  //     value[index] = self.val()
  //   }
  //   else{
  //     return false;
  //   }
  // })
  return value
}

function loadVariant() {
  var dataAttribute = getDataAttribute();
  var result = [];
  if (dataAttribute.length){
    result = dataAttribute.reduce(function (a, b) {
      return a.reduce(function (r, v) {
        return r.concat(b.map(function (w) {
          return [].concat(v, w);
        }));
      }, []);
    });
  }

  var htmlVariant = '';
  var total;
  if (Array.isArray(result[0])){
    var listVariant = result.map(function (a) {
      return a.join('/');
    });
    listVariant.forEach(function (element, index) {
      htmlVariant += tmpl('add-variant',element)
    })
    total = listVariant.length;
  }
  else {
    result.forEach(function (element, index) {
      htmlVariant += tmpl('add-variant',element)
    })
    total = result.length;
  }
  $('.list-all-variant').html(htmlVariant);
  $('.total-variant').text('Tổng cộng ' + total + ' phiên bản')
}

function disableInventory() {
  var check = $('select[name="inventory-management"]').val();
  if (check && parseInt(check)) {
    $(document).find('input[name="variant-inventory"]').prop('disabled', false);
  } else {
    $(document).find('input[name="variant-inventory"]').prop('disabled', true);
  }
}