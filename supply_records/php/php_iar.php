<?php

require "../../php/php_conn.php";
require "../../php/php_general_functions.php";

$special_category = ["Drugs and Medicines", "Medical Supplies", "Various Supplies"];

session_start();

function get_inspectorate() {
    global $conn;

    $query = "SELECT g.id AS group_id, g.name AS group_name, m.name AS inspector_name, m.designation 
        FROM tbl_inspectorate_members m
        JOIN tbl_inspectorate_group g ON m.group_id = g.id
        ORDER BY g.id";
    $result = mysqli_query($conn, $query);

    $grouped_inspectors = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $group_id = $row['group_id'];
        $group_name = $row['group_name'];
        $inspector_name = $row['inspector_name'];
        $designation = $row['designation'];

        if (!isset($grouped_inspectors[$group_id])) {
            $grouped_inspectors[$group_id] = [
                'group_name' => $group_name,
                'names' => [],
                'designations' => []
            ];
        }

        $grouped_inspectors[$group_id]['names'][] = $inspector_name;
        $grouped_inspectors[$group_id]['designations'][] = $designation;
    }

    $options = [];
    foreach ($grouped_inspectors as $group_id => $inspectors) {
        $names_text = implode(' | ', $inspectors['names']);
        $designations_value = implode(', ', $inspectors['designations']);
        
        $options[] = "<option value=\"$designations_value\">$names_text</option>";
    }
    echo implode('', $options);
}


function delete_control(){
	global $conn;

	$number = mysqli_real_escape_string($conn, $_POST["number"]);
	mysqli_query($conn, "DELETE FROM tbl_iar WHERE iar_number = '$number'");
	mysqli_query($conn, "UPDATE tbl_po SET iar_no = '', inspection_status = '0' WHERE iar_no = '$number'");

	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." deleted an IAR document No. ".$number;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
}

function get_nod_dv(){
	global $conn;

	$iar_number = mysqli_real_escape_string($conn, $_POST["iar_number"]);
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$supplier = ""; $charge_invoice = ""; $item_description = ""; $item_name = "";
	$spvs = ""; $spvs_designation = ""; $res_cc = ""; $inspector = ""; $inspector_designation = "";
	$date_received = ""; $date_delivered = ""; $delivery_term = ""; $payment_term = "";
	$end_user = ""; $procurement_mode = ""; $date_conformed = "";

	$total_amount = 0.00;

	$sql = mysqli_query($conn, "SELECT s.supplier, i.charge_invoice, i.inspector, i.inspector_designation, i.spvs, i.spvs_designation, i.res_cc, p.item_name, p.description, p.date_received, p.date_delivered, p.date_conformed, p.delivery_term, p.payment_term, p.end_user, p.procurement_mode FROM tbl_po AS p, tbl_iar AS i, ref_supplier AS s WHERE p.supplier_id = s.supplier_id AND p.po_number = '$po_number' AND p.iar_no = '$iar_number' AND i.iar_number = '$iar_number' AND i.po_number = '$po_number'");
	$rows = mysqli_num_rows($sql);

	if($rows != 0){
		$row = mysqli_fetch_assoc($sql);
		$supplier = $row["supplier"];
		$charge_invoice = $row["charge_invoice"];
		$rowspvs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT spvs, spvs_designation FROM tbl_iar WHERE iar_number = '$iar_number'"));
		$spvs = $rowspvs["spvs"]; $spvs_designation = $rowspvs["spvs_designation"];
		$res_cc = $row["res_cc"];
		$date_received = $row["date_received"]; $date_delivered = $row["date_delivered"];
		$delivery_term =$row["delivery_term"]; $payment_term = $row["payment_term"];
		$end_user = $row["end_user"]; $procurement_mode = $row["procurement_mode"];
		$inspector = $row["inspector"];
		$inspector_designation = $row["inspector_designation"];
		$date_conformed = $row["date_conformed"];
		if($rows > 1){
			$item_name = $row["item_name"]." and etc.";
			$item_description = $row["item_name"]." - ".$row["description"]." and etc.";
		}else{
			$item_name = $row["item_name"];
			$item_description = $row["item_name"]." - ".$row["description"];
		}
	}

	$sql2 = mysqli_query($conn, "SELECT unit_cost, main_stocks FROM tbl_po WHERE po_number = '$po_number' AND iar_no = '$iar_number'");
	while($rowt = mysqli_fetch_assoc($sql2)){
		$total_amount+=((float)$rowt["unit_cost"] * (float)$rowt["main_stocks"]);
	}
	echo json_encode(array(
		"supplier"=>$supplier,
		"charge_invoice"=>$charge_invoice,
		"item_description"=>$item_description,
		"num_rows"=>$rows,
		"spvs"=>$spvs,
		"spvs_designation"=>$spvs_designation,
		"res_cc"=>$res_cc,
		"item_name"=>$item_name,
		"date_received"=>$date_received,
		"date_delivered"=>$date_delivered,
		"date_conformed"=>$date_conformed,
		"delivery_term"=>$delivery_term,
		"payment_term"=>$payment_term,
		"end_user"=>$end_user,
		"procurement_mode"=>$procurement_mode,
		"inspector"=>$inspector,
		"inspector_designation"=>$inspector_designation,
		"total_amount"=>number_format((float)$total_amount, 2)
	));
}

