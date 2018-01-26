initDataTable('table');
var modelName = 'product_buy_together';

var statusValue = $('select[name="status"]').data('value');
$('select[name="status"] option').each(function () {
    if ($(this).val()==statusValue){
        $(this).attr('selected','selected');
    }
})
$('.btn-create').click(function () {
    var id = $(this).data('id');
    var data = {};
    data.product_id = $('select[name="product"]').val();
    if (data.product_id==0){
        toastr.error("Vui lòng chọn sản phẩm chính");
        $('select[name="product"]').addClass('error');
        return;
    }
    data.product_buy_together_id = $('select[name="product_buy_together"]').val();
    if (data.product_buy_together_id==0){
        toastr.error("Vui lòng chọn sản phẩm chính mua kèm");
        $('select[name="product_buy_together"]').addClass('error');
        return;
    }
    data.product_buy_together_title = $('select[name="product_buy_together"] option:selected').text();
    data.product_title = $('select[name="product"] option:selected').text();
    data.price_sale = $('input[name="price_sale"]').val();
    if (!data.price_sale){
        toastr.error("Vui lòng nhập giá");
        $('input[name="price_sale"]').addClass('error');
        return;
    }
    data.promotion = $('input[name="promotion"]').val();
    data.status = $('select[name="status"]').val();
    if (!id){
        $.ajax({
            type: 'POST',
            url: '/admin/product_buy_together',
            data: data,
            success: function (json) {
                if (!json.code){
                    toastr.success('Tạo thành công');
                    reloadPage('/admin/product_buy_together/' + json.id);
                }
                else if (json.code==-1){
                    toastr.error("Đã tồn tại sản phẩm mua kèm");
                } else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
            }
        })
    }
    else{
        $.ajax({
            type: 'PUT',
            url: '/admin/product_buy_together/' + id,
            data: data,
            success: function (json) {
                if (!json.code){
                    toastr.success('Sửa thành công');
                    reloadPage('/admin/product_buy_together/');
                }
                else toastr.error('Có lỗi xảy ra, xin vui lòng thử lại');
            }
        })
    }

})