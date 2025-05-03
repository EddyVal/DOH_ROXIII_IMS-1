<?php

require "php_conn.php";
require "php_general_functions.php";

session_start();

function get_notc_details(){
	global $conn;

	$items = ""; $date_conformed = ""; $delivery_term = "";

	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$sql = mysqli_query($conn, "SELECT item_name, date_conformed, delivery_term FROM tbl_po WHERE po_number LIKE '$po_number'");
	$row = mysqli_fetch_assoc($sql);
	$items = (mysqli_num_rows($sql) > 1) ? $row["item_name"]." & etc." : $row["item_name"];
	$date_conformed = _m_d_yyyy_($row["date_conformed"]);
	$delivery_term = $row["delivery_term"];

	echo json_encode(array("items"=>$items, "date_conformed"=>$date_conformed, "delivery_term"=>$delivery_term));
	
}

function get_billing_history(){
	global $conn;
	$index = 0;
	$tbody = "";
	$arr_order = array("1st","2nd","3rd","4th","5th","6th","7th","8th","9th","10th");
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$sql = mysqli_query($conn, "SELECT DISTINCT iar_number FROM tbl_iar WHERE po_number LIKE '$po_number'");
	if(mysqli_num_rows($sql) != 0){
		while($rows = mysqli_fetch_assoc($sql)){
			$iarn = $rows["iar_number"];
			$total_amount = 0.000;
			$sql2 = mysqli_query($conn, "SELECT main_stocks, unit_cost FROM tbl_po WHERE iar_no LIKE '$iarn'");
			while($row = mysqli_fetch_assoc($sql2)){
				$total_amount+=(float)$row["main_stocks"] * (float)$row["unit_cost"];
			}
			$tbody.="<tr>
						<td>".$arr_order[$index]."</td>
						<td>".$iarn."</td>
						<td>".number_format((float)$total_amount, 3)."</td>
						<td>".mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUBSTRING(dt, 1, 10) AS dtm FROM tbl_logs WHERE description LIKE '%created an IAR No. $iarn%'"))["dtm"]."</td>
					</tr>";
			$index+=1;
		}
	}

	echo $tbody;
}

function item_name_search(){
	global $conn;

	$filter = "";$tbody = "";
	$po_numbers = array();
	$item_name = mysqli_real_escape_string($conn, $_POST["item_name"]);
	$po_search = mysqli_real_escape_string($conn, $_POST["po_search"]);
	if($po_search != ""){
		$filter.=" AND po_number LIKE '$po_search'";
	}
	$po_option = "<option disabled selected></option>";
	$query = "SELECT po_number, item_name, description, main_stocks, unit_cost, quantity, end_user FROM tbl_po WHERE item_name LIKE '$item_name' AND status LIKE 'Delivered'";

	$sql = mysqli_query($conn, $query.$filter);
	while($row = mysqli_fetch_assoc($sql)){
		$quantity = (explode(" ", $row["quantity"]))[0];
		if(!in_array($row["po_number"], $po_numbers)){
			array_push($po_numbers, $row["po_number"]);
			$po_option.="<option>".$row["po_number"]."</option>";
		}
		$tbody.="<tr>
					<td>".$row["po_number"]."</td>
                    <td>".$row["item_name"]."</td>
                    <td>".$row["description"]."</td>
                    <td>₱ ".number_format((float)$row["unit_cost"], 3)."</td>
                    <td>".number_format((float)$row["main_stocks"], 0)."</td>
                    <td>₱ ".number_format((float)$row["main_stocks"] * (float)$row["unit_cost"], 2)."</td>
                    <td>".number_format((float)$quantity, 0)."</td>
                    <td>₱ ".number_format((float)$quantity * (float)$row["unit_cost"], 2)."</td>
                    <td>".$row["end_user"]."</td>
                </tr>";
	}

	echo json_encode(array("tbody"=>$tbody, "po_option"=>$po_option));
}

function generate_dl(){
	global $conn;

	$supplier = mysqli_real_escape_string($conn, $_POST["supplier"]);
	$po_list = mysqli_real_escape_string($conn, $_POST["po_list"]);
	$separator = mysqli_real_escape_string($conn, $_POST["separator"]);

	$list_po = explode($separator, $po_list);

	$tbody = "";

	foreach($list_po AS $po){
		$sql = mysqli_query($conn, "SELECT item_name, description, main_stocks, unit_cost, quantity FROM tbl_po WHERE po_number LIKE '$po'");
		$flag = true;
		$suma_tot = 0.00;
		while($row = mysqli_fetch_assoc($sql)){
			$unit = (explode(" ", $row["quantity"]))[1];
			$tbody.="<tr>
            			<td style=\"padding: 5px; border-left-style: solid; border-left-width: 1px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;\">".(($flag) ? $po : "")."</td>
            			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;\">".$unit."</td>
            			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top;\"><span style=\"text-align: left;\"><b>".$row["item_name"]."</b> - ".$row["description"]."</span></td>
            			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;\">".$row["main_stocks"]."</td>
            			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;\">".number_format($row["unit_cost"], 2)."</td>
            			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;\">".number_format(((float)$row["unit_cost"] * (float)$row["main_stocks"]), 3)."</td>
            		</tr>";
            		$flag = false;
            		$suma_tot+=((float)$row["unit_cost"] * (float)$row["main_stocks"]);
		}
		$tbody.="<tr>
        			<td style=\"padding: 5px; border-left-style: solid; border-left-width: 1px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;\">-</td>
        			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;\"></td>
        			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top;\"></td>
        			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;\"></td>
        			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;\"></td>
        			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;\">-</td>
        		</tr>
        		<tr>
        			<td style=\"padding: 5px; border-left-style: solid; border-left-width: 1px; border-right-style: solid; border-right-width: 1px; border-bottom-style: solid; border-bottom-width: 1px; vertical-align: top; text-align: center;\"></td>
        			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;border-bottom-style: solid; border-bottom-width: 1px;\"></td>
        			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: right; border-bottom-style: solid; border-bottom-width: 1px; font-weight:bold;\">TOTAL</td>
        			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;border-bottom-style: solid; border-bottom-width: 1px;\"></td>
        			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: right;border-bottom-style: solid; border-bottom-width: 1px; font-weight:bold;\">Php</td>
        			<td style=\"padding: 5px; border-right-style: solid; border-right-width: 1px; vertical-align: top; text-align: center;border-bottom-style: solid; border-bottom-width: 1px; font-weight: bold;\">".number_format((float)$suma_tot, 3)."</td>
        		</tr>";
	}

	echo json_encode(array("tbody"=>$tbody,"date_today"=>_m_d_yyyy_(date("Y-m-d"))));
}

