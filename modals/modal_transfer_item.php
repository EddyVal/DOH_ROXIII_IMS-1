<div class="modal inmodal modal-child" id="modal_transfer_item" tabindex="1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-focus="false">
    <div class="modal-dialog modal-lg" style="width: 1000px;">
    <div class="modal-content animated fadeInDown">
            <div class="modal-header">
                <button type="button" class="close" onclick="$('#modal_transfer_item').modal('hide'); $('body').addClass('modal-open');"><span aria-hidden="true">&times;</span></button>
                <h2><b><i class="fa fa-exchange"></i> Transfer Item (Item#<span id="trans_item_id"></span>)</b></h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">ICS No.:</label>
                                    <div class="col-lg-9">
                                        <input id="trans_ics" type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Transfer To:</label>
                                    <div class="col-lg-9">
                                        <select id="trans_name" class="select2_demo_1 form-control">
                                            <option value="" disabled selected></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <i class="fa fa-list"></i> Item Lists
                            </div>
                            <div class="panel-body" style="height: 200px; overflow: auto;">
                                <table id="trans_items" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Property Number</th>
                                            <th>Serial Number</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" onclick="$('#modal_transfer_item').modal('hide'); $('body').addClass('modal-open');">Close</button>
                <button type="button" class="btn btn-primary" onclick="trans_now();">Transfer</button>
            </div>
        </div>
    </div>
</div>