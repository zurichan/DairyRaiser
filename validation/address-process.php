<?php

session_start();

date_default_timezone_set('Asia/Manila');

require('../configs/database.php');

require_once '../includes/classes.php';

$api = new MyAPI($main_conn);

/** ADD NEW ADDRESS */
if (isset($_POST['add-address-process'])) {

    $province_id = filter_input(INPUT_POST, 'province', FILTER_SANITIZE_NUMBER_INT);
    $municipality_id = filter_input(INPUT_POST, 'municipality', FILTER_SANITIZE_NUMBER_INT);
    $barangay_id = filter_input(INPUT_POST, 'barangay', FILTER_SANITIZE_NUMBER_INT);
    $postal_code = filter_input(INPUT_POST, 'postal_code', FILTER_SANITIZE_NUMBER_INT);
    $house_number = filter_input(INPUT_POST, 'house_number', FILTER_SANITIZE_SPECIAL_CHARS);
    $landmark = filter_input(INPUT_POST, 'near_landmark', FILTER_SANITIZE_SPECIAL_CHARS);

    $err = 0;
    // $pass = 0;
    // $all_province = $api->Read('province', 'all');

    // foreach($all_province as $province) {
    //     if($province->province_id == $province_id) {
    //         $pass++;
    //         break;
    //     }
    // }
    // $all_municipality = $api->Read('municipality', 'all');
    // foreach($all_municipality as $municipality) {
    //     if($municipality->province_id == $province_id) {
    //         if($municipality->municipality == $municipality_id) {

    //         }
    //         $pass++;
    //         break;
    //     }
    // }
    echo $province_id . '<br>' . $municipality_id . '<br>' . $barangay_id . '<br>';
    (empty($barangay_id)) ? $err++ : NULL;
    (empty($province_id)) ? $err++ : NULL;
    (empty($municipality_id)) ? $err++ : NULL;

    $get_province = $api->Read('province', 'set', "province_id", $province_id);
    $get_municipality = $api->Read('municipality', 'set', "municipality_id", $municipality_id);
    $get_barangay = $api->Read('barangay', 'set', "barangay_id", $barangay_id);
    (empty($get_province)) ? $err++ : NULL;
    (empty($get_municipality)) ? $err++ : NULL;
    (empty($get_barangay)) ? $err++ : NULL;

    $validate_province = $api->Read('province', 'set', "province_id", $province_id);
    $validate_municipality = $api->Read('municipality', 'set', "province_id", $province_id);
    $validate_barangay = $api->Read('barangay', 'set', "municipality_id", $municipality_id);
    (empty($validate_province)) ? $err++ : NULL;
    (empty($validate_municipality)) ? $err++ : NULL;
    (empty($validate_barangay)) ? $err++ : NULL;

    $postal_codeLength = strlen((string)$postal_code);
    ($postal_code < 0) ? $err++ : NULL;
    ($postal_codeLength > 4) ? $err++ : NULL;
    (strlen($house_number) >= 100) ? $err++ : NULL;
    (strlen($landmark) >= 40) ? $err++ : NULL;
    (empty($postal_code)) ? $err++ : NULL;
    (empty($house_number)) ? $err++ : NULL;
    echo $err;
    if ($err == 0) {
        $fetch_province = $api->Read('province', 'set', 'province_id', $province_id);
        $fetch_municipality = $api->Read('municipality', 'set', 'municipality_id', $municipality_id);
        $fetch_barangay = $api->Read('barangay', 'set', 'barangay_id', $barangay_id);

        $province = $fetch_province[0]->province_name;
        $municipality = $fetch_municipality[0]->municipality_name;
        $barangay = $fetch_barangay[0]->barangay_name;

        $complete_address = $house_number . ', ' . $barangay . ', ' . $municipality . ', ' . $province . ', ' . $postal_code;
        $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
        $user_address_rows = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id, true);

        if ($user_address_rows != 0 && $user_address_rows <= 4) {

            $user_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);
            $isDefault = 'yes';
            foreach ($user_address as $address) {
                if ($address->isDefault == 'yes') {
                    $isDefault = 'no';
                    break;
                }
            }

            $api->Create('user_address', [
                'key1' => ['user_id', $_SESSION['users'][0]->user_id],
                'key2' => ['house_number', "'$house_number'"],
                'key3' => ['landmark', "'$landmark'"],
                'key4' => ['province', "'$province'"],
                'key5' => ['municipality', "'$municipality'"],
                'key6' => ['barangay', "'$barangay'"],
                'key7' => ['postalCode', "'$postal_code'"],
                'key8' => ['complete_address', "'$complete_address'"],
                'key9' => ['isDefault', "'$isDefault'"]
            ]);

            $_SESSION['address-message'] = array(
                "title" => 'Successfully Added New Address',
                "body" => '',
                "type" => 'success'
            );
        } else {

            $_SESSION['address-message'] = array(
                "title" => 'You have too many Address',
                "body" => '',
                "type" => 'error'
            );
        }

        $user_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);

        if ($user_address_rows == 0) {

            $api->Create('user_address', [
                'key1' => ['user_id', $_SESSION['users'][0]->user_id],
                'key2' => ['house_number', "'$house_number'"],
                'key3' => ['landmark', "'$landmark'"],
                'key4' => ['province', "'$province'"],
                'key5' => ['municipality', "'$municipality'"],
                'key6' => ['barangay', "'$barangay'"],
                'key7' => ['postalCode', "'$postal_code'"],
                'key8' => ['complete_address', "'$complete_address'"],
                'key9' => ['isDefault', "'yes'"]
            ]);

            $_SESSION['address-message'] = array(
                "title" => 'Successfully Added New Address',
                "body" => '',
                "type" => 'success'
            );
        }
    } else {
        $_SESSION['address-message'] = array(
            "title" => 'Something Went Wrong',
            "body" => 'Invalid Input',
            "type" => 'error'
        );
    }
    header('Location: ../user/account/addresses.php');
    exit();
}


