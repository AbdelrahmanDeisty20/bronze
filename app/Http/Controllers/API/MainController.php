<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Contact;
use App\Models\Gift;
use App\Models\Message;
use App\Models\Order;
use App\Models\PaymenMethod;
use App\Models\Product;
use App\Models\Review;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Charge;
use Stripe\Stripe;

class MainController extends Controller
{
    public function  contact(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'messge' => 'required',
        ]);
        if ($validator->fails()) {
            return resposeJison(0, $validator->errors()->first(), $validator->errors());
        }
        $contact = Contact::create($request->all());
        return resposeJison(1, 'message sent successfully', $contact);
    }
    public function messges()
    {
        $messges = Message::all();
        return resposeJison(1, 'messages', $messges);
    }
    public function myMessges()
    {
        $messges = Message::all();
        return resposeJison(1, 'messages', $messges);
    }
    public function myGifts()
    {
        $messges = Gift::all();
        return resposeJison(1, 'messages', $messges);
    }
    public function newOrder(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required',
            'color' => 'required',
            'shipping_id' => 'nullable',
            'address_id' => 'nullable'
        ]);

        if ($validator->fails()) {
            return resposeJison(0, $validator->errors()->first(), $validator->errors());
        }

        $total = 0;
        foreach ($request->products as $i) {
            $product = Product::find($i['product_id']);
            if (!$product) {
                return resposeJison(0, 'Product not found', []);
            }

            $quantity = $product->quantity - $i['quantity'];
            if ($quantity < 0) {
                return resposeJison(0, 'Insufficient stock for product ID: ' . $i['product_id'], []);
            }

            $totalPrice = $product->price * $i['quantity'];
            $total += $totalPrice;

            $product->quantity = $quantity;
            $product->save();
        }

        $order = request()->user()->orders()->create([
            'total_price' => $total,
            'color' => $request->color
        ]);

        foreach ($request->products as $i) {
            $order->products()->attach($i['product_id'], [
                'quantity' => $i['quantity'],
                'price' => $totalPrice
            ]);
        }
        $client = auth()->user();
        if ($order->save()) {
            $order->carts()->create([
                'order_id' => $order->id,
                'price' => $product->price,
                'product_id' => $product->id,
                'client_id' => $order->client_id,
                'quantity' => $i['quantity'],
                'total_price' => $order->total_price
            ]);

            $client->notifications()->create([
                'title' => 'Order created',
                'content' => 'Your order has been created successfully and your order is : ' . $order->id,
                'order_id' => $order->id
            ]);
            $tokens = $client->tokens()->where('token', '=!', '')->pluck('token')->toArray();
            $audience = ['inclide_players_ids', $tokens];
            $contents = [
                'en' => 'Your order has been created successfully',
            ];
            $send = notifyByFirebase($audience, $contents, [
                'user_type' => 'client',
                'action' => 'add-order',
                'order_id' => $order->id,
            ]);
            $send = json_decode($send);
            $data = [
                'order' => $order->fresh()->load('products')
            ];

            return resposeJison(1, 'Order created successfully and added to cart', $order);
        }
    }
    public function myOrders(Request $request)
    {
        $order = Order::all();
        return resposeJison(1, 'Orders retrieved successfully', $order);
    }
    public function myCart()
    {
        $cart = Cart::all();
        return resposeJison(1, 'Cart retrieved successfully', $cart);
    }
    //     public function paymentMethod(Request $request)
    //     {
    //         $validator = validator()->make($request->all(), [
    //             'cart_id' => 'required',
    //             'name' => 'required',
    //             'card_number' => 'required',
    //             'security_code' => 'required',
    //             'end_date' => 'required',
    //             'address_id' => 'required',
    //             'shipping_id' => 'required'
    //         ]);
    //         if ($validator->fails()) {
    //             return resposeJison(0, $validator->errors()->first(), $validator->errors());
    //         }
    //         $paypal = new PayPalClient;
    //         $paypal->setApiCredentials(config('paypal'));
    //         $token = $paypal->getAccessToken();
    //         $paypal->setAccessToken($token);


    //         $cart = Cart::all();
    //         $totalPrice = $cart->sum('total_price');


    //         $response = $paypal->createOrder([
    //             "intent" => "CAPTURE",
    //             "purchase_units" => [
    //                 [
    //                     "amount" => [
    //                         "currency_code" => "USD",
    //                         "value" => $totalPrice,
    //                     ]
    //                 ]
    //             ]
    //         ]);
    //         if ($response['status'] === 'CREATED') {
    //             $approveLink = collect($response['links'])->firstWhere('rel', 'approve')['href'];

    //             return response()->json([
    //                 'approval_url' => $approveLink,
    //                 'order' => $response,
    //             ]);
    //         }
    //     }

    //     public function capture(Request $request)
    // {
    //     $paypal = new PayPalClient;
    //     $paypal->setApiCredentials(config('paypal'));
    //     $token = $paypal->getAccessToken();
    //     $paypal->setAccessToken($token);

    //     $orderId = $request->input('order_id');

    //     $response = $paypal->capturePaymentOrder($orderId);

    //     return response()->json($response);
    // }



    public function paymentMethod(Request $request) {
        $validator= validator()->make($request->all(),[
            'cart_id'=>'required',
            'shipping_id'=>'required',
            'address_id'=>'required',
            'card_number'=>'nullable',
            'name'=>'nullable',
            'security_code'=>'nullable',
            'end_date'=>'nullable',
            'token'=>'required',
            'description'=>'nullable|string'
        ]);
        if ($validator->fails()) {
            $data = $validator->errors();
            return response()->json([
                'success'=>false,
                'message'=> 'something is error',
                'data'=>$validator->errors()
            ]);
        }
        $shipping = Shipping::where('id',$request->shipping_id)->first();
        $cost = $shipping->cost;
        $cart = Cart::where('id',$request->cart_id)->first();
        $totalPrice= $cart->sum('total_price')+$cost;
        if ($totalPrice <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Total price must be greater than zero',
            ]);
        }
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $charge = Charge::create([
            'amount' => $totalPrice*1,
            'currency' => 'usd',
            'source' => $request->token,
            'description' =>$request-> description ?? 'Test Payment',
        ]);
        //check if prccess success
        if($charge->status==='succeeded')
        {
            //and the insert into payments
            $payments= PaymenMethod::create([
                'transaction_id' => $charge->id,
                'amount' => $charge->amount,
                'currency' => $charge->currency,
                'status' => $charge->status,
                'cart_id' => $request->cart_id,
                'shipping_id'=>$request->shipping_id,
                'address_id'=>$request->address_id,
                'description' => $request->description ?? 'No description provided',
            ]);
            return response()->json([
                'success'=>true,
                'message'=> 'payment successed',
                'data'=>$payments
            ]);
        }
        return response()->json([
            'success'=>false,
            'message'=> 'payment faild',
            'data'=>$charge
        ]);
    }
    public function addReviews(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'rate' => 'required',
            'comment' => 'required',
            'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            return resposeJison(0, $validator->errors()->first(), $validator->errors());
        }
        $reviews = Review::create([
            'rate' => $request->rate,
            'comment' => $request->comment,
            'client_id' => $request->user()->id,
            'product_id' => $request->product_id
        ]);
        return resposeJison(1, 'Review added successfully', $reviews);
    }
    public function getReviews()
    {
        $reviews = Review::all();
        return resposeJison(1, 'Reviews retrieved successfully', $reviews);
    }
    public function getReviewsByProduct(Request $request)
    {
        $reviews = Review::where('product_id', $request->product_id)->paginate(10);
        return resposeJison(1, 'Reviews retrieved successfully', $reviews);
    }
    public function getProduct(Request $request)
    {
        $products = Product::with('reviews')
            ->where('id', $request->product_id)->paginate(10);
        // $products->load('reviews');
        return resposeJison(1, 'Product retrieved successfully', $products);
    }
    public function productsTopSeles()
    {
        $products = Product::orderBy('sales_count', 'desc')->take(10)->get();
        return resposeJison(1, 'Products retrieved successfully', $products);
    }
    public function getProductsByCategory(Request $request)
    {
        $products = Product::where('category_id', $request->category_id)->paginate(10);
        return resposeJison(1, 'Products retrieved successfully', $products);
    }
    public function getProductsPopular(Request $request)
    {
        $products = Product::whereHas('order')->get();
        return resposeJison(1, 'Products retrieved successfully', $products);
    }
}