function update(){
	global $conn;
	global $special_category;

	$entity_name = mysqli_real_escape_string($conn, $_POST["entity_name"]);
	$iar_number = mysqli_real_escape_string($conn, $_POST["iar_number"]);
	$fund_cluster = mysqli_real_escape_string($conn, $_POST["fund_cluster"]);
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$req_office = mysqli_real_escape_string($conn, $_POST["req_office"]);
	$res_cc = mysqli_real_escape_string($conn, $_POST["res_cc"]);
	$charge_invoice = mysqli_real_escape_string($conn, $_POST["charge_invoice"]);
	$inspector = mysqli_real_escape_string($conn, $_POST["inspector"]);
	$inspector_designation = mysqli_real_escape_string($conn, $_POST["inspector_designation"]);
	$date_inspected = mysqli_real_escape_string($conn, $_POST["date_inspected"]);
	$date_received = mysqli_real_escape_string($conn, $_POST["date_received"]);
	$spvs = mysqli_real_escape_string($conn, $_POST["spvs"]);
	$spvs_designation = mysqli_real_escape_string($conn, $_POST["spvs_designation"]);
	$items = $_POST["items"];
	$iar_type = mysqli_real_escape_string($conn, $_POST["iar_type"]);
	for($i = 0; $i < count($items); $i++){
		$id = $items[$i][0];
		$item_name = mysqli_real_escape_string($conn,$items[$i][1]);
		$description = mysqli_real_escape_string($conn,$items[$i][2]);
		$exp_date = $items[$i][3];
		$manufactured_by = mysqli_real_escape_string($conn,$items[$i][4]);
		$bool = $items[$i][5];
		$lot_no = $items[$i][6];
		mysqli_query($conn,"UPDATE tbl_po SET inspection_status = '$bool', exp_date = '$exp_date', activity_title = '$manufactured_by' WHERE po_id = '$id'");
		if(in_array($iar_type, $special_category) && $lot_no != ""){
			mysqli_query($conn, "UPDATE tbl_po SET sn_ln = '$lot_no' WHERE po_id = '$id'");
			mysqli_query($conn, "UPDATE tbl_serial SET serial_no = '$lot_no' WHERE inventory_id = '$id'");
		}
	}
	mysqli_query($conn,"UPDATE tbl_iar SET entity_name = '$entity_name', fund_cluster = '$fund_cluster', req_office = '$req_office', res_cc = '$res_cc', charge_invoice = '$charge_invoice', inspector = '$inspector', inspector_designation = '$inspector_designation', date_inspected = '$date_inspected', date_received = '$date_received', spvs = '$spvs', spvs_designation = '$spvs_designation' WHERE iar_number LIKE '$iar_number'");

	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." edited the details of IAR No. ".$iar_number;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
}

