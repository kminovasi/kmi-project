<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class SessionController extends Controller
{
    function index()
    {
        return view('layouts.login');
    }

    private function single_auth($data)
    {
        $ch = curl_init(env('SSO_URL'));
        $curlDefault = array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'token:$2y$10$3DDqyL./M7Qn4h426rnOAux3H20.VWXE2sqO83tk6n24QDtswGwF.',  // when akses using token
            ),
        );
        // execute!
        curl_setopt_array($ch, $curlDefault);
        $html = curl_exec($ch);
        $response = json_decode($html, true);
        $err = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $response;
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'username' => ['required'],
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        $data = array(
            'username' => $request->username,
            'password' => $request->password,
            'token' => env('SSO_TOKEN'),
        );

        $remember = $request->remember_me;

        /* Has Active if Use SSO Provide */
        /*        $respon = $this->single_auth($data);

                if(!is_null($respon) && $respon['success']) {

                    $user = User::where('username',$credentials['username'])->first();
                    if(is_null($user)){
                        throw ValidationException::withMessages([
                            'username' => __('auth.nofound'),
                        ]);
                    }


                    Auth::login($user, $remember);
                    return redirect('/');
                }*/

        // ketika fail dari sso
        $user = User::where('username', $credentials['username'])->first();
        // dd($user);
        if (is_null($user)) {
            throw ValidationException::withMessages([
                'username' => __('auth.nofound'),
            ]);
        }



        if (!Auth::guard('web')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }
        if ($user->role === 'Admin' || $user->role === 'Superadmin') {
            return redirect()->intended('dashboard');
        }

        return redirect()->route('homepage');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

















    // function login(Request $request)
    // {
    //     $cred = $request->validate([
    //         'email' => 'required|email:dns',
    //         'password' => 'required'
    //     ]);

    //     if (Auth::attempt($cred)) {
    //         $request->session()->regenerate();
    //         return redirect()->intended('page/dashboard');
    //     }

    //     return back()->with('loginErr', 'Login failed.');
    // }
}
