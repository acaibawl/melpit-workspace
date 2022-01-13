<?php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mypage\Profile\EditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function showProfileEditForm()
    {
        return view('mypage.profile_edit_form')->with('user', Auth::user());
    }

    public function editProfile(EditRequest $request)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $user->name = $request->input('name');
        $user->save();

        // 変数に値を詰めて直前の画面にリダイレクトする
        return redirect()->back()->with('status', 'プロフィールを変更しました。');
    }
}