function get_iar_details(){
	global $conn;
	global $special_category;

	$iar_number = mysqli_real_escape_string($conn, $_POST["iar_number"]);
	$entity_name="";$fund_cluster="";$po_number="";$req_office="";$res_cc="";$charge_invoice="";$date_inspected="";$inspector="";$date_received="";$end_user="";$supplier="";$spvs = ""; $spvs_designation = "";
	$iar_type = "";
	$table = "";
	$sql = mysqli_query($conn, "SELECT p.po_id, i.iar_type, p.sn_ln, i.entity_name, i.fund_cluster, i.po_number, i.req_office, i.res_cc, i.charge_invoice, i.date_inspected, i.inspector, i.date_received, i.spvs, i.spvs_designation, p.end_user, p.date_conformed, p.date_delivered, p.item_name, p.description, p.quantity, p.unit_cost, p.inspection_status, p.main_stocks, p.exp_date, s.supplier, p.activity_title FROM tbl_iar AS i, tbl_po AS p, ref_supplier AS s WHERE i.iar_number LIKE '$iar_number' AND i.iar_number = p.iar_no AND s.supplier_id = p.supplier_id");
	while($row = mysqli_fetch_assoc($sql)){
		$entity_name = $row["entity_name"];$fund_cluster = $row["fund_cluster"];$po_number = $row["po_number"];$req_office = $row["req_office"];$res_cc = $row["res_cc"];
		$charge_invoice = $row["charge_invoice"];$date_inspected = $row["date_inspected"];$inspector = $row["inspector"];$date_received = $row["date_received"];
		$end_user = $row["end_user"];$supplier = $row["supplier"];$spvs = $row["spvs"];$spvs_designation = $row["spvs_designation"]; $iar_type = $row["iar_type"];
		$unit = (explode(" ", $row["quantity"]))[1];
		$sn_ln = implode(",", explode("|", rtrim($row["sn_ln"], "|")));
		$table.="<tr>
					<td>".$row["po_id"]."</td>
					<td>".$row["date_delivered"]."</td>
					<td>".$row["item_name"]."</td>
					<td>".$row["description"]."</td>
					<td><input type=\"text\" value=\"".$sn_ln."\" ".(!in_array($row["iar_type"], $special_category) || $sn_ln == "" ? 'disabled' : '' )."></td>
					<td><input type=\"text\" value=\"".$row["exp_date"]."\" onfocus=\"(this.type='date')\" onblur=\"(this.type='text')\"></td>
					<td><input type=\"text\" value=\"".$row["activity_title"]."\"></td>
					<td>".$row["main_stocks"]." ".$unit."</td>
					<td>".$row["unit_cost"]."</td>
					<td><center>".($row["inspection_status"] == "1" ? "<input type=\"checkbox\" checked>" : "<input type=\"checkbox\">")."</center></td>
				</tr>";
	}
	echo json_encode(array(
		"entity_name"=>$entity_name,
		"fund_cluster"=>$fund_cluster,
		"po_number"=>$po_number,
		"req_office"=>$req_office,
		"res_cc"=>$res_cc,
		"charge_invoice"=>$charge_invoice,
		"date_inspected"=>$date_inspected,
		"inspector"=>$inspector,
		"date_received"=>$date_received,
		"end_user"=>$end_user,
		"supplier"=>$supplier,
		"spvs"=>$spvs,
		"spvs_designation"=>$spvs_designation,
		"table"=>$table,
		"iar_type"=>$iar_type,
	));
}

