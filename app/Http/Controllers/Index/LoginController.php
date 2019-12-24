<?php

namespace App\Http\Controllers\Index;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index()
    {
        return view('index/index');
    }
    public function login(Request $request)
    {
        if ($request->isMethod('get')) {

            if(Session::has('office_id')) { // 已登录
                return view('index/index');
            } else { // 未登录
                return view('index/login');
            }

        } else {

            // 接收校验参数
            $validator = Validator::make($request->all(), [
                'office_id' => 'required',
                'phone' => 'required',
                'sms_code' => 'required',
            ], [
                'office_id.required' => '请选择办事处',
                'phone.required' => '手机号码必填',
                'sms_code.required' => '验证码必填',
            ]);

            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            // 手机号码 和 验证码
            $sms_code = DB::table('cx_sms_code')->where('phone', $request->phone)->where('code', $request->sms_code)->first();
            if (!$sms_code) return $this->showJson('9999', '验证码错误');
            if ($sms_code->created_at < Carbon::now()->addMinutes(-$sms_code->cache_time)) return $this->showJson('9999', '验证码已失效，请重新获取');

            Session::put('office_id', $request->office_id);
            Session::put('phone', $request->phone);
            return $this->showJson("0000","登陆成功");

        }
    }

    // 退出登录
    public function logout()
    {
        Session::forget('office_id');
        Session::forget('phone');

        return redirect('index/login', 302); // 302是临时重定向，301是永久重定向
    }

    public function getOffices()
    {
        $offices = DB::table('cx_office')->select('name as title', 'id as value')->get();
        return $this->showJson("0000","操作成功", $offices);
    }

    // 经销商
    public function getDealers()
    {
        $office_id = Session::get('office_id');
        $offices = DB::table('cx_dealers')->select('dealers_name as title', 'id as value')->where('office_id', $office_id)->get();
        return $this->showJson("0000","操作成功", $offices);
    }

    // 渠道
    public function getChannels()
    {
        $_list = DB::table('cx_sales')->select('sales_name as title', 'id as value')->get();
        return $this->showJson("0000","操作成功", $_list);
    }

    // 品牌（品项）
    public function getActivityItems()
    {
        $_list = DB::table('cx_activity_item')->select('name as title', 'id as value')->get();
        return $this->showJson("0000","操作成功", $_list);
    }



}
