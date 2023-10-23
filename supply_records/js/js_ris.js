var items = [];
var $po_regex=/^([0-9]{4}-[0-9]{2}-[0-9]{4})|^([0-9]{4}-[0-9]{2}-[0-9]{3})$/;
var po_details = {};
var lot_no = "", exp_date = "";

$(document).ready(function(){
    
});

Date.prototype.toDateInputValue = (function() {
    var local = new Date(this);
    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
    return local.toJSON().slice(0,10);
});

function origNumber(s){
    return s.split(',').join('');
}

function formatNumber(num) {
  return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
}

function ready_all(){
    $(".select2_demo_1").select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    $("#ris_no").ready(function(){
        $("#date").val(new Date().toDateInputValue());
        var po_value = (new Date().toDateInputValue()).split("-");
        $.ajax({
            type: "POST",
            url: "php/php_ris.php",
            data: {call_func: "get_latest_ris", yy_mm: po_value[0]+"-"+po_value[1]},
            success: function(data){
                $('#ris_no').val(po_value[0]+"-"+po_value[1]+"-"+data);
            }
        });
    });

    $("#requested_by").ready(function(){
        $.ajax({
            type: "POST",
            url: "php/php_ics.php",
            data: {call_func: "get_employee"},
            success: function(data){
                $("#requested_by").html("<option disabled selected></option>").append(data);
            }
        });
    });

    $("#issued_by").ready(function(){
        $.ajax({
            type: "POST",
            url: "php/php_ics.php",
            data: {call_func: "get_employee"},
            success: function(data){
                $("#issued_by").html("<option disabled selected></option>").append(data);
                $('#issued_by option').each(function() {
                    if($(this).text() == $("#ris_no").data("ppb")){
                        $(this).prop("selected", true).change();
                    }
                });
                $("#approved_by").html("<option disabled selected></option>").append(data);
                /*$('#approved_by option').each(function() {
                    if($(this).text() == $("#ris_no").data("pc")){
                        $(this).prop("selected", true).change();
                    }
                });*/
            }
        });
    });

    $("#po_no").ready(function(){
        $.ajax({
            type: "POST",
            url: "php/php_ris.php",
            data: {call_func: "get_po"},
            success: function(data){
                $("#po_no").html("<option disabled selected></option>").append(data);
                $("#po_no option").each(function(){
                    po_details[this.text] = {};
                });
                $("#ris_po_multiple").append(data);
            }
        });
    });

    $("#ris_po_multiple").change(function(){
        $.ajax({
            type: "POST",
            data: {call_func: "get_items_all", po_numbers: $("#ris_po_multiple").val()},
            url: "php/php_ris.php",
            success: function(data){
                $("table#ris_items tbody").html(data);
            }
        });
    });

    $("#po_no").change(function(){
        $("#item_name").val(null).change();
        $("#stock").val("");
        $("#unit").val("");
        $("#description").val("");
        $("#unit_value").val("");
        $("#category").val("");
        $.ajax({
            type: "POST",
            url: "php/php_ris.php",
            data: {call_func: "get_item", po_number: $("#po_no option:selected").text()},
            success: function(data){
               if(data!=""){
                    $("#item_name").html("<option disabled selected></option>").append(data);
                    $("#item_name option").each(function() {
                        if(!po_details[$("#po_no option:selected").text()].hasOwnProperty(this.value)) {
                            po_details[$("#po_no option:selected").text()][this.value] = [this.text, 0, false];
                        }
                    });
                }else{
                    swal("Items are not available!", "Items of this PO are not inspected or maybe out of stocks!", "warning");
                    $("#item_name").html("<option disabled selected></option>").append(data);
                }
            }
        });
    });

    $("#item_name").change(function(){
        //$("#po_no").val($("#item_name option:selected").data("po"));
        $.ajax({
            type: "POST",
            data: {call_func: "get_item_details", item_name: $("#item_name option:selected").text(), po_id: $("#item_name").val()},
            url: "php/php_ris.php",
            dataType: "JSON",
            success: function(data){
                if(po_details[$("#po_no option:selected").text()][$("#item_name").val()][2] == false){
                    po_details[$("#po_no option:selected").text()][$("#item_name").val()][1] = data["quantity"];
                    po_details[$("#po_no option:selected").text()][$("#item_name").val()][2] = true;
                }
                lot_no = data["lot_no"];
                exp_date = data["exp_date"];
                $("#description").val(data["description"]);
                $("#stock").val(po_details[$("#po_no option:selected").text()][$("#item_name").val()][1]);
                $("#unit_value").val(formatNumber(data["unit_cost"]));
                $("#unit").val(data["unit"]);
                $("#category").val(data["category"]);
            }
        });
    });

    $("#division").ready(function(){
        $.ajax({
            type: "POST",
            data: {call_func: "get_division"},
            url: "php/php_ris.php",
            success: function(data){
                $("#division").html("<option disabled selected></option>").append(data);
            }
        });
    });

    $("#division").change(function(){
        $("#office").val(null).change();
        $.ajax({
            type: "POST",
            data: {call_func: "get_division_office", division: $("#division option:selected").text()},
            url: "php/php_ris.php",
            success: function(data){
                $("#office").html("<option disabled selected></option>").append(data);
            }
        });
    });
}

