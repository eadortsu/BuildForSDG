<?php
/**
 * Created by PhpStorm.
 * User: virus
 * Date: 12/29/2017
 * Time: 5:05 AM
 */


$dbname = 'project.db';

if(!class_exists('SQLite3'))
    die("sqlite3 no supported");
$db = new SQLite3($dbname);


function select( $table ,$parm = "*",  $where = "",$order = "" ,$limit = ""){
    if ($parm === "all" || $parm === "ALL") {
        $parm = "*";
    }


    $query = "select {$parm}  ";
    $query .= " FROM {$table} ";
    if($where != ""){  $query .= " WHERE {$where}";
    }
    if($order != ""){  $query .= " ORDER BY {$order}";
    }
    if($limit != ""){  $query .= " LIMIT {$limit}";
    }


    $result_set = $GLOBALS['db']->query($query);
    //$result_set = mysqli_query($GLOBALS['db'], $query);
    // confirm($result_set);
    if ($result_set) {
        return $result_set;
    }else{
        return "Database query failed.".$query;
    }


}


function insert($table, $data){


    $fields = array_keys($data);


    $query = "INSERT INTO {$table}";
    $query .= "(`";
    $query .= implode('`,`', $fields);
    $query .= "`)";
    $query .= "VALUES('" . implode("','", $data) . "')";

    $result_set = $GLOBALS['db']->query($query);
    //$result_set = mysqli_query($GLOBALS['db'], $query);
    //confirm($result_set);
    if ($result_set == 1) {
        return 111;
    }else{
        //  return 999;

        $err =  "Database query failed. ".$query;
        return  (string)$err;
    }

}



function update($table, $data ,$parm ="" )
{

    $sets = array();

    foreach($data as $column => $value)

    {

        $sets[] = "`".$column."` = '".$value."'";

    }




    $query = "UPDATE {$table}";
    $query .= " SET ";
    $query .=  implode(', ', $sets);
    $query .= " WHERE {$parm}";

    $result_set = $GLOBALS['db']->query($query);
    // confirm($result_set);
    if ($result_set == 1) {
        return 111;
    }else{
        //  return 999;

        $err = "Database query failed. ".$query;
        return  (string)$err;
    }


}




function delete($table, $where)
{


    $query = " DELETE  ";
    $query .= " FROM {$table} ";
    $query .= "WHERE {$where}";


    $result_set = $GLOBALS['db']->query($query);

    // confirm($result_set);
    if ($result_set = 1) {
        return 111;
    }else{
        $err = "Database query failed. ".$GLOBALS['db']->lastErrroMsg().$query;
        return  (string)$err;
    }

}

function redirect_to( $location = NULL ) {
    if ($location != NULL) {
        header("Location: {$location}");
        exit;
    }
}















