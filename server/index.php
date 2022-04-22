<?php
   
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header("Access-Control-Allow-Headers: Content-Type");
    require_once './restler.php';
    use Luracast\Restler\Restler;
    $r = new Restler();
    $r->setSupportedFormats('JsonFormat','UploadFormat');
    $r->addAPIClass('Ares');
    $r->handle();

