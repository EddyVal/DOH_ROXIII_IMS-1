var designations = [];
var selected = {"designation": "", "id": ""};

$(document).ready(function(){
	ready_all();
});


function ready_all(){
	$.ajax({
		type: "POST",
		data: {call_func: "load_designation"},
		dataType: "JSON",
		url: "php/config.php",
		success: function(data){
			for (var i = 0; i < data.length; i++){
				designations.push({"name": data[i].designation, "code": data[i].id})
			}
			$('#add_designation').typeahead({
		        source: designations,
		        afterSelect: function(item){
		        	selected.designation = item.name;
		        	selected.id          = item.code;
		        }
		    });
			$('#edit_designation').typeahead({
		        source: designations,
		        afterSelect: function(item){
		        	selected.designation = item.name;
		        	selected.id          = item.code;
		        }
		    });
		}
	});

	$(".select2_demo_1").select2({
        theme: 'bootstrap4',
        width: '100%'
    });
    $("#director_head").ready(function(){
    	$.ajax({
    		url: "../supply_records/php/php_ics.php",
    		data: {call_func: "get_employee"},
    		type: "POST",
    		success: function(data){
    			$("#director_head").html("<option disabled selected></option>").append(data);
    			$("#property_custodian").html("<option disabled selected></option>").append(data);
    			$("#division_chief").html("<option disabled selected></option>").append(data);
    			$("#ppe_prepared_by").html("<option disabled selected></option>").append(data);
    			$("#ppe_noted_by").html("<option disabled selected></option>").append(data);
    			$("#wi_prepared_by").html("<option disabled selected></option>").append(data);
    			$("#wi_reviewed_by").html("<option disabled selected></option>").append(data);
    			$("#wi_noted_by").html("<option disabled selected></option>").append(data);
    			$("#wi_approved_by").html("<option disabled selected></option>").append(data);
    			$("#rpci_prepared_by").html("<option disabled selected></option>").append(data);
    			$("#rpci_certified").html("<option disabled selected></option>").append(data);
    			$("#rpci_noted_by").html("<option disabled selected></option>").append(data);
    			$("#rpci_approved_by").html("<option disabled selected></option>").append(data);
    			get_data();
    		}
    	});
    });
    $("#users-list").ready(function(){
    	$.ajax({
    		url: "php/config.php",
    		type: "POST",
    		data: {call_func: "load_employee"},
    		success: function(data){
    			$("#users-list").html(data);
    		}
    	})
    });
	$("#designation-list").ready(function() {
		$.ajax({
			url: "php/config.php",
			type: "POST",
			data: {call_func: "load_designations"},
			success: function(data){
				$("#designation-list").html(data);
			}
		})
	});

}

// Function to open the Edit Employee modal
function openEditEmployeeModal(emp_id, prefix, fname, mname, lname, suffix, designation) {
    $('#edit_emp_id').val(emp_id);
	$('#edit_prefix').val(prefix);
    $('#edit_fname').val(fname);
	$('#edit_mname').val(mname);
	$('#edit_lname').val(lname);
	$('#edit_suffix').val(suffix);
	$('#edit_designation').val(designation);
    // Populate other fields as needed based on emp_id

    $('#modal_edit_employee').modal('show');
}

function openEditDesignationModal(designation_id, designation) {
	$('#edit_designation_id').val(designation_id);
	$('#edit_new_designation').val(designation);

	$('#modal_edit_designation').modal('show');
}
function capitalizeFirstLetter(element,str) {
	element.value = (str.charAt(0).toUpperCase() + str.slice(1).toLowerCase()).split('.').join("");
}

function capitalize(element,str) {
	element.value = (str.toUpperCase()).split('.').join("");
}

function update_designation() {
	if($("#edit_new_designation").val().trim() != "") {
		$.ajax({
			type: "POST",
			url: "php/config.php",
			data: {
				call_func: "update_designation",
				designation_id: $("#edit_designation_id").val().trim(),
				designation: $("#edit_new_designation").val().trim()
			},
			success: function(data) {
				$("#modal_edit_designation .close").click();
				swal("Saved!", "Existing designation updated.", "success");
			}
		})
	} else {
		swal("Please fill in!", "Designation", "warning");
	}
}
function update_employee() {
	if($("#edit_fname").val().trim() != ""){
		if($("#edit_lname").val().trim() != ""){
			if($("#edit_designation").val().trim() != ""){
				$.ajax({
					type: "POST",
					url: "php/config.php",
					data: {
						call_func: "update_employee",
						emp_id: $("#edit_emp_id").val().trim(),
						prefix: $("#edit_prefix").val().trim(),
						fname: $("#edit_fname").val().trim(),
						mname: $("#edit_mname").val().trim(),
						lname: $("#edit_lname").val().trim(),
						suffix: $("#edit_suffix").val().trim(),
						designation: $("#edit_designation").val().trim()
					},
					success: function(data){
						$("#modal_edit_employee .close").click();
						swal("Saved!", "Existing employee updated.", "success");
					}
				})

			}else{
				swal("Please fill in!", "Designation", "warning");
			}
		}else{
			swal("Please fill in!", "Lastname", "warning");
		}
	}else{
		swal("Please fill in!", "Firstname", "warning");
	}
}

