<?php

/** A CLASS FOR CREATE, READ, UPDATE, AND DELETE DATA INTO DATABASE */

/** CLASSES FOR USERS */




/** --------------------------------------------------------------------- */



class MyAPI
{
    protected $db;

    /** CONSTRUCT */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /** CREATE */
    public function Create(string $tableName, array $values)
    {
        $query = '';
        $statement = '';

        $query = "INSERT INTO `$tableName` (";
        $value_size = sizeof($values);
        $counter = 0;

        foreach ($values as $value) {
            $query .= " `$value[0]`";
            $counter++;
            if ($counter != $value_size) {
                $query .= ", ";
            } else if ($counter == $value_size) {
                $query .= ") ";
            }
        }

        $query .= "VALUES (";
        $counter = 0;

        foreach ($values as $value) {
            $query .= " $value[1]";
            $counter++;
            if ($counter != $value_size) {
                $query .= ", ";
            } else if ($counter == $value_size) {
                $query .= ") ";
            }
        }

        $statement = $this->db->prepare($query);
        $statement->execute();
    }

    /** READ */
    public function Read(string $tableName, string $method, $target = false, $values = false, bool $rows_only = false)
    {
        $query = '';
        $statement = '';
        $result = '';

        switch ($method) {
            case 'all':
                $query = "SELECT * FROM `$tableName`";
                break;
            case 'set':
                $query = "SELECT * FROM `$tableName` WHERE $target = :input_value";
                break;
            default:
                return 'Wrong Target';
                break;
        }
        $statement = $this->db->prepare($query);
        ($values == false) ?
            $statement->execute() :
            $statement->execute(['input_value' => $values]);

        ($rows_only == false) ?
            $result = $statement->fetchAll() :
            $result = $statement->rowCount();

        return $result;
    }

    /** UPDATE */
    public function Update(string $tableName, string $target, array $values, $unique_char)
    {
        $query = '';
        $statement = '';

        $query = "UPDATE `$tableName` SET ";
        $value_size = sizeof($values);
        $counter = 0;

        foreach ($values as $value) {
            $query .= " $value[0] = $value[1]";
            $counter++;
            if ($counter != $value_size) {
                $query .= ', ';
            }
        }

        $query .= " WHERE `$target` = :input_value";
        $statement = $this->db->prepare($query);
        $statement->execute(['input_value' => $unique_char]);
    }

    /** DELETE */
    public function Delete(string $tableName, mixed $target, mixed $values, bool $betweeen = false)
    {
        $query = '';
        $statement = '';

        if ($betweeen == true) {
            $val1 = $values[0];
            $val2 = $values[1];
            $query = "DELETE FROM `$tableName` WHERE `$target` BETWEEN $val1 AND $val2";
        } else {
            $query = "DELETE FROM `$tableName` WHERE `$target` = :input_value";
        }

        $statement = $this->db->prepare($query);

        if ($betweeen == true) {
            $statement->execute();
        } else {
            $statement->execute(['input_value' => $values]);
        }
    }

    /** SUM */
    public function Sum(string $tableName, string $method, string $sum_target, $target = false, mixed $values = false)
    {
        $query = '';
        $statement = '';
        $result = '';

        switch ($method) {
            case 'all':
                $query = "SELECT SUM(`$sum_target`) AS `output` FROM `$tableName`";
                break;
            case 'set':
                $query = "SELECT SUM(`$sum_target`) AS `output` FROM `$tableName` WHERE `$target` = :input_value";
                break;
            default:
                echo 'Wrong Method';
                break;
        }
        $statement = $this->db->prepare($query);
        ($values == false) ?
            $statement->execute() :
            $statement->execute(['input_value' => $values]);

        $result = $statement->fetch();

        return $result;
    }

    /** SEARCH */
    public function Search(string $tableName, string $target, mixed $values, bool $rows_only = false)
    {
        $query = '';
        $statement = '';
        $result = '';

        $query = "SELECT * FROM `$tableName` WHERE `$target` LIKE '%" . $values . "%'";
        $statement = $this->db->prepare($query);
        $statement->execute();
        ($rows_only == false) ? $result = $statement->fetchAll() : $result = $statement->rowCount();

        return $result;
    }

    /** BETWEEN */
    public function Between(string $tableName, string $target, mixed $value1, mixed $value2)
    {
        $query = '';
        $statement = '';
        $result = '';

        $query = "SELECT * FROM `$tableName` WHERE `$target` BETWEEN $value1 AND  $value2 ORDER BY `date`";
        $statement = $this->db->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();

        return $result;
    }

    /** IP ADDRESS */
    public function IP_address()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    /** FILTER STRING */
    public function filter_string_polyfill(string $string): string
    {
        $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
        return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
    }
}













// function checkKeys($main_conn, $keys)
// {
//     $new_user = new User();

//     $all_users = $new_user->All_Users($main_conn);
    
//     foreach ($all_users as $id) {
//         if ($id->user_id == $keys) {
//             $keyExists = true;
//             break;
//         } else {
//             $keyExists = false;
//         }
//     }
//     return $keyExists;
// }

// function generateKeys($main_conn)
// {
//     $keyLength = 4;
//     $str = "1234567890";
//     $randStr = substr(str_shuffle($str), 0, $keyLength);
//     $keys = intval($randStr);

//     $checkKeys = checkKeys($main_conn, $keys);

//     while ($checkKeys == true) {
//         $randStr = substr(str_shuffle($str), 0, $keyLength);
//         $keys = intval($randStr);

//         $checkKeys = checkKeys($main_conn, $keys);
//     }

//     return $keys;
// }
// $finalkeys = generateKeys($main_conn);