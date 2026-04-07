<?php
session_start();

print_r($_SESSION);
echo "<br>";
echo "<pre>";
print_r($_SERVER);
echo "</pre>";

echo $_SERVER['HTTP_REFERER'];
echo '<p><a href="javascript:history.go(-1)" title="Return to previous page">« Go back</a></p>';
print_r($_SERVER['HTTP_REFERER']);
?>