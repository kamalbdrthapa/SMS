<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function ProfileView(){
        $id = Auth::user()->id;
        $user = User::find($id);

        return view('backend.user.view_profile',compact('user'));
    }

    public function ProfileEdit(){
        $id = Auth::user()->id;
        $userData = User::find($id);

        return view('backend.user.edit_profile',compact('userData'));

    }


    public function ProfileUpdate(Request $request){
        $profileData = User::find(Auth::user()->id);
        $profileData->name = $request->name;
        $profileData->email = $request->email;
        $profileData->mobile = $request->mobile;
        $profileData->address = $request->address;
        $profileData->gender = $request->gender;
        
        if($request->file('image')){
            $file = $request->file('image');
            @unlink(public_path('upload/user_images/'.$profileData->image));
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/user_images'),$filename);
            $profileData['image']=$filename;
        } //End if

        $profileData->save();

        $notification = array(
            'message'=>'User Profile Updated Successfully',
            'alert-type'=>'success'
        );

        return redirect()->route('profile.view')->with($notification);
        

    }

        public function PasswordEdit(){
        $id = Auth::user()->id;
        $userData = User::find($id);

        return view('backend.user.edit_password',compact('userData'));
    }

        public function PasswordUpdate(Request $request){
            $validatedData = $request->validate([
                'current_password' => 'required',
                'password' => 'required|confirmed',
            ]);

            $hashedPassword = Auth::user()->password;
            if(Hash::check($request->current_password, $hashedPassword)){
                $user = User::find(Auth::user()->id);
                $user->password = Hash::make($request->password);
                $user->save();
                Auth::logout();
                return redirect()->route('login');
            }
            else {
                return redirect()->back();
            }

            

        }


}
