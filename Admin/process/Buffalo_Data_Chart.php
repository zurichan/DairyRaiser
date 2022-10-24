<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../../includes/classes.php';

// $monthNum  = 3;
// $dateObj   = DateTime::createFromFormat('!m', $monthNum);
// $monthName = $dateObj->format('F'); // March

$api = new MyAPI($main_conn);
$date = date('Y-m-d');

/** FETCH YEARS */ if (isset($_POST['Fetch_MP_Year']) && isset($_POST['buffalo_milked_Year'])) {

    if (isset($_POST['yearChartFilter'])) {
        $filter_input = filter_input(INPUT_POST, 'yearChartFilter', FILTER_SANITIZE_NUMBER_INT);
    } else {
        $filter_input = date('Y');
    }

    $time = strtotime($date);
    $yearData = [];
    $bpData = [];
    $bpResult = [];
    $yearResult = [];
    $finalResult = [];
    $strCurrentMonth = date('F');
    $month = date('n');
    $year = date('Y');

    $buffalos = $api->Read('buffalos', 'all');
    $mp = $api->Read('milk_production', 'all');
    $lactations = array('Early Lactation', 'Middle Lactation', 'Late Lactation', 'Dry Period');

    $stringDates = [
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December'
    ];

    $yearData['January'] = 0;
    $yearData['February'] = 0;
    $yearData['March'] = 0;
    $yearData['April'] = 0;
    $yearData['May'] = 0;
    $yearData['June'] = 0;
    $yearData['July'] = 0;
    $yearData['August'] = 0;
    $yearData['September'] = 0;
    $yearData['October'] = 0;
    $yearData['November'] = 0;
    $yearData['December'] = 0;

    foreach ($mp as $pregnant_buffalo) {
        $value = strtotime($pregnant_buffalo->date);
        $strYear = date('Y', $value);
        if ($strYear == $filter_input) {
            $strMonth = date('m', strtotime($pregnant_buffalo->date));

            switch ($strMonth) {
                case '01':
                    $yearData['January'] += $pregnant_buffalo->liters;
                    break;
                case '02':
                    $yearData['February'] += $pregnant_buffalo->liters;
                    break;
                case '03':
                    $yearData['March'] += $pregnant_buffalo->liters;
                    break;
                case '04':
                    $yearData['April'] += $pregnant_buffalo->liters;
                    break;
                case '05':
                    $yearData['May'] += $pregnant_buffalo->liters;
                    break;
                case '06':
                    $yearData['June'] += $pregnant_buffalo->liters;
                    break;
                case '07':
                    $yearData['July'] += $pregnant_buffalo->liters;
                    break;
                case '08':
                    $yearData['August'] += $pregnant_buffalo->liters;
                    break;
                case '09':
                    $yearData['September'] += $pregnant_buffalo->liters;
                    break;
                case '10':
                    $yearData['October'] += $pregnant_buffalo->liters;
                    break;
                case '11':
                    $yearData['November'] += $pregnant_buffalo->liters;
                    break;
                case '12':
                    $yearData['December'] += $pregnant_buffalo->liters;
                    break;
            }
        }
    }

    foreach ($stringDates as $labelkey => $labelvalue) {
        if (!empty($yearData[$labelvalue])) {
            $yearResult[$labelvalue] = $yearData[$labelvalue];
        } else {
            $yearResult[$labelvalue] = 0;
        }
    }
    /** END OF ASYNC YEAR */

    $input_month = date('m');

    $startDate = $filter_input . '-01-01';
    $endDate = $filter_input . '-12-31';

    $date = date('Y-m-d');

    $mp_buffalo = $api->Read('milk_production', 'all');
    $BetweenDate = $api->Between('milk_production', 'date', "'$startDate'", "'$endDate'");

    foreach ($BetweenDate as $bfdate) {
        $time = strtotime($bfdate->date);
        $MonthDate = date('m', $time);
        $YearDate = date('Y', $time);
        $DaysInMonths = cal_days_in_month(CAL_GREGORIAN, $MonthDate, $YearDate);
        $outputStartDate = $YearDate . '-' . $MonthDate . '-01';
        $outputEndDate = $YearDate . '-' . $MonthDate . '-' . $DaysInMonths;
        if (!isset($bpData[$MonthDate])) {
            $bpData[$MonthDate] = $bfdate->total_pregnant;
        } else {
            $bpData[$MonthDate] = $bpData[$MonthDate] + $bfdate->total_pregnant;
        }
        $fetch_buffalo = $api->Between('milk_production', 'date', "'$outputStartDate'", "'$outputEndDate'");
        $total_pregBuf = 0;

        foreach ($fetch_buffalo as $milk) {
            if (isset($fetch_buffalo)) {
                $total_pregBuf += $milk->total_pregnant;
            }
        }
    }

    foreach ($stringDates as $labelkey => $labelvalue) {
        if (!empty($bpData[$labelkey])) {
            $bpResult[$labelvalue] = $bpData[$labelkey];
        } else {
            $bpResult[$labelvalue] = 0;
        }
    }

    $finalResult = [
        'Yield' => $yearResult,
        'Milked' => $bpResult,
        'Year' => date('Y')
    ];

    echo json_encode($finalResult);
}

