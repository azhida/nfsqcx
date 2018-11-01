<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleschannelController extends Controller
{
    public function saleschannelList(Request $request)
    {
        $query = DB::table('cx_sales_channel');
        if ($request->start) {
            $query = $query->where('create_time', '>=', strtotime($request->start));
        }
        if ($request->end) {
            $query = $query->where('create_time', '<=', strtotime($request->end));
        }
        if ($request->sales_name) {
            $query = $query->where('sales_name', 'like', '%' . $request->sales_name . '%');
        }

        $office_list = $query->orderBy('id', 'DESC')->paginate(10);

        return view('admin/saleschannelList', ['list' => $office_list, 'search_params' => $request->all()]);

    }

    public function saleschannelAdd(Request $request)
    {
        if ($request->isMethod('get')) {

            return view('admin/saleschannelAdd');

        } else {

            $validator = Validator::make($request->all(), [
                'sales_name' => 'required|unique:cx_sales_channel,sales_name',
            ], [
                'sales_name.required' => '销售渠道必填',
                'sales_name.unique' => '销售渠道已存在',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $insert_data = [
                'sales_name' => $request->sales_name,
                'create_time' => time(),
                'update_time' => time(),
            ];

            $id = DB::table('cx_sales_channel')->insertGetId($insert_data);
            if ($id) {
                return $this->showJson('0000', '操作成功');
            } else {
                return $this->showJson('9999', '操作失败');
            }

        }

    }

    public function saleschannelEdit(Request $request)
    {
        if ($request->isMethod('get')) {

            $info = DB::table('cx_sales_channel')->where('id', $request->id)->first();

            return view('admin/saleschannelEdit', ['info' => $info]);

        } else {

            $update_data = $request->all();
            unset($update_data['_token']);
            if ($request->sales_name) {
                // 先查 修改的 账号 是否已经存在
                $count = DB::table('cx_sales_channel')->where('id', '<>', $request->id)->where('sales_name', $request->sales_name)->count();
                if ($count) return $this->showJson('9999', '办事处已经在');
            }

            $update_data['update_time'] = time();

            DB::table('cx_sales_channel')->where('id', $request->id)->update($update_data);

            return $this->showJson('0000', '操作成功');

        }

    }

    public function saleschannelDelete(Request $request)
    {
        DB::table('cx_sales_channel')->whereIn('id', $request->ids)->delete();
        return $this->showJson('0000', '操作成功');
    }
}
