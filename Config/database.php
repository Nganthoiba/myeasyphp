<?php
use MyEasyPHP\Libs\Database\DbConnectionStore;
/*** Database Configuration ***/

/*
    For setting database driver (DB_DRIVER). Use the followings:
*   1. mysql     :-  for MySql Database Server
*   2. pgsql     :-  for Postgres Database Server
*   3. sqlsrv    :-  for Microsoft SQL Database Server
 * 
 * Make sure that all your database connection parameters including credentials are set in the .env file.
 * One example has already been set for you in the .example.env file. Use global function env(<<key>>) to get the 
 * configuration set in the .env file.
*/

DbConnectionStore::addConnection('Default', [
    "DB_HOST" => env("DB_HOST"),
    "DB_PORT" => env("DB_PORT"),
    "DB_DRIVER"=>env("DB_DRIVER"), //Database driver
    "DB_NAME" => env("DB_NAME"),
    "DB_USERNAME" => env("DB_USERNAME"),
    "DB_PASSWORD" => env("DB_PASSWORD")
]);

DbConnectionStore::addConnection('DbServer1', [
    "DB_HOST" => env("DB1_HOST"),
    "DB_PORT" => env("DB1_PORT"),
    "DB_DRIVER"=>env("DB1_DRIVER"), //Database driver
    "DB_NAME" => env("DB1_NAME"),
    "DB_USERNAME" => env("DB1_USERNAME"),
    "DB_PASSWORD" => env("DB1_PASSWORD")
]);

DbConnectionStore::addConnection('DbServer2', [
    "DB_HOST" => env("DB2_HOST"),
    "DB_PORT" => env("DB2_PORT"),
    "DB_DRIVER"=>env("DB2_DRIVER"), //Database driver
    "DB_NAME" => env("DB2_NAME"),
    "DB_USERNAME" => env("DB2_USERNAME"),
    "DB_PASSWORD" => env("DB2_PASSWORD")
]);

/*** You can add another database connection in the same way as above ***/
/*
 * when you want to use a connection variable, then
 * use 
 * $conn = DbConnectionStore::getConnection("<<your_connection_name>>");
 * In case of the above defined example:
 * $conn = DbConnectionStore::getConnection("Default");
 */