/** FETCH MONTHS/WEEKS/DAYS */ if (isset($_POST['Fetch_MP_Weeks'])) {

    $weeksData = [];
    $mpData = [];
    $weeksResult = [];
    $mpResult = [];
    $finalResult = [];
    $weeks = '';
    $rangeValue1 = '';
    $rangeValue2 = '';
    $rangeNum1 = '';
    $rangeNum2 = '';
    
    $buffalo_mp = $api->Read('milk_production', 'all');

    /** WEEKS */
    if (isset($_POST['weeksChartFilter'])) {
        switch ($_POST['weeksChartFilter']) {
            case '1st':
                $days = 1;
                break;
            case '2nd':
                $days = 8;
                break;
            case '3rd':
                $days = 15;
                break;
            case '4th':
                $days = 22;
                break;
        }
    } else {
        $days = date('d');
    }

    /** YEARS */
    if (isset($_POST['yearsChartFilter'])) {
        $year = $_POST['yearsChartFilter'];
    } else {
        $year = date('Y');
    }

    /** MONTHS */

    if (isset($_POST['monthsChartFilter'])) {
        $month = date($_POST['monthsChartFilter']);
    } else {
        $month = date('n');
    }

    $DaysInMonths = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    if ($days <= 7) { # 1-7
        $weeks = 0;
        $rangeValue1 = date($year . '-' . $month . '-01');
        $rangeValue2 = date($year . '-' . $month . '-07');
        $rangeNum1 = 1;
        $rangeNum2 = 7;
    } else if ($days <= 14) { # 8-14
        $weeks = 1;
        $rangeValue1 = date($year . '-' . $month . '-08');
        $rangeValue2 = date($year . '-' . $month . '-14');
        $rangeNum1 = 8;
        $rangeNum2 = 14;
    } else if ($days <= 21) { # 15-21
        $weeks = 2;
        $rangeValue1 = date($year . '-' . $month . '-15');
        $rangeValue2 = date($year . '-' . $month . '-21');
        $rangeNum1 = 15;
        $rangeNum2 = 21;
    } else if ($days > 21) { # > 21
        $weeks = 3;
        $rangeValue1 = date($year . '-' . $month . '-22');
        $rangeValue2 = date($year . '-' . $month . '-' . $DaysInMonths);
        $rangeNum1 = 22;
        $rangeNum2 = $DaysInMonths;
    }

    $betweenDate = $api->Between('milk_production', 'date', "'$rangeValue1'", "'$rangeValue2'");

    foreach ($betweenDate as $value) {

        $time = strtotime($value->date);
        $FinalValue = date('j', $time);

        if (isset($weeksData)) {
            $weeksData[$FinalValue] = $value->liters;
            if(!isset($mpData)) {
                $mpData[$FinalValue] = $value->total_pregnant;
            } else {
                $mpData[$FinalValue] = $value->total_pregnant;
            }
        }
    }

    $index = 0;
    while ($rangeNum1 <= $rangeNum2) {

        $create_date = date_create($rangeValue1);
        date_add($create_date, date_interval_create_from_date_string("$index days"));
        $key_date = date_format($create_date, 'M-j, D');

        if (isset($weeksData[$rangeNum1]) && isset($mpData)) {
            $weeksResult[$key_date] = $weeksData[$rangeNum1];
            $mpResult[$key_date] = $mpData[$rangeNum1];
        } else {
            $weeksResult[$key_date] = 0;
            $mpResult[$key_date] = 0;
        }

        $index++;
        $rangeNum1++;
    }

    $finalResult = [
        'Yield' => $weeksResult,
        'Milked' => $mpResult,
        'Weeks' => $weeks
    ];

    echo json_encode($finalResult);
}

/** FETCH BUFFALO DATA NORMAL/SICK/DECEASED/LACTATING */ if (isset($_POST['Fetch_Stats'])) {

    $buffalos = $api->Read('buffalos', 'all');
    $buffalo_rows = $api->Read('buffalos', 'all', NULL, NULL, TRUE);
    $lactations = array('Early Lactation', 'Middle Lactation', 'Late Lactation', 'Dry Period');

    $normal = 0;
    $sold = 0;
    $lactating = 0;
    $sick = 0;
    $deceased = 0;
    $Result = [];

    foreach ($buffalos as $bf_stats) {
        
        if($bf_stats->Marked_As == 'Deceased') {
            $deceased++;
        } else if($bf_stats->Marked_As == 'Sold') {
            $sold++;
        } else if($bf_stats->Health_Status == 'Sick') {
            $sick++;
        } else if ($bf_stats->Gender == 'Female' && in_array($bf_stats->Lactation_Cycle, $lactations)) {
            $lactating++;
        } else if($bf_stats->Health_Status == 'Normal') {
            $normal++;
        }
    }
    
    $percentNormal = number_format(($normal / $buffalo_rows) * 100, 2);
    $percentLactating = number_format(($lactating / $buffalo_rows) * 100, 2);
    $percentSold = number_format(($sold / $buffalo_rows) * 100, 2);
    $percentSick = number_format(($sick / $buffalo_rows) * 100, 2);
    $percentDeceased = number_format(($deceased / $buffalo_rows) * 100, 2);

    $Result = [
        'total' => $buffalo_rows,
        'normal' => $percentNormal,
        'lactating' => $percentLactating,
        'sold' => $percentSold,
        'sick' => $percentSick,
        'deceased' => $percentDeceased
    ];

    echo json_encode($Result);

}


