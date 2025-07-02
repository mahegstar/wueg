<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $jwtKey = Setting::get('jwt_key', '');
        $footerCopyrightsText = Setting::get('footer_copyrights_text', '');
        
        return view('profile', compact('jwtKey', 'footerCopyrightsText'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'required',
            'jwt_key' => 'required',
            'footer_copyrights_text' => 'required',
            'full_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'half_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'background_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'bot_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update app name
        Setting::set('app_name', $request->app_name);
        
        // Update JWT key
        Setting::set('jwt_key', $request->jwt_key);
        
        // Update footer copyrights text
        Setting::set('footer_copyrights_text', $request->footer_copyrights_text);
        
        // Update theme color
        if ($request->has('theme_color')) {
            Setting::set('theme_color', $request->theme_color);
        }
        
        // Update navbar color
        if ($request->has('navbar_color')) {
            Setting::set('navbar_color', $request->navbar_color);
        }
        
        // Update navbar text color
        if ($request->has('navbar_text_color')) {
            Setting::set('navbar_text_color', $request->navbar_text_color);
        }

        // Handle full logo upload
        if ($request->hasFile('full_file')) {
            $fullLogo = Setting::where('type', 'full_logo')->first();
            if ($fullLogo && $fullLogo->message && File::exists(public_path('images/' . $fullLogo->message))) {
                File::delete(public_path('images/' . $fullLogo->message));
            }
            
            $image = $request->file('full_file');
            $imageName = microtime(true) * 10000 . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            
            Setting::set('full_logo', $imageName);
        }
        
        // Handle half logo upload
        if ($request->hasFile('half_file')) {
            $halfLogo = Setting::where('type', 'half_logo')->first();
            if ($halfLogo && $halfLogo->message && File::exists(public_path('images/' . $halfLogo->message))) {
                File::delete(public_path('images/' . $halfLogo->message));
            }
            
            $image = $request->file('half_file');
            $imageName = microtime(true) * 10000 . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            
            Setting::set('half_logo', $imageName);
        }
        
        // Handle background file upload
        if ($request->hasFile('background_file')) {
            $backgroundFile = Setting::where('type', 'background_file')->first();
            if ($backgroundFile && $backgroundFile->message && File::exists(public_path('images/' . $backgroundFile->message))) {
                File::delete(public_path('images/' . $backgroundFile->message));
            }
            
            $image = $request->file('background_file');
            $imageName = microtime(true) * 10000 . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            
            Setting::set('background_file', $imageName);
        }
        
        // Handle bot image upload
        if ($request->hasFile('bot_image')) {
            $botImage = Setting::where('type', 'bot_image')->first();
            if ($botImage && $botImage->message && File::exists(public_path('images/' . $botImage->message))) {
                File::delete(public_path('images/' . $botImage->message));
            }
            
            $image = $request->file('bot_image');
            $imageName = microtime(true) * 10000 . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            
            Setting::set('bot_image', $imageName);
        }

        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldpassword' => 'required',
            'newpassword' => 'required|min:6',
            'confirmpassword' => 'required|same:newpassword',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        
        if (!Hash::check($request->oldpassword, $user->password)) {
            return redirect()->back()->with('error', 'Old password is incorrect');
        }
        
        $user->password = Hash::make($request->newpassword);
        $user->save();
        
        return redirect()->route('profile')->with('success', 'Password changed successfully');
    }
}