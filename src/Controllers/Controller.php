<?php

namespace App\Controllers;

abstract class Controller {
    protected function response($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    protected function getFullUrl($relativePath) {
        if (empty($relativePath)) return "";
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        return $protocol . "://" . $host . $scriptDir . "/public/" . $relativePath;
    }
}
