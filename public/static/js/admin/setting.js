$(document).ready(function () {
  $.get('/admin/view/setting', function (html) {
    $('#content').html(html);
    $('.content .ajax-source').each(function () {
      var self = $(this);
      if (self.data('type')) {
        var type = self.data('type');
        $.get('/admin/setting/getList?type=' + type, function (json) {
          if (!json.code) {
            self.html('')
            json.data.forEach(function (element, index) {
              var html = '<option value=' + parseInt(element.id) + '>' + element.title + '</option>'
              self.append(html)
            })
            initTinymce('.tinymce');
            if (self.attr('multiple')) {
              self.chosen({width: "100%"});
            }
          }
        })
      }
    })
  })
  $.get('/admin/getSetting', function (json) {
    if (!json.code) {
      json.datas.forEach(function (element, index) {
        var tag = $('.content').find('[name=' + element.key + ']');
        tag.val(element.value);
        if (tag.prop('tagName') == 'textarea'){
          tag.append(element)
        }
        if (tag.hasClass('ajax-source') && tag.attr('multiple')) {
          var value = JSON.parse(element.value);
          var mapNumber = value.map(Number);
          setTimeout(function () {
            mapNumber.forEach(function (val, index) {
              tag.find('option[value="' + val + '"]').attr('selected', 'selected');
            });
            tag.trigger("chosen:updated");
          }, 5000);
        }
      })
      $('.content').find('form input[type="hidden"]').each(function () {
        var inputPrev = $(this).closest('form').find('img');
        var value = $(this).val();
        inputPrev.attr('src', '/uploads/' + value);
      })
    }
  })

  $('.type-item').on('change', function () {
    if ($(this).val() == 'block') {
      $('.num-block').addClass('disabled');
    }
    else {
      $('.num-block').removeClass('disabled');
      $('.num-block').html('');
      $('.num-block').append('<option value="0">Chọn block cần thêm vào</option>')
      $('.content .box').each(function (index, element) {
        var html = '<option value=' + parseInt(index + 1) + '>Block ' + parseInt(index + 1) + '</option>'
        $('.num-block').append(html)
      })
    }
  })

  $('.select-type select').on('change', function () {
    var self = $(this);
    var value = $(this).val();
    $.get('/admin/setting/getList?type=' + value, function (json) {
      if (!json.code) {
        self.closest('.form-group').find('.select-item select').html('')
        json.data.forEach(function (element, index) {
          var html = '<option value=' + parseInt(element.id) + '>' + element.title + '</option>'
          self.closest('.form-group').find('.select-item select').append(html)
        })
      }
    })
  })
})

$('.btn-update-setting-website').click(function () {
  var data = {};
  var btn = $(this);
  $('.content :input:not(".upload")').each(function (index, element) {
    var self = $(this)
    var name = element.name;
    if (self.hasClass('tinymce')){
      data[name] = tinyMCE.get(name).getContent();
    }
    else {
      data[name] = $(this).val();
    }
  })
  btn.addClass('disabled');
  $.ajax({
    type: 'PUT',
    url: '/admin/setting',
    data: data,
    success: function (json) {
      btn.removeClass('disabled');
      if (!json.code) {
        toastr.success('Cập nhật thành công');
        reloadPage();
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$(document).on('change', '.upload', function () {
  var self = $(this);
  if (checkExtImage($(this).val())) {
    var form = $(this).closest('form');
    var formData = new FormData(form[0]);
    $.ajax({
      type: 'POST',
      url: '/admin/api/uploadImage',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: function (json) {
        if (!json.code) {
          var image = json.data;
          var timestamp = new Date() - 0;
          form.find('[type="hidden"]').val(image);
          form.find('img').attr('src', '/uploads/' + image + '?v=' + timestamp);
        } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
      }
    });
  }
});