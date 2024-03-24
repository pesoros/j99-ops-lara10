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
            if (INTVAL($value->status) === INTVAL(0)) {
                $busMaintenance[] = $value;
            } else if (INTVAL($value->status) === INTVAL(1)) {
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
