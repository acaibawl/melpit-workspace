<?php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoldItemsController extends Controller
{
    public function showSoldItems()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $items = $user->soldItems()->orderBy('id', 'DESC')->get();

        return view('mypage.sold_items')
            ->with('items', $items);
    }
}
