<?php

require "../../php/php_conn.php";
require "../../php/php_general_functions.php";

session_start();

function print_gatepass() {
    global $conn;

    $table_field = ["ICS" => ["tbl_ics", "ics_id"], "PAR" => ["tbl_par", "par_id"], "PTR" => ["tbl_ptr", "ptr_id"], "RIS" => ["tbl_ris", "ris_id"]];

    $id = mysqli_real_escape_string($conn, $_POST["id"]);
    $sql = mysqli_query($conn, "SELECT * FROM tbl_gatepass WHERE id = '$id'");
    
    if(mysqli_num_rows($sql) != 0) {
        $data = mysqli_fetch_assoc($sql);
        
        $gatepass_id = $data['id'];
        $sql_details = mysqli_query($conn, "SELECT * FROM tbl_gatepass_details WHERE gatepass_id = '$gatepass_id'");

        $items = [];
        if(mysqli_num_rows($sql_details) > 0) {
            while($row = mysqli_fetch_assoc($sql_details)) {
                $issuance_id = $row['issuance_id'];
                $issuance_type = $row['issuance_type'];

                if (array_key_exists($issuance_type, $table_field)) {
                    $table = $table_field[$issuance_type][0];
                    $field = $table_field[$issuance_type][1];

                    $sql_items = mysqli_query($conn, "SELECT * FROM $table WHERE $field = '$issuance_id'");
                    if ($sql_items && mysqli_num_rows($sql_items) > 0) {
                        $issuance_data = mysqli_fetch_assoc($sql_items);
                        $merged_data = array_merge($row, $issuance_data);
                        $items[] = $merged_data;
                    } else {
                        $items[] = ["error" => "No matching issuance found for ID $issuance_id in $table."];
                    }
                } else {
                    $items[] = ["error" => "Invalid issuance type: $issuance_type"];
                }
            }
        }
        $response = [
            'gatepass' => $data,
            'items' => $items
        ];
        echo json_encode($response);

    } else {
        echo json_encode(["error" => "No data found."]);
    }
}

function get_items_issuances(){
    global $conn;

    $table = mysqli_real_escape_string($conn, $_POST["table"]);
    $field = mysqli_real_escape_string($conn, $_POST["field"]);
    $issuances = $_POST["issuances"];

    if (!empty($issuances) && is_array($issuances)) {
        $sanitized_issuances = array_map(function($issuance) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $issuance) . "'";
        }, $issuances);

        $issuances_list = implode(',', $sanitized_issuances);

        $sql = "SELECT * FROM $table WHERE $field IN ($issuances_list)";
        $result = mysqli_query($conn, $sql);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        echo json_encode($data);
    } else {
        echo json_encode(["error" => "No data found."]);
    }
}

function get_issuance_no(){
    global $conn;

    $table = mysqli_real_escape_string($conn, $_POST["table"]);
    $field = mysqli_real_escape_string($conn, $_POST["field"]);
    $id = mysqli_real_escape_string($conn, $_POST["id"]);

    $sql = mysqli_query($conn, "SELECT DISTINCT ".$field." FROM ".$table." ORDER BY ".$id." DESC LIMIT 500");
    $options = "";
    if(mysqli_num_rows($sql) != 0){
        while($row = mysqli_fetch_assoc($sql)){
            $options.="<option value=".$row[$field].">".$row[$field]."</option>";
        }	
    }
    echo $options;

}

