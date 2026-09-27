<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart');

        return view('cart.index', compact('cart'));
    }

    public function increment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'required|integer'
        ]);

        $product = Product::findOrFail($request->product_id);

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {

            if ($cart[$product->id]["qty"] >= $product->quantity) {
                return redirect()->back()->with('error', 'محصول با بیشترین تعداد ممکن به سبد خرید اضافه شده');
            }

            $cart[$product->id]["qty"]++;
        } else {
            $cart[$product->id] = [
                "name" => $product->name,
                "quantity" => $product->quantity,
                "is_sale" => $product->is_sale,
                "price" => $product->price,
                "sale_price" => $product->sale_price,
                "primary_image" => $product->primary_image,
                "qty" => 1
            ];
        }

        $request->session()->put('cart', $cart);

        return redirect()->back()->with('success', 'محصول مورد نظر به سبد خرید اضافه شد');
    }

    public function decrement(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'required|integer'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->qty == 0) {
            return redirect()->back()->with('error', 'تعداد محصول مورد نظر کمتر از حد مجاز می باشد');
        }

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {

            $cart[$product->id] = [
                "name" => $product->name,
                "quantity" => $product->quantity,
                "is_sale" => $product->is_sale,
                "price" => $product->price,
                "sale_price" => $product->sale_price,
                "primary_image" => $product->primary_image,
                "qty" => $cart[$product->id]["qty"] - 1,
            ];
        }

        $request->session()->put('cart', $cart);

        return redirect()->back()->with('success', 'تعداد محصول مورد نظر از سبد خرید کاهش یافت');
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'required|integer'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->qty > $product->quantity) {
            return redirect()->back()->with('error', 'تعداد محصول مورد نظر بیشتر از حد مجاز می باشد');
        }

        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {

            $cart[$product->id]["qty"] = $request->qty;
        } else {
            $cart[$product->id] = [
                "name" => $product->name,
                "quantity" => $product->quantity,
                "is_sale" => $product->is_sale,
                "price" => $product->price,
                "sale_price" => $product->sale_price,
                "primary_image" => $product->primary_image,
                "qty" => $request->qty
            ];
        }

        $request->session()->put('cart', $cart);

        return redirect()->back()->with('success', 'محصول مورد نظر به سبد خرید اضافه شد');
    }

    public function remove(Request $request)
    {
        $cart = $request->session()->get('cart');

        if ( isset($cart[$request->product_id]) ) {
            unset($cart[$request->product_id]);
        }

        $request->session()->put('cart', $cart);

        return redirect()->back()->with('success', 'محصول مورد نظر از سبد خرید حذف شد');
    }

    public function clear(Request $request)
    {
        $request->session()->put('cart', []);
        return redirect()->route('product.menu')->with('warning', 'سبد خرید شما خالی شد');
    }
}
