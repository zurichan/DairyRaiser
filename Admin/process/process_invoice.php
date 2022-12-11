<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../../includes/classes.php';

$api = new MyAPI($main_conn);

$date = date('Y-m-d h:i:s');

/** Create Buffalo Milk Invoice */ if (isset($_POST['create_bi_invoice_milk']) && isset($_SESSION['admins'])) {
    $client = filter_input(INPUT_POST, 'client_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $remarks = filter_input(INPUT_POST, 'Remarks', FILTER_SANITIZE_SPECIAL_CHARS);
    $discount = filter_input(INPUT_POST, 'discount', FILTER_SANITIZE_NUMBER_INT);
    $price_per_liter = filter_input(INPUT_POST, 'price_per_liter', FILTER_SANITIZE_NUMBER_INT);
    $liter = filter_input(INPUT_POST, 'milk_liter', FILTER_SANITIZE_NUMBER_INT);
    $other_fees = filter_input(INPUT_POST, 'other_fees', FILTER_SANITIZE_NUMBER_INT);

    $subTotal = 0;
    $discountTotal = 0;
    $total = 0;

    $err = 0;

    (empty($client)) ? $err++ : FALSE;
    (empty($liter)) ? $err++ : FALSE;
    (empty($discount)) ? $discount = 0 : FALSE;
    (empty($price_per_liter)) ? $err++ : FALSE;

    if ($err == 0) {

        function checkKeys($api, $randStr)
        {
            $bi_list = $api->Read('bi_list', 'all');
            foreach ($bi_list as $bi) {
                if ($bi->code == $randStr) {
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
            $keyLength = 8;
            $str = "123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $randStr = substr(str_shuffle($str), 0, $keyLength);
            $checkKey = checkKeys($api, $randStr);

            while ($checkKey == true) {
                $randStr = substr(str_shuffle($str), 0, $keyLength);
                $checkKey = checkKeys($api, $randStr);
            }

            return $randStr;
        }

        $code = 'BI-' . generateKeys($api);
        $category = 'milk';
        $item = $price_per_liter;
        $milk_liter = $liter;
        $subTotal = 0;
        $subTotal_otherFees = 0;
        $discountTotal = 0;
        $total = 0;
        $grandTotal = 0;

        (empty($other_fees)) ?  $other_fees = 0 : NULL;
        (empty($price_per_liter)) ?  $price_per_liter = 0 : NULL;

        $total = number_format($liter * $price_per_liter);
        if (empty($discount)) {
            $grandTotal = number_format($total + $other_fees);
        } else {
            $discountTotal = number_format(($discount / 100), 2);
            $grandTotal = number_format((($total + $other_fees) - ($discountTotal * ($total + $other_fees))));
        }

        $subTotal = number_format($total);
        $subTotal_otherFees = number_format($other_fees +  $total);

        $api->create('bi_list', [
            '1' => ['code', "'$code'"],
            '2' => ['client', "'$client'"],
            '3' => ['category', "'$category'"],
            '4' => ['items', $item],
            '11' => ['milk_liter', $milk_liter],
            '5' => ['other_fees', $other_fees],
            '6' => ['discount', $discount],
            '7' => ['subTotal', $subTotal],
            '8' => ['amount', $grandTotal],
            '9' => ['remarks', "'$remarks'"],
            '10' => ['date', "'$date'"]
        ]);

        $_SESSION['buffalo-message'] = array(
            "title" => 'New Invoices Added',
            "body" => 'Successfully create a new Invoice record.',
            "type" => 'success'
        );

        header('Location: ../Manage_Buffalos/invoice_list.php?page=all&bi_code=none');
        exit();
    } else {
        $_SESSION['buffalo-message'] = array(
            "title" => 'Something went wrong',
            "body" => '',
            "type" => 'error'
        );

        header('Location: ../Manage_Buffalos/invoice_list.php?page=create&bi_code=milk');
        exit();
    }
}
/** Create Buffalo Invoice */ if (isset($_POST['create_bi_invoice_buffalo']) && isset($_SESSION['admins']) && isset($_SESSION['create_bi'])) {

    $err = 0;
    $client = filter_input(INPUT_POST, 'client_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $remarks = filter_input(INPUT_POST, 'Remarks', FILTER_SANITIZE_SPECIAL_CHARS);
    $discount = filter_input(INPUT_POST, 'discount', FILTER_SANITIZE_NUMBER_INT);
    $otherFee = filter_input(INPUT_POST, 'other_fees', FILTER_SANITIZE_NUMBER_INT);

    $subTotal = 0;
    $discountTotal = 0;
    $total = 0;

    (empty($client)) ? $err++ : NULL;
    (empty($discount)) ? $discount = 0 : NULL;

    function checkKeys($api, $randStr)
    {
        $bi_list = $api->Read('bi_list', 'all');
        foreach ($bi_list as $bi) {
            if ($bi->code == $randStr) {
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
        $keyLength = 8;
        $str = "123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $randStr = substr(str_shuffle($str), 0, $keyLength);
        $checkKey = checkKeys($api, $randStr);

        while ($checkKey == true) {
            $randStr = substr(str_shuffle($str), 0, $keyLength);
            $checkKey = checkKeys($api, $randStr);
        }

        return $randStr;
    }

    $code = 'BI-' . generateKeys($api);

    foreach ($_SESSION['create_bi'] as $bi) {
        $validate_bf = '';
        $validate_bf = $api->Read('buffalos', 'set', 'Buffalo_id', $bi['buffalo_id']);
        (!empty($validate_bf[0]->Marked_As)) ? $err++ : NULL;
    }

    if ($err == 0) {

        $rows = 0;
        foreach ($_SESSION['create_bi'] as $bi) {
            $api->Create('bi_session', [
                '1' => ['code', "'$code'"],
                '8' => ['client', "'$client'"],
                '2' => ['buffalo_id', $bi['buffalo_id']],
                '3' => ['buffalo_name', "'" . $bi['buffalo_name'] . "'"],
                '4' => ['buffalo_gender', "'" . $bi['buffalo_gender'] . "'"],
                '7' => ['buffalo_weight', $bi['buffalo_weight']],
                '5' => ['price', $bi['price']],
                '6' => ['date', "'$date'"]
            ]);
            $subTotal += $bi['price'];
            $rows++;
            $api->Update('buffalos', 'Buffalo_id', [
                '1' => ['Marked_As', "'Sold'"],
                '2' => ['BI_Reciept', "'$code'"]
            ], $bi['buffalo_id']);
        }

        (empty($otherFee)) ? $otherFee = 0 : $subTotal += $otherFee;

        if (empty($discount)) {
            $total = number_format($subTotal);
        } else {
            $discountTotal = number_format(($discount / 100), 2);
            $total = number_format($subTotal - ($discountTotal * $subTotal));
        }

        $subTotal = number_format($subTotal);

        $api->Create('bi_list', [
            '1' => ['code', "'$code'"],
            '2' => ['client', "'$client'"],
            '9' => ['category', "'buffalo'"],
            '3' => ['items', $rows],
            '7' => ['other_fees', $otherFee],
            'a' => ['discount', $discount],
            'b' => ['subTotal', "'$subTotal'"],
            '4' => ['amount', "'$total'"],
            'c' => ['remarks', "'$remarks'"],
            '5' => ['date', "'$date'"]
        ]);

        unset($_SESSION['create_bi']);
        $_SESSION['buffalo-message'] = array(
            "title" => 'New Invoices Added',
            "body" => 'Successfully create a new Invoice record.',
            "type" => 'success'
        );
    } else {
        $_SESSION['buffalo-message'] = array(
            "title" => 'Something went wrong!',
            "body" => '',
            "type" => 'error'
        );
    }

    header('Location: ../Manage_Buffalos/invoice_list.php?page=all&bi_code=none');
    exit();
}

/** Update && View Buffalo Invoice */ if (isset($_POST['update_buffalo_invoice']) && isset($_SESSION['code']) && isset($_SESSION['admins'])) {

    $bi_list = $api->Read('bi_list', 'all');
    $code = $_SESSION['code'];
    $ptoken = 0;

    foreach ($bi_list as $bi) {
        ($bi->code == $code) ? $ptoken++ : NULL;
    }

    if ($ptoken == 1) {

        $fetch_bi = $api->Read('bi_list', 'set', 'code', "'$code'");
        $fetch_session = $api->Read('bi_session', 'set', 'code', "'$code'");
        $bf_reciept = $api->Read('buffalos', 'set', 'BI_Reciept', "'$code'");

        $err = 0;
        $client = filter_input(INPUT_POST, 'client_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $remarks = filter_input(INPUT_POST, 'Remarks', FILTER_SANITIZE_SPECIAL_CHARS);
        $discount = filter_input(INPUT_POST, 'discount', FILTER_SANITIZE_NUMBER_INT);
        $otherFee = filter_input(INPUT_POST, 'other_fees', FILTER_SANITIZE_NUMBER_INT);

        $subTotal = 0;
        $discountTotal = 0;
        $total = 0;

        (empty($client)) ? $err++ : NULL;

        if ($err == 0) {
            $rows = 0;
            $subTotal = number_format($subTotal);
            $api->Delete('bi_session', 'code', "$code");
            $arr_id = array();
            foreach ($_SESSION['edit_bi'] as $bi) {
                $api->Create('bi_session', [
                    '1' => ['code', "'$code'"],
                    '2' => ['buffalo_id', $bi['buffalo_id']],
                    '3' => ['buffalo_name', "'" . $bi['buffalo_name'] . "'"],
                    '4' => ['buffalo_gender', "'" . $bi['buffalo_gender'] . "'"],
                    '7' => ['buffalo_weight', "'" . $bi['buffalo_weight'] . "'"],
                    '5' => ['price', $bi['price']],
                    '6' => ['date', "'$date'"],
                    '8' => ['client', "'$client'"]
                ]);
                array_push($arr_id, $bi['buffalo_id']);
                $subTotal += $bi['price'];
                $rows++;
            }

            foreach ($bf_reciept as $reciept_buffalo) {
                $api->Update('buffalos', 'Buffalo_id', [
                    '1' => ['Marked_As', 'NULL'],
                    '2' => ['BI_Reciept', 'NULL']
                ], $reciept_buffalo->Buffalo_id);
            }

            foreach ($arr_id as $id) {
                echo $id . '<br>';
                $api->Update('buffalos', 'Buffalo_id', [
                    '1' => ['Marked_As', "'Sold'"],
                    '2' => ['BI_Reciept', "'$code'"]
                ], $id);
            }

            $otherFee = number_format($otherFee);
            if (empty($discount)) {
                $discount = 0;
                $total = number_format($subTotal + $otherFee);
            } else {
                $discountTotal = number_format(($discount / 100), 2);
                $total = number_format((($subTotal + $otherFee) - ($discountTotal * ($subTotal + $otherFee))));
            }

            $subTotal = number_format($subTotal);

            $api->Update('bi_list', 'code', [
                '2' => ['client', "'$client'"],
                'h' => ['other_fees', "'$otherFee'"],
                '3' => ['items', $rows],
                'a' => ['discount', $discount],
                'b' => ['subTotal', "'$subTotal'"],
                '4' => ['amount', "'$total'"],
                'c' => ['remarks', "'$remarks'"],
                '5' => ['date', "'$date'"]
            ], $code);

            $_SESSION['buffalo-message'] = array(
                "title" => 'Update Invoice',
                "body" => 'Successfully Updated an Invoice.',
                "type" => 'success'
            );
            unset($_SESSION['edit_bi']);
            unset($_SESSION['code']);
        } else {
            $_SESSION['buffalo-message'] = array(
                "title" => 'Something went wrong!',
                "body" => '',
                "type" => 'error'
            );
        }
    } else {
        $_SESSION['buffalo-message'] = array(
            "title" => 'Something went wrong!',
            "body" => '',
            "type" => 'error'
        );
    }
    header('Location: ../Manage_Buffalos/invoice_list.php?page=all&bi_code=none');
    exit();
}

/** UPDATE MILK INVOICE */ if (isset($_POST['update_bi_invoice_milk']) && isset($_SESSION['admins'])) {

    $bi_list = $api->Read('bi_list', 'all');
    $code = $_SESSION['code'];
    $ptoken = 0;

    foreach ($bi_list as $bi) {
        ($bi->code == $code) ? $ptoken++ : NULL;
    }

    if ($ptoken == 1) {

        $client = filter_input(INPUT_POST, 'edit_client_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $remarks = filter_input(INPUT_POST, 'edit_remarks', FILTER_SANITIZE_SPECIAL_CHARS);
        $other_fees = filter_input(INPUT_POST, 'edit_other_fees', FILTER_SANITIZE_NUMBER_INT);
        $discount = filter_input(INPUT_POST, 'edit_discount', FILTER_SANITIZE_NUMBER_INT);
        $price_per_liter = filter_input(INPUT_POST, 'edit_price_per_liter', FILTER_SANITIZE_NUMBER_INT);
        $milk_liter = filter_input(INPUT_POST, 'edit_milk_liter', FILTER_SANITIZE_NUMBER_INT);

        $err = 0;

        (empty($client)) ? $err : NULL;
        (empty($other_fees)) ? $err : NULL;
        (empty($discount)) ? $err : NULL;
        (empty($price_per_liter)) ? $err : NULL;
        (empty($milk_liter)) ? $err : NULL;

        if ($err == 0) {
            $subTotal = 0;
            $subTotal_otherFees = 0;
            $discountTotal = 0;
            $total = 0;
            $grandTotal = 0;

            (empty($other_fees)) ?  $other_fees = 0 : NULL;
            (empty($price_per_liter)) ?  $price_per_liter = 0 : NULL;

            if (!empty($milk_liter) && !empty($price_per_liter)) {
                $total = $milk_liter * $price_per_liter;
                if (empty($discount)) {
                    $grandTotal = number_format($total + $other_fees);
                } else {
                    $discountTotal = number_format(($discount / 100), 2);
                    $grandTotal = number_format((($total + $other_fees) - ($discountTotal * ($total + $other_fees))));
                }
            }

            $subTotal = $total;
            $subTotal_otherFees = number_format($other_fees +  $total);

            $api->Update('bi_list', 'code', [
                '1' => ['client', "'$client'"],
                '2' => ['items', $price_per_liter],
                '3' => ['milk_liter', $milk_liter],
                '4' => ['other_fees', $other_fees],
                '5' => ['discount', $discount],
                '6' => ['subTotal', $subTotal],
                '7' => ['amount', $total],
                '8' => ['remarks', "'$remarks'"],
                '9' => ['date', "'$date'"]
            ], "$code");

            $_SESSION['buffalo-message'] = array(
                "title" => 'Update Invoice',
                "body" => 'Successfully Updated an Invoice.',
                "type" => 'success'
            );
        } else {
            $_SESSION['buffalo-message'] = array(
                "title" => 'Something went wrong!',
                "body" => '',
                "type" => 'error'
            );
        }
    } else {
        $_SESSION['buffalo-message'] = array(
            "title" => 'Something went wrong!',
            "body" => '',
            "type" => 'error'
        );
    }
    header('Location: ../Manage_Buffalos/invoice_list.php?page=all&bi_code=none');
    exit();
}

/** SELECT CATEGORY */
if (isset($_POST['CreateNewInvoice']) && isset($_SESSION['admins'])) {
    $selectInvoiceCategory = filter_input(INPUT_POST, 'selectInvoiceCategory', FILTER_SANITIZE_SPECIAL_CHARS);
    $selectInvoiceCategory = strtolower($selectInvoiceCategory);
    $category = array('milk', 'buffalo');

    $pass = 0;

    (empty($selectInvoiceCategory)) ? $err++ : NULL;
    (!in_array($selectInvoiceCategory, $category)) ? $err++ : NULL;

    if ($pass == 0) {
        if ($selectInvoiceCategory == $category[0]) {
            header('Location: ../../Admin/Manage_Buffalos/invoice_list.php?page=create&bi_code=milk');
            exit();
        } else if ($selectInvoiceCategory == $category[1]) {
            header('Location: ../../Admin/Manage_Buffalos/invoice_list.php?page=create&bi_code=buffalo');
            exit();
        } else {
            $_SESSION['buffalo-message'] = array(
                "title" => 'Something went wrong!',
                "body" => '',
                "type" => 'error'
            );
            header('Location: ../../Admin/Manage_Buffalos/invoice_list.php?page=all&bi_code=none');
            exit();
        }
    } else {
        $_SESSION['buffalo-message'] = array(
            "title" => 'Something went wrong!',
            "body" => '',
            "type" => 'error'
        );
        header('Location: ../../Admin/Manage_Buffalos/invoice_list.php?page=all&bi_code=none');
        exit();
    }
}

// $_POST['create_bis'] = true;
// $_POST['compute_bi_milk'] = true;

/** CREATE INVOICE SESSIONS */ if (isset($_POST['create_bis']) && isset($_SESSION['admins'])) {

    /** ADD */ if (isset($_POST['create_bi'])) {

        $buffalo = filter_input(INPUT_POST, 'buffalo', FILTER_SANITIZE_NUMBER_INT);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_INT);

        $err = 0;
        (empty($buffalo)) ? $err++ : NULL;
        (empty($price)) ? $err++ : NULL;

        (!isset($_SESSION['create_bi'])) ? $_SESSION['create_bi'] = array() : NULL;
        (!isset($_SESSION['create_bi_otherFee'])) ? $_SESSION['create_bi_otherFee'] = array() : NULL;

        if ($err == 0) {
            $fetch_buffalo = $api->Read('buffalos', 'set', 'Buffalo_id', $buffalo);
            $buffalo_name = $fetch_buffalo[0]->Name;
            $buffalo_gender = $fetch_buffalo[0]->Gender;
            $buffalo_weight = $fetch_buffalo[0]->Weight;

            if (!empty($fetch_buffalo)) {
                foreach ($_SESSION['create_bi'] as $index => $bi) {
                    if ($bi['buffalo_id'] == $buffalo) {
                        unset($_SESSION['create_bi'][$index]);
                        break;
                    }
                }

                array_push($_SESSION['create_bi'],  [
                    'buffalo_id' => $buffalo,
                    'buffalo_name' => $buffalo_name,
                    'buffalo_gender' => $buffalo_gender,
                    'buffalo_weight' => $buffalo_weight,
                    'price' => $price,
                    'date' => $date
                ]);

                $indexes = 1;
                $subTotal = 0;
                $Total = 0;
                foreach ($_SESSION['create_bi'] as $bi) {
                    echo '<tr>';
                    echo "<td>$indexes</td>";
                    echo '<td> <span class="fullName_bi">' . $bi["buffalo_name"] . '</span> [<span class="id_bi">' . $bi["buffalo_id"] . '</span>]</td>';
                    echo '<td class="">' . $bi["buffalo_gender"] . '</td>';
                    echo '<td class="">' . $bi["buffalo_weight"] . ' kg</td>';
                    echo '<td>₱' . $bi["price"] . '.00</td>';
                    echo "<td><button class='remove_btn btn btn-sm btn-outline-danger'><i class='bi bi-x'></i></button></td>";
                    echo '</tr>';
                    $indexes++;
                    $subTotal += $bi["price"];
                }
            }
        }
        /** COMPUTATION */
    } else if (isset($_POST['compute_bi'])) {

        $discount = filter_input(INPUT_POST, 'discount', FILTER_SANITIZE_NUMBER_INT);
        $otherFee = filter_input(INPUT_POST, 'otherFee', FILTER_SANITIZE_NUMBER_INT);;
        $subTotal = 0;
        $discountTotal = 0;
        $total = 0;
        $result = [];

        $err = 0;
        unset($_SESSION['create_bi_otherFee']);
        (!isset($_SESSION['create_bi'])) ? $_SESSION['create_bi'] = array() : NULL;
        (!isset($_SESSION['create_bi_otherFee'])) ? $_SESSION['create_bi_otherFee'] = array() : NULL;

        if ($err == 0) {

            foreach ($_SESSION['create_bi'] as $bi) {
                $subTotal += $bi['price'];
            }

            (empty($otherFee)) ? $otherFee = 0 : NULL;

            $_SESSION['create_bi_otherFee'] = $otherFee;

            if (empty($discount)) {
                $total = number_format($subTotal + $otherFee);
            } else {
                $discountTotal = number_format(($discount / 100), 2);
                $total = number_format((($subTotal + $otherFee) - ($discountTotal * ($subTotal + $otherFee))));
            }

            $subTotal = number_format($subTotal);

            $result = [
                'subTotal' => $subTotal,
                'Total' => $total,
                'SESSION OF CREATE' => $_SESSION['create_bi']
            ];

            echo json_encode($result);
        }
    } else if (isset($_POST['compute_bi_milk'])) {
        $discount = filter_input(INPUT_POST, 'discount', FILTER_SANITIZE_NUMBER_INT);
        $price_per_liter = filter_input(INPUT_POST, 'price_per_liter', FILTER_SANITIZE_NUMBER_INT);
        $liter = filter_input(INPUT_POST, 'milk_liter', FILTER_SANITIZE_NUMBER_INT);
        $other_fees = filter_input(INPUT_POST, 'other_fees', FILTER_SANITIZE_NUMBER_INT);

        $subTotal = 0;
        $subTotal_otherFees = 0;
        $discountTotal = 0;
        $total = 0;
        $grandTotal = 0;

        (empty($other_fees)) ?  $other_fees = 0 : NULL;
        (empty($price_per_liter)) ?  $price_per_liter = 0 : NULL;

        if (!empty($liter) && !empty($price_per_liter)) {
            $total = $liter * $price_per_liter;
            if (empty($discount)) {
                $grandTotal = number_format($total + $other_fees);
            } else {
                $discountTotal = number_format(($discount / 100), 2);
                $grandTotal = number_format((($total + $other_fees) - ($discountTotal * ($total + $other_fees))));
            }
        }

        $subTotal = $total;
        $subTotal_otherFees = number_format($other_fees +  $total);

        $result = [
            'subTotal' => $subTotal,
            'subTotalw_otherFees' => $subTotal_otherFees,
            'Total' => $grandTotal
        ];
        echo json_encode($result);
        /** REMOVE ITEM */
    } else if (isset($_POST['remove_bi'])) {
        $remove_item = filter_input(INPUT_POST, 'remove_item', FILTER_SANITIZE_NUMBER_INT);
        $err = 0;
        (empty($remove_item)) ? $err++ : NULL;
        (!isset($_SESSION['create_bi'])) ? $_SESSION['create_bi'] = array() : NULL;
        (!isset($_SESSION['create_bi_otherFee'])) ? $_SESSION['create_bi'] = array() : NULL;

        if ($err == 0) {
            $fetch_buffalo = $api->Read('buffalos', 'set', 'Buffalo_id', $remove_item);

            if (!empty($fetch_buffalo)) {
                foreach ($_SESSION['create_bi'] as $key => $value) {
                    if ($value['buffalo_id'] == $remove_item) {
                        unset($_SESSION['create_bi'][$key]);
                        break;
                    }
                }

                $indexes = 1;
                $subTotal = 0;
                $Total = 0;
                foreach ($_SESSION['create_bi'] as $bi) {
                    echo '<tr>';
                    echo "<td>$indexes</td>";
                    echo '<td> <span class="fullName_bi">' . $bi["buffalo_name"] . '</span> [<span class="id_bi">' . $bi["buffalo_id"] . '</span>]</td>';
                    echo '<td class="">' . $bi["buffalo_gender"] . '</td>';
                    echo '<td class="">' . $bi["buffalo_weight"] . ' kg</td>';
                    echo '<td>₱' . $bi["price"] . '.00</td>';
                    echo "<td><button class='remove_btn btn btn-sm btn-outline-danger'><i class='bi bi-x'></i></button></td>";
                    echo '</tr>';
                    $indexes++;
                    $subTotal += $bi["price"];
                }
            }
        }
    }
}

/** UPDATE INVOICE SESSIONS */ if (isset($_POST['update_bis']) && isset($_SESSION['admins'])) {

    /** EDIT */ if (isset($_POST['edit_bi'])) {

        $buffalo = filter_input(INPUT_POST, 'buffalo', FILTER_SANITIZE_NUMBER_INT);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_INT);
        $code = $_SESSION['code'];

        $err = 0;
        (empty($buffalo)) ? $err++ : NULL;
        (empty($price)) ? $err++ : NULL;

        if ($err == 0) {

            $fetch_buffalo = $api->Read('buffalos', 'set', 'Buffalo_id', $buffalo);
            // $fetch_session = $api->Read('bi_session', 'set', 'code', "'$code'");
            $buffalo_name = $fetch_buffalo[0]->Name;
            $buffalo_gender = $fetch_buffalo[0]->Gender;
            $buffalo_weight = $fetch_buffalo[0]->Weight;

            if (!empty($fetch_buffalo)) {

                foreach ($_SESSION['edit_bi'] as $index => $bi) {
                    if ($bi['buffalo_id'] == $buffalo) {
                        unset($_SESSION['edit_bi'][$index]);
                        break;
                    }
                }

                array_push($_SESSION['edit_bi'],  [
                    'buffalo_id' => $buffalo,
                    'buffalo_name' => $buffalo_name,
                    'buffalo_gender' => $buffalo_gender,
                    'buffalo_weight' => $buffalo_weight,
                    'price' => $price,
                    'date' => $date
                ]);

                $api->Delete('bi_session', 'code', "$code");
                foreach ($_SESSION['edit_bi'] as $bi) {
                    $api->Create('bi_session', [
                        '1' => ['code', "'$code'"],
                        '2' => ['client', "'$client'"],
                        '3' => ['buffalo_id', $bi['buffalo_id']],
                        '4' => ['buffalo_name', "'" . $bi['buffalo_name'] . "'"],
                        '5' => ['buffalo_gender', "'" . $bi['buffalo_gender'] . "'"],
                        '6' => ['buffalo_weight', "'" . $bi['buffalo_gender'] . "'"],
                        '7' => ['price', $bi['price']],
                        '8' => ['date', $bi['date']]
                    ]);
                }

                $indexes = 1;
                $subTotal = 0;
                $Total = 0;
                foreach ($_SESSION['edit_bi'] as $bi) {
                    echo '<tr>';
                    echo "<td>$indexes</td>";
                    echo '<td> <span class="fullName_bi">' . $bi["buffalo_name"] . '</span> [<span class="id_bi">' . $bi["buffalo_id"] . '</span>]</td>';
                    echo '<td class="">' . $bi["buffalo_gender"] . '</td>';
                    echo '<td class="">' . $bi["buffalo_weight"] . ' kg</td>';
                    echo '<td>₱' . $bi["price"] . '.00</td>';
                    echo "<td><button class='remove_btn btn btn-sm btn-outline-danger'><i class='bi bi-x'></i></button></td>";
                    echo '</tr>';
                    $indexes++;
                    $subTotal += $bi["price"];
                }
            }
        }

        /** COMPUTATION */
    } else if (isset($_POST['compute_bi'])) {
        $discount = filter_input(INPUT_POST, 'discount', FILTER_SANITIZE_NUMBER_INT);
        $otherFee = filter_input(INPUT_POST, 'otherFee', FILTER_SANITIZE_NUMBER_INT);
        $subTotal = 0;
        $discountTotal = 0;
        $total = 0;
        $result = [];

        $err = 0;

        if ($err == 0) {
            foreach ($_SESSION['edit_bi'] as $bi) {
                $subTotal += $bi['price'];
            }
            (empty($otherFee)) ? $otherFee = 0 : NULL;
            $_SESSION['edit_bi_otherFee'] = $otherFee;
            if (empty($discount)) {
                $total = number_format($subTotal + $otherFee);
            } else {
                $discountTotal = number_format(($discount / 100), 2);
                $total = number_format((($subTotal + $otherFee) - ($discountTotal * ($subTotal + $otherFee))));
            }
            $subTotal = number_format($subTotal);

            $result = [
                'subTotal' => $subTotal,
                'Total' => $total,
                'SESSION' => $_SESSION['edit_bi']
            ];

            echo json_encode($result);
        }

        /** COMPUTATION FOR MILK BI */
    } else if (isset($_POST['compute_bi_milk'])) {
        $discount = filter_input(INPUT_POST, 'discount', FILTER_SANITIZE_NUMBER_INT);
        $price_per_liter = filter_input(INPUT_POST, 'price_per_liter', FILTER_SANITIZE_NUMBER_INT);

        $subTotal = 0;
        $discountTotal = 0;
        $total = 0;
        $result = [];

        $err = 0;

        if ($err == 0) {
            (empty($price_per_liter)) ? $price_per_liter = 0 : NULL;
            if (empty($discount)) {
                $total = $price_per_liter;
            } else {
                $discountTotal = number_format(($discount / 100), 2);
                $total = number_format((($subTotal + $price_per_liter) - ($discountTotal * ($subTotal + $price_per_liter))));
            }
            $subTotal = $price_per_liter;

            $result = [
                'subTotal' => $subTotal,
                'Total' => $total
            ];

            echo json_encode($result);
        }
        /** REMOVE ITEM */
    } else if (isset($_POST['remove_bi'])) {
        $remove_item = filter_input(INPUT_POST, 'remove_item', FILTER_SANITIZE_NUMBER_INT);
        $err = 0;
        (empty($remove_item)) ? $err++ : NULL;
        if ($err == 0) {
            $fetch_buffalo = $api->Read('buffalos', 'set', 'Buffalo_id', $remove_item);

            if (!empty($fetch_buffalo)) {
                foreach ($_SESSION['edit_bi'] as $key => $value) {
                    if ($value['buffalo_id'] == $remove_item) {
                        unset($_SESSION['edit_bi'][$key]);
                        break;
                    }
                }

                $indexes = 1;
                $subTotal = 0;
                $Total = 0;
                foreach ($_SESSION['edit_bi'] as $bi) {
                    echo '<tr>';
                    echo "<td>$indexes</td>";
                    echo '<td> <span class="fullName_bi">' . $bi["buffalo_name"] . '</span> [<span class="id_bi">' . $bi["buffalo_id"] . '</span>]</td>';
                    echo '<td class="">' . $bi["buffalo_gender"] . '</td>';
                    echo '<td class="">' . $bi["buffalo_weight"] . ' kg</td>';
                    echo '<td>₱' . $bi["price"] . '.00</td>';
                    echo "<td><button class='remove_btn btn btn-sm btn-outline-danger'><i class='bi bi-x'></i></button></td>";
                    echo '</tr>';
                    $indexes++;
                    $subTotal += $bi["price"];
                }
            }
        }
    }
}

/** REMOVE INVOICE */ if (isset($_POST['remove_invoice']) && isset($_SESSION['admins'])) {

    $err = 0;
    $bi_code = filter_input(INPUT_POST, 'remove_invoice', FILTER_SANITIZE_SPECIAL_CHARS);
    $bf_reciept = $api->Read('buffalos', 'set', 'BI_Reciept', "'$bi_code'");

    (empty($bi_code)) ? $err++ : NULL;

    $fetch_bi = $api->Read('bi_list', 'set', 'code', "'$bi_code'");
    $fetch_session = $api->Read('bi_session', 'set', 'code', "'$bi_code'");

    (empty($fetch_bi)) ? $err++ : NULL;

    if ($err == 0) {
        foreach ($bf_reciept as $reciept) {
            $api->Update('buffalos', 'Buffalo_id', [
                '1' => ['Marked_As', 'NULL'],
                '2' => ['BI_Reciept', 'NULL']
            ], $reciept->Buffalo_id);
        }
        $api->Delete('bi_list', 'code', $bi_code);
        $api->Delete('bi_session', 'code', $bi_code);
    }
}

/** RETRIEVE INVOICE */ if (isset($_POST['retrieve_invoice']) && isset($_SESSION['admins'])) {

    $bi_code = filter_input(INPUT_POST, 'retrieve_invoice', FILTER_SANITIZE_SPECIAL_CHARS);
    $err = 0;

    (empty($bi_code)) ? $err++ : NULL;

    if ($err == 0) {
        $fetch_session = $api->Read('bi_session', 'set', 'code', "'$bi_code'");

        $api->Update('bi_list', 'code', [
            '1' => ['marked_as', "'retrieved'"]
        ], $bi_code);

        foreach ($fetch_session as $bi) {
            $api->Update('buffalos', 'Buffalo_id', [
                '1' => ['Marked_As', 'NULL']
            ], $bi->buffalo_id);

            $api->Update('bi_session', 'code', [
                '1' => ['is_sold', 0]
            ], "'$bi_code'");
        }

        $bi_list = $api->Read('bi_list', 'all');
        $indexes = 1;
    }
}