var items = [];
var $po_regex=/^([0-9]{4}-[0-9]{2}-[0-9]{4})|^([0-9]{4}-[0-9]{2}-[0-9]{3})$/;
var po_details = {};

var ttype = null;
var ettype = null;
var exp_date = "";

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

function total_amount(){
    $("#total_amount").val(formatNumber((parseFloat($("#quantity").val() == "" ? 0.00 : $("#quantity").val()) * parseFloat(origNumber($("#unit_value").val()))).toFixed(2)));
}

$('#ptr_items').on('click', 'tbody tr button', function(event) {
    event.preventDefault();
    po_details[$(this).attr('id')][$(this).val()][1] = po_details[$(this).attr('id')][$(this).val()][1] + parseInt($(this).data("quan"));
    $(this).parents('tr').remove();
});

function get_rows(){
    items = [];
    var table = $("table#ptr_items tbody");
    var rows = 0;
    table.find('tr').each(function (i) {
        var $tds = $(this).find('td');
        items.push([$tds.eq(0).text(),$tds.eq(1).text(),$tds.eq(2).text(),$tds.eq(3).text(),$tds.eq(4).text(),$tds.eq(5).text(),$tds.eq(6).text(),$tds.eq(7).text(),$tds.eq(8).text(),$tds.eq(9).text(),origNumber($tds.eq(10).text()),origNumber($tds.eq(11).text()),$tds.eq(12).text(),$tds.eq(13).text()]);
        rows++;
    });
    return rows;
}

function validate(){
    if($("#ptr_no").val().match($po_regex)){
        if($("#from").val() != ""){
            if($("#entity_name").val() != ""){
                if($("#approved_by").val() != null){
                        if($("#date").val() != ""){
                            if(ttype != null){
                                if($("#reference_no").val() != null){
                                    if($("#to").val() != ""){
                                        if($("#received_from").val() != null){
                                            if($("#area").val() != null){
                                                if($("#reason").val() != ""){
                                                    if(get_rows() != 0){
                                                        $("#save_changes").attr("disabled", true);
                                                        $.ajax({
                                                            type: "POST",
                                                            data: 
                                                                { 
                                                                    call_func: "insert_ptr",
                                                                    ptr_no: $("#ptr_no").val(),
                                                                    from: $("#from").val(),
                                                                    entity_name: $("#entity_name").val(),
                                                                    approved_by_id: $("#approved_by").val(),
                                                                    approved_by: $("#approved_by option:selected").text(),
                                                                    date_released: $("#date").val(),
                                                                    transfer_type:ttype,
                                                                    reference_no: $("#reference_no option:selected").text(),
                                                                    fund_cluster: $("#fund_cluster").val(),
                                                                    to: $("#to").val(),
                                                                    received_from_id: $("#received_from").val(),
                                                                    received_from: $("#received_from option:selected").text(),
                                                                    area: $("#area option:selected").text(),
                                                                    reason: $("#reason").val(),
                                                                    address: $("#address").val(),
                                                                    items: items,
                                                                    alloc_num: $("#alloc_num").val(),
                                                                    storage_temp: $("#storage_temp option:selected").text(),
                                                                    transport_temp: $("#transport_temp option:selected").text()
                                                                },
                                                            url: "php/php_ptr.php",
                                                            success: function(data){
                                                                if(data == "0"){
                                                                    swal("Inserted!", "Saved successfully to the database.", "success");
                                                                    setTimeout(function () {
                                                                        location.reload();
                                                                      }, 1500);
                                                                }else{
                                                                    $("#save_changes").attr("disabled", false);
                                                                    swal("PTR Number already existed!", "Please enter another PTR number!", "warning");
                                                                }
                                                            }
                                                        });
                                                    }else{
                                                        swal("Please fill in!", "Please add an item!", "warning");
                                                    }
                                                }else{
                                                    swal("Please fill in!", "Reason", "warning");
                                                }
                                            }else{
                                                swal("Please fill in!", "Address", "warning");
                                            }
                                        }else{
                                            swal("Please fill in!", "Received from", "warning");
                                        }
                                    }else{
                                        swal("Please fill in!", "To", "warning");
                                    }
                                }else{
                                    swal("Please fill in!", "Reference number", "warning");
                                }
                            }else{
                                swal("Please fill in!", "Transfer Type", "warning");
                            }
                        }else{
                            swal("Please fill in!", "Date released", "warning");
                        }
                }else{
                    swal("Please fill in!", "Approved by", "warning");
                }
            }else{
                swal("Please fill in!", "Entity name", "warning");
            }
        }else{
            swal("Please fill in!", "From", "warning");
        }
    }else{
        swal("Invalid input!", "PTR Number not valid", "warning");
    }
}

