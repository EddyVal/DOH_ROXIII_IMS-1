<?php

require "../../php/php_conn.php";
require "../../php/php_general_functions.php";

session_start();

function create_trans(){
	global $conn; global $connhr;

	$id = mysqli_real_escape_string($conn, $_POST["id"]);
	$trans_ics = mysqli_real_escape_string($conn, $_POST["trans_ics"]);
	$requested_by = mysqli_real_escape_string($conn, $_POST["trans_name"]);
	$trans_id = mysqli_real_escape_string($conn, $_POST["trans_id"]);
	$issued_by = mysqli_real_escape_string($conn, $_POST["issue_name"]);
	$issued_by_id = mysqli_real_escape_string($conn, $_POST["issue_id"]);
	$approved_by = mysqli_real_escape_string($conn, $_POST["approve_name"]);
	$approved_by_id = mysqli_real_escape_string($conn, $_POST["approve_id"]);
	$prop_no = implode(",",(array) $_POST["prop_no"]);
	$serial_no = implode(",",(array) $_POST["serial_no"]);
	$un_prop_no = implode(",",(array) $_POST["un_prop_no"]);
	$un_serial_no = implode(",",(array) $_POST["un_serial_no"]);
	$date_released = mysqli_real_escape_string($conn, $_POST["date_released"]);

	$type 		= mysqli_real_escape_string($conn, $_POST["type"]);
	$table 		= mysqli_real_escape_string($conn, $_POST["table"]);
	$table_id 	= mysqli_real_escape_string($conn, $_POST["table_id"]);
	$table_no 	= mysqli_real_escape_string($conn, $_POST["table_no"]);

	$quer1	 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$trans_id'");
	$requested_by_designation = mysqli_real_escape_string($conn, mysqli_fetch_assoc($quer1)["designation"]);

	$quer2 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$issued_by_id'");
	$issued_by_designation = mysqli_real_escape_string($conn, mysqli_fetch_assoc($quer2)["designation"]);

	$quer3 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$approved_by_id'");
	$approved_by_designation = mysqli_real_escape_string($conn, mysqli_fetch_assoc($quer3)["designation"]);
	
	$sql = mysqli_query($conn, "SELECT * FROM ".$table." WHERE ".$table_id." = '$id'");
	if($row = mysqli_fetch_assoc($sql)){
		$quantity_trans = count(explode(",", $prop_no));
		$remarks = "This cancels previous ".$type." issued to ".$row["received_by"]." (".$row[$table_no].")";
		echo "INSERT INTO tbl_ris(ris_no, entity_name, fund_cluster, reference_no, item, `description`, unit, supplier, lot_no, category, property_no, quantity, cost, total, remarks, requested_by, requested_by_designation, issued_by, issued_by_designation, approved_by, approved_by_designation, date, po_id) 
		 											VALUES ('$trans_ics', '".$row["entity_name"]."', '".$row["fund_cluster"]."', '".$row["reference_no"]."', '".$row["item"]."', '".$row["description"]."', '".$row["unit"]."', '".$row["supplier"]."', '$serial_no', '".$row["category"]."', '$prop_no', '$quantity_trans', '".$row["cost"]."', '0.00', '$remarks', '$requested_by', '$requested_by_designation', '$issued_by', '$issued_by_designation', '$approved_by', '$approved_by_designation', '$date_released', '".$row["po_id"]."')";
		// mysqli_query($conn, "INSERT INTO tbl_ris(ris_no, entity_name, fund_cluster, reference_no, item, `description`, unit, supplier, lot_no, category, property_no, quantity, cost, total, remarks, requested_by, requested_by_designation, issued_by, issued_by_designation, approved_by, approved_by_designation, date, po_id) 
		// 											VALUES ('$trans_ics', '".$row["entity_name"]."', '".$row["fund_cluster"]."', '".$row["reference_no"]."', '".$row["item"]."', '".$row["description"]."', '".$row["unit"]."', '".$row["supplier"]."', '$serial_no', '".$row["category"]."', '$prop_no', '$quantity_trans', '".$row["cost"]."', '0.00', '$remarks', '$requested_by', '$requested_by_designation', '$issued_by', '$issued_by_designation', '$approved_by', '$approved_by_designation', '$date_released', '".$row["po_id"]."')");
		
		$quantity_new = (int)$row["quantity"] - $quantity_trans;
		mysqli_query($conn, "UPDATE ".$table." SET property_no = '$un_prop_no', serial_no = '$un_serial_no', quantity = '$quantity_new' WHERE ".$table_id." = '$id'");

		$emp_id = $_SESSION["emp_id"];
		$description = $_SESSION["username"]." created an RIS transfer (".$trans_ics.") to ".$requested_by." with a remarks - ".$remarks;
		mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
	}
}

function delete_existing(){
	global $conn;

	$po_id = mysqli_real_escape_string($conn, $_POST["po_id"]);
	$iss_id = mysqli_real_escape_string($conn, $_POST["iss_id"]);
	$iss_no = mysqli_real_escape_string($conn, $_POST["iss_no"]);
	$quan = mysqli_real_escape_string($conn, $_POST["quan"]);

	$query_get_stocks = mysqli_query($conn, "SELECT quantity FROM tbl_po WHERE po_id = '$po_id'");
	$rstocks = explode(" ", mysqli_fetch_assoc($query_get_stocks)["quantity"]);
	$newrstocks = ((int)$rstocks[0] + (int)$quan)." ".$rstocks[1];
	mysqli_query($conn, "UPDATE tbl_po SET quantity = '$newrstocks' WHERE po_id = '$po_id'");
	mysqli_query($conn, "DELETE FROM tbl_ris WHERE ris_id LIKE '".$iss_id."'");

	reload_item($iss_no);
}

