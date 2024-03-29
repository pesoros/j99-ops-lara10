<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\GoodsRequest;
use App\Models\Complaint;
use App\Models\User;

class LetterGoodsController extends Controller
{
    public function listGoodsRequest()
    {
        $data['title'] = 'Surat permintaan barang';
        $data['list'] = GoodsRequest::getGoodsRequestlist();

        return view('letter::goods.index', $data);
    }

    public function addGoodsRequest(Request $request)
    {
        $workorder_uuid = $request->query('workorder_uuid');
        
        $data['title'] = 'Tambah Surat permintaan barang';
        $data['hasWorkorder'] = true;
        $data['workorder'] = GoodsRequest::getWorkorder($workorder_uuid);
        $data['damages'] = Complaint::getDamages($data['workorder']->bus_uuid);

        return view('letter::goods.add', $data);
    }

    public function addGoodsRequestStore(Request $request)
    {
        $credentials = $request->validate([
            'workorder_uuid'    => ['required', 'string'],
        ]);

        if (!isset($request->part_id)) {
            return back()->with('failed', 'Anda belum memasukkan item barang');   
        }

        $uuid = generateUuid();
        $goodsrequestCount = GoodsRequest::getGoodsRequestCount();
        $count = !isset($goodsrequestCount->count) ? 1 : $goodsrequestCount->count + 1;
                
        $saveData = [
            'uuid' => $uuid,
            'created_by' => auth()->user()->uuid,
            'numberid' => genrateLetterNumber('SPB',$count),
            'workorder_uuid' => $request->workorder_uuid,
            'count' => $count,
            'status' => 0,
        ];

        $savePartData = [];
        foreach ($request->part_id as $key => $value) {
            $savePartData[] = [
                'uuid' => generateUuid(),
                'goodsrequest_uuid' =>  $uuid,
                'damage' =>  $request->damage_scope[$key],
                'part_id' =>  $value,
                'part_name' =>  $request->part_name[$key],
                'qty' =>  $request->part_qty[$key],
                'status' => 0,
            ];
        }

        $saveGoodsRequest = GoodsRequest::saveGoodsRequest($saveData);
        $savePartsItem = GoodsRequest::saveGoodsRequestParts($savePartData);

        if ($saveGoodsRequest) {
            return back()->with('success', 'Surat permintaan barang tersimpan!');
        }

        return back()->with('failed', 'Surat permintaan barang gagal tersimpan!');   
    }

    public function detailGoodsRequest(Request $request, $uuid)
    {
        $data['title'] = 'Detail SPB';
        $data['detailGoodsRequest'] = GoodsRequest::getGoodsRequest($uuid);
        $data['creator'] = User::getUser($data['detailGoodsRequest']->created_by);
        $data['parts'] = GoodsRequest::getGoodsRequestParts($data['detailGoodsRequest']->uuid);

        return view('letter::goods.detail', $data);
    }

    public function progressGoodsRequest(Request $request, $uuid)
    {
        $updateData['status'] = 1;

        $updateGoodsRequest = GoodsRequest::updateGoodsRequest($uuid, $updateData);

        if ($updateGoodsRequest) {
            return back()->with('success', 'Status SPB berhasil diubah menjadi dikerjakan!');
        }

        return back()->with('failed', 'Status SPB gagal diubah menjadi dikerjakan!');   
    }

    public function closeGoodsRequest(Request $request, $uuid)
    {
        $checkParts = GoodsRequest::checkPartsValid($uuid);

        if (count($checkParts) > 0) {
            return back()->with('failed', 'Masih terdapat barang yang statusnya menunggu, Status SPB gagal diubah menjadi selesai!');   
        }

        $updateData['status'] = 2;

        $updateGoodsRequest = GoodsRequest::updateGoodsRequest($uuid, $updateData);

        if ($updateGoodsRequest) {
            return back()->with('success', 'Status SPB berhasil diubah menjadi selesai!');
        }

        return back()->with('failed', 'Status SPB gagal diubah menjadi selesai!');   
    }

    public function updateAction(Request $request, $uuid)
    {
        foreach ($request->parts_uuid as $key => $value) {
            $updateData = [
                'status' =>  $request->parts_status[$key],
                'description' =>  $request->parts_description[$key],
            ];
            $updateDamageAction = GoodsRequest::updateGoodsParts($value, $updateData);
        }

        return back()->with('success', 'Status penanganan barang berhasil diubah!');
    }
}
