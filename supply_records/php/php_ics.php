<?php

require "../../php/php_conn.php";
require "../../php/php_general_functions.php";

session_start();

function new_add_item(){

	global $conn;
	global $special_category;

	$table = "";
	$num_iss = mysqli_real_escape_string($conn, $_POST["num_iss"]);
    $item_name = mysqli_real_escape_string($conn, $_POST["item_name"]);
    $item_id = mysqli_real_escape_string($conn, $_POST["item_id"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $category = mysqli_real_escape_string($conn, $_POST["category"]);
    $stock = mysqli_real_escape_string($conn, $_POST["stock"]);
    $quantity = mysqli_real_escape_string($conn, $_POST["quantity"]);
    $reference_no = mysqli_real_escape_string($conn, $_POST["reference_no"]);
    $serial_no = mysqli_real_escape_string($conn, $_POST["serial_no"]);
    $unit = mysqli_real_escape_string($conn, $_POST["unit"]);
    $unit_value = mysqli_real_escape_string($conn, $_POST["unit_value"]);
	$property_no = mysqli_real_escape_string($conn, $_POST["property_no"]);
	$remarks = mysqli_real_escape_string($conn, $_POST["remarks"]);

    $sql = mysqli_query($conn, "SELECT * FROM tbl_ics WHERE ics_no LIKE '$num_iss'");
	$supplier = mysqli_real_escape_string($conn, mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.supplier, p.supplier_id FROM tbl_po AS p, ref_supplier AS s WHERE s.supplier_id = p.supplier_id AND p.po_number LIKE '$reference_no'"))["supplier"]);
	$row = mysqli_fetch_assoc($sql); $entity_name = $row["entity_name"]; $fund_cluster = $row["fund_cluster"]; $received_from = $row["received_from"]; 
	$received_from_designation = $row["received_from_designation"]; $date_released = $row["date_released"]; $area = $row["area"]; $view_ics = $row["view_ics"];
	$received_by = $row["received_by"]; $received_by_designation = $row["received_by_designation"]; $date_supply_received = $row["date_supply_received"]; $issued = $row["issued"];

	mysqli_query($conn, "INSERT INTO tbl_ics(ics_no, entity_name, fund_cluster, reference_no, item, description, unit, supplier, serial_no, category, property_no, quantity, cost, total, remarks, received_from, received_from_designation, received_by, received_by_designation, date_released, date_supply_received, issued, view_ics, area, po_id) 
	VALUES ('$num_iss', '$entity_name', '$fund_cluster', '$reference_no', '$item_name', '$description', '$unit', '$supplier', '$serial_no', '$category', '$property_no', '$quantity', '$unit_value', '0.00', '$remarks', '$received_from', '$received_from_designation', '$received_by', '$received_by_designation', '$date_released', '$date_supply_received', '$issued', '$view_ics', '$area', '$item_id')");
	
	$query_get_stocks = mysqli_query($conn, "SELECT quantity FROM tbl_po WHERE po_id = '$item_id'");
	$rstocks = explode(" ", mysqli_fetch_assoc($query_get_stocks)["quantity"]);
	$newrstocks = ((int)$rstocks[0] - (int)$quantity)." ".$rstocks[1];
	mysqli_query($conn, "UPDATE tbl_po SET quantity = '$newrstocks' WHERE po_id = '$item_id'");
	if(!in_array($category, $special_category)){
		$serials = explode(",", $serial_no);
		for($j = 0; $j < count($serials); $j++){
			$sn = $serials[$j];
			mysqli_query($conn, "UPDATE tbl_serial SET is_issued = 'Y' WHERE inventory_id = '$item_id' AND serial_no = '$sn'");
		}
		$pns = explode(",", $property_no);
		$pn = end($pns);
		$currentDate = date('Y-m');
		$pnDate = substr($pn, 0, 7);
		if ($currentDate === $pnDate) {
			mysqli_query($conn, "UPDATE ref_lastpn SET property_no = '$pn' WHERE id = 1");
		}
	}

	reload_item($num_iss);
}

function reload_item($num_iss){
	global $conn;

	$table = "";
	$sql = mysqli_query($conn, "SELECT ics_id, po_id, entity_name, received_from, received_from_designation, SUBSTRING(date_released,1,10) AS date_r, reference_no,  fund_cluster, received_by, received_by_designation, area, item, description, serial_no, category, property_no, quantity, unit, cost, total, remarks FROM tbl_ics WHERE ics_no LIKE '".$num_iss."'");
	while($row = mysqli_fetch_assoc($sql)){
		$table.="<tr>
					<td>".$row["item"]."</td>
					<td>".$row["description"]."</td>
					<td>".$row["serial_no"]."</td>
					<td>".$row["category"]."</td>
					<td>".$row["property_no"]."</td>
					<td onclick=\"edit_quantity('".$row["ics_id"]."','".$row["quantity"]."','".$row["reference_no"]."','".mysqli_real_escape_string($conn, $row["item"])."','".mysqli_real_escape_string($conn, $row["description"])."', 'tbl_ics', 'ics_id', '".$row["po_id"]."');\"><a><u>".$row["quantity"]."</u></a></td>
					<td>".$row["unit"]."</td>
					<td>".number_format((float)$row["cost"], 3)."</td>
					<td>".number_format((float)$row["cost"] * (float)$row["quantity"], 3)."</td>
					<td><input onblur=\"update_remarks('".$row["ics_id"]."', this.value, 'tbl_ics', 'ics_id');\" type=\"text\" value=\"".$row["remarks"]."\"></td>
					<td>
						<button class=\"btn btn-xs btn-info dim\" onclick=\"get_item_trans('".$row["ics_id"]."', 'tbl_ics', 'ics_id', 'ics_no', '".$row["quantity"]."', '".$row["property_no"]."');\"><i class=\"fa fa-exchange\"></i></button>
						<button class=\"btn btn-xs btn-success dim\" onclick=\"get_history('".$row['property_no']."');\"><i class=\"fa fa-history\"></i></button>
					</td>
					</tr>";
	}
	echo $table;
}

function create_trans(){
	global $conn; global $connhr;
	date_default_timezone_set("Asia/Shanghai");
	$id = mysqli_real_escape_string($conn, $_POST["id"]);
	$trans_ics = mysqli_real_escape_string($conn, $_POST["trans_ics"]);
	$received_by = mysqli_real_escape_string($conn, $_POST["trans_name"]);
	$trans_id = mysqli_real_escape_string($conn, $_POST["trans_id"]);
	$prop_no = implode(",",(array) $_POST["prop_no"]);
	$serial_no = implode(",",(array) $_POST["serial_no"]);
	$un_prop_no = implode(",",(array) $_POST["un_prop_no"]);
	$un_serial_no = implode(",",(array) $_POST["un_serial_no"]);
	$date_released = mysqli_real_escape_string($conn, $_POST["date_released"])." ".date("H:i:s");

	$type 		= mysqli_real_escape_string($conn, $_POST["type"]);
	$table 		= mysqli_real_escape_string($conn, $_POST["table"]);
	$table_id 	= mysqli_real_escape_string($conn, $_POST["table_id"]);
	$table_no 	= mysqli_real_escape_string($conn, $_POST["table_no"]);

	$quer1 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$trans_id'");
	$received_by_designation = mysqli_real_escape_string($conn, mysqli_fetch_assoc($quer1)["designation"]);

	$sql = mysqli_query($conn, "SELECT * FROM ".$table." WHERE ".$table_id." = '$id'");
	if($row = mysqli_fetch_assoc($sql)){
		$quantity_trans = count(explode(",", $prop_no));
		$received = ($type === "PTR") ? $row["to"] : $row["received_by"];
		$remarks = "This cancels previous " . $type . " issued to " . $received . " (" . $row[$table_no] . ")";
		mysqli_query($conn, "INSERT INTO tbl_ics(ics_no, entity_name, fund_cluster, reference_no, item, description, unit, supplier, serial_no, category, property_no, quantity, cost, total, remarks, received_from, received_from_designation, received_by, received_by_designation, date_released, area, po_id) VALUES ('$trans_ics', '".$row["entity_name"]."', '".$row["fund_cluster"]."', '".$row["reference_no"]."', '".$row["item"]."', '".$row["description"]."', '".$row["unit"]."', '".$row["supplier"]."', '$serial_no', '".$row["category"]."', '$prop_no', '$quantity_trans', '".$row["cost"]."', '0.00', '$remarks', '".$row["received_from"]."', '".$row["received_from_designation"]."', '$received_by', '$received_by_designation', '$date_released', '".$row["area"]."', '".$row["po_id"]."')");
		
		$quantity_new = (int)$row["quantity"] - $quantity_trans;
		mysqli_query($conn, "UPDATE ".$table." SET quantity = '$quantity_new' WHERE ".$table_id." = '$id'");

		$emp_id = $_SESSION["emp_id"];
		$description = $_SESSION["username"]." created an ICS transfer (".$trans_ics.") to ".$received_by." with a remarks - ".$remarks;
		mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
	}
}

function get_item_trans(){
	global $conn; $latest_icspar = ""; $tbody = "";

	$id = mysqli_real_escape_string($conn, $_POST["id"]);
	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$field_id = mysqli_real_escape_string($conn, $_POST["field_id"]);
	$yy_mm = substr(mysqli_real_escape_string($conn, $_POST["yy_mm"]), 0, 4);
	$field = mysqli_real_escape_string($conn, $_POST["field"]);

	$sql = mysqli_query($conn, "SELECT property_no, serial_no FROM ".$table." WHERE ".$field_id." = ".$id);
	while($row = mysqli_fetch_assoc($sql)){
		$props = explode(",", $row["property_no"]); $sers = explode(",", $row["serial_no"]);
		for($i = 0; $i < count($props); $i++){
			$tbody.="<tr>
									<td>".$props[$i]."</td>
									<td>".$sers[$i]."</td>
									<td><center><label class=\"col-form-label\"><input type=\"checkbox\" class=\"i-checks\" style=\"height: 18px; width: 18px;\"></label></center></td>
							</tr>";
		}
	}

	echo json_encode(array("tbody"=>$tbody));
}

function get_ics_par_no(){
	global $conn;

	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$field_id = mysqli_real_escape_string($conn, $_POST["field_id"]);
	$yy_mm = substr(mysqli_real_escape_string($conn, $_POST["yy_mm"]), 0, 4);
	$field = mysqli_real_escape_string($conn, $_POST["field"]);
	$latest_icspar = "";

	$sql = mysqli_query($conn, "SELECT DISTINCT ".$field." FROM ".$table." WHERE ".$field." LIKE '%$yy_mm%' ORDER BY ".$field_id." DESC LIMIT 1");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		$latest_icspar = str_pad(((int)explode("-", $row[$field])[2]) + 1, 4, '0', STR_PAD_LEFT);
	}else{
		$latest_icspar = "0001";
	}

	echo $latest_icspar;
}

function set_remarks(){
	global $conn;

	$id = mysqli_real_escape_string($conn, $_POST["id"]);
	$value = mysqli_real_escape_string($conn, $_POST["value"]);
	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$field_id = mysqli_real_escape_string($conn, $_POST["field_id"]);

	mysqli_query($conn, "UPDATE ".$table." SET remarks = '".$value."' WHERE ".$field_id." = '".$id."'");

}

function set_dr(){
	global $conn;

	$control_no = mysqli_real_escape_string($conn, $_POST["control_no"]);
	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$control = mysqli_real_escape_string($conn, $_POST["control"]);
	$new_date_dr = mysqli_real_escape_string($conn, $_POST["new_date_dr"]);
	$field = ($table == "tbl_ris") ? "date_received" : "date_supply_received";
	mysqli_query($conn, "UPDATE ".$table." SET ".$field." = '".$new_date_dr."' WHERE ".$control." LIKE '".$control_no."'");
}

function get_dr(){
	global $conn;

	$control_no = mysqli_real_escape_string($conn, $_POST["control_no"]);
	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$control = mysqli_real_escape_string($conn, $_POST["control"]);
	$field = ($table == "tbl_ris") ? "date_received" : "date_supply_received";
	$sql = mysqli_query($conn, "SELECT ".$field." FROM ".$table." WHERE ".$control." LIKE '".$control_no."'");

	echo substr(mysqli_fetch_assoc($sql)[$field], 0, 10);
}

function iss_validator(){
	global $conn;

	$ics_no = mysqli_real_escape_string($conn, $_POST["ics_no"]);
	$sql = mysqli_query($conn, "SELECT view_ics FROM tbl_ics WHERE ics_no = '$ics_no'");
	$view_ics = mysqli_fetch_assoc($sql)["view_ics"];
	$parts = explode(".", $view_ics);
	if(strtoupper(end($parts)) == "PDF"){
		echo "1";
	}else{
		echo "0";
	}
}

function update_quantity(){
	global $conn;

	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$field = mysqli_real_escape_string($conn, $_POST["field"]);
	$iss_id = mysqli_real_escape_string($conn, $_POST["iss_id"]);
	$item = mysqli_real_escape_string($conn, $_POST["item"]);
	$description = mysqli_real_escape_string($conn, $_POST["description"]);
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$quantity = mysqli_real_escape_string($conn, $_POST["quantity"]);
	$new_quantity = mysqli_real_escape_string($conn, $_POST["new_quantity"]);

	if($new_quantity != ""){
		$difference = (int)$quantity - (int)$new_quantity;
		$query_get_stocks = mysqli_query($conn, "SELECT quantity FROM tbl_po WHERE po_number = '$po_number' AND item_name = '$item' AND description = '$description'");
		$rstocks = explode(" ", mysqli_fetch_assoc($query_get_stocks)["quantity"]);
		$newrstocks = ((int)$rstocks[0] + (int)$difference)/*." ".$rstocks[1]*/;
		if($newrstocks >= 0){
			$newrstocks.=" ".$rstocks[1];
			mysqli_query($conn, "UPDATE tbl_po SET quantity = '$newrstocks' WHERE po_number = '$po_number' AND item_name = '$item' AND description = '$description'");
			mysqli_query($conn, "UPDATE ".$table." SET quantity = '$new_quantity' WHERE ".$field." = '$iss_id'");
			echo "1";
		}else{
			echo "0";
		}
	}
}

function get_pic(){
	global $conn;

	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$field = mysqli_real_escape_string($conn, $_POST["field"]);
	$iss_number = mysqli_real_escape_string($conn, $_POST["iss_number"]);
	$iss_field = mysqli_real_escape_string($conn, $_POST["iss_field"]);
	$sql = mysqli_query($conn, "SELECT ".$field." FROM ".$table." WHERE ".$iss_field." LIKE '".$iss_number."'");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		echo $row[$field];
	}
}

function update(){
	global $conn;

	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$field = mysqli_real_escape_string($conn, $_POST["field"]);
	$number=mysqli_real_escape_string($conn, $_POST["number"]);
	
	$entity_name=mysqli_real_escape_string($conn, $_POST["entity_name"]);
	$received_from=mysqli_real_escape_string($conn, $_POST["received_from"]);
	$received_from_designation=mysqli_real_escape_string($conn, $_POST["received_from_designation"]);
	$date_released=mysqli_real_escape_string($conn, $_POST["date_released"]);
	$reference_no=mysqli_real_escape_string($conn, $_POST["reference_no"]);
	$fund_cluster=mysqli_real_escape_string($conn, $_POST["fund_cluster"]);
	$received_by=mysqli_real_escape_string($conn, $_POST["received_by"]);
	$received_by_designation=mysqli_real_escape_string($conn, $_POST["received_by_designation"]);
	$area=mysqli_real_escape_string($conn, $_POST["area"]);

	$dtype = ($table == "tbl_ics") ? "ICS" : "PAR";
	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." edited the details of ".$dtype." No. ".$number;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
	
	mysqli_query($conn, "UPDATE ".$table." SET entity_name='$entity_name',received_from='$received_from',received_from_designation='$received_from_designation',date_released='$date_released',fund_cluster='$fund_cluster',received_by='$received_by',received_by_designation='$received_by_designation',area='$area' WHERE ".$field." LIKE '".$number."'");
}

function modify(){
	global $conn;

	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$field = mysqli_real_escape_string($conn, $_POST["field"]);
	$field_id = mysqli_real_escape_string($conn, $_POST["field_id"]);
	$number = mysqli_real_escape_string($conn, $_POST["number"]);

	$entity_name="";$received_from="";$received_from_designation="";$date_released="";$reference_no="";$fund_cluster="";$received_by="";$received_by_designation="";$area=""; $tabled = ""; $tot_amt = 0.00;
	$sql = mysqli_query($conn, "SELECT ".$field_id.", po_id, entity_name, received_from, received_from_designation, SUBSTRING(date_released,1,10) AS date_r, reference_no,  fund_cluster, received_by, received_by_designation, area, item, description, serial_no, category, property_no, quantity, unit, cost, total, remarks FROM ".$table." WHERE ".$field." LIKE '".$number."'");
	while($row = mysqli_fetch_assoc($sql)){
		$entity_name=$row["entity_name"];$received_from=$row["received_from"];$received_from_designation=$row["received_from_designation"];$date_released=$row["date_r"];$reference_no=$row["reference_no"];$fund_cluster=$row["fund_cluster"];$received_by=$row["received_by"];$received_by_designation=$row["received_by_designation"];$area=$row["area"];
		$tabled.="<tr>
					<td>".$row["item"]."</td>
					<td>".$row["description"]."</td>
					<td>".$row["serial_no"]."</td>
					<td>".$row["category"]."</td>
					<td>".$row["property_no"]."</td>
					<td onclick=\"edit_quantity('".$row[$field_id]."','".$row["quantity"]."','".$row["reference_no"]."','".mysqli_real_escape_string($conn, $row["item"])."','".mysqli_real_escape_string($conn, $row["description"])."', '$table', '$field_id', '".$row["po_id"]."');\"><a><u>".$row["quantity"]."</u></a></td>
					<td>".$row["unit"]."</td>
					<td>".number_format((float)$row["cost"], 3)."</td>
					<td>".number_format((float)$row["cost"] * (float)$row["quantity"], 3)."</td>
					<td><input onblur=\"update_remarks('".$row[$field_id]."', this.value, '".$table."', '".$field_id."');\" type=\"text\" value=\"".$row["remarks"]."\"></td>
					<td>
						<button class=\"btn btn-xs btn-info dim\" onclick=\"get_item_trans('".$row[$field_id]."', '".$table."', '".$field_id."', '".$field."', '".$row["quantity"]."', '".$row["property_no"]."');\"><i class=\"fa fa-exchange\"></i></button>
						<button class=\"btn btn-xs btn-success dim\" onclick=\"get_history('".$row['property_no']."');\"><i class=\"fa fa-history\"></i></button>
					</td>
					</tr>";
					$tot_amt+=(float)$row["cost"] * (float)$row["quantity"];
	}
	
	echo json_encode(array(
		"entity_name"=>$entity_name,
		"received_from"=>$received_from,
		"received_from_designation"=>$received_from_designation,
		"date_released"=>$date_released,
		"reference_no"=>$reference_no,
		"fund_cluster"=>$fund_cluster,
		"received_by"=>$received_by,
		"received_by_designation"=>$received_by_designation,
		"area"=>$area,
		"tabled"=>$tabled,
		"tot_amt"=>$tot_amt
	));
}

function delete_record(){
	global $conn;

	$field = mysqli_real_escape_string($conn, $_POST["field"]);
	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$number=mysqli_real_escape_string($conn, $_POST["number"]);
	$sql = mysqli_query($conn, "SELECT item, description, quantity, serial_no, reference_no, po_id FROM ".$table." WHERE ".$field." LIKE '".$number."'");
	while($row = mysqli_fetch_assoc($sql)){
		$item = mysqli_real_escape_string($conn, $row["item"]); $description = mysqli_real_escape_string($conn, $row["description"]); $reference_no = mysqli_real_escape_string($conn, $row["reference_no"]); $quantity = $row["quantity"]; $serial_no = $row["serial_no"];$pid = $row["po_id"];
		$po_id = ($pid == "0") ? "" : " AND po_id = '".$pid."'";
		$query_get_stocks = mysqli_query($conn, "SELECT quantity FROM tbl_po WHERE po_number = '$reference_no' AND item_name = '$item' AND description = '$description'".$po_id);
		$rstocks = explode(" ", mysqli_fetch_assoc($query_get_stocks)["quantity"]);
		$newrstocks = ((int)$rstocks[0] + (int)$quantity)." ".$rstocks[1];
		mysqli_query($conn, "UPDATE tbl_po SET quantity = '$newrstocks' WHERE po_number = '$reference_no' AND item_name = '$item' AND description = '$description'".$po_id);
		if($row["serial_no"] != null){
			$serials = explode(",", $row["serial_no"]);
			for($j = 0; $j < count($serials); $j++){
				$sn = $serials[$j];
				mysqli_query($conn, "UPDATE tbl_serial SET is_issued = 'N' WHERE serial_no = '$sn'");
			}
		}
	}
	mysqli_query($conn, "DELETE FROM ".$table." WHERE ".$field." LIKE '".$number."'");
	$dtype = ($table == "tbl_ics") ? "ICS" : (($table == "tbl_par") ? "PAR" : "PTR");
	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." deleted a record ".$dtype." No. ".$number;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
}

function to_issue(){
	global $conn;
	$ics_no = mysqli_real_escape_string($conn, $_POST["ics_no"]);
	mysqli_query($conn, "UPDATE tbl_ics SET issued = '1' WHERE ics_no = '$ics_no'");
}

function get_item_details(){
	global $conn;

	$item_id = mysqli_real_escape_string($conn, $_POST["item_id"]);
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$sql = mysqli_query($conn, "SELECT category, exp_date, quantity, description, sn_ln, unit_cost FROM tbl_po WHERE po_id = '$item_id' AND po_number = '$po_number'");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		$q_u = explode(" ", $row["quantity"]);
		$sql2 = mysqli_query($conn, "SELECT serial_id, serial_no FROM tbl_serial WHERE is_issued = 'N' AND inventory_id = '$item_id'");
		$sl_options = "";
		if(mysqli_num_rows($sql2) != 0){
			while($rows = mysqli_fetch_assoc($sql2)){
				$sl_options.="<option value=\"".$rows["serial_no"]."\"><p style=\"font-color: black;\">".$rows["serial_no"]."</p></option>";
			}	
		}
		$category = $row["category"];
		$code = mysqli_fetch_assoc(mysqli_query($conn, "SELECT category_code FROM ref_category WHERE category = '$category'"))["category_code"];
		echo json_encode(array("stocks"=>$q_u[0],"unit"=>$q_u[1],"description"=>$row["description"],"unit_cost"=>$row["unit_cost"],"exp_date"=>$row["exp_date"],"category"=>$row["category"],"sn_ln"=>$sl_options,"code"=>$code));
	}
}

function get_item() {
	global $conn;

	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$fund_cluster_result = mysqli_query($conn, "SELECT fund_cluster FROM tbl_iar WHERE po_number LIKE '$po_number' LIMIT 1");
	$fund_cluster = '';
	if (mysqli_num_rows($fund_cluster_result) > 0) {
		$row_fc = mysqli_fetch_assoc($fund_cluster_result);
		$fund_cluster = $row_fc['fund_cluster'];
	}
	$sql = mysqli_query($conn, "SELECT p.quantity, p.po_id, p.item_id, i.item 
		FROM tbl_po AS p, ref_item AS i 
		WHERE p.item_id = i.item_id 
		AND p.po_number LIKE '$po_number' 
		AND p.inspection_status = '1'");

	$output = '';
	if (mysqli_num_rows($sql) != 0) {
		while ($row = mysqli_fetch_assoc($sql)) {
			$quan = explode(" ", $row["quantity"]);
			if ((int)$quan[0] != 0) {
				$output .= "<option value=\"".$row["po_id"]."\">".$row["item"]."</option>";
			}
		}
	}
	echo json_encode(array('options' => $output, 'fund_cluster' => $fund_cluster));
}

function get_category(){
	global $conn;
	$sql = mysqli_query($conn, "SELECT category_id, category, category_code FROM ref_category WHERE status = '0' ORDER BY category_id ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option value=".$row["category_id"]."┼".$row["category_code"].">".$row["category"]."</option>";
		}
	}
}

function get_area(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT area_id, area FROM ref_area WHERE status = '0' ORDER BY area_id ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option value=\"".$row["area_id"]."\">".$row["area"]."</option>";
		}
	}
}

