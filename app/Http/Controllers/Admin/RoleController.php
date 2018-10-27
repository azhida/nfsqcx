<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function roleList(Request $request)
    {
        if ($request->isMethod('get')) {

//            dd($request->all());
            $query = DB::table('cx_role');
            if ($request->start) {
                $query = $query->where('add_time', '>=', $request->start);
            }
            if ($request->end) {
                $query = $query->where('add_time', '<=', $request->end);
            }
            if ($request->role_name) {
                $query = $query->where('role_name', 'like', '%' . $request->role_name . '%');
            }

            $role_list = $query->orderBy('id', 'DESC')->paginate(10);

            return view('admin/roleList', ['list' => $role_list, 'search_params' => $request->all()]);
        } else {



        }

    }

    public function roleAdd(Request $request)
    {
        if ($request->isMethod('get')) {

            $rule_list = $this->getRoleListOfLevel();

            return view('admin/roleAdd', ['list' => $rule_list]);

        } else {

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:cx_role,role_name',
            ], [
                'name.required' => '角色名必填',
                'name.unique' => '角色名已存在',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $res = DB::transaction(function () use ($request) {
                try {
                    // 写入角色表
                    $role_name = $request->name ?? '';
                    $role_desc = $request->desc ?? '';

                    $insert_data = [
                        'role_name' => $role_name,
                        'role_desc' => $role_desc,
                        'add_time' => date('Y-m-d H:i:s', time()),
                    ];

                    $role_id = DB::table('cx_role')->insertGetId($insert_data);

                    // 写入 角色权限关系表
                    $role_rule_insert_data = [];
                    foreach ($request->rule_ids as $rule_id) {
                        $temp_data = [
                            'role_id' => $role_id,
                            'rule_id' => $rule_id,
                            'add_time' => date('Y-m-d H:i:s', time()),
                        ];
                        array_push($role_rule_insert_data, $temp_data);
                    }
                    DB::table('cx_role_rule')->insert($role_rule_insert_data);

                    return ['code' => '0000', 'mes' => '添加成功'];
                } catch (\Exception $exception) {
                    return ['code' => '9998', 'mes' => '添加成功'];
                }
            });

            return $this->showJson($res['code'], $res['mes']);
        }
    }

    public function roleEdit(Request $request)
    {
        if ($request->isMethod('get')) {

            $rule_list = $this->getRoleListOfLevel();

            // 角色信息
            $role_info = DB::table('cx_role')->where('id', $request->id)->first();

            // 角色拥有的所有权限 id
            $role_rule_ids = DB::table('cx_role_rule')->where('role_id', $request->id)->pluck('rule_id')->toArray();
            $role_rule_ids = implode('|', $role_rule_ids);

            return view('admin/roleEdit', ['list' => $rule_list, 'role_info' => $role_info, 'role_rule_ids' => $role_rule_ids]);

        } else {

            $count = DB::table('cx_role')->where('role_name', $request->role_name)->where('id', '<>', $request->id)->count();
            if ($count > 0) $this->showJson('9999', '角色名已存在');

            DB::table('cx_role')->where('id', $request->id)
                ->update(['role_name' => $request->role_name, 'role_desc' => $request->role_desc, 'update_time' => date('Y-m-d H:i:s')]);

            $role_rule_insert_data = [];
            foreach ($request->rule_ids as $v) {
                $role_rule_insert_data[] = ['role_id' => $request->id, 'rule_id' => $v];
            }
            DB::table('cx_role_rule')->where('role_id', $request->id)->delete();
            DB::table('cx_role_rule')->insert($role_rule_insert_data);

            return $this->showJson('0000', '更新成功');

        }
    }

    public function roleDetele(Request $request)
    {
        DB::table('cx_role')->whereIn('id', $request->ids)->where('id', '>', 1)->delete();
        return $this->showJson('0000', '删除成功');
    }
    
    // 查出 角色的层级关系 列表
    public function getRoleListOfLevel()
    {
        // 先查出 顶级菜单
        $rule_list = DB::table('cx_rule')->where('parent_id', 0)->orderBy('sort', 'DESC')->get()->toArray();
        // 在查出 二级菜单
        $rule_son_list = DB::table('cx_rule')->whereIn('parent_id', array_column($rule_list, 'id'))->orderBy('sort', 'ASC')->get()->toArray();

        foreach ($rule_list as &$value) {
            foreach ($rule_son_list as $v) {
                if ($v->parent_id == $value->id) {
                    $value->son_list[] = $v;
                }
            }
        }

        return $rule_list;
    }
}