function get_employee() {
    global $connhr;
    $sql = mysqli_query($connhr, "
        SELECT e.*, d.designation 
        FROM tbl_employee e 
        LEFT JOIN ref_designation d ON e.designation_fid = d.designation_id 
        WHERE e.status LIKE 'Active' 
        ORDER BY e.fname ASC
    ");
    if (mysqli_num_rows($sql) != 0) {
        while ($row = mysqli_fetch_assoc($sql)) {
            $full_name = (($row["prefix"] != null) ? $row["prefix"] . " " : "") .
                         $row["fname"] . " " .
                         (($row["mname"] != null) ? $row["mname"][0] . ". " : "") .
                         $row["lname"] .
                         (($row["suffix"] != null) ? ", " . $row["suffix"] : "");
            echo "<option value=\"" . $full_name . "|" . $row["designation"] . "\">" . $full_name . "</option>";
        }
    }
}

function get_sources(){
    global $conn;

    $sql = mysqli_query($conn, "SELECT DISTINCT authorized_personnel FROM tbl_gatepass");
    $authorized_personnel = [];
    if(mysqli_num_rows($sql) != 0){
        while($row = mysqli_fetch_assoc($sql)){
            $authorized_personnel[] = $row["authorized_personnel"];
        }
    }

    $sql = mysqli_query($conn, "SELECT DISTINCT plate_number FROM tbl_gatepass");
    $plate_number = [];
    if(mysqli_num_rows($sql) != 0){
        while($row = mysqli_fetch_assoc($sql)){
            $plate_number[] = $row["plate_number"];
        }
    }

    $sql = mysqli_query($conn, "SELECT DISTINCT driver FROM tbl_gatepass");
    $driver = [];
    if(mysqli_num_rows($sql) != 0){
        while($row = mysqli_fetch_assoc($sql)){
            $driver[] = $row["driver"];
        }
    }

    $sql = mysqli_query($conn, "SELECT DISTINCT vehicle_type FROM tbl_gatepass");
    $vehicle_type = [];
    if(mysqli_num_rows($sql) != 0){
        while($row = mysqli_fetch_assoc($sql)){
            $vehicle_type[] = $row["vehicle_type"];
        }
    }

    echo json_encode(["authorized_personnel" => $authorized_personnel, "driver" => $driver, "vehicle_type" => $vehicle_type, "plate_number" => $plate_number]);
}

function get_latest_gatepass(){
	global $conn;

	$yy_mm = date('Y-m');
	$sql = mysqli_query($conn, "SELECT DISTINCT control_number FROM tbl_gatepass WHERE control_number LIKE '%$yy_mm%' ORDER BY id DESC LIMIT 1");
	if(mysqli_num_rows($sql) != 0){
		$row = mysqli_fetch_assoc($sql);
		echo str_pad(((int)end(explode("-", $row["control_number"]))) + 1, 3, '0', STR_PAD_LEFT);
	}else{
		echo "001";
	}
}

function delete_gatepass(){
    global $conn;

    $id = mysqli_real_escape_string($conn, $_POST["id"]);
    mysqli_query($conn, "DELETE FROM tbl_gatepass WHERE id = '$id'");
    mysqli_query($conn, "DELETE FROM tbl_gatepass_details WHERE gatepass_id = '$id'");

    $emp_id = $_SESSION["emp_id"];
    $description = $_SESSION["username"]." deleted a Gatepass ID - ".$id;
    mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
}

function update_gatepass() {
    global $conn;

    $gatepass_id = mysqli_real_escape_string($conn, $_POST['gatepass_id']);
    $control_number = mysqli_real_escape_string($conn, $_POST['control_number']);
    $authorized_personnel = mysqli_real_escape_string($conn, $_POST['authorized_personnel']);
    $driver = mysqli_real_escape_string($conn, $_POST['driver']);
    $plate_number = mysqli_real_escape_string($conn, $_POST['plate_number']);
    $vehicle_type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $checked_by = mysqli_real_escape_string($conn, $_POST['checked_by']);
    $approved_by = mysqli_real_escape_string($conn, $_POST['approved_by']);

    $sql = "UPDATE tbl_gatepass SET 
        control_number = '$control_number',
        authorized_personnel = '$authorized_personnel',
        driver = '$driver',
        plate_number = '$plate_number',
        vehicle_type = '$vehicle_type',
        checked_by = '$checked_by',
        approved_by = '$approved_by'
        WHERE id = $gatepass_id";
    mysqli_query($conn, $sql);

    $statuses = $_POST['status'];
    $issuance_ids = $_POST['issuance_id'];
    $issuance_no = $_POST['issuance_no'];
    $programs = $_POST['program'];
    $purposes = $_POST['purpose'];

    $retained_ids = [];

    for ($i = 0; $i < count($statuses); $i++) {
        $status = mysqli_real_escape_string($conn, $statuses[$i]);
        $issuance_id = intval($issuance_ids[$i]);
        $issuance_data = explode("#", $issuance_no[$i]);
        $issuance_type = $issuance_data[0];
        $issuance_number = $issuance_data[1];
        $program = mysqli_real_escape_string($conn, $programs[$i]);
        $purpose = mysqli_real_escape_string($conn, $purposes[$i]);

        if ($status === 'old') {
            $sql = "UPDATE tbl_gatepass_details SET 
                issuance_program = '$program',
                issuance_purpose = '$purpose'
                WHERE id = $issuance_id AND gatepass_id = $gatepass_id";
            mysqli_query($conn, $sql);
            $retained_ids[] = $issuance_id;

        } else{
            $sql = "INSERT INTO tbl_gatepass_details (gatepass_id, issuance_id, issuance_type, issuance_number, issuance_program, issuance_purpose) 
                VALUES ($gatepass_id, $issuance_id, '$issuance_type', '$issuance_number', '$program', '$purpose')";
            mysqli_query($conn, $sql);
            $new_id = mysqli_insert_id($conn);
            $retained_ids[] = $new_id;
        }
    }
    
    if (!empty($retained_ids)) {
        $ids_str = implode(',', array_map('intval', $retained_ids));
        $sql = "DELETE FROM tbl_gatepass_details 
                WHERE gatepass_id = $gatepass_id AND id NOT IN ($ids_str)";
        mysqli_query($conn, $sql);
    } else {
        $sql = "DELETE FROM tbl_gatepass_details WHERE gatepass_id = $gatepass_id";
        mysqli_query($conn, $sql);
    }
    $emp_id = $_SESSION["emp_id"];
    $description = $_SESSION["username"]." updated a Gatepass No - ".$control_number;
    mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");

    echo json_encode(['status' => 'success']);
}


function insert_gatepass() {
    global $conn;

    // Escape and sanitize non-array POST data
    $control_number = mysqli_real_escape_string($conn, $_POST['control_number']);
    $authorized_personnel = mysqli_real_escape_string($conn, $_POST['authorized_personnel']);
    $driver = mysqli_real_escape_string($conn, $_POST['driver']);
    $plate_number = mysqli_real_escape_string($conn, $_POST['plate_number']);
    $vehicle_type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $checked_by = mysqli_real_escape_string($conn, $_POST['checked_by']);
    $approved_by = mysqli_real_escape_string($conn, $_POST['approved_by']);

    $issuance_id = $_POST['issuance_id'];
    $issuance_no = $_POST['issuance_no'];
    $program = $_POST['program'];
    $purpose = $_POST['purpose'];

    $sql = "INSERT INTO tbl_gatepass (control_number, authorized_personnel, driver, plate_number, vehicle_type, checked_by, approved_by) 
            VALUES ('$control_number', '$authorized_personnel', '$driver', '$plate_number', '$vehicle_type', '$checked_by', '$approved_by')";

    if (mysqli_query($conn, $sql)) {
        $gatepass_id = mysqli_insert_id($conn);

        for ($i = 0; $i < count($issuance_id); $i++) {
            $issuance_data = explode("#", $issuance_no[$i]);
            $issuance_type = $issuance_data[0];
            $issuance_number = $issuance_data[1];

            $details_sql = "INSERT INTO tbl_gatepass_details 
                            (gatepass_id, issuance_id, issuance_type, issuance_number, issuance_program, issuance_purpose) 
                            VALUES ('$gatepass_id', 
                                    '".mysqli_real_escape_string($conn, $issuance_id[$i])."', 
                                    '".mysqli_real_escape_string($conn, $issuance_type)."', 
                                    '".mysqli_real_escape_string($conn, $issuance_number)."', 
                                    '".mysqli_real_escape_string($conn, $program[$i])."', 
                                    '".mysqli_real_escape_string($conn, $purpose[$i])."')";

            if (!mysqli_query($conn, $details_sql)) {
                echo json_encode(["error" => mysqli_error($conn)]);
            }
        }
        $emp_id = $_SESSION["emp_id"];
        $description = $_SESSION["username"]." created a Gatepass No - ".$control_number;
        mysqli_query($conn, "INSERT INTO tbl_logs(emp_id,description) VALUES('$emp_id','$description')");
        echo json_encode(["success" => "Data inserted successfully."]);
    } else {
        echo json_encode(["error" => mysqli_error($conn)]);
    }
}


function get_gatepass() {
    global $conn;

    if (!$conn) {
        echo "<table class=\"table table-bordered\">
                <tr><th style=\"border: 2px solid black;\" colspan=\"11\">Not connected to the server.</th></tr>
              </table>";
        return;
    }

    $limit = 10;
    $page = max(1, (int)$_POST["page"]);
    $start = ($page - 1) * $limit;
    $search = mysqli_real_escape_string($conn, $_POST["search"]);

    $base_query = "
        SELECT g.*, 
               GROUP_CONCAT(CONCAT(d.issuance_type, '#', d.issuance_number) SEPARATOR ', ') AS issuance_str
        FROM tbl_gatepass AS g
        LEFT JOIN tbl_gatepass_details AS d ON g.id = d.gatepass_id
    ";

    if ($search != "") {
        $base_query .= "
            WHERE g.control_number LIKE '%$search%' 
               OR g.authorized_personnel LIKE '%$search%' 
               OR g.plate_number LIKE '%$search%' 
               OR g.driver LIKE '%$search%' 
               OR g.vehicle_type LIKE '%$search%' 
               OR g.checked_by LIKE '%$search%' 
               OR d.issuance_number LIKE '%$search%'
        ";
    }

    $sql_orig = mysqli_query($conn, $base_query . " GROUP BY g.id");
    $total_data = mysqli_num_rows($sql_orig);

    $query = $base_query . " GROUP BY g.id ORDER BY g.id DESC LIMIT $start, $limit";
    $sql = mysqli_query($conn, $query);

    $tbody = "";
    if ($total_data > 0) {
        while ($row = mysqli_fetch_assoc($sql)) {
            $tbody .= "<tr>
                <td>{$row['id']}</td>
                <td>{$row['control_number']}</td>
                <td style=\"font-size: 10px;\">{$row['issuance_str']}</td>
                <td>{$row['authorized_personnel']}</td>
                <td>{$row['plate_number']}</td>
                <td>{$row['driver']}</td>
                <td>{$row['vehicle_type']}</td>
                <td>" . explode("|", $row['checked_by'])[0] . "</td>
                <td>" . explode("|", $row['approved_by'])[0] . "</td>
                <td>{$row['created_at']}</td>
                <td>
                    <center>
                        <button id=\"{$row['id']}\" class=\"btn btn-xs btn-info dim\" data-toggle=\"tooltip\" title=\"Print\" onclick=\"print_gatepass(this.id);\">
                            <i class=\"fa fa-print\"></i>
                        </button>
                        <button id=\"{$row['id']}\" class=\"btn btn-xs btn-warning dim\" data-toggle=\"tooltip\" title=\"Edit\" onclick=\"edit_gatepass(this.id);\">
                            <i class=\"fa fa-edit\"></i>
                        </button>
                        <button id=\"{$row['id']}\" class=\"btn btn-xs btn-danger dim\" data-toggle=\"tooltip\" title=\"Delete\" onclick=\"delete_gatepass(this.id);\">
                            <i class=\"fa fa-trash\"></i>
                        </button>
                    </center>
                </td>
            </tr>";
        }
    } else {
        $tbody = "<tr><td colspan=\"11\" style=\"text-align: center;\">No data found.</td></tr>";
    }

    $pagination = create_table_pagination(
        $page,
        $limit,
        $total_data,
        ["ID", "Control#", "Issuance Numbers", "Authorized Personnel", "Plate#", "Driver", "Vehicle Type", "Checked by", "Approved by", "Date Created", ""]
    );

    echo $pagination[0] . $tbody . $pagination[1];
}



$call_func = mysqli_real_escape_string($conn, $_POST["call_func"]);
if ($call_func === "get_records") {
    get_gatepass();
}elseif($call_func === "get_latest_gatepass"){
    get_latest_gatepass();
}elseif($call_func === "get_issuance_no"){
    get_issuance_no();
}elseif($call_func === "get_items_issuances"){
    get_items_issuances();
}elseif($call_func === "insert_gatepass"){
    insert_gatepass();
}elseif($call_func === "get_employee"){
    get_employee();
}elseif($call_func === "print_gatepass"){
    print_gatepass();
}elseif($call_func === "delete_gatepass"){
    delete_gatepass();
}elseif($call_func === "get_sources"){
    get_sources();
}elseif($call_func === "update_gatepass"){
    update_gatepass();
}

?>