/** MAKE AN ADDRESS DEFAULT */
if (isset($_POST['make_default_address'])) {

    $err_message = '';

    $address = filter_input(INPUT_POST, 'make_default_address', FILTER_SANITIZE_NUMBER_INT);
    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $fetch_address = $api->Read('user_address', 'set', 'address_id', $address);

    $err = 0;
    (empty($address)) ? $err++ : NULL;
    (empty($fetch_address)) ? $err++ : NULL;
    ($fetch_address[0]->isDefault == 'yes') ? $err++ : NULL;
    if ($err == 0) {
        $complete_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);
        foreach ($complete_address as $addresses) {
            if ($addresses->address_id == $address) {
                $api->Update('user_address', 'address_id', [
                    'key1' => ['isDefault', "'yes'"]
                ], $addresses->address_id);
            } else {
                $api->Update('user_address', 'address_id', [
                    'key1' => ['isDefault', "'no'"]
                ], $addresses->address_id);
            }
        }
    } else {
        $err_message = 'Something went wrong or the address is in Default.';
    }

    echo json_encode($err_message);
}

/** REMOVE AN ADDRESS */
if (isset($_POST['remove_address'])) {

    $err_message = '';
    $address = filter_input(INPUT_POST, 'remove_address', FILTER_SANITIZE_NUMBER_INT);
    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $fetch_address = $api->Read('user_address', 'set', 'address_id', $address);

    $err = 0;
    (empty($address)) ? $err++ : NULL;
    (empty($fetch_address)) ? $err++ : NULL;
    ($fetch_address[0]->isDefault == 'yes') ? $err++ : NULL;
    if ($err == 0) {
        $api->Delete('user_address', 'address_id', $address);
    } else {
        $err_message = 'Something went wrong or the address is in Default.';
    }

    echo json_encode($err_message);
}

