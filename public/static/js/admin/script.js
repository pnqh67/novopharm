



var staticURI = $('body').data('uri');
function initTinymce(item) {
  if ($(item).length) {
    tinymce.init({
    	selector: item,
    	height: 300,
    	theme: 'modern',
      relative_urls : false,
      remove_script_host : false,
      convert_urls : true,
    	plugins: [
    		'code advlist autolink lists link image charmap print preview hr anchor pagebreak',
    		'searchreplace wordcount visualblocks visualchars code fullscreen',
    		'insertdatetime media nonbreaking save table contextmenu directionality',
    		'emoticons template paste textcolor colorpicker textpattern imagetools'
    	],
    	toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image ',
    	toolbar2: 'print preview media | forecolor backcolor emoticons | removeformat',
    	image_advtab: true,
      automatic_uploads: true,
      images_upload_base_path: '/uploads',
      imageupload_url: '/',
      file_browser_callback_types: 'file image media',
      file_browser_callback: function(field_name, url, type, win) {
        tinymce.activeEditor.windowManager.open({
            title: "Hình ảnh",
            filetype: 'all',
            file: '/admin/api/tinymce/images',
            width: 800,
            height: 450,
            inline: 1
          }, {
          window : win,
          input : field_name
        });
      },
      images_upload_handler: function (blobInfo, success, failure) {
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', 'postAcceptor.php');
        xhr.onload = function() {
          var json;

          if (xhr.status != 200) {
            failure('HTTP Error: ' + xhr.status);
            return;
          }
          json = JSON.parse(xhr.responseText);

          if (!json || typeof json.location != 'string') {
            failure('Invalid JSON: ' + xhr.responseText);
            return;
          }
          success(json.location);
        };
        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
      }
    });
  }
}

var tbl;
function initDataTable(item) {
  if ($(item).length) {
    tbl = $(item).DataTable({
      aaSorting: [],
      bInfo: true,
	    iDisplayLength: 25,
      bDestroy: true,
      bLengthChange: true,
      columnDefs: [ {
        targets: 0,
        orderable: false,
        order: []
      }],
      responsive: true,
      rowReorder: false,
      fnRowCallback: function(a, b, c, d) {
        var img = $(a).find('img')[0];
        var dataSrc = $(img).data('src');
        if (dataSrc) $(img).attr('src', dataSrc);
      },
      dom: 'Blfrtip',
      buttons: [
        {
            extend: 'print',
            exportOptions: {
                columns: ':visible'
            }
        },
        {
            extend: 'csv',
            text: "Xuất file CSV",
            exportOptions: {
                columns: ':visible'
            }
        },
        {
            extend: 'colvis',
            columns: ':not(:first-child)'
        }
      ],
      oTableTools: {
          aButtons: [
              {
                  sExtends: "csv",
                  bBomInc: 'false',
                  sCharSet: "utf8"
              }
          ]
      },
      language: {
          buttons: {
            print: 'In',
            colvis: 'Ẩn/hiện cột',
          },
          sSearch: "Tìm kiếm: "
      }
    });
    // tbl.column(0).visible(true);
  }
}

$(document).ready(function () {
    $('.dt-button-background').remove();
    $('div .dt-buttons').prepend('<a class="dt-button btn-fullscreen"><span>Xem full</span></a>');
})


function checkExtImage(image) {
  var ext = image.split('.').pop().toLowerCase();
  if($.inArray(ext, ['png','jpg','jpeg', 'gif']) == -1) {
	  toastr.error('Vui lÃ²ng chá»n Ä‘Ãºng Ä‘á»‹nh dáº¡nh áº£nh');
		return 0;
	}
  return 1;
}

function resizeImage(image, size) {
  if(image) {
    var ext = image.split('.').pop();
  	var a = '.' + ext;
  	var b = '_' + size + a;
  	var c = image.replace(a, b);
    return c;
  }
  return image;
}

$('#modal-change-password').on('show.bs.modal', function() {
  $('#modal-change-password').find('input').val('');
});

