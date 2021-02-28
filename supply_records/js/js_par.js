var items = [];
var $po_regex=/^([0-9]{4}-[0-9]{2}-[0-9]{4})|^([0-9]{4}-[0-9]{2}-[0-9]{3})$/;

var po_details = {};

$(document).ready(function(){
    get_par();
});

Date.prototype.toDateInputValue = (function() {
    var local = new Date(this);
    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
    return local.toJSON().slice(0,10);
});

function get_par(){
    $.ajax({
        type: "POST",
        data: {call_func: "get_par"},
        url: "php/php_par.php",
        success: function(data){
            $("table#par_data tbody").html(data);
            create_datatable();
        }
    });
}

function create_datatable(){
    $('.dataTables-example').DataTable({
        pageLength: 10,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            { extend: 'copy'},
            {extend: 'csv'},
            {extend: 'excel', title: 'PAR'},
            {extend: 'pdf', title: 'PAR'},

            {extend: 'print',
             customize: function (win){
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
            }
            }
        ]
    });
    $(".first_col").click();
    ready_all();
}

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

    $("#par_no").ready(function(){
        var po_value = (new Date().toDateInputValue()).split("-");
        $.ajax({
            type: "POST",
            dataType: "JSON",
            url: "php/php_par.php",
            data: {call_func: "get_latest_par", yy_mm: po_value[0]+"-"+po_value[1]},
            success: function(data){
                $('#par_no').val(po_value[0]+"-"+po_value[1]+"-"+data["latest_par"]);
                $('#lbl_pn').html(po_value[0]+"-"+po_value[1]+"-"+data["latest_pn"]);
            }
        });
    });

    $("#par_area").ready(function(){
        $.ajax({
            type: "POST",
            url: "php/php_ics.php",
            data: {call_func: "get_area"},
            success: function(data){
                $("#par_area").html("<option disabled selected></option>").append(data);
                $("#eics_area").html("<option disabled selected></option>").append(data);
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
                    if($(this).text() == $("#par_no").data("pc")){
                        $(this).prop("selected", true).change();
                    }
                });
            }
        });
    });

    $("#received_by").ready(function(){
        $.ajax({
            type: "POST",
            url: "php/php_ics.php",
            data: {call_func: "get_employee"},
            success: function(data){
                $("#received_by").html("<option disabled selected></option>").append(data);
            }
        });
    });

    $("#reference_no").ready(function(){
        $.ajax({
            type: "POST",
            data: {call_func: "get_po", action: "get_number", po_type: "ictvar"},
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
        $("#unit").val("");
        $("#description").val("");
        $("#unit_value").val("");
        $("#category").val("");
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
            }
        });
    });

    $("#unit").ready(function(){
        $.ajax({
            type: "POST",
            data: {call_func: "get_unit"},
            url: "../php/php_po.php",
            success: function(data){
                $("#unit").html("<option disabled selected></option>").append(data);
            }
        });
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

function total_amount(){
    $("#total_amount").val(formatNumber((parseFloat($("#quantity").val() == "" ? 0.00 : $("#quantity").val()) * parseFloat(origNumber($("#unit_value").val()))).toFixed(2)));
}

$('#par_items').on('click', 'tbody tr button', function(event) {
    event.preventDefault();
    po_details[$(this).attr('id')][$(this).val()][1] = po_details[$(this).attr('id')][$(this).val()][1] + parseInt($(this).data("quan"));
    $(this).parents('tr').remove();
    calculate_all_total();
});

function validate(){
    if($("#par_no").val().match($po_regex)){
        if($("#entity_name").val() != ""){
            if($("#received_from").val() != null){
                if($("#date").val() != ""){
                    if($("#reference_no").val() != ""){
                        if($("#received_by").val() != null){
                            if($("#par_area").val() != null){
                                if(get_rows() != 0){
                                    $("#save_changes").attr("disabled", true);
                                    $.ajax({
                                        type: "POST",
                                        data: {
                                            call_func: "insert_par",
                                            par_no: $("#par_no").val(),
                                            entity_name: $("#entity_name").val(),
                                            fund_cluster: $("#fund_cluster").val(),
                                            reference_no: $("#reference_no option:selected").text(),
                                            received_from_id: $("#received_from").val(),
                                            received_from: $("#received_from option:selected").text(),
                                            received_by_id: $("#received_by").val(),
                                            received_by: $("#received_by option:selected").text(),
                                            date_released: $("#date").val(),
                                            area: $("#par_area option:selected").text(),
                                            items: items
                                        },
                                        url: "php/php_par.php",
                                        success: function(data){
                                            if(data == "0"){
                                                swal("Inserted!", "Saved successfully to the database.", "success");
                                                setTimeout(function () {
                                                    location.reload();
                                                  }, 1500);
                                            }else{
                                                $("#save_changes").attr("disabled", false);
                                                swal("PAR Number already existed!", "Please enter another PAR number!", "warning");
                                            }
                                        }
                                    });
                                }else{
                                    swal("No items!", "Please add an item", "warning");
                                }
                            }else{
                                swal("Please fill in!", "PAR Area", "warning");
                            }
                        }else{
                            swal("Please fill in!", "Received By", "warning");
                        }
                    }else{
                        swal("Please fill in!", "Reference Number", "warning");
                    }
                }else{
                    swal("Please fill in!", "Date Released", "warning");
                }
            }else{
                swal("Please fill in!", "Received From", "warning");
            }   
        }else{
            swal("Please fill in!", "Entity Name", "warning");
        }
    }else{
        swal("Invalid input!", "PAR Number not valid", "warning");
    }
}

function add_item(){
    if($("#item_name").val() != null){
        if($("#category").val() != null){
            if($("#property_no").val() != ""){
                if($("#quantity").val() != ""){
                    if(parseInt($("#quantity").val()) <= po_details[$("#reference_no option:selected").text()][$("#item_name").val()][1]) {
                        if(parseInt($("#quantity").val()) > 0){
                            if($("#serial_no").val() == ""){
                                $("table#par_items tbody").append("<tr>"+
                                    "<td>"+$("#item_name").val()+"</td>"+
                                    "<td>"+$("#item_name option:selected").text()+"</td>"+
                                    "<td>"+$("#description").val()+"</td>"+
                                    "<td></td>"+
                                    "<td>"+$("#category").val()+"</td>"+
                                    "<td>"+$("#property_no").val()+"</td>"+
                                    "<td>"+$("#quantity").val()+"</td>"+
                                    "<td>"+$("#unit").val()+"</td>"+
                                    "<td>"+$("#unit_value").val()+"</td>"+
                                    "<td>"+$("#total_amount").val()+"</td>"+
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
                                $("#total_amount").val("");
                                calculate_all_total();
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
                swal("Please fill in!", "Property Number", "warning");  
            }
        }else{
            swal("Please fill in!", "Category", "warning"); 
        }
    }else{
        swal("Please fill in!", "Item Name", "warning");    
    }
}

function validate_with_snln(){
    if($("#category").val() != "Drugs and Medicines"){
        if(parseInt($("#quantity").val()) == $('#serial_no').select2("val").length){
            $("table#par_items tbody").append("<tr>"+
                "<td>"+$("#item_name").val()+"</td>"+
                "<td>"+$("#item_name option:selected").text()+"</td>"+
                "<td>"+$("#description").val()+"</td>"+
                "<td>"+$('#serial_no').select2("val")+"</td>"+
                "<td>"+$("#category").val()+"</td>"+
                "<td>"+$("#property_no").val()+"</td>"+
                "<td>"+$("#quantity").val()+"</td>"+
                "<td>"+$("#unit").val()+"</td>"+
                "<td>"+$("#unit_value").val()+"</td>"+
                "<td>"+$("#total_amount").val()+"</td>"+
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
                $("#total_amount").val("");
            calculate_all_total();
        }else{
            swal("Quantity not matched!", "Number of serial numbers selected should correspond to the inputted quantity.", "warning");
        }
    }else{
        $("table#par_items tbody").append("<tr>"+
        "<td>"+$("#item_name").val()+"</td>"+
        "<td>"+$("#item_name option:selected").text()+"</td>"+
        "<td>"+$("#description").val()+"</td>"+
        "<td>"+$('#serial_no').select2("val")+"</td>"+
        "<td>"+$("#category").val()+"</td>"+
        "<td>"+$("#property_no").val()+"</td>"+
        "<td>"+$("#quantity").val()+"</td>"+
        "<td>"+$("#unit").val()+"</td>"+
        "<td>"+$("#unit_value").val()+"</td>"+
        "<td>"+$("#total_amount").val()+"</td>"+
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
        $("#total_amount").val("");
        calculate_all_total();
    }
}

function get_rows(){
    items = [];
    var table = $("table#par_items tbody");
    var rows = 0;
    table.find('tr').each(function (i) {
        var $tds = $(this).find('td');
        items.push([$tds.eq(0).text(),$tds.eq(1).text(),$tds.eq(2).text(),$tds.eq(3).text(),$tds.eq(4).text(),$tds.eq(5).text(),$tds.eq(6).text(),$tds.eq(7).text(),origNumber($tds.eq(8).text()),origNumber($tds.eq(9).text()),$tds.eq(10).text()]);
        rows++;
    });
    return rows;
}

function calculate_all_total(){
    var all_total_amount = 0.00;
    var table = $("table#par_items tbody");
    table.find('tr').each(function (i) {
        var $tds = $(this).find('td');
        all_total_amount+=parseFloat(origNumber($tds.eq(9).text()));
    });
    $("#all_total_amount").html(formatNumber(all_total_amount));
}

function to_issue(par_no, ref_no){
    swal({
        title: "Are you sure?",
        text: "This PAR record will be issued as soon as you clicked 'Yes'",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: false
    }, function () {
        $.ajax({
            type: "POST",
            data: {call_func: "to_issue", par_no: par_no},
            url: "php/php_par.php",
            success: function(data){
                swal("Issued!", "The items on PAR No. "+par_no+" is now issued.", "success");
                setTimeout(function () {
                    location.reload();
                  }, 1500);
            }
        });
    });
}

function modify(par_no){
    $("#edit_ics_par").modal();
    $("#panel_heading").html("Property Acknowledgement Receipt");
    $("#label_name").html("PAR No");
    $("#eics_no").val(par_no);
    $.ajax({
        type: "POST",
        dataType: "JSON",
        url: "php/php_ics.php",
        data: {
            call_func: "modify",
            table: "tbl_par",
            field: "par_no",
            number: par_no
        },
        success: function(data){
            $("table#eics_items tbody").html(data["tabled"]);
            $("#tot_amt").html(formatNumber(data["tot_amt"].toFixed(2)));
            $("#eentity_name").val(data["entity_name"]);
            $("#ereceived_from").val(data["received_from"]);
            $("#ereceived_from_designation").val(data["received_from_designation"]);
            $("#edate").val(data["date_released"]);
            $("#ereference_no").val(data["reference_no"]);
            $("#efund_cluster").val(data["fund_cluster"]);
            $("#ereceived_by").val(data["received_by"]);
            $("#ereceived_by_designation").val(data["received_by_designation"]);
            $('#eics_area option').each(function() {
                if($(this).text() == data["area"]){
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
            table: "tbl_par",
            field: "par_no",
            number: $("#eics_no").val(),
            entity_name: $("#eentity_name").val(),
            received_from: $("#ereceived_from").val(),
            received_from_designation: $("#ereceived_from_designation").val(),
            date_released: $("#edate").val(),
            reference_no: $("#ereference_no").val(),
            fund_cluster: $("#efund_cluster").val(),
            received_by: $("#ereceived_by").val(),
            received_by_designation: $("#ereceived_by_designation").val(),
            area: $("#eics_area option:selected").text()
        },
        url: "php/php_ics.php",
        success: function(data){
            swal("Updated!", "ICS details successfully updated.", "success");
            setTimeout(function () {
                location.reload();
              }, 1500);
        }
    });
}

function delete_control(par_no){
    swal({
        title: "Are you sure?",
        text: "This PAR record will be removed from the database.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: false
    }, function () {
        $.ajax({
            type: "POST",
            data: {call_func: "delete",
                    field: "par_no",
                    table: "tbl_par",
                    number: par_no
                },
            url: "php/php_ics.php",
            success: function(data){
                swal("Deleted!", "The PAR No. "+par_no+" is now deleted.", "success");
                setTimeout(function () {
                    location.reload();
                  }, 1500);
            }
        });
    });
}

function print_par(par_no){
    $.ajax({
        type: "POST",
        data: {call_func: "get_par_details", par_no: par_no},
        url: "php/php_par.php",
        dataType: "JSON",
        success: function(data){
            $("#print_parno").html(par_no);
            $("#par_tbody").html(data["par_tbody"]);
            $("#total_cost").html(data["total_cost"]);
            $("#print_received_from").html("<u>"+data["receivers"][0].toUpperCase()+"</u>");
            $("#print_received_from_designation").html(data["receivers"][1]);
            $("#print_received_by").html("<u>"+data["receivers"][2].toUpperCase()+"</u>");
            $("#print_received_by_designation").html(data["receivers"][3]);
            $(".date_r").html(data["receivers"][4]);

            var divContents = $("#report_par").html(); 
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

function download_xls(par_no){
    $.ajax({
        type: "POST",
        data: {call_func: "get_par_details", par_no: par_no},
        url: "php/php_par.php",
        dataType: "JSON",
        success: function(data){
            $("#print_parno").html(par_no);
            $("#par_tbody").html(data["par_tbody"]);
            $("#total_cost").html(data["total_cost"]);
            $("#print_received_from").html("<u>"+data["receivers"][0].toUpperCase()+"</u>");
            $("#print_received_from_designation").html(data["receivers"][1]);
            $("#print_received_by").html("<u>"+data["receivers"][2].toUpperCase()+"</u>");
            $("#print_received_by_designation").html(data["receivers"][3]);
            $(".date_r").html(data["receivers"][4]);

            exportTableToExcel("report_par", "PAR No. "+par_no);
        }
    });
}