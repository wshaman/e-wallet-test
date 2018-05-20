$(document).ready(function () {
    $(function () {
        $(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});

    });
    loadClients();
    $("form").submit(function (e) {
        let self = this;
        let url = $(this).attr('action') +'?'+ $(self).serialize();
        console.log(url);
        getjson(url, function (data) {
            putLog(data);
        });
        e.preventDefault();
        return false;
    });
});

const C={
    urls: {
        clientList:"/admin/client/list"
    }
};

function showError(msg){
    alert(msg);
}

function getjson(url, cb){
    $.getJSON(url, function (data) {
        if (typeof data['error'] != 'undefined') {
            showError(data['error'])
        } else {
            cb(data.response);
        }
    })
}

function loadClients(){
    getjson(C.urls.clientList, function(data){
        console.log(data);
        let sel_obj = $('select#user_list');
        sel_obj.html('');
        for(let i=0; i<data.length; i++){
            sel_obj.append(`<option value="${data[i].id}">${data[i].name}</option>`)
        }
    });
}

function putLog(data) {
    console.log(data);
    let tbl_obj = $('table#table-data tbody');
    let tbl_stat = $('table#table-stat tbody tr');
    tbl_obj.html('');
    $('.coin-name').html(data.receiver_coin);
    tbl_stat.find('td').eq(0).html(data.value_coin);
    tbl_stat.find('td').eq(1).html(data.value_usd);
    for(let i=0; i< data.rows.length; i++){
        let itm = data.rows[i];
        tbl_obj.append(`<tr><td>${itm.hash}</td><td>${itm.sender}</td><td>${itm.receiver}</td><td>${itm.amount}</td><td></td></tr>`);
    }
}