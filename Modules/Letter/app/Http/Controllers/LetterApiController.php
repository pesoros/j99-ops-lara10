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

    public function fuelAllowance(Request $request, $busUuid, $route)
    {
        $getData = Rest::getFuelAllowance($busUuid, $route);
        $allowance = 0;
        if (count($getData) > 0) {
            $allowance = $getData[0]->allowance;
        }
        return [
            'allowance' => $allowance
        ];
    }

    public function invoice(Request $request)
    {
        $page = $request->query('page');
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');
        $invoice = Rest::getInvoice($page, $startDate, $endDate);

        $result['pageInfo'] = $invoice->sp;
        $result['data'] = $invoice->d;

        return $result;
    }

    public function invoiceDetail(Request $request, $id)
    {
        $spareParts = Rest::getInvoiceDetail($id);

        return $spareParts->d;
    }
}