function consolidate_po(){
	global $conn;

	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$fileArray= array();
	
	$sql = mysqli_query($conn, "SELECT DISTINCT po_number, view_po, end_user FROM tbl_po WHERE po_number LIKE '$po_number'");
	if(mysqli_num_rows($sql)!=0){
		$row = mysqli_fetch_assoc($sql);
		array_push($fileArray, "../../archives/po/".substr($po_number,0,4)."/".str_replace(' ', '', $row["end_user"])."/".$row["view_po"]);	
	}
	$sql = mysqli_query($conn, "SELECT view_iar FROM tbl_iar WHERE po_number LIKE '$po_number'");
	if(mysqli_num_rows($sql)){
		$row = mysqli_fetch_assoc($sql);
		array_push($fileArray, "../../archives/IAR/".substr($po_number,0,4)."/".$row["view_iar"]);
	}	
	$sql = mysqli_query($conn, "SELECT DISTINCT ics_no, received_by, view_ics FROM tbl_ics WHERE reference_no LIKE '$po_number'");
	if(mysqli_num_rows($sql)!=0){
		while($row = mysqli_fetch_assoc($sql)) {
			array_push($fileArray, "../../archives/ICS/".substr($row["ics_no"],0,4)."/".str_replace(' ', '', $row["received_by"])."/".$row["view_ics"]);
		}
	}
	$sql = mysqli_query($conn, "SELECT DISTINCT par_no, received_by, view_par FROM tbl_par WHERE reference_no LIKE '$po_number'");
	if(mysqli_num_rows($sql)!=0){
		while($row = mysqli_fetch_assoc($sql)){
			array_push($fileArray, "../../archives/PAR/".substr($row["par_no"],0,4)."/".str_replace(' ', '', $row["received_by"])."/".$row["view_par"]);
		}
	}
	$sql = mysqli_query($conn, "SELECT DISTINCT ris_no, requested_by, view_ris FROM tbl_ris WHERE reference_no LIKE '$po_number'");
	if(mysqli_num_rows($sql)!=0){
		while($row = mysqli_fetch_assoc($sql)){
			array_push($fileArray, "../../archives/RIS/".substr($row["ris_no"],0,4)."/".str_replace(' ', '', $row["requested_by"])."/".$row["view_ris"]);
		}
	}
	$sql = mysqli_query($conn, "SELECT DISTINCT ptr_no, tbl_ptr.to, view_ptr FROM tbl_ptr WHERE reference_no LIKE '$po_number'");
	if(mysqli_num_rows($sql)!=0){
		while($row = mysqli_fetch_assoc($sql)){
			array_push($fileArray, "../../archives/PTR/".substr($row["ptr_no"],0,4)."/".str_replace(' ', '', $row["to"])."/".$row["view_ptr"]);
		}
	}/**/
	
	$filepath = '../../archives/consolidated_po/'.substr($po_number,0,4).'/';
	if(!is_dir($filepath)){
		mkdir($filepath, 0777, true);
	}
	$filepath .= 'Consolidated-PONo.'.$po_number.'.pdf';
	$cmd = '"C:\Program Files\gs\gs9.53.3\bin\gswin64c.exe" -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE='.$filepath.' -dBATCH ';
	foreach($fileArray as $file) {
	    $cmd .= $file.' ';
	}
	shell_exec($cmd);
	
	echo 'Consolidated-PONo.'.$po_number.'.pdf';
}