function new_add_item(){
	global $conn;

	$num_iss = mysqli_real_escape_string($conn, $_POST["num_iss"]);
    $item_name = mysqli_real_escape_string($conn, $_POST["item_name"]);
    $item_id = mysqli_real_escape_string($conn, $_POST["item_id"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $category = mysqli_real_escape_string($conn, $_POST["category"]);
    $stock = mysqli_real_escape_string($conn, $_POST["stock"]);
    $quantity = mysqli_real_escape_string($conn, $_POST["quantity"]);
    $reference_no = mysqli_real_escape_string($conn, $_POST["reference_no"]);
    $serial_no = mysqli_real_escape_string($conn, $_POST["serial_no"]);
    $exp_date = mysqli_real_escape_string($conn, $_POST["exp_date"]);
    $unit = mysqli_real_escape_string($conn, $_POST["unit"]);
    $unit_value = mysqli_real_escape_string($conn, $_POST["unit_value"]);

    $sql = mysqli_query($conn, "SELECT entity_name,fund_cluster,division,office,rcc,reference_no,requested_by,requested_by_designation,issued_by,issued_by_designation,approved_by,approved_by_designation,tbl_ris.date,view_ris,purpose FROM tbl_ris WHERE ris_no LIKE '$num_iss'");
	$supplier = mysqli_real_escape_string($conn, mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.supplier, p.supplier_id FROM tbl_po AS p, ref_supplier AS s WHERE s.supplier_id = p.supplier_id AND p.po_number LIKE '$reference_no'"))["supplier"]);
   	$row = mysqli_fetch_assoc($sql); $entity_name = $row["entity_name"]; $fund_cluster = $row["fund_cluster"]; $division = $row["division"]; $office = $row["office"]; $rcc = $row["rcc"]; $requested_by = $row["requested_by"]; $requested_by_designation = $row["requested_by_designation"]; $issued_by = $row["issued_by"]; $issued_by_designation = $row["issued_by_designation"]; $approved_by = $row["approved_by"]; $approved_by_designation = $row["approved_by_designation"]; $date = $row["date"]; $view_ris = $row["view_ris"]; $purpose = $row["purpose"];

	mysqli_query($conn, "INSERT INTO tbl_ris(ris_no,entity_name,fund_cluster,division,office,rcc,item,unit,description,category,quantity,unit_cost,total,available,quantity_stocks,remarks,reference_no,purpose,requested_by,requested_by_designation,issued_by,issued_by_designation,approved_by,approved_by_designation,tbl_ris.date,supplier,lot_no,exp_date,po_id) VALUES ('$num_iss','$entity_name','$fund_cluster','$division','$office','$rcc','$item_name','$unit','$description','$category','$quantity','$unit_value','0.000','1','$stock','','$reference_no','$purpose','$requested_by','$requested_by_designation','$issued_by','$issued_by_designation','$approved_by','$approved_by_designation','$date','$supplier','$serial_no','$exp_date','$item_id')");
	$query_get_stocks = mysqli_query($conn, "SELECT quantity FROM tbl_po WHERE po_id = '$item_id' AND item_name LIKE '$item_name'");
	$rstocks = explode(" ", mysqli_fetch_assoc($query_get_stocks)["quantity"]);
	$newrstocks = ((int)$rstocks[0] - (int)$quantity)." ".$rstocks[1];
	mysqli_query($conn, "UPDATE tbl_po SET quantity = '$newrstocks' WHERE po_id = '$item_id' AND item_name LIKE '$item_name'");
	reload_item($num_iss);
}


function reload_item($num_iss){
	global $conn;
	$table = "";
	$sql = mysqli_query($conn, "SELECT ris_id, po_id, reference_no, item, description, category, quantity, unit, unit_cost, total, quantity_stocks, remarks FROM tbl_ris WHERE ris_no LIKE '$num_iss'");
	while($row = mysqli_fetch_assoc($sql)){
		$table.="<tr>
					<td>".$row["reference_no"]."</td>
					<td>".$row["item"]."</td>
					<td>".$row["description"]."</td>
					<td>".$row["category"]."</td>
					<td onclick=\"edit_quantity('".$row["ris_id"]."','".$row["quantity"]."','".$row["reference_no"]."','".mysqli_real_escape_string($conn, $row["item"])."','".mysqli_real_escape_string($conn, $row["description"])."', 'tbl_ris', 'ris_id', '".$row["po_id"]."');\"><a><u>".$row["quantity"]."</u></a></td>
					<td>".$row["unit"]."</td>
					<td>".number_format((float)$row["unit_cost"],3)."</td>
					<td>".number_format((float)$row["unit_cost"] * $row["quantity"],3)."</td>
					<td>".$row["quantity_stocks"]."</td>
					<td>".$row["remarks"]."</td>
					<td><center><button class=\"btn btn-xs btn-danger\" onclick=\"delete_existing('".$row["po_id"]."','".$row["ris_id"]."','".$num_iss."','".$row["quantity"]."')\"><i class=\"fa fa-trash\"></i></button></center></td>
				</tr>";
	}

	echo $table;
}

function get_items_all(){
	global $conn;

	$j = 1;
	$tbody = "";
	$po_numbers = $_POST["po_numbers"];
	for($i = 0; $i < count((is_countable($po_numbers)?$po_numbers:[])); $i++){
		$sql = mysqli_query($conn, "SELECT po_id, category, item_name, description, unit_cost, quantity, sn_ln, exp_date FROM tbl_po WHERE po_number LIKE '".$po_numbers[$i]."'");
		while($row = mysqli_fetch_assoc($sql)){
			$quan_unit = explode(" ", $row["quantity"]);
			if($quan_unit[0] != 0){
				$tbody.="<tr>
						<td hidden style=\"border: thin solid black;\">".$row["po_id"]."</td>
		                <td style=\"border: thin solid black;\">".$po_numbers[$i]."</td>
		                <td style=\"border: thin solid black;\">".$row["item_name"]."</td>
		                <td style=\"border: thin solid black;\">".$row["description"]."</td>
		                <td hidden style=\"border: thin solid black;\">".$row["category"]."</td>
		                <td style=\"border: thin solid black;\">".$row["sn_ln"]."</td>
		                <td style=\"border: thin solid black;\">".$row["exp_date"]."</td>
		                <td style=\"border: thin solid black;\">".$quan_unit[0]."</td>
		                <td style=\"border: thin solid black;\"><input type=\"number\" onkeyup=\"validate_input_quantity(this, '".$quan_unit[0]."', '".$j."', '".$row["unit_cost"]."')\"></td>
		                <td hidden style=\"border: thin solid black;\">".$quan_unit[1]."</td>
		                <td style=\"border: thin solid black;\">".$row["unit_cost"]."</td>
		                <td style=\"border: thin solid black;\"><span id=\"totid".$j."\"></span></td>
		                <td style=\"border: thin solid black;\"></td>
	                </tr>";
	                $j++;
			}
		}
	}
	echo $tbody;
}

function iss_validator(){
	global $conn;

	$ris_no = mysqli_real_escape_string($conn, $_POST["ris_no"]);
	$sql = mysqli_query($conn, "SELECT view_ris FROM tbl_ris WHERE ris_no = '$ris_no'");
	$view_ris = mysqli_fetch_assoc($sql)["view_ris"];
	$parts = explode(".", $view_ris);
	if(end($parts) == "pdf" || end($parts) == "PDF"){
		echo "1";
	}else{
		echo "0";
	}
}

function update(){
	global $conn;

	$ris_no = mysqli_real_escape_string($conn, $_POST["ris_no"]);
	$entity_name = mysqli_real_escape_string($conn, $_POST["entity_name"]);
	$division = mysqli_real_escape_string($conn, $_POST["division"]);
	$office = mysqli_real_escape_string($conn, $_POST["office"]);
	$date = mysqli_real_escape_string($conn, $_POST["date"]);
	$fund_cluster = mysqli_real_escape_string($conn, $_POST["fund_cluster"]);
	$rcc = mysqli_real_escape_string($conn, $_POST["rcc"]);
	$requested_by = mysqli_real_escape_string($conn, $_POST["requested_by"]);
	$requested_by_designation = mysqli_real_escape_string($conn, $_POST["requested_by_designation"]);
	$issued_by = mysqli_real_escape_string($conn, $_POST["issued_by"]);
	$issued_by_designation = mysqli_real_escape_string($conn, $_POST["issued_by_designation"]);
	$approved_by = mysqli_real_escape_string($conn, $_POST["approved_by"]);
	$approved_by_designation = mysqli_real_escape_string($conn, $_POST["approved_by_designation"]);
	$purpose = mysqli_real_escape_string($conn, $_POST["purpose"]);

	mysqli_query($conn, "UPDATE tbl_ris SET entity_name='$entity_name',division='$division',office='$office',tbl_ris.date='$date',fund_cluster='$fund_cluster',rcc='$rcc',requested_by='$requested_by',requested_by_designation='$requested_by_designation',issued_by='$issued_by',issued_by_designation='$issued_by_designation',approved_by='$approved_by',approved_by_designation='$approved_by_designation',purpose='$purpose' WHERE ris_no LIKE '$ris_no'");
	
	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." edited the details of RIS No. ".$ris_no;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
}

function modify(){
	global $conn;

	$ris_no = mysqli_real_escape_string($conn, $_POST["ris_no"]);
	$entity_name = "";$division = "";$office = "";$date = "";$fund_cluster = "";$rcc = "";$requested_by = "";$requested_by_designation="";$issued_by = "";$issued_by_designation="";$approved_by="";$approved_by_designation="";$purpose = "";$table = "";
	$sql = mysqli_query($conn, "SELECT ris_id, po_id, entity_name, division, office, SUBSTRING(tbl_ris.date,1,10) AS date_r, fund_cluster, rcc, requested_by, requested_by_designation, issued_by, issued_by_designation, approved_by, approved_by_designation, purpose, reference_no, item, description, category, quantity, unit, unit_cost, total, quantity_stocks, remarks FROM tbl_ris WHERE ris_no LIKE '$ris_no'");
	while($row = mysqli_fetch_assoc($sql)){
		$entity_name = $row["entity_name"];$division = $row["division"];$office = $row["office"];$date = $row["date_r"];$fund_cluster = $row["fund_cluster"];
		$rcc = $row["rcc"];$requested_by = $row["requested_by"];$requested_by_designation = $row["requested_by_designation"];$issued_by = $row["issued_by"];$issued_by_designation=$row["issued_by_designation"];$approved_by = $row["approved_by"];$approved_by_designation=$row["approved_by_designation"];$purpose = $row["purpose"];
		$table.="<tr>
					<td>".$row["reference_no"]."</td>
					<td>".$row["item"]."</td>
					<td>".$row["description"]."</td>
					<td>".$row["category"]."</td>
					<td onclick=\"edit_quantity('".$row["ris_id"]."','".$row["quantity"]."','".$row["reference_no"]."','".mysqli_real_escape_string($conn, $row["item"])."','".mysqli_real_escape_string($conn, $row["description"])."', 'tbl_ris', 'ris_id', '".$row["po_id"]."');\"><a><u>".$row["quantity"]."</u></a></td>
					<td>".$row["unit"]."</td>
					<td>".number_format((float)$row["unit_cost"],3)."</td>
					<td>".number_format((float)$row["unit_cost"] * $row["quantity"],3)."</td>
					<td>".$row["quantity_stocks"]."</td>
					<td>".$row["remarks"]."</td>
					<td><center><button class=\"btn btn-xs btn-danger\" onclick=\"delete_existing('".$row["po_id"]."','".$row["ris_id"]."','".$ris_no."','".$row["quantity"]."')\"><i class=\"fa fa-trash\"></i></button></center></td>
				</tr>";
	}
	echo json_encode(array(
		"entity_name"=>$entity_name,
		"division"=>$division,
		"office"=>$office,
		"date"=>$date,
		"fund_cluster"=>$fund_cluster,
		"rcc"=>$rcc,
		"requested_by"=>$requested_by,
		"requested_by_designation"=>$requested_by_designation,
		"issued_by"=>$issued_by,
		"issued_by_designation"=>$issued_by_designation,
		"approved_by"=>$approved_by,
		"approved_by_designation"=>$approved_by_designation,
		"purpose"=>$purpose,
		"table"=>$table
	));
}

function to_issue(){
	global $conn;
	$ris_no = mysqli_real_escape_string($conn, $_POST["ris_no"]);
	mysqli_query($conn, "UPDATE tbl_ris SET issued = '1' WHERE ris_no = '$ris_no'");
}

function delete_control(){
	global $conn;

	$field = mysqli_real_escape_string($conn, $_POST["field"]);
	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$number=mysqli_real_escape_string($conn, $_POST["number"]);
	$sql = mysqli_query($conn, "SELECT po_id, item, description, quantity, reference_no FROM ".$table." WHERE ".$field." LIKE '".$number."'");
	while($row = mysqli_fetch_assoc($sql)){
		$item = mysqli_real_escape_string($conn, $row["item"]); $description = mysqli_real_escape_string($conn, $row["description"]); $reference_no = mysqli_real_escape_string($conn, $row["reference_no"]); $quantity = $row["quantity"]; $po_id = $row["po_id"];
		$query_get_stocks = mysqli_query($conn, "SELECT quantity FROM tbl_po WHERE po_id = '$po_id'");
		$rstocks = explode(" ", mysqli_fetch_assoc($query_get_stocks)["quantity"]);
		$newrstocks = ((int)$rstocks[0] + (int)$quantity)." ".$rstocks[1];
		mysqli_query($conn, "UPDATE tbl_po SET quantity = '$newrstocks' WHERE po_id = '$po_id'");
	}
	mysqli_query($conn, "DELETE FROM ".$table." WHERE ".$field." LIKE '".$number."'");
	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." deleted a record RIS No. ".$number;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
}

function print_ris_dm(){
	global $conn;

	$rows_limit = 20;
	$rows_allocate = 0;
	$ris_no = mysqli_real_escape_string($conn, $_POST["ris_no"]);$reference_no = "";
	$entity_name = "";$fund_cluster = "";$division = "";$office = "";$rcc = "";$supplier = "";$all_total = 0.00;
	$purpose = "";$requested_by = "";$requested_by_designation = "";$issued_by = "";$issued_by_designation="";$approved_by="";$approved_by_designation=""; $date = "";
	$tbody = "";
	$sql = mysqli_query($conn, "SELECT entity_name,reference_no,supplier,lot_no,exp_date,fund_cluster,division,office,rcc,unit,item,description,quantity,unit_cost,total,available,quantity_stocks,remarks,purpose,requested_by,requested_by_designation,issued_by,issued_by_designation,approved_by,approved_by_designation,SUBSTRING(tbl_ris.date,1,10) AS dr FROM tbl_ris WHERE ris_no LIKE '$ris_no'");
	while($row = mysqli_fetch_assoc($sql)){
		$entity_name = $row["entity_name"];$fund_cluster = $row["fund_cluster"];$division = $row["division"];$office = $row["office"];$rcc = $row["rcc"]; $reference_no = $row["reference_no"];$supplier = $row["supplier"];
		$purpose = $row["purpose"];$requested_by = $row["requested_by"];$requested_by_designation = $row["requested_by_designation"];$issued_by = $row["issued_by"];$issued_by_designation = $row["issued_by_designation"];$date = $row["dr"];$approved_by = $row["approved_by"];$approved_by_designation = $row["approved_by_designation"];
		$all_total += (float)($row["unit_cost"] * $row["quantity"]);
		if($row["available"] == 1){
			$tbody.="<tr>
          <td style=\"width: 50.4px; height: 18px; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
          <td style=\"width: 35.4px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["unit"]."</td>
          <td style=\"width: 153.6px; height: 18px; text-align: left; font-size: 12px; font-weight: bold; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"><b>".$row["description"]."</b></td>
          <td style=\"width: 54.6px; height: 18px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["quantity"]."</td>
          <td style=\"width: 37.2px; height: 18px; text-align: center; font-size: 12px; font-weight: bold; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">✓</td>
          <td style=\"width: 34.2px; height: 18px; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 37.8px; height: 18px; font-size: 12px; text-align: center; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["quantity_stocks"]."</td>
          <td style=\"width: 45.6px; height: 18px; text-align: center; font-size: 11px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".(($row["lot_no"] == "") ? "N/A" : (explode("|",$row["lot_no"]))[0])."</td>
          <td style=\"width: 52.2px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".($row["exp_date"] == "0000-00-00" ? "N/A" : $row["exp_date"])."</td>
          <td style=\"width: 78.6px; height: 18px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["supplier"]."</td>
          <td style=\"width: 50.4px; height: 18px; text-align: center; font-size: 11px;vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$row["unit_cost"], 3)."</td>
          <td style=\"width: 109.8px; height: 18px; text-align: center; font-size: 10px;vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$row["quantity"] * $row["unit_cost"], 3)."</td>
        </tr>";
	      $rows_allocate+=round((float)strlen($row["description"]) / 10.00);
		}
	}

	$the_rest = array("***Nothing Follows***","","","","PO #".$reference_no,$supplier);
	for($j = 0; $j < count($the_rest); $j++){
		$tbody.="<tr>
          <td style=\"width: 50.4px; height: 18px; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
          <td style=\"width: 35.4px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 153.6px; height: 18px; text-align: left; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$the_rest[$j]."</td>
          <td style=\"width: 54.6px; height: 18px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 37.2px; height: 18px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 34.2px; height: 18px; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 37.8px; height: 18px; font-size: 9px; text-align: center; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 45.6px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 52.2px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 78.6px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 50.4px; height: 18px; text-align: center; font-size: 10px;vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 109.8px; height: 18px; text-align: center; font-size: 10px;vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
        </tr>";
	      $rows_allocate+=round((float)strlen($supplier) / 45.00);
	}

	for($i = 0; $i < ($rows_limit - $rows_allocate); $i++){
		$tbody.="<tr>
          <td style=\"width: 50.4px; height: 18px; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
          <td style=\"width: 35.4px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 153.6px; height: 18px; text-align: left; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 54.6px; height: 18px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 37.2px; height: 18px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 34.2px; height: 18px; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 37.8px; height: 18px; font-size: 9px; text-align: center; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 45.6px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 52.2px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 78.6px; height: 18px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 50.4px; height: 18px; text-align: center; font-size: 10px;vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
          <td style=\"width: 109.8px; height: 18px; text-align: center; font-size: 10px;vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
        </tr>";
	}

	echo json_encode(array(
		"entity_name"=>$entity_name,
		"fund_cluster"=>$fund_cluster,
		"division"=>$division,
		"office"=>$office,
		"rcc"=>$rcc,
		"tbody"=>$tbody,
		"purpose"=>$purpose,
		"requested_by"=>$requested_by,
		"requested_by_designation"=>$requested_by_designation,
		"issued_by"=>$issued_by,
		"issued_by_designation"=>$issued_by_designation,
		"approved_by"=>$approved_by,
		"approved_by_designation"=>$approved_by_designation,
		"total_cost"=>number_format((float)$all_total, 3),
		"date"=>_m_d_yyyy_($date)));
}

function print_ris(){
	global $conn;

	$rows_limit = 40;
	$rows_allocate = 0;
	$ris_no = mysqli_real_escape_string($conn, $_POST["ris_no"]);$reference_no = "";
	$entity_name = "";$fund_cluster = "";$division = "";$office = "";$rcc = "";$supplier = "";$all_total = 0.00;
	$purpose = "";$requested_by = "";$requested_by_designation = "";$issued_by = "";$issued_by_designation="";$date = "";$approved_by = "";$approved_by_designation = "";
	$tbody = "";
	$sql = mysqli_query($conn, "SELECT entity_name,reference_no,supplier,fund_cluster,division,office,rcc,unit,item,description,quantity,unit_cost,total,available,quantity_stocks,remarks,purpose,requested_by,requested_by_designation,issued_by,issued_by_designation,approved_by,approved_by_designation,SUBSTRING(tbl_ris.date,1,10) AS dr FROM tbl_ris WHERE ris_no LIKE '$ris_no'");
	while($row = mysqli_fetch_assoc($sql)){
		$entity_name = $row["entity_name"];$fund_cluster = $row["fund_cluster"];$division = $row["division"];$office = $row["office"];$rcc = $row["rcc"]; $reference_no = $row["reference_no"];$supplier = $row["supplier"];
		$purpose = $row["purpose"];$requested_by = $row["requested_by"];$requested_by_designation = $row["requested_by_designation"];$issued_by = $row["issued_by"];$issued_by_designation = $row["issued_by_designation"];$approved_by=$row["approved_by"];$approved_by_designation=$row["approved_by_designation"];$date = $row["dr"];
		$all_total += (float)($row["unit_cost"] * $row["quantity"]);
		if($row["available"] == 1){
			$tbody.="<tr>
	        <td style=\"width: 64.8px; height: 15px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
	        <td style=\"width: 35.4px; height: 15px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["unit"]."</td>
	        <td style=\"width: 115.8px; height: 15px; text-align: left; font-size: 12px; vertical-align: center; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 1px; border-right-style: solid;\"><b>".$row["item"]."</b> - ".$row["description"]."</td>
	        <td style=\"width: 46.8px; height: 15px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["quantity"]."</td>
	        <td style=\"width: 41.4px; height: 15px; text-align: center; font-size: 12px; font-style: italic; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"><b>✓</b></td>
	        <td style=\"width: 40.2px; height: 15px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 61.2px; height: 15px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["quantity_stocks"]."</td>
	        <td style=\"width: 108.6px; height: 15px; font-size: 12px; text-align: center; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$row["unit_cost"], 3)."</td>
	        <td style=\"width: 108.6px; height: 15px; font-size: 12px; text-align: center; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$row["quantity"] * $row["unit_cost"], 3)."</td>
	      </tr>";
	      $rows_allocate+=2;
		}
	}
	$the_rest = array("***Nothing Follows***","","","","PO #".$reference_no,$supplier);
	for($j = 0; $j < count($the_rest); $j++){
		$tbody.="<tr>
	        <td style=\"width: 64.8px; height: 15px; text-align: center; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
	        <td style=\"width: 35.4px; height: 15px; text-align: center; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 115.8px; height: 15px; text-align: left; vertical-align: center; font-size: 12px; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 1px; border-right-style: solid;\">".$the_rest[$j]."</td>
	        <td style=\"width: 46.8px; height: 15px; text-align: center; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 41.4px; height: 15px; text-align: center; font-size: 11px; font-style: italic; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 40.2px; height: 15px; text-align: center; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 61.2px; height: 15px; text-align: center; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 108.6px; height: 15px; text-align: left; font-size: 11px; font-weight: bold; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 108.6px; height: 15px; text-align: left; font-size: 11px; font-weight: bold; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	      </tr>";
	      $rows_allocate++;
	}
	for($i = 0; $i < ($rows_limit - $rows_allocate); $i++){
		$tbody.="<tr>
	        <td style=\"width: 64.8px; height: 15px; text-align: center; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
	        <td style=\"width: 35.4px; height: 15px; text-align: center; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 115.8px; height: 15px; text-align: left; font-size: 11px; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 1px; border-right-style: solid;\"></td>
	        <td style=\"width: 46.8px; height: 13.5px; text-align: center; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 41.4px; height: 15px; text-align: center; font-size: 11px; font-style: italic; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 40.2px; height: 15px; text-align: center; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 61.2px; height: 15px; text-align: center; font-size: 11px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 108.6px; height: 15px; text-align: left; font-size: 11px; font-weight: bold; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 108.6px; height: 15px; text-align: left; font-size: 11px; font-weight: bold; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	      </tr>";
	}

	echo json_encode(array(
		"entity_name"=>$entity_name,
		"fund_cluster"=>$fund_cluster,
		"division"=>$division,
		"office"=>$office,
		"rcc"=>$rcc,
		"tbody"=>$tbody,
		"purpose"=>$purpose,
		"requested_by"=>$requested_by,
		"requested_by_designation"=>$requested_by_designation,
		"issued_by"=>$issued_by,
		"issued_by_designation"=>$issued_by_designation,
		"approved_by"=>$approved_by,
		"approved_by_designation"=>$approved_by_designation,
		"total_cost"=>number_format((float)$all_total, 3),
		"date"=>_m_d_yyyy_($date)));
}

function get_division_office(){
	global $connhr;
	$division = mysqli_real_escape_string($connhr, $_POST["division"]);
	$sql = mysqli_query($connhr, "SELECT unit FROM ref_division WHERE division LIKE '$division'");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option>".$row["unit"]."</option>";
		}
	}
}

function get_division(){
	global $connhr;
	$sql = mysqli_query($connhr, "SELECT DISTINCT division FROM ref_division ORDER BY division_id ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option>".$row["division"]."</option>";
		}
	}
}

