<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Mail\SendMail;
use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use function Symfony\Component\String\b;

class ClientController extends Controller
{
    //

    public function home()
    {
        $sliders  = Slider::all()->where('status', 1);
        $products = Product::all()->where('status', 1);
        return view('client.home')->with('sliders', $sliders)->with('products', $products);
    }
    public function shop()
    {
        $categories  = Category::all();
        $products = Product::all()->where('status', 1);
        return view('client.shop')->with('categories', $categories)->with('products', $products);
    }
    public function addtocart($id)
    {
        $product = Product::find($id);

        $oldCart = session()->has('cart') ? session()->get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->add($product, $id);
        session()->put('cart', $cart);

        //    dd(session()->get('cart'));
        return back();
    }

    public function update_qty(Request $request, $id)
    {
        
        $oldCart = session()->has('cart') ? session()->get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->updateQty($id, $request->quantity);
        session()->put('cart', $cart);
        return redirect('/cart');
    }

    public function remove_from_cart($id)
    {
        $oldCart = session()->has('cart') ? session()->get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->removeItem($id);

        if (count($cart->items) > 0) {
            session()->put('cart', $cart);
        } else {
            session()->forget('cart');
        }
        return redirect('/cart');
    }

    ##########################Sigin $ Login################
    public function signup()
    {
        return view('client.signup');
    }

    public function login()
    {
        return view('client.login');
    }
    
    public function logout()
    {
        session()->forget('client');
        return redirect('/shop');
    }

    public function create_account(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:clients',
            'password' => 'required|min:6'
        ]);
        $client = new Client();
        $client->email    = $request->input('email');
        $client->password = bcrypt($request->input('password'));

        $client->save();
        return back()->with('status', 'Your account has been successfully created !!');
    }

    public function access_account(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $client = Client::where('email', $request->input('email'))->first();

        if ($client) {
            if (Hash::check($request->input('password'), $client->password)) {
                session()->put('client', $client);
                return redirect('/shop');
            } else {
                return back()->with('status', 'Invalid Email or Password!');
            }
        } else {
            return back()->with('status', 'You do not have an account with this email!! ');
        }
    }

    #######################################################

    public function cart()
    {
        if (!session()->has('cart')) {
            return view('client.cart');
        }

        $oldCart = session()->has('cart') ? session()->get('cart') : null;
        $cart = new Cart($oldCart);
        return view('client.cart', ['products' => $cart->items]);
    }

    public function checkout()
    {
        if (!session()->has('client')) {
            return view('client.login');
        }

        if(!session()->has('cart'))
        {
            return view('client.cart');
        }

        return view('client.checkout');
    }

    public function postcheckout(Request $request)
    {
        $oldCart = session()->has('cart') ? session()->get('cart') : null;
        $cart = new Cart($oldCart);
        $payer_id = time();
        $order = new Order();
        $order->name    = $request->input('name');
        $order->address = $request->input('address');
        $order->cart    =  serialize($cart);
        $order->payer_id =$payer_id;

        $order->save();

        session()->forget('cart');

        $orders = Order::where('payer_id',$payer_id)->get();
        $orders->transform(function($order, $key){
            $order->cart = unserialize($order->cart);

            return $order;
        });
        $email = session()->get('client')->email;
        Mail::to($email)->send(new SendMail($orders));
        return redirect('/cart')->with('status','Your purchase has been successfully!!');
    }

    public function orders()
    {
        $orders = Order::All();
        $orders->transform(function($order, $key){
            $order->cart = unserialize($order->cart);

            return $order;
        });
        return view('admin.orders')->with('orders',$orders);
    }
}
