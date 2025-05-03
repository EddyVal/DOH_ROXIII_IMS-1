Date.prototype.toDateInputValue = (function() {
    var local = new Date(this);
    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
    return local.toJSON().slice(0,10);
});

function ready_all(){
    $(".select2_demo_1").select2({
        theme: 'bootstrap4',
        width: '100%',
    });

    $("#control_number").ready(function(){
        var date = (new Date().toDateInputValue()).split("-");
        $.ajax({
            type: "POST",
            url: _url,
            data: {call_func: "get_latest_rfi", yy_mm: date[0]+"-"+date[1]},
            success: function(data){
                $('#control_number').val('RFI/CHD13-CR-'+date[0]+"-"+date[1]+"-"+data);
            }
        });
    });
    
    $("#chairperson").ready(function(){
        $.ajax({
            type: "POST",
            data: {call_func: "get_chairperson"},
            url: _url,
            success: function(data){
                $("#chairperson").html("<option disabled selected></option>").append(data);
                $("#edit_chairperson").html("<option disabled selected></option>").append(data);
            }
        });
    });
    
    $("#reference_no").ready(function(){
        $.ajax({
            type: "POST",
            data: {call_func: "get_po_for_rfi"},
            url: _url,
            success: function(data){
                $("#reference_no").html(data);
                $("#edit_reference_no").html(data);
            }
        });
    });
}

$("#reference_no").change(function(){
    $.ajax({
        type: "POST",
        data: {call_func: "get_items_po", po_numbers: $("#reference_no").val()},
        url: _url,
        success: function(data){
            var items = JSON.parse(data.replace(/&quot;/g, '"'));
            $("#item_table_body").empty();
            items.forEach(function(item) {
                var row = `<tr>
                    <td class="d-none"><input type='hidden' name="po_id[]" value='${item.id}'></td>
                    <td>${item.item_description}</td>
                    <td style='text-align: center;'>${item.quantity_delivered}</td>
                    <td><input type='text' class='form-control' name='rsd_control_no[]' /></td>
                    <td><input type='date' class='form-control' name='approved_date[]' value='${item.date_delivered}' /></td>
                    <td>${item.location}</td>
                    <td><button type='button' class='btn btn-danger btn-sm dim' onclick='removeRow(this)'><i class='fa fa-trash'></i> </button></td>
                </tr>`;
                $("#item_table_body").append(row);
            });
        }
    });
});

$("#edit_reference_no").change(function(){
    $.ajax({
        type: "POST",
        data: {call_func: "get_items_po", po_numbers: $("#edit_reference_no").val()},
        url: _url,
        success: function(data){
            var items = JSON.parse(data.replace(/&quot;/g, '"'));
            $("#edit_item_table_body").empty();
            items.forEach(function(item) {
                var row = `<tr>
                    <td class="d-none">
                        <input type="hidden" name="status[]" value="new">
                        <input type="hidden" name="item_id[]" value="${item.id}">
                    </td>
                    <td>${item.item_description}</td>
                    <td style='text-align: center;'>${item.quantity_delivered}</td>
                    <td><input type='text' class='form-control' name='rsd_control_no[]' /></td>
                    <td><input type='date' class='form-control' name='approved_date[]' value='${item.date_delivered}' /></td>
                    <td>${item.location}</td>
                    <td><button type='button' class='btn btn-danger btn-sm dim' onclick='removeRow(this)'><i class='fa fa-trash'></i> </button></td>
                </tr>`;
                $("#edit_item_table_body").append(row);
            });
        }
    });
});

$('#insert_rfi').on('submit', function(event) {
    event.preventDefault();

    let formData = $(this).serialize();
    formData += `&${encodeURIComponent('call_func')}=${encodeURIComponent('insert_rfi')}`;

    console.log("Serialized Form Data:", formData);
    
    $.ajax({
        url: _url,
        type: 'POST',
        data: formData,
        success: function(response) {
            swal("Inserted!", "Saved successfully to the database.", "success");
            setTimeout(function () {
                location.reload();
            }, 1500);
            
        },
        error: function(xhr, status, error) {
            swal("Error saving rfi!", error, "error");
        }
    });
});

$('#update_rfi').on('submit', function(event) {
    event.preventDefault();

    let formData = $(this).serialize();
    formData += `&${encodeURIComponent('call_func')}=${encodeURIComponent('update_rfi')}`;

    console.log("Serialized Form Data:", formData);
    
    $.ajax({
        url: _url,
        type: 'POST',
        data: formData,
        success: function(response) {
            swal("Updated!", "RFI updated successfully.", "success");
            setTimeout(function () {
                location.reload();
            }, 1500);
            
        },
        error: function(xhr, status, error) {
            swal("Error updating rfi!", error, "error");
        }
    });
});