function add_item(){
    if($("#item_name").val() != null){
        if($("#category").val() != null){
            if($("#quantity").val() != ""){
                if(parseInt($("#quantity").val()) <= po_details[$("#reference_no option:selected").text()][$("#item_name").val()][1]){
                    if(parseInt($("#quantity").val()) > 0){
                        if($("#serial_no").val() == ""){
                            check_pn_exist($("#property_no").val());
                        }else{
                            validate_with_snln();
                        }
                    }else{
                        swal("Invalid input!", "Quantity can't be zero or negative", "warning");
                    }
                }else{
                    swal("Invalid input!", "Quantity is greater than remaining stocks", "warning"); 
                }
            }else{
                swal("Please fill in!", "Quantity", "warning"); 
            }
        }else{
            swal("Please fill in!", "Category", "warning"); 
        }
    }else{
        swal("Please fill in!", "Item Name", "warning");    
    }
}

function validate_with_snln(){
    if($("#category").val() != "Drugs and Medicines" && $("#category").val() != "Medical Supplies"){
        if(parseInt($("#quantity").val()) == $('#serial_no').select2("val").length){
            check_pn_exist($("#property_no").val());
        }else{
            swal("Quantity not matched!", "Number of serial numbers selected should correspond to the inputted quantity.", "warning");
        }
    }else{
       check_pn_exist($("#property_no").val());
    }
}

function check_pn_exist(pn_){
    $.ajax({
        type: "POST",
        url: "php/php_ics.php",
        data: {
            call_func: "check_pn_exist",
            pn_: pn_
        },
        success: function(data){
            if(data == ""){
                $("table#ptr_items tbody").append("<tr>"+
                "<td>"+$("#item_name").val()+"</td>"+
                "<td>"+$("#reference_no option:selected").text()+"</td>"+
                "<td>"+$("#item_name option:selected").text()+"</td>"+
                "<td>"+$("#description").val()+"</td>"+
                "<td>"+$("#serial_no").val()+"</td>"+
                "<td>"+exp_date+"</td>"+
                "<td>"+$("#category").val()+"</td>"+
                "<td>"+$("#property_no").val()+"</td>"+
                "<td>"+$("#quantity").val()+"</td>"+
                "<td>"+$("#unit").val()+"</td>"+
                "<td>"+$("#unit_value").val()+"</td>"+
                "<td>"+$("#total_amount").val()+"</td>"+
                "<td>"+$("#conditions").val()+"</td>"+
                "<td>"+$("#remarks").val()+"</td>"+
                "<td><button class=\"btn btn-danger btn-xs\" id=\""+$("#reference_no option:selected").text()+"\" value=\""+$("#item_name").val()+"\" data-quan=\""+$("#quantity").val()+"\"><i class=\"fa fa-trash\"></i></button></td>"+
                "</tr>");
                var rs = po_details[$("#reference_no option:selected").text()][$("#item_name").val()][1] - parseInt($("#quantity").val());
                po_details[$("#reference_no option:selected").text()][$("#item_name").val()][1] = rs;
                $("#item_name").val(null).change();
                $("#stock").val("");
                $("#unit").val("");
                $("#description").val("");
                $("#unit_value").val("");
                $("#quantity").val("");
                $("#serial_no").val(null).change();
                $("#property_no")[0].selectize.clear();
                $("#remarks").val("");
                $("#category").val("");
                $("#conditions").val("");
                $("#total_amount").val("");
            }else{
                swal("Property Number Exists!", data, "warning");
            }
        }
    });
}

