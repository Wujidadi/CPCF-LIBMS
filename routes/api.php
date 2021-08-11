<?php

$Route->map('GET', '/api', function()
{
    header('Content-Type: text/plain');
    echo 'API';
});
