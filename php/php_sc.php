<?php

require "php_conn.php";
require "php_general_functions.php";

function get_ics_par(){
	global $conn;

	$tbody = "";

	$sql = mysqli_query($conn, "SELECT item, description, category, property_no, serial_no, quantity, cost, received_by, SUBSTRING(date_released, 1, 10) AS drdr FROM tbl_ics WHERE category LIKE 'ICT Equipments' AND date_released BETWEEN '2023-01-01' AND '2023-01-11'");
	while($row = mysqli_fetch_assoc($sql)){
		$tbody.="<tr style=\"font-size: 8px;\">
					<td><b>".$row["item"]."</b>-".$row["description"]."</td>
					<td>'".(($row["property_no"] == "") ? "N/A" : str_replace(",", "<br>'", $row["property_no"]))."</td>
					<td>'".(($row["serial_no"] == "") ? "N/A" : str_replace(",", "<br>'", $row["serial_no"]))."</td>
					<td>".$row["quantity"]."</td>
					<td>".$row["cost"]."</td>
					<td>".$row["received_by"]."</td>
					<td>".$row["drdr"]."</td>
				</tr>";
	}
	echo $tbody;
}

function get_idr(){
	global $conn;

	$from = mysqli_real_escape_string($conn, $_POST["from"]);
	$to = mysqli_real_escape_string($conn, $_POST["to"]);
	$end = false;
	$tbody = "";

	$sub_total = 0.00; $grand_total = 0.00;

	$months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	$dates = array();

	while(!$end) {
		array_push($dates, $from);
		if($from == $to) {
			$end = true;
			break;
		}
		$from_arr = explode("-", $from);
		if(((int)$from_arr[1]) < 12) {
			$from = $from_arr[0]."-".str_pad(((int)$from_arr[1]) + 1, 2, '0', STR_PAD_LEFT);
		}else{
			$from = (((int)$from_arr[0]) + 1 )."-01";
		}
	}
	foreach ($dates as $date) {
		$sql = mysqli_query($conn, "SELECT p.date_delivered, p.end_user, p.po_number, p.sn_ln, p.exp_date, p.quantity, s.supplier, p.item_name, p.description, p.main_stocks, p.unit_cost FROM tbl_po AS p, ref_supplier AS s WHERE p.supplier_id = s.supplier_id AND p.date_delivered LIKE '%$date%' AND p.status = 'Delivered' ORDER BY p.date_delivered ASC");
		while($row = mysqli_fetch_assoc($sql)){
			$unit_name = (explode(" ", $row["quantity"]))[1];
			$sn_ln = str_replace("|", "<br>", $row["sn_ln"]);
			$tbody.="<tr>
				      <td style=\"width: 32.4px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
				      <td style=\"width: 72.6px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["date_delivered"]."</td>
				      <td style=\"width: 59.4px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["end_user"]."</td>
				      <td style=\"width: 105px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["po_number"]."</td>
				      <td style=\"width: 96.6px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["supplier"]."</td>
				      <td style=\"width: 244.6px; text-align: left; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".(strpos(strtoupper($row["description"]), strtoupper($row["item_name"])) !== false ? "<b>".$row["description"]."</b>" :"<b>".$row["item_name"]."</b> - ".$row["description"])."</td>
				      <td style=\"width: 89.4px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$sn_ln."</td>
				      <td style=\"width: 89.4px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".($row["exp_date"] == "0000-00-00" ? "" : $row["exp_date"])."</td>
				      <td style=\"width: 69.4px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["main_stocks"]."</td>
				      <td style=\"width: 69.4px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$unit_name."</td>
				      <td style=\"width: 79.4px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$row["unit_cost"], 2)."</td>
				      <td style=\"width: 99.4px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$row["main_stocks"] * (float)$row["unit_cost"], 2)."</td>
				    </tr>";
				    $sub_total+=((float)$row["main_stocks"] * (float)$row["unit_cost"]);
		}
		$date_arr = explode("-", $date);
		$tbody.="<tr>
				      <td colspan=\"2\" style=\"width: 32.4px; height: 18px; text-align: center; font-size: 9px; font-weight: bold; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-bottom-width: 1px; border-left-width: 1px; border-bottom-style: solid; border-left-style: solid; background-color: rgb(255, 255, 0);border-right-color: rgb(0, 0, 0);border-right-width: 1px;border-right-style: solid;\">".strtoupper($months[(int)$date_arr[1] - 1])." ".$date_arr[0]."</td>
				      <td style=\"width: 59.4px; height: 18px; text-align: center; font-size: 9px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
				      <td style=\"width: 105px; height: 18px; text-align: center; font-size: 9px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
				      <td style=\"width: 96.6px; height: 18px; text-align: center; font-size: 9px; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
				      <td colspan=\"6\" style=\"width: 294.6px; height: 18px; text-align: right; font-size: 9px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid; background-color: rgb(255, 255, 0);\">SUB - TOTAL P</td>
				      <td style=\"width: 119.4px; height: 18px; text-align: center; font-size: 9px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid; background-color: rgb(255, 255, 0);\">".number_format($sub_total, 2)."</td>
				    </tr>";
				    $grand_total+=$sub_total;
				    $sub_total = 0.00;
	}
	echo json_encode(array("tbody"=>$tbody, "grand_total"=>number_format($grand_total,2)));
}