function total_amount(){
    $("#total_amount").val(formatNumber((parseFloat($("#quantity").val() == "" ? 0.00 : $("#quantity").val()) * parseFloat(origNumber($("#unit_value").val()))).toFixed(2)));
}

function validate(){
    if($("#ris_no").val().match($po_regex)){
        if($("#entity_name").val() != ""){
            if($("#division").val() != null){
                if($("#date").val() != ""){
                    if($("#requested_by").val() != null){
                        if($("#issued_by").val() != null){
                            if($("#approved_by").val() != null){
                                if($("#purpose").val() != ""){
                                    if(get_rows1() != 0){
                                        $("#save_changes").attr("disabled", true);
                                        $.ajax({
                                            type: "POST",
                                            data: {call_func: "insert_ris2",
                                                    ris_no: $("#ris_no").val(),
                                                    entity_name: $("#entity_name").val(),
                                                    fund_cluster: $("#fund_cluster").val(),
                                                    division: $("#division option:selected").text(),
                                                    office: $("#office option:selected").text(),
                                                    date: $("#date").val(),
                                                    rcc: $("#rcc").val(),
                                                    requested_by_id: $("#requested_by").val(),
                                                    requested_by: $("#requested_by option:selected").text(),
                                                    issued_by_id: $("#issued_by").val(),
                                                    issued_by: $("#issued_by option:selected").text(),
                                                    approved_by_id: $("#approved_by").val(),
                                                    approved_by: $("#approved_by option:selected").text(),
                                                    purpose: $("#purpose").val(),
                                                    items: items
                                                    },
                                            url: "php/php_ris.php",
                                            success: function(data){
                                                if(data == "0"){
                                                    swal("Inserted!", "Saved successfully to the database.", "success");
                                                    setTimeout(function () {
                                                        location.reload();
                                                      }, 1500);
                                                }else{
                                                    $("#save_changes").attr("disabled", false);
                                                    swal("RIS Number already existed!", "Please enter another RIS number!", "warning");
                                                }
                                            }
                                        });
                                    }else{
                                        swal("No items!", "Please add an item", "warning");
                                    }
                                }else{
                                    swal("Please fill in!", "Purpose", "warning");
                                }
                            }else{
                                swal("Please fill in!", "Approved by", "warning");
                            }
                        }else{
                            swal("Please fill in!", "Issued by", "warning");
                        }
                    }else{
                        swal("Please fill in!", "Requested by", "warning");
                    }
                }else{
                    swal("Please fill in!", "Date", "warning");
                }
            }else{
                swal("Please fill in!", "Division", "warning");
            }
        }else{
            swal("Please fill in!", "Entity name", "warning");
        }
    }else{
        swal("Invalid input!", "RIS Number not valid", "warning");
    }
}

$('#ris_items').on('click', 'tbody tr button', function(event) {
    event.preventDefault();
    po_details[$(this).attr('id')][$(this).val()][1] = po_details[$(this).attr('id')][$(this).val()][1] + parseInt($(this).data("quan"));
    $(this).parents('tr').remove();
});