function get_po_items($po_number){

	global $conn;

	$sql = mysqli_query($conn, "SELECT po_id, item_name, description, main_stocks, unit_cost, quantity, category, sn_ln FROM tbl_po WHERE po_number LIKE '$po_number'");
	$tbody = "";
	$tot_amt = 0.00;
	while($row = mysqli_fetch_assoc($sql)){
		$tbody.="<tr>
				<td><button class=\"btn btn-xs btn-default\" onclick=\"swal('".$row["po_id"]."');\">GetID</button></td>
				<td>".$row["item_name"]."</td>
				<td ".(($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU") ? "onclick=\"edit_description('".$po_number."', '".mysqli_real_escape_string($conn, $row["item_name"])."', '".mysqli_real_escape_string($conn, $row["description"])."', '".$row["unit_cost"]."')\"" : "")."><a><u>".$row["description"]."</u></a></td>
				<td>".number_format((float)$row["unit_cost"], 3)."</td>
				<td>".$row["main_stocks"]."</td>
				<td ".((($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU") && $row["main_stocks"] == explode(" ", $row["quantity"])[0]) ? "onclick=\"add_quantity('".$row["po_id"]."', '".$po_number."')\"" : "")."><a><u>".$row["quantity"]."</u></a></td>
				<td>".number_format(((float)$row["unit_cost"]) * (float)(explode(" ", $row["quantity"])[0]), 3)."</td>
				<td><center>".(($row["sn_ln"] == null) ? "<button value=\"".$row["po_id"]."\" id=\"".(int)(explode(" ", $row["quantity"])[0])."\" onclick=\"add_sl(this.value, this.id, '".$row["category"]."');\" class=\"btn btn-info btn-xs\"><i class=\"fa fa-plus\"></i> Add SN/LN</button>" : "<button class=\"btn btn-xs\" style=\"border-radius: 10px; background-color: #00FF00; color: white; font-weight: bold;\" disabled><i class=\"fa fa-check\"></i></button>")."</center></td>
			</tr>";
			$tot_amt+=((float)$row["unit_cost"]) * (float)(explode(" ", $row["quantity"])[0]);
	}
	return json_encode(array("tbody"=>$tbody, "tot_amt"=>$tot_amt));
}

function add_item(){
	global $conn;

	$edate_received = mysqli_real_escape_string($conn, $_POST["edate_received"]);
	$epo_number = mysqli_real_escape_string($conn, $_POST["epo_number"]);
	$epr_no = mysqli_real_escape_string($conn, $_POST["epr_no"]);
	$eprocurement_mode = mysqli_real_escape_string($conn, $_POST["eprocurement_mode"]);
	$edelivery_term = mysqli_real_escape_string($conn, $_POST["edelivery_term"]);
	$epayment_term = mysqli_real_escape_string($conn, $_POST["epayment_term"]);
	$esupplier = mysqli_real_escape_string($conn, $_POST["esupplier"]);
	$epo_enduser = mysqli_real_escape_string($conn, $_POST["epo_enduser"]);
	$edate_conformed = mysqli_real_escape_string($conn, $_POST["edate_conformed"]);
	$edate_delivered = mysqli_real_escape_string($conn, $_POST["edate_delivered"]);
	$estatus = mysqli_real_escape_string($conn, $_POST["estatus"]);
	//$einspection_status = mysqli_real_escape_string($conn, $_POST["einspection_status"]);
	$e_item_id = mysqli_real_escape_string($conn, $_POST["e_item_id"]);
	$e_item_name = mysqli_real_escape_string($conn, $_POST["e_item_name"]);
	$e_description = mysqli_real_escape_string($conn, $_POST["e_description"]);
	$e_category = mysqli_real_escape_string($conn, $_POST["e_category"]);
	$e_unit_cost = mysqli_real_escape_string($conn, $_POST["e_unit_cost"]);
	$e_exp_date = mysqli_real_escape_string($conn, $_POST["e_exp_date"]);
	$e_quantity = mysqli_real_escape_string($conn, $_POST["e_quantity"]);
	$e_unit = mysqli_real_escape_string($conn, $_POST["e_unit"]);

	//$e_iarno = mysqli_fetch_assoc(mysqli_query($conn, "SELECT DISTINCT iar_no FROM tbl_po WHERE po_number LIKE '$epo_number'"))["iar_no"];

	$qu = $e_quantity." ".$e_unit;
	$inspection_status = $esupplier == 0 ? 1 : 0;

	mysqli_query($conn, "INSERT INTO tbl_po(po_number,date_received,procurement_mode,delivery_term,payment_term,pr_no, supplier_id,item_id,item_name,description,category,exp_date,unit_cost,main_stocks,quantity,end_user,date_conformed,date_delivered,status, po_type, inspection_status) VALUES('$epo_number','$edate_received','$eprocurement_mode','$edelivery_term','$epayment_term','$epr_no','$esupplier','$e_item_id','$e_item_name','$e_description','$e_category','$e_exp_date','$e_unit_cost','$e_quantity','$qu','$epo_enduser','$edate_conformed','$edate_delivered','$estatus','$e_category','$inspection_status')");

	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." added an item (".$e_item_name.") to PO No.".$epo_number;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
	
	echo get_po_items($epo_number);
}

function edit_description(){
	global $conn;

	$po = mysqli_real_escape_string($conn, $_POST["po"]);
	$item = mysqli_real_escape_string($conn, $_POST["item"]);
	$desc = mysqli_real_escape_string($conn, $_POST["desc"]);
	$price = mysqli_real_escape_string($conn, $_POST["price"]);
	$newdesc = mysqli_real_escape_string($conn, $_POST["newdesc"]);

	if($newdesc != null){
		mysqli_query($conn, "UPDATE tbl_po SET description = '$newdesc' WHERE po_number = '$po' AND item_name = '$item' AND description = '$desc' AND unit_cost = '$price'");
		mysqli_query($conn, "UPDATE tbl_ics SET description = '$newdesc' WHERE reference_no = '$po' AND item = '$item' AND description = '$desc' AND cost = '$price'");
		mysqli_query($conn, "UPDATE tbl_par SET description = '$newdesc' WHERE reference_no = '$po' AND item = '$item' AND description = '$desc' AND cost = '$price'");
		mysqli_query($conn, "UPDATE tbl_ptr SET description = '$newdesc' WHERE reference_no = '$po' AND item = '$item' AND description = '$desc' AND cost = '$price'");
		mysqli_query($conn, "UPDATE tbl_ris SET description = '$newdesc' WHERE reference_no = '$po' AND item = '$item' AND description = '$desc' AND unit_cost = '$price'");

		$emp_id = $_SESSION["emp_id"];
		$description = $_SESSION["username"]." modified the description of ".$item." from ".$desc." to ".$newdesc." - PO#".$po;
		mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
	}

	echo get_po_items($po);
}