function get_rpci(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT category, CASE WHEN category = 'Drugs and Medicines' THEN 1 WHEN category = 'Medical Supplies' THEN 2 ELSE 3 END AS category_order FROM ref_category ORDER BY category_order ASC");
	$tbody = "";
	$grand_total = 0.00;
	while($row = mysqli_fetch_assoc($sql)){
		$category = $row["category"];
		$tbody.="<tr>
          <td colspan=\"11\" style=\"width: 54.6px; height: 19.5px; text-align: center; font-size: 11px; font-weight: bold; vertical-align: bottom; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid; background-color: rgb(255, 255, 0);\">".strtoupper($category)."</td>
        </tr>";
		$sub_total = 0.00;
		$sql2 = mysqli_query($conn, "SELECT p.po_id, p.end_user, p.po_number, p.item_name, s.supplier, p.date_delivered, p.description, p.quantity, p.unit_cost FROM tbl_po AS p, ref_supplier AS s WHERE p.supplier_id = s.supplier_id AND p.category = '$category' AND (p.status = 'Delivered' OR p.status = '') ORDER BY p.end_user ASC");
		if(mysqli_num_rows($sql2) != 0){
			while($row2 = mysqli_fetch_assoc($sql2)){
				$quantity_unit = explode(" ", $row2["quantity"]);
				$total_amount = (float)$quantity_unit[0] * (float)$row2["unit_cost"];
				$sub_total+=$total_amount;
				if($quantity_unit[0] != "0"){
					$qi = "q".$row2["po_id"];
					$vi = "v".$row2["po_id"];
					$tbody.="<tr>
				          <td style=\"width: 54.6px; height: 15px; font-size: 11px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-left-width: 2px; border-right-style: solid; border-left-style: solid;border-bottom-width: 1px;border-bottom-color: rgb(0, 0, 0);border-bottom-style: solid;\"></td>
				          <td style=\"width: 258.6px; height: 15px; text-align: left; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"><b>".$row2["item_name"]."</b> - ".$row2["description"]."</td>
				          <td style=\"width: 56.4px; height: 15px; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
				          <td style=\"width: 64.2px; height: 15px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$quantity_unit[1]."</td>
				          <td style=\"width: 64.2px; height: 15px; text-align: right; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$row2["unit_cost"],2)."</td>
				          <td style=\"width: 94.2px; height: 15px; text-align: right; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$total_amount,2)."</td>
				          <td style=\"width: 62.4px; height: 15px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format($quantity_unit[0],0)."</td>
				          <td style=\"width: 57.6px; height: 15px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"><input type=\"number\" onchange=\"this.setAttribute('value', this.value)\" style=\"border: none transparent;outline: none; text-align: center;\" onblur=\"compute_shortage('".$quantity_unit[0]."','".$row2["unit_cost"]."',this.value,'".$qi."','".$vi."')\"></td>
				          <td style=\"width: 44.4px; height: 15px; font-size: 9px; vertical-align: center; text-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"><span id=\"".$qi."\"></span></td>
				          <td style=\"width: 48px; height: 15px; font-size: 9px; vertical-align: center; text-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"><span id=\"".$vi."\"></span></td>
				          <td style=\"width: 84px; height: 15px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
				        </tr>";
				}
			}
		}else{
			$tbody.="<tr>
				          <td style=\"width: 54.6px; height: 15px; font-size: 11px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-left-width: 2px; border-right-style: solid; border-left-style: solid;border-bottom-width: 1px;border-bottom-color: rgb(0, 0, 0);border-bottom-style: solid;\">-</td>
				          <td style=\"width: 258.6px; height: 15px; text-align: left; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
				          <td style=\"width: 56.4px; height: 15px; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
				          <td style=\"width: 64.2px; height: 15px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
				          <td style=\"width: 64.2px; height: 15px; text-align: right; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
				          <td style=\"width: 94.2px; height: 15px; text-align: right; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
				          <td style=\"width: 62.4px; height: 15px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
				          <td style=\"width: 57.6px; height: 15px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
				          <td style=\"width: 44.4px; height: 15px; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
				          <td style=\"width: 48px; height: 15px; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
				          <td style=\"width: 84px; height: 15px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
				        </tr>";
		}
		$tbody.="<tr>
		          <td colspan=\"5\" style=\"width: 54.6px; height: 18.75px; text-align: right; font-size: 10px; font-weight: bold; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid; background-color: rgb(255, 255, 0);\">Sub - Total</td>
		          <td style=\"width: 94.2px; height: 18.75px; text-align: right; font-size: 9px; font-weight: bold; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid; background-color: rgb(255, 255, 0);\">".number_format((float)$sub_total, 2)."</td>
		          <td style=\"width: 62.4px; height: 18.75px; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
		          <td style=\"width: 57.6px; height: 18.75px; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
		          <td style=\"width: 44.4px; height: 18.75px; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
		          <td colspan=\"2\" style=\"width: 48px; height: 18.75px; text-align: center; font-size: 10px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
		        </tr>";
		        $grand_total+=$sub_total;
	}
	echo json_encode(array("tbody"=>$tbody, "grand_total"=>number_format((float)$grand_total, 2))); 
}


