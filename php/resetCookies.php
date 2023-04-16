<?php
setcookie("pageRatingNumber", "" , time()-1800 );
setcookie("pageRatingCookie", "" , time()-1800 );
setcookie("currentRate", "" , time()-1800 );
header( "Location: logged.php");
?>