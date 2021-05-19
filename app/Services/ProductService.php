<?php


namespace App\Services;


use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
class ProductService
{
        protected $product;
        protected $productVariants;
        protected $files;
        public function setProduct($data)
        {
            $this->product = $data;
        }

        public function setProductVariants($data)
        {
            $this->productVariants = $data;
        }

        public function setFiles($data)
        {
            $this->files = $data;
        }

        public function storeProduct($data)
        {
            $product = Product::create($data);
            return $product->id;
        }

        public function updateProduct($data)
        {
            Product::find($this->product)->update($data);
        }

        public function storeProductVariant($data)
        {
            $variants = json_decode($data["product_variant"], true);
            $ids = array();
            foreach ($variants as $variant){
                foreach ($variant['tags'] as $tag){
                    $productVariant = new ProductVariant();
                    $productVariant->product_id = $this->product;
                    $productVariant->variant_id = $variant["option"];
                    $productVariant->variant = $tag;
                    $productVariant->save();
                    $ids[$tag] = $productVariant->id;
                }
            }
            return $ids;
        }

        public function updateProductVariant($data)
        {
            $this->deleteProductVariant();
            $ids = $this->storeProductVariant($data);
            return $ids;
        }

        public function deleteProductVariant()
        {
            ProductVariant::product($this->product)->delete();
        }

        public function storeProductVariantPrice($response)
        {
            $productVariantPrices = json_decode($response['product_variant_prices']);
            $data = array();
            foreach ($productVariantPrices as $productVariantPrice){
                $variationValue = explode('/',$productVariantPrice->title);
                $data[] = [
                    'price' => $productVariantPrice->price,
                    'stock' => $productVariantPrice->stock,
                    'product_id' => $this->product,
                    'product_variant_one' => !empty($this->productVariants[$variationValue[0]]) ? $this->productVariants[$variationValue[0]] : null,
                    'product_variant_two' => !empty($this->productVariants[$variationValue[1]]) ? $this->productVariants[$variationValue[1]] : null,
                    'product_variant_three' => !empty($this->productVariants[$variationValue[2]]) ? $this->productVariants[$variationValue[2]] : null,
                ];
            }
            ProductVariantPrice::insert($data);
        }

        public function updateProductVariantPrice($response)
        {
            $this->deleteProductVariantPrice();
            $this->storeProductVariantPrice($response);

        }

        public function deleteProductVariantPrice()
        {
            ProductVariantPrice::item($this->product)->delete();
        }

        public function saveImagesOnDB()
        {
            $images = $this->files;
            $data = array();
            foreach ($images as $image){
                $data[] = [
                    'product_id' => $this->product,
                    'file_path' => $image,
                ];
            }

            ProductImage::insert($data);
        }

        public function removeImage($data)
        {
            $productImage = ProductImage::find($data->image_id);
            $path = '/image/'.$productImage->file_path;
            if(file_exists(public_path($path)) && $productImage->file_path){
                unlink(public_path($path));
                $productImage->delete();
                return 'Image Removed';
            } else {
                return 'File not store on server';
            }
        }

        public function bulkImageDelete($images)
        {
            foreach ($images as $image){
                $path = '/image/'.$image->file_path;
                if(file_exists(public_path($path)) && $image->file_path) {
                    unlink(public_path($path));
                }
                $image->delete();
            }
        }
}