$('.form-change-password').on('submit', function(e){
  e.preventDefault();
  var password = $(this).find('input[name="password"]').val();
  var new_password = $(this).find('input[name="new_password"]').val();
  $.ajax({
    type: 'PUT',
    url: '/admin/api/user/changePassword',
    data: {
      password: password,
      new_password: new_password
    },
    success: function(json) {
      if(!json.code) {
        toastr.success('Đổi mật khẩu thành công');
        setTimeout(function(){
          window.location.reload();
        }, 1000);
      } else if(json.code == -1) toastr.error('Mật khẩu cũ không đúng');
      else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
});

$('.btn-remove-image').click(function() {
  var image = $(this).data('image');
  $(this).closest('.form-group').find('input').val('');
  $(this).closest('.form-group').find('img').attr('src', '/static/img/' + image);
});

function createHandle(str) {
	if(str) {
		str = str.trim();
		str = str.toLowerCase();
	  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
	  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
	  str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
	  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");
	  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
	  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
	  str = str.replace(/đ/g,"d");
    str = str.replace(/\,/g, '-');
    str = str.replace(/\./g, '-');
    str = str.replace(/\!/g, '-');
    str = str.replace(/\?/g, '-');
    str = str.replace(/\~/g, '-');
    str = str.replace(/\ /g, '-');
    str = str.replace(/\|/g, '-');
		str = str.replace(/\./g, '-');
    str = str.replace(/\"/g, '-');
    str = str.replace(/\'/g, '-');
    str = str.replace(/\--/g, '-');
		str = str.replace(/\--/g, '-');
		str = str.replace(/\--/g, '-');
    str = str.replace(/\--/g, '-');
    if(str.slice(-1) == '-') str = str.substring(0, str.length - 1);
	}
  return str;
}

function checkDate(datetime) {
  return new Date(datetime);
}

function reloadPage(url = null) {
  setTimeout(function() {
    if (url) location.href = url;
    else location.reload();
  }, 1000);
}

$(window).on('load', function() {
  $("select[data-value]").each(function() {
    var value = $(this).data('value');
    if ($(this).hasClass('chosen-select'))  $(this).val(value).trigger('chosen:updated');
    else $(this).val(value);
  });

  $( ".datepicker" ).datepicker({
    dateFormat: 'dd-mm-yy',
    minDate: 0,
    defaultDate: "+1w",
    changeMonth: true
  });
});

$('.main-item').click(function() {
  var treeview = $(this).closest('.treeview');
  treeview.siblings().removeClass('active');
  if (treeview.hasClass('active')) {
    treeview.removeClass('active');
  } else treeview.addClass('active');
});

$('.main-sidebar .treeview-menu a').click(function(e) {
  e.stopPropagation();
});

function popupConfirm(message, callback) {
  bootbox.confirm({
    message: message,
    buttons: {
      confirm: {
          label: 'Đồng ý',
          className: 'btn-primary'
      },
      cancel: {
          label: 'Hủy',
          className: 'btn-default'
      }
    },
    callback: callback
  });
}

function popupPrompt(message, callback) {
  bootbox.prompt(message, callback);
}

function createOrUpdateSEO(type, type_id) {
  var data = {};
  data.g_meta_title = $('input[name="g_meta_title"]').val();
  data.g_meta_description = $('textarea[name="g_meta_description"]').val();
  data.g_meta_keyword = $('textarea[name="g_meta_keyword"]').val();
  data.g_meta_robots = $('select[name="g_meta_robots"]').val();
  data.g_meta_title_en = $('input[name="g_meta_title_en"]').val();
  data.g_meta_description_en = $('textarea[name="g_meta_description_en"]').val();
  data.g_meta_keyword_en = $('textarea[name="g_meta_keyword_en"]').val();

  data.f_meta_title = $('input[name="f_meta_title"]').val();
  data.f_meta_description = $('textarea[name="f_meta_description"]').val();
  data.f_meta_title_en = $('input[name="f_meta_title_en"]').val();
  data.f_meta_description_en = $('textarea[name="f_meta_description_en"]').val();
  data.f_image = $('input[name="f_image"]').val();
  $.ajax({
    type: 'POST',
    url: '/admin/seo',
    data: {
      type: type,
      type_id: type_id,
      data: data
    },
    success: function(json) {
      if (!json.code) console.log("success");
    }
  });
}

function dmy2ymd(date) {
  if (date.indexOf('-') > -1) {
    var arr = date.split('-');
    return arr[2] + '-' + arr[1] + '-' + arr[0];
  } else if (date.indexOf('/') > -1) {
    var arr = date.split('/');
    return arr[2] + '/' + arr[1] + '/' + arr[0];
  }
  return date;
}

$(document).on('click','table :checkbox',function () {
    if ($(this).hasClass('select-all')){
        $('tbody').find(':checkbox').prop('checked', $(this).is(':checked'));
    }
    if ($('tbody input:checkbox:checked').length){
        $('.action-box').removeClass('hidden');
        var numberOfChecked = $('tbody input:checkbox:checked').length;
        $('.num-select').html(' ' + numberOfChecked + ' ');
    }
    else $('.action-box').addClass('hidden');
})

$(document).on('click','.btn-fullscreen',function () {
    $('.box-table').toggleClass('fullscreen');
})

$(document).on('click','.status-active',function () {
    var arrId = [];
    $('tbody input:checkbox:checked').each(function () {
        arrId.push($(this).val());
    })
    $.ajax({
        type: 'POST',
        url: '/admin/api/updateStatus',
        data: {
            type: modelName,
            arrId: arrId,
            status: 'inactive'
        },
        success: function (json) {
            $('tbody input:checkbox:checked').each(function () {
                $(this).closest('tr').find('td:last-of-type').html('<label class="label label-warning">Đang ẩn</label>')
            })
        }
    })
})
$(document).on('click','.status-inactive',function () {
    var arrId = [];
    $('tbody input:checkbox:checked').each(function () {
        arrId.push($(this).val());
    })
    $.ajax({
        type: 'POST',
        url: '/admin/api/updateStatus',
        data: {
            type: modelName,
            arrId: arrId,
            status: 'active'
        },
        success: function (json) {
            $('tbody input:checkbox:checked').each(function () {
                $(this).closest('tr').find('td:last-of-type').html('<label class="label label-info">Đang hiện</label>')
            })
        }
    })
})
$(document).on('click','.status-delete',function () {
    var arrId = [];
    $('tbody input:checkbox:checked').each(function () {
        arrId.push($(this).val());
    })
    var numSelected = $('tbody input:checkbox:checked').length;
    popupConfirm('Bạn có muốn xóa ' + numSelected + ' mục đã chọn không?',function (result) {
        if (result){
            $.ajax({
                type: 'POST',
                url: '/admin/api/updateStatus',
                data: {
                    type: modelName,
                    arrId: arrId,
                    status: 'delete'
                },
                success: function (json) {
                    $('tbody input:checkbox:checked').each(function () {
                        $(this).closest('tr').remove();
                    })
                }
            })
        }
    })
})
// //Set value select item to default
// for (i = 1; i < 6; i++) {
//   $('input[name=type_' + i + ']')[0].val("collection");
//   $('input[name=type_' + i + ']')[0].val("product");
//   alert("Done!");
// }
