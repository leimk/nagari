<?php
function check_date($str) {
    return (date('Y-m-d', strtotime($str)) == $str);
}

?>