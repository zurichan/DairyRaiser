<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../../includes/classes.php';

$api = new MyAPI($main_conn);

$date = date('Y-m-d h:i:s');

/** ADD */ if (isset($_POST['add-matured-buffalo']) && isset($_SESSION['admins'])) {

    $buffalo_name = filter_input(INPUT_POST, 'buffalo_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $buffalo_gender = filter_input(INPUT_POST, 'buffalo_gender', FILTER_SANITIZE_SPECIAL_CHARS);
    $buffalo_weight = filter_input(INPUT_POST, 'buffalo_weight', FILTER_SANITIZE_SPECIAL_CHARS);
    $buffalo_status = filter_input(INPUT_POST, 'health_status', FILTER_SANITIZE_SPECIAL_CHARS);
    $buffalo_birthdate = filter_input(INPUT_POST, 'buffalo_birthdate', FILTER_SANITIZE_SPECIAL_CHARS);
    $buffalo_cycle = filter_input(INPUT_POST, 'lactation_cycle', FILTER_SANITIZE_SPECIAL_CHARS);

    $gender = array('Male', 'Female');
    $health_status = array('Normal', 'Sick');
    $lactations = array('Not_Applicable','N/A', 'Not Pregnant', 'Early Lactation', 'Middle Lactation', 'Late Lactation', 'Dry Period');

    $buffalo_error = 0;

    if (!preg_match("/^[a-zA-Z-' ]*$/", $buffalo_name)) {
        $buffalo_error++;
        echo 'name';
    }

    if (!in_array($buffalo_gender, $gender)) {
        $buffalo_error++;
    }

    if ($buffalo_weight > 1499) {
        $buffalo_error++;
    }

    if (!in_array($buffalo_status, $health_status)) {
        $buffalo_error++;
    }

    if (!in_array($buffalo_cycle, $lactations)) {
        $buffalo_error++;
    }

    if ($buffalo_gender == 'Male') {
        $buffalo_cycle = 'N/A';
    }

    if ($buffalo_error == 0) {

        function checkKeys($api, $randStr)
        {
            $buffalo = $api->Read('buffalos', 'all');
            foreach ($buffalo as $b) {
                if ($b->code == $randStr) {
                    $keyExists = true;
                    break;
                } else {
                    $keyExists = false;
                }
            }
            return $keyExists;
        }

        function generateKeys($api)
        {
            $keyLength = 4;
            $str = "1234567890";
            $randStr = substr(str_shuffle($str), 0, $keyLength);
            $checkKey = checkKeys($api, $randStr);

            while ($checkKey == true) {
                $randStr = substr(str_shuffle($str), 0, $keyLength);
                $checkKey = checkKeys($api, $randStr);
            }

            return $randStr;
        }

        $code = generateKeys($api);

        $api->Create('buffalos', [
            'key1' => ['Name', "'$buffalo_name'"],
            'key2' => ['Gender', "'$buffalo_gender'"],
            'key3' => ['Weight', $buffalo_weight],
            'key4' => ['Health_Status', "'$buffalo_status'"],
            'key5' => ['Birthdate', "'$buffalo_birthdate'"],
            'key6' => ['Lactation_Cycle', "'$buffalo_cycle'"],
            'key7' => ['lastUpdate', "'$date'"],
            'key8' => ['BUffalo_id', $code]
        ]);

        $_SESSION['buffalo-message'] = array(
            "title" => 'Success!',
            "body" => 'Buffalo Added!',
            "type" => 'success'
        );
    } else {
        $_SESSION['buffalo-message'] = array(
            "title" => 'Something went wrong!',
            "body" => 'Error Input Details. Please Try Again.',
            "type" => 'error'
        );
    }
    header('Location: ../Manage_Buffalos/buffalo_list.php?page=all');
    exit();
}

/** UPDATE */ if (isset($_POST['update_buffalo']) && isset($_SESSION['admins'])) {

    $buffalo_name = filter_input(INPUT_POST, 'update_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $buffalo_weight = filter_input(INPUT_POST, 'update_weight', FILTER_SANITIZE_NUMBER_INT);
    $buffalo_status = filter_input(INPUT_POST, 'update_status', FILTER_SANITIZE_SPECIAL_CHARS);
    $buffalo_cycle = filter_input(INPUT_POST, 'update_cycle', FILTER_SANITIZE_SPECIAL_CHARS);
    $buffalo_id = filter_input(INPUT_POST, 'buffalo_id', FILTER_SANITIZE_NUMBER_INT);

    $date = date('Y-m-d h:i:s');
    $gender = array('Male', 'Female');
    $health_status = array('Normal', 'Sick');
    $lactations = array('N/A', 'Not Pregnant', 'Early Lactation', 'Middle Lactation', 'Late Lactation', 'Dry Period');

    $buffalo_error = 0;

    if (!preg_match("/^[a-zA-Z-' ]*$/", $buffalo_name)) {
        $buffalo_error++;
    }

    if ($buffalo_weight > 1499) {
        $buffalo_error++;
    }

    if (!in_array($buffalo_status, $health_status)) {
        $buffalo_error++;
    }

    if (!in_array($buffalo_cycle, $lactations)) {
        $buffalo_error++;
    }

    if ($buffalo_error == 0) {

        $api->Update('buffalos', 'Buffalo_id', [
            'key1' => ['Name', "'$buffalo_name'"],
            'key2' => ['Weight', $buffalo_weight],
            'key3' => ['Health_Status', "'$buffalo_status'"],
            'key4' => ['Lactation_Cycle', "'$buffalo_cycle'"],
            'key5' => ['lastUpdate', "'$date'"]
        ], $buffalo_id);

        $_SESSION['buffalo-message'] = array(
            "title" => 'Success!',
            "body" => 'Buffalo has been Updated!',
            "type" => 'success'
        );
    } else {
        $_SESSION['buffalo-message'] = array(
            "title" => 'Something went wrong!',
            "body" => 'Error Input Details. Please Try Again.',
            "type" => 'error'
        );
    }
    header('Location: ../Manage_Buffalos/buffalo_list.php?page=all');
    exit();
}

/** DELETE BUFFALO */ if (isset($_POST['remove_buffalo']) && isset($_SESSION['admins'])) {

    $buffalo_id = filter_input(INPUT_POST, 'remove_buffalo', FILTER_SANITIZE_NUMBER_INT);
    $position = $_POST['position'];

    $err = 0;

    (empty($buffalo_id)) ? $err++ : NULL;
    (!is_numeric($buffalo_id)) ? $err++ : NULL;

    if ($err == 0) {

        $api->Delete('buffalos', 'Buffalo_id', $buffalo_id);

        if (isset($position) && $position == 'sick') {
            $sick_buffalo = $api->Read('buffalos', 'set', 'Health_Status', 'Sick');
            $index = 1;
            if (empty($sick_buffalo)) {
            } else {
                foreach ($sick_buffalo as $buffalo) {
?>
                    <tr>
                        <td><?= $index; ?></td>
                        <td><?= $buffalo->Name; ?></td>
                        <td data-target="buffalo_id"><?= $buffalo->Buffalo_id; ?></td>
                        <td><?= $buffalo->Gender; ?></td>
                        <td><?= $buffalo->Weight; ?> kg</td>
                        <td><?= $buffalo->Birthdate; ?></td>
                        <td><?= $buffalo->Lactation_Cycle; ?></td>
                        <td><?= $buffalo->lastUpdate; ?></td>
                        <td>
                            <a class=" dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                            <ul class="dropdown-menu dropdown-sm">
                                <li><button type="button" class="dropdown-item" data-btn="marked_healthy_btn">Mark as Healthy</a></li>
                                <li><button type="button" class="dropdown-item" data-btn="remove_sick_btn">Remove</button></li>
                            </ul>
                        </td>
                    </tr>
                    <?php
                    $index++;
                }
            }
        } else if (isset($position) && $position == 'all') {
            $index = 1;
            $buffalos = $api->Read('buffalos', 'all');
            foreach ($buffalos as $buffalo) :
                if ($buffalo->Marked_As !== 'Deceased') :
                    if ($buffalo->Marked_As !== 'Sold') :
                    ?>
                        <tr>
                            <td><?= $index; ?></td>
                            <td data-target="name"><?= $buffalo->Name; ?></td>
                            <td data-target="buffalo_id"><?= $buffalo->Buffalo_id; ?></td>
                            <td data-target="gender"><?= $buffalo->Gender; ?></td>
                            <td data-target="weight"><?= $buffalo->Weight; ?> kg</td>
                            <td data-target="health_status"><?= $buffalo->Health_Status; ?></td>
                            <td><?= $buffalo->Birthdate; ?></td>
                            <td data-target="cycle"><?= $buffalo->Lactation_Cycle; ?></td>
                            <td><?= $buffalo->lastUpdate; ?></td>
                            <td>
                                <a class=" dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                                <ul class="dropdown-menu dropdown-sm">
                                    <li><button type="button" class="dropdown-item" data-btn="update_btn">Update</button></li>
                                    <li><button type="button" class="dropdown-item" data-btn="marked_sick_btn">Marked As Sick</button></li>
                                    <li><button type="button" class="dropdown-item" data-btn="marked_deceased_btn">Marked As Deceased</button></li>
                                    <li><button type="button" class="dropdown-item" data-btn="remove_buffalo_btn">Remove</button></li>
                                </ul>
                            </td>
                        </tr>
                    <?php
                    endif;
                endif;
                $index++;
            endforeach;
        } else if (isset($position) && $position == 'deceased') {
            $deceased_buffalo = $api->Read('buffalos', 'set', 'Marked_As', 'Deceased');
            $index = 1;
            if (empty($deceased_buffalo)) {
            } else {
                foreach ($deceased_buffalo as $buffalo) {
                    ?>
                    <tr>
                        <td><?= $index; ?></td>
                        <td><?= $buffalo->Name; ?></td>
                        <td data-target="buffalo_id"><?= $buffalo->Buffalo_id; ?></td>
                        <td><?= $buffalo->Gender; ?></td>
                        <td><?= $buffalo->Weight; ?> kg</td>
                        <td><?= $buffalo->Birthdate; ?></td>
                        <td><?= $buffalo->Comments; ?></td>
                        <td><?= $buffalo->lastUpdate; ?></td>
                        <td>
                            <a class=" dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                            <ul class="dropdown-menu dropdown-sm">
                                <li><button type="button" class="dropdown-item" data-btn="remove_buffalo_btn">Remove</button></li>
                            </ul>
                        </td>
                    </tr>
                <?php
                    $index++;
                }
            }
        }
    }
}

/** MARKED AS */ if (isset($_POST['marked_buffalo']) && isset($_SESSION['admins'])) {

    $buffalo_id = filter_input(INPUT_POST, 'marked_buffalo', FILTER_SANITIZE_NUMBER_INT);
    $position = $_POST['position'];

    $err = 0;

    (empty($buffalo_id)) ? $err++ : NULL;
    (!is_numeric($buffalo_id)) ? $err++ : NULL;

    if ($err == 0) {

        if (isset($position) && $position == 'sick') {
            $api->Update('buffalos', 'Buffalo_id', [
                '1' => ['Health_Status', "'Normal'"]
            ], $buffalo_id);

            $sick_buffalo = $api->Read('buffalos', 'set', 'Health_Status', 'Sick');

            $index = 1;

            foreach ($sick_buffalo as $buffalo) {
                ?>
                <tr>
                    <td><?= $index; ?></td>
                    <td><?= $buffalo->Name; ?></td>
                    <td data-target="buffalo_id"><?= $buffalo->Buffalo_id; ?></td>
                    <td><?= $buffalo->Gender; ?></td>
                    <td><?= $buffalo->Weight; ?> kg</td>
                    <td><?= $buffalo->Birthdate; ?></td>
                    <td><?= $buffalo->Lactation_Cycle; ?></td>
                    <td><?= $buffalo->lastUpdate; ?></td>
                    <td>
                        <a class=" dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                        <ul class="dropdown-menu dropdown-sm">
                            <li><button type="button" class="dropdown-item" data-btn="marked_healthy_btn">Mark as Healthy</a></li>
                            <li><button type="button" class="dropdown-item" data-btn="remove_sick_btn">Remove</button></li>
                        </ul>
                    </td>
                </tr>
                <?php
                $index++;
            }
        } else if (isset($position) && $position == 'all-sick') {

            $api->Update('buffalos', 'Buffalo_id', [
                '1' => ['Health_Status', "'Sick'"]
            ], $buffalo_id);

            $index = 1;
            $buffalos = $api->Read('buffalos', 'all');
            foreach ($buffalos as $buffalo) :
                if ($buffalo->Marked_As !== 'Deceased') :
                    if ($buffalo->Marked_As !== 'Sold') :
                ?>
                        <tr>
                            <td><?= $index; ?></td>
                            <td data-target="name"><?= $buffalo->Name; ?></td>
                            <td data-target="buffalo_id"><?= $buffalo->Buffalo_id; ?></td>
                            <td data-target="gender"><?= $buffalo->Gender; ?></td>
                            <td data-target="weight"><?= $buffalo->Weight; ?> kg</td>
                            <td data-target="health_status"><?= $buffalo->Health_Status; ?></td>
                            <td><?= $buffalo->Birthdate; ?></td>
                            <td data-target="cycle"><?= $buffalo->Lactation_Cycle; ?></td>
                            <td><?= $buffalo->lastUpdate; ?></td>
                            <td>
                                <a class=" dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                                <ul class="dropdown-menu dropdown-sm">
                                    <li><button type="button" class="dropdown-item" data-btn="update_btn">Update</button></li>
                                    <li><button type="button" class="dropdown-item" data-btn="marked_sick_btn">Marked As Sick</button></li>
                                    <li><button type="button" class="dropdown-item" data-btn="marked_deceased_btn">Marked As Deceased</button></li>
                                    <li><button type="button" class="dropdown-item" data-btn="remove_buffalo_btn">Remove</button></li>
                                </ul>
                            </td>
                        </tr>
            <?php
                    endif;
                endif;
                $index++;
            endforeach;
            ?>
            <?php
        } else if (isset($position) && $position == 'all-deceased') {

            $api->Update('buffalos', 'Buffalo_id', [
                '1' => ['Marked_As', "'Deceased'"]
            ], $buffalo_id);

            $index = 1;
            $buffalos = $api->Read('buffalos', 'all');
            foreach ($buffalos as $buffalo) :
                if ($buffalo->Marked_As !== 'Deceased') :
                    if ($buffalo->Marked_As !== 'Sold') :
            ?>
                        <tr>
                            <td><?= $index; ?></td>
                            <td data-target="name"><?= $buffalo->Name; ?></td>
                            <td data-target="buffalo_id"><?= $buffalo->Buffalo_id; ?></td>
                            <td data-target="gender"><?= $buffalo->Gender; ?></td>
                            <td data-target="weight"><?= $buffalo->Weight; ?> kg</td>
                            <td data-target="health_status"><?= $buffalo->Health_Status; ?></td>
                            <td><?= $buffalo->Birthdate; ?></td>
                            <td data-target="cycle"><?= $buffalo->Lactation_Cycle; ?></td>
                            <td><?= $buffalo->lastUpdate; ?></td>
                            <td>
                                <a class=" dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                                <ul class="dropdown-menu dropdown-sm">
                                    <li><button type="button" class="dropdown-item" data-btn="update_btn">Update</button></li>
                                    <li><button type="button" class="dropdown-item" data-btn="marked_sick_btn">Marked As Sick</button></li>
                                    <li><button type="button" class="dropdown-item" data-btn="marked_deceased_btn">Marked As Deceased</button></li>
                                    <li><button type="button" class="dropdown-item" data-btn="remove_buffalo_btn">Remove</button></li>
                                </ul>
                            </td>
                        </tr>
<?php
                    endif;
                endif;
                $index++;
            endforeach;
        }
    }
}

/** SELL */
if (isset($_POST['sell_buffalo']) && isset($_SESSION['admins'])) {

    $price = filter_input(INPUT_POST, 'buffalo_price', FILTER_SANITIZE_NUMBER_INT);
    $buyer = filter_input(INPUT_POST, 'buyer', FILTER_SANITIZE_SPECIAL_CHARS);
    $comments = filter_input(INPUT_POST, 'buyer_comments', FILTER_SANITIZE_SPECIAL_CHARS);
    $buffalo_id = filter_input(INPUT_POST, 'buffalo_id', FILTER_SANITIZE_NUMBER_INT);

    $error = 0;
    (!preg_match("/^[a-zA-Z-' ]*$/", $comments)) ? $error++ : NULL;
    (!preg_match("/^[a-zA-Z-' ]*$/", $buyer)) ? $error++ : NULL;
    (!is_numeric($price)) ? $error++ : NULL;
    (empty($price)) ? $error++ : NULL;
    (empty($buyer)) ? $error++ : NULL;
    (empty($buffalo_id)) ? $error++ : NULL;

    if ($error == 0) {
        $selected_buffalo = $api->Read('buffalos', 'set', 'Buffalo_id', $buffalo_id, true);
        if ($selected_buffalo >= 1) {

            $api->Update('buffalos', 'Buffalo_id', [
                'key1' => ['Marked_As', "'Sold'"],
                'key2' => ['Price', $price],
                'key3' => ['Buyer', "'$buyer'"],
                'key4' => ['Comments', "'$comments'"]
            ], $buffalo_id);

            $_SESSION['buffalo-message'] = array(
                "title" => 'Buffalo has been Sold!',
                "body" => '',
                "type" => 'success'
            );
        } else {
            $_SESSION['buffalo-message'] = array(
                "title" => 'Something went wrong!',
                "body" => 'Please Try Again.',
                "type" => 'error'
            );
        }
    } else {
        $_SESSION['buffalo-message'] = array(
            "title" => 'Something went wrong!',
            "body" => 'Please Try Again.',
            "type" => 'error'
        );
    }

    header('Location: ../buffalos/buffalo.php');
    exit();
}

/** mf input */ if (isset($_POST['submit_input_mf']) && isset($_SESSION['admins'])) {

    $input_date = preg_replace("([^0-9/])", "", $_POST['mf_date']);
    $input_data = filter_input(INPUT_POST, 'mf_data', FILTER_SANITIZE_NUMBER_INT);
    $TpregBuffalo = filter_input(INPUT_POST, 'mf_pregBuffalo', FILTER_SANITIZE_NUMBER_INT);

    $err = 0;

    $buffalos = $api->Read('buffalos', 'all');
    $lactations = array('Early Lactation', 'Middle Lactation', 'Late Lactation', 'Dry Period');
    $total_pregnant = 0;

    foreach ($buffalos as $buffalo) {
        if ($buffalo->Gender == 'Female' && (in_array($buffalo->Lactation_Cycle, $lactations))) {
            $total_pregnant++;
        }
    }

    (empty($TpregBuffalo)) ? $err++ : NULL;
    (empty($input_date)) ? $err++ : NULL;
    (empty($input_data)) ? $err++ : NULL;

    if ($err == 0 && $total_pregnant != 0) {
        $fetch_buffalo = $api->Read('milk_production', 'all');
        $method = 0;
        foreach ($fetch_buffalo as $value) {
            # code...
            $replaceDate = str_replace('-', '', $value->date);
            if ($replaceDate == $input_date) {

                $method++;
                $fetch_identical = $api->Read('milk_production', 'set', 'mp_id', $value->mp_id);
                $new_total_mf = $fetch_identical[0]->liters + $input_data;
                $new_total_preg = $fetch_identical[0]->total_pregnant + $TpregBuffalo;
                $api->Update('milk_production', 'mp_id', [
                    '1' => ['liters', $new_total_mf],
                    '2' => ['total_pregnant', $new_total_preg],
                    '3' => ['Update_At', "'$date'"]
                ], $value->mp_id);
                break;
            }
        }

        if ($method == 0) {
            $api->Create('milk_production', [
                '1' => ['total_pregnant', $TpregBuffalo],
                '2' => ['liters', $input_data],
                '3' => ['date', "'$input_date'"],
                '4' => ['Update_At', "'$date'"]
            ]);
        }

        $_SESSION['buffalo-message'] = array(
            "title" => 'Success',
            "body" => '',
            "type" => 'success'
        );
    } else {
        $_SESSION['buffalo-message'] = array(
            "title" => 'Something went wrong!',
            "body" => '',
            "type" => 'error'
        );
    }

    header('Location: ../Manage_Buffalos/stocks&yield.php');
    exit();
}

/** Remove Buffalo Data */ if (isset($_POST['submit_removal_mf'])) {

    $dateValue1 = filter_input(INPUT_POST, 'mf_date_1', FILTER_SANITIZE_NUMBER_INT);
    $dateValue2 = filter_input(INPUT_POST, 'mf_date_2', FILTER_SANITIZE_NUMBER_INT);

    $err = 0;

    (empty($dateValue1)) ? $err++ : NULL;
    (empty($dateValue2)) ? $err++ : NULL;
    ($dateValue1 > $dateValue2) ? $err++ : NULL;

    if ($err == 0) {
        $remove_buffalo_data = $api->Delete('milk_production', 'date', ["'$dateValue1'", "'$dateValue2'"], true);
        $_SESSION['buffalo-message'] = array(
            "title" => 'Success',
            "body" => '',
            "type" => 'success'
        );
    } else {
        $_SESSION['buffalo-message'] = array(
            "title" => 'Something went wrong!',
            "body" => '',
            "type" => 'error'
        );
    }

    header('Location: ../Manage_Buffalos/stocks&yield.php');
    exit();
}

/** UPDATE MILK STOCK */ if (isset($_POST['submit_update_ms']) && isset($_SESSION['admins'])) {

    $in_val = filter_input(INPUT_POST, 'increase_ms', FILTER_SANITIZE_NUMBER_INT);
    $out_val = filter_input(INPUT_POST, 'decrease_ms', FILTER_SANITIZE_NUMBER_INT);

    $err = 0;

    (!is_numeric($in_val)) ? $err++ : NULL;
    (!is_numeric($out_val)) ? $err++ : NULL;
    (empty($in_val) && empty($out_val)) ? $err++ : NULL;

    if ($err == 0) {

        $milk_stock = $api->Read('milk_stock', 'set', 'ms_id', 1);
        $current_ms = $milk_stock[0]->milk_stock;

        if (!empty($milk_stock)) {

            $update_err = 0;

            if (!empty($in_val)) {
                $total_in = $current_ms + $in_val;
                $api->Update('milk_stock', 'ms_id', [
                    '1' => ['milk_stock', $total_in],
                    '2' => ['date', "'$date'"]
                ], 1);
            }

            $recurrent_ms = $milk_stock[0]->milk_stock;

            if (!empty($out_val)) {
                if ($out_val <= $recurrent_ms) {
                    $total_out = $current_ms - $out_val;
                    $api->Update('milk_stock', 'ms_id', [
                        '1' => ['milk_stock', $total_out],
                        '2' => ['date', "'$date'"]
                    ], 1);
                } else {
                    $update_err++;
                }
            }

            if ($update_err == 0) {
                $_SESSION['buffalo-message'] = array(
                    "title" => 'Success',
                    "body" => 'Milk Stock has been Updated. (' . $date . ')',
                    "type" => 'success'
                );
            } else {
                $_SESSION['buffalo-message'] = array(
                    "title" => 'Failed',
                    "body" => 'Something Went Wrong. Please Try Again.',
                    "type" => 'error'
                );
            }
        } else {
            $_SESSION['buffalo-message'] = array(
                "title" => 'Failed',
                "body" => 'Something Went Wrong. Please Try Again.',
                "type" => 'error'
            );
        }
    } else {
        $_SESSION['buffalo-message'] = array(
            "title" => 'Failed',
            "body" => 'Something Went Wrong. Please Try Again.',
            "type" => 'error'
        );
    }

    header('Location: ../Manage_Buffalos/stocks&yield.php');
    exit();
}
