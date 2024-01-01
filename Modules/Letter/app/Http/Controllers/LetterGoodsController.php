<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Rest;

class LetterGoodsController extends Controller
{
    public function listGoods()
    {
        $goodsList = Rest::getGoodsList();

        $result['status'] = 'tes';
        $result['goods'] = $goodsList;
        return $result;
    }
}
