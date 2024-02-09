<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\Approval;

class LetterPurchaseRequestController extends Controller
{
    public function listPurchaseRequest()
    {
        $data['title'] = 'Surat pengadaan barang';
        $data['list'] = PurchaseRequest::getPurchaseRequestList();

        return view('letter::purchaserequest.index', $data);
    }

    public function addPurchaseRequest()
    {
        $data['title'] = 'Tambah Surat pengadaan barang';

        return view('letter::purchaserequest.add', $data);
    }

    public function addPurchaseRequestStore(Request $request)
    {
        if (!isset($request->part_id)) {
            return back()->with('failed', 'Anda belum memasukkan item barang');   
        }

        $uuid = generateUuid();
        $purchaseRequestCount = PurchaseRequest::getPurchaseRequestCount();
        $count = !isset($purchaseRequestCount->count) ? 1 : $purchaseRequestCount->count + 1;

        $saveData = [
            'uuid' => $uuid,
            'created_by' => auth()->user()->uuid,
            'numberid' => genrateLetterNumber('PR',$count),
            'count' => $count,
            'status' => 0,
        ];

        $savePartData = [];
        foreach ($request->part_id as $key => $value) {
            $savePartData[] = [
                'uuid' => generateUuid(),
                'purchaserequest_uuid' =>  $uuid,
                'part_id' =>  $value,
                'part_name' =>  $request->part_name[$key],
                'qty' =>  $request->part_qty[$key],
                'status' => 0,
            ];
        }

        $savePurchaseRequest = PurchaseRequest::savePurchaseRequest($saveData);
        $savePartsItem = PurchaseRequest::savePurchaseRequestParts($savePartData);

        if ($savePurchaseRequest) {
            return back()->with('success', 'Anda berhasil membuat surat pengadaan');
        }

        return back()->with('failed', 'surat pengadaan gagal tersimpan!');   
    }

    public function detailPurchaseRequest(Request $request, $uuid)
    {
        $data['title'] = 'Detail surat pengadaan';
        $data['detailPurchaseRequest'] = PurchaseRequest::getPurchaseRequest($uuid);
        $data['creator'] = User::getUser($data['detailPurchaseRequest']->created_by);
        $data['parts'] = PurchaseRequest::getPurchaseRequestParts($data['detailPurchaseRequest']->uuid);
        $data['approval'] = Approval::getApprovalList($uuid, config('constants.purpose.pr'), config('constants.approval_role_needs.pr'));

        return view('letter::purchaserequest.detail', $data);
    }

    public function purchaseRequestApproval(Request $request, $uuid)
    {
        $credentials = $request->validate([
            'approval_status'  => ['required', 'string'],
            'approval_note'    => ['required', 'string'],
        ]);

        $roleInfo = Session('role_info_session');

        $saveApprovalData = [
            'uuid' => generateUuid(),
            'purpose' => config('constants.purpose.pr'),
            'approved_by' => auth()->user()->uuid,
            'role_uuid' => $roleInfo->role_uuid,
            'related_uuid' => $uuid,
            'status' => $request->approval_status,
            'note' =>  $request->approval_note,
        ];
        
        $clearence = Approval::clearance($saveApprovalData);
        $saveApproval = Approval::saveApproval($saveApprovalData);

        if ($saveApproval) {
            return back()->with('success', 'Persetujuan tersimpan!');
        }

        return back()->with('failed', 'Persetujuan gagal tersimpan!');   
    }
}
