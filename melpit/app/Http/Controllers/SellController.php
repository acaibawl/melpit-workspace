<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SellRequest;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\PrimaryCategory;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SellController extends Controller
{
    public function showSellForm()
    {
        $categories = PrimaryCategory::query()
            // N+1問題回避
            ->with([
                // 連想配列のキーはリレーションを定義しているメソッド名。
                // 連想配列の値にはEager Load時のクエリをカスタマイズする無名関数。
                'secondaryCategories' => function($query) {
                    $query->orderBy('sort_no');
                }
            ])
            ->orderBy('sort_no')
            ->get();
        
        $conditions = ItemCondition::orderBy('sort_no')->get();
        
        return view('sell')
            ->with('categories', $categories)
            ->with('conditions', $conditions); 
    }

    public function sellItem(SellRequest $request)
    {
        $user = Auth::user();
        $imageName = $this->saveImage($request->file('item-image'));

        $item                        = new Item();
        $item->image_file_name       = $imageName;
        $item->seller_id             = $user->id;
        $item->name                  = $request->input('name');
        $item->description           = $request->input('description');
        $item->secondary_category_id = $request->input('category');
        $item->item_condition_id     = $request->input('condition');
        $item->price             = $request->input('price');
        $item->state             = Item::STATE_SELLING;
        $item->save();

        return redirect()->back()
            ->with('status', "商品を出品しました。");
    }

    /**
     * 商品画像をリサイズして保存
     *
     * @param UploadedFile $file アップロードされた商品画像
     * @return string ファイル名
     */
    private function saveImage(UploadedFile $file): string
    {
        $tempPath = $this->makeTempPath();
        Image::make($file)->fit(300, 300)->save($tempPath);

        $filePath = Storage::disk('public')
            ->putFile('item-images', new File($tempPath));
        
        return basename($filePath);
    }

    /**
     * 一時的なファイルを生成してパスを返します。
     *
     * @return string ファイルパス
     */
    private function makeTempPath(): string
    {
        $temp_fp = tmpfile();
        $meta = stream_get_meta_data($temp_fp);
        return $meta['uri'];
    }
}
