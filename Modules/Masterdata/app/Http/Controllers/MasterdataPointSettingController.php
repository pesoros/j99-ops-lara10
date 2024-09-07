<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PointSetting;

class MasterdataPointSettingController extends Controller
{
    public function listPoint()
    {
        $data['title'] = 'Setting Point';
        $data['list'] = PointSetting::getPointList();

        return view('masterdata::point_setting.index', $data);
    }

    public function editPoint($fleetid)
    {
        $data['title'] = 'Edit Point Setting';
        $data['fleet'] = PointSetting::getFleet($fleetid);
        $data['point'] = PointSetting::getPoint($fleetid);
        $data['percentage'] = isset($data['point']->percentage) ? $data['point']->percentage : 0;
        $data['is_new'] = isset($data['point']->percentage) ? 'false' : 'true';

        return view('masterdata::point_setting.edit', $data);
    }

    public function editPointUpdate(Request $request, $fleetid)
    {
        $credentials = $request->validate([
            'percentage'      => ['required', 'string'],
            'isnew'      => ['required'],
        ]);
        
        $updateData['percentage'] = $request->percentage;

        if (strval($request->isnew) === 'false') {
            $updateItem = PointSetting::updatePoint($fleetid, $updateData);
        } else {
            $updateData['uuid'] = generateUuid();
            $updateData['fleet_type'] = $fleetid;
            $updateItem = PointSetting::saveNewPoint($updateData);
        }
        

        if ($updateItem) {
            return back()->with('success', 'Point setting bagian berhasil diubah!');
        }

        return back()->with('failed', 'Point setting bagian gagal diubah!');   
    }
}