function print_wi(){
	global $conn;

	$sql = mysqli_query($conn, "SELECT category, CASE WHEN category = 'Drugs and Medicines' THEN 1 WHEN category = 'Medical Supplies' THEN 2 ELSE 3 END AS category_order FROM ref_category ORDER BY category_order ASC");
	$tbody = "";
	$grand_total = 0.00;
	$filter = mysqli_real_escape_string($conn, $_POST["filter"]);
	while($row = mysqli_fetch_assoc($sql)){
		$category = $row["category"];
		$sub_total = 0.00;
		$sql2 = mysqli_query($conn, "SELECT p.end_user, p.po_number, p.item_name, s.supplier, p.date_delivered, p.description, p.quantity, p.sn_ln, p.exp_date, p.unit_cost FROM tbl_po AS p, ref_supplier AS s WHERE p.end_user LIKE '%$filter%' AND p.supplier_id = s.supplier_id AND p.category = '$category' AND (p.status = 'Delivered' OR p.status = '') ORDER BY p.end_user ASC");
		if(mysqli_num_rows($sql2) != 0){
			while($row2 = mysqli_fetch_assoc($sql2)){
				$quantity_unit = explode(" ", $row2["quantity"]);
				$total_amount = (float)$quantity_unit[0] * (float)$row2["unit_cost"];
				$sn_ln = ($category != "Drugs and Medicines" && $category != "Medical Supplies") ? "" : explode("|", $row2["sn_ln"])[0];
				$exp_date = ($category != "Drugs and Medicines" && $category != "Medical Supplies") ? "" : $row2["exp_date"];
				$sub_total+=$total_amount;
				if($quantity_unit[0] != "0"){
					$tbody.="<tr>
		            <td style=\"width: 61.2px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\">".$row2["end_user"]."</td>
		            <td style=\"width: 56.4px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row2["po_number"]."</td>
		            <td style=\"width: 73.8px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row2["supplier"]."</td>
		            <td style=\"width: 56.4px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row2["date_delivered"]."</td>
		            <td style=\"width: 204.6px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"><b>".$row2["item_name"]."</b> - ".$row2["description"]."</td>
		            <td style=\"width: 59.4px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$sn_ln."</td>
		            <td style=\"width: 53.4px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$exp_date."</td>
		            <td style=\"width: 54px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format($quantity_unit[0],0)."</td>
		            <td style=\"width: 41.4px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$quantity_unit[1]."</td>
		            <td style=\"width: 48.6px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$row2["unit_cost"], 2)."</td>
		            <td style=\"width: 73.8px; text-align: right; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format($total_amount, 2)."</td>
		            <td style=\"width: 51.6px; text-align: center; font-size: 9px; font-weight: bold; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		          </tr>";
				}
			}
		}else{
			$tbody.="<tr>
		            <td style=\"width: 61.2px; text-align: center; font-size: 9px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\">-</td>
		            <td style=\"width: 56.4px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
		            <td style=\"width: 73.8px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
		            <td style=\"width: 56.4px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
		            <td style=\"width: 204.6px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
		            <td style=\"width: 59.4px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
		            <td style=\"width: 53.4px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
		            <td style=\"width: 54px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
		            <td style=\"width: 41.4px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
		            <td style=\"width: 48.6px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
		            <td style=\"width: 73.8px; text-align: right; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
		            <td style=\"width: 51.6px; text-align: center; font-size: 9px; font-weight: bold; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">-</td>
		          </tr>";
		}
		$tbody.="<tr>
		      <td colspan=\"3\" style=\"width: 61.2px; text-align: left; font-size: 8px; font-weight: bold; vertical-align: center; border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-bottom-width: 1px; border-left-width: 1px; border-bottom-style: solid; border-left-style: solid; background-color: rgb(255, 255, 0);\">Category: ".$category."</td>
		      <td style=\"width: 56.4px; text-align: center; font-size: 8px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
		      <td style=\"width: 204.6px; text-align: center; font-size: 8px; font-weight: bold; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
		      <td style=\"width: 59.4px; text-align: center; font-size: 8px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
		      <td style=\"width: 53.4px; text-align: center; font-size: 8px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
		      <td style=\"width: 54px; text-align: center; font-size: 8px; vertical-align: bottom; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
		      <td colspan=\"2\" style=\"width: 41.4px; text-align: center; font-size: 9px; font-weight: bold; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(255, 255, 0);\">SUB - TOTAL</td>
		      <td style=\"width: 73.8px; text-align: right; font-size: 9px; font-weight: bold; border-bottom-color: rgb(0, 0, 0); border-bottom-width: 1px; border-bottom-style: solid; background-color: rgb(255, 255, 0);\">".number_format((float)$sub_total, 2)."</td>
		      <td style=\"width: 51.6px; font-size: 8px; font-weight: bold; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid; background-color: rgb(255, 255, 0);\"></td>
		    </tr>";
		    $grand_total+=$sub_total;
	}
	echo json_encode(array("tbody"=>$tbody, "grand_total"=>number_format((float)$grand_total, 2)));
}

