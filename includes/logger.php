<?php
// basic logger for security stuff
function log_event($action, $user_id = 'Guest', $details = '')
{
    $logfile = __DIR__ . '/../../logs/security.log';

    // make logs folder if it doesn't exist
    $logdir = dirname($logfile);
    if (!is_dir($logdir)) {
        mkdir($logdir, 0755, true);
    }

    $timestamp = date("Y-m-d H:i:s");
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $log_entry = "[$timestamp] [IP: $ip_address] [User: $user_id] [Action: $action] [Details: $details] [Agent: $user_agent]\n";

    file_put_contents($logfile, $log_entry, FILE_APPEND);
}
?>