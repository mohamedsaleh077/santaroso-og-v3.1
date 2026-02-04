<?php
$cooldown_time = 30;
$remaining_time = 0;

if (isset($_SESSION['lastSubmit'])) {
    $time_since_last = time() - $_SESSION['lastSubmit'];
    if ($time_since_last < $cooldown_time) {
        $remaining_time = $cooldown_time - $time_since_last;
    }
}