function get_rsmi_details(){
	global $conn;

	$year_month = mysqli_real_escape_string($conn, $_POST["year_month"]);
	mysqli_query($conn, "TRUNCATE tbl_rsmi");
	$tbody = ""; $total_rsmi = 0.00;
	$sql = mysqli_query($conn, "SELECT tbl_ris.date_received,ris_no,item,description,rcc,category,quantity,unit,unit_cost,requested_by,remarks FROM tbl_ris WHERE tbl_ris.date_received LIKE '%".$year_month."%' AND issued = 1");
	while($row = mysqli_fetch_assoc($sql)){
		$date_released = $row["date_received"];
		$ris_no = $row["ris_no"];
		$description = mysqli_real_escape_string($conn, $row["description"]);
		$rcc = $row["rcc"];
		$unit = $row["unit"];
		$quantity = $row["quantity"];
		$category = $row["category"];
		$account_code = get_account_code("RIS", $category, 0);
		$requested_by = $row["requested_by"];
		$unit_cost = $row["unit_cost"];
		mysqli_query($conn, "INSERT INTO tbl_rsmi(date_released,control_no,item,unit,quantity,recipients,unit_cost,account_code,rcc) VALUES('$date_released','$ris_no','$description','$unit','$quantity','$requested_by','$unit_cost','$account_code','$rcc')");
	}
	$sql = mysqli_query($conn, "SELECT SUBSTRING(date_released,1,10) AS date_r,control_no,rcc,account_code,item,unit,quantity,recipients,unit_cost FROM tbl_rsmi ORDER BY date_released ASC");
	while($row = mysqli_fetch_assoc($sql)){
		$tbody .= "<tr>
		      <td style=\"width: 61.8px; height: 16px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\">".$row["date_r"]."</td>
		      <td style=\"width: 63px; height: 16px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["control_no"]."</td>
		      <td style=\"width: 49.8px; height: 16px; text-align: center;font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["rcc"]."</td>
		      <td style=\"width: 48px; height: 18px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["account_code"]."</td>
		      <td style=\"width: 190.2px; height: 16px; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["item"]."</td>
		      <td style=\"width: 52.2px; height: 16px; text-align: center;font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["unit"]."</td>
		      <td style=\"width: 46.8px; height: 16px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["quantity"]."</td>
		      <td style=\"width: 118.8px; height: 16px; text-align: center;font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".$row["recipients"]."</td>
		      <td style=\"width: 103.2px; height: 16px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$row["unit_cost"], 2)."</td>
		      <td style=\"width: 62.4px; height: 16px; text-align: center;font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\">".number_format((float)$row["quantity"] * (float)$row["unit_cost"], 2)."</td>
		    </tr>";
		    $total_rsmi+=((float)$row["quantity"] * (float)$row["unit_cost"]);
	}
	$tbody .= "<tr>
		      <td style=\"width: 61.8px; height: 16px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 2px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid;\"></td>
		      <td style=\"width: 63px; height: 16px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		      <td style=\"width: 49.8px; height: 16px; text-align: center;font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		      <td style=\"width: 48px; height: 18px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		      <td style=\"width: 190.2px; height: 16px; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		      <td style=\"width: 52.2px; height: 16px; text-align: center;font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		      <td style=\"width: 46.8px; height: 16px; text-align: center; font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		      <td style=\"width: 118.8px; height: 16px; text-align: center;font-size: 8px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"></td>
		      <td style=\"width: 103.2px; height: 16px; text-align: center; font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 1px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"><b>TOTAL</b></td>
		      <td style=\"width: 62.4px; height: 16px; text-align: center;font-size: 12px; vertical-align: center; border-right-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-right-width: 2px; border-bottom-width: 1px; border-right-style: solid; border-bottom-style: solid;\"><b>₱ ".number_format((float)$total_rsmi, 2)."</b></td>
		    </tr>";

	echo $tbody;
}

