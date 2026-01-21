<?php
function jsonResponse($status, $message, $data = null, $code = 200) {
    header("HTTP/1.1 $code");
    header("Content-Type: application/json");
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
?>