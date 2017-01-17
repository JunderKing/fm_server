<?php

class Database {
    protected $mysqli;
    function __construct(){
        $this->connectDb();
    }
    function connectDb(){
        $this->mysqli = new mysqli("127.0.0.1", "myuser", "youxiwang", "freeman");
        if ($this->mysqli->connect_errno) {
            printf ("connect failed:%s\n", $this->connect_error);
            exit();
        }
    }
    function execute($sql){
        $ret = $this->mysqli->query($sql);
        if (!$ret) {
            return $this->mysqli->error;
        }
        return $this->mysqli->affected_rows;
    }
    function query($sql){
        $ret = $this->mysqli->query($sql);
        $data = array();
        while ($row = $ret->fetch_array(MYSQLI_ASSOC)){
            $data[] = $row;
        }
        return $data;
    }
    function eacape($string){
        return $this->mysqli->real_escape_string($string);
    }
    function insertId(){
        return $this->mysqli->insert_id;
    }
    function closeDb(){
        return $this->mysqli->close();
    }
}
