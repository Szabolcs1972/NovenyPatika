<?php
// READ USER DATAFILE
function getData($path) {
    $users = [];
    try {
        $file = fopen($path, "r");
    } catch (Exception $exception) {
        print $exception.getMessage()."<br>";
    }
    if ($file) {

        while (!feof($file)) {
            $users[] = unserialize(fgets($file));

        }

        fclose($file);
    }


    return $users;
}