function get_item_details(){
	global $conn;

	$item_name = mysqli_real_escape_string($conn, $_POST["item_name"]);
	$po_id = mysqli_real_escape_string($conn, $_POST["po_id"]);
	$sql = mysqli_query($conn, "SELECT category, description, unit_cost, quantity, sn_ln, exp_date FROM tbl_po WHERE item_name LIKE '$item_name' AND po_id LIKE '$po_id'");
	$row = mysqli_fetch_assoc($sql);
	$quan_unit = explode(" ", $row["quantity"]);
	echo json_encode(array("description"=>$row["description"], "unit_cost"=>$row["unit_cost"], "quantity"=>$quan_unit[0], "unit"=>$quan_unit[1], "category"=>$row["category"], "lot_no"=>$row["sn_ln"], "exp_date"=>$row["exp_date"]));
}

function get_item(){
	global $conn;

	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$sql = mysqli_query($conn, "SELECT po_id, po_number, item_name, quantity FROM tbl_po WHERE inspection_status = '1' AND po_number LIKE '$po_number' ORDER BY po_id DESC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			if((int)explode(" ", $row["quantity"])[0] != 0){
				echo "<option data-po=\"".$row["po_number"]."\" value=\"".$row["po_id"]."\">".$row["item_name"]."</option>";
			}
		}
	}
}

