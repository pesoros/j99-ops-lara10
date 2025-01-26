<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PointUsers;

class MasterdataPointUsersController extends Controller
{
    public function searchUsers(Request $request)
    {
        $data['title'] = 'User Point';
        $data['email'] = $request->query('email');

        if ($request->query('email')) {
            $data['userdata'] = PointUsers::getUser($request->query('email'));
            if (isset($data['userdata']->user_id)) {
                $data['userpointhistory'] = PointUsers::getUserPointHistory($data['userdata']->user_id);
            }
        }

        return view('masterdata::point_users.index', $data);
    }
}
