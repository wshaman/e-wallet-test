$(document).ready(function () {
    $(function () {
        $(".datepicker").datepicker();
    });
    loadClients();
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
    $.getJSON(C.urls.clientList, function (data) {
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
        sel_obj.html();
        for(let i=0; i<data.length; i++){
            sel_obj.append(`<option value="${data[i].id}">${data[i].name}</option>`)
        }
    });
}