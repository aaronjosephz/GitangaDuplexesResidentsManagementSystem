<?php
$con = @mysqli_connect("localhost", "root", "12345678", "rental_house",3307);
if(!$con){
  echo "Connection failed!".@mysqli_error($con);
}
?>
