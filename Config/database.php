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
*/

DbConnectionStore::addConnection('Default', [
    "DB_HOST" => env("DB_HOST"),
    "DB_PORT" => env("DB_PORT"),
    "DB_DRIVER"=>env("DB_DRIVER"), //Database driver
    "DB_NAME" => env("DB_NAME"),
    "DB_USERNAME" => env("DB_USERNAME"),
    "DB_PASSWORD" => env("DB_PASSWORD")
]);

/*** You can add another database connection in the same way as above ***/
/*
 * when you want to use a connection variable, then
 * use 
 * $conn = DbConnectionStore::getConnection("<<your_connection_name>>");
 * In case of the above defined example:
 * $conn = DbConnectionStore::getConnection("Default");
 */

