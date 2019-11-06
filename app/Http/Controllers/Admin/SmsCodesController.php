<?php

namespace App\Http\Controllers\Admin;

use App\Models\SmsCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SmsCodesController extends Controller
{
    public function index(Request $request)
    {
        $query = SmsCode::query();

        if ($request->start) $query->where('create_time', '>=', strtotime($request->start));
        if ($request->end) $query->where('create_time', '<=', strtotime($request->end) + 24 * 60 * 60);
        if ($request->phone) $query->where('phone', 'like', '%' . $request->phone . '%');
        if ($request->status != '') $query->where('status', $request->status);

        $list = $query->orderBy('id', 'DESC')->paginate(20);

        return view('admin/smsCodeList', ['list' => $list, 'search_params' => $request->all()]);
    }
}