function update_quantity(){
	global $conn;

	$po_id = mysqli_real_escape_string($conn, $_POST["po_id"]);
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$quantity = mysqli_real_escape_string($conn, $_POST["quantity"]);
	$item_name = ""; $rstocks = "";
	if($quantity != null){
		$query_get_stocks = mysqli_query($conn, "SELECT item_name, quantity FROM tbl_po WHERE po_id = '$po_id'");
		$row = mysqli_fetch_assoc($query_get_stocks);
		$rstocks = explode(" ", $row["quantity"]);
		$item_name = $row["item_name"];
		$newrstocks = $quantity." ".$rstocks[1];
		mysqli_query($conn, "UPDATE tbl_po SET quantity = '$newrstocks', main_stocks = '$quantity' WHERE po_id = '$po_id'");
		
		$emp_id = $_SESSION["emp_id"];
		$description = $_SESSION["username"]." modified the quantity of ".$item_name." from ".$rstocks[0]." ".$rstocks[1]." to ".$quantity." ".$rstocks[1]." - PO#".$po_number;
		mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
	}
	echo get_po_items($po_number);
}

function add_serials(){
	global $conn;

	$po_id = mysqli_real_escape_string($conn, $_POST["po_id"]);
	$sn_ln = mysqli_real_escape_string($conn, $_POST["sn_ln"]);
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	mysqli_query($conn, "UPDATE tbl_po SET sn_ln = '$sn_ln' WHERE po_id = '$po_id'");
	$query_get_stocks = mysqli_query($conn, "SELECT item_name FROM tbl_po WHERE po_id = '$po_id'");
	$item_name = mysqli_fetch_assoc($query_get_stocks)["item_name"];
	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." added serial numbers to ".$item_name." - PO#".$po_number;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");

	$serials_lots = explode("|", $sn_ln);
	for($j = 0; $j < count($serials_lots) - 1; $j++){
		$sl = $serials_lots[$j];
		mysqli_query($conn, "INSERT INTO tbl_serial(inventory_id,serial_no,is_issued) VALUES('$po_id','$sl','N')");
	}

	echo get_po_items($po_number);
}

function delete_control(){
	global $conn;

	$field = mysqli_real_escape_string($conn, $_POST["field"]);
	$table = mysqli_real_escape_string($conn, $_POST["table"]);
	$number=mysqli_real_escape_string($conn, $_POST["number"]);
	mysqli_query($conn, "DELETE FROM ".$table." WHERE ".$field." LIKE '".$number."'");
	$emp_id = $_SESSION["emp_id"];
	if($table == "tbl_iar"){
		mysqli_query($conn, "UPDATE tbl_po SET inspection_status = '0', iar_no = '' WHERE iar_no = '$number'");
		$description = $_SESSION["username"]." deleted an IAR document No. ".$number;
		mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
	}
	if($table == "tbl_po"){
		$description = $_SESSION["username"]." deleted a PO document No. ".$number;
		mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
	}
}

function update_po(){
	global $conn;
	
	$edate_received = mysqli_real_escape_string($conn, $_POST["edate_received"]);	
	$epo_number = mysqli_real_escape_string($conn, $_POST["epo_number"]);
	$epr_no = mysqli_real_escape_string($conn, $_POST["epr_no"]);
	$eprocurement_mode = mysqli_real_escape_string($conn, $_POST["eprocurement_mode"]);
	$edelivery_term = mysqli_real_escape_string($conn, $_POST["edelivery_term"]);
	$epayment_term = mysqli_real_escape_string($conn, $_POST["epayment_term"]);
	$esupplier = mysqli_real_escape_string($conn, $_POST["esupplier"]);
	$epo_enduser = mysqli_real_escape_string($conn, $_POST["epo_enduser"]);
	$edate_conformed = mysqli_real_escape_string($conn, $_POST["edate_conformed"]);
	$edate_delivered = mysqli_real_escape_string($conn, $_POST["edate_delivered"]);
	$estatus = mysqli_real_escape_string($conn, $_POST["estatus"]);
	$einspection_status = mysqli_real_escape_string($conn, $_POST["einspection_status"]);

	if($esupplier == "0"){
		echo "1";
	}else{
		mysqli_query($conn, "UPDATE tbl_po SET date_received='$edate_received', pr_no='$epr_no', procurement_mode='$eprocurement_mode', delivery_term='$edelivery_term', payment_term='$epayment_term', date_conformed='$edate_conformed', date_delivered='$edate_delivered', status='$estatus', /*inspection_status='$einspection_status',*/ supplier_id='$esupplier', end_user='$epo_enduser' WHERE po_number LIKE '$epo_number'");
		$emp_id = $_SESSION["emp_id"];
		$description = $_SESSION["username"]." edited the details of PO#".$epo_number;
		mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
		echo "0";
	}
}

function get_po_pic(){
	global $conn;

	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$sql = mysqli_query($conn, "SELECT view_po FROM tbl_po WHERE po_number LIKE '$po_number' AND view_po <> ''");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		echo $row["view_po"];
	}
}