function add_item(){
    if($("#item_name").val() != null){
        if($("#quantity").val() != ""){
            if(parseInt($("#quantity").val()) <= po_details[$("#po_no option:selected").text()][$("#item_name").val()][1]){
                if(parseInt($("#quantity").val()) > 0){
                    $("table#ris_items tbody").append("<tr>"+
                        "<td>"+$("#item_name").val()+"</td>"+
                        "<td>"+$("#po_no").val()+"</td>"+
                        "<td>"+$("#item_name option:selected").text()+"</td>"+
                        "<td>"+$("#description").val()+"</td>"+
                        "<td>"+$("#category").val()+"</td>"+
                        "<td>"+lot_no+"</td>"+
                        "<td>"+exp_date+"</td>"+
                        "<td>"+$("#quantity").val()+"</td>"+
                        "<td>"+$("#unit").val()+"</td>"+
                        "<td>"+$("#unit_value").val()+"</td>"+
                        "<td>"+$("#total_amount").val()+"</td>"+
                        "<td>"+$("#stock").val()+"</td>"+
                        "<td>"+$("#remarks").val()+"</td>"+
                        "<td><button class=\"btn btn-danger btn-xs\" id=\""+$("#po_no option:selected").text()+"\" value=\""+$("#item_name").val()+"\" data-quan=\""+$("#quantity").val()+"\"><i class=\"fa fa-trash\"></i></button></td>"+
                        "</tr>");
                    var rs = po_details[$("#po_no option:selected").text()][$("#item_name").val()][1] - parseInt($("#quantity").val());
                    po_details[$("#po_no option:selected").text()][$("#item_name").val()][1] = rs;
                    $("#item_name").val(null).change();
                    $("#description").val("");
                    $("#quantity").val("");
                    $("#unit").val("");
                    $("#unit_value").val("");
                    $("#total_amount").val("");
                    $("#remarks").val("");
                    $("#stock").val("");
                    $("#category").val("");
                    lot_no = "";
                    exp_date = "";
                }else{
                    swal("Invalid input!", "Quantity cannot be zero or negative", "warning");    
                }
            }else{
                swal("Invalid input!", "Quantity is greater than remaining stocks", "warning"); 
            }
        }else{
            swal("Please fill in!", "Quantity", "warning"); 
        }
    }else{
        swal("Please fill in!", "Item name", "warning"); 
    }
}

function get_rows(){
    items = [];
    var table = $("table#ris_items tbody");
    var rows = 0;
    table.find('tr').each(function (i) {
        var $tds = $(this).find('td');
        items.push([$tds.eq(0).text(),$tds.eq(1).text(),$tds.eq(2).text(),$tds.eq(3).text(),$tds.eq(4).text(),$tds.eq(5).text(),$tds.eq(6).text(),$tds.eq(7).text(),origNumber($tds.eq(8).text()),origNumber($tds.eq(9).text()),$tds.eq(10).text(),$tds.eq(11).text(),$tds.eq(12).text()]);
        rows++;
    });
    return rows;
}

function get_rows1(){
    items = [];
    var table = $("table#ris_items tbody");
    var rows = 0;
    table.find('tr').each(function (i) {
        var $tds = $(this).find('td');
        if(parseInt($tds.eq(8).find('input').val()) > 0){
            items.push([$tds.eq(0).text(),$tds.eq(1).text(),$tds.eq(2).text(),$tds.eq(3).text(),$tds.eq(4).text(),$tds.eq(5).text(),$tds.eq(6).text(),$tds.eq(7).text(),$tds.eq(8).find('input').val(),$tds.eq(9).text(),$tds.eq(10).text(),$tds.eq(11).text(),$tds.eq(12).text()]);
            rows++;
        }
    });
    return rows;
}

function to_issue(ris_no, ref_no){
    $.ajax({
        type: "POST",
        data: {call_func: "iss_validator", ris_no: ris_no},
        url: "php/php_ris.php",
        success: function(data){
            if(data == "1"){
                swal({
                    title: "Are you sure?",
                    text: "This RIS record will be issued as soon as you clicked 'Yes'",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    $.ajax({
                        type: "POST",
                        data: {call_func: "to_issue", ris_no: ris_no},
                        url: "php/php_ris.php",
                        success: function(data){
                            swal("Issued!", "The items on RIS No. "+ris_no+" is now issued.", "success");
                            var query = $('#search_box').val();
                            get_records(active_page, _url, query);
                        }
                    });
                });        
            }else{
                 swal("Please upload first the scanned copy of this RIS record.","", "error");
            }
        }
    });

}

