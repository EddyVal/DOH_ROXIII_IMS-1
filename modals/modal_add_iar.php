<div class="modal inmodal" id="add_iar" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-focus="false">
    <div class="modal-dialog modal-lg">
    <div class="modal-content animated slideInDown">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><i class="fa fa-plus"></i> Add IAR</h5>
            </div>
            <div class="modal-body">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" onclick="set_state(1);" href="#tab-1">General</a></li>
                        <?php //<li><a class="nav-link" data-toggle="tab" onclick="set_state(2);" href="#tab-2">Drugs and Medicines</a></li> ?>
                    </ul>
                    <div class="tab-content">

                        <div role="tabpanel" id="tab-1" class="tab-pane active">
                            <div class="panel-body">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <i class="fa fa-info-circle"></i> IAR Information
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Entity Name:</label>
                                                    <div class="col-lg-8">
                                                        <input id="var_en" type="text" class="form-control" value="<?php echo $_SESSION["entity_name"]; ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Purchase Order:</label>
                                                    <div class="col-lg-8">
                                                        <select id="var_po" class="select2_demo_1 form-control">
                                                            <option value="" disabled selected></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Supplier's Name:</label>
                                                    <div class="col-lg-8">
                                                        <input id="var_sn" type="text" class="form-control" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Requisitioning Office/Dept:</label>
                                                    <div class="col-lg-8">
                                                        <select id="var_rod" class="select2_demo_1 form-control">
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Responsibility Center Code:</label>
                                                    <div class="col-lg-8">
                                                        <input id="var_rcc" type="text" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Fund Cluster:</label>
                                                    <input id="var_fc" type="text" class="col-lg-8 form-control">
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">IAR Number:</label>
                                                    <input id="var_iar" type="text" class="col-lg-8 form-control">
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Date Delivered:</label>
                                                    <input id="var_dated" class="col-lg-8 form-control" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" id="date" disabled="" />
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Charge Invoice:</label>
                                                    <input id="var_ci" type="text" class="col-lg-8 form-control">
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Date Conformed:</label>
                                                    <input id="var_datec" class="col-lg-8 form-control" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" id="date" disabled="" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <i class="fa fa-info-circle"></i> Item Details
                                    </div>
                                    <div class="panel-body" style="height: 220px; overflow: auto;">
                                        <table id="var_items" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date Delivered/Travel</th>
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Expiry Date</th>
                                                    <th>Manufacturer</th>
                                                    <th>Quantity</th>
                                                    <th>Amount</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">
                                                <i class="fa fa-list"></i> Inspection Information
                                            </div>
                                            <div class="panel-body">
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Date Inspected:</label>
                                                    <div class="col-lg-8">
                                                        <input id="var_inspected" class="form-control" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" />
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Inspector's Name:</label>
                                                    <div class="col-lg-8">
                                                        <select id="var_inspector" class="select2_demo_1 form-control">
                                                            <option disabled selected></option>
<option value="Chairperson, Vice Chairperson, Member, Member">Ma. Dorothy C. Hornido, RN|Diana L. Ebay|Claire A. Cañizares|Nhyrma Fe C. Tabugoc</option>
 <option value="Chairperson, Vice Chairperson, Member, Member">Gladys D. Buladaco, RN|Fernando L. Mina|Daryl A. Estrella|Claire A. Cañizares</option>
     <option value="Chairperson, Vice Chairperson, Member, Member">Leslie C. Cortes, RPh|Dorcas Ga-Dinhayan|Rossie Mae O. Tagudin|Claire A. Cañizares</option>
     <option value="Chairperson, Vice Chairperson, Member, Member, Member">Emmi Marie J. Cabahug, RN|Joy Dyana V. Cervantes, RN|Nadine Joyce C. Torres, RMT|Engr. Leore Ann M. Bagulor|Claire A. Cañizares</option>
      <option value="Chairperson, Vice Chairperson, Member">Neil H. Nituda, RMT|Marlon A. Cahayon|Daryl A. Estrella</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Supervisor (For DV):</label>
                                                    <div class="col-lg-8">
                                                        <select id="spvs" class="select2_demo_1 form-control">
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                                 <label style="display: none;"><input id="var_chk" type="checkbox" class="i-checks"> Inspected, verified and found in order as to quantity and specifications</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">
                                                <i class="fa fa-list"></i> Acceptance Information
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group row">
                                                            <label class="col-lg-5 col-form-label">Date Received:</label>
                                                            <input id="var_dr" class="col-lg-7 form-control" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" id="date" />
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">End User:</label>
                                                            <input id="var_eu" type="text" class="col-lg-8 form-control" disabled="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">Property Custodian:</label>
                                                    <input id="var_pc" type="text" class="col-lg-9 form-control" value="<?php echo $_SESSION["property_custodian"]; ?>">
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label">Status:</label>
                                                            <div class="col-lg-8">
                                                                <select id="var_as" class="form-control select2_demo_1">
                                                                    <option disabled selected></option>
                                                                    <option value="complete">Complete</option>
                                                                    <option value="partial">Partial</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div role="tabpanel" id="tab-2" class="tab-pane">
                            <div class="panel-body">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <i class="fa fa-info-circle"></i> IAR Information
                                    </div>
                                    <div class="panel-body">

                                    </div>
                                </div>
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <i class="fa fa-info-circle"></i> Item Details
                                    </div>
                                    <div class="panel-body" style="height: 220px; overflow: auto;">
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">
                                                <i class="fa fa-list"></i> Inspection Information
                                            </div>
                                            <div class="panel-body">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">
                                                <i class="fa fa-list"></i> Acceptance Information
                                            </div>
                                            <div class="panel-body">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save_changes" onclick="save_changes();">Save changes</button>
            </div>
        </div>
    </div>
</div>