function print_iar_dm(){
	global $conn;

	$rows_limit = 26; $rows_occupied = 0;
	$iar_number = mysqli_real_escape_string($conn, $_POST["iar_number"]);
	$entity_name = "";$fund_cluster = "";$po_number = "";$req_office = "";$res_cc = "";$invoice = "";$date_inspected = "";$inspector = ""; $inspector_designation = ""; 
	$inspected = ""; $date_received = "";$property_custodian = "";$status = "";$partial_specify = "";$supplier = "";$date_conformed = "";$date_delivered = "";
	$end_user = ""; $tbody = "";$total_amount = 0.00;$manufactured_by="";
	$sql = mysqli_query($conn, "SELECT po_number, entity_name, fund_cluster, req_office, res_cc, charge_invoice, inspector, inspector_designation, inspected, property_custodian, partial_specify FROM tbl_iar WHERE iar_number LIKE '$iar_number'");
	while($row = mysqli_fetch_assoc($sql)){
		$po_number = $row["po_number"];$entity_name = $row["entity_name"];$fund_cluster = $row["fund_cluster"];$req_office = $row["req_office"];
		$res_cc = $row["res_cc"];$invoice = $row["charge_invoice"];$date_inspected = "";$inspector = $row["inspector"]; $inspector_designation = $row["inspector_designation"];
		$inspected = $row["inspected"];$date_received = "";$property_custodian = $row["property_custodian"];$status = "";
		$partial_specify = "";
	}
	//$inspector = str_replace('|', '____', $inspector);

	$sql2 = mysqli_query($conn, "SELECT p.item_name, p.po_id, s.supplier, p.description, p.unit_cost, p.date_conformed, p.date_delivered, p.end_user FROM ref_supplier AS s, tbl_po AS p WHERE p.po_number LIKE '$po_number' AND p.inspection_status = '1' AND s.supplier_id = p.supplier_id AND p.iar_no LIKE '$iar_number'");
	while($row = mysqli_fetch_assoc($sql2)){
		$po_id = $row["po_id"];
		$item_name = mysqli_real_escape_string($conn,$row["item_name"]); $poid = $row["po_id"];
		$supplier = $row["supplier"]; $date_conformed = $row["date_conformed"];$date_delivered = $row["date_delivered"];$end_user = $row["end_user"];
		$quan_unit = "";
		$getQuan = mysqli_query($conn, "SELECT quantity, main_stocks, activity_title FROM tbl_po WHERE item_name LIKE '$item_name' AND iar_no LIKE '$iar_number' AND po_id = '$po_id'");
		$total_quan = 0.00;
		while($rowt = mysqli_fetch_assoc($getQuan)){
			$quan_unit = explode(" ", $rowt["quantity"]);
			$total_quan+=(float)$rowt["main_stocks"];
			$manufactured_by = $rowt["activity_title"];
		}
		$tbody.="<tr>
	          <td style=\"width: 73.2px; height: 15px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
	          <td colspan=\"3\" style=\"width: 148.8px; height: 15px; text-align: left; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["description"]."</td>
	          <td style=\"width: 63px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$quan_unit[1]."</td>
	          <td colspan=\"2\" style=\"width: 57.6px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: center; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; border-right-color: rgb(0, 0, 0); border-right-width:2px;border-right-style:solid;\">".number_format((float)$total_quan, 0)."</td>
	        </tr>";
	        
	        $sql3 = mysqli_query($conn, "SELECT main_stocks, quantity, sn_ln, exp_date FROM tbl_po WHERE item_name LIKE '$item_name' AND iar_no LIKE '$iar_number' AND po_id = '$po_id'");
	        while($rows = mysqli_fetch_assoc($sql3)){
	        	$tbody.="<tr>
		          <td style=\"width: 73.2px; height: 15px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
		          <td style=\"width: 148.8px; height: 15px; text-align: left; font-size: 10px; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;\">Lot#: ".implode(",", explode("|", rtrim($rows["sn_ln"], "|")))."</td>
		          <td style=\"width: 144px; height: 15px; text-align: left; font-size: 10px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;\">Exp. Date: ".$rows["exp_date"]."</td>
		          <td style=\"width: 72.6px; height: 15px; text-align: right; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$rows["main_stocks"]." ".$quan_unit[1]."&nbsp;&nbsp;&nbsp;&nbsp;</td>
		          <td style=\"width: 63px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		          <td style=\"width: 57.6px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;\"></td>
		          <td style=\"width: 28.8px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		        </tr>";
		        $rows_occupied+=1;
	        }

	        $tbody.="<tr>
	          <td style=\"width: 73.2px; height: 15px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
	          <td colspan=\"3\" style=\"width: 148.8px; height: 15px; text-align: left; font-size: 10px; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 2px; border-right-style: solid;\">Manufactured by: ".$manufactured_by."</td>
	          <td style=\"width: 63px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	          <td style=\"width: 57.6px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;\"></td>
	          <td style=\"width: 28.8px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        </tr>
	        <tr>
	          <td style=\"width: 73.2px; height: 15px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
	          <td colspan=\"3\" style=\"width: 148.8px; height: 15px; text-align: left; font-size: 10px; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 2px; border-right-style: solid;\">Price: P ".number_format((float)$row["unit_cost"],3)." = P ".number_format(((float)$row["unit_cost"] * (float)$total_quan), 3)."</td>
	          <td style=\"width: 63px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	          <td style=\"width: 57.6px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;\"></td>
	          <td style=\"width: 28.8px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        </tr>
	        <tr>
	          <td style=\"width: 73.2px; height: 15px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
	          <td style=\"width: 148.8px; height: 15px; font-size: 10px; font-weight: bold; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;\"></td>
	          <td style=\"width: 144px; height: 15px; font-size: 10px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;\"></td>
	          <td style=\"width: 72.6px; height: 15px; text-align: left; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	          <td style=\"width: 63px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	          <td style=\"width: 57.6px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;\"></td>
	          <td style=\"width: 28.8px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        </tr>";
	        $rows_occupied+=4;
	        $total_amount+=((float)$row["unit_cost"] * (float)$total_quan);
	}
	for($i = 0; $i < ($rows_limit - $rows_occupied); $i++){
		$tbody.="<tr>
		          <td style=\"width: 73.2px; height: 15px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
		          <td style=\"width: 148.8px; height: 15px; font-size: 10px; font-weight: bold; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;\"></td>
		          <td style=\"width: 144px; height: 15px; font-size: 10px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;\"></td>
		          <td style=\"width: 72.6px; height: 15px; text-align: left; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		          <td style=\"width: 63px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		          <td style=\"width: 57.6px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;\"></td>
		          <td style=\"width: 28.8px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		        </tr>";
	}

	echo json_encode(array(
		"entity_name"=>$entity_name,
		"fund_cluster"=>$fund_cluster,
		"po_number"=>$po_number." / "._m_d_yyyy_($date_conformed),
		"req_office"=>$req_office,
		"res_cc"=>$res_cc,
		"invoice_number"=>$invoice,
		"tbody"=>$tbody,
		"date_inspected"=>$date_inspected,
		"inspector"=>$inspector,
		"inspector_designation"=>$inspector_designation,
		"inspected"=>$inspected,
		"date_received"=>$date_received,
		"property_custodian"=>$property_custodian,
		"status"=>$status,
		"partial_specify"=>$partial_specify,
		"date_conformed"=>_m_d_yyyy_($date_conformed),
		"date_delivered"=>_m_d_yyyy_($date_delivered),
		"end_user"=>$end_user,
		"supplier"=>$supplier,
		"total_amount"=>number_format((float)$total_amount, 2)
		)
	);
}