function get_employee() {
    global $connhr;
    $sql = mysqli_query($connhr, "SELECT emp_id, fname, mname, lname, prefix, suffix FROM tbl_employee WHERE status = 'Active' ORDER BY fname ASC");

    if (mysqli_num_rows($sql) != 0) {
        $options = '';
        $employees = [];

        while ($row = mysqli_fetch_assoc($sql)) {
            $prefix = !empty($row["prefix"]) ? $row["prefix"] . " " : "";
            $mname_initial = !empty($row["mname"]) ? $row["mname"][0] . ". " : "";
            $suffix = !empty($row["suffix"]) ? ", " . $row["suffix"] : "";

            $full_name = "{$prefix}{$row['fname']} {$mname_initial}{$row['lname']}{$suffix}";

            $options .= "<option value=\"{$row['emp_id']}\">{$full_name}</option>";
            $employees[] = [
                'id' => $row['emp_id'],
                'name' => $full_name
            ];
        }

		echo json_encode([
            'options' => $options,
            'employees' => $employees
        ]);
    }
}

function get_ics_details(){
  global $conn;

  $entity_name = "";
  $fund_cluster = "";
  $ics_tbody = "";
  $total_cost = 0.00; $reference_no = ""; $supplier = ""; $date_released = "";
  $received_from = ""; $received_by = "";
  $received_from_designation = ""; $received_by_designation = ""; $remarks = "";
  $rows_limit = 43; $rows_occupied = 0;
  $ics_no = mysqli_real_escape_string($conn, $_POST["ics_no"]);
  $sql = mysqli_query($conn, "SELECT entity_name, fund_cluster, quantity, unit, cost, total, item, description, category, property_no, serial_no, reference_no, supplier, SUBSTRING(date_released, 1, 10) AS date_r, received_from, received_from_designation, received_by, received_by_designation, remarks FROM tbl_ics WHERE ics_no LIKE '$ics_no'");
  if(mysqli_num_rows($sql) != 0){
    while($row = mysqli_fetch_assoc($sql)) {
      $entity_name = $row["entity_name"];
      $fund_cluster = $row["fund_cluster"];
      $total_cost += (float)($row["cost"] * $row["quantity"]);
      $reference_no = $row["reference_no"]; $supplier = $row["supplier"]; $date_released = $row["date_r"];
      $received_from = $row["received_from"]; $received_by = $row["received_by"];
      $received_from_designation = $row["received_from_designation"]; $received_by_designation = $row["received_by_designation"];
      $remarks = $row["remarks"];
      $pn = explode(",", $row["property_no"]);
      $ics_tbody.="<tr>
                  <td style=\"width: 75.6px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\">".$row["quantity"]."</td>
                  <td style=\"width: 70.8px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["unit"]."</td>
                  <td style=\"width: 61.8px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$row["cost"], 3)."</td>
                  <td style=\"width: 62.4px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)($row["cost"] * $row["quantity"]), 3)."</td>
                  <td colspan=\"2\" style=\"width: 48px; height: 14.5px; text-align: left; font-size: 10px; vertical-align: center; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; border-right-color: rgb(0, 0, 0); border-right-width: 1px; border-right-style: solid;\"><b>".$row["item"]."</b><br>".$row["description"]."</td>
                  <td style=\"width: 86.4px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".((count($pn) >= 1 && $row["serial_no"] == null) ? $pn[0] : "")."</td>
                  <td style=\"width: 89.4px; height: 14.5px; font-size: 11px; text-align: center; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".get_estimated_life($row["category"])." years</td>
               </tr>";$rows_occupied++;
               $rows_occupied+=round((float)strlen($row["description"]) / 60.00);
               if($row["serial_no"] == null && count($pn) > 1){
                for($j = 1; $j < count($pn); $j++){
                  $ics_tbody.="<tr>
                      <td style=\"width: 75.6px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
                      <td style=\"width: 70.8px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                      <td style=\"width: 61.8px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                      <td style=\"width: 62.4px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                      <td colspan=\"2\" style=\"width: 48px; height: 14.5px; text-align: left; font-size: 10px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; border-right-color: rgb(0, 0, 0); border-right-width: 1px; border-right-style: solid;\"></td>
                      <td style=\"width: 86.4px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$pn[$j]."</td>
                      <td style=\"width: 89.4px; height: 14.5px; font-size: 11px; vertical-align: bottom; text-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                   </tr>";
                   $rows_occupied++;
                }
               }
               if($row["serial_no"] != null){
                $serials = explode(",", $row["serial_no"]);
                for($i = 0; $i < count($serials); $i++){
                  if(!array_key_exists($i, $pn)){
                    $ics_tbody.="<tr>
                          <td style=\"width: 75.6px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
                          <td style=\"width: 70.8px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                          <td style=\"width: 61.8px; height: 14.5px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                          <td style=\"width: 62.4px; height: 14.5px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                          <td colspan=\"2\" style=\"width: 48px; height: 14.5px; text-align: left; font-size: 10px; vertical-align: center; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; border-right-color: rgb(0, 0, 0); border-right-width: 1px; border-right-style: solid;\">Serial No. ".$serials[$i]."</td>
                          <td style=\"width: 86.4px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                          <td style=\"width: 89.4px; height: 14.5px; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                       </tr>";
                       $rows_occupied++;
                  }else{
                    $ics_tbody.="<tr>
                          <td style=\"width: 75.6px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
                          <td style=\"width: 70.8px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                          <td style=\"width: 61.8px; height: 14.5px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                          <td style=\"width: 62.4px; height: 14.5px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                          <td colspan=\"2\" style=\"width: 48px; height: 14.5px; text-align: left; font-size: 10px; vertical-align: center; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; border-right-color: rgb(0, 0, 0); border-right-width: 1px; border-right-style: solid;\">Serial No. ".$serials[$i]."</td>
                          <td style=\"width: 86.4px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$pn[$i]."</td>
                          <td style=\"width: 89.4px; height: 14.5px; font-size: 11px; text-align: center; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                       </tr>";
                  }
                  $rows_occupied++;
                }
               }
    }
    $the_rest = array("*Nothing Follows*", "", "", "PO No. ".$reference_no, $supplier, "", "", "", "", "<p style=\"font-size: 14px;\">".$remarks."</p>");
    for($i = 0; $i < count($the_rest); $i++){
      $ics_tbody.="<tr>
                <td style=\"width: 75.6px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
                <td style=\"width: 70.8px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                <td style=\"width: 61.8px; height: 14.5px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                <td style=\"width: 62.4px; height: 14.5px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                <td colspan=\"2\" style=\"width: 48px; height: 14.5px; text-align: left; font-size: 10px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; border-right-color: rgb(0, 0, 0); border-right-width: 1px; border-right-style: solid;\">".$the_rest[$i]."</td>
                <td style=\"width: 86.4px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                <td style=\"width: 89.4px; height: 14.5px; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
             </tr>";
             $rows_occupied = $rows_occupied + ($i == count($the_rest) - 1 ? 3 : 1);
    }
    for($i = 0; $i < ($rows_limit - $rows_occupied); $i++){
      $ics_tbody.="<tr>
                <td style=\"width: 75.6px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
                <td style=\"width: 70.8px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                <td style=\"width: 61.8px; height: 14.5px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                <td style=\"width: 62.4px; height: 14.5px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                <td colspan=\"2\" style=\"width: 48px; height: 14.5px; text-align: left; font-size: 10px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; border-right-color: rgb(0, 0, 0); border-right-width: 1px; border-right-style: solid;\">-</td>
                <td style=\"width: 86.4px; height: 14.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
                <td style=\"width: 89.4px; height: 14.5px; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
             </tr>";
    }
    $received_by = get_complete_name($received_by);
    echo json_encode(array("entity_name"=>$entity_name, "fund_cluster"=>$fund_cluster, "ics_tbody"=>$ics_tbody, "total_cost"=>number_format((float)$total_cost,3), "receivers"=>array($received_from, $received_from_designation, $received_by, $received_by_designation, _m_d_yyyy_($date_released))));
  }
}

