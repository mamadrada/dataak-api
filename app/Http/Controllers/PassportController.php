<?php

namespace App\Http\Controllers;

use App\Traits\ActivityTraits;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use App\User;
use DB;
use Auth;
use Sentinel;

class PassportController extends Controller
{
    use ActivityTraits, UploadImageTrait;

    public function register(Request $request)
    {

        $this->validate($request, [
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|unique:users',
            'password' => 'required|min:6',
            'image' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $user = new User();
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->mobile = $request->input('mobile');
            $user->active = $request->input('active');
            $user->password = bcrypt($request->input('password'));
            if ($request->has('image')) {
                $image = $request->file('image');
                $fileUrl = $this->uploadImage($image, 'users', $request->title);
                $user->image = $fileUrl;
            }
            $user->save();
            DB::commit();
            $token = $user->createToken('dataakUser')->accessToken;
            return response()->json(['token' => $token], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['error' => $exception->getMessage()], 403);
        }

    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);
        $login_type = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'mobile';
        $request->merge([
            $login_type => $request->input('username')
        ]);
        if (Auth::attempt($request->only($login_type, 'password'))) {
            $this->logLoginDetails(Auth::user());
            $token = auth()->user()->createToken('dataakUser')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised'], 401);
        }
    }

}
