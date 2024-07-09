<?php
$conn = @mysqli_connect("localhost", "root", "12345678", "tuma_pesa",3307);
if(!$conn){
  echo "Connection failed!".@mysqli_error($conn);
}
?>