function print_iar_gen(){
	global $conn;

	$rows_limit = 26; $rows_occupied = 0;
	$iar_number = mysqli_real_escape_string($conn, $_POST["iar_number"]);
	$entity_name = "";$fund_cluster = "";$po_number = "";$req_office = "";$res_cc = "";$invoice = "";$date_inspected = "";$inspector = ""; $inspector_designation = ""; 
	$inspected = ""; $date_received = "";$property_custodian = "";$status = "";$partial_specify = "";$supplier = "";$date_conformed = "";$date_delivered = "";$end_user = "";
	$tbody = "";$total_amount = 0.00;
	$sql = mysqli_query($conn, "SELECT po_number, entity_name, fund_cluster, req_office, res_cc, charge_invoice, inspector, inspector_designation, inspected, property_custodian, partial_specify FROM tbl_iar WHERE iar_number LIKE '$iar_number'");
	while($row = mysqli_fetch_assoc($sql)){
		$po_number = $row["po_number"];$entity_name = $row["entity_name"];$fund_cluster = $row["fund_cluster"];$req_office = $row["req_office"];
		$res_cc = $row["res_cc"];$invoice = $row["charge_invoice"];$date_inspected = "";$inspector = $row["inspector"]; $inspector_designation = $row["inspector_designation"];
		$inspected = $row["inspected"];$date_received = "";$property_custodian = $row["property_custodian"];$status = "";
		$partial_specify = $row["partial_specify"];
	}
	//$inspector = str_replace('|', '____', $inspector);
	$sql2 = mysqli_query($conn, "SELECT p.item_name, s.supplier, p.description, p.quantity, p.main_stocks, p.unit_cost, p.date_conformed, p.date_delivered, p.end_user FROM ref_supplier AS s, tbl_po AS p WHERE p.po_number LIKE '$po_number' AND p.inspection_status = '1' AND s.supplier_id = p.supplier_id AND p.iar_no LIKE '$iar_number'");
	while($row = mysqli_fetch_assoc($sql2)){
		$supplier = $row["supplier"]; $date_conformed = $row["date_conformed"];$date_delivered = $row["date_delivered"];$end_user = $row["end_user"];
		$quan_unit = explode(" ",$row["quantity"]);
		$tbody.="<tr>
			      <td style=\"width: 84.6px; height: 15px; font-size: 10px; text-align: center; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\">".$date_delivered."</td>
			      <td colspan=\"3\" style=\"width: 148.8px; height: 15px; text-align: left; font-size: 10px; font-weight: bold; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 2px; border-right-style: solid;\">".$row["item_name"]."</td>
			      <td style=\"width: 82.2px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$quan_unit[1]."</td>
			      <td colspan=\"2\" style=\"width: 57.6px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 2px; border-right-style: solid;\">".$row["main_stocks"]."</td>
			    </tr>";
			    $rows_occupied++;
			    $total_amount+=((float)$row["unit_cost"] * (float)$row["main_stocks"]);
		$arr = array($row["description"], "");
		for($j = 0; $j < count($arr); $j++){
			$tbody.="<tr>
				      <td style=\"width: 84.6px; height: 15px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
				      <td colspan=\"3\" style=\"width: 148.8px; height: 15px; text-align: left; font-size: 10px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 2px; border-right-style: solid;\">".$arr[$j]."</td>
				      <td style=\"width: 82.2px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
				      <td colspan=\"2\" style=\"width: 57.6px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 2px; border-right-style: solid;\"></td>
				    </tr>";
		   	$rows_occupied++;
		}
	}
	for($i = 0; $i < ($rows_limit - $rows_occupied); $i++){
		$tbody.="<tr>
			      <td style=\"width: 84.6px; height: 15px; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
			      <td colspan=\"3\" style=\"width: 148.8px; height: 15px; text-align: left; font-size: 10px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 2px; border-right-style: solid;\"></td>
			      <td style=\"width: 82.2px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
			      <td colspan=\"2\" style=\"width: 57.6px; height: 15px; text-align: center; font-size: 10px; font-weight: bold; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 2px; border-right-style: solid;\"></td>
			    </tr>";
	}

	echo json_encode(array(
		"entity_name"=>$entity_name,
		"fund_cluster"=>$fund_cluster,
		"po_number"=>$po_number,
		"req_office"=>$req_office,
		"res_cc"=>$res_cc,
		"charge_invoice"=>$invoice,
		"tbody"=>$tbody,
		"date_inspected"=>$date_inspected,
		"inspector"=>$inspector,
		"inspector_designation"=>$inspector_designation,
		"inspected"=>$inspected,
		"date_received"=>$date_received,
		"property_custodian"=>$property_custodian,
		"status"=>$status,
		"partial_specify"=>$partial_specify,
		"date_conformed"=>_m_d_yyyy_($date_conformed),
		"date_delivered"=>_m_d_yyyy_($date_delivered),
		"end_user"=>$end_user,
		"supplier"=>$supplier,
		"total_amount"=>number_format((float)$total_amount, 3)
		)
	);
}

