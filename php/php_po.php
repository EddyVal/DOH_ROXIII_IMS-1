<?php

require "php_conn.php";

session_start();

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
	$sql = mysqli_query($conn, "SELECT po_id, item_name, unit_cost, quantity, sn_ln FROM tbl_po WHERE po_number LIKE '$po_number'");
	$tbody = "";
	$tot_amt = 0.00;
	while($row = mysqli_fetch_assoc($sql)){
		$tbody.="<tr>
				<td>".$row["item_name"]."</td>
				<td>".number_format((float)$row["unit_cost"], 2)."</td>
				<td onclick=\"add_quantity('".$row["po_id"]."','".$po_number."');\"><a><u>".$row["quantity"]."</u></a></td>
				<td>".number_format(((float)$row["unit_cost"]) * (float)(explode(" ", $row["quantity"])[0]), 2)."</td>
				<td><center>".(($row["sn_ln"] == null) ? "<button value=\"".$row["po_id"]."\" id=\"".(int)(explode(" ", $row["quantity"])[0])."\" onclick=\"add_sl(this.value, this.id);\" class=\"btn btn-info btn-xs\"><i class=\"fa fa-plus\"></i> Add SN/LN</button>" : "<button class=\"btn btn-xs\" style=\"border-radius: 10px; background-color: #00FF00; color: white; font-weight: bold;\" disabled><i class=\"fa fa-check\"></i></button>")."</center></td>
			</tr>";
			$tot_amt+=((float)$row["unit_cost"]) * (float)(explode(" ", $row["quantity"])[0]);
	}
	echo json_encode(array("tbody"=>$tbody, "tot_amt"=>$tot_amt));
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
	

	$sql = mysqli_query($conn, "SELECT po_id, item_name, unit_cost, quantity, sn_ln FROM tbl_po WHERE po_number LIKE '$po_number'");
	while($row = mysqli_fetch_assoc($sql)){
		echo "<tr>
				<td>".$row["item_name"]."</td>
				<td>".number_format((float)$row["unit_cost"], 2)."</td>
				<td onclick=\"add_quantity('".$row["po_id"]."');\">".$row["quantity"]."</td>
				<td>".number_format(((float)$row["unit_cost"]) * (float)(explode(" ", $row["quantity"])[0]), 2)."</td>
				<td><center>".(($row["sn_ln"] == null) ? "<button value=\"".$row["po_id"]."\" id=\"".(int)(explode(" ", $row["quantity"])[0])."\" onclick=\"add_sl(this.value, this.id);\" class=\"btn btn-info btn-xs\"><i class=\"fa fa-plus\"></i> Add SN/LN</button>" : "<button class=\"btn btn-xs\" style=\"border-radius: 10px; background-color: #00FF00; color: white; font-weight: bold;\" disabled><i class=\"fa fa-check\"></i></button>")."</center></td>
			</tr>";
	}
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

	mysqli_query($conn, "UPDATE tbl_po SET date_received='$edate_received', pr_no='$epr_no', procurement_mode='$eprocurement_mode', delivery_term='$edelivery_term', payment_term='$epayment_term', date_conformed='$edate_conformed', date_delivered='$edate_delivered', status='$estatus', inspection_status='$einspection_status', supplier_id='$esupplier', end_user='$epo_enduser' WHERE po_number LIKE '$epo_number'");

	$emp_id = $_SESSION["emp_id"];
	$description = $_SESSION["username"]." edited the details of PO#".$epo_number;
	mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");

}

function get_po_pic(){
	global $conn;

	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$sql = mysqli_query($conn, "SELECT view_po FROM tbl_po WHERE po_number LIKE '$po_number'");
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

	$sql = mysqli_query($conn, "SELECT i.item_id, i.item, c.category FROM ref_item AS i, ref_category AS c WHERE i.status = '0' AND c.category_id = i.category_id ORDER BY i.item ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			echo "<option data-cat=\"".$row["category"]."\" value=".$row["item_id"]."┼".$row["item"].">".$row["item"]."</option>";
		}
	}
}

