<?php
// Ubah kredensial sesuai XAMPP kamu
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "db_ktp";
$DB_FALLBACK_NAME = "db_ktp_app";

function db_name_sql($name){
    if(!preg_match('/^[A-Za-z0-9_]+$/', $name)){
        die("Nama database tidak valid.");
    }
    return "`".$name."`";
}

require_once __DIR__.'/schema.php';

function open_ktp_connection($host, $user, $pass, $name){
    $db = new mysqli($host, $user, $pass);
    if($db->connect_error){
        die("Koneksi gagal: ".$db->connect_error);
    }
    $db->set_charset("utf8mb4");
    $db->query("CREATE DATABASE IF NOT EXISTS ".db_name_sql($name)." CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    if(!$db->select_db($name)){
        die("Database tidak bisa dipilih: ".$db->error);
    }
    return $db;
}

$koneksi = open_ktp_connection($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

try{
    ensure_core_schema($koneksi);
} catch(mysqli_sql_exception $e){
    $isOrphanTablespace = (int)$e->getCode() === 1813 || stripos($e->getMessage(), 'Tablespace for table') !== false;
    if(!$isOrphanTablespace){
        throw $e;
    }

    $koneksi->close();
    $koneksi = open_ktp_connection($DB_HOST, $DB_USER, $DB_PASS, $DB_FALLBACK_NAME);
    ensure_core_schema($koneksi);
}
