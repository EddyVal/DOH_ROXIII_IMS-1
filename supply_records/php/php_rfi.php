<?php

require "../../php/php_conn.php";
require "../../php/php_general_functions.php";

session_start();

function print_rfi() {
    global $conn;

    $rfi_id = mysqli_real_escape_string($conn, $_POST["id"]);

    $rfi_query = "SELECT * FROM tbl_rfi WHERE id = '$rfi_id'";
    $rfi_result = mysqli_query($conn, $rfi_query);
    $rfi_data = mysqli_fetch_assoc($rfi_result);

    $rfi_details_query = "
        SELECT rd.*, po.item_name, po.description, po.main_stocks, po.quantity 
        FROM tbl_rfi_details rd 
        JOIN tbl_po po ON rd.po_id = po.po_id 
        WHERE rd.rfi_id = '$rfi_id'
    ";
    $rfi_details_result = mysqli_query($conn, $rfi_details_query);

    $rfi_details = [];
    while ($row = mysqli_fetch_assoc($rfi_details_result)) {
        $quan_unit = explode(" ", $row["quantity"]);
        $rfi_details[] = array(
            "item_name" => $row["item_name"],
            "description" => $row["description"],
            "main_stocks" => $row["main_stocks"]." ".$quan_unit[1],
            "rsd_no" => $row["rsd_no"],
            "approved_date" => $row["approved_date"],
            "location" => isset($_SESSION['warehouse_name']) ? $_SESSION['warehouse_name'] : ''
        );
    }

    echo htmlspecialchars(json_encode(array('rfi' => $rfi_data, 'rfi_details' => $rfi_details)), ENT_QUOTES, 'UTF-8');
}

function get_latest_rfi(){
	global $conn;

	$yy_mm = substr(mysqli_real_escape_string($conn, $_POST["yy_mm"]), 0, 4);
	$sql = mysqli_query($conn, "SELECT DISTINCT control_number FROM tbl_rfi WHERE control_number LIKE '%$yy_mm%' ORDER BY id DESC LIMIT 1");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		echo str_pad(((int)end(explode("-", $row["control_number"]))) + 1, 4, '0', STR_PAD_LEFT);
	}else{
		echo "0001";
	}
}

function get_items_po() {
    global $conn;

    $items = [];
    $po_numbers = isset($_POST["po_numbers"]) ? $_POST["po_numbers"] : [];

    if (is_countable($po_numbers)) {
        foreach ($po_numbers as $po_number) {
            $po_number = mysqli_real_escape_string($conn, $po_number);
            $sql = mysqli_query($conn, "SELECT * FROM tbl_po WHERE po_number LIKE '$po_number'");

            if ($sql) {
                while ($row = mysqli_fetch_assoc($sql)) {
                    $quan_unit = explode(" ", $row["quantity"]);
                    $items[] = array(
                        "id" => $row["po_id"],
                        "item_description" => $row['item_name'] . " - " . $row['description'],
                        "quantity_delivered" => $row['main_stocks'] . " " . (isset($quan_unit[1]) ? $quan_unit[1] : ''),
                        "date_delivered" => $row['date_delivered'],
                        "location" => isset($_SESSION['warehouse_name']) ? $_SESSION['warehouse_name'] : ''
                    );
                }
            }
        }
    }
    echo htmlspecialchars(json_encode($items), ENT_QUOTES, 'UTF-8');
}


function get_po_for_rfi(){
    global $conn;

	$sql = mysqli_query($conn, "SELECT DISTINCT po_number FROM tbl_po WHERE status LIKE 'Delivered' ORDER BY po_id DESC");
    $options = "";
    if(mysqli_num_rows($sql) != 0){
        while($row = mysqli_fetch_assoc($sql)){
            $options.="<option value=".$row["po_number"].">".$row["po_number"]."</option>";
        }	
    }
    echo $options;
}

function get_chairperson() {
    global $conn;

    $result = mysqli_query($conn, "SELECT * FROM tbl_inspectorate_members WHERE designation = 'Chairperson'");
    $options = "";
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $options .= "<option value=\"{$row['name']}|{$row['designation']}\">{$row['name']}</option>";
        }
    }
    echo $options;
}



function delete_rfi(){
    global $conn;
}

function update_rfi(){
    global $conn;
}

