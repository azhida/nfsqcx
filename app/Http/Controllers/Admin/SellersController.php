<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SellersController extends Controller
{
    public function sellersList(Request $request)
    {
        $query = DB::table('cx_saler');
        if ($request->start) $query = $query->where('create_time', '>=', strtotime($request->start));
        if ($request->end) $query = $query->where('create_time', '<=', strtotime($request->end));
        if ($request->account) $query = $query->where('account', 'like', '%' . $request->account . '%');
        if ($request->office_id) $query = $query->where('office_id', $request->office_id);

        $saller_list = $query->orderBy('id', 'DESC')->paginate(10);

        // 办事处列表
        $office_list = DB::table('cx_office')->orderBy('id', 'DESC')->get();

        if (!empty($office_list)) {
            foreach ($saller_list as &$value) {
                $value->add_time = date('Y-m-d H:i:s', $value->create_time);
                $value->update_time = date('Y-m-d H:i:s', $value->update_time);
                foreach ($office_list as $v) {
                    if ($v->id == $value->office_id) {
                        $value->office_name = $v->name;
                    }
                }
            }
        }

        // 办事处

        return view('admin/sellersList', ['list' => $saller_list, 'search_params' => $request->all(), 'office_list' => $office_list]);
    }

    public function sellersAdd(Request $request)
    {
        if ($request->isMethod('get')) {

        } else {

        }
    }

    public function sellersEdit(Request $request)
    {
        if ($request->isMethod('get')) {

        } else {

        }

    }

    public function sellersDelete(Request $request)
    {
        DB::table('cx_saler')->whereIn('id', $request->ids)->delete();
        return $this->showJson('0000', '操作成功');
    }

}
