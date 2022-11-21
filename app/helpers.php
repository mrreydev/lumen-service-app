<?php

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

function generateParentNum()
{
    $randomStr = Str::upper(Str::random(5));
    $timestamp = strval(Carbon::now()->timestamp);
    $timestamp = substr($timestamp, -5);

    $parentNum = $randomStr.$timestamp;

    return $parentNum;
}
