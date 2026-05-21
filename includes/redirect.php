<?php
/**
 * Redirect without "headers already sent" when prior output exists.
 */
function app_redirect($url)
{
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }
    echo '<script>location.replace(' . json_encode($url) . ');</script>';
    exit;
}