function modify(ris_no){
    $("#edit_ris").modal();
    $("#eris_no").val(ris_no);
    $.ajax({
        type: "POST",
        data: {
            call_func: "modify",
            ris_no: ris_no
        },
        dataType: "JSON",
        url: "php/php_ris.php",
        success: function(data){
            $("#eentity_name").val(data["entity_name"]);
            $("#edivision").val(data["division"]);
            $("#eoffice").val(data["office"]);
            $("#edate").val(data["date"]);
            $("#efund_cluster").val(data["fund_cluster"]);
            $("#ercc").val(data["rcc"]);
            $("#erequested_by").val(data["requested_by"]);
            $("#erequested_by_designation").val(data["requested_by_designation"]);
            $("#eissued_by").val(data["issued_by"]);
            $("#eissued_by_designation").val(data["issued_by_designation"]);
            $("#eapproved_by").val(data["approved_by"]);
            $("#eapproved_by_designation").val(data["approved_by_designation"]);
            $("#epurpose").val(data["purpose"]);
            $("table#eris_items tbody").html(data["table"]);
        }
    });
}

function update(){
    $.ajax({
        type: "POST",
        data: {
            call_func: "update",
            ris_no: $("#eris_no").val(),
            entity_name: $("#eentity_name").val(),
            division: $("#edivision").val(),
            office: $("#eoffice").val(),
            date: $("#edate").val(),
            fund_cluster: $("#efund_cluster").val(),
            rcc: $("#ercc").val(),
            requested_by: $("#erequested_by").val(),
            requested_by_designation: $("#erequested_by_designation").val(),
            issued_by: $("#eissued_by").val(),
            issued_by_designation: $("#eissued_by_designation").val(),
            approved_by: $("#eapproved_by").val(),
            approved_by_designation: $("#eapproved_by_designation").val(),
            purpose: $("#epurpose").val()
        },
        url: "php/php_ris.php",
        success: function(data){
            swal("Updated!", "RIS details successfully updated.", "success");
            setTimeout(function () {
                location.reload();
              }, 1500);
        }
    });
}

function delete_control(ris_no){
    swal({
        title: "Are you sure?",
        text: "This RIS record will be removed from the database.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: false
    }, function () {
        $.ajax({
            type: "POST",
            data: {call_func: "delete",
                    field: "ris_no",
                    table: "tbl_ris",
                    number: ris_no
                },
            url: "php/php_ris.php",
            success: function(data){
                swal("Deleted!", "The RIS No. "+ris_no+" is now deleted.", "success");
                setTimeout(function () {
                    location.reload();
                  }, 1500);
            }
        });
    });
}
function validate_input_quantity(element, stocks, i, uc){
    if(parseInt(element.value) > stocks){    
        element.value = "";
        $("#totid"+i).html("");
        return
    }
    $("#totid"+i).html(parseInt(element.value) * parseInt(uc));
}

function print_ris(ris_no){
    $.ajax({
        type: "POST",
        data: {call_func: "print_ris", ris_no: ris_no},
        dataType: "JSON",
        url: "php/php_ris.php",
        success: function(data){
            $("#print_en").html(data["entity_name"]);
            $("#print_fc").html(data["fund_cluster"]);
            $("#print_div").html(data["division"]);
            $("#print_rcc").html(data["rcc"]);
            $("#print_off").html(data["office"]);
            $("#print_risno").html(ris_no);
            $("#ris_body").html(data["tbody"]);
            $("#print_purp").html(data["purpose"]);
            $("#print_rb").html(data["requested_by"].toUpperCase());
            $("#print_rbd").html(data["requested_by_designation"].toUpperCase());
            $("#print_ib").html(data["issued_by"].toUpperCase());
            $("#print_ibd").html(data["issued_by_designation"].toUpperCase());
            $("#print_ab").html(data["approved_by"].toUpperCase());
            $("#print_abd").html(data["approved_by_designation"].toUpperCase());
            $("#print_tc").html(data["total_cost"]);
            $(".print_date").html(data["date"]);

            var divContents = $("#report_ris").html(); 
            var a = window.open('', '_blank', 'height=1500, width=800'); 
            a.document.write('<html>');
            a.document.write('<body><center>');
            a.document.write('<table><tr>');
            a.document.write('<td>'+divContents+'</td>'); 
            a.document.write('</tr></table>');
            a.document.write('</center></body></html>'); 
            a.document.close(); 
            a.print();
        }
    });
}

