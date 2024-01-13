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
}
