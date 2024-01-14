<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Goodsrequest;

class LetterGoodsController extends Controller
{
    public function listGoodsRequest()
    {
        $data['title'] = 'Surat permintaan barang';
        $data['list'] = Goodsrequest::getGoodsRequestlist();

        return view('letter::goods.index', $data);
    }

    public function addGoodsRequest(Request $request)
    {
        $workorder_uuid = $request->query('workorder_uuid');
        
        $data['title'] = 'Tambah Surat permintaan barang';
        if (isset($workorder_uuid)) {
            $data['hasWorkorder'] = true;
            $data['workorder'] = Goodsrequest::getWorkorder($workorder_uuid);
        } else {
            $data['hasWorkorder'] = false;
            $data['workorder'] = Goodsrequest::getWorkorderList();
        }

        return view('letter::goods.add', $data);
    }

    public function addGoodsRequestStore(Request $request)
    {
        $credentials = $request->validate([
            'workorder_uuid'    => ['required', 'string'],
            'description'       => ['required', 'string'],
        ]);

        if (!isset($request->part_id)) {
            return back()->with('failed', 'Anda belum memasukkan item barang');   
        }

        $uuid = generateUuid();
        $goodsrequestCount = Goodsrequest::getGoodsrequestCount();
        $count = !isset($goodsrequestCount->count) ? 1 : $goodsrequestCount->count + 1;
                
        $saveData = [
            'uuid' => $uuid,
            'created_by' => auth()->user()->uuid,
            'numberid' => genrateLetterNumber('SPB',$count),
            'workorder_uuid' => $request->workorder_uuid,
            'description' => $request->description,
            'count' => $count,
            'status' => 0,
        ];

        $savePartData = [];
        foreach ($request->part_id as $key => $value) {
            $savePartData[] = [
                'uuid' => generateUuid(),
                'goodsrequest_uuid' =>  $uuid,
                'part_id' =>  $value,
                'part_name' =>  $request->part_name[$key],
                'qty' =>  $request->part_qty[$key],
            ];
        }

        $saveGoodsRequest = Goodsrequest::saveGoodsRequest($saveData);
        $savePartsItem = Goodsrequest::saveGoodsRequestParts($savePartData);

        if ($saveGoodsRequest) {
            return back()->with('success', 'Surat permintaan barang tersimpan!');
        }

        return back()->with('failed', 'Surat permintaan barang gagal tersimpan!');   
    }
}
