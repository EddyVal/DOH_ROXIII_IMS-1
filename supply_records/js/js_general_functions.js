var iss_numbers, tables, fields, isss, iss_fields, rbs;
var _url, active_page;

var control_no, text, table, control;
var prop_no = []; var serial_no = [];
var un_prop_no = []; var un_serial_no = [];

var $po_regex=/^([0-9]{4}-[0-9]{2}-[0-9]{4})|^([0-9]{4}-[0-9]{2}-[0-9]{3})$/;

function exportTableToExcel(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    filename = filename?filename+'.xls':'excel_data.xls';
    downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
}

function view_iss(iss_number, table, field, iss, iss_field, rb){
    $("#view_iss").modal();
    $("#iss").html(iss);
    $("#iss_num").html(iss_number);
    iss_numbers = iss_number; tables = table; fields = field; isss = iss; iss_fields = iss_field; rbs = rb;
    $.ajax({
        type: "POST",
        data: {
            call_func: "get_pic",
            table: table,
            field: field,
            iss_field: iss_field,
            iss_number: iss_number
        },
        url: "php/php_ics.php",
        success: function(data){
            if(iss != "IAR"){
                $("#img_iss").attr("src", "../../archives/"+iss+"/"+iss_number.substring(0,4)+"/"+rb+"/"+data);
            }else{
                $("#img_iss").attr("src", "../../archives/"+iss+"/"+rb+"/"+data);
            }
        }
    });
    
}

$(document).on('change', '.file', function(){
    prepareUpload(event);
});

function prepareUpload(event){
    files = event.target.files;
    uploadFiles(event);
}

function uploadFiles(event) {
    event.stopPropagation();
    event.preventDefault();
    var data = new FormData();
    $.each(files, function(key, value){
        data.append(key, value);
    });
    console.log(data);
    $.ajax({
        url: '../php/upload_iss.php?files&iss_number='+iss_numbers+'&table='+tables+'&field='+fields+'&iss='+isss+'&iss_field='+iss_fields+'&rb='+rbs,
        type: 'POST',
        data: data,
        cache: false,
        processData: false,
        contentType: false,
        success: function(data){
            if(isss != "IAR"){
                $("#img_iss").attr("src", "../../archives/"+isss+"/"+iss_numbers.substring(0,4)+"/"+rbs+"/"+data);
            }else{
                $("#img_iss").attr("src", "../../archives/"+isss+"/"+rbs+"/"+data);
            }
        }
    });
}

function edit_quantity(id,quantity,po_number,item,description, table, field, po_id){
    //alert("PO ID: "+po_id);
    var new_quantity = prompt("Enter new quantity:", quantity);
    $.ajax({
        type: "POST",
        data: {
                call_func: "update_quantity",
                item: item,
                description: description,
                po_number: po_number,
                quantity: quantity,
                new_quantity: new_quantity,
                table: table,
                field: field,
                iss_id: id
            },
        url: "php/php_ics.php",
        success: function(data){
            if(data == "1"){
                swal("Updated!", "Quantity and remaining stocks updated successfully.", "success");
                setTimeout(function () {
                    location.reload();
                  }, 1500);
            }else{
                swal("Invalid!", "Remaining stocks cannot be negative.", "warning");
            }
        }
    });
}

$(document).on('click', '.page-link', function(){
    active_page = $(this).data('page_number');
    var page = $(this).data('page_number');
    var query = $('#search_box').val();
    get_records(page, _url, query);
});

$('#search_box').keyup(function(){
    var query = $('#search_box').val();
    get_records(1, _url, query);
    });

