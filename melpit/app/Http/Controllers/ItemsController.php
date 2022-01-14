<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function showItems(Request $request)
    {
        // ORDER BY FIELD(state, 'selling', 'bought')とすることで、sellingを先、bouchtを後に来るように並び替え
        $items = Item::orderByRaw("FIELD(state, '" . Item::STATE_SELLING . "', '" . Item::STATE_BOUGHT . "')")
            ->orderBy('id', 'DESC')
            ->paginate(1);
        
        return view('items.items')
            ->with('items', $items);
    }

    // ルートモデルバインディングを利用。
    // ルートパラメータからEloquent Modelをidで自動的に解決する仕組み
    public function showItemDetail(Item $item)
    {
        return view('items.item_detail')
            ->with('item', $item);
    }
}