function get_ntc_attr(){
	global $conn;

	$ntc_category = mysqli_real_escape_string($conn, $_POST["ntc_category"]);
	$ntc_year = mysqli_real_escape_string($conn, $_POST["ntc_year"]);
	$sql = mysqli_query($conn, "SELECT total_contract, contract_effectivity, contract_number, ntc_balance, actual_balance, breakfast_ppax, amsnacks_ppax, lunch_ppax, pmsnacks_ppax, dinner_ppax, lodging_ppax FROM ref_ntc WHERE ntc_category LIKE '$ntc_category' AND contract_effectivity LIKE '%$ntc_year%'");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		echo json_encode(array("total_contract"=>$row["total_contract"], 
			"contract_effectivity"=>$row["contract_effectivity"], 
			"contract_number"=>$row["contract_number"],
			"ntc_balance"=>$row["ntc_balance"],
			"actual_balance"=>$row["actual_balance"],
			"_ppax"=>array($row["breakfast_ppax"],$row["amsnacks_ppax"],$row["lunch_ppax"],$row["pmsnacks_ppax"],$row["dinner_ppax"],$row["lodging_ppax"]),
			"found"=>true));
	}else{
		echo json_encode(array("found"=>false));
	}
}

function get_ntc(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT ntc_category FROM ref_ntc ORDER BY ntc_id ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option data-val=".$row["ntc_category"].">".$row["ntc_category"]."</option>";
		}	
	}
}

function get_caterer(){
	global $conn;
	
	$sql = mysqli_query($conn, "SELECT caterer_id, caterer FROM ref_caterer WHERE status = '0' ORDER BY caterer_id ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option value=".$row["caterer_id"].">".$row["caterer"]."</option>";
		}
	}
}

function get_coordinator(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT coordinator_id, coordinator FROM ref_coordinator WHERE status = '0' ORDER BY coordinator_id ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option value=".$row["coordinator_id"].">".$row["coordinator"]."</option>";
		}
	}
}

function get_category(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT category_id, category FROM ref_category WHERE status = '0' ORDER BY category_id ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option value=".$row["category_id"].">".$row["category"]."</option>";
		}
	}	
}

function get_unit(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT unit_id, unit FROM ref_unit WHERE status = '0' ORDER BY unit_id ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option value=".$row["unit_id"]."┼".$row["unit"].">".$row["unit"]."</option>";
		}
	}
}

function get_item(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT i.item_id, i.item, c.category, c.account_code FROM ref_item AS i, ref_category AS c WHERE i.status = '0' AND c.category_id = i.category_id ORDER BY i.item ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option data-cat=\"".$row["category"]."\" data-ac=\"".$row["account_code"]."\" value=".$row["item_id"]."┼".$row["item"].">".$row["item"]."</option>";
		}
	}
}

function get_end_user() {
    global $connhr;
    $sql = mysqli_query($connhr, "SELECT emp_id, fname, mname, lname, prefix, suffix FROM tbl_employee WHERE status = 'Active' ORDER BY fname ASC");
    if (mysqli_num_rows($sql) != 0) {
        $options = '';
        while ($row = mysqli_fetch_assoc($sql)) {
            $prefix = !empty($row["prefix"]) ? $row["prefix"] . " " : "";
            $mname_initial = !empty($row["mname"]) ? $row["mname"][0] . ". " : "";
            $suffix = !empty($row["suffix"]) ? ", " . $row["suffix"] : "";
            $name = "{$prefix}{$row['fname']} {$mname_initial}{$row['lname']}{$suffix}";
            $options .= "<option data-fn=\"{$name}\" value=\"{$row['emp_id']}\">{$name}</option>";
        }
        echo $options;
    }
}


function get_supplier(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT supplier_id, supplier FROM ref_supplier WHERE status = '0' ORDER BY supplier_id ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option data-s=\"".$row["supplier"]."\" value=".$row["supplier_id"]."┼".$row["supplier"].">".$row["supplier"]."</option>";
		}
	}
}

function get_latest_po(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT DISTINCT po_number FROM tbl_po ORDER BY po_id DESC LIMIT 1");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		echo str_pad(((int)explode("-", $row["po_number"])[2]) + 1, 4, '0', STR_PAD_LEFT);
	}else{
		echo "0001";
	}
}

function get_delayed_po(){
	global $conn;

	$supplier_po = array();
	$lists = "";
	$separator = "|";
	$sql = mysqli_query($conn, "SELECT DISTINCT p.po_number, p.status, p.procurement_mode, s.supplier, p.delivery_term, p.date_conformed FROM tbl_po AS p, ref_supplier AS s WHERE p.supplier_id = s.supplier_id ORDER BY po_id ASC");
	$csp = 0;

	while($row = mysqli_fetch_assoc($sql)){
		$date = date_create($row["date_conformed"]);
		date_add($date,date_interval_create_from_date_string($row["delivery_term"] == "Progress Billing" || $row["delivery_term"] == "" ? "0 days" : $row["delivery_term"]));
		$expected_delivery_date = date_format($date,"Y-m-d");
		$start_date = strtotime(date("Y-m-d"));
		$end_date = strtotime($expected_delivery_date);

		$remaining_days = round(($end_date - $start_date)/60/60/24);
		$supplier_po = ($remaining_days < 0 && $row["status"] == "Not Yet Delivered") ? array_push_assoc($supplier_po, $row["supplier"], $row["po_number"]) : $supplier_po;
	}

	if(count($supplier_po) != 0){
		foreach ($supplier_po as $key => $value) {
	        $lists.="<li>
                        <a onclick=\"print_dl('".$key."', '".implode($separator, $value)."', '".$separator."');\" class=\"dropdown-item\">
                            <div>
                                <i class=\"fa fa-user\"></i> ".strtoupper($key)."
                                <span class=\"float-right text-muted small\"><span class=\"label label-primary\" style=\"border-radius: 10px;\">".count($supplier_po[$key])."</span> Purchase Orders</span>
                            </div>
                        </a>
                    </li>
                    <li class=\"dropdown-divider\"></li>";
		}
		$csp = 1;
	}else{
		$lists="<li>
                    <a class=\"dropdown-item\">
                        <div>
                            <span class=\"text-muted medium\"><center>No NOTC to generate.</center></span>
                        </div>
                    </a>
                </li>";
	}

	echo json_encode(array("tbody"=>$tbody, "supplier_po"=>$supplier_po, "lists"=>$lists, "csp"=>$csp));

}

