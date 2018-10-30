<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DealersController extends Controller
{
    public function dealersList(Request $request)
    {
        $query = DB::table('cx_dealers');
        if ($request->start) $query = $query->where('create_time', '>=', strtotime($request->start));
        if ($request->end) $query = $query->where('create_time', '<=', strtotime($request->end));
        if ($request->dealers_name) $query = $query->where('dealers_name', 'like', '%' . $request->dealers_name . '%');
        if ($request->office_id) $query = $query->where('office_id', $request->office_id);

        $dealer_list = $query->orderBy('id', 'DESC')->paginate(10);

        // 办事处列表
        $office_list = DB::table('cx_office')->orderBy('id', 'DESC')->get();

        if (!empty($office_list)) {
            foreach ($dealer_list as &$value) {
                $value->add_time = date('Y-m-d H:i:s', $value->create_time);
                $value->update_time = date('Y-m-d H:i:s', $value->update_time);
                foreach ($office_list as $v) {
                    if ($v->id == $value->office_id) {
                        $value->office_name = $v->name;
                    }
                }
            }
        }

        return view('admin/dealersList', ['list' => $dealer_list, 'search_params' => $request->all(), 'office_list' => $office_list]);
    }

    public function dealersAdd(Request $request)
    {
        if ($request->isMethod('get')) {
            $office_list = DB::table('cx_office')->orderBy('id', 'DESC')->get();
            return view('admin/dealersAdd', ['list' => $office_list]);
        } else {

            $validator = Validator::make($request->all(), [
                'dealers_name' => 'required|unique:cx_dealers,dealers_name',
                'office_id' => 'required|numeric|min:1',
            ], [
                'dealers_name.required' => '经销商必填',
                'dealers_name.unique' => '经销商已存在',
                'office_id.required' => '办事处必选',
                'office_id.numeric' => '办事处必选',
                'office_id.min' => '办事处必选',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $insert_data = [
                'dealers_name' => $request->dealers_name,
                'office_id' => $request->office_id,
                'create_time' => time(),
                'update_time' => time(),
            ];

            $id = DB::table('cx_dealers')->insertGetId($insert_data);
            if ($id) {
                return $this->showJson('0000', '操作成功');
            } else {
                return $this->showJson('9999', '操作失败');
            }

        }
    }

    public function dealersEdit(Request $request)
    {
        if ($request->isMethod('get')) {

            $info = DB::table('cx_dealers')->where('id', $request->id)->first();
            $office_list = DB::table('cx_office')->orderBy('id', 'DESC')->get();

            return view('admin/dealersEdit', ['list' => $office_list, 'info' => $info]);

        } else {

            $update_data = $request->all();
            unset($update_data['_token']);
            if ($request->account) {
                // 先查 修改的 账号 是否已经存在
                $count = DB::table('cx_dealers')->where('id', '<>', $request->id)->where('dealers_name', $request->dealers_name)->count();
                if ($count) return $this->showJson('9999', '经销商已经在');
            }

            DB::table('cx_dealers')->where('id', $request->id)->update($update_data);

            return $this->showJson('0000', '操作成功');

        }

    }

    public function dealersDelete(Request $request)
    {
        DB::table('cx_dealers')->whereIn('id', $request->ids)->delete();
        return $this->showJson('0000', '操作成功');
    }
}
