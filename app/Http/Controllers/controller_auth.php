<?php

namespace App\Http\Controllers;

use http\Client\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserType;
use App\helper\help;
use Illuminate\Support\Facades\Cache;

class controller_auth extends Controller
{
    public function register(Request $request)
    {
        $data = User::where('phone', $request->phone)->where('activated', false)->get();
        if (count($data) > 0) {
            User::where('phone', $request->phone)->where('activated', false)->delete();
        }
        $validator = Validator::make($request->all(), [
            'phone' => 'required|unique:users|max:255|digits:11',
            'password' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error', $validator->messages()]);
        }
        $user = new User([
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'activated' => false,
            'address' => '',
            'level' => UserType::client,
        ]);
        $user->save();
        $token = JWTAuth::fromUser($user);
        $verificationCode = rand(100000, 999999);
        Cache::put('verification_code_' . $request->phone, $verificationCode, now()->addMinutes(5));
        $text="
        سلام
        {$verificationCode} کد تائید شما در سایت طهوریان
        لغو 11
        ";
        help::send_sms($text,array($request->phone));
        return response()->json(['success' => true,  'token'=> $token], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|max:255|digits:11',
            'password' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error', $validator->messages()]);
        }
        $data = User::where('phone', $request->phone)->where('activated', false)->get();
        if (count($data) > 0) {
            User::where('phone', $request->phone)->where('activated', false)->delete();
        }

        $credentials = $request->only('phone', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json(['success' => true, ['token' => $token]]);
    }

    public function active_account(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'verification_code' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error', $validator->messages()]);
        }
        if(!Auth::guard('api')->check()){
            return response()->json(['error'=>'0'],401);
        }
        $token = JWTAuth::parseToken();
        $user = $token->authenticate();
        $phone = $user->phone;
        $enteredCode = $request->verification_code;

        $storedCode = Cache::get('verification_code_' . $phone);

        if ($storedCode && $enteredCode == $storedCode) {
            User::where('phone',$phone)->update(['activated'=>true]);
            Cache::forget('verification_code_' . $phone); // پاک کردن کد از کش
            // ارسال پاسخ موفقیت آمیز به کاربر از طریق API Response
            $data = help::add_user_in_crm([
                'name'=>'تعیین نشده',
                'last_name'=>'تعیین نشده',
                'company'=>'تعیین نشده',
                'stable_phone'=>'تعیین نشده',
                'phone'=>$phone,
                'gender'=>'0',
                'job'=>'تعیین نشده',
                'email'=>'تعیین نشده',
                'address'=>'تعیین نشده',
                'birthdate'=>'1000-10-10',
                'marriagedate'=>'1000-10-10',
                'partnerdate'=>'1000-10-10',
                'ncode'=>'تعیین نشده',
                'explain'=>'',
                'mobileintroducer'=>'تعیین نشده',
            ]);
            $text="
        ثبت نام شما در سایت طهوریان با موفقیت انجام شد.
        با تشکر از شما
        لغو 11
        ";
            help::send_sms($text,[$phone]);
            return response()->json(['status' => true], 200);
        } else {
            // کد تایید نامعتبر است
            // ارسال پاسخ ناموفقیت آمیز به کاربر از طریق API Response
            return response()->json(['status' => false], 200);
        }

    }
    public function get_code_again(Request $request){
        if(!Auth::guard('api')->check()){
            return response()->json(['error'=>'0'],401);
        }
        $verificationCode = rand(100000, 999999);
        $token = JWTAuth::parseToken();
        $user = $token->authenticate();
        $phone = $user->phone;
        if($user->activated==false){
        Cache::forget('verification_code_' . $phone);
        Cache::put('verification_code_' . $request->phone, $verificationCode, now()->addMinutes(5));
        $text="
        سلام
        {$verificationCode} کد تائید شما در سایت طهوریان
        لغو 11
        ";
        help::send_sms($text,[$phone]);
        return response()->json(['status'=>"3"]);
        }else{
            return response()->json(['status'=>'1']);
        }
    }
        public function update_user(Request $request){
            if(!Auth::guard('api')->check()){
                return response()->json(['error'=>'0'],401);
            }
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|max:255|string',
                'lastname' => 'required|max:255|string',
                'companyname' => 'required| max:255|string',
                'phone' => 'required|numeric',
                'gender' => 'required|max:255|numeric',
                'job' => 'required|max:255|string',
                'mail' => 'required|max:255|email',
                'address' => 'required|max:255|string',
                'birthdate' => 'required|max:255|date',
                'marriagedate' => 'required|max:255|date',
                'partnerdate' => 'required|max:255|date',
                'ncode' => 'required|numeric',
                'explain' => 'max:1000',
            ]);
            if ($validator->fails()) {
                return response()->json(['error', $validator->messages()]);
            }
            $token = JWTAuth::parseToken();
            $user = $token->authenticate();
            if($user->activated == true){
                $data = help::update_user([
                    'name'=>$request->firstname,
                    'last_name'=>$request->lastname,
                    'company'=>$request->companyname,
                    'stable_phone'=>$request->phone,
                    'phone'=>$user->phone,
                    'gender'=>$request->gender,
                    'job'=>$request->job,
                    'email'=>$request->mail,
                    'address'=>$request->address,
                    'birthdate'=>$request->birthdate,
                    'marriagedate'=>$request->marriagedate,
                    'partnerdate'=>$request->partnerdate,
                    'ncode'=>$request->ncode,
                    'explain'=>$request->explain,
                    'mobileintroducer'=>'',
                    'idcustomer'=>$user->phone
                ]);
                dd($data);
                return response()->json(['status'=>"2"]);
            }else{
                return response()->json(['status'=>1]);
            }
        }

}
