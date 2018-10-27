<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RuleController extends Controller
{
    public function ruleList(Request $request)
    {
        $query = DB::table('cx_rule')->where('parent_id', 0);
        if ($request->start) $query = $query->where('add_time', '>=', $request->start);
        if ($request->end) $query = $query->where('add_time', '<=', $request->end);
        if ($request->rule_name) $query = $query->where('rule_name', 'like', '%' . $request->rule_name . '%');

        $rule_list = $query->orderBy('sort', 'ASC')->paginate(10);

        $ids = $query->pluck('id')->toArray();
        $rule_son_list = DB::table('cx_rule')->whereIn('parent_id', $ids)->orderBy('sort', 'ASC')->get();
        foreach ($rule_list as &$value) {
            foreach ($rule_son_list as $v) {
                if ($v->parent_id == $value->id) {
                    $value->son_list[] = $v;
                }
            }
        }

        return view('admin/ruleList', ['list' => $rule_list, 'search_params' => $request->all()]);

    }

    public function ruleAdd(Request $request)
    {
        if ($request->isMethod('get')) {
            // 获取 顶层 权限
            $rule_list = DB::table('cx_rule')->where('parent_id', 0)->get();
            return view('admin/ruleAdd', ['list' => $rule_list]);
        } else {

            $validator = Validator::make($request->all(), [
                'rule_name' => 'required|unique:cx_rule,rule_name',
                'route_name' => 'required|unique:cx_rule,route_name',
            ], [
                'rule_name.required' => '权限名必填',
                'rule_name.unique' => '权限名已存在',
                'route_name.required' => '访问路由必填',
                'route_name.unique' => '访问路由已存在',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            // 获取 同一个 parent_id 下的 最大 sort，新插入数据 默认 当前 最大sort + 1
            $max_sort = DB::table('cx_rule')->where('parent_id', $request->parent_id)->max('sort');

            $insert_data = $request->all();
            unset($insert_data['_token']);
            $insert_data['sort'] = $max_sort + 1;

            DB::table('cx_rule')->insert($insert_data);

            return $this->showJson('0000', '添加成功');
        }

    }

    public function ruleEdit(Request $request)
    {

    }

    public function ruleDelete(Request $request)
    {

    }

    public function changeSort(Request $request)
    {
        // 排序规则， sort 字段 越小 越靠前，sort 字段 最小 为 1
        $id = $request->id;
        $type = $request->type;
        if (!$id || !$type) return $this->showJson('9999', '参数错误');

        $rule_info = DB::table('cx_rule')->where('id', $id)->first();
        if (!$rule_info) return $this->showJson('9999', '权限不存在');

        // 先计算 sort ，sort 在同一个父类的同一个 级别中是唯一的，所以 可以通过 parent_id 和 sort 两个字段组合 确定 一条唯一的记录
        $paren_id = $rule_info->parent_id;
        $sort_old = $rule_info->sort;
        if ($type == 'up') { // 上移，相当于 当前记录 与 同级的 上一条记录 互换位置
            $sort_new = $sort_old - 1;
        } else { // 下移， 相当于 当前记录 与 同级的 下一条记录 互换位置
            $sort_new = $sort_old + 1;
        }

        // $sort_new 和 sort_old 就是 要互换的两个位置，对应的就是两条记录

        // 先更新 两条数据中的 被换位 的那条数据
        DB::table('cx_rule')->where('parent_id', $paren_id)->where('sort', $sort_new)->update(['sort' => $sort_old]);
        // 更新主动要换位的这条数据
        DB::table('cx_rule')->where('id', $id)->update(['sort' => $sort_new]);

        // 返回同个 parant_id 的所有记录
        $rule_list = DB::table('cx_rule')->where('parent_id', $paren_id)->orderBy('sort', 'ASC')->get();

        return $this->showJson('0000', '操作成功', ['parent_id' => $paren_id, 'list' => $rule_list]);
    }
}
