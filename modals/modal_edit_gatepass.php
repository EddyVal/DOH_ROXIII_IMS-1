<div class="modal inmodal" id="edit_gatepass" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated slideInDown">
            <form id="update_gatepass">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title"><i class="fa fa-edit"></i> Edit Gatepass</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_gatepass_id" name="gatepass_id">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-info-circle"></i> Gatepass Information
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">Control Number:</label>
                                        <div class="col-lg-6">
                                            <input id="edit_control_number" type="text" class="form-control" name="control_number" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">Authorize:</label>
                                        <div class="col-lg-6">
                                            <input id="edit_authorized_personnel" type="text" placeholder="Enter name of authorized personnel" class="form-control" name="authorized_personnel" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label"><span id="edit_issuance_type">ICS/RIS/PAR/PTR</span>:</label>
                                        <div class="col-lg-6">
                                            <div class="d-flex">
                                                <select id="edit_issuance_no" class="form-control select2_demo_1" name="issuance_no[]" multiple>
                                                    <!-- Options will be populated dynamically -->
                                                </select>
                                                <span class="input-group-append">
                                                    <button onclick="edit_insert_issuance()" type="button" class="btn btn-primary dim"><i class="fa fa-plus"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 d-flex justify-content-between py-2">
                                            <div class="form-check abc-radio abc-radio-success">
                                                <input class="form-check-input" type="radio" name="edit_issuance_type" id="edit_radio1" value="ICS" data-table="tbl_ics" data-field="ics_no" data-id="ics_id">
                                                <label class="form-check-label" for="edit_radio1">
                                                    ICS
                                                </label>
                                            </div>
                                            <div class="form-check abc-radio abc-radio-success">
                                                <input class="form-check-input" type="radio" name="edit_issuance_type" id="edit_radio2" value="PAR" data-table="tbl_par" data-field="par_no" data-id="par_id">
                                                <label class="form-check-label" for="edit_radio2">
                                                    PAR
                                                </label>
                                            </div>
                                            <div class="form-check abc-radio abc-radio-success">
                                                <input class="form-check-input" type="radio" name="edit_issuance_type" id="edit_radio3" value="RIS" data-table="tbl_ris" data-field="ris_no" data-id="ris_id">
                                                <label class="form-check-label" for="edit_radio3">
                                                    RIS
                                                </label>
                                            </div>
                                            <div class="form-check abc-radio abc-radio-success">
                                                <input class="form-check-input" type="radio" name="edit_issuance_type" id="edit_radio4" value="PTR" data-table="tbl_ptr" data-field="ptr_no" data-id="ptr_id">
                                                <label class="form-check-label" for="edit_radio4">
                                                    PTR
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12" style="border-top: 1px solid grey; border-bottom: 1px solid grey; height: 50vh; overflow: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr style="text-align: center;">
                                                <th style="width: 15%;">ICS/PAR/RIS/PTR No</th>
                                                <th style="width: 15%;">Source</th>
                                                <th style="width: 15%;">Item Description</th>
                                                <th style="width: 10%;">Batch/Lot/Serial No</th>
                                                <th style="width: 5%;">Qty</th>
                                                <th style="width: 5%;">Unit</th>
                                                <th style="width: 15%;">Program</th>
                                                <th style="width: 15%;">Purpose</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="edit_item_table_body">
                                            <!-- Rows will be populated dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label">Driver:</label>
                                        <div class="col-lg-8">
                                            <input id="edit_driver" type="text" class="form-control" name="driver" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label">Plate Number:</label>
                                        <div class="col-lg-8">
                                            <input id="edit_plate_number" type="text" class="form-control" name="plate_number" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label">Vehicle Type:</label>
                                        <div class="col-lg-8">
                                            <input id="edit_vehicle_type" type="text" class="form-control" name="vehicle_type" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4">
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label">Checked by:</label>
                                        <div class="col-lg-8">
                                            <select id="edit_checked_by" class="form-control select2_demo_1" name="checked_by" required>
                                                <!-- Options will be populated dynamically -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label">Approved by:</label>
                                        <div class="col-lg-8">
                                            <select id="edit_approved_by" class="form-control select2_demo_1" name="approved_by" required>
                                                <!-- Options will be populated dynamically -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Update Gatepass">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function removeRow(button) {
        const row = button.parentElement.parentElement;
        row.remove();
    }
</script>