function ready_all(){
    $(".select2_demo_1").select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    $("#ptr_no").ready(function(){
        $("#date").val(new Date().toDateInputValue());
        var po_value = (new Date().toDateInputValue()).split("-");
        $.ajax({
            type: "POST",
            dataType: "JSON",
            url: "php/php_ptr.php",
            data: {call_func: "get_latest_ptr", yy_mm: po_value[0]+"-"+po_value[1]},
            success: function(data){
                //alert(data["latest_ptr"]);
                $('#ptr_no').val(po_value[0]+"-"+po_value[1]+"-"+data["latest_ptr"]);
                $('#lbl_pn').html(po_value[0]+"-"+po_value[1]+"-"+data["latest_pn"]);
            }
        });
    });

    $("#area").ready(function(){
        $.ajax({
            type: "POST",
            url: "php/php_ics.php",
            data: {call_func: "get_area"},
            success: function(data){
                $("#area").html("<option disabled selected></option>").append(data);
                $("#earea").html("<option disabled selected></option>").append(data);
            }
        });
    });

    $("#received_from").ready(function(){
        $.ajax({
            type: "POST",
            url: "php/php_ics.php",
            data: {call_func: "get_employee"},
            success: function(data){
                $("#received_from").html("<option disabled selected></option>").append(data);
                $('#received_from option').each(function() {
                    if($(this).text() == $("#ptr_no").data("pc")){
                        $(this).prop("selected", true).change();
                    }
                });
            }
        });
    });

    $("#approved_by").ready(function(){
        $.ajax({
            type: "POST",
            url: "php/php_ics.php",
            data: {call_func: "get_employee"},
            success: function(data){
                $("#approved_by").html("<option disabled selected></option>").append(data);
                $('#approved_by option').each(function() {
                    if($(this).text() == $("#ptr_no").data("ch")){
                        $(this).prop("selected", true).change();
                    }
                });
            }
        });
    });

    $("#reference_no").ready(function(){
        $.ajax({
            type: "POST",
            data: {call_func: "get_po", action: "get_number", po_type: "gen"},
            url: "php/php_iar.php",
            success: function(data){
                $("#reference_no").html("<option disabled selected></option>").append(data);
                $("#reference_no option").each(function() {
                    po_details[this.text] = {};
                });
            }
        });
    });

    $("#reference_no").change(function(){
        $("#item_name").val(null).change();
        $("#stock").val("");
        $("#category").val("");
        $("#unit").val("");
        $("#description").val("");
        $("#unit_value").val("");
        $("#conditions").val("");
        $("#serial_no").val(null).change();
        $.ajax({
            type: "POST",
            data: {call_func: "get_item", po_number: $("#reference_no option:selected").text()},
            url: "php/php_ics.php",
            success: function(data){
                if(data!=""){
                    $("#item_name").html("<option disabled selected></option>").append(data);
                    $("#item_name option").each(function() {
                        if(!po_details[$("#reference_no option:selected").text()].hasOwnProperty(this.value)) {
                            po_details[$("#reference_no option:selected").text()][this.value] = [this.text, 0, false];
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
        $.ajax({
            type: "POST",
            data: {call_func: "get_item_details", item_id: $("#item_name").val(), po_number: $("#reference_no option:selected").text()},
            url: "php/php_ics.php",
            dataType: "JSON",
            success: function(data){
                if(po_details[$("#reference_no option:selected").text()][$("#item_name").val()][2] == false){
                    po_details[$("#reference_no option:selected").text()][$("#item_name").val()][1] = data["stocks"];
                    po_details[$("#reference_no option:selected").text()][$("#item_name").val()][2] = true;
                }
                $("#stock").val(po_details[$("#reference_no option:selected").text()][$("#item_name").val()][1]);
                $("#unit").val(data["unit"]);
                $("#description").val(data["description"]);
                $("#unit_value").val(formatNumber(data["unit_cost"]));
                $("#category").val(data["category"]);
                $("#serial_no").html("").append(data["sn_ln"]);
                exp_date = data["exp_date"];
            }
        });
    });

    $("#transfer_type").change(function(){
        if($("#transfer_type option:selected").text() == "Others"){
            var person = prompt("Please specify", "");
            if(person != null){
                ttype = person;
            }
        }else{
            ttype = $("#transfer_type option:selected").text();
        }
    });

    $("#etransfer_type").change(function(){
        if($("#etransfer_type option:selected").text() == "Others"){
            var person = prompt("Please specify", "");
            if(person != null){
                ettype = person;
            }
        }else{
            ettype = $("#etransfer_type option:selected").text();
        }
    });

    $('#property_no').selectize({
        plugins: ['remove_button'],
        delimiter: ',',
        persist: false,
        create: function(input) {
            return {
                value: input,
                text: input
            }
        }
    });
}

function to_issue(ptr_no, ref_no){
    $.ajax({
        type: "POST",
        data: {call_func: "iss_validator", ptr_no: ptr_no},
        url: "php/php_ptr.php",
        success: function(data){
            if(data == "1"){
                swal({
                    title: "Are you sure?",
                    text: "This PTR record will be issued as soon as you clicked 'Yes'",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    $.ajax({
                        type: "POST",
                        data: {call_func: "to_issue", ptr_no: ptr_no},
                        url: "php/php_ptr.php",
                        success: function(data){
                            swal("Issued!", "The items on PTR No. "+ptr_no+" is now issued.", "success");
                            var query = $('#search_box').val();
                            get_records(active_page, _url, query);
                        }
                    });
                });
            }else{
                swal("Please upload first the scanned copy of this PTR record.","", "error");
            }
        }
    });
}

function modify(ptr_no){
    $("#edit_ptr").modal();
    $("#eptr_no").val(ptr_no);
    $.ajax({
        type: "POST",
        data: {call_func: "modify",
            ptr_no: ptr_no
        },
        url: "php/php_ptr.php",
        dataType: "JSON",
        success: function(data){
            $("#efrom").val(data["from"]);
            $("#eentity_name").val(data["entity_name"]);
            $("#eapproved_by").val(data["approved_by"]);
            $("#eabd").val(data["approved_by_designation"]);
            $("#edate").val(data["date_released"]);
            $('#etransfer_type option').each(function() {
                if($(this).text() == data["transfer_type"]){
                    $(this).prop("selected", true).change();
                }
            });
            $("#ereference_no").val(data["reference_no"]);
            $("#eto").val(data["to"]);
            $("#efund_cluster").val(data["fund_cluster"]);
            $("#ereceived_from").val(data["received_from"]);
            $("#erfd").val(data["received_from_designation"]);
            $('#earea option').each(function() {
                if($(this).text() == data["area"]){
                    $(this).prop("selected", true).change();
                }
            });
            $("#ereason").val(data["reason"]);
            $("#eaddress").val(data["address"]);
            $("table#eptr_items tbody").html(data["table"]);
            $("#ealloc_num").val(data["alloc_num"]);
            $("#estorage_temp option").each(function(){
                if($(this).text() == data["storage_temp"]){
                    $(this).prop("selected", true).change();
                }
            });
            $("#etransport_temp option").each(function(){
                if($(this).text() == data["transport_temp"]){
                    $(this).prop("selected", true).change();
                }
            });
        }
    });
}

function update(){
    $.ajax({
        type: "POST",
        data: {
            call_func: "update",
            ptr_no: $("#eptr_no").val(),
            from: $("#efrom").val(),
            entity_name: $("#eentity_name").val(),
            approved_by: $("#eapproved_by").val(),
            approved_by_designation: $("#eabd").val(),
            date_released: $("#edate").val(),
            //transfer_type: $("#etransfer_type option:selected").text(),
            ttype: ettype,
            to: $("#eto").val(),
            fund_cluster: $("#efund_cluster").val(),
            received_from: $("#ereceived_from").val(),
            received_from_designation: $("#erfd").val(),
            area: $("#earea option:selected").text(),
            reason: $("#ereason").val(),
            address: $("#eaddress").val(),
            alloc_num: $("#ealloc_num").val(),
            storage_temp: $("#estorage_temp option:selected").text(),
            transport_temp: $("#etransport_temp option:selected").text()
        },
        url: "php/php_ptr.php",
        success: function(data){
            swal("Updated!", "PTR details successfully updated.", "success");
            setTimeout(function () {
                location.reload();
              }, 1500);
        }
    });
}

function delete_control(ptr_no){
    swal({
        title: "Are you sure?",
        text: "This PTR record will be removed from the database.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: false
    }, function () {
        $.ajax({
            type: "POST",
            data: {call_func: "delete",
                    field: "ptr_no",
                    table: "tbl_ptr",
                    number: ptr_no
                },
            url: "php/php_ics.php",
            success: function(data){
                swal("Deleted!", "The PTR No. "+ptr_no+" is now deleted.", "success");
                setTimeout(function () {
                    location.reload();
                  }, 1500);
            }
        });
    });
}

function reset_paper(){
    var div_names = ["Donation","Relocate","Reassignment","Others"];
    for(var i = 0; i < div_names.length; i++){
        $("#"+div_names[i]).attr("checked",false);
        $("#g"+div_names[i]).attr("checked",false);
    }
    $("#print_specify").html("");
    $("#gprint_specify").html("");
}

function print_ptr(ptr_no){
    reset_paper();
    $.ajax({
        type: "POST",
        data: {call_func: "get_ptr_details", ptr_no: ptr_no},
        url: "php/php_ptr.php",
        dataType: "JSON",
        success: function(data){
            $("#ptr_tbody").html(data["ptr_tbody"]);
            $("#print_en").html(data["ptr_details"][0]);
            $("#print_fc").html(data["ptr_details"][1]);
            $("#print_from").html(data["ptr_details"][2]);
            $("#print_ptrno").html(ptr_no);
            $("#print_to").html(data["ptr_details"][3]);
            $("#print_date").html(data["ptr_details"][4]);
            $("#print_tc").html(data["ptr_details"][6]);
            $("#print_reason").html(data["ptr_details"][7]);
            $("#print_approved_by").html("<u>"+data["ptr_details"][8].toUpperCase()+"</u>");
            $("#print_approved_by_designation").html(data["ptr_details"][9]);
            //$("#print_received_from").html("<u>"+data["ptr_details"][10].toUpperCase()+"</u>");
            //$("#print_received_from_designation").html(data["ptr_details"][11]);
            $(".date_r").html(data["ptr_details"][4]);
            if(data["ptr_details"][5] != "Donation" && data["ptr_details"][5] != "Relocate" && data["ptr_details"][5] != "Reassignment"){
                $("input#Others").attr("checked", "checked");
                $("#print_specify").html(data["ptr_details"][5]);
            }else{
                $("input#"+data["ptr_details"][5]).attr("checked", "checked");
            }

            $("#palloc_num").html(data["alloc_num"]);
            $("#pstorage_temp").html(data["storage_temp"]);
            $("#ptransport_temp").html(data["transport_temp"]);
            var divContents = $("#report_ptr").html(); 
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

function download_xls(ptr_no){
    reset_paper();
    $.ajax({
        type: "POST",
        data: {call_func: "get_ptr_details", ptr_no: ptr_no},
        url: "php/php_ptr.php",
        dataType: "JSON",
        success: function(data){
            $("#ptr_tbody").html(data["ptr_tbody"]);
            $("#print_en").html(data["ptr_details"][0]);
            $("#print_fc").html(data["ptr_details"][1]);
            $("#print_from").html(data["ptr_details"][2]);
            $("#print_ptrno").html(ptr_no);
            $("#print_to").html(data["ptr_details"][3]);
            $("#print_date").html(data["ptr_details"][4]);
            $("#print_tc").html(data["ptr_details"][6]);
            $("#print_reason").html(data["ptr_details"][7]);
            $("#print_approved_by").html("<u>"+data["ptr_details"][8].toUpperCase()+"</u>");
            $("#print_approved_by_designation").html(data["ptr_details"][9]);
            $("#print_received_from").html("<u>"+data["ptr_details"][10].toUpperCase()+"</u>");
            $("#print_received_from_designation").html(data["ptr_details"][11]);
            $(".date_r").html(data["ptr_details"][4]);
            if(data["ptr_details"][5] != "Donation" && data["ptr_details"][5] != "Relocate" && data["ptr_details"][5] != "Reassignment"){
                $("input#Others").attr("checked", "checked");
                $("#print_specify").html(data["ptr_details"][5]);
            }else{
                $("input#"+data["ptr_details"][5]).attr("checked", "checked");
            }
            $("#palloc_num").html(data["alloc_num"]);
            $("#pstorage_temp").html(data["storage_temp"]);
            $("#ptransport_temp").html(data["transport_temp"]);
            
            exportTableToExcel("report_ptr", "PTR No. "+ptr_no);
        }
    });
}

function print_ptr_gen(ptr_no){
    reset_paper();
    $.ajax({
        type: "POST",
        data: {call_func: "print_ptr_gen", ptr_no: ptr_no},
        url: "php/php_ptr.php",
        dataType: "JSON",
        success: function(data){
            $("#gen_tbody").html(data["ptr_tbody"]);
            $("#gprint_en").html(data["ptr_details"][0]);
            $("#gprint_fc").html(data["ptr_details"][1]);
            $("#gprint_from").html(data["ptr_details"][2].toUpperCase());
            $("#gprint_ptrno").html(ptr_no);
            $("#gprint_to").html(data["ptr_details"][3].toUpperCase());
            $("#gprint_date").html(data["ptr_details"][4]);
            $("#gprint_ta").html(data["ptr_details"][6]);
            $("#gprint_reason").html(data["ptr_details"][7]);
            $("#gprint_ab").html("<u>"+data["ptr_details"][8].toUpperCase()+"</u>");
            $("#gprint_abd").html(data["ptr_details"][9]);
            $("#gprint_rb").html("<u>"+data["ptr_details"][10].toUpperCase()+"</u>");
            $("#gprint_rbd").html(data["ptr_details"][11]);
			$("#addr").html(data["ptr_details"][12]);
            $(".gdate").html(data["ptr_details"][4]);
            if(data["ptr_details"][5] != "Donation" && data["ptr_details"][5] != "Relocate" && data["ptr_details"][5] != "Reassignment"){
                $("input#gOthers").attr("checked", "checked");
                $("#gprint_specify").html(data["ptr_details"][5]);
            }else{
                $("input#g"+data["ptr_details"][5]).attr("checked", "checked");
            }

            var divContents = $("#report_ptr_gen").html(); 
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

function download_xls_gen(ptr_no){
    reset_paper();
    $.ajax({
        type: "POST",
        data: {call_func: "print_ptr_gen", ptr_no: ptr_no},
        url: "php/php_ptr.php",
        dataType: "JSON",
        success: function(data){
            $("#gen_tbody").html(data["ptr_tbody"]);
            $("#gprint_en").html(data["ptr_details"][0]);
            $("#gprint_fc").html(data["ptr_details"][1]);
            $("#gprint_from").html(data["ptr_details"][2].toUpperCase());
            $("#gprint_ptrno").html(ptr_no);
            $("#gprint_to").html(data["ptr_details"][3].toUpperCase());
            $("#gprint_date").html(data["ptr_details"][4]);
            $("#gprint_ta").html(data["ptr_details"][6]);
            $("#gprint_reason").html(data["ptr_details"][7]);
            $("#gprint_ab").html("<u>"+data["ptr_details"][8].toUpperCase()+"</u>");
            $("#gprint_abd").html(data["ptr_details"][9]);
            $("#gprint_rb").html("<u>"+data["ptr_details"][10].toUpperCase()+"</u>");
            $("#gprint_rbd").html(data["ptr_details"][11]);
            $("#addr").html(data["ptr_details"][12]);
            $(".gdate").html(data["ptr_details"][4]);
            if(data["ptr_details"][5] != "Donation" && data["ptr_details"][5] != "Relocate" && data["ptr_details"][5] != "Reassignment"){
                $("input#gOthers").attr("checked", "checked");
                $("#gprint_specify").html(data["ptr_details"][5]);
            }else{
                $("input#g"+data["ptr_details"][5]).attr("checked", "checked");
            }

            exportTableToExcel("report_ptr_gen", "PTR No. "+ptr_no);
        }
    });
}