function get_records(){
	global $conn;

	$limit = '10';
	$page = 1;
	if($_POST["page"] > 1){
	  $start = (($_POST["page"] - 1) * $limit);
	  $page = $_POST["page"];
	}else{
	  $start = 0;
	}

	$query = "SELECT DISTINCT ics_no, area, SUBSTRING(date_released, 1, 10) AS date_r, received_from, received_by, issued FROM tbl_ics ";
	if($_POST["search"] != ""){
		$qs = mysqli_real_escape_string($conn, $_POST["search"]);
		$query.="WHERE ics_no LIKE '%$qs%' OR reference_no LIKE '%$qs%' OR area LIKE '%$qs%' OR received_from LIKE '%$qs%' OR received_by LIKE '%$qs%' OR item LIKE '%$qs%' ";
	}
	$query.="ORDER BY ics_id DESC ";
	
	$sql_orig = mysqli_query($conn, $query);
	$sql = mysqli_query($conn, $query."LIMIT ".$start.", ".$limit."");
	$tbody = "";
	$total_data = mysqli_num_rows($sql_orig);
	if($total_data != 0){
		while($row = mysqli_fetch_assoc($sql)){
			$icsn = $row["ics_no"];
			$rb = str_replace(' ', '', $row["received_by"]);
			$rb = rtrim($rb, '.');
			$in = array();
			$get_items = mysqli_query($conn, "SELECT item FROM tbl_ics WHERE ics_no LIKE '$icsn'");
			while($ri = mysqli_fetch_assoc($get_items)){
				array_push($in, $ri["item"]);
			}
			$refs = array();
			$get_rf = mysqli_query($conn, "SELECT DISTINCT reference_no FROM tbl_ics WHERE ics_no LIKE '$icsn'");
			while($rf = mysqli_fetch_assoc($get_rf)){
				array_push($refs, $rf["reference_no"]);
			}
			$rems = array();
			$get_rf = mysqli_query($conn, "SELECT DISTINCT remarks FROM tbl_ics WHERE ics_no LIKE '$icsn'");
			while($rf = mysqli_fetch_assoc($get_rf)){
				array_push($rems, $rf["remarks"]);
			}
			$tbody.="<tr>
					<td><center>".(($row["issued"] == '0') ? "<button id=\"\" value=\"".$row["ics_no"]."\" ".(($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU") ? "onclick=\"to_issue(this.value, this.id);\"" : "")." class=\"btn btn-xs btn-danger\" style=\"border-radius: 10px;\">✖</button>" : "<button class=\"btn btn-xs\" style=\"border-radius: 10px; background-color: #00FF00; color: white; font-weight: bold;\" value=\"".$row["ics_no"]."\" onclick=\"modify_dr(this.value, 'ICS No. '+this.value, 'tbl_ics', 'ics_no');\">✓</button>")."</center></td>
					<td>".$row["area"]."</td>
					<td>".$row["ics_no"]."</td>
					<td style=\"font-size: 10px;\">".implode(", ", $refs)."</td>
					<td style=\"font-size: 10px;\">".implode(", ", $in)."</td>
					<td>".$row["date_r"]."</td>
					<td>".$row["received_from"]."</td>
					<td>".$row["received_by"]."</td>
					<td style=\"font-size: 12px;\">".implode(", ", $rems)."</td>
					<td><center><button class=\"btn btn-xs btn-primary dim\" value=\"".$row["ics_no"]."\" onclick=\"view_iss(this.value,'tbl_ics','view_ics','ICS','ics_no','".$rb."');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Preview\"><i class=\"fa fa-picture-o\"></i></button>&nbsp;".(($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU") ? "<button class=\"btn btn-xs btn-info dim\" data-toggle=\"tooltip\" value=\"".$row["ics_no"]."\" data-placement=\"top\" title=\"Edit\" onclick=\"modify(this.value);\"><i class=\"fa fa-pencil-square-o\"></i></button>&nbsp;" : "")."<button class=\"btn btn-xs btn-success dim\" value=\"".$row["ics_no"]."\" onclick=\"print_ics(this.value);\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Print\"><i class=\"fa fa-print\"></i></button>&nbsp;".(($_SESSION["role"] == "SUPPLY_SU") ? "<button class=\"btn btn-xs btn-danger dim\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete\" value=\"".$row["ics_no"]."\" onclick=\"delete_control(this.value);\"><i class=\"fa fa-trash\"></i></button>&nbsp;" : "")."<button class=\"btn btn-xs btn-warning dim\" value=\"".$row["ics_no"]."\" onclick=\"download_xls(this.value);\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Save as Excel\"><i class=\"fa fa-file-excel-o\"></i></button></center></td>
				</tr>";
		}
	}else{
		$tbody = "<tr><td colspan=\"11\" style=\"text-align: center;\">No data found.</td></tr>";
	}
	$in_out = create_table_pagination($page, $limit, $total_data, array("","Area","ICS No.","PO No.","Items","Date Released","Received From", "Received By", "Remarks", ""));
	$whole_dom = $in_out[0]."".$tbody."".$in_out[1];
	echo $whole_dom;
}

function insert_ics(){
	global $conn;
	global $connhr;
	date_default_timezone_set("Asia/Shanghai");
	$time_now = date("H:i:s");
	$ics_no = mysqli_real_escape_string($conn, $_POST["ics_no"]);
	$entity_name = mysqli_real_escape_string($conn, $_POST["entity_name"]);
	$fund_cluster = mysqli_real_escape_string($conn, $_POST["fund_cluster"]);
	$reference_no = mysqli_real_escape_string($conn, $_POST["reference_no"]);
	$received_from_id = mysqli_real_escape_string($conn, $_POST["received_from_id"]);
	$received_from = mysqli_real_escape_string($conn, $_POST["received_from"]);
	$received_by_id = mysqli_real_escape_string($conn, $_POST["received_by_id"]);
	$received_by = mysqli_real_escape_string($conn, $_POST["received_by"]);
	$date_released = mysqli_real_escape_string($conn, $_POST["date_released"])." ".$time_now;
	$area = mysqli_real_escape_string($conn, $_POST["area"]);
	$items = $_POST["items"];

	$quer1 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$received_from_id'");
	$quer2 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$received_by_id'");
	$query = mysqli_query($conn, "SELECT s.supplier, p.supplier_id FROM tbl_po AS p, ref_supplier AS s WHERE s.supplier_id = p.supplier_id AND p.po_number LIKE '$reference_no'");
	$received_from_designation = mysqli_real_escape_string($conn, mysqli_fetch_assoc($quer1)["designation"]);
	$received_by_designation = mysqli_real_escape_string($conn, mysqli_fetch_assoc($quer2)["designation"]);

	$supplier = mysqli_real_escape_string($conn, mysqli_fetch_assoc($query)["supplier"]);
	if(mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT ics_no FROM tbl_ics WHERE ics_no = '$ics_no'"))==0){
		$emp_id = $_SESSION["emp_id"];
		$description = $_SESSION["username"]." created an ICS No. ".$ics_no;
		mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
		echo "0";
		for($i = 0; $i < count($items); $i++){
			$item_id = $items[$i][0];
			$ref_no = $items[$i][1];
			$item = mysqli_real_escape_string($conn, $items[$i][2]);
			$description = mysqli_real_escape_string($conn, $items[$i][3]);
			$serial_no = mysqli_real_escape_string($conn, $items[$i][4]);
			$category = $items[$i][5];
			$property_no = $items[$i][6];
			$quantity = $items[$i][7];
			$unit = $items[$i][8];
			$cost = $items[$i][9];
			$total = $items[$i][10];
			$remarks = $items[$i][11];
			mysqli_query($conn, "INSERT INTO tbl_ics(ics_no, entity_name, fund_cluster, reference_no, item, description, unit, supplier, serial_no, category, property_no, quantity, cost, total, remarks, received_from, received_from_designation, received_by, received_by_designation, date_released, area, po_id) VALUES ('$ics_no', '$entity_name', '$fund_cluster', '$ref_no', '$item', '$description', '$unit', '$supplier', '$serial_no', '$category', '$property_no', '$quantity', '$cost', '$total', '$remarks', '$received_from', '$received_from_designation', '$received_by', '$received_by_designation', '$date_released', '$area', '$item_id')");
			$query_get_stocks = mysqli_query($conn, "SELECT quantity FROM tbl_po WHERE po_number = '$ref_no' AND po_id = '$item_id'");
			$rstocks = explode(" ", mysqli_fetch_assoc($query_get_stocks)["quantity"]);
			$newrstocks = ((int)$rstocks[0] - (int)$quantity)." ".$rstocks[1];
			mysqli_query($conn, "UPDATE tbl_po SET quantity = '$newrstocks' WHERE po_number = '$ref_no' AND po_id = '$item_id'");
			$serials = explode(",", $serial_no);
			for($j = 0; $j < count($serials); $j++){
				$sn = $serials[$j];
				mysqli_query($conn, "UPDATE tbl_serial SET is_issued = 'Y' WHERE inventory_id = '$item_id' AND serial_no = '$sn'");
			}
			$pns = explode(",", $property_no);
			$pn = end($pns);
			$currentDate = date('Y-m');
			$pnDate = substr($pn, 0, 7);
			if ($currentDate === $pnDate) {
				mysqli_query($conn, "UPDATE ref_lastpn SET property_no = '$pn' WHERE id = 1");
			}
		}
	}else{
		echo "1";
	}
}

function get_latest_ics(){
	global $conn; $latest_ics = ""; $latest_pn = "";
	
	$yy_mm = substr(mysqli_real_escape_string($conn, $_POST["yy_mm"]), 0, 4);
	$sql = mysqli_query($conn, "SELECT DISTINCT ics_no FROM tbl_ics WHERE ics_no LIKE '%$yy_mm%' ORDER BY ics_id DESC LIMIT 1");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		$latest_ics = str_pad(((int)explode("-", $row["ics_no"])[2]) + 1, 4, '0', STR_PAD_LEFT);
	}else{
		$latest_ics = "0001";
	}
	$sql = mysqli_query($conn, "SELECT property_no FROM ref_lastpn WHERE id = 1 AND property_no LIKE '%$yy_mm%'");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		$latest_pn = str_pad(((int)explode("-", $row["property_no"])[2]) + 1, 4, '0', STR_PAD_LEFT);
	}else{
		$latest_pn = "0001";
	}

	echo json_encode(array("latest_ics"=>$latest_ics,"latest_pn"=>$latest_pn));
}

