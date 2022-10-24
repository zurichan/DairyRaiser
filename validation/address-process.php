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

    $add_address_error = 0;
    $location_succes = 0;

    (empty($barangay_id)) ? $add_address_error++ : NULL;
    (empty($province_id)) ? $add_address_error++ : NULL;
    (empty($municipality_id)) ? $add_address_error++ : NULL;

    $all_province = $api->Read('province', 'all');
    $province;

    foreach ($all_province as $provinces) {

        if ($provinces->province_id == $province_id) {
            $province = $provinces->province_name;
            $location_succes++;
            break;
        }
    }

    $all_municipality = $api->Read('municipality', 'set', 'province_id', $province_id);
    $municipality;

    (empty($all_municipality)) ? $add_address_error++ : NULL;

    foreach ($all_municipality as $municipalities) {

        if ($municipalities->municipality_id == $municipality_id) {
            $municipality = $municipalities->municipality_name;
            $location_succes++;
            break;
        }
    }

    $all_barangay = $api->Read('barangay', 'set', 'municipality_id', $municipality_id);
    $barangay;

    (empty($all_barangay)) ? $add_address_error++ : NULL;

    foreach ($all_barangay as $barangays) {

        if ($barangays->barangay_id == $barangay_id) {
            $barangay = $barangays->barangay_name;
            $location_succes++;
            break;
        }
    }

    $postal_codeLength = strlen((string)$postal_code);
    ($postal_code < 0) ? $add_address_error++ : NULL;
    ($postal_codeLength > 4) ? $add_address_error++ : NULL;
    (strlen($house_number) >= 100) ? $add_address_error++ : NULL;
    (strlen($landmark) >= 40) ? $add_address_error++ : NULL;
    (empty($postal_code)) ? $add_address_error++ : NULL;
    (empty($house_number)) ? $add_address_error++ : NULL;

    $complete_address = $house_number . ', ' . $barangay . ', ' . $municipality . ', ' . $province . ', ' . $postal_code;
    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_address_rows = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id, true);

    if ($user_address_rows != 0 && $user_address_rows <= 4) {

        if ($add_address_error == 0 && $location_succes == 3) {

            $api->Create('user_address', [
                'key1' => ['user_id', $_SESSION['users'][0]->user_id],
                'key2' => ['house_number', "'$house_number'"],
                'key3' => ['landmark', "'$landmark'"],
                'key4' => ['province', "'$province'"],
                'key5' => ['municipality', "'$municipality'"],
                'key6' => ['barangay', "'$barangay'"],
                'key7' => ['postalCode', "'$postal_code'"],
                'key8' => ['complete_address', "'$complete_address'"],
                'key9' => ['isDefault', "'no'"]
            ]);

            $_SESSION['address-message'] = array(
                "title" => 'Successfully Added New Address',
                "body" => '',
                "type" => 'success'
            );
        } else {

            $_SESSION['add-address-message'] = array(
                "title" => 'Invalid Address Input',
                "body" => '',
                "type" => 'error'
            );

            header('Location: ../user/account/update/add-address.php');
            exit();
        }
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

    header('Location: ../user/account/addresses.php');
    exit();
}


/** MAKE AN ADDRESS DEFAULT */
if (isset($_POST['make_default_address'])) {

    $making_default_error = [];

    $make_default_address = $_POST['make_default_address'];
    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $complete_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $make_default_address_success = 0;

    foreach ($complete_address as $addresses) {

        if ($addresses->complete_address == $make_default_address) {
            $make_default_address_success++;
        }
    }

    if ($make_default_address_success == 1) {

        foreach ($complete_address as $addresses) {

            if ($addresses->complete_address == $make_default_address) {

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

        $making_default_error['default_error'] = 'Something went wrong. Please try again.';
    }

    echo json_encode($making_default_error);
    //header('Location: ../user/account/addresses.php');
}

/** REMOVE AN ADDRESS */

if (isset($_POST['remove_address'])) {

    $making_default_error = [];
    $remove_address = $_POST['remove_address'];
    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $complete_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);

    $remove_address_success = 0;
    $remove_address_error = 0;
    $removing_address;

    foreach ($complete_address as $addresses) {

        if ($addresses->complete_address == $remove_address) {

            $removing_address = $api->Read('user_address', 'set', 'address_id', $addresses->address_id);
            $remove_address_success++;
            break;
        }
    }

    if ($removing_address) {

        if ($removing_address[0]->isDefault == 'yes') {

            $remove_address_error++;
        }
    }

    if ($remove_address_success = 1) {

        if ($remove_address_error == 0) {

            foreach ($complete_address as $addresses) {

                if ($addresses->complete_address == $remove_address) {

                    $api->Delete('user_address', 'address_id', $addresses->address_id);
                }
            }
        } else {
            $making_default_error['default_error'] = 'This is a Default Address.';
        }
    } else {
        $making_default_error['default_error'] = 'Something went wrong. Please try again.';
    }

    echo json_encode($making_default_error);
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
            'key1' => ['house_number', "'".$edit_house_number."'"],
            'key2' => ['landmark', "'".$edit_landmark."'"],
            'key3' => ['province', "'".$province."'"],
            'key4' => ['municipality', "'".$municipality."'"],
            'key5' => ['barangay', "'".$barangay."'"],
            'key6' => ['postalCode', $edit_postal_code],
            'key7' => ['complete_address', "'".$complete_address."'"]
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