function get_po(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT DISTINCT po_number FROM tbl_po WHERE inspection_status = '1' ORDER BY po_id DESC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option id=".$row["po_number"].">".$row["po_number"]."</option>";
		}
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

	$query = "SELECT DISTINCT ris_no,division,office,SUBSTRING(tbl_ris.date,1,10) AS d,requested_by,issued_by,purpose, issued FROM tbl_ris ";
	if($_POST["search"] != ""){
		$qs = mysqli_real_escape_string($conn, $_POST["search"]);
		$query.="WHERE ris_no LIKE '%$qs%' OR reference_no LIKE '%$qs%' OR division LIKE '%$qs%' OR office LIKE '%$qs%' OR requested_by LIKE '%$qs%' OR purpose LIKE '%$qs%' OR item LIKE '%$qs%' ";
	}
	$query.="ORDER BY ris_id DESC ";

	$sql_orig = mysqli_query($conn, $query);
	$sql = mysqli_query($conn, $query."LIMIT ".$start.", ".$limit."");
	$tbody = "";
	$total_data = mysqli_num_rows($sql_orig);
	if($total_data != 0){
		while($row = mysqli_fetch_assoc($sql)){
			$rb = str_replace(' ', '', $row["requested_by"]);
			$ris_no = $row["ris_no"];
			$category = mysqli_fetch_assoc(mysqli_query($conn, "SELECT category FROM tbl_ris WHERE ris_no = '$ris_no'"))["category"];
			$call_print = ($category != "Drugs and Medicines" && $category != "Medical Supplies") ? "print_ris(this.value);" : "print_ris_dm(this.value);";
			$call_excel = ($category != "Drugs and Medicines" && $category != "Medical Supplies") ? "download_xls(this.value);" : "download_xls_dm(this.value);";

			$in = array();
			$get_items = mysqli_query($conn, "SELECT item FROM tbl_ris WHERE ris_no LIKE '$ris_no'");
			while($ri = mysqli_fetch_assoc($get_items)){
				array_push($in, $ri["item"]);
			}
			$refs = array();
			$get_rf = mysqli_query($conn, "SELECT DISTINCT reference_no FROM tbl_ris WHERE ris_no LIKE '$ris_no'");
			while($rf = mysqli_fetch_assoc($get_rf)){
				array_push($refs, $rf["reference_no"]);
			}
			$tbody.="<tr>
					<td><center>".(($row["issued"] == '0') ? "<button id=\"\" value=\"".$row["ris_no"]."\" ".(($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU") ? "onclick=\"to_issue(this.value, this.id);\"" : "")." class=\"btn btn-xs btn-danger\" style=\"border-radius: 10px;\">✖</button>" : "<button class=\"btn btn-xs\" style=\"border-radius: 10px; background-color: #00FF00; color: white; font-weight: bold;\" value=\"".$row["ris_no"]."\" onclick=\"modify_dr(this.value, 'RIS No. '+this.value, 'tbl_ris', 'ris_no');\">✓</button>")."</center></td>
					<td>".$row["ris_no"]."</td>
					<td>".$row["division"]."</td>
					<td>".$row["office"]."</td>
					<td style=\"font-size: 10px;\">".implode(", ", $refs)."</td>
					<td style=\"font-size: 10px;\">".implode(", ", $in)."</td>
					<td>".$row["d"]."</td>
					<td>".utf8_encode($row["requested_by"])."</td>
					<td>".utf8_encode($row["issued_by"])."</td>
					<td>".$row["purpose"]."</td>
					<td><center><button class=\"btn btn-xs btn-primary dim\" value=\"".$row["ris_no"]."\" onclick=\"view_iss(this.value,'tbl_ris','view_ris','RIS','ris_no','".$rb."');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Preview\"><i class=\"fa fa-picture-o\"></i></button>&nbsp;".(($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU") ? "<button class=\"btn btn-xs btn-info dim\" value=\"".$row["ris_no"]."\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit\" onclick=\"modify(this.value);\"><i class=\"fa fa-pencil-square-o\"></i></button>&nbsp;" : "")."<button class=\"btn btn-xs btn-success dim\" value=\"".$row["ris_no"]."\" onclick=\"".$call_print."\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Print\"><i class=\"fa fa-print\"></i></button>&nbsp;".(($_SESSION["role"] == "SUPPLY_SU") ? "<button class=\"btn btn-xs btn-danger dim\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete\" value=\"".$row["ris_no"]."\" onclick=\"delete_control(this.value);\"><i class=\"fa fa-trash\"></i></button>&nbsp;" : "")."<button class=\"btn btn-xs btn-warning dim\" value=\"".$row["ris_no"]."\" onclick=\"".$call_excel."\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Save as Excel\"><i class=\"fa fa-file-excel-o\"></i></button></center></td>
				</tr>";
		}
	}else{
		$tbody="<tr><td colspan=\"11\" style=\"text-align: center;\">No data found.</td></tr>";
	}
	$in_out = create_table_pagination($page, $limit, $total_data, array("","RIS No.","Division","Office","PO No.","Items","Date Released","Requested By", "Issued By", "Purpose", ""));
	$whole_dom = $in_out[0]."".$tbody."".$in_out[1];
	echo $whole_dom;
}

