<div class="modal inmodal" id="edit_rfi" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 950px;">
        <div class="modal-content animated slideInDown">
            <form id="update_rfi">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h5 class="modal-title"><i class="fa fa-edit"></i> Edit RFI</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_rfi_id" name="edit_rfi_id">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><i class="fa fa-info-circle"></i> RFI Information</div>
                        <div class="panel-body">
                            <!-- Control Number -->
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Control Number:</label>
                                <div class="col-lg-9">
                                    <input id="edit_control_number" name="control_number" type="text" class="form-control" required>
                                </div>
                            </div>
                            <!-- Chairperson -->
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Chairperson:</label>
                                <div class="col-lg-9">
                                    <select id="edit_chairperson" name="chairperson" class="form-control select2_demo_1" required></select>
                                </div>
                            </div>
                            <!-- Reference/PO -->
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Reference/PO Number:</label>
                                <div class="col-lg-9">
                                    <select id="edit_reference_no" name="reference_no[]" class="form-control select2_demo_1" multiple required></select>
                                </div>
                            </div>
                            <!-- Item Table -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr style="text-align: center;">
                                                <th class="d-none">ID</th>
                                                <th style="width: 30%;">Item Description</th>
                                                <th style="width: 10%;">Quantity Delivered</th>
                                                <th style="width: 25%;">RSD Control No.</th>
                                                <th style="width: 10%;">Approved Date</th>
                                                <th style="width: 20%;">Delivery Location</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="edit_item_table_body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Update RFI">
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

