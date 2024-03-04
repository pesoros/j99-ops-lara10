<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\GoodsRelease;
use App\Models\Complaint;
use App\Models\User;

class LetterGoodsReleaseController extends Controller
{
    public function listGoodsRelease()
    {
        $data['title'] = 'Surat keluar barang';
        $data['list'] = GoodsRelease::getGoodsReleaselist();

        return view('letter::goodsrelease.index', $data);
    }

    public function addGoodsRelease(Request $request)
    {        
        $data['title'] = 'Tambah Surat keluar barang';
        $data['hasWorkorder'] = true;

        return view('letter::goodsrelease.add', $data);
    }

    public function addGoodsReleaseStore(Request $request)
    {
        if (!isset($request->part_id)) {
            return back()->with('failed', 'Anda belum memasukkan item barang');   
        }

        $uuid = generateUuid();
        $goodsReleaseCount = GoodsRelease::getGoodsReleaseCount();
        $count = !isset($goodsReleaseCount->count) ? 1 : $goodsReleaseCount->count + 1;
                
        $saveData = [
            'uuid' => $uuid,
            'created_by' => auth()->user()->uuid,
            'numberid' => genrateLetterNumber('SKB',$count),
            'count' => $count,
            'status' => 0,
        ];

        $savePartData = [];
        foreach ($request->part_id as $key => $value) {
            $savePartData[] = [
                'uuid' => generateUuid(),
                'goodsrelease_uuid' =>  $uuid,
                'part_id' =>  $value,
                'part_name' =>  $request->part_name[$key],
                'qty' =>  $request->part_qty[$key],
                'status' => 0,
            ];
        }

        $saveGoodsRelease = GoodsRelease::saveGoodsRelease($saveData);
        $savePartsItem = GoodsRelease::saveGoodsReleaseParts($savePartData);

        if ($saveGoodsRelease) {
            return back()->with('success', 'Surat keluar barang tersimpan!');
        }

        return back()->with('failed', 'Surat keluar barang gagal tersimpan!');   
    }

    public function detailGoodsRelease(Request $request, $uuid)
    {
        $data['title'] = 'Detail SKB';
        $data['detailGoodsRelease'] = GoodsRelease::getGoodsRelease($uuid);
        $data['creator'] = User::getUser($data['detailGoodsRelease']->created_by);
        $data['parts'] = GoodsRelease::getGoodsReleaseParts($data['detailGoodsRelease']->uuid);

        return view('letter::goodsrelease.detail', $data);
    }

    public function progressGoodsRelease(Request $request, $uuid)
    {
        $updateData['status'] = 1;

        $updateGoodsRelease = GoodsRelease::updateGoodsRelease($uuid, $updateData);

        if ($updateGoodsRelease) {
            return back()->with('success', 'Status SKB berhasil diubah menjadi dikerjakan!');
        }

        return back()->with('failed', 'Status SKB gagal diubah menjadi dikerjakan!');   
    }

    public function closeGoodsRelease(Request $request, $uuid)
    {
        $checkParts = GoodsRelease::checkPartsValid($uuid);

        if (count($checkParts) > 0) {
            return back()->with('failed', 'Masih terdapat barang yang statusnya menunggu, Status SKB gagal diubah menjadi selesai!');   
        }

        $updateData['status'] = 2;

        $updateGoodsRelease = GoodsRelease::updateGoodsRelease($uuid, $updateData);

        if ($updateGoodsRelease) {
            return back()->with('success', 'Status SKB berhasil diubah menjadi selesai!');
        }

        return back()->with('failed', 'Status SKB gagal diubah menjadi selesai!');   
    }

    public function updateAction(Request $request, $uuid)
    {
        foreach ($request->parts_uuid as $key => $value) {
            $updateData = [
                'status' =>  $request->parts_status[$key],
                'description' =>  $request->parts_description[$key],
            ];
            $updateDamageAction = GoodsRelease::updateGoodsParts($value, $updateData);
        }

        return back()->with('success', 'Status penanganan barang berhasil diubah!');
    }
}