function print_rfi(id) {
    $.ajax({
        url: _url,
        type: 'POST',
        data: { call_func: "print_rfi", id: id },
        success: function(response) {
            var data = JSON.parse(response.replace(/&quot;/g, '"'));
            let inspector = data.rfi.inspector.split("|");
            
            $.ajax({
                url: 'https://api.genderize.io',
                type: 'GET',
                data: { name: inspector[0] },
                success: function(res) {
                    let gender = (res.gender === "male") ? "Sir" : "Ma'am";
                    setPrintContent(data, inspector, gender);
                },
                error: function() {
                    let gender = "Sir/Ma'am";
                    setPrintContent(data, inspector, gender);
                }
            });
        },
        error: function(xhr, status, error) {
            swal("Error printing rfi!", error, "error");
        }
    });
}

function setPrintContent(data, inspector, gender) {
    $("#print_control_no").html(data.rfi.control_number);
    $("#print_created_at").html(formatDateTime(data.rfi.created_at));
    $("#recipient_name").html(inspector[0].toUpperCase());
    $("#recipient_designation").html(inspector[1]);
    $("#recipient_gender").html(gender);
    $("#other_designation").html("Inspection Committee");

    let items = data.rfi_details;
    $("#print_items").empty();
    items.forEach(function(item, index) {
        var row = `<tr>
            <td style="height: 20px; text-align: center; vertical-align: middle;"></td>
            <td style="width: 5%; height: 20px; text-align: center; font-size: 11px; vertical-align: middle; border: 1px solid black;">${index + 1}</td>
            <td style="width: 30%; height: 20px; font-size: 11px; vertical-align: middle; border: 1px solid black;"><b>${item.item_name}</b> - ${item.description}</td>
            <td style="width: 10%; height: 20px; text-align: center; font-size: 11px; vertical-align: middle; border: 1px solid black;">${item.main_stocks}</td>
            <td style="width: 20%; height: 20px; text-align: center; font-size: 11px; vertical-align: middle; border: 1px solid black;">${item.rsd_no}</td>
            <td style="width: 15%; height: 20px; text-align: center; font-size: 11px; vertical-align: middle; border: 1px solid black;">${item.approved_date}</td>
            <td style="width: 20%; height: 20px; text-align: center; font-size: 11px; vertical-align: middle; border: 1px solid black;">${item.location}</td>
            <td style="height: 20px; text-align: center; vertical-align: middle;"></td>
        </tr>`;
        $("#print_items").append(row);
    });

    var divContents = $("#report_rfi").html(); 
    var a = window.open('', '_blank', 'height=1500, width=800'); 
    a.document.write(`
        <html>
        <head>
            <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" type="text/css" href="../css/demand_letter.css">
        </head>
        <body>
            <center>
    `);
    a.document.write('<table style="width: 100%;"><tr><td>');
    a.document.write(divContents);
    
    // Page breaker
    a.document.write('<hr style="page-break-before:always; border:none; margin:0;">');

    // For coa
    $("#recipient_name").html("JANAH P. TAPANGAN");
    $("#recipient_designation").html("State Auditor IV");
    $("#recipient_gender").html("Ma'am");
    $("#other_designation").html("Audit Team Leader");
    
    divContents = $("#report_rfi").html();
    a.document.write(divContents);
    a.document.write('</td></tr></table>');
    a.document.write('</center></body></html>'); 
    a.document.close(); 
    setTimeout(function() { 
        a.print(); 
    }, 1000);
}


function delete_rfi(id){
    swal({
        title: "Are you sure?",
        text: "This RFI record will be deleted as soon as you clicked 'Yes'",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: false
    }, function () {
        $.ajax({
            type: "POST",
            data: {call_func: "delete_rfi", id: id},
            url: _url,
            success: function(data){
                swal("Deleted!", "RFI record successfully deleted.", "success");
                setTimeout(function () { location.reload(); }, 1500);
            }
        });
    });
}

function edit_rfi(id) {
    $.ajax({
        url: _url,
        type: 'POST',
        data: { call_func: "print_rfi", id: id },
        success: function(response) {
            var data = JSON.parse(response.replace(/&quot;/g, '"'));
            console.log(data);
            $('#edit_rfi_id').val(data.rfi.id);
            $('#edit_control_number').val(data.rfi.control_number);
            $('#edit_chairperson').val(data.rfi.inspector).trigger('change');
            $('#edit_reference_no').val(data.rfi.po_number.split("|")).trigger('change');

            let tbody = '';
            data.rfi_details.forEach(item => {
                tbody += `<tr>
                    <td class="d-none">
                        <input type="hidden" name="status[]" value="old">
                        <input type="hidden" name="item_id[]" value="${item.id}">
                    </td>
                    <td><b>${item.item_name}</b> - ${item.description}</td>
                    <td>${item.main_stocks}</td>
                    <td><input type="text" name="rsd_control_no[]" class="form-control" value="${item.rsd_no}" required></td>
                    <td><input type="date" name="approved_date[]" class="form-control" value="${item.approved_date}" required></td>
                    <td>${item.location}</td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fa fa-trash"></i></button></td>
                </tr>`;
            });

            $('#edit_item_table_body').html(tbody);
            $('#edit_rfi').modal('show');
        }
    });
}
