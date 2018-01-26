initDataTable('table');

var data = {};
data['order'] = [{
  id: "order",
  text: "Đơn hàng",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/order'
      }
    },
    {
      text: "Mới",
      data: {
        method: 'GET',
        endpoint: '/order?order_status=new'
      }
    },
    {
      text: "Đã xác nhận",
      data: {
        method: 'GET',
        endpoint: '/order?order_status=confirm'
      }
    },
    {
      text: "Chưa thanh toán",
      data: {
        method: 'GET',
        endpoint: '/order?order_status=confirm&payment_status=0'
      }
    },
    {
      text: "Hoàn tất",
      data: {
        method: 'GET',
        endpoint: '/order?order_status=done'
      }
    },
    {
      text: "Bị hoàn trả",
      data: {
        method: 'GET',
        endpoint: '/order?order_status=return'
      }
    },
    {
      text: "Bị hủy",
      data: {
        method: 'GET',
        endpoint: '/order?order_status=cancel'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/order'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/order'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/order/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/order/csv'
      }
    }
  ]
}];
data['product'] = [{
  id: "product",
  text: "Sản phẩm",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/product'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/product/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/product'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/product'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/product/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/product/csv'
      }
    }
  ]
}];
data['collection'] = [{
  id: "collection",
  text: "Nhóm sản phẩm",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/collection'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/collection/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/collection'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/collection'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/collection/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/collection/csv'
      }
    }
  ]
}];
data['attribute'] = [{
  id: "attribute",
  text: "Thuộc tính sản phẩm",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/attribute'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/attribute/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/attribute'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/attribute'
      }
    }
  ]
}];
data['coupon'] = [{
  id: "coupon",
  text: "Mã giảm giá",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/coupon'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/coupon/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/coupon'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/coupon'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/coupon/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/coupon/csv'
      }
    }
  ]
}];
data['sale'] = [{
  id: "sale",
  text: "Chương trình giảm giá",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/sale'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/sale/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/sale'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/sale'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/sale/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/sale/csv'
      }
    }
  ]
}];
data['customer'] = [{
  id: "customer",
  text: "Khách hàng",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/customer'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/customer/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/customer'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/customer'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/customer/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/customer/csv'
      }
    }
  ]
}];
data['article'] = [{
  id: "article",
  text: "Bài viết",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/article'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/article/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/article'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/article'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/article/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/article/csv'
      }
    }
  ]
}];
data['blog'] = [{
  id: "blog",
  text: "Nhóm bài viết",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/blog'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/blog/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/blog'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/blog'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/blog/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/blog/csv'
      }
    }
  ]
}];
data['page'] = [{
  id: "page",
  text: "Trang nội dung",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/page'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/page/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/page'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/page'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/page/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/page/csv'
      }
    }
  ]
}];
data['comment'] = [{
  id: "comment",
  text: "Bình luận",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Xem tất cả",
      data: {
        method: 'GET',
        endpoint: '/comment'
      }
    },
    {
      text: "Mới",
      data: {
        method: 'GET',
        endpoint: '/comment?status=new'
      }
    },
    {
      text: "Thuộc sản phẩm",
      data: {
        method: 'GET',
        endpoint: '/comment/product'
      }
    },
    {
      text: "Thuộc bài viết",
      data: {
        method: 'GET',
        endpoint: '/comment/article'
      }
    },
    {
      text: "Trả lời",
      data: {
        method: 'PUT',
        endpoint: '/comment'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/comment'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/comment/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/comment/csv'
      }
    }
  ]
}];
data['contact'] = [{
  id: "contact",
  text: "Liên hệ",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Xem tất cả",
      data: {
        method: 'GET',
        endpoint: '/contact'
      }
    },
    {
      text: "Trả lời",
      data: {
        method: 'PUT',
        endpoint: '/contact'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/contact'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/contact/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/contact/csv'
      }
    }
  ]
}];
data['menu'] = [{
  id: "menu",
  text: "Menu",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/menu'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/menu/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/menu'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/menu'
      }
    }
  ]
}];
data['slider'] = [{
  id: "slider",
  text: "Slider",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/slider'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/slider/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/slider'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/slider'
      }
    }
  ]
}];
data['shipping_fee'] = [{
  id: "shipping_fee",
  text: "Phí vận chuyển",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/shipping_fee'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/shipping_fee/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/shipping_fee'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/shipping_fee'
      }
    }
  ]
}];
data['user'] = [{
  id: "user",
  text: "Quản trị viên",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/user'
      }
    },
    {
      text: "Lịch sử hoạt động",
      data: {
        method: 'GET',
        endpoint: '/history'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/user/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/user'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/user'
      }
    },
    {
      text: "In",
      data: {
        method: 'GET',
        endpoint: '/user/print'
      }
    },
    {
      text: "Xuất file CSV",
      data: {
        method: 'GET',
        endpoint: '/user/csv'
      }
    }
  ]
}];
data['permission'] = [{
  id: "permission",
  text: "Phân quyền",
  state: {
    opened: true
  },
  data: 'parent',
  children: [
    {
      text: "Danh sách",
      data: {
        method: 'GET',
        endpoint: '/permission'
      }
    },
    {
      text: "Thêm",
      data: {
        method: 'GET',
        endpoint: '/permission/create'
      }
    },
    {
      text: "Sửa",
      data: {
        method: 'PUT',
        endpoint: '/permission'
      }
    },
    {
      text: "Xóa",
      data: {
        method: 'DELETE',
        endpoint: '/permission'
      }
    }
  ]
}];

