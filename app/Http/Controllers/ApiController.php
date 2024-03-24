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
            if ($value->status === 0) {
                $busMaintenance[] = $value;
            } else if ($value->status === 1) {
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