function get_records(page, url, query = ""){
    var arr = ["<div class=\"sk-spinner sk-spinner-wave\"><div class=\"sk-rect1\"></div>&nbsp;<div class=\"sk-rect2\"></div>&nbsp;<div class=\"sk-rect3\"></div>&nbsp;<div class=\"sk-rect4\"></div>&nbsp;<div class=\"sk-rect5\"></div></div>", "<div class=\"sk-spinner sk-spinner-rotating-plane\"></div>", "<div class=\"sk-spinner sk-spinner-double-bounce\"><div class=\"sk-double-bounce1\"></div><div class=\"sk-double-bounce2\"></div></div>", "<div class=\"sk-spinner sk-spinner-wandering-cubes\"><div class=\"sk-cube1\"></div><div class=\"sk-cube2\"></div></div>", "<div class=\"sk-spinner sk-spinner-pulse\"></div>", "<div class=\"sk-spinner sk-spinner-chasing-dots\"><div class=\"sk-dot1\"></div><div class=\"sk-dot2\"></div></div>", "<div class=\"sk-spinner sk-spinner-three-bounce\"><div class=\"sk-bounce1\"></div><div class=\"sk-bounce2\"></div><div class=\"sk-bounce3\"></div></div>", "<div class=\"sk-spinner sk-spinner-circle\"><div class=\"sk-circle1 sk-circle\"></div><div class=\"sk-circle2 sk-circle\"></div><div class=\"sk-circle3 sk-circle\"></div><div class=\"sk-circle4 sk-circle\"></div><div class=\"sk-circle5 sk-circle\"></div><div class=\"sk-circle6 sk-circle\"></div><div class=\"sk-circle7 sk-circle\"></div><div class=\"sk-circle8 sk-circle\"></div><div class=\"sk-circle9 sk-circle\"></div><div class=\"sk-circle10 sk-circle\"></div><div class=\"sk-circle11 sk-circle\"></div><div class=\"sk-circle12 sk-circle\"></div></div>", "<div class=\"sk-spinner sk-spinner-cube-grid\"><div class=\"sk-cube\"></div><div class=\"sk-cube\"></div><div class=\"sk-cube\"></div><div class=\"sk-cube\"></div><div class=\"sk-cube\"></div><div class=\"sk-cube\"></div><div class=\"sk-cube\"></div><div class=\"sk-cube\"></div><div class=\"sk-cube\"></div></div>", "<div class=\"sk-spinner sk-spinner-fading-circle\"><div class=\"sk-circle1 sk-circle\"></div><div class=\"sk-circle2 sk-circle\"></div><div class=\"sk-circle3 sk-circle\"></div><div class=\"sk-circle4 sk-circle\"></div><div class=\"sk-circle5 sk-circle\"></div><div class=\"sk-circle6 sk-circle\"></div><div class=\"sk-circle7 sk-circle\"></div><div class=\"sk-circle8 sk-circle\"></div><div class=\"sk-circle9 sk-circle\"></div><div class=\"sk-circle10 sk-circle\"></div><div class=\"sk-circle11 sk-circle\"></div><div class=\"sk-circle12 sk-circle\"></div></div>"];
    $('#dynamic_content').html(arr[Math.floor(Math.random() * arr.length)]+"<br>");
    $.ajax({
        type: "POST",
        cache: true,
        data: {call_func: "get_records", page: page, search: query},
        url: url,
        success: function(data){
            $('#dynamic_content').html(data);
        }
    });
}

function set_url(url){
    _url = url;
    ready_all();
    get_records(1, _url);
}

function modify_dr(control_no, text, table, control){
    this.control_no = control_no;
    this.text = text;
    this.table = table;
    this.control = control;
    $("#edit_dr").modal();
    $("#control_no").html(text);
    $.ajax({
        type: "POST",
        data: {call_func: "get_dr", control_no: control_no, table: table, control: control},
        url: "php/php_ics.php",
        success: function(data){
            $("#input_date_dr").val(data);
        }
    });
}

function set_dr(){
    $.ajax({
        type: "POST",
        data: {call_func: "set_dr", control_no: this.control_no, table: this.table, control: this.control, new_date_dr: $("#input_date_dr").val()},
        url: "php/php_ics.php",
        success: function(data){
            var query = $('#search_box').val();
            get_records(active_page, _url, query);
        }
    });
}

function upload_alloc(){
    swal("Coming soon!", "The developer is currently working on this feature.", "warning");
}

