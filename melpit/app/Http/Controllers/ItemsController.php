<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function showItems(Request $request)
    {
        $query = Item::query();

        // カテゴリで絞り込み
        // filledはパラメータが空じゃなければtrueを返す
        if ($request->filled('category')) {
            // list関数で、戻り値の配列の要素をそれぞれ別変数に取り出す
            list($categoryType, $categoryID) = explode(':', $request->input('category'));

            if($categoryType === 'primary') {
                // リレーション先のテーブルのカラムを基に絞り込む場合はwhereHasメソッドを使用
                // 第一引数にはリレーションを定義しているメソッドの名前
                $query->whereHas('secondaryCategory', function($query) use ($categoryID) {
                    $query->where('primary_category_id', $categoryID);
                });
            } else if ($categoryType === 'secondary') {
                $query->where('secondary_category_id', $categoryID);
            }
        }

        // キーワードで絞り込み
        if ($request->filled('keyword')) {
            $keyword = '%' . $this->escape($request->input('keyword')) . '%';

            // A AND (B OR C)の条件
            // whereメソッドの引数に無名関数を渡してその中でorWhereで繋ぐ
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', $keyword);
                $query->orWhere('description', 'LIKE', $keyword);
            });
        }

        // ORDER BY FIELD(state, 'selling', 'bought')とすることで、sellingを先、bouchtを後に来るように並び替え
        $items = $query->orderByRaw("FIELD(state, '" . Item::STATE_SELLING . "', '" . Item::STATE_BOUGHT . "')")
            ->orderBy('id', 'DESC')
            ->paginate(52);
        
        return view('items.items')
            ->with('items', $items);
    }

    private function escape(string $value)
    {
        return str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\', '\\_'],
            $value
        );
    }

    // ルートモデルバインディングを利用。
    // ルートパラメータからEloquent Modelをidで自動的に解決する仕組み
    public function showItemDetail(Item $item)
    {
        return view('items.item_detail')
            ->with('item', $item);
    }

    public function showByItemForm(Item $item)
    {
        if (!$item->isStateSelling) {
            // 処理を切り上げて404を返す
            abort(404);
        }

        return view('items.item_buy_form')
            ->with('item', $item);
    }
}