function get_ppe_details(){
    global $conn;

    $year_month = mysqli_real_escape_string($conn, $_POST["year_month"]);
    $tbody = "";

    $query = "
    SELECT 
        date_supply_received AS date_r, reference_no AS ref_no, item, category, ics_no AS par_ptr_reference, quantity AS qty, unit, 
        cost AS unit_cost, (quantity * cost) AS total_cost, received_by, remarks, 'ics' AS type, property_no, 'TYPE-ICS' AS type_rep
    FROM tbl_ics
    WHERE date_supply_received LIKE '%$year_month%' 
      AND issued = 1 AND quantity <> 0

    UNION ALL

    SELECT 
        date_supply_received AS date_r, reference_no AS ref_no, item, category, par_no AS par_ptr_reference, quantity AS qty, unit,
		cost AS unit_cost, (quantity * cost) AS total_cost,received_by, remarks, 'par' AS type, property_no, 'TYPE-PAR' AS type_rep
    FROM tbl_par
    WHERE date_supply_received LIKE '%$year_month%' 
      AND issued = 1 AND quantity <> 0

    UNION ALL

    SELECT 
        date_supply_received AS date_r, reference_no AS ref_no, item, category, ptr_no AS par_ptr_reference, quantity AS qty, unit, 
        cost AS unit_cost, (quantity * cost) AS total_cost, tbl_ptr.to AS received_by, remarks, 'ptr' AS type, property_no, 'TYPE-PTR' AS type_rep
    FROM tbl_ptr
    WHERE date_supply_received LIKE '%$year_month%' 
      AND issued = 1 AND quantity <> 0

    ORDER BY date_r ASC
";

    $result = mysqli_query($conn, $query);

    $ics_total = 0.00;
    $par_total = 0.00;
    $ptr_total = 0.00;
    $overall = 0.00;

    while ($row = mysqli_fetch_assoc($result)) {
        $tbody .= "<tr style='font-size: 12px;'>
            <td style='padding-left: 10px; padding-right: 10px;'>{$row['date_r']}</td>
			<td style='padding-left: 10px; padding-right: 10px; display: none;'>{$row['property_no']} - {$row['type_rep']}</td>
            <td style='padding-left: 10px; padding-right: 10px;'>{$row['ref_no']}</td>
            <td style='padding-left: 10px; padding-right: 10px;'>{$row['item']}</td>
            <td style='padding-left: 10px; padding-right: 10px;'>{$row['par_ptr_reference']}</td>
            <td style='padding-left: 10px; padding-right: 10px;'>{$row['qty']}</td>
            <td style='padding-left: 10px; padding-right: 10px;'>{$row['unit']}</td>
            <td style='padding-left: 10px; padding-right: 10px;'>".number_format((float)$row['unit_cost'], 2)."</td>
            <td style='padding-left: 10px; padding-right: 10px;'>".number_format((float)$row['total_cost'], 2)."</td>
            <td style='padding-left: 10px; padding-right: 10px;'>".
                (((int)$row['unit_cost'] < 15000) ? get_account_code("PTR", $category, 0) : get_account_code("PTR", $category, 1))
            ."</td>
            <td style='padding-left: 10px; padding-right: 10px;'>".(($row['type'] == 'ptr') ? number_format((float)$row['total_cost'], 2) : "")."</td>
            <td style='padding-left: 10px; padding-right: 10px;'>".(($row['type'] == 'par') ? number_format((float)$row['total_cost'], 2) : "")."</td>
            <td style='padding-left: 10px; padding-right: 10px;'>".(($row['type'] == 'ics') ? number_format((float)$row['total_cost'], 2) : "")."</td>
            <td style='padding-left: 10px; padding-right: 10px;'>{$row['received_by']}</td>
            <td style='padding-left: 10px; padding-right: 10px;'>{$row['remarks']}</td>
        </tr>";


        $overall += (float)$row['total_cost'];
        $ics_total += ($row['type'] == 'ics') ? (float)$row['total_cost'] : 0.00;
        $par_total += ($row['type'] == 'par') ? (float)$row['total_cost'] : 0.00;
        $ptr_total += ($row['type'] == 'ptr') ? (float)$row['total_cost'] : 0.00;
    }

    $tbody .= "<tr style='font-size: 12px;'>
        <td colspan='7' style='padding-left: 10px; padding-right: 10px; text-align: right;'><b>Overall Total:</b></td>
        <td style='padding-left: 10px; padding-right: 10px;'><b>".number_format((float)$overall, 2)."</b></td>
        <td></td>
        <td style='padding-left: 10px; padding-right: 10px;'><b>".number_format((float)$ptr_total, 2)."</b></td>
        <td style='padding-left: 10px; padding-right: 10px;'><b>".number_format((float)$par_total, 2)."</b></td>
        <td style='padding-left: 10px; padding-right: 10px;'><b>".number_format((float)$ics_total, 2)."</b></td>
        <td colspan='2'></td>
    </tr>";

    echo $tbody;
}