function get_po() {
    global $conn;
	$limit = '10';
	$page = 1;
	if($_POST["page"] > 1){
	  $start = (($_POST["page"] - 1) * $limit);
	  $page = $_POST["page"];
	}else{
	  $start = 0;
	}
	$query = "SELECT DISTINCT p.po_number, p.remarks, p.status, p.procurement_mode,s.supplier, SUBSTRING(p.date_received, 1, 10) AS date_r, p.delivery_term, p.date_conformed, p.date_delivered, p.activity_date, p.end_user FROM tbl_po AS p, ref_supplier AS s WHERE p.supplier_id = s.supplier_id ";
	if($_POST["search"] != ""){
		$qs = mysqli_real_escape_string($conn, $_POST["search"]);
		$query.="AND (p.end_user LIKE '%$qs%' OR p.po_number LIKE '%$qs%' OR p.status LIKE '%$qs%' OR p.procurement_mode LIKE '%$qs%' OR s.supplier LIKE '%$qs%' OR p.item_name LIKE '%$qs%') ";
	}
	$query.="ORDER BY p.po_id DESC ";
	$sql_orig = mysqli_query($conn, $query);
	$sql = mysqli_query($conn, $query."LIMIT ".$start.", ".$limit."");
	$tbody = "";
	$total_data = mysqli_num_rows($sql_orig);
	if($total_data != 0){
		while($row = mysqli_fetch_assoc($sql)){
			$eu = str_replace(' ', '', $row["end_user"]);
			$pon = $row["po_number"];
			$items_query = "SELECT item_name, inspection_status FROM tbl_po WHERE po_number = ?";
			$items_stmt = mysqli_prepare($conn, $items_query);
			mysqli_stmt_bind_param($items_stmt, 's', $pon);
			mysqli_stmt_execute($items_stmt);
			$items_result = mysqli_stmt_get_result($items_stmt);

			$num_item_rows = mysqli_num_rows($items_result);
			$total_inspected = 0;
			$items = [];
			while ($item_row = mysqli_fetch_assoc($items_result)) {
				$items[] = trim($item_row["item_name"]);
				$total_inspected += (int)$item_row["inspection_status"];
			}

			$tbody .= "<tr>
				<td>{$row['date_r']}</td>
				<td>{$row['po_number']}</td>
				<td style=\"font-size: 10px;\">" . implode(", ", $items) . "</td>
				<td>{$row['procurement_mode']}</td>
				<td>{$row['date_conformed']}</td>
				<td>{$row['date_delivered']}</td>
				<td>{$row['supplier']}</td>
				<td>{$row['end_user']}</td>
				<td>{$row['status']}</td>
				<td style=\"font-size: 10px;\">" . ($num_item_rows == $total_inspected ? '✔️ Fully Inspected (All items have an IAR)' : ($total_inspected == 0 ? '❌ Not Inspected (No items have an IAR)' : '⌛ Partially Inspected (Some items have an IAR)')) . "</td>
				<td><center>
					<button id=\"{$row['po_number']}\" class=\"btn btn-xs btn-warning dim\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"View\" onclick=\"view_po(this.id, '$eu')\"><i class=\"fa fa-picture-o\"></i></button>
					<button id=\"{$row['po_number']}\" class=\"btn btn-xs btn-info dim\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit\" onclick=\"edit_po_various(this.id)\"><i class=\"fa fa-pencil-square-o\"></i></button>
					<button id=\"{$row['po_number']}\" class=\"btn btn-xs btn-success dim\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Consolidate\" onclick=\"consolidate(this.id)\"><i class=\"fa fa-stack-overflow\"></i></button>
					<button id=\"{$row['po_number']}\" class=\"btn btn-xs btn-default dim\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Billing History\" onclick=\"billing_history(this.id)\"><i class=\"fa fa-history\"></i></button>
					" . (($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU") ? 
						"<button id=\"{$row['po_number']}\" class=\"btn btn-xs btn-danger dim\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete\" onclick=\"delete_control(this.id)\"><i class=\"fa fa-trash\"></i></button>" : "") . "
				</center></td>
			</tr>";
		}
	}else{
		$tbody = "<tr><td colspan=\"11\" style=\"text-align: center;\">No data found.</td></tr>";
	}
	$in_out = create_table_pagination($page, $limit, $total_data, ["Date Received","PO Number","Items","Procurement Mode","Date Conformed","Date Delivered", "Supplier","End User","Status","Inspection",""]);
	$whole_dom = $in_out[0] . $tbody . $in_out[1];
	echo $whole_dom;
}

