<?php
require_once('Inventory.php');

$inventory = new Inventory();
$inventory->addPurchase($_POST['product_id'], $_POST['quantity'], $_POST['supplier_id']);
?>