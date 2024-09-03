@php
    $categoryProductSliderSectionOne = json_decode($categoryProductSliderSectionOne->value);
    $lastKey = [];

    foreach($categoryProductSliderSectionOne as $key => $category){
        if($category === null ){
            break;
        }
        $lastKey = [$key => $category];
    }

    // Filter by category name 'Service'
    $serviceCategory = \App\Models\Category::where('name', 'Service')->first();

    if($serviceCategory) {
        if(array_keys($lastKey)[0] === 'category'){
            $products = \App\Models\Product::withAvg('reviews', 'rating')->withCount('reviews')
            ->with(['variants', 'category', 'productImageGalleries'])
            ->where('category_id', $serviceCategory->id)->orderBy('id', 'DESC')->take(12)->get();
        } elseif(array_keys($lastKey)[0] === 'sub_category') {
            $products = \App\Models\Product::withAvg('reviews', 'rating')->withCount('reviews')
            ->with(['variants', 'category', 'productImageGalleries'])
            ->where('sub_category_id', $serviceCategory->id)->orderBy('id', 'DESC')->take(12)->get();
        } else {
            $products = \App\Models\Product::withAvg('reviews', 'rating')->withCount('reviews')
            ->with(['variants', 'category', 'productImageGalleries'])
            ->where('child_category_id', $serviceCategory->id)->orderBy('id', 'DESC')->take(12)->get();
        }
    } else {
        $products = collect(); // If no 'Service' category found, return an empty collection.
    }
@endphp

<section id="wsus__electronic">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="wsus__section_header">
                    <h3>{{$category->name}}</h3>
                    <a class="see_btn" href="{{route('products.index', ['category' => $category->slug])}}">see more <i class="fas fa-caret-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row flash_sell_slider">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>