function get_rcc(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT code, acronym FROM ref_rcc WHERE status = '0' ORDER by id");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option value=\"".$row["code"]."\">".$row["acronym"]."</option>";
		}
	}
}

function get_po(){
	global $special_category;
	global $conn;

	$action = mysqli_real_escape_string($conn, $_POST["action"]);
	$add_query = (isset($_POST["add_query"])) ? "AND inspection_status <> '1' AND status LIKE 'Delivered'" : "";

	if($action == "get_number"){
		$operator = (mysqli_real_escape_string($conn, $_POST["po_type"]) == "ictvar") ? "po_type != 'Catering'" : "po_type != 'Catering'";
		$sql = mysqli_query($conn, "SELECT DISTINCT po_number FROM tbl_po WHERE ".$operator." ".$add_query." ORDER BY po_id DESC");
		
		if(mysqli_num_rows($sql) != 0){
			while($row = mysqli_fetch_assoc($sql)){
				echo "<option id=".$row["po_number"].">".$row["po_number"]."</option>";
			}	
		}
	}

	if($action == "get_details"){
		$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
		$sql = mysqli_query($conn, "SELECT p.po_id,  p.sn_ln, s.supplier, p.date_delivered, p.date_conformed, p.end_user, i.item, p.description, p.exp_date, p.unit_cost, p.quantity, p.po_type, p.activity_title FROM ref_supplier AS s, tbl_po AS p, ref_item AS i WHERE p.po_number LIKE '$po_number' AND s.supplier_id = p.supplier_id AND i.item_id = p.item_id AND p.inspection_status = '0'");
		$supplier = "";
		$date_delivered = "";
		$date_conformed = "";
		$end_user = "";
		$po_type = "";
		$tbody = "";

		if(mysqli_num_rows($sql) != 0){
			while($row = mysqli_fetch_assoc($sql)){
				$supplier = $row["supplier"];
				$date_delivered = $row["date_delivered"];
				$date_conformed = $row["date_conformed"];
				$end_user = $row["end_user"];
				$quantity = explode(" ", $row["quantity"]);
				$po_type = $row["po_type"];
				$sn_ln = implode(",", explode("|", rtrim($row["sn_ln"], "|")));
				$tbody.="<tr>
							<td>".$row["po_id"]."</td>
							<td>".$date_delivered."</td>
							<td>".$row["item"]."</td>
							<td>".$row["description"]."</td>
							<td><input type=\"text\" value=\"".$sn_ln."\" ".(!in_array($po_type, $special_category) || $sn_ln == "" ? 'disabled' : '' )."></td>
							<td><input type=\"text\" value=\"".$row["exp_date"]."\" onfocus=\"(this.type='date')\" onblur=\"(this.type='text')\"></td>
							<td><input type=\"text\" value=\"".$row["activity_title"]."\"></td>
							<td>".$row["quantity"]."</td>
							<td>".number_format((float)$quantity[0] * (float)$row["unit_cost"], 3)."</td>
							<td><center><input type=\"checkbox\" checked></center></td>
						</tr>";
			}
			$iar_number = get_latest_iar($supplier);
			echo json_encode(array("supplier"=>$supplier, 
				"date_delivered"=>$date_delivered, 
				"date_conformed"=>$date_conformed, 
				"tbody"=>$tbody, 
				"end_user"=>$end_user,
				"po_type"=>$po_type,
				"iar_number"=>$iar_number,
				"success"=>true));
		}else{
			echo json_encode(array("success"=>false));
		}
	}
}

