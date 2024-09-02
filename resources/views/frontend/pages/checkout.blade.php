@extends('frontend.layouts.master')

@section('title')
{{$settings->site_name}} || Checkout
@endsection

@section('content')
<section id="wsus__breadcrumb">
    <div class="wsus_breadcrumb_overlay">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h4>Check Out</h4>
                    <ul>
                        <li><a href="{{route('home')}}">Home</a></li>
                        <li><a href="javascript:;">Check Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="wsus__cart_view">
    @php
        use App\Models\Product;

        $shippingFee = 0;
        $totalQtyBarang = 0;

        foreach(Cart::content() as $item){
            $product = Product::find($item->id);

            if ($product && $product->jenis === 'barang') {
                $totalQtyBarang += $item->qty;
            }
        }

        $shippingFee = $totalQtyBarang * 10000;
        $cartTotal = getCartTotal();
        $mainCartTotal = getMainCartTotal();
    @endphp
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="wsus__check_form">
                    <div class="d-flex">
                        <h5>Shipping Details</h5>
                        <a href="javascript:;" style="margin-left:auto;" class="common_btn" data-bs-toggle="modal" data-bs-target="#exampleModal">Add New Address</a>
                    </div>

                    <div class="row">
                        @foreach ($addresses as $address)
                        <div class="col-xl-6">
                            <div class="wsus__checkout_single_address">
                                <div class="form-check">
                                    <input class="form-check-input shipping_address" data-id="{{$address->id}}" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Select Address
                                    </label>
                                </div>
                                <ul>
                                    <li><span>Name :</span> {{$address->name}}</li>
                                    <li><span>Phone :</span> {{$address->phone}}</li>
                                    <li><span>Email :</span> {{$address->email}}</li>
                                    <li><span>Country :</span> {{$address->country}}</li>
                                    <li><span>City :</span> {{$address->city}}</li>
                                    <li><span>Zip Code :</span> {{$address->zip}}</li>
                                    <li><span>Address :</span> {{$address->address}}</li>
                                </ul>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="wsus__order_details" id="sticky_sidebar">
                    <div class="wsus__order_details_summery">
                        @foreach(Cart::content() as $item)
                        <p>Price: <span>{{ $settings->currency_icon }}{{ number_format($item->price, 2) }}</span></p>
                        <p>Qty: <span>{{ $item->qty }}</span></p>
                        @endforeach
                        <p>Subtotal: <span>{{ $settings->currency_icon }}{{ number_format($cartTotal, 2) }}</span></p>
                        <p>Shipping fee(+): <span id="shipping_fee">{{ $settings->currency_icon }}{{ number_format($shippingFee, 2) }}</span></p>
                        <p>Coupon(-):</p>
                        <p><b>Total:</b> <span><b id="total_amount">{{ $settings->currency_icon }}{{ number_format($mainCartTotal + $shippingFee, 2) }}</b></span></p>
                    </div>
                    <div class="terms_area">
                        <div class="form-check">
                            <input class="form-check-input agree_term" type="checkbox" value="" id="flexCheckChecked3" checked>
                            <label class="form-check-label" for="flexCheckChecked3">
                                I have read and agree to the website <a href="#">terms and conditions *</a>
                            </label>
                        </div>
                    </div>
                    <form action="" id="checkOutForm">
                        <input type="hidden" name="shipping_method_id" value="" id="shipping_method_id">
                        <input type="hidden" name="shipping_address_id" value="" id="shipping_address_id">
                        <input type="hidden" name="qty_barang" value="" id="qty_barang">
                    </form>
                    <a href="" id="submitCheckoutForm" class="common_btn">Place Order</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
  $(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Mengambil nilai dari Blade
    let shippingFee = @json($shippingFee);
    let cartTotal = @json($cartTotal); // Ambil total cart dari Blade
    let totalAmount = @json($mainCartTotal) + shippingFee;

    $('#shipping_fee').text("{{$settings->currency_icon}}" + shippingFee.toFixed(2));
    $('#total_amount').text("{{$settings->currency_icon}}" + totalAmount.toFixed(2));

    // Hitung total qty barang
    let totalQty = 0;
    @foreach(Cart::content() as $item)
        totalQty += {{ $item->qty }};
    @endforeach

    // Set nilai qty_barang ke dalam form
    $('#qty_barang').val(totalQty);

    $('.shipping_address').on('click', function(){
        $('#shipping_address_id').val($(this).data('id'));
    });

    // submit checkout form
    $('#submitCheckoutForm').on('click', function(e){
        e.preventDefault();
        if ($('#shipping_address_id').val() == ""){
            toastr.error('Shipping address is required');
        } else if (!$('.agree_term').prop('checked')){
            toastr.error('You have to agree website terms and conditions');
        } else {
            $.ajax({
                url: "{{route('user.checkout.form-submit')}}",
                method: 'POST',
                data: $('#checkOutForm').serialize(),
                beforeSend: function(){
                    $('#submitCheckoutForm').html('<i class="fas fa-spinner fa-spin fa-1x"></i>')
                },
                success: function(data){
                    if(data.status === 'success'){
                        $('#submitCheckoutForm').text('Place Order');
                        // redirect user to next page
                        window.location.href = data.redirect_url;
                    }
                },
                error: function(data){
                    console.log(data);
                }
            })
        }
    });
  });
</script>
@endpush