function edit_po_various(){
	global $conn;
	
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$sql = mysqli_query($conn, "SELECT p.po_id, SUBSTRING(p.date_received,1,10) AS dr, p.delivery_term, p.payment_term, p.description, p.main_stocks, p.pr_no, s.supplier, p.item_name, p.end_user, p.unit_cost, p.quantity, p.category, p.date_conformed, p.date_delivered, p.inspection_status, p.status, p.sn_ln, p.po_type, p.procurement_mode FROM tbl_po AS p, ref_supplier AS s WHERE p.po_number LIKE '$po_number' AND s.supplier_id = p.supplier_id");

	$tbody = "";
	$date_received = "";
	$delivery_term = "";
	$payment_term = "";
	$pr_no = "";
	$supplier = "";
	$end_user = "";
	$date_conformed = "";
	$date_delivered = "";
	$status = "";
	$inspection_status = "";
	$po_type = "";
	$procurement_mode = "";
	$tot_amt = 0.00;

	while($row = mysqli_fetch_assoc($sql)){
		$date_received = $row["dr"];
		$delivery_term = $row["delivery_term"];
		$payment_term = $row["payment_term"];
		$pr_no = $row["pr_no"];
		$supplier = $row["supplier"];
		$end_user = $row["end_user"];
		$date_conformed = $row["date_conformed"];
		$date_delivered = $row["date_delivered"];
		$status = $row["status"];
		$inspection_status = $row["inspection_status"];
		$po_type = $row["po_type"];
		$procurement_mode = $row["procurement_mode"];

		$tbody.="<tr>
					<td><button class=\"btn btn-xs btn-default\" onclick=\"swal('".$row["po_id"]."');\">GetID</button></td>
					<td>".$row["item_name"]."</td>
					<td ".(($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU") ? "onclick=\"edit_description('".$po_number."', '".mysqli_real_escape_string($conn,$row["item_name"])."', '".mysqli_real_escape_string($conn, $row["description"])."', '".$row["unit_cost"]."')\"" : "")."><a><u>".$row["description"]."</u></a></td>
					<td>".number_format((float)$row["unit_cost"], 3)."</td>
					<td>".$row["main_stocks"]."</td>
					<td ".((($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU") && $row["main_stocks"] == explode(" ", $row["quantity"])[0]) ? "onclick=\"add_quantity('".$row["po_id"]."', '".$po_number."')\"" : "")."><a><u>".$row["quantity"]."</u></a></td>
					<td>".number_format(((float)$row["unit_cost"]) * (float)(explode(" ", $row["quantity"])[0]), 3)."</td>
					<td><center>".($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU" ? (($row["sn_ln"] == null) ? "<button value=\"".$row["po_id"]."\" id=\"".(int)(explode(" ", $row["quantity"])[0])."\" onclick=\"add_sl(this.value, this.id, '".$row["category"]."');\" class=\"btn btn-info btn-xs\"><i class=\"fa fa-plus\"></i> Add SN/LN</button>" : "<button class=\"btn btn-xs\" style=\"border-radius: 10px; background-color: #00FF00; color: white; font-weight: bold;\" disabled><i class=\"fa fa-check\"></i></button>") : "")."</center></td>
				</tr>";
				$tot_amt+=((float)$row["unit_cost"]) * (float)(explode(" ", $row["quantity"])[0]);
	}
	echo json_encode(array(
		"tbody"=>$tbody,
		"date_received"=>$date_received,
		"delivery_term"=>$delivery_term,
		"payment_term"=>$payment_term,
		"pr_no"=>$pr_no,
		"supplier"=>$supplier,
		"end_user"=>$end_user,
		"date_conformed"=>$date_conformed,
		"date_delivered"=>$date_delivered,
		"status"=>$status,
		"inspection_status"=>$inspection_status,
		"tot_amt"=>$tot_amt,
		"po_type"=>($po_type == "Drugs and Medicines") ? "Drugs and Medicines" : "Supplies, Equipments, and Properties",
		"procurement_mode"=>$procurement_mode));
}

function insert_po_catering(){
	global $conn;

	$date_received = mysqli_real_escape_string($conn, $_POST["date_received"]);
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$ntc_category = mysqli_real_escape_string($conn, $_POST["ntc_category"]);
	$procurement_mode = mysqli_real_escape_string($conn, $_POST["procurement_mode"]);
	$date_filed = mysqli_real_escape_string($conn, $_POST["date_filed"]);
	$activity_date = mysqli_real_escape_string($conn, $_POST["activity_date"]);
	$control_number = mysqli_real_escape_string($conn, $_POST["control_number"]);
	$activity_title = mysqli_real_escape_string($conn, $_POST["activity_title"]);
	$coordinator_id = mysqli_real_escape_string($conn, $_POST["coordinator_id"]);
	$caterer_id = mysqli_real_escape_string($conn, $_POST["caterer_id"]);
	$ntc_amount = mysqli_real_escape_string($conn, $_POST["ntc_amount"]);
	$po_amount = mysqli_real_escape_string($conn, $_POST["po_amount"]);
	$ntc_balance = mysqli_real_escape_string($conn, $_POST["ntc_balance"]);
	$actual_amount = mysqli_real_escape_string($conn, $_POST["actual_amount"]);
	$remarks = mysqli_real_escape_string($conn, $_POST["remarks"]);
	$supply_received = mysqli_real_escape_string($conn, $_POST["supply_received"]);
	$supply_processed = mysqli_real_escape_string($conn, $_POST["supply_processed"]);
	$finance_forwarded = mysqli_real_escape_string($conn, $_POST["finance_forwarded"]);
	$accountant_forwarded = mysqli_real_escape_string($conn, $_POST["accountant_forwarded"]);

	mysqli_query($conn, "INSERT INTO tbl_po(po_number,procurement_mode,date_received,date_filed,activity_date,control_number,activity_title,coordinator_id,caterer_id,ntc_amount,po_amount,ntc_balance,actual_amount,remarks,supply_received,supply_processed,finance_forwarded,accountant_forwarded,po_type) VALUES('$po_number','$procurement_mode','$date_received','$date_filed','$activity_date','$control_number','$activity_title','$coordinator_id','$caterer_id','$ntc_amount','$po_amount','$ntc_balance','$actual_amount','$remarks','$supply_received','$supply_processed','$finance_forwarded','$accountant_forwarded','Catering')");

}

