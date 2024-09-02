<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ShippingRule;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckOutController extends Controller
{
    public function index()
    {
        $addresses = UserAddress::where('user_id', Auth::user()->id)->get();
        $shippingMethods = ShippingRule::where('status', 1)->get();
        return view('frontend.pages.checkout', compact('addresses', 'shippingMethods'));
    }

    public function createAddress(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:200'],
            'phone' => ['required', 'max:200'],
            'email' => ['required', 'email'],
            'country' => ['required', 'max: 200'],
            'state' => ['required', 'max: 200'],
            'city' => ['required', 'max: 200'],
            'zip' => ['required', 'max: 200'],
            'address' => ['required', 'max: 200']
        ]);

        $address = new UserAddress();
        $address->user_id = Auth::user()->id;
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->email = $request->email;
        $address->country = $request->country;
        $address->state = $request->state;
        $address->city = $request->city;
        $address->zip = $request->zip;
        $address->address = $request->address;
        $address->save();

        toastr('Address created successfully!', 'success', 'Success');

        return redirect()->back();
    }

    public function checkOutFormSubmit(Request $request)
    {
        // Validasi input
        $request->validate([
            'shipping_method_id' => ['nullable', 'integer'], // Mengizinkan null untuk shipping_method_id
            'shipping_address_id' => ['required', 'integer'],
            'qty_barang' => ['required', 'integer', 'min:1'], // Tambahkan validasi untuk qty_barang
        ]);

        // Jika shipping_method_id null, set nilainya ke default dan cost ke 0
        if ($request->shipping_method_id === null) {
            $shippingMethod = [
                'id' => 4, // ID default untuk jasa
                'name' => 'jasa',
                'type' => 'jasa',
                'cost' => 0
            ];
        } else {
            // Jika ada shipping_method_id yang valid
            $shippingRule = ShippingRule::findOrFail($request->shipping_method_id);

            // Hitung biaya pengiriman berdasarkan qty_barang
            $shippingMethod = [
                'id' => 3,
                'name' => $shippingRule->name,
                'type' => $shippingRule->type,
                'cost' => $shippingRule->cost * $request->qty_barang
            ];
        }

        // Simpan metode pengiriman ke dalam session
        Session::put('shipping_method', $shippingMethod);

        // Simpan alamat ke dalam session
        $address = UserAddress::findOrFail($request->shipping_address_id)->toArray();
        Session::put('address', $address);

        // Mengembalikan response sukses dan redirect ke halaman pembayaran
        return response(['status' => 'success', 'redirect_url' => route('user.payment')]);
    }

}