/** EDIT ADDRESS */
if (isset($_POST['update_address'])) {

    $edit_province_id = filter_input(INPUT_POST, 'edit_province', FILTER_SANITIZE_NUMBER_INT);
    $edit_municipality_id = filter_input(INPUT_POST, 'edit_municipality', FILTER_SANITIZE_NUMBER_INT);
    $edit_barangay_id = filter_input(INPUT_POST, 'edit_barangay', FILTER_SANITIZE_NUMBER_INT);
    $edit_postal_code = filter_input(INPUT_POST, 'edit_postal_code', FILTER_SANITIZE_NUMBER_INT);
    $edit_house_number = filter_input(INPUT_POST, 'edit_house_number', FILTER_SANITIZE_SPECIAL_CHARS);
    $edit_landmark = filter_input(INPUT_POST, 'edit_near_landmark', FILTER_SANITIZE_SPECIAL_CHARS);
    $address_id = filter_input(INPUT_POST, 'address_id', FILTER_SANITIZE_NUMBER_INT);

    $update_address_error = 0;
    $location_succes = 0;

    (empty($edit_barangay_id)) ? $update_address_error++ : NULL;
    (empty($edit_province_id)) ? $update_address_error++ : NULL;
    (empty($edit_municipality_id)) ? $update_address_error++ : NULL;

    $all_province = $api->Read('province', 'all');
    $province;

    foreach ($all_province as $provinces) {

        if ($provinces->province_id == $edit_province_id) {
            $province = $provinces->province_name;
            $location_succes++;
            break;
        }
    }

    $all_municipality = $api->Read('municipality', 'set', 'province_id', $edit_province_id);
    $municipality;

    (empty($all_municipality)) ? $add_address_error++ : NULL;

    foreach ($all_municipality as $municipalities) {

        if ($municipalities->municipality_id == $edit_municipality_id) {
            $municipality = $municipalities->municipality_name;
            $location_succes++;
            break;
        }
    }

    $all_barangay = $api->Read('barangay', 'set', 'municipality_id', $edit_municipality_id);
    $barangay;

    (empty($all_barangay)) ? $update_address_error++ : NULL;

    foreach ($all_barangay as $barangays) {

        if ($barangays->barangay_id == $edit_barangay_id) {
            $barangay = $barangays->barangay_name;
            $location_succes++;
            break;
        }
    }

    $postal_codeLength = strlen((string)$edit_postal_code);

    ($edit_postal_code < 0) ? $update_address_error++ : NULL;
    ($postal_codeLength > 4) ? $update_address_error++ : NULL;
    (strlen($edit_house_number) >= 100) ? $update_address_error++ : NULL;
    (strlen($edit_landmark) >= 40) ? $update_address_error++ : NULL;
    (empty($edit_postal_code)) ? $update_address_error++ : NULL;
    (empty($edit_house_number)) ? $update_address_error++ : NULL;

    $complete_address = $edit_house_number . ', ' . $barangay . ', ' . $municipality . ', ' . $province . ', ' . $edit_postal_code;

    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id, true);

    if ($update_address_error == 0 && $location_succes == 3) {

        $api->Update('user_address', 'address_id', [
            'key1' => ['house_number', "'" . $edit_house_number . "'"],
            'key2' => ['landmark', "'" . $edit_landmark . "'"],
            'key3' => ['province', "'" . $province . "'"],
            'key4' => ['municipality', "'" . $municipality . "'"],
            'key5' => ['barangay', "'" . $barangay . "'"],
            'key6' => ['postalCode', $edit_postal_code],
            'key7' => ['complete_address', "'" . $complete_address . "'"]
        ], $address_id);

        $_SESSION['address-message'] = array(
            "title" => 'Your Address has been Updated Address',
            "body" => '',
            "type" => 'success'
        );
    } else {

        $_SESSION['address-message'] = array(
            "title" => 'Invalid Address Input',
            "body" => '',
            "type" => 'error'
        );
    }

    header('Location: ../user/account/addresses.php');
    exit();
}

/** LAOD PROVINCE, MUNICIPALITY, BARANGAY */
if (isset($_POST['select_province'])) {

    $selected_province_message = [];
    $provinces = $api->Read('province', 'all');

    echo json_encode($provinces);
}

if (isset($_POST['select_municipality'])) {

    $selected_municipality_message = [];
    $municipality = $api->Read('municipality', 'set', 'province_id', $_POST['select_municipality']);

    echo json_encode($municipality);
}

if (isset($_POST['select_barangay'])) {

    $selected_barangay_message = [];
    $barangay = $api->Read('barangay', 'set', 'municipality_id', $_POST['select_barangay']);

    echo json_encode($barangay);
}