function insert_po_various(){
	global $conn;
	date_default_timezone_set("Asia/Shanghai");
	$time_now = date("H:i:s");
	$date_received = mysqli_real_escape_string($conn, $_POST["date_received"])." ".$time_now;
	$po_number = mysqli_real_escape_string($conn, str_replace(' ', '', $_POST["po_number"]));
	$procurement_mode = mysqli_real_escape_string($conn, $_POST["procurement_mode"]);
	$delivery_term = mysqli_real_escape_string($conn, $_POST["delivery_term"]);
	$payment_term = mysqli_real_escape_string($conn, $_POST["payment_term"]);
	$pr_no = mysqli_real_escape_string($conn, $_POST["pr_no"]);
	$items = $_POST["items"];
	//$reso_no = mysqli_real_escape_string($conn, $_POST["reso_no"]);
	//$abstract_no = mysqli_real_escape_string($conn, $_POST["abstract_no"]);
	$supplier_id = mysqli_real_escape_string($conn, $_POST["supplier_id"]);
	$inspect = mysqli_real_escape_string($conn, $_POST["inspect"]);
	$end_user = mysqli_real_escape_string($conn, $_POST["end_user"]);
	$date_conformed = mysqli_real_escape_string($conn, $_POST["date_conformed"]);
	$date_delivered = mysqli_real_escape_string($conn, $_POST["date_delivered"]);
	$status = mysqli_real_escape_string($conn, $_POST["status"]);

	for($i = 0; $i < count($items); $i++){
		$item_id = $items[$i][0];
		$item_name = mysqli_real_escape_string($conn, $items[$i][1]);
		$description = mysqli_real_escape_string($conn, preg_replace('/["]+/', '', trim(strip_tags($items[$i][2]))));
		$category = $items[$i][3];
		$sn_ln = $items[$i][4];
		$exp_date = $items[$i][5];
		$unit_cost = $items[$i][6];
		$quantity = $items[$i][7];
		$main_stocks = explode(" ", $quantity)[0];
		if(mysqli_query($conn, "INSERT INTO tbl_po(po_number,date_received,procurement_mode,delivery_term,payment_term,pr_no, supplier_id,inspection_status,item_id,item_name,description,category,sn_ln,exp_date,unit_cost,main_stocks,quantity,end_user,date_conformed,date_delivered,status,po_type,entry_type) VALUES('$po_number','$date_received','$procurement_mode','$delivery_term','$payment_term','$pr_no','$supplier_id','$inspect','$item_id','$item_name','$description','$category','$sn_ln','$exp_date','$unit_cost','$main_stocks','$quantity','$end_user','$date_conformed','$date_delivered','$status','$category','0')")){
			if($sn_ln != ""){
				$last_id = (int)mysqli_insert_id($conn);
				$serials_lots = explode("|", $sn_ln);
				for($j = 0; $j < count($serials_lots) - 1; $j++){
					$sl = $serials_lots[$j];
					mysqli_query($conn, "INSERT INTO tbl_serial(inventory_id,serial_no,is_issued) VALUES('$last_id','$sl','N')");
				}
			}
		}
	}
	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." encoded a new Purchase Order No. ".$po_number;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
}

//get_po();

$call_func = mysqli_real_escape_string($conn, $_POST["call_func"]);

switch($call_func){
	case "get_records":
		get_po();
		break;
	case "get_latest_po":
		get_latest_po();
		break;
	case "get_supplier":
		get_supplier();
		break;
	case "get_end_user":
		get_end_user();
		break;
	case "get_item":
		get_item();
		break;
	case "get_unit":
		get_unit();
		break;
	case "get_category":
		get_category();
		break;
	case "get_coordinator":
		get_coordinator();
		break;
	case "get_caterer":
		get_caterer();
		break;
	case "get_ntc":
		get_ntc();
		break;
	case "get_ntc_attr":
		get_ntc_attr();
		break;
	case "insert_po_various":
		insert_po_various();
		break;
	case "insert_po_catering":
		insert_po_catering();
		break;
	case "edit_po_various":
		edit_po_various();
		break;
	case "get_po_pic":
		get_po_pic();
		break;
	case "update_po":
		update_po();
		break;
	case "delete_control":
		delete_control();
		break;
	case "add_serials":
		add_serials();
		break;
	case "update_quantity":
		update_quantity();
		break;
	case "consolidate_po":
		consolidate_po();
		break;
	case "generate_dl":
		generate_dl();
		break;
	case "edit_description":
		edit_description();
		break;
	case "item_name_search":
		item_name_search();
		break;
	case "get_billing_history":
		get_billing_history();
		break;
	case "get_delayed_po":
		get_delayed_po();
		break;
	case "add_item":
		add_item();
		break;
	case "get_notc_details":
		get_notc_details();
		break;
}

?>
