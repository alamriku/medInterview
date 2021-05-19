<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\Services\FileService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected  $product;
    protected $fileService;
    protected $product_variant = array();
    protected $data  = array();
    public function __construct(ProductService $product, FileService $fileService)
    {
        $this->product = $product;
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $variants = Variant::with('productVariants')->get();
//        $productPrices = ProductVariantPrice::with([
//            'product',
//            'productVariantOne.productImages',
//            'productVariantOne.variant',
//            'productVariantTwo.variant',
//            'productVariantThree.variant',
//        ])->paginate(5);
        $productPrices = Product::with([
            'productVariantPrices',
            'productVariantPrices.productVariantOne.productImages',
            'productVariantPrices.productVariantOne.variant',
            'productVariantPrices.productVariantTwo.variant',
            'productVariantPrices.productVariantThree.variant',
        ])->paginate(5);
        return view('products.index', compact('productPrices','variants'));
    }

    public function search(Request $request)
    {
        $variants = Variant::with('productVariants')->get();
        $data = Product::with([
            'productVariantPrices' => function($query) use ($request){
                if($request->price_from && $request->price_to){
                    $query->whereBetween('price', [$request->price_from, $request->price_to]);
                }

                $query->Orwhere('product_variant_one',$request->variant);
                $query->Orwhere('product_variant_two',$request->variant);
                $query->Orwhere('product_variant_three',$request->variant);
            },
            'productVariantPrices.productVariantOne.productImages',
            'productVariantPrices.productVariantOne.variant',
            'productVariantPrices.productVariantTwo.variant',
            'productVariantPrices.productVariantThree.variant',
        ]);

        if($request->title){
            $data->where('title',$request->title);
        }

        if($request->date){
            $data->whereDate('created_at',$request->date);
        }

        $productPrices = $data->paginate(5);
        //$productPrices = $data->get();
       // dd($productPrices);
        return view('products.search', compact('productPrices','variants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request)
    {
        try {
            DB::beginTransaction();

            $product = $this->product->storeProduct($request->only('title','sku','description'));
            $this->product->setProduct($product);
            $variants = $this->product->storeProductVariant($request->only('product_variant'));
            $this->product->setProductVariants($variants);
            $this->product->storeProductVariantPrice($request->only('product_variant_prices'));
            $files = $this->storeImages($request->file);
            $this->product->setFiles($files);
            $this->product->saveImagesOnDB();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
        }
        return response()->json(['success' => true, 'alert' => 'Product Saved'],200);
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
    public function edit(Product $product)
    {
        $variants = Variant::all();
            $product = $product->with([
            'productVariantPrices',
            'productVariantPrices.productVariantOne.productImages',
            'productVariantPrices.productVariantOne.variant',
            'productVariantPrices.productVariantTwo.variant',
            'productVariantPrices.productVariantThree.variant',
        ])->find($product->id);

        if(count($product->productVariantPrices)){
            $variantOne = $product->productVariantPrices[0]->productVariantOne;
            $variantTwo = $product->productVariantPrices[0]->productVariantTwo;
            $variantThree = $product->productVariantPrices[0]->productVariantThree;
            if ($variantOne) {
                $variantOne = $variantOne->variant_id;
                $this->arrangeElement($variantOne, $product->id);
            }
            if ($variantTwo) {
                $variantTwo = $variantTwo->variant_id;
                $this->arrangeElement($variantTwo, $product->id);
            }
            if ($variantThree) {
                $variantThree = $variantThree->variant_id;
                $this->arrangeElement($variantThree, $product->id);
            }
            $collector = $this->variantPriceStock($product->productVariantPrices);
        }
        $productVariantPrices = json_encode($collector);
        $productVariant = json_encode($this->product_variant);

        return view('products.edit', compact('variants','product','productVariant','productVariantPrices'));
    }

    public function arrangeElement($variantId,$productId)
    {
        $tagArray = ProductVariant::combinationElement(
            $variantId,
            $productId
        )->pluck('variant')->toArray();
        $this->data['option'] = $variantId;
        $this->data['tags'] = $tagArray;
        $this->product_variant[] = $this->data;
    }

    public function variantPriceStock($productVariants)
    {
        $collector = [];
        $data = [];
        foreach ($productVariants as $productVariantPrice){
            $variantOne = $productVariantPrice->productVariantOne;
            $variantTwo = $productVariantPrice->productVariantTwo;
            $variantThree = $productVariantPrice->productVariantThree;
            $one = $variantOne ? $variantOne->variant : '';
            $two = $variantTwo ? $variantTwo->variant : '';
            $three = $variantThree ? $variantThree->varinat : '';
            $data['title'] =  $one.'/'.$two.'/'.$three;
            $data['price'] = $productVariantPrice->price;
            $data['stock'] = $productVariantPrice->stock;
            $collector[] = $data;
        }
        return $collector;
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
        try {
            DB::beginTransaction();

            $this->product->setProduct($product->id);
            $this->product->updateProduct($request->only('title','sku','description'));
            $variants = $this->product->updateProductVariant($request->only('product_variant'));
            $this->product->setProductVariants($variants);
            $this->product->updateProductVariantPrice($request->only('product_variant_prices'));
            if($request->file){
                $files = $this->storeImages($request->file);
                $this->product->setFiles($files);
                $this->product->saveImagesOnDB();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
        }
        return response()->json(['success' => true, 'alert' => 'Product Updated'],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productVariantPrice = ProductVariantPrice::with([
            'product',
            'productVariantOne.productImages',
            'productVariantTwo',
            'productVariantThree',
        ])->find($id);
        $variantPriceOne = $productVariantPrice->productVariantOne
                           ? $productVariantPrice->productVariantOne->productVariantPricesOne->count()
                           : 0;

        $variantPriceTwo = $productVariantPrice->productVariantTwo
                           ? $productVariantPrice->productVariantTwo->productVariantPricesTwo->count()
                           : 0;
        $variantPriceThree = $productVariantPrice->productVariantThree
                             ? $productVariantPrice->productVariantThree->productVariantPricesThree->count()
                             : 0;
        $productAgainstVariants = $productVariantPrice->product->productVariantPrices->count();
        try{
            DB::beginTransaction();
            if($productAgainstVariants == 1){
                $productVariantPrice->product->delete();
                $this->removeAllImages($productVariantPrice->productVariantOne->productImages);
            }
            if($variantPriceOne == 1){
                $productVariantPrice->productVariantOne->delete();
            }
            if($variantPriceTwo == 1 ){
                $productVariantPrice->productVariantTwo->delete();
            }
            if($variantPriceThree == 1){
                $productVariantPrice->productVariantThree->delete();
            }
            $productVariantPrice->delete();
            DB::commit();
        }  catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
        }

        return  redirect()->back()->with(['message' => 'Deleted']);
    }

    public function removeAllImages($images)
    {
        $this->product->bulkImageDelete($images);
    }

    public function storeImages($files)
    {
        $fileNames = array();
        foreach ($files as $file){
            $fileNames[] = $this->fileService->storeFile($file);
        }
        return $fileNames;
    }

    public function removeImage(Request $request)
    {
        if ($request->expectsJson()) {
            try {
               $data = $this->product->removeImage($request);
            } catch (\Exception $e) {
                return response()->json([
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace(),
                ]);
            }
            return response()->json($data, 200);
        }
    }
}