function get_records(){
	global $conn;
	global $special_category;

	$limit = '10';
	$page = 1;
	if($_POST["page"] > 1){
	  $start = (($_POST["page"] - 1) * $limit);
	  $page = $_POST["page"];
	}else{
	  $start = 0;
	}

	$query = "SELECT DISTINCT iar_id, iar_number, iar_type, po_number, req_office, res_cc FROM tbl_iar ";
	if($_POST["search"] != ""){
		$qs = mysqli_real_escape_string($conn, $_POST["search"]);
		$query.="WHERE iar_number LIKE '%$qs%' OR po_number LIKE '%$qs%' OR req_office LIKE '%$qs%' OR res_cc LIKE '%$qs%' ";
	}
	$query.="ORDER BY iar_id DESC ";
	
	$sql_orig = mysqli_query($conn, $query);
	$sql = mysqli_query($conn, $query."LIMIT ".$start.", ".$limit."");
	$tbody = "";
	$total_data = mysqli_num_rows($sql_orig);

	if($total_data != 0){
		while($row = mysqli_fetch_assoc($sql)){
			$pn = $row["po_number"];
			$iar_number = $row["iar_number"];
			$iar_types = [];
			$get_iar_types = mysqli_query($conn, "SELECT po_type FROM tbl_po WHERE iar_no LIKE '$iar_number'");
			while ($ri = mysqli_fetch_assoc($get_iar_types)) {
				$iar_types[] = $ri["po_type"];
			}
			$has_special = count(array_intersect($iar_types, $special_category)) > 0;
			$printAction = $has_special ? "print_iar_dm" : "print_iar";
			$xlsAction   = $has_special ? "download_xls_dm" : "download_xls";

			$tbody .= "<tr>
						<td>{$row["iar_id"]}</td>
						<td>{$row["po_number"]}</td>
						<td>{$row["iar_number"]}</td>
						<td>{$row["req_office"]}</td>
						<td>{$row["res_cc"]}</td>
						<td>
							<center>
								<button class=\"btn btn-xs btn-primary dim\" value=\"{$row["iar_number"]}\" onclick=\"view_iss(this.value, 'tbl_iar', 'view_iar', 'IAR', 'iar_number', '".substr($pn, 0, 4)."');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Preview\">
									<i class=\"fa fa-picture-o\"></i>
								</button>
								&nbsp;".
								(($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU") ? "
								<button class=\"btn btn-xs btn-info dim\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit\" value=\"{$row["iar_number"]}\" onclick=\"modify(this.value);\">
									<i class=\"fa fa-pencil-square-o\"></i>
								</button>
								&nbsp;" : "")."
								<button class=\"btn btn-xs btn-success dim ladda-button\" data-style=\"slide-down\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Print\" value=\"{$row["iar_number"]}\" onclick=\"{$printAction}(this.value);\">
									<i class=\"fa fa-print\"></i>
								</button>
								&nbsp;".
								(($_SESSION["role"] == "SUPPLY" || $_SESSION["role"] == "SUPPLY_SU") ? "
								<button id=\"{$row["iar_number"]}\" class=\"btn btn-xs btn-danger dim\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete\" onclick=\"delete_control(this.id);\">
									<i class=\"fa fa-trash\"></i>
								</button>
								&nbsp;" : "")."
								<button class=\"btn btn-xs btn-warning dim\" value=\"{$row["iar_number"]}\" onclick=\"{$xlsAction}(this.value);\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Save as Excel\">
									<i class=\"fa fa-file-excel-o\"></i>
								</button>
								&nbsp;|&nbsp;
								<button class=\"btn btn-xs btn-default dim\" title=\"Notice of Delivery\" onclick=\"print_nod('{$row["iar_number"]}', '{$row["po_number"]}');\">
									<i class=\"fa fa-truck\"></i>
								</button>
								&nbsp;
								<button class=\"btn btn-xs btn-default dim\" title=\"Disbursement Voucher\" onclick=\"print_dv('{$row["iar_number"]}', '{$row["po_number"]}');\">
									<i class=\"fa fa-credit-card\"></i>
								</button>
								&nbsp;
								<button class=\"btn btn-xs btn-default dim\" title=\"Performance Evaluation\" onclick=\"print_pe('{$row["iar_number"]}', '{$row["po_number"]}');\">
									<i class=\"fa fa-tasks\"></i>
								</button>
							</center>
						</td>
					</tr>";
		}
	}else{
		$tbody = "<tr><td colspan=\"6\" style=\"text-align: center;\">No data found.</td></tr>";
	}
	$in_out = create_table_pagination($page, $limit, $total_data, array("","PO No.","IAR No.","Requisitioning Office","Responsibility Center Code",""));
	$whole_dom = $in_out[0]."".$tbody."".$in_out[1];
	echo $whole_dom;
}

function insert_ntc(){
	echo "ntc_inserted!";
}

function insert_various(){
	global $conn;
	global $connhr;
	global $special_category;

	$entity_name = mysqli_real_escape_string($conn, $_POST["entity_name"]);
	$fund_cluster = mysqli_real_escape_string($conn, $_POST["fund_cluster"]);
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$iar_number = mysqli_real_escape_string($conn, $_POST["iar_number"]);
	$iar_type = mysqli_real_escape_string($conn, $_POST["iar_type"]);
	$req_office = mysqli_real_escape_string($conn, $_POST["req_office"]);
	$res_cc = mysqli_real_escape_string($conn, $_POST["res_cc"]);
	$charge_invoice = mysqli_real_escape_string($conn, $_POST["charge_invoice"]);
	$date_inspected = mysqli_real_escape_string($conn, $_POST["date_inspected"]);
	$inspector = mysqli_real_escape_string($conn, $_POST["inspector"]);
	$inspector_designation = mysqli_real_escape_string($conn, $_POST["inspector_designation"]);
	$inspected = mysqli_real_escape_string($conn, $_POST["inspected"]);
	$date_received = mysqli_real_escape_string($conn, $_POST["date_received"]);
	$property_custodian = mysqli_real_escape_string($conn, $_POST["property_custodian"]);
	$status = mysqli_real_escape_string($conn, $_POST["status"]);
	$partial_specify = mysqli_real_escape_string($conn, $_POST["partial_specify"]);
	$items = $_POST["items"];
	$spvs = mysqli_real_escape_string($conn, $_POST["spvs"]);
	$spvs_id = mysqli_real_escape_string($conn, $_POST["spvs_id"]);

	$quer1 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$spvs_id'");
	$spvs_designation = mysqli_fetch_assoc($quer1)["designation"];

	if(mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT iar_number FROM tbl_iar WHERE iar_number = '$iar_number'"))==0){
		mysqli_query($conn, "INSERT INTO tbl_iar(entity_name,fund_cluster,po_number,iar_number,iar_type,req_office,res_cc,charge_invoice,date_inspected,inspector,inspector_designation,inspected,date_received,property_custodian,status,partial_specify,spvs,spvs_designation) VALUES('$entity_name','$fund_cluster','$po_number','$iar_number','$iar_type','$req_office','$res_cc','$charge_invoice','$date_inspected','$inspector','$inspector_designation','$inspected','$date_received','$property_custodian','$status','$partial_specify','$spvs','$spvs_designation')");
		for($i = 0; $i < count($items); $i++){
			$id = $items[$i][0];
			$item_name = mysqli_real_escape_string($conn, $items[$i][1]);
			$description = mysqli_real_escape_string($conn, $items[$i][2]);
			$exp_date = $items[$i][3];
			$manufactured_by = $items[$i][4];
			$bool = $items[$i][5];
			$lot_no = $items[$i][6];
			$iarno = ($bool == 0) ? "" : $iar_number;
			mysqli_query($conn, "UPDATE tbl_po SET inspection_status = '$bool', iar_no = '$iarno', exp_date = '$exp_date', activity_title = '$manufactured_by' WHERE po_id = '$id'");
			if(in_array($iar_type, $special_category) && $lot_no != ""){
				mysqli_query($conn, "UPDATE tbl_po SET sn_ln = '$lot_no' WHERE po_id = '$id'");
				mysqli_query($conn, "UPDATE tbl_serial SET serial_no = '$lot_no' WHERE inventory_id = '$id'");
			}
		}
		$emp_id = $_SESSION["emp_id"];
		$description = $_SESSION["username"]." created an IAR No. ".$iar_number." - PO#".$po_number;
		mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
		echo "0";
	}else{
		echo "1";
	}
}

function get_latest_iar($supplier){
	global $conn; $latest_iar = "";
	
	$yy_mm = date('Y-m');
	$sql = mysqli_query($conn, "SELECT DISTINCT iar_number FROM tbl_iar WHERE iar_number LIKE '%$yy_mm%' ORDER BY iar_id DESC LIMIT 1");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		$latest_iar = str_pad(((int)substr($row["iar_number"], -4)) + 1, 4, '0', STR_PAD_LEFT); 
	}else{
		$latest_iar = "0001";
	}

	return $yy_mm."-".($supplier == "Central Office" ? "CO" : "PO").$latest_iar;
}

$call_func = mysqli_real_escape_string($conn, $_POST["call_func"]);
switch($call_func){
	case "insert_various":
		insert_various();
		break;
	case "insert_ntc":
		insert_ntc();
		break;
	case "get_po":
		get_po();
		break;
	case "get_rcc":
		get_rcc();
		break;
	case "get_records":
		get_records();
		break;
	case "print_iar_gen":
		print_iar_gen();
		break;
	case "print_iar_dm":
		print_iar_dm();
		break;
	case "get_iar_details":
		get_iar_details();
		break;
	case "update":
		update();
		break;
	case "get_nod_dv":
		get_nod_dv();
		break;
	case "delete_control":
		delete_control();
		break;
	case "get_inspectorate":
		get_inspectorate();
		break;
}

?>