function get_latest_ris(){
	global $conn;

	$yy_mm = substr(mysqli_real_escape_string($conn, $_POST["yy_mm"]), 0, 4);
	$sql = mysqli_query($conn, "SELECT DISTINCT ris_no FROM tbl_ris WHERE ris_no LIKE '%$yy_mm%' ORDER BY ris_id DESC LIMIT 1");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		echo str_pad(((int)explode("-", $row["ris_no"])[2]) + 1, 4, '0', STR_PAD_LEFT);
	}else{
		echo "0001";
	}
}

function insert_ris2(){
	global $conn;
	global $connhr;
	date_default_timezone_set("Asia/Shanghai");
	$time_now = date("H:i:s");
	$ris_no = mysqli_real_escape_string($conn, $_POST["ris_no"]);
	$entity_name = mysqli_real_escape_string($conn, $_POST["entity_name"]);
	$fund_cluster = mysqli_real_escape_string($conn, $_POST["fund_cluster"]);
	$division = mysqli_real_escape_string($conn, $_POST["division"]);
	$office = mysqli_real_escape_string($conn, $_POST["office"]);
	$date = mysqli_real_escape_string($conn, $_POST["date"])." ".$time_now;
	$rcc = mysqli_real_escape_string($conn, $_POST["rcc"]);
	$requested_by_id = mysqli_real_escape_string($conn, $_POST["requested_by_id"]);
	$requested_by = mysqli_real_escape_string($conn, $_POST["requested_by"]);
	$issued_by_id = mysqli_real_escape_string($conn, $_POST["issued_by_id"]);
	$issued_by = mysqli_real_escape_string($conn, $_POST["issued_by"]);
	$approved_by_id = mysqli_real_escape_string($conn, $_POST["approved_by_id"]);
	$approved_by = mysqli_real_escape_string($conn, $_POST["approved_by"]);
	$purpose = mysqli_real_escape_string($conn, $_POST["purpose"]);
	$items = $_POST["items"];
	$reference_no = $items[0][1];

	$query = mysqli_query($conn, "SELECT s.supplier, p.supplier_id FROM tbl_po AS p, ref_supplier AS s WHERE s.supplier_id = p.supplier_id AND p.po_number LIKE '$reference_no'");
	$quer1 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$requested_by_id'");
	$quer2 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$issued_by_id'");
	$quer3 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$approved_by_id'");

	$supplier = mysqli_real_escape_string($conn, mysqli_fetch_assoc($query)["supplier"]);
	$requested_by_designation = mysqli_real_escape_string($conn, mysqli_fetch_assoc($quer1)["designation"]);
	$issued_by_designation = mysqli_real_escape_string($conn, mysqli_fetch_assoc($quer2)["designation"]);
	$approved_by_designation = mysqli_real_escape_string($conn, mysqli_fetch_assoc($quer3)["designation"]);
	if(mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT ris_no FROM tbl_ris WHERE ris_no = '$ris_no'"))==0){
		$emp_id = $_SESSION["emp_id"];
		$description = $_SESSION["username"]." created an RIS No. ".$ris_no;
		mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
		echo "0";
		for($i = 0; $i < count($items); $i++){
			$po_id = $items[$i][0];
			$ref_no = $items[$i][1];
			$item = mysqli_real_escape_string($conn, $items[$i][2]);
			$description = mysqli_real_escape_string($conn, $items[$i][3]);
			$category = $items[$i][4];
			$lot_no = $items[$i][5];
			$exp_date = $items[$i][6];
			$stock = $items[$i][7];
			$quantity = $items[$i][8];
			$unit = $items[$i][9];
			$cost = $items[$i][10];
			$total = $items[$i][11];
			$remarks = $items[$i][12];
			mysqli_query($conn, "INSERT INTO tbl_ris(ris_no,entity_name,fund_cluster,division,office,rcc,item,unit,description,category,quantity,unit_cost,total,available,quantity_stocks,remarks,reference_no,purpose,requested_by,requested_by_designation,issued_by,issued_by_designation,approved_by,approved_by_designation,tbl_ris.date,supplier,lot_no,exp_date,po_id) VALUES ('$ris_no','$entity_name','$fund_cluster','$division','$office','$rcc','$item','$unit','$description','$category','$quantity','$cost','$total','1','$stock','$remarks','$ref_no','$purpose','$requested_by','$requested_by_designation','$issued_by','$issued_by_designation','$approved_by','$approved_by_designation','$date','$supplier','$lot_no','$exp_date','$po_id')");
			$query_get_stocks = mysqli_query($conn, "SELECT quantity FROM tbl_po WHERE po_id = '$po_id' AND item_name LIKE '$item'");
			$rstocks = explode(" ", mysqli_fetch_assoc($query_get_stocks)["quantity"]);
			$newrstocks = ((int)$rstocks[0] - (int)$quantity)." ".$rstocks[1];
			mysqli_query($conn, "UPDATE tbl_po SET quantity = '$newrstocks' WHERE po_id = '$po_id' AND item_name LIKE '$item'");
		}
	}else{
		echo "1";
	}
}

$call_func = mysqli_real_escape_string($conn, $_POST["call_func"]);
switch($call_func){
	case "get_records":
		get_records();
		break;
	case "get_po":
		get_po();
		break;
	case "get_item":
		get_item();
		break;
	case "get_item_details":
		get_item_details();
		break;
	case "get_division":
		get_division();
		break;
	case "get_division_office":
		get_division_office();
		break;
	case "print_ris":
		print_ris();
		break;
	case "print_ris_dm":
		print_ris_dm();
		break;
	case "get_latest_ris":
		get_latest_ris();
		break;
	case "delete":
		delete_control();
		break;
	case "to_issue":
		to_issue();
		break;
	case "modify":
		modify();
		break;
	case "update":
		update();
		break;
	case "iss_validator":
		iss_validator();
		break;
	case "get_items_all":
		get_items_all();
		break;
	case "insert_ris2":
		insert_ris2();
		break;
	case "new_add_item":
		new_add_item();
		break;
	case "delete_existing":
		delete_existing();
		break;
	case "create_trans":
		create_trans();
		break;
}

?>