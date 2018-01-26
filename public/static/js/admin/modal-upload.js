var elementImage;
var chooseMultipleImage = false;
var uploadType = '';

function showModalUpload() {
  $('#modal-upload').modal('show');
}

$('.item-choose-image img').click(function() {
  elementImage = $(this).parent();
  $('#modal-upload').modal('show');
});


$(document).on('click', '.choose-image-variant .add-image', function() {
  elementImage = $(this).parent();
  chooseMultipleImage = true;
  uploadType = 'variant';
  $('#modal-upload').modal('show');
});

$('.file-manager').on('click', '.item:not(form)', function() {
  if (!chooseMultipleImage) $('.file-manager').find('.item.active').removeClass('active');
  if ($(this).hasClass('active')) {
    $(this).removeClass('active');
  } else {
    $(this).addClass('active');
  }
  var countActive = $('.file-manager').find('.item.active').length;
  if (countActive) $('.file-manager').find('.btn-choose-image').removeClass('disabled');
  else $('.file-manager').find('.btn-choose-image').addClass('disabled');
});

$('.btn-choose-image').click(function() {
  if (!chooseMultipleImage) {
    var name = $('.file-manager').find('.item.active').attr('data-src');
    var src = '/uploads/' + resizeImage(name, 480);
    elementImage.addClass('active');
    elementImage.find('img').attr('src', src);
    elementImage.find('.value').val(name);
    $('#modal-upload').modal('hide');
  } else {
    if (uploadType == 'variant') {
      $('.file-manager').find('.item.active').each(function(index, elem) {
        var name = $(this).attr('data-src');
        var obj = {
          name: name,
          src: '/uploads/' + resizeImage(name, 240)
        };
        var item_image = tmpl("add-item-image", obj);
        elementImage.find('.add-image').before(item_image);
      });
      $('#modal-upload').modal('hide');
    }
  }
});


function readURL(files, callback) {
  function loadOne(file) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var imgData = reader.result;
      output.push(imgData);
      if (output.length == files.length) {
        callback(output);
      }
    };
    reader.readAsDataURL(file);
  }
  var output = [];
  for (var i = 0; i < files.length; i++) {
    loadOne(files[i]);
  }
}

$('.file-manager').on('change', '#upload-image', function() {
  if($(this).val()) {
    var form = $(this).closest('form');
    form.addClass('disabled');
    var formData = new FormData(form[0]);
    $.ajax({
      type: 'POST',
      url: '/admin/api/uploadImage',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: function(json) {
        var list = '';
        $.each(json.data, function(i, e) {
          var src = location.origin + '/uploads/' + e;
          list += '<div class="col-xs-2 image"><div class="item" style="background: url('+src+')" data-src="'+e+'"></div></div>';
        });
        $('.file-manager').find('.add-image').after(list);
        form.removeClass('disabled');
      }
    });
  }
});

$('.item-choose-image').on('click', '.remove-image', function() {
  var parent = $(this).parent();
  var default_img = parent.data('default');
  parent.find('img').attr('src', default_img);
  parent.find('.value').val('');
  parent.removeClass('active');
});

$('#modal-upload').on('hidden.bs.modal', function() {
  $('#modal-upload').find('.add-image').nextAll().remove();
  uploadType = '';
  chooseMultipleImage = false;
});

var currentPage = 1;
$('#modal-upload').on('show.bs.modal', function() {
  getUpload(currentPage);
});

function getUpload(page = 1) {
  currentPage = page!=1?page:1;
  $('#modal-upload').find('div.image').remove();
  $.get('/admin/api/uploads?page=' + page, function(json) {
    if (!json.code) {
      var list = '';
      $.each(json.data, function(index, elem) {
        var src = location.origin + '/uploads/' + resizeImage(elem, 240);
        var obj = {
          src: src,
          image: elem
        };
        var item = tmpl("item-upload-image", obj);
        list += item;
      });
      $('#modal-upload').find('.add-image').after(list);
      var page = ''
      for (i = 1; i<= json.total_page; i++){
        if (json.page_number == i)
          page += '<li class="active"><a onclick="getUpload(' + i + ')">' + i + '</a></li>'
        else
          page += '<li><a href="#" onclick="getUpload(' + i + ')">' + i + '</a></li>'
      }
      $('#modal-upload').find('.pagination-upload ul').html(page);
    }
  });
}

$(document).on('click','.image .remove-image',function () {
  var self = $(this);
  var name = self.closest('.item').data('src');
  popupConfirm('Bạn có muốn xóa hình không?',function (result) {
    if (result){
      $.ajax({
        type: 'POST',
        url: '/admin/api/removeUploads',
        data:{
          'name': name
        },
        success: function (json) {
          if (!json.code){
            toastr.success('Xóa thành công');
            self.closest('.item').remove();
            getUpload(1);
          } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        }
      })
    }
  })
})
