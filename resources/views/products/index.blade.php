@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control multiple">
						<option value=""></option>
						@foreach($allVariants as $variants)
							@php
								$productVarinats = $allProductVariants->where('variant_id',$variants->id);
								//$productVarinats = [];
							@endphp
						<optgroup label="{{$variants->title}}">
							@foreach($productVarinats as $productVariant)
								<option value="{{$productVariant->variant}}">{{$productVariant->variant}}</option>
							@endforeach
						</optgroup>
						@endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th style="width:30%">Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>
					@php
						if(isset($allProducts)){
							$allProductsData = $allProducts;
						}else{
							$allProductsData = $products;
						}
					@endphp
					@foreach($allProductsData as $product)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$product->title}} <br> Created at : {{date('d-M-Y',strtotime($product->created_at))}}</td>
                        <td>{{$product->description}}</td>
                        <td>
							@php
								$varaints  = $product->variants;
								$prices = $product->prices;
							@endphp
							@foreach($prices as $price)
                            <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">
								@php
									$varinatsCombine = $varaints->whereIn('id',[$price->product_variant_one,$price->product_variant_two,$price->product_variant_three,]);
									//dd($varinatsCombine);
								@endphp 
                                <dt class="col-sm-3 pb-0">
								{{implode('/',$varinatsCombine->pluck('variant')->toArray())}}
                                </dt>
                                <dd class="col-sm-9">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 pb-0">Price : {{ number_format($price->price,2) }}</dt>
                                        <dd class="col-sm-8 pb-0">InStock : {{ number_format($price->stock,2) }}</dd>
                                    </dl>
                                </dd>
                            </dl>
							@endforeach
                            <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.edit',$product->id) }}" class="btn btn-success">Edit</a>
                            </div>
                        </td>
                    </tr>
					@endforeach
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{$products->firstItem()}} to {{$products->lastItem()}} out of {{$products->total()}}</p>
                </div>
                <div class="col-md-2">
				{{$products->links()}}
                </div>
            </div>
        </div>
    </div>

@endsection
