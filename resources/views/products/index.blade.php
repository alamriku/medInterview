@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
        @if(session('message'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>{{session('message')}}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
            @endif
    </div>


    <div class="card">
        <form action="{{route('search')}}" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">

                        @foreach($variants as $variant)
                            <optgroup label="{{$variant->title}}">
                                @foreach($variant->productVariants as $productVariant)
                                <option value="{{$productVariant->id}}">{{$productVariant->variant}}</option>
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
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @php $i = 1; @endphp
                    @foreach($productPrices as $productPrice)

                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $productPrice->title }} <br> {{ $productPrice->created_at->isoFormat('LLLL') }}</td>
                            <td>{{ $productPrice->description }}</td>
                            <td style="height: 80px; overflow: hidden" id="variant{{$loop->iteration}}">
                                @foreach($productPrice->productVariantPrices as $data)
                                    @php
                                        $variantOne = $data->productVariantOne ? $data->productVariantOne->variant : '';
                                        $variantTwo = $data->productVariantTwo ? ' / '.$data->productVariantTwo->variant : '';
                                        $variantThree =  $data->productVariantThree ? " / ".$data->productVariantThree->variant : '';
                                    @endphp
                                <dl class="row mb-0" >

                                    <dt class="col-sm-3 pb-0">
                                        {{ $variantOne.$variantTwo.$variantThree }}
                                    </dt>
                                    <dd class="col-sm-9">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4 pb-0">Price : {{ number_format($data->price,2) }}</dt>
                                            <dd class="col-sm-8 pb-0">InStock : {{ number_format($data->stock,2) }}</dd>
                                        </dl>
                                    </dd>
                                </dl>
                                @endforeach
                                    <button onclick="$('#variant{{$loop->iteration}}').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
{{--                                    <button  class="btn btn-danger" onclick="confirm('Are you sure about this ?') ? document.getElementById('delete-form{{$productPrice->id}}').submit(): event.preventDefault()">Delete</button>--}}
{{--                                    <form action="{{route('product.destroy',$productPrice->id)}}" style="display: none;"--}}
{{--                                          id="delete-form{{$productPrice->id}}" method="post">--}}
{{--                                        @csrf--}}
{{--                                        @method("DELETE")--}}
{{--                                    </form>--}}
                                    <a href="{{ route('product.edit', $productPrice->id) }}" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>

                    @endforeach
{{--                    @php $i = 1; @endphp--}}
{{--                    @foreach($productPrices as $productPrice)--}}
{{--                        @php--}}
{{--                            $variantOne = $productPrice->productVariantOne ? $productPrice->productVariantOne->variant : '';--}}
{{--                            $variantTwo = $productPrice->productVariantTwo ? ' / '.$productPrice->productVariantTwo->variant : '';--}}
{{--                            $variantThree =  $productPrice->productVariantThree ? " / ".$productPrice->productVariantThree->variant : '';--}}
{{--                        @endphp--}}
{{--                        <tr>--}}
{{--                            <td>{{ $i++ }}</td>--}}
{{--                            <td>{{ $productPrice->product->title }} <br> {{ $productPrice->product->created_at->isoFormat('LLLL') }}</td>--}}
{{--                            <td>{{ $productPrice->product->description }}</td>--}}
{{--                            <td>--}}
{{--                                <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant_{{$loop->iteration}}">--}}

{{--                                    <dt class="col-sm-3 pb-0">--}}
{{--                                        {{ $variantOne.$variantTwo.$variantThree }}--}}
{{--                                    </dt>--}}
{{--                                    <dd class="col-sm-9">--}}
{{--                                        <dl class="row mb-0">--}}
{{--                                            <dt class="col-sm-4 pb-0">Price : {{ number_format($productPrice->price,2) }}</dt>--}}
{{--                                            <dd class="col-sm-8 pb-0">InStock : {{ number_format($productPrice->stock,2) }}</dd>--}}
{{--                                        </dl>--}}
{{--                                    </dd>--}}
{{--                                </dl>--}}
{{--                                <button onclick="$('#variant_{{$loop->iteration}}').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>--}}
{{--                            </td>--}}
{{--                            <td>--}}
{{--                                <div class="btn-group btn-group-sm">--}}
{{--                                    <button  class="btn btn-danger" onclick="confirm('Are you sure about this ?') ? document.getElementById('delete-form{{$productPrice->id}}').submit(): event.preventDefault()">Delete</button>--}}
{{--                                    <form action="{{route('product.destroy',$productPrice->id)}}" style="display: none;"--}}
{{--                                          id="delete-form{{$productPrice->id}}" method="post">--}}
{{--                                        @csrf--}}
{{--                                        @method("DELETE")--}}
{{--                                    </form>--}}
{{--                                    <a href="{{ route('product.edit', $productPrice->product->id) }}" class="btn btn-success">Edit</a>--}}
{{--                                </div>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        @endforeach--}}


                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{($productPrices->currentpage()-1)*$productPrices->perpage()+1}} to {{(($productPrices->currentpage()-1)*$productPrices->perpage())+$productPrices->count()}} of {{$productPrices->total()}}</p>
                </div>
                {{ $productPrices->links() }}
            </div>
        </div>
    </div>

@endsection
