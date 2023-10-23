<div class="modal inmodal modal-child" id="modal_edit_employee" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-focus="false" style="color: black;">
    <div class="modal-dialog modal-xs">
        <div class="modal-content animated fadeInDown">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h2><b><i class="fa fa-pencil"></i> Edit Employee</b></h2>
            </div>
            <div class="modal-body">
                <!-- Replace the content below with the fields to edit employee information -->
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Employee ID:</label>
                    <div class="col-lg-9">
                        <input id="edit_emp_id" type="text" class="form-control" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Honorifics:</label>
                    <div class="col-lg-9">
                        <input id="edit_prefix" type="text" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Firstname: <span style="color: red;">*</span></label>
                    <div class="col-lg-9">
                        <input id="edit_fname" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Middlename:</label>
                    <div class="col-lg-9">
                        <input id="edit_mname" type="text" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Lastname: <span style="color: red;">*</span></label>
                    <div class="col-lg-9">
                        <input id="edit_lname" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Post-nominal:</label>
                    <div class="col-lg-9">
                        <input id="edit_suffix" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Designation: <span style="color: red;">*</span></label>
                    <div class="col-lg-9">
                        <input id="edit_designation" type="text" class="form-control" required>
                    </div>
                </div>
                <!-- Add fields for other employee information that you want to edit -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="update_employee();">Save Changes</button>
            </div>
        </div>
    </div>
</div>
