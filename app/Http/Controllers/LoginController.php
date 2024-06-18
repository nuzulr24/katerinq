<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// additional modules
use App\Helpers\MailerHelper as Mailers;
use App\Enums\GlobalEnum;
use App\Models\User;
use App\Models\UserManager;
use App\Models\LogActivites;

use Ramsey\Uuid\Uuid;

class LoginController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'CMSQ',
            'subtitle' => 'Login',
        ];

        return view('auth/login', compact('data'));
    }

    public function proses_login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            switch($user->level) {
                case GlobalEnum::isAdmin:
                    LogActivites::default([
                        'causedBy' => $user->id,
                        'logType' => GlobalEnum::LogOfLogin,
                        'withContent' => [
                            'status' => 'add',
                            'text' => 'Login as ' . $user->name,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'time' => date('Y-m-d H:i:s')
                        ]
                    ]);
                    return redirect()->intended('app/dashboard');
                    break;
                case GlobalEnum::isMembers:
                    LogActivites::default([
                        'causedBy' => $user->id,
                        'logType' => GlobalEnum::LogOfLogin,
                        'withContent' => [
                            'status' => 'add',
                            'text' => 'Login as ' . $user->name,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'time' => date('Y-m-d H:i:s')
                        ]
                    ]);
                    return redirect()->intended('user');
                    break;
                case 2:
                        LogActivites::default([
                            'causedBy' => $user->id,
                            'logType' => GlobalEnum::LogOfLogin,
                            'withContent' => [
                                'status' => 'add',
                                'text' => 'Login as ' . $user->name,
                                'ip_address' => $request->ip(),
                                'user_agent' => $request->userAgent(),
                                'time' => date('Y-m-d H:i:s')
                            ]
                        ]);
                        return redirect()->to(site_url('seller', '/'));
                        break;
            }
        }

        return back()->withErrors([
            'email' => 'There is no valid email exists!',
            'password' => 'Look like there is wrong password!'
        ])->withInput();
    }

    public function logout(Request $request)
    {
       $request->session()->flush();
       Auth::logout();
       return redirect('/');
    }

    public function register()
    {
        $data = [
            'subtitle' => 'Register',
        ];

        return view('auth/register', compact('data'));
    }

    public function storeRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'level' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();
        $userEmailToken = md5(Str::random(25));
        $user = new User([
            'name' => $input['name'],
            'username' => Str::before($input['email'], '@') . rand(100, 999),
            'email' => $input['email'],
            'level' => $input['level'],
            'email_verified_token' => NULL,
            'password' => bcrypt($input['password']),
            'balance' => 0,
            'phone' => NULL,
            'income' => 0,
            'status' => 1
        ]);

        $check = User::where('email', $input['email'])->count();
        if($check == 0) {
            if($user->save()) {
                return redirect()->route('login')->with('swal', swal_alert('success', 'Kamu berhasil mendaftar! Silahkan cek email untuk verifikasi akun anda'));
            } else {
                return redirect()->back()->with('swal', swal_alert('error', 'Kamu gagal mendaftar!'))->withInput();
            }
        } else {
            return redirect()->back()->with('swal', swal_alert('error', 'Akun sudah terdaftar!'))->withInput();
        }
    }

    public function verify($token)
    {
        $userAccount = User::where('email_verified_token', $token)->first();
        if($token == null) {
            return redirect()->route('login')->with('swal', swal_alert('error', 'Token tidak ditemukan!'));
        } elseif(empty($userAccount) || is_null($userAccount)) {
            return redirect()->route('login')->with('swal', swal_alert('error', 'Token sudah digunakan!'));
        } elseif($userAccount->status == 1 || $userAccount->email_verified_at != null) {
            return redirect()->route('login')->with('swal', swal_alert('error', 'Akun sudah terverifikasi!'));
        } else {

            // check before update
            $user = User::find($userAccount->id);
            $user->email_verified_token = null;
            $user->email_verified_at = now();
            $user->status = 1;

            // update
            $user->save();
            return redirect()->route('login')->with('swal', swal_alert('success', 'Email telah berhasil diverifikasi, anda dapat menggunakan layanan'));
        }
    }

    public function forgot()
    {
        $data = [
            'subtitle' => 'Lupa Kata Sandi',
        ];

        return view('auth/forgot_password', compact('data'));
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();
        $userEmailToken = md5(Str::random(25));
        $user = new UserManager([
            'uuid' => Uuid::uuid4(),
            'email' => $input['email'],
            'token' => $userEmailToken,
            'isUsed' => 0,
        ]);

        $check = User::where('email', $input['email'])->count();
        $userName = User::where('email', $input['email'])->first()->name;
        if($check == 1) {
            $sendEmail = Mailers::to($input['email'], false, 'email.auth.forgot_password', [
                'message' => 'You have been successfully requested! Please check your email to reset your password',
                'subject' => 'Forgot Password',
                'name' => $userName,
                'token' => $userEmailToken
            ]);
            if($sendEmail) {
                $user->save();
                return redirect()->route('login')->with('swal', swal_alert('success', 'Kamu berhasil mengirim permintaan reset password! Silahkan cek email untuk verifikasi akun anda'));
            } else {
                return back()->withErrors([
                    'email' => 'Something went wrong!',
                ])->withInput();
            }
        } else {
            return back()->withErrors([
                'email' => 'Found valid email exists!',
            ])->withInput();
        }
    }

    public function reset($token)
    {

        $findTokenByUsed = UserManager::where('token', $token)->first();
        if($findTokenByUsed->isUsed == 1) {
            return redirect()->route('login')->with('swal', swal_alert('error', 'Token sudah digunakan!'));
        } elseif(hasExpired($findTokenByUsed->created_at)) {
            return redirect()->route('login')->with('swal', swal_alert('error', 'Token expired!'));
        }

        $data = [
            'subtitle' => 'Reset Password',
        ];

        return view('auth/reset_password', compact('data','token'));
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
            'retype_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if($request->password != $request->retype_password) {
            return redirect()->back()->with('swal', swal_alert('error', 'Password tidak sama!'))->withInput();
        }

        $input = $request->all();
        $userEmailbyToken = UserManager::where('token', $input['token'])->first();
        $findUserByEmail = User::where('email', $userEmailbyToken->email)->first();

        // check expired token 60 minutes
        if(hasExpired($findUserByEmail->created_at)) {
            return redirect()->route('login')->with('swal', swal_alert('error', 'Token sudah kadaluarsa!'));
        } else {

            // update password
            $findUserByEmail->password = bcrypt($input['password']);
            $findUserByEmail->save();

            // update token
            $userEmailbyToken->isUsed = 1;
            $userEmailbyToken->save();

            return redirect()->route('login')->with('swal', swal_alert('success', 'Password telah berhasil diubah'));
        }
    }

}