function update_remarks(id, value, table, field_id){
    $.ajax({
        type: "POST",
        url: "php/php_ics.php",
        data: {call_func: "set_remarks", id: id, value: value, table: table, field_id: field_id},
        success: function(data){
            var query = $('#search_box').val();
            get_records(active_page, _url, query);
        }
    });
}

function get_item_trans(id, table, field_id, field){
    var po_value = (new Date().toDateInputValue()).split("-");
    $.ajax({
        type: "POST",
        url: "php/php_ics.php",
        dataType: "JSON",
        data: {call_func: "get_item_trans", id: id, table: table, field_id: field_id, field: field, yy_mm: po_value[0]+"-"+po_value[1]},
        success: function(data){
            $("#modal_transfer_item").modal();
            $("#trans_item_id").html(id);
            //$("#trans_ics").val(po_value[0]+"-"+po_value[1]+"-"+data["latest_icspar"]);
            $("table#trans_items tbody").html(data["tbody"]);
        }
    });
}

$("#trans_type").change(function(){
    var po_value = (new Date().toDateInputValue()).split("-");
    var arr_data = $("#trans_type").val() == "ICS" ? ["ics_id","ics_no","tbl_ics"] : ["par_id","par_no","tbl_par"];
    $.ajax({
        type: "POST",
        url: "php/php_ics.php",
        data: {
            call_func: "get_ics_par_no",
            yy_mm: po_value[0]+"-"+po_value[1],
            table: arr_data[2],
            field: arr_data[1],
            field_id: arr_data[0]
        },
        success: function(data){
            $("#trans_ics").val(po_value[0]+"-"+po_value[1]+"-"+data);
        }
    });
});

function get_checked_items(){
    prop_no = []; serial_no = [];
    un_prop_no = []; un_serial_no = [];
    var table = $("table#trans_items tbody");
    var rows = 0;
    table.find('tr').each(function (i) {
        var $tds = $(this).find('td');
        if($tds.eq(2).find('input').is(":checked")){
            prop_no.push($tds.eq(0).text());
            serial_no.push($tds.eq(1).text());
            rows++;
        }else{
            un_prop_no.push($tds.eq(0).text());
            un_serial_no.push($tds.eq(1).text());
        }
    });

    return rows;
}

function trans_now(){
    var temp_url = $("#trans_type").val() == "ICS" ? "php/php_ics.php" : "php/php_par.php";
    var arr_data = _url == "php/php_ics.php" ? ["ICS", "tbl_ics", "ics_id", "ics_no"] : ["PAR", "tbl_par", "par_id", "par_no"];
    if($("#trans_ics").val().match($po_regex)){
        if($("#trans_name").val() != null){
            if(get_checked_items() != 0){
                $.ajax({
                    type: "POST",
                    url: temp_url,
                    data: {call_func: "create_trans",
                            id: $("#trans_item_id").html(),
                            trans_ics: $("#trans_ics").val(),
                            trans_name: $("#trans_name option:selected").text(),
                            trans_id: $("#trans_name").val(),
                            prop_no: prop_no,
                            serial_no: serial_no,
                            un_prop_no: un_prop_no,
                            un_serial_no: un_serial_no,
                            date_released: new Date().toDateInputValue(),
                            type: arr_data[0],
                            table: arr_data[1],
                            table_id: arr_data[2],
                            table_no: arr_data[3]
                        },
                    success: function(data){
                        swal("Transferred successfully!", "", "success");
                        $("#modal_transfer_item .close").click();
                        $("#edit_ics_par .close").click();
                        var query = $('#search_box').val();
                        get_records(active_page, _url, query);
                    }
                });
            }else{
                swal("No checked items!", "Kindly check at least one item for ICS/PAR transfer.", "warning");
            }
        }else{
            swal("Please fill in!", "Transfer To (New End-User).", "warning");
        }
    }else{
        swal("Invalid!", "ICS/PAR Number is not valid.", "warning");
    }
}