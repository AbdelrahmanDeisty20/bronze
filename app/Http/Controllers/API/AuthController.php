<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\Address;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = validator()->make(request()->all(), [
            "email" => "required|email|unique:clients",
            "password" => "required|confirmed",
            'first_name' => 'required',
            'second_name' => 'required',
            'phone' => 'required'
        ]);
        if ($validator->fails()) {
            return resposeJison(0, $validator->errors()->first(), $validator->errors()->first());
        }
        $random = Str::random(40);
        $client = Client::create($request->all());
        $client->api_token = $random;
        $client->save();
        return resposeJison(1, 'تم انشاء الحساب بنجاح', [
            'api_token' => $client->api_token,
            'client' => $client
        ]);
    }
    public function login(Request $request)
    {
        $validator = validator()->make(request()->all(), [
            'identifier' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return resposeJison(0, $validator->errors()->first(), $validator->errors());
        }
        $identifier = $request->input('identifier');
        $client = Client::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();
        if (Hash::check($request->password, $client->password)) {
            return resposeJison(1, 'login successfully', [
                'api_token' => $client->api_token,
                'client' => $client
            ]);
        } else {
            return resposeJison(0, 'this acount is not exist');
        }
    }
    public function registerToken(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'token' => 'required',
            'platform' => 'required|in:android,ios'
        ]);
        if ($validator->fails()) {
            $data = $validator->errors();
            return resposeJison(status: 0, msg: $validator->errors()->first(), data: $data);
        }
        Token::where('token', $request->token)->delete();
        $request->user()->tokens()->create($request->all());
        return resposeJison(status: 1, msg: 'token register succwssfully');
    }
    public function removeToken(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            $data = $validator->errors();
            return resposeJison(status: 0, msg: $validator->errors()->first(), data: $data);
        }
        Token::where('token', $request->token)->delete();
        return resposeJison(status: 1, msg: 'تم الحذف بنجاح');
    }
    public function profile(Request $request)
    {
        $validator = validator()->make(request()->all(), [
            'first_name' => Rule::unique('clients')->ignore($request->user()->id),
            'second_name' => Rule::unique('clients')->ignore($request->user()->id),
            'email' => Rule::unique('clients')->ignore($request->user()->id),
            'phone' => Rule::unique('clients')->ignore($request->user()->id),
            'birth_date' => Rule::unique('clients')->ignore($request->user()->id),
        ]);
        if ($validator->fails()) {
            return resposeJison(0, $validator->errors()->first(), $validator->errors());
        }
        $client = $request->user();
        $client->update($request->all());
        return resposeJison(1, 'data updated successfully', $client);
    }
    public function updatePassword(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return resposeJison(0, $validator->errors()->first(), $validator->errors());
        }

        $client = $request->user();


        if (Hash::check($request->old_password, $client->password)) {

            $client->password = $request->new_password;
            $client->save();
            return resposeJison(1, 'تم تحديث كلمة المرور بنجاح', $client);
        } else {
            return resposeJison(0, 'كلمة المرور القديمة غير صحيحة');
        }
    }
    public function resetPassword(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'phone' => 'required',
        ]);
        if ($validator->fails()) {
            return resposeJison(0, $validator->errors()->first(), $validator->errors());
        }
        $user = Client::where('phone', $request->phone)->first();
        if ($user) {
            $code = rand(1111, 9999);
            $user->pin_code = $code;
            $update = $user->update(['pin_code' => $code]);
            if ($update) {
                smsMisr($request->phone, 'your code is' . $code);
                Mail::to($user->email)
                    ->bcc('abooda601@gmail.com')
                    ->send(new ResetPassword($code));
                return resposeJison(1, 'code sending successfully', ['your code is' => $code]);
            } else {
                return resposeJison(0, 'code sending failed');
            }
        } else {
            return resposeJison(0, 'user not found');
        }
    }
    public function password(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'pin_code' => 'required',
            'password' => 'required|confirmed',
            'phone' => 'required'
        ]);
        if ($validator->fails()) {
            return resposeJison(0, $validator->errors()->first(), $validator->errors());
        }
        $user = Client::where('pin_code', request()->pin_code)->where('pin_code', '!=', 0)
            ->where('phone', $request->phone)->first();
        if ($user) {
            $user->update(['password' => bcrypt($request->password)]);
            $user->pin_code = null;
            if ($user->save()) {
                return resposeJison(1, 'password updated successfully');
            } else {
                return resposeJison(0, 'something is wrong');
            }
        } else {
            return resposeJison(0, 'code is not correct');
        }
    }
    public function addAddress(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'recipient_name' => 'required',
            'cntry_region' => 'nullable',
            'company_name' => 'nullable',
            'Identity' => 'nullable',
            'zip_code' => 'nullable',
            'cntry_name' => 'required',
            'phone' => 'required'
        ]);
        if ($validator->fails()) {
            return resposeJison(0, $validator->errors()->first(), $validator->errors());
        }
        $address = Address::create($request->all());
        return resposeJison(1,'address added successfully',$address);
    }
    public function myAddresses()
    {
        $addresses = Address::all();
        return resposeJison(1, 'all addresses', $addresses);
    }

}