function get_end_user(){
	global $connhr;
	$sql = mysqli_query($connhr, "SELECT emp_id, fname, mname, lname, prefix, suffix FROM tbl_employee WHERE status LIKE 'Active' ORDER BY fname ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			$name = (($row["prefix"] != null) ? $row["prefix"]." " : "")."".$row["fname"]." ".$row["mname"][0].". ".$row["lname"]."".(($row["suffix"] != null) ? ", ".$row["suffix"] : "");
			echo "<option data-fn=\"".$name."\" value=\"".$row["emp_id"]."\">".$name."</option>";
		}
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

function get_po(){
	global $conn;
	
	$sql = mysqli_query($conn, "SELECT DISTINCT p.po_number, p.remarks, p.status, p.inspection_status, p.procurement_mode,s.supplier, SUBSTRING(p.date_received, 1, 10) AS date_r, p.date_conformed, p.date_delivered, p.activity_date, p.end_user FROM tbl_po AS p, ref_supplier AS s WHERE p.supplier_id = s.supplier_id ORDER BY po_id ASC");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_assoc($sql)){
			$eu = $row["end_user"];
			echo "<tr>
					<td>".$row["date_r"]."</td>
					<td>".$row["po_number"]."</td>
					<td>".$row["procurement_mode"]."</td>
					<td>".$row["date_conformed"]."</td>
					<td>".$row["date_delivered"]."</td>
					<td>".$row["supplier"]."</td>
					<td>".$row["end_user"]."</td>
					<td>".$row["status"]."</td>
					<td><center>".(($row["inspection_status"] == '0') ? "<button class=\"btn btn-xs btn-danger\" style=\"border-radius: 10px;\" disabled>✖</button>" : "<button class=\"btn btn-xs\" style=\"border-radius: 10px; background-color: #00FF00; color: white; font-weight: bold;\" disabled>✓</button>")."</center></td>
					<td><center><button id=\"".$row["po_number"]."\" class=\"btn btn-xs btn-warning\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"View\" onclick=\"view_po(this.id, '".$eu."')\"><i class=\"fa fa-picture-o\"></i></button>&nbsp;<button id=\"".$row["po_number"]."\" class=\"btn btn-xs btn-info\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Edit\" onclick=\"edit_po_various(this.id)\"><i class=\"fa fa-pencil-square-o\"></i></button>&nbsp;".(($_SESSION["role"] == "SUPPLY") ? "<button id=\"".$row["po_number"]."\" class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Delete\" onclick=\"delete_control(this.id)\"><i class=\"fa fa-trash\"></i></button>" : "")."</center></td></tr>";
			}
	}
}

function edit_po_various(){
	global $conn;
	
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
	$sql = mysqli_query($conn, "SELECT p.po_id, SUBSTRING(p.date_received,1,10) AS dr, p.delivery_term, p.payment_term, p.pr_no, s.supplier, i.item, p.end_user, p.unit_cost, p.quantity, p.date_conformed, p.date_delivered, p.inspection_status, p.status, p.sn_ln, p.po_type, p.procurement_mode FROM tbl_po AS p, ref_supplier AS s, ref_item AS i WHERE p.po_number LIKE '$po_number' AND s.supplier_id = p.supplier_id AND i.item_id = p.item_id");

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
					<td>".$row["item"]."</td>
					<td>".number_format((float)$row["unit_cost"], 2)."</td>
					<td ".(($_SESSION["role"] == "SUPPLY") ? "onclick=\"add_quantity('".$row["po_id"]."', '".$po_number."')\"" : "")."><a><u>".$row["quantity"]."</u></a></td>
					<td>".number_format(((float)$row["unit_cost"]) * (float)(explode(" ", $row["quantity"])[0]), 2)."</td>
					<td><center>".($_SESSION["role"] == "SUPPLY" ? (($row["sn_ln"] == null) ? "<button value=\"".$row["po_id"]."\" id=\"".(int)(explode(" ", $row["quantity"])[0])."\" onclick=\"add_sl(this.value, this.id);\" class=\"btn btn-info btn-xs\"><i class=\"fa fa-plus\"></i> Add SN/LN</button>" : "<button class=\"btn btn-xs\" style=\"border-radius: 10px; background-color: #00FF00; color: white; font-weight: bold;\" disabled><i class=\"fa fa-check\"></i></button>") : "")."</center></td>
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
	$po_number = mysqli_real_escape_string($conn, $_POST["po_number"]);
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
		$item_name = $items[$i][1];
		$description = $items[$i][2];
		$category = $items[$i][3];
		$sn_ln = $items[$i][4];
		$exp_date = $items[$i][5];
		$unit_cost = $items[$i][6];
		$quantity = $items[$i][7];
		$main_stocks = explode(" ", $quantity)[0];
		if(mysqli_query($conn, "INSERT INTO tbl_po(po_number,date_received,procurement_mode,delivery_term,payment_term,pr_no, supplier_id,inspection_status,item_id,item_name,description,category,sn_ln,exp_date,unit_cost,main_stocks,quantity,end_user,date_conformed,date_delivered,status,po_type) VALUES('$po_number','$date_received','$procurement_mode','$delivery_term','$payment_term','$pr_no','$supplier_id','$inspect','$item_id','$item_name','$description','$category','$sn_ln','$exp_date','$unit_cost','$main_stocks','$quantity','$end_user','$date_conformed','$date_delivered','$status','$category')")){
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

$call_func = mysqli_real_escape_string($conn, $_POST["call_func"]);

switch($call_func){
	case "get_po":
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
}

?>