<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Rest;

class LetterApiController extends Controller
{
    public function spareParts(Request $request)
    {
        $keyword = $request->query('keyword');
        $spareParts = Rest::getSpareParts($keyword);

        return $spareParts->d;
    }

    public function trasBus(Request $request)
    {
        $busuuid = $request->query('busuuid');
        if (!$busuuid) {
            return [];
        }

        $getBus = Rest::getBus($busuuid);
        $result[] = Rest::getTripAssign($getBus->assign_id_a);
        $result[] = Rest::getTripAssign($getBus->assign_id_b);

        return $result;
    }
}
