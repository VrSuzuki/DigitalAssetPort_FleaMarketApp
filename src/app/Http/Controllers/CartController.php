<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Content;
use App\Models\LibraryItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->cart($request)->load('items.content.author');

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request, Content $content)
    {
        if ($content->user_id === $request->user()->id) {
            return back()->with('status', '自分が投稿したコンテンツは購入できません。');
        }

        if ($this->hasPurchased($request, $content)) {
            $order = $this->purchaseOrder($request, $content);

            return $order
                ? redirect()->route('purchases.show', $order)->with('status', 'このコンテンツは購入済みです。')
                : redirect()->route('library.index')->with('status', 'このコンテンツはライブラリに追加済みです。');
        }

        if ($content->price === 0) {
            LibraryItem::firstOrCreate([
                'user_id' => $request->user()->id,
                'content_id' => $content->id,
            ], [
                'added_type' => 'free',
            ]);

            return redirect()->route('library.index')->with('status', '無料コンテンツをライブラリに追加しました。');
        }

        $cart = $this->cart($request);

        $cartItem = CartItem::firstOrCreate([
            'cart_id' => $cart->id,
            'content_id' => $content->id,
        ]);

        return redirect()->route('cart.index')->with('status', $cartItem->wasRecentlyCreated ? 'カートに追加しました。' : 'すでにカートに入っています。');
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 403);
        $cartItem->delete();

        return back()->with('status', 'カートから削除しました。');
    }

    public function checkout(Request $request)
    {
        $cart = $this->cart($request)->load('items.content');

        $cart->items->filter(function ($item) use ($request) {
            return $item->content->user_id === $request->user()->id || $this->hasPurchased($request, $item->content);
        })->each->delete();

        $cart->load('items.content');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'カートが空です。');
        }

        if (config('services.stripe.secret')) {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => $cart->items->map(function ($item) {
                    return [
                        'price_data' => [
                            'currency' => 'jpy',
                            'product_data' => ['name' => $item->content->title],
                            'unit_amount' => $item->content->price,
                        ],
                        'quantity' => 1,
                    ];
                })->values()->all(),
                'success_url' => route('checkout.success', [], true),
                'cancel_url' => route('cart.index', [], true),
            ]);

            session(['stripe_session_id' => $session->id]);

            return redirect($session->url);
        }

        $order = $this->createOrder($request, $cart);

        return redirect()->route('purchases.show', $order)->with('status', 'ローカル決済として購入を完了しました。');
    }

    public function success(Request $request)
    {
        $cart = $this->cart($request)->load('items.content');
        $order = $this->createOrder($request, $cart, session('stripe_session_id'));
        session()->forget('stripe_session_id');

        return redirect()->route('purchases.show', $order)->with('status', '決済が完了しました。');
    }

    public function download(Content $content)
    {
        $ownsContent = $content->user_id === auth()->id();
        $hasLibrary = LibraryItem::where('user_id', auth()->id())->where('content_id', $content->id)->exists();

        abort_unless($ownsContent || $hasLibrary, 403);

        return response(
            "DigitalAssetPort download package\nTitle: {$content->title}\nThis local portfolio build returns a sample download payload.\n",
            200,
            [
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.Str::slug($content->title ?: 'asset').'.txt"',
            ]
        );
    }

    private function cart(Request $request)
    {
        return Cart::firstOrCreate([
            'user_id' => $request->user()->id,
            'active' => true,
        ]);
    }

    private function createOrder(Request $request, Cart $cart, ?string $stripeSessionId = null)
    {
        $cart->load('items.content');
        $total = $cart->items->sum(fn ($item) => $item->content->price);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'order_number' => 'DAP-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
            'total_amount' => $total,
            'stripe_session_id' => $stripeSessionId,
            'status' => 'paid',
            'purchased_at' => now(),
        ]);

        foreach ($cart->items as $item) {
            $orderItem = $order->items()->create([
                'content_id' => $item->content_id,
                'price' => $item->content->price,
            ]);

            LibraryItem::firstOrCreate([
                'user_id' => $request->user()->id,
                'content_id' => $item->content_id,
            ], [
                'order_item_id' => $orderItem->id,
                'added_type' => 'purchase',
            ]);
        }

        $cart->items()->delete();

        return $order;
    }

    private function hasPurchased(Request $request, Content $content)
    {
        return LibraryItem::where('user_id', $request->user()->id)
            ->where('content_id', $content->id)
            ->exists();
    }

    private function purchaseOrder(Request $request, Content $content)
    {
        return Order::where('user_id', $request->user()->id)
            ->whereHas('items', function ($query) use ($content) {
                $query->where('content_id', $content->id);
            })
            ->latest('purchased_at')
            ->first();
    }
}
