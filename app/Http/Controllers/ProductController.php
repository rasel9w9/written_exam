<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use DB;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
		//$products = Product::with(['variants','prices'])->paginate(5);
		$requestData = request()->all();
		$filtering = false;
		foreach($requestData as $rData=>$value){
			if($value or $value!=''){$filtering=true;};
		}
		
		if($filtering){
			$productsQuery = Product::query();
			/* if(request()->variant){
				$variantFileter=true;
				$varinatsProduct=ProductVariant::where('variant',request()->variant)->get(['id','product_id']);
				//$varinatsProductId = $varinatsProduct->pluck('product_id')->toArray();
				$varinatsId = $varinatsProduct->pluck('id')->toArray();
				//dd($varinatsId);
				$ProductVariantPrice = ProductVariantPrice::all();//->unique('product_id')->pluck('product_id')->toArray();
				$productVariantFileter = $ProductVariantPrice->filter(function($item) use($varinatsId){
					$varinatsId = array_values($varinatsId);
					//dd($varinatsId);
					//dd($item->product_variant_two);
					if(in_array("$item->product_variant_one",$varinatsId)){
						return true;
					}elseif(in_array("$item->product_variant_two",$varinatsId)){
						return true;
					}elseif(in_array("$item->product_variant_three",$varinatsId)){
						return true;
					}else{
						return false;
					}
					return false;
				});
				$variantsProductid = $productVariantFileter->pluck('product_id')->toArray();
			} */
			/* if(request()->price_from and request()->price_to){
				$priceFilter = true;
				$priceBasedProductId=ProductVariantPrice::whereBetween('price',request()->price_from,request()->price_to)->get(['product_id'])->pluck('product_id')->toArray();
			}
			if($variantFileter and $priceFilter){
				$finalFileteredProductsId = array_intersect($variantsProductid,$priceBasedProductId);
				$productsQuery = $productsQuery->whereIn('id',$finalFileteredProductsId);
			}elseif($variantFileter){
				$finalFileteredProductsId = $variantsProductid;
				$productsQuery = $productsQuery->whereIn('id',$finalFileteredProductsId);
			}elseif($priceFilter){
				$finalFileteredProductsId = $priceBasedProductId;
				$productsQuery = $productsQuery->whereIn('id',$finalFileteredProductsId);
			} */
			if(request()->title){
				$str_search = request()->title;
				$productsQuery = $productsQuery->where('title','LIKE','%'.$str_search.'%');
			}
			if(request()->date){
				$date = request()->date; 
				$productsQuery = $productsQuery->whereDate('created_at',$date);
			}
			/* if(request()->variant){
				$varinatsProduct=ProductVariant::where('variant',request()->variant)->get(['id','variant_id','product_id','variant']);
				//return $varinatsProduct;
				$pv_id = $varinatsProduct->pluck('id')->toArray();
				$varinatsProductId = $varinatsProduct->pluck('product_id')->toArray();
				//return $varinatsProductId;
						$productsQuery = $productsQuery->whereIn('id',$varinatsProductId)->with(['variants',
						'prices'=>function($query) use($pv_id){
							$query->whereIn('product_variant_one',$pv_id);
							$query->orWhereIn('product_variant_two',$pv_id);
							$query->orWhereIn('product_variant_three',$pv_id);
						}]);
			} */
			/* if(request()->price_from and request()->price_to){
				$productsQuery = $productsQuery->whereHas('prices',function($query){
					$query->whereBetween('price',[request()->price_from,request()->price_to]);
				});
			} */
			$products = $productsQuery->with(['variants','prices'])->paginate(5);
			//$products = $productsQuery->paginate(5);
			$allProducts=collect($products->items());
			$variantFileter = request()->variant;
			$priceFilterFrom = request()->price_from;
			$priceFilterTo = request()->price_to;
			if($variantFileter){
				$allProducts = $allProducts->filter(function($product)use($variantFileter,$priceFilterFrom,$priceFilterTo){
					$FiltervariantId = @$product->variants->where('variant',$variantFileter)->first()->id;
					if(!$FiltervariantId){return false;}
					$allPrices=[];
					foreach($product->prices as $price){
						if($price->product_variant_one==$FiltervariantId){
							$allPrices[]=$price;
						}elseif($price->product_variant_two==$FiltervariantId){
							$allPrices[]=$price;
						}elseif($price->product_variant_three==$FiltervariantId){
							$allPrices[]=$price;
						}
					}
					if(count($allPrices)>0){
						$product->prices=$allPrices;
						return true;
					}
				});
			}
			if($priceFilterFrom and $priceFilterTo){
				$allProducts = $allProducts->filter(function($product)use($variantFileter,$priceFilterFrom,$priceFilterTo){
					$allPrices=[];
					foreach($product->prices as $price){
						if($price->price>=$priceFilterFrom and $price->price<=$priceFilterTo){
							$allPrices[]=$price;
						}
					}
					if(count($allPrices)>0){
						$product->prices=$allPrices;
						return true;
					}
				});
			}
			/* foreach($products as $product){
				$FiltervariantId = @$product->variants->where('variant',request()->variant)->first()->id;
				//dd($FiltervariantId);
				foreach($product->prices as $pPrice){
					if($pPrice->product_variant_one==$FiltervariantId){
						$allProducts[]=$product;
						break;
					}elseif($pPrice->product_variant_two==$FiltervariantId){
						$allProducts[]=$product;
						break;
					}elseif($pPrice->product_variant_three==$FiltervariantId){
						$allProducts[]=$product;
						break;
					}
				}
			} */
		}else{
			$products = Product::with(['variants','prices'])->paginate(5);
			$allProducts=collect($products->items());
		}
		//return $products;
		$allVariants =  Variant::with('productVariant')->get();
		//$allProductVariants = ProductVariant::all()->unique('variant')->groupBy('variant_id');
		$allProductVariants = ProductVariant::all()->unique('variant');
		$allVariants = Variant::all();
        return view('products.index',compact('products','allProductVariants','allVariants','allProducts'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
		session()->forget('image_uploaded');
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
		$formData = $request->all();
		DB::beginTransaction();
		if($request->product_id){
			$product = Product::find($request->product_id);
			$product->update($formData);
		}else{
			$product = Product::create($formData);
		}
		if($product){
			$imagesSaving = $this->__saveAttachment($product->id);
			$productVarinatSaving = $this->saveProductVariant($formData,$product->id);
			$variantPricessaving = $this->saveProductVariantPrice($formData,$product->id);
		}
		if($imagesSaving and $productVarinatSaving and $variantPricessaving){
			DB::commit();
			return response()->json([
				'success'=>true,
				'redirect'=>route('product.index')
			],200);
		}
    }
	public function saveProductVariant($formData,$productId){
		if(isset($formData['product_id'])){
			ProductVariant::where('product_id',$productId)->delete();
		}
		$timeStamp = date("Y-m-d h:i:s");
		$allProductVariant=[];
		$allTags=[];
		foreach($formData['product_variant'] as $pVariant){
			$variantId = $pVariant['option'];
			foreach($pVariant['tags'] as $tag){
				$data['variant_id'] = $variantId;
				$data['variant'] = $tag;
				$data['product_id'] = $productId;
				$data['created_at'] = $timeStamp;
				$data['updated_at'] = $timeStamp;
				$allTags[]=$tag;
				$allProductVariant[]=$data;
			}
		};
		//dd($allTags);
		if(count($allProductVariant)>0){
			if(ProductVariant::insert($allProductVariant)){
				return true;
			};
		}
	}
	public function saveProductVariantPrice($formData,$productId){
		if(isset($formData['product_id'])){
			ProductVariantPrice::where('product_id',$productId)->delete();
		}
		$productVariants = ProductVariant::where('product_id',$productId)->get();
		$timeStamp = date("Y-m-d h:i:s");
		$allVariantPrices=[];
		foreach($formData['product_variant_prices'] as $variantPrice){
			$titles = explode('/',$variantPrice['title']);
			$keys = ['1'=>'one','2'=>'two','3'=>'three'];
			foreach($titles as $index=>$title){
				$serial = $index+1;
				//dd($title);
				$productVariantId = @$productVariants->where('variant',$title)->first()->id;
				//dd($productVariantId);
				$columnName = "product_variant_".$keys["$serial"];
				$data[$columnName] = $productVariantId;
			}
			$data['price'] = $variantPrice['price'];
			$data['stock'] = $variantPrice['stock'];
			$data['product_id'] = $productId;
			$data['created_at'] = $timeStamp;
			$data['updated_at'] = $timeStamp;
			$allVariantPrices[]=$data;
		}
		if(count($allVariantPrices)>0){
			if(ProductVariantPrice::insert($allVariantPrices)){
				return true;
			};
		}
	}
	public function saveAttachment(){
		$basePath = base_path();
		$directory="/public/attachments/";
		$destinationPath=$basePath.$directory;
		$timeStamp = date("Y-m-d h:i:s");
		$imageName = time().'.'.request()->file->getClientOriginalExtension();
		if(request()->file->move($destinationPath, $imageName)){
			$data['product_id']='';
			$data['file_path']=$destinationPath.$imageName;
			$data['thumbnail']=1;
			$data['created_at'] = $timeStamp;
			$data['updated_at'] = $timeStamp;
			if(session('image_uploaded')){
				$allData = session('image_uploaded');
				$allData[]=$data;
				session()->put('image_uploaded',$allData);
			}else{
				session()->put('image_uploaded',[$data]);
			}
		};
		//return response()->json(['success'=>'You have successfully upload file.']);
	}
	
	public function __saveAttachment($productId){
		if(request()->product_id){
			\App\Models\ProductImage::where('product_id',$productId)->delete();
		}
		if(session()->get('image_uploaded')){
			$allImages = session()->get('image_uploaded');
			$allData = [];
			foreach($allImages as $image){
				$image['product_id'] = $productId;
				$allData[]=$image;
			}
			if(count($allData)>0){
				\App\Models\ProductImage::insert($allData);
				return true;
			}
			session()->forget('image_uploaded');
		}
	}
	
	public function saveVariant($variants,$ProductId){
		if($variants){
			foreach($variants as $variant){
				$data['product_id'] = $id;
				$data['file_path'] = $destinationPath;
				$data['thumbnail'] = $timeStamp.".".$attachment->getClientOriginalExtension();
				$data['created_at'] = $timeStamp;
				$data['updated_at'] = $timeStamp;
			}
		}
	}
    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $variants = Variant::all();
		$products = Product::where('id',$id)->with(['variants','prices'])->first();
		$variantId=[];
		$vaiantsGroups = $products->variants->groupBy('variant_id');
		$allPrices = $products->prices;
		$allData=[];
		foreach($vaiantsGroups as $key=>$vaiantsGroup){
			$data = [
				'option'=>$key,
				'tags'=>$vaiantsGroup->pluck('variant')->toArray(),
			];
			$allData[]=$data;
		}
		$products->varinttags=$allData;
		$allVariants = $products->variants;
		foreach($allPrices as $price){
			$varinatCombine = $allVariants->whereIn('id',[$price->product_variant_one,$price->product_variant_two,$price->product_variant_three])->pluck('variant')->toArray();
			$data1['tag_combine'] = implode('/',$varinatCombine).'/';
			$data1['price'] = $price->price;
			$data1['stock'] = $price->stock;
			$allData1[$data1['tag_combine']]=$data1;
		}
		//dd($allData1);
		$products->pricecombine=$allData1;
		$passeddata=[
			'variants'=>$variants,
			'products'=>$products
		];
        return view('products.edit', compact('passeddata'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
