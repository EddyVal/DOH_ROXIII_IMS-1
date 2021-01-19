<?php

require "../../php/php_conn.php";
require "../../php/php_general_functions.php";

session_start();

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
	$issued_by = mysqli_real_escape_string($conn, $_POST["issued_by"]);
	$purpose = mysqli_real_escape_string($conn, $_POST["purpose"]);

	mysqli_query($conn, "UPDATE tbl_ris SET entity_name='$entity_name',division='$division',office='$office',tbl_ris.date='$date',fund_cluster='$fund_cluster',rcc='$rcc',requested_by='$requested_by',issued_by='$issued_by',purpose='$purpose' WHERE ris_no LIKE '$ris_no'");
	
	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." edited the details of RIS No. ".$ris_no;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
}

function modify(){
	global $conn;

	$ris_no = mysqli_real_escape_string($conn, $_POST["ris_no"]);
	$entity_name = "";$division = "";$office = "";$date = "";$fund_cluster = "";$rcc = "";$requested_by = "";$issued_by = "";$purpose = "";$table = "";
	$sql = mysqli_query($conn, "SELECT entity_name, division, office, SUBSTRING(tbl_ris.date,1,10) AS date_r, fund_cluster, rcc, requested_by, issued_by, purpose, reference_no, item, description, category, quantity, unit, unit_cost, total, quantity_stocks, remarks FROM tbl_ris WHERE ris_no LIKE '$ris_no'");
	while($row = mysqli_fetch_assoc($sql)){
		$entity_name = $row["entity_name"];$division = $row["division"];$office = $row["office"];$date = $row["date_r"];$fund_cluster = $row["fund_cluster"];
		$rcc = $row["rcc"];$requested_by = $row["requested_by"];$issued_by = $row["issued_by"];$purpose = $row["purpose"];
		$table.="<tr>
					<td>".$row["reference_no"]."</td>
					<td>".$row["item"]."</td>
					<td>".$row["description"]."</td>
					<td>".$row["category"]."</td>
					<td>".$row["quantity"]."</td>
					<td>".$row["unit"]."</td>
					<td>".number_format((float)$row["unit_cost"],2)."</td>
					<td>".number_format((float)$row["total"],2)."</td>
					<td>".$row["quantity_stocks"]."</td>
					<td>".$row["remarks"]."</td>
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
		"issued_by"=>$issued_by,
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
	$sql = mysqli_query($conn, "SELECT item, description, quantity, reference_no FROM ".$table." WHERE ".$field." LIKE '".$number."'");
	while($row = mysqli_fetch_assoc($sql)){
		$item = $row["item"]; $description = $row["description"]; $reference_no = $row["reference_no"]; $quantity = $row["quantity"];
		$query_get_stocks = mysqli_query($conn, "SELECT quantity FROM tbl_po WHERE po_number = '$reference_no' AND item_name = '$item' AND description = '$description'");
		$rstocks = explode(" ", mysqli_fetch_assoc($query_get_stocks)["quantity"]);
		$newrstocks = ((int)$rstocks[0] + (int)$quantity)." ".$rstocks[1];
		mysqli_query($conn, "UPDATE tbl_po SET quantity = '$newrstocks' WHERE po_number = '$reference_no' AND item_name = '$item' AND description = '$description'");
	}
	mysqli_query($conn, "DELETE FROM ".$table." WHERE ".$field." LIKE '".$number."'");
	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." deleted a record RIS No. ".$number;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
}

function print_ris(){
	global $conn;

	$rows_limit = 50;
	$rows_allocate = 0;
	$ris_no = mysqli_real_escape_string($conn, $_POST["ris_no"]);
	$entity_name = "";$fund_cluster = "";$division = "";$office = "";$rcc = "";
	$purpose = "";$requested_by = "";$requested_by_designation = "";$issued_by = "";$issued_by_designation;$date = "";
	$tbody = "";
	$sql = mysqli_query($conn, "SELECT entity_name,fund_cluster,division,office,rcc,unit,description,quantity,unit_cost,total,available,quantity_stocks,remarks,purpose,requested_by,requested_by_designation,issued_by,issued_by_designation,SUBSTRING(tbl_ris.date,1,10) AS dr FROM tbl_ris WHERE ris_no LIKE '$ris_no'");
	while($row = mysqli_fetch_assoc($sql)){
		$entity_name = $row["entity_name"];$fund_cluster = $row["fund_cluster"];$division = $row["division"];$office = $row["office"];$rcc = $row["rcc"];
		$purpose = $row["purpose"];$requested_by = $row["requested_by"];$requested_by_designation = $row["requested_by_designation"];$issued_by = $row["issued_by"];$issued_by_designation = $row["issued_by_designation"];$date = $row["dr"];
		if($row["available"] == 1){
			$tbody.="<tr>
	        <td style=\"width: 64.8px; height: 13.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
	        <td style=\"width: 35.4px; height: 13.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["unit"]."</td>
	        <td colspan=\"2\" style=\"width: 115.8px; height: 13.5px; text-align: left; font-size: 9px; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 1px; border-right-style: solid;\">".$row["description"]."</td>
	        <td style=\"width: 46.8px; height: 13.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["quantity"]."</td>
	        <td style=\"width: 41.4px; height: 13.5px; text-align: center; font-size: 10px; font-style: italic; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"><b>✓</b></td>
	        <td style=\"width: 40.2px; height: 13.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 61.2px; height: 13.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["quantity_stocks"]."</td>
	        <td style=\"width: 108.6px; height: 13.5px; text-align: left; font-size: 9px; font-weight: bold; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["remarks"]."</td>
	      </tr>";
	      $rows_allocate+=round((float)strlen($row["description"]) / 60.00);;
		}
	}
	for($i = 0; $i < ($rows_limit - $rows_allocate); $i++){
		$tbody.="<tr>
	        <td style=\"width: 64.8px; height: 13.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
	        <td style=\"width: 35.4px; height: 13.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td colspan=\"2\" style=\"width: 115.8px; height: 13.5px; text-align: left; font-size: 9px; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid;border-right-color: rgb(0, 0, 0); border-right-width: 1px; border-right-style: solid;\"></td>
	        <td style=\"width: 46.8px; height: 13.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 41.4px; height: 13.5px; text-align: center; font-size: 10px; font-style: italic; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 40.2px; height: 13.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 61.2px; height: 13.5px; text-align: center; font-size: 10px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
	        <td style=\"width: 108.6px; height: 13.5px; text-align: left; font-size: 9px; font-weight: bold; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
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
	$sql = mysqli_query($conn, "SELECT category, description, unit_cost, quantity FROM tbl_po WHERE item_name LIKE '$item_name' AND po_id LIKE '$po_id'");
	$row = mysqli_fetch_assoc($sql);
	$quan_unit = explode(" ", $row["quantity"]);
	echo json_encode(array("description"=>$row["description"], "unit_cost"=>$row["unit_cost"], "quantity"=>$quan_unit[0], "unit"=>$quan_unit[1], "category"=>$row["category"]));
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

	$sql = mysqli_query($conn, "SELECT DISTINCT po_number FROM tbl_po WHERE inspection_status = '1' AND (category LIKE 'Office Supplies' OR category LIKE 'Other Supplies' OR category LIKE 'Various Supplies' OR category LIKE 'Drugs and Medicines' OR category LIKE 'ICT Supplies') ORDER BY po_id DESC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option id=".$row["po_number"].">".$row["po_number"]."</option>";
		}
	}
}

function get_ris(){
	global $conn;
	
	$sql = mysqli_query($conn, "SELECT DISTINCT division,office,ris_no, SUBSTRING(tbl_ris.date,1,10) AS d,requested_by,issued_by,purpose, reference_no, issued FROM tbl_ris ORDER BY ris_id DESC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			$rb = $row["issued_by"];
			echo "<tr>
					<td><center>".(($row["issued"] == '0') ? "<button id=\"".$row["reference_no"]."\" value=\"".$row["ris_no"]."\" ".(($_SESSION["role"] == "SUPPLY") ? "onclick=\"to_issue(this.value, this.id);\"" : "")." class=\"btn btn-xs btn-danger\" style=\"border-radius: 10px;\">✖</button>" : "<button class=\"btn btn-xs\" style=\"border-radius: 10px; background-color: #00FF00; color: white; font-weight: bold;\" disabled>✓</button>")."</center></td>
					<td>".$row["division"]."</td>
					<td>".$row["office"]."</td>
					<td>".$row["ris_no"]."</td>
					<td>".$row["reference_no"]."</td>
					<td>".$row["d"]."</td>
					<td>".utf8_encode($row["requested_by"])."</td>
					<td>".utf8_encode($row["issued_by"])."</td>
					<td>".$row["purpose"]."</td>
					<td><center><button class=\"btn btn-xs btn-primary\" value=\"".$row["ris_no"]."\" onclick=\"view_iss(this.value,'tbl_ris','view_ris','RIS','ris_no','".$rb."');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Preview\"><i class=\"fa fa-picture-o\"></i></button>&nbsp;".(($_SESSION["role"] == "SUPPLY") ? "<button class=\"btn btn-xs btn-info\" value=\"".$row["ris_no"]."\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit\" onclick=\"modify(this.value);\"><i class=\"fa fa-pencil-square-o\"></i></button>&nbsp;" : "")."<button class=\"btn btn-xs btn-success\" value=\"".$row["ris_no"]."\" onclick=\"print_ris(this.value);\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Print\"><i class=\"fa fa-print\"></i></button>&nbsp;".(($_SESSION["role"] == "SUPPLY") ? "<button class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete\" value=\"".$row["ris_no"]."\" onclick=\"delete_control(this.value);\"><i class=\"fa fa-trash\"></i></button>&nbsp;" : "")."<button class=\"btn btn-xs btn-warning\" value=\"".$row["ris_no"]."\" onclick=\"download_xls(this.value);\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Save as Excel\"><i class=\"fa fa-file-excel-o\"></i></button></center></td>
				</tr>";
		}
	}
}

