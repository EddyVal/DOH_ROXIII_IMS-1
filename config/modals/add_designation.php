<div class="modal inmodal modal-child" id="modal_add_designation" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-focus="false" style="color: black;">
    <div class="modal-dialog modal-xs">
    <div class="modal-content animated fadeInDown">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h2><b><i class="fa fa-plus"></i> Add new designation</b></h2>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Designation:</label>
                    <div class="col-lg-9">
                        <input id="add_new_designation" type="text" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="save_designation();">Save</button>
            </div>
        </div>
    </div>
</div>