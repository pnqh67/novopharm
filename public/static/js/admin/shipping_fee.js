initDataTable('table');

$(".chosen-select").chosen({width: "100%"});

$('.chosen-select').on('change',function () {
    var regionId = $(this).val();
    $('.btn-shipping-add').removeClass('hidden');
    $.ajax({
        type: 'POST',
        url: '/admin/subregion/' + regionId,
        success: function (html) {
            $('#modal-subregion').remove();
            $('.shipping-fee').append(html);
        }
    });
    loadShipingFee(regionId);
});


function loadShipingFee(regionid) {
    $.ajax({
        type: 'POST',
        url: '/admin/shipping_fee/fee/' + regionid,
        success: function (html) {
            $('.box-shipping-fee .box').each(function (index,value) {
                $(this).remove();
            })
            $('.box-shipping-fee').append(html);
        }
    })
}

$(document).on('click','.btn-create-shipping',function () {
    var modal = $(this).closest('.modal');
    var idShippingFee = modal.data('id');
    var regionId = modal.data('regionid');
    var data ={};
    var dataSubregion =[];
    data.title = $('input[name="title"]').val();
    if (!data.title.trim().length){
        toastr.error('Chưa nhập tên phương thức vận chuyển');
        $('input[name="title"]').addClass('error');
        return;
    }
    data.type = $('select[name="type"]').val();
    data.from = $('input[name="from"]').val();
    data.to = $('input[name="to"]').val();
    data.price = $('input[name="price"]').val();
    data.region_id = regionId;

    $('input[name="subregion_id"]').each(function (index) {
        var element = {};
        element.subregion_id = $(this).data('id');
        element.price = $(this).val();
        if (element.price != '')
            dataSubregion.push(element);
    })
    if (!idShippingFee){
        $.ajax({
            type: 'POST',
            url: '/admin/shipping_fee',
            data:{
                data: data,
                subRegion: dataSubregion
            },
            success: function (json) {
                if(!json.code) {
                    toastr.success('Tạo phương thức thành công');
                    reloadPage('/admin/shipping_fee/edit/' + regionId);
                } else if(json.code == -1) toastr.error('Phương thức đã tồn tại');
                else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
            }
        });
    }
    else{
        $.ajax({
            type: 'PUT',
            url: '/admin/shipping_fee/' + idShippingFee,
            data:{
                data: data,
                subRegion: dataSubregion
            },
            success: function (json) {
                if(!json.code) {
                    toastr.success('Sửa phương thức thành công');
                    reloadPage('/admin/shipping_fee/edit/' + regionId);
                } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
            }
        });
    }

});

$(document).on('click','.btn-edit-shipping-fee',function () {
    var id = $(this).data('id');
    $('.btn-create-shipping').html('Sửa');
    $.ajax({
       type: 'GET',
       url: '/admin/shipping_fee/' + id,
       success: function (json) {
           var data = json.data;
           var subregion = json.subregion;
           $('#modal-subregion').attr('data-regionid',data.region_id);
           $('#modal-subregion').attr('data-id',data.id);
           data.title?$('input[name="title"]').val(data.title):'';
           data.type?$('select[name="type"]').val(data.type):'';
           data.from?$('input[name="from"]').val(data.from):'';
           data.to?$('input[name="to"]').val(data.to):'';
           data.price?$('input[name="price"]').val(data.price):'';
           subregion.forEach(function (value, index) {
               $('input[name="subregion_id"]').each(function () {
                   if ($(this).data('id')==value.subregion_id){
                       $(this).val(value.price)
                   }
               })
           })
       } 
    });
})

$(document).on('click','.btn-shipping-add',function () {
    $('.btn-create-shipping').html('Thêm');
    $('.modal').removeAttr('data-id');
    $('.modal :input').each(function () {
        if ($(this).attr('name') != 'type')
            $(this).val('');
    })
})

$(document).on('click','.btn-remove-shipping',function () {
    var id = $(this).data('id');
    var box = $(this).closest('.box-shipping-fee .box');
    popupConfirm('Xóa phương thức vận chuyển',function (result) {
        if (result){
            $.ajax({
                type: 'DELETE',
                url: '/admin/shipping_fee/' + id,
                success: function (json) {
                    if(!json.code) {
                        toastr.success('Xóa phương thức vận chuyển thành công');
                        box.remove().draw();
                    } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
                }
            });
        }
    })
})