$('.box-tree').each(function() {
  var role_id = $('input[name="role_id"]').val();
  var item = $(this).data('item');
  var element = $(this).find('.tree');
  var _data = data[item];
  if (role_id) {
    getPermission(role_id, item, function(json) {
      if (json.count) {
        var children = _data[0]['children'];
        $.each(children, function(index, elem) {
          $.each(json.data, function(i, e) {
            var obj = {
              method: e.method,
              endpoint: e.endpoint
            };
            if (JSON.stringify(obj) == JSON.stringify(elem.data)) {
              _data[0]['children'][index].state = { checked: true};
            }
          });
        });
        initJsTree(element, _data);
      } else initJsTree(element, _data);
    });
  } else initJsTree(element, _data);
});

function initJsTree(element, data) {
  element.jstree({
    core: {
      data: data,
      check_callback: false,
      themes: {
        name: 'proton',
        responsive: true
      }
    },
    checkbox: {
      three_state : true,
      whole_node : false,
      tie_selection : false,
      keep_selected_style: false
    },
    plugins: ['checkbox']
  });
}

$('.btn-create-update-role').click(function() {
  var id = $(this).data('id');
  $('input').removeClass('error')
  var data = {};
  data.title = $('input[name="title"]').val();
  if(!data.title) {
    toastr.error('Chưa nhập tên phân quyền');
    $('input[name="title"]').addClass('error');
    return;
  }
  data.permission = [];
  $('.box-tree').each(function() {
    var group = $(this).data('item');
    var tree = $(this).find('.tree');
    $.each(tree.jstree("get_checked", true), function() {
      if (this.data != 'parent' && this.data.method && this.data.endpoint) {
        var obj = {
          group: group,
          method: this.data.method,
          endpoint: this.data.endpoint
        };
        data.permission.push(obj);
      }
    });
  });

  $(this).addClass('disabled');
  if (id) updateRole(id, data);
  else createRole(data);
});

function createRole(data) {
  $.ajax({
    type: 'POST',
    url: '/admin/role',
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Tạo thành công');
        reloadPage('/admin/role/' + json.id);
      } else if (json.code == -1) {
        toastr.error('Phân quyền đã tồn tại');
      } else if (json.code == -4) {
        toastr.error(json.message);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

function updateRole(id, data) {
  $.ajax({
    type: 'PUT',
    url: '/admin/role/' + id,
    data: data,
    success: function(json) {
      $(document).find('.disabled').removeClass('disabled');
      if(!json.code) {
        toastr.success('Cập nhật thành công');
      } else if (json.code == -1) {
        toastr.error('Phân quyền đã tồn tại');
      } else if (json.code == -4) {
        toastr.error(json.message);
      } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
    }
  });
}

$(document).on('click', '.btn-remove', function() {
  var id = $(this).data('id');
  var tr = $(this).closest('tr');
  popupConfirm("Xóa phân quyền?", function(result) {
    if (result) {
      $.ajax({
        type: 'DELETE',
        url: '/admin/role/' + id,
        success: function(json) {
          if(!json.code) {
            toastr.success('Xóa thành công');
            tbl.row(tr).remove().draw();
          } else if (json.code == -1) {
            toastr.error('Phân quyền này đang được sử dụng');
          } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
        }
      });
    }
  });
});

function getPermission(role_id, group, fn) {
  var url = '/admin/api/permission?role_id=' + role_id + '&group=' + group;
  $.get(url, fn);
}
