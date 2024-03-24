<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;

class ApiController extends Controller
{
    public function busStatus()
    {
        $busReady = [];
        $busMaintenance = [];
        $getBus = Bus::getBusList();

        foreach ($getBus as $key => $value) {
            if (STRVAL($value->status) === STRVAL(0)) {
                $busMaintenance[] = $value;
            } else if (STRVAL($value->status) === STRVAL(1)) {
                $busReady[] = $value;
            }
        }

        $result = [
            'busReady'        => $busReady,
            'busMaintenance'  => $busMaintenance,
        ];

        return $result;
    }
}
