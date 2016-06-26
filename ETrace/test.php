<?php
require_once "TItem.php";
require_once "Error.php";

//throw new ErrorException("Error Processing Request", 1);

$ddd = new X\ETrace\Error("Error Processing Request", 1);

throw $ddd;
?>