function get_item(){
	global $conn;

	$category = mysqli_real_escape_string($conn, $_POST["category"]);
	$searchkw = mysqli_real_escape_string($conn, $_POST["searchkw"]);
	$sql = mysqli_query($conn, "SELECT DISTINCT description, item_name, category, po_number FROM tbl_po /*WHERE category = '$category' AND (status LIKE 'Delivered' OR status LIKE '')*/ ORDER BY item_name ASC");
	$list_items = "";
	$num_items = mysqli_num_rows($sql);
	if($num_items != 0){
		while($row = mysqli_fetch_assoc($sql)){
			$list_items.="<ol class=\"dd-list\">
                    <li class=\"dd-item\">
                        <div data-desc=\"".$row["description"]."\" data-ctgry=\"".$row["category"]."\" class=\"dd-handle\"><b>".$row["item_name"]."</b> ➜ ".$row["description"]." (".$row["po_number"].")</div>
                    </li>
                </ol>";
		}
	}
	echo json_encode(array("list_items"=>$list_items, "num_items"=>$num_items));
}

function print_stock_card() {
    global $conn;
    $sc_drugs = "";
    $qty_balance = 0;
    $item_name = mysqli_real_escape_string($conn, $_POST["item_name"]);
    $item_desc = mysqli_real_escape_string($conn, $_POST["item_desc"]);
	$refn = mysqli_real_escape_string($conn, $_POST["refn"]);
    $spec = mysqli_real_escape_string($conn, $_POST["spec"]);
    
    $is_issued 	= ($spec == "") ? "" : " AND issued = '" . $spec . "'";
	$is_po		= ($refn == "") ? "" : " AND p.po_number LIKE '$refn'";
	$is_ref		= ($refn == "") ? "" : " AND reference_no LIKE '$refn'";


    $sql = mysqli_query($conn, "
        SELECT 'IN' AS status, p.date_received AS date, p.main_stocks AS quantity, p.po_number AS reference_no, s.supplier AS office, '' AS remarks 
        FROM tbl_po AS p
        JOIN ref_supplier AS s ON p.supplier_id = s.supplier_id
        WHERE p.item_name LIKE '$item_name' AND p.description LIKE '$item_desc' $is_po
        
        UNION ALL
        
        SELECT 'OUT' AS status, ics.date_released AS date, ics.quantity, CONCAT('ICS#', ics.ics_no) AS reference_no, ics.received_by AS office, ics.issued AS remarks
        FROM tbl_ics AS ics
        WHERE ics.quantity <> 0 AND ics.item LIKE '$item_name' AND ics.description LIKE '$item_desc' $is_issued $is_ref
        
        UNION ALL
        
        SELECT 'OUT' AS status, par.date_released AS date, par.quantity, CONCAT('PAR#', par.par_no) AS reference_no, par.received_by AS office, par.issued AS remarks
        FROM tbl_par AS par
        WHERE par.quantity <> 0 AND  par.item LIKE '$item_name' AND par.description LIKE '$item_desc' $is_issued $is_ref
        
        UNION ALL
        
        SELECT 'OUT' AS status, ris.date AS date, ris.quantity, CONCAT('RIS#', ris.ris_no) AS reference_no, ris.requested_by AS office, ris.issued AS remarks
        FROM tbl_ris AS ris
        WHERE ris.quantity <> 0 AND  ris.item LIKE '$item_name' AND ris.description LIKE '$item_desc' $is_issued $is_ref
        
        UNION ALL
        
        SELECT 'OUT' AS status, ptr.date_released AS date, ptr.quantity, CONCAT('PTR#', ptr.ptr_no) AS reference_no, ptr.to AS office, ptr.issued AS remarks
        FROM tbl_ptr AS ptr
        WHERE ptr.quantity <> 0 AND  ptr.item = '$item_name' AND ptr.description LIKE '$item_desc' $is_issued $is_ref

		ORDER BY date ASC
    ");

    while ($row = mysqli_fetch_assoc($sql)) {
        $date_r = substr($row["date"], 0, 10);
        $quantity = $row["quantity"];
        $reference_no = $row["reference_no"];
        $office = $row["office"];
        $remarks = $row["remarks"];

        $remarks_display = ($remarks === "1") ? "✔️ Reported to Accounting" : "❌";

        if ($row["status"] == "IN") {
            $qty_balance += (int)$quantity;
            $sc_drugs .= "<tr>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'>$date_r</td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'>$reference_no</td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'>$quantity</td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'>$office</td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'>$qty_balance</td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
            </tr>";
        } else {
            $qty_balance -= (int)$quantity;
            $sc_drugs .= "<tr>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'>$date_r</td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'>$reference_no</td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'>$quantity</td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'>$office</td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'>$qty_balance</td>
                <td style='font-size: 12px; text-align: center; border: 1px solid black;'>$remarks_display</td>
            </tr>";
        }
    }

    for ($i = 0; $i < (45 - mysqli_num_rows($sql)); $i++) {
        $sc_drugs .= "<tr>
            <td style='font-size: 12px; text-align: center; border: 1px solid black;'><span style='visibility: hidden;'>LALA</span></td>
            <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
            <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
            <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
            <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
            <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
            <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
            <td style='font-size: 12px; text-align: center; border: 1px solid black;'></td>
        </tr>";
    }

    $option_ref = "<option></option>";
	$sql_ref = mysqli_query($conn, "SELECT DISTINCT po_number FROM tbl_po WHERE item_name LIKE '$item_name' AND description LIKE '$item_desc'");
	while ($row = mysqli_fetch_assoc($sql_ref)) {
		$option_ref .= "<option>" . $row["po_number"] . "</option>";
	}

	echo json_encode(array("sc_drugs" => $sc_drugs, "option_ref" => $option_ref));

}

$call_func = mysqli_real_escape_string($conn, $_POST["call_func"]);
switch($call_func){
	case "print_stock_card":
		print_stock_card();
		break;
	case "get_item":
		get_item();
		break;
	case "get_ppe_details":
		get_ppe_details();	
		break;
	case "get_rsmi_details":
		get_rsmi_details();
		break;
	case "print_wi":
		print_wi();
		break;
	case "get_rpci":
		get_rpci();
		break;
	case "get_idr":
		get_idr();
		break;
	case "get_sc_ref":
		get_sc_ref();
		break;
	case "get_ics_par":
		get_ics_par();
		break;
}

?>