function get_latest_ris(){
	global $conn;

	$yy_mm = mysqli_real_escape_string($conn, $_POST["yy_mm"]);
	$sql = mysqli_query($conn, "SELECT DISTINCT ris_no FROM tbl_ris WHERE ris_no LIKE '%$yy_mm%' ORDER BY ris_id DESC LIMIT 1");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		echo str_pad(((int)explode("-", $row["ris_no"])[2]) + 1, 4, '0', STR_PAD_LEFT);
	}else{
		echo "0001";
	}
}

function insert_ris(){
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
	$purpose = mysqli_real_escape_string($conn, $_POST["purpose"]);
	$items = $_POST["items"];
	
	$quer1 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$requested_by_id'");
	$quer2 = mysqli_query($connhr, "SELECT d.designation, e.designation_fid FROM tbl_employee AS e, ref_designation AS d WHERE d.designation_id = e.designation_fid AND e.emp_id = '$issued_by_id'");
	$requested_by_designation = mysqli_fetch_assoc($quer1)["designation"];
	$issued_by_designation = mysqli_fetch_assoc($quer2)["designation"];
	for($i = 0; $i < count($items); $i++){
		$po_id = $items[$i][0];
		$reference_no = $items[$i][1];
		$item = $items[$i][2];
		$description = $items[$i][3];
		$category = $items[$i][4];
		$quantity = $items[$i][5];
		$unit = $items[$i][6];
		$cost = $items[$i][7];
		$total = $items[$i][8];
		$stock = $items[$i][9];
		$remarks = $items[$i][10];
		mysqli_query($conn, "INSERT INTO tbl_ris(ris_no,entity_name,fund_cluster,division,office,rcc,item,unit,description,quantity,unit_cost,total,available,quantity_stocks,remarks,reference_no,purpose,requested_by,requested_by_designation,issued_by,issued_by_designation,tbl_ris.date) VALUES ('$ris_no','$entity_name','$fund_cluster','$division','$office','$rcc','$item','$unit','$description','$quantity','$cost','$total','1','$stock','$remarks','$reference_no','$purpose','$requested_by','$requested_by_designation','$issued_by','$issued_by_designation','$date')");
		$query_get_stocks = mysqli_query($conn, "SELECT quantity FROM tbl_po WHERE po_id = '$po_id' AND item_name LIKE '$item'");
		$rstocks = explode(" ", mysqli_fetch_assoc($query_get_stocks)["quantity"]);
		$newrstocks = ((int)$rstocks[0] - (int)$quantity)." ".$rstocks[1];
		mysqli_query($conn, "UPDATE tbl_po SET quantity = '$newrstocks' WHERE po_id = '$po_id' AND item_name LIKE '$item'");
	}
	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." created an RIS No. ".$ris_no;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
}

$call_func = mysqli_real_escape_string($conn, $_POST["call_func"]);
switch($call_func){
	case "insert_ris":
		insert_ris();
		break;
	case "get_ris":
		get_ris();
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
}

?>