function insert_rfi() {
    global $conn;

    $control_number = mysqli_real_escape_string($conn, $_POST['control_number']);
    $inspector = mysqli_real_escape_string($conn, $_POST['chairperson']);
    $po_numbers = $_POST['reference_no'];
    $po_ids = $_POST['po_id'];
    $rsd_control_nos = $_POST['rsd_control_no'];
    $approved_dates = $_POST['approved_date'];

    $po_numbers_string = mysqli_real_escape_string($conn, implode('|', $po_numbers));

    $sql_rfi = "INSERT INTO tbl_rfi (control_number, inspector, po_number) VALUES ('$control_number', '$inspector', '$po_numbers_string')";
    mysqli_query($conn, $sql_rfi);
    $rfi_id = mysqli_insert_id($conn);

    for ($i = 0; $i < count($po_ids); $i++) {
        $po_id = mysqli_real_escape_string($conn, $po_ids[$i]);

        $rsd_no = mysqli_real_escape_string($conn, $rsd_control_nos[$i]);
        $approved_date = mysqli_real_escape_string($conn, $approved_dates[$i]);

        $sql_details = "INSERT INTO tbl_rfi_details (rfi_id, po_id, rsd_no, approved_date) VALUES ('$rfi_id', '$po_id', '$rsd_no', '$approved_date')";
        mysqli_query($conn, $sql_details);
    }
}

function get_rfi(){
    global $conn;

    if(!$conn) {
        echo "<table class=\"table table-bordered\">
                <tr><th style=\"border: 2px solid black;\" colspan=\"6\">Not connected to the server.</th></tr>
              </table>";
        return;
    }

    $limit = 10;
    $page = max(1, (int)$_POST["page"]);
    $start = ($page - 1) * $limit;
    $search = mysqli_real_escape_string($conn, $_POST["search"]);
    
    $query = "SELECT * FROM tbl_rfi";
    if($search != "") {
        $query .= " WHERE control_number LIKE '%$search%' OR inspector LIKE '%$search%' OR po_number LIKE '%$search%'";
    }

    $sql_orig = mysqli_query($conn, $query);
    $total_data = mysqli_num_rows($sql_orig);

    $query .= " ORDER BY id DESC LIMIT $start, $limit";
    $sql = mysqli_query($conn, $query);

    $tbody = "";
    if($total_data > 0){
        while($row = mysqli_fetch_assoc($sql)){
            $tbody .= "<tr>
                        <td style=\"border: thin solid black;\">{$row['id']}</td>
                        <td style=\"border: thin solid black;\">{$row['control_number']}</td>
                        <td style=\"border: thin solid black;\">{$row['inspector']}</td>
                        <td style=\"font-size: 10px; border: thin solid black;\">{$row['po_number']}</td>
                        <td style=\"border: thin solid black;\">{$row['created_at']}</td>
                        <td style=\"border: thin solid black;\">
                            <center>
                                <button id=\"{$row['id']}\" class=\"btn btn-xs btn-info\" data-toggle=\"tooltip\" title=\"Edit\" onclick=\"print_rfi(this.id);\">
                                    <i class=\"fa fa-print\"></i>
                                </button>
                                <button id=\"{$row['id']}\" class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"Delete\" onclick=\"delete_rfi(this.id);\">
                                    <i class=\"fa fa-trash\"></i>
                                </button>
                            </center></td>
                    </tr>";
        }
    } else {
        $tbody = "<tr><td colspan=\"6\" style=\"text-align: center;\">No data found.</td></tr>";
    }

    $pagination = create_table_pagination($page, $limit, $total_data, ["RFI ID", "Control Number", "Inspector", "Reference/PO Numbers", "Date Created", ""]);
    echo $pagination[0] . $tbody . $pagination[1];
}


$call_func = mysqli_real_escape_string($conn, $_POST["call_func"]);
if ($call_func === "get_rfi") {
    get_rfi();
}elseif ($call_func === "insert_rfi"){
    insert_rfi();
}elseif ($call_func === "get_chairperson"){
    get_chairperson();
}elseif ($call_func === "get_po_for_rfi"){
    get_po_for_rfi();
}elseif ($call_func === "get_items_po"){
    get_items_po();
}elseif ($call_func === "get_latest_rfi"){
    get_latest_rfi();
}elseif ($call_func === "print_rfi"){
    print_rfi();
}

?>
