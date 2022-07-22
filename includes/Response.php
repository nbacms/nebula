<?php

namespace Nebula;

class Response
{
    /**
     * 单例实例
     *
     * @var Response
     */
    private static $instance;

    /**
     * 获取单例实例
     *
     * @return Response
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function render404()
    {
        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <style>
        body {
            background-color: #1d1f23;
        }
        .title {
            padding-top: 10%;
            color: #c7cacd;
            font-size: 3rem;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="title">404 Not Found</div>
</body>
</html>\n
HTML;
    }

    public function render500()
    {
        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Internal Server Error</title>
    <style>
        body {
            background-color: #1d1f23;
        }
        .title {
            padding-top: 10%;
            color: #c7cacd;
            font-size: 3rem;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="title">500 Internal Server Error</div>
</body>
</html>\n
HTML;
    }
}
