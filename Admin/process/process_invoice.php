<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../../includes/classes.php';

$api = new MyAPI($main_conn);

$date = date('Y-m-d h:i:s');

/** Create Buffalo Invoice */ if (isset($_POST['create_bi_invoice']) && isset($_SESSION['admins']) && isset($_SESSION['create_bi'])) {

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

    $code = 'BI - ' . generateKeys($api);

    if ($err == 0) {

        $rows = 0;
        foreach ($_SESSION['create_bi'] as $bi) {
            $api->Create('bi_session', [
                '9' => ['is_sold', '1'],
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
                '1' => ['Marked_As', "'Sold'"]
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

        $fetch_bi = $api->Read('bi_list', 'set', 'code', $code);
        $fetch_session = $api->Read('bi_session', 'set', 'code', $code);

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
            $api->Delete('bi_session', 'code', $code);
            foreach ($_SESSION['edit_bi'] as $bi) {
                $api->Create('bi_session', [
                    '1' => ['code', "'$code'"],
                    '2' => ['buffalo_id', $bi['buffalo_id']],
                    '3' => ['buffalo_name', "'" . $bi['buffalo_name'] . "'"],
                    '4' => ['buffalo_gender', "'" . $bi['buffalo_gender'] . "'"],
                    '5' => ['price', $bi['price']],
                    '6' => ['date', "'$date'"]
                ]);
                $subTotal += $bi['price'];
                $rows++;
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
                $in_arr = 0;
                foreach ($_SESSION['create_bi'] as $bi_query) {
                    if (in_array($buffalo, $bi_query)) {
                        $in_arr++;
                        break;
                    }
                }

                if ($in_arr == 0) {
                    array_push($_SESSION['create_bi'],  [
                        'buffalo_id' => $buffalo,
                        'buffalo_name' => $buffalo_name,
                        'buffalo_gender' => $buffalo_gender,
                        'buffalo_weight' => $buffalo_weight,
                        'price' => $price,
                        'date' => $date
                    ]);
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
                'Total' => $total
            ];

            echo json_encode($result);
        }

        /** REMOVING ITEM */
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
            $fetch_session = $api->Read('bi_session', 'set', 'code', $code);
            $buffalo_name = $fetch_buffalo[0]->Name;
            $buffalo_gender = $fetch_buffalo[0]->Gender;
            $buffalo_weight = $fetch_buffalo[0]->Weight;

            if (!empty($fetch_buffalo)) {

                $in_arr = 0;
                foreach ($_SESSION['edit_bi'] as $bi_query) {
                    if (in_array($buffalo, $bi_query)) {
                        $in_arr++;
                        break;
                    }
                }

                if ($in_arr == 0) {
                    array_push($_SESSION['edit_bi'],  [
                        'buffalo_id' => $buffalo,
                        'buffalo_name' => $buffalo_name,
                        'buffalo_gender' => $buffalo_gender,
                        'bufalo_weight' => $buffalo_weight,
                        'price' => $price,
                        'date' => $date
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
                'Total' => $total
            ];

            echo json_encode($result);
        }

        /** REMOVING ITEM */
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
                    echo '<td>' . $bi["price"] . '</td>';
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
    $bi_code = $_POST['remove_invoice'];

    (empty($bi_code)) ? $err++ : NULL;

    $fetch_bi = $api->Read('bi_list', 'set', 'code', $bi_code);
    $fetch_session = $api->Read('bi_session', 'set', 'code', $bi_code);

    (empty($fetch_bi)) ? $err++ : NULL;

    if ($err == 0) {

        $api->Delete('bi_list', 'code', $bi_code);
        $api->Delete('bi_session', 'code', $bi_code);

        $bi_list = $api->Read('bi_list', 'all');
        $indexes = 1;

        foreach ($bi_list as $bi) {
            $pcs = '';
            ($bi->items > 1) ? $pcs = 'pcs' : $pcs = 'pc';
            if ($bi->marked_as == NULL) {
?>
                <tr>
                    <td><?= $indexes; ?></td>
                    <td><?= $bi->date; ?></td>
                    <td data-target="bi_code"><?= $bi->code; ?></td>
                    <td><?= $bi->client; ?></td>
                    <td><?= $bi->remarks; ?></td>
                    <td><?= $bi->items . ' ' . $pcs; ?></td>
                    <td>₱<?= $bi->subTotal; ?>.00</td>
                    <td>₱<?= $bi->other_fees; ?>.00</td>
                    <td><?= $bi->discount; ?>%</td>
                    <td>₱<?= $bi->amount; ?>.00</td>
                    <td>
                        <a class=" dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                        <ul class="dropdown-menu dropdown-sm">
                            <li><a href="./invoice_list.php?page=view&bi_code=<?= $bi->code; ?>" class="dropdown-item">View</a></li>
                            <li><a href="./invoice_list.php?page=edit&bi_code=<?= $bi->code; ?>" class="dropdown-item">Edit</a></li>
                            <li><button type="button" class="dropdown-item" data-btn="retrieve">Retrieve</button></li>
                            <li><button type="button" class="dropdown-item" data-btn="remove">Remove</button></li>
                        </ul>
                    </td>
                </tr>
            <?php
                $indexes++;
            }
        }
    }
}

/** RETRIEVE INVOICE */ if (isset($_POST['retrieve_invoice']) && isset($_SESSION['admins'])) {

    $bi_code = filter_input(INPUT_POST, 'retrieve_invoice', FILTER_SANITIZE_SPECIAL_CHARS);
    $err = 0;

    (empty($bi_code)) ? $err++ : NULL;

    if ($err == 0) {
        $fetch_session = $api->Read('bi_session', 'set', 'code', $bi_code);

        $api->Update('bi_list', 'code', [
            '1' => ['marked_as', "'retrieved'"]
        ], $bi_code);

        foreach ($fetch_session as $bi) {
            $api->Update('buffalos', 'Buffalo_id', [
                '1' => ['Marked_As', 'NULL']
            ], $bi->buffalo_id);
            
            $api->Update('bi_session', 'code',[
                '1' => ['is_sold', 0]
            ], $bi_code);
        }

        $bi_list = $api->Read('bi_list', 'all');
        $indexes = 1;

        foreach ($bi_list as $bi) {
            $pcs = '';
            ($bi->items > 1) ? $pcs = 'pcs' : $pcs = 'pc';

            if ($bi->marked_as == NULL) {
            ?>
                <tr>
                    <td><?= $indexes; ?></td>
                    <td><?= $bi->date; ?></td>
                    <td data-target="bi_code"><?= $bi->code; ?></td>
                    <td><?= $bi->client; ?></td>
                    <td><?= $bi->remarks; ?></td>
                    <td><?= $bi->items . ' ' . $pcs; ?></td>
                    <td>₱<?= $bi->subTotal; ?>.00</td>
                    <td>₱<?= $bi->other_fees; ?>.00</td>
                    <td><?= $bi->discount; ?>%</td>
                    <td>₱<?= $bi->amount; ?>.00</td>
                    <td>
                        <a class=" dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                        <ul class="dropdown-menu dropdown-sm">
                            <li><a href="./invoice_list.php?page=view&bi_code=<?= $bi->code; ?>" class="dropdown-item">View</a></li>
                            <li><a href="./invoice_list.php?page=edit&bi_code=<?= $bi->code; ?>" class="dropdown-item">Edit</a></li>
                            <li><button type="button" class="dropdown-item" data-btn="retrieve">Retrieve</button></li>
                            <li><button type="button" class="dropdown-item" data-btn="remove">Remove</button></li>
                        </ul>
                    </td>
                </tr>
<?php
                $indexes++;
            }
        }
    }
}