function download_xls(ris_no){
    $.ajax({
        type: "POST",
        data: {call_func: "print_ris", ris_no: ris_no},
        dataType: "JSON",
        url: "php/php_ris.php",
        success: function(data){
           $("#print_en").html(data["entity_name"]);
            $("#print_fc").html(data["fund_cluster"]);
            $("#print_div").html(data["division"]);
            $("#print_rcc").html(data["rcc"]);
            $("#print_off").html(data["office"]);
            $("#print_risno").html(ris_no);
            $("#ris_body").html(data["tbody"]);
            $("#print_purp").html(data["purpose"]);
            $("#print_rb").html(data["requested_by"].toUpperCase());
            $("#print_ib").html(data["issued_by"].toUpperCase());
            $("#print_ibd").html(data["issued_by_designation"].toUpperCase());
            $("#print_ab").html(data["approved_by"].toUpperCase());
            $("#print_abd").html(data["approved_by_designation"].toUpperCase());
            $("#print_tc").html(data["total_cost"]);
            $(".print_date").html(data["date"]);

            exportTableToExcel("report_ris", "RIS No. "+ris_no);
        }
    });
}

function print_ris_dm(ris_no){
    $.ajax({
        type: "POST",
        data: {call_func: "print_ris_dm", ris_no: ris_no},
        dataType: "JSON",
        url: "php/php_ris.php",
        success: function(data){
            $("#dprint_en").html(data["entity_name"]);
            $("#dprint_fc").html(data["fund_cluster"]);
            $("#dprint_div").html(data["division"]);
            $("#dprint_rcc").html(data["rcc"]);
            $("#dprint_off").html(data["office"]);
            $("#dprint_risno").html(ris_no);
            $("#dris_body").html(data["tbody"]);
            $("#dprint_purp").html(data["purpose"]);
            $("#dprint_rb").html(data["requested_by"].toUpperCase());
            $("#dprint_rbd").html(data["requested_by_designation"].toUpperCase());
            $("#dprint_ib").html(data["issued_by"].toUpperCase());
            $("#dprint_ibd").html(data["issued_by_designation"].toUpperCase());
            $("#dprint_ab").html(data["approved_by"].toUpperCase());
            $("#dprint_abd").html(data["approved_by_designation"].toUpperCase());
            $("#dprint_tc").html(data["total_cost"]);
            $(".dprint_date").html(data["date"]);

            $("#ris_preview").modal();
        }
    });
}

function add_ris_rows(){
    $("#dris_body").append("<tr>"+
          "<td style=\"width: 50.4px; height: 18px; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>"+
          "<td style=\"width: 35.4px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>"+
          "<td style=\"width: 153.6px; height: 18px; text-align: left; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>"+
          "<td style=\"width: 54.6px; height: 18px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>"+
          "<td style=\"width: 37.2px; height: 18px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>"+
          "<td style=\"width: 34.2px; height: 18px; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>"+
          "<td style=\"width: 37.8px; height: 18px; font-size: 9px; text-align: center; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>"+
          "<td style=\"width: 45.6px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>"+
          "<td style=\"width: 52.2px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>"+
          "<td style=\"width: 78.6px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>"+
          "<td style=\"width: 50.4px; height: 18px; text-align: center; font-size: 10px;vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>"+
          "<td style=\"width: 109.8px; height: 18px; text-align: center; font-size: 10px;vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>"+
        "</tr>");
}

function download_xls_dm(ris_no){
    alert("Downloading RIS-"+ris_no+"....");
}