$call_func = mysqli_real_escape_string($conn, $_POST["call_func"]);
switch($call_func){
	case "insert_ics":
		insert_ics();
		break;
	case "get_records":
		get_records();
		break;
	case "get_area":
		get_area();
		break;
	case "get_employee":
		get_employee();
		break;
	case "get_category":
		get_category();
		break;
	case "get_ics_details":
		get_ics_details();
		break;
	case "get_item":
		get_item();
		break;
	case "get_item_details":
		get_item_details();
		break;
	case "get_latest_ics":
		get_latest_ics();
		break;
	case "get_latest_pn":
		get_latest_pn();
		break;
	case "to_issue":
		to_issue();
		break;
	case "delete":
		delete_record();
		break;
	case "modify":
		modify();
		break;
	case "update":
		update();
		break;
	case "get_pic":
		get_pic();
		break;
	case "update_quantity":
		update_quantity();
		break;
	case "check_pn_exist":
		check_pn_exist();
		break;
	case "iss_validator":
		iss_validator();
		break;
	case "get_dr":
		get_dr();
		break;
	case "set_dr":
		set_dr();
		break;
	case "set_remarks":
		set_remarks();
		break;
	case "get_item_trans":
		get_item_trans();
		break;
	case "create_trans":
		create_trans();
		break;
	case "get_ics_par_no":
		get_ics_par_no();
		break;
	case "trace_origins":
		trace_origins();
		break;
	case "new_add_item":
		new_add_item();
		break;
}

?>