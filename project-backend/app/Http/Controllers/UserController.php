<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\User\Register;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\User\Verify;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\VarDumper\Cloner\Data;
class UserController extends Controller
{
    public function register(Register $request){
        $verification_token = Str::random(128);
        if($request->get('nickname')){
            $nickname = $request->get('nickname');
        }else{
            $nickname = null;
        }
        if($request->file()){
            $filehref =  $this->fileUpload($request->file());
        }else{
            $filehref = "default.png";
        }

        $user = User::query()->create([
            'name'=> $request->get('name'),
            'email'=> $request->get('email'),
            'password'=> Hash::make($request->get('password')),
            'role'=> $request->get('role'),
            "verification_token" => $verification_token,
            'avatar' => $filehref,
            "nickname" => $nickname
        ]);
        if($user){
            $this->sendVerMail($user, $verification_token);
        }
        return response()->json([
            'success'=> 'true',
            "data" => $user,
            'message'=> 'User Successfully Registered'
        ]);
    }
    public function sendVerMail(User $user, $token){
        Mail::send('mail.verification', ['user' => $user, 'token'=> $token], function($m) use ($user){
            $m->to($user->email, $user->name)->subject('Please Verify your Email');
        });
    }

    public function verify(Request $request) {

        $user = User::query()
            ->where('email', $request->get('email'))
            ->where('verification_token', $request->get('token'))
            ->first();
        if($user ) {
            $user->update([
                'verification_token' => null,
            'email_verification_at'=> "OK"
            ]);
            dd($request->all());
        }
//            return response()->json([
//                'success'=> 'true',
//                'message'=> 'User Successfully Verified'
//            ]);
//        }
//        return response()->json([
//            'success'=> 'false',
//            'message'=> 'Verification Failed'
//        ], 401);

    }
    public function fileUpload($data){
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 6; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if($data){
            $fileToArray = $data["file"];
            $fileName = $_FILES["file"]["name"];
            $fileToArray->storeAs("/public/avatar/",$randomString . $fileName );
            $fileHref = $randomString .  $fileName ;
            return $fileHref;
        }
    }
    public function updateUser(Request $request){
        $email = $request->get("email");
        $user = User::query()->where("email",'=',$email)->first();
        $validated = $request->validate([
            'name' => 'required',
        ]);
        if($request->get("nickname") && $request->get("nickname") != "null") {
            $nickname = $request->get("nickname");
        }else{
            $nickname = null;
        }

        if($request->file()){
            $fileHref =  $this->fileUpload($request->all("file"));
            $user->avatar = $fileHref;
        }
          $name = $request->get("name");
        $user->nickname = $nickname;
        $user->name = $name;
        $user->save();
        return response()->json([
            'success'=> 'true',
            "data" => $user,
            'message'=> 'User Edit Successfully'
        ]);
    }
    public function changePassword(Request $request){
        $email = $request->get("email");
        $password = Hash::make($request->get('newPassword'));
        $user = User::query()->where("email",'=',$email)->first();
        $user->password = $password;
        $user->save();
        return response()->json([
            'success'=> 'true',
            "data" => $user,
            'message'=> 'User Edit Successfully'
        ]);
    }
    public function bloggerList(){
        $blogger = User::query()->where("role","=","blogger")->paginate(10);
        if($blogger[0]){
            $data2 = Carbon::parse($blogger[0]["block_time"]);
            $now = Carbon::now();
            for($i = 0;$i<count($blogger);$i++){
                if($blogger[$i]["block_time"]) {
                    if (Carbon::parse($blogger[$i]["block_time"])->lt(Carbon::now())) {
                        $blogger[$i]["block_time"] = null;
                        $blogger[$i]->save();
                    }
                }
            }
        }
        return response()->json([
            "data" => $blogger
        ]);
    }
    public function blockUser(Request $request){
            $user = User::query()->where("name","=",$request->get(1))->first();
        $user->block_time = Carbon::now()->addDays($request->get(0));
        $user->save();
    }
    public function checkTime(Request $request){
        $user = User::query()->where("name","=",$request->get(0))->first();

        if(Carbon::parse($user['block_time'])->lt(Carbon::now())){
            $user->block_time  = null;
            $user->save();
        }
        return response()->json([
           "data" => $user
        ]);
    }
}