function save_employee(){
	if($("#add_fname").val().trim() != ""){
		if($("#add_lname").val().trim() != ""){
			if($("#add_designation").val().trim() != ""){
				var job_id = ($("#add_designation").val() != selected.designation) ? 0 : selected.id;
				$.ajax({
					type: "POST",
					url: "php/config.php",
					data: {
						call_func: "save_employee",
						prefix: $("#add_honor").val().trim(),
						fname: $("#add_fname").val().trim(),
						mname: $("#add_mname").val().trim(),
						lname: $("#add_lname").val().trim(),
						suffix: $("#add_postn").val().trim(),
						position: $("#add_designation").val().trim(),
						position_id: job_id
					},
					success: function(data){
						$("#modal_add_employee .close").click();
						swal("Saved!", "New employee added.", "success");
					}
				})

			}else{
				swal("Please fill in!", "Designation", "warning");
			}
		}else{
			swal("Please fill in!", "Lastname", "warning");
		}
	}else{
		swal("Please fill in!", "Firstname", "warning");
	}
	
}

//saving new designation

function save_designation() {
	if($("#add_new_designation").val().trim() != "") {
		$.ajax({
			type: "POST",
			url: "php/config.php",
			data: {
				call_func: "save_designation",
				designation: $("#add_new_designation").val().trim()
			},
			success: function(data) {
				$("#modal_add_designation .close").click();
				swal("Saved!", "New designation added.", "success");
			}
		})
	} else {
		swal("Please fill in!", "Designation", "warning");
	}
}
function get_data(){
	$.ajax({
		type: "POST",
		data: {call_func: "get_data"},
		dataType: "JSON",
		url: "php/config.php",
		success: function(data){
			$("#img_logo").attr("src", "../../archives/img/"+data["company_logo"]);
			$("#company_title").val(data["company_title"]);
			$("#supporting_title").val(data["supporting_title"]);
			$("#entity_name").val(data["entity_name"]);
			set_names("director_head", data["company_head"]);
			set_names("property_custodian", data["property_custodian"]);
			set_names("division_chief", data["division_chief"]);
			set_names("ppe_prepared_by", data["ppe_prepared_by"]);
			set_names("ppe_noted_by", data["ppe_noted_by"]);
			set_names("wi_prepared_by", data["wi_prepared_by"]);
			set_names("wi_reviewed_by", data["wi_reviewed_by"]);
			set_names("wi_noted_by", data["wi_noted_by"]);
			set_names("wi_approved_by", data["wi_approved_by"]);
			set_names("rpci_prepared_by", data["rpci_prepared_by"]);
			set_names("rpci_certified", data["rpci_certified_correct"]);
			set_names("rpci_noted_by", data["rpci_noted_by"]);
			set_names("rpci_approved_by", data["rpci_approved_by"]);
			$("#rpci_coa").val(data["rpci_coa"]);
			$("#rpci_coa_designation").val(data["rpci_coa_designation"]);
			$("#warehouse_name").val(data["warehouse_name"]);
		}
	});
}

function set_names(id, value){
	var v = value.split("|");
	$('#'+id+' option').each(function() {
        if($(this).text() == v[1]) {
            $(this).prop("selected", true).change();
        }
    });
}

function save_organizational(){
	$.ajax({
		type: "POST",
		data: {
			call_func: "save_org",
			company_title: $("#company_title").val(),
			supporting_title: $("#supporting_title").val(),
			entity_name: $("#entity_name").val(),
			company_head: $("#director_head").val()+"|"+$("#director_head option:selected").text(),
			warehouse_name: $("#warehouse_name").val()
			},
		url: "php/config.php",
		success: function(data){
			swal("Updated!", "Organization details successfully updated.", "success");
		}
	});
}

function save_reporting(){
	$.ajax({
		type: "POST",
		data: {
			call_func: "save_rep",
			property_custodian: $("#property_custodian").val()+"|"+$("#property_custodian option:selected").text(),
			division_chief: $("#division_chief").val()+"|"+$("#division_chief option:selected").text(),
			ppe_prepared_by: $("#ppe_prepared_by").val()+"|"+$("#ppe_prepared_by option:selected").text(),
			ppe_noted_by: $("#ppe_noted_by").val()+"|"+$("#ppe_noted_by option:selected").text(),
			wi_prepared_by: $("#wi_prepared_by").val()+"|"+$("#wi_prepared_by option:selected").text(),
			wi_reviewed_by: $("#wi_reviewed_by").val()+"|"+$("#wi_reviewed_by option:selected").text(),
			wi_noted_by: $("#wi_noted_by").val()+"|"+$("#wi_noted_by option:selected").text(),
			wi_approved_by: $("#wi_approved_by").val()+"|"+$("#wi_approved_by option:selected").text(),
			rpci_prepared_by: $("#rpci_prepared_by").val()+"|"+$("#rpci_prepared_by option:selected").text(),
			rpci_certified_correct: $("#rpci_certified").val()+"|"+$("#rpci_certified option:selected").text(),
			rpci_noted_by: $("#rpci_noted_by").val()+"|"+$("#rpci_noted_by option:selected").text(),
			rpci_approved_by: $("#rpci_approved_by").val()+"|"+$("#rpci_approved_by option:selected").text(),
			rpci_coa: $("#rpci_coa").val(),
			rpci_coa_designation: $("#rpci_coa_designation").val()
			},
		url: "php/config.php",
		success: function(data){
			swal("Updated!", "Reporting details successfully updated.", "success");
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
	$.ajax({
		url: 'php/upload_logo.php?files&id=1',
		type: 'POST',
		data: data,
		cache: false,
		processData: false,
		contentType: false,
		success: function(data){
			$("#img_logo").attr("src", "../../archives/img/"+data);
  		}
	});
}