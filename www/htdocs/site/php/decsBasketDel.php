<?
session_start();

/* 	Input to this file $_GET['productIdToRemove'] 
    This file only updates the database, i.e. remove a product from the shopping basket. It outputs the string "OK" if everything is correct.
*/

if(!isset($_GET['productIdToRemove']))die("Not OK");	/* No product id sent as input to this file */

// Add your db queries here
$_SESSION["terms"][$_GET['productIdToRemove']] = NULL;
unset( $_SESSION["terms"][$_GET['productIdToRemove']] );

echo "OK";
?>