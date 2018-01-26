var chartVisit;
var chartRevenue;

$(document).ready(function () {

    //hover show action contact
    $('.mt-comment').hover(function () {
        $(this).css('background-color','white');
        $(this).find('.mt-comment-details').removeClass('hidden');
    },function () {
        $(this).css('background-color','');
        $(this).find('.mt-comment-details').addClass('hidden');
    })

    $('input[name="visit"]').click(function () {
        var numOfDays = $(this).val();
        $(this).closest('.optionchart').find('label').removeClass('btn-danger');
        $(this).closest('label').addClass('btn-danger');
    })

    //aminate num
    $('.count').each(function () {
        $(this).prop('Counter',0).animate({
            Counter: $(this).data('value')
        }, {
            duration: 4000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    })

    //fullscreen
    $('.btn-fullscreen-visit').click(function () {
        $(this).closest('.box-visit').toggleClass('fullscreen');
        // loadDataVisit();
        $(this).find('i').toggleClass('fa-expand').toggleClass('fa-compress');
    })

    //time since history
    $('.mt-log-right p').each(function (index, element) {
        var timeResult = timeSince($(this).text());
        $(this).html(timeResult);
    });

    //graph Visit
    loadDataVisit();

    //graph Revenue
    loadDataRevenue();

});

$(document).on('change','.revenue-select', function () {
    loadDataRevenue();
})
$(document).on('change','input[name="visit"]', function () {
    loadDataVisit();
})

//contact
$(document).on('click','.btn-remove-contact',function () {
    var id = $(this).data('value');
    var block = $(this).closest('.mt-comment');
    popupConfirm('Bạn có muốn xóa liên hệ ?', function (result) {
        if (result){
            $.ajax({
                type: 'POST',
                url: '/admin/api/updateStatus',
                data: {
                    type: 'contact',
                    arrId: [id],
                    status: 'delete'
                },
                success: function (json) {
                    block.remove();
                }
            })
        }
    })
})

$(document).on('change','input[name="checklog"]',function () {
    $('input[name="checklog"]:checked').each(function (element,index) {
        if ($(this).val()=='admin'){
            $('.mt-log').each(function () {
                if ($(this).data('type')==0)
                    $(this).removeClass('hidden');
            })
        }
        if ($(this).val()=='user'){
            $('.mt-log').each(function () {
                if ($(this).data('type')==1)
                    $(this).removeClass('hidden');
            })
        }
    })
    $('input[name="checklog"]:not(:checked)').each(function (element,index) {
        if ($(this).val()=='admin'){
            $('.mt-log').each(function () {
                if ($(this).data('type')==0)
                    $(this).addClass('hidden');
            })
        }
        if ($(this).val()=='user'){
            $('.mt-log').each(function () {
                if ($(this).data('type')==1)
                    $(this).addClass('hidden');
            })
        }
    })
})

function loadDataVisit() {
    var numOfDays = $('input[name="visit"]:checked').val();
    $.ajax({
        type: 'get',
        url: '/admin/dashboard/getvisit/' + numOfDays,
        success: function (json) {
            if (!json.code){
                var value = Intl.NumberFormat();
                var graphData = makeRevenueGraph(json.id, numOfDays);
                $('#visitchart').remove();
                $('iframe.chartjs-hidden-iframe').remove();
                $('.child-box-visit').append('<canvas id="visitchart" style="max-height: 80%;"><canvas>');
                var ctxVisit = document.getElementById('visitchart').getContext('2d');
                chartVisit = new Chart(ctxVisit, {
                    type: 'line',
                    data: {
                        labels: graphData.labels,
                        datasets: [{
                            backgroundColor: 'rgb(120, 172, 214)',
                            borderColor: 'rgb(53, 105, 214)',
                            data: graphData.data,
                        }]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            }
        }
    })
}

function loadDataRevenue() {
    var numOfDays = $('.revenue-select').val();
    $.ajax({
       type: 'get',
       url: '/admin/dashboard/getrevenue/' + numOfDays,
       success: function (json) {
            if (!json.code){
                var value = Intl.NumberFormat();
                $('.revenue-total').html(value.format(json.id.countTotal.total) + 'đ');
                $('.revenue-count').html(json.id.countTotal.count);
                var graphData = makeRevenueGraph(json.id.sumOrder, numOfDays);
                $('#revenuechart').remove();
                $('iframe.chartjs-hidden-iframe').remove();
                $('.revenue-info').before('<canvas id="revenuechart" style="max-height: 65%;"><canvas>');
                var ctxRevenue = document.getElementById('revenuechart').getContext('2d');
                chartRevenue = new Chart(ctxRevenue, {
                    type: 'line',
                    data: {
                        labels: graphData.labels,
                        datasets: [{
                            backgroundColor: 'rgb(220, 74, 61)',
                            borderColor: 'rgb(255, 99, 132)',
                            data: graphData.data,
                        }]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            }
       }
    });
}

function makeRevenueGraph(revenue, numOfDays) {
    var hash = {};
    revenue.forEach(function(elem, i) {
        hash[elem.date] = elem.sum;
    });
    var labels = [];
    var data = [];
    if (numOfDays==7) {
        for (var i = numOfDays - 1; i >= 0; i--) {
            var date = new Date(new Date() - i * 86400000);
            var dateName = (date.getDate()<10?'0'+date.getDate():date.getDate()) + '-' + ((date.getMonth() + 1)<10?'0'+(date.getMonth() + 1):(date.getMonth() + 1));
            labels.push(dateName);
            if (hash[dateName]) {
                data.push(hash[dateName]);
            } else {
                data.push(0);
            }
        }
    }
    if (numOfDays==14) {
        for (var i = numOfDays - 1; i >= 0; i-=2) {
            var date = new Date(new Date() - i * 86400000);
            var dateName = (date.getDate()<10?'0'+date.getDate():date.getDate()) + '-' + ((date.getMonth() + 1)<10?'0'+(date.getMonth() + 1):(date.getMonth() + 1));
            labels.push(dateName);
            if (hash[dateName]) {
                data.push(hash[dateName]);
            } else {
                data.push(0);
            }
        }
    }
    if (numOfDays==30) {
        for (var i = numOfDays - 1; i >= 0; i-=4) {
            var date = new Date(new Date() - i * 86400000);
            var dateName = (date.getDate()<10?'0'+date.getDate():date.getDate()) + '-' + ((date.getMonth() + 1)<10?'0'+(date.getMonth() + 1):(date.getMonth() + 1));
            labels.push(dateName);
            if (hash[dateName]) {
                data.push(hash[dateName]);
            } else {
                data.push(0);
            }
        }
    }
    if (numOfDays==60){
        for (var i = numOfDays - 1; i >= 0; i-=8) {
            var date = new Date(new Date() - i * 86400000);
            var dateName = (date.getDate()<10?'0'+date.getDate():date.getDate()) + '-' + ((date.getMonth() + 1)<10?'0'+(date.getMonth() + 1):(date.getMonth() + 1));
            labels.push(dateName);
            if (hash[dateName]) {
                data.push(hash[dateName]);
            } else {
                data.push(0);
            }
        }
    }
    return {
        labels: labels,
        data: data
    }
}

function timeSince(date) {
    var seconds = Math.floor((new Date() - new Date(date)) / 1000);
    var interval = Math.floor(seconds / 31536000);
    if (interval >= 1) {
        return interval + " năm trước";
    }
    interval = Math.floor(seconds / 2592000);
    if (interval >= 1) {
        return interval + " tháng trước";
    }
    interval = Math.floor(seconds / 86400);
    if (interval >= 1) {
        return interval + " ngày trước";
    }
    interval = Math.floor(seconds / 3600);
    if (interval >= 1) {
        return interval + " giờ trước";
    }
    interval = Math.floor(seconds / 60);
    if (interval >= 1) {
        return interval + " phút trước";
    }
    return Math.floor(seconds) + " giây trước";
}
