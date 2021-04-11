<?php

namespace App\Http\Controllers\Admin;

use App\Model\User;
use App\Org\code\Code;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    //
    public function login()
    {
        return view('admin.login');
    }

    public function code()
    {
        $code = new Code();
        return $code->make();
    }
    public function captcha()
    {
        $phrase = new PhraseBuilder;
        // 设置验证码位数
        $code = $phrase->build(4);
        // 生成验证码图片的Builder对象,配置相应属性
        $builder = new CaptchaBuilder($code, $phrase);
        // 设置背景颜色
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(10);
        $builder->setMaxFrontLines(10);
        // 可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        // 获取验证码的内容
        $phrase = $builder->getPhrase();
        // 生成图片
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-Type:image/jpeg');
        $builder->output();
        exit;
    }

    public function doLogin(Request $request)
    {
        $input     = $request->except('_token');
        $rule      = [
            'username' => 'required|between:4,18',
            'password' => 'required|between:4,18|alpha_dash',
        ];
        $msg       = [
            'username.required'   => '用户名必填',
            'username.between'    => '用户名长度在4-8之间',
            'password.required'   => '密码必填',
            'password.between'    => '密码长度在4-8之间',
            'password.alpha_dash' => '密码必须数字字母下划线',
        ];
        $validator = Validator::make($input, $rule, $msg);
        if ($validator->fails()) {
            return redirect('admin/login')->withErrors($validator)->withInput();
        }

        if( strtolower($input['code']) != strtolower(session()->get('code')) ){
            return redirect('admin/login')->with('errors','验证码错误');
        }
        $user = User::where('user_name',$input['username'])->first();

        if(!$user){
            return redirect('admin/login')->with('errors','用户名错误');
        }

        if(decrypt($user->user_pass) != $input['password']){
            return redirect('admin/login')->with('errors','密码错误');
        }

        session()->put('user',$user);

        return redirect('admin/index');

    }

    public function jm()
    {
        $str = '123456';
        $crypt_str = 'eyJpdiI6InJVS0FYU0JpRHBsOTRtUzRCK0ZZMnc9PSIsInZhbHVlIjoibk9DK2JqWWRYUWJEMzJBMlEreUhjdz09IiwibWFjIjoiMDI2YTY5OGU2ODM1ZGFmNTVlZTcyMTY3ZDRjYzRmN2RiMTk2ZWQzZmRhNzhkYjRmNGJjNTEzMWU3Mzc2NDA1OCJ9';
//        $crypt_str = Crypt::encrypt($str);
        if(crypt::decrypt($crypt_str) == $str){
            return '密码一致';
        }
        return $crypt_str;
    }

    public function index()
    {
        return view('admin.index');
    }

    public function welcome()
    {
        return view('admin.welcome');
    }

    public function logout()
    {
        session()->flush();
        return redirect('admin/login');
    }
    public function noaccess()
    {
        return view('errors.noaccess');
    }
}
