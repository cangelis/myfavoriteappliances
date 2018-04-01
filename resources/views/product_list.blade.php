@extends('layouts.content')

@section('panel_heading')
    {{ $title or "Products" }}
    <select onchange="onOrderChange.call(this)" class="pull-right">
        <option value="price_asc" {{selected("price_asc")}}>Price Ascending</option>
        <option value="price_desc" {{selected("price_desc")}}>Price Descending</option>
        <option value="name_asc" {{selected("name_asc")}}>Product Name A-Z</option>
        <option value="name_desc" {{selected("name_desc")}}>Product Name Z-A</option>
    </select>
    <div class="pull-right">Order By&nbsp;</div>
    <div class="clearfix"></div>
@stop

@section('panel_body')

    <?php
    function selected($item) {
        if ((Request::get("order_by", "name_asc") == $item))
        {
            return "selected";
        }
        return "";
    }
    ?>

    @include('layouts.flash_fixed')
    @if (isset($shareable))
        <a href="{{ action('Share@index') }}" class="btn btn-primary btn-sm pull-right"><i class="glyphicon glyphicon-share"></i> Share Your List!</a>
    @endif
    <div class="clearfix"></div>
    @foreach ($products as $product)
        <span id="product-{{$product->id}}"></span>
        <div class="row">
            <div class="col-md-4">
                <img class="img-responsive" src="{{$product->image_url}}">
            </div>
            <div class="col-md-5">
                <img src="{{$product->brand_image_url}}" class="img-responsive">
                <h4>{{$product->name}}</h4>
                <ul>
                    @foreach ($product->features as $feature)
                        <li>{{$feature}}</li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-3">
                <h3>&euro;{{$product->price}}</h3>
                @if (!Auth::getUser()->wishes->contains($product) && (!isset($shared_list)))
                    <a href="{{ action('Product@addList', $product->id) }}" class="btn-block btn btn-danger btn-sm"><i class="glyphicon glyphicon-heart"></i> Add to wish list</a>
                @else
                    @php
                    if (isset($shared_list))
                    {
                        $action = action('Product@removeShared', [$product->id, $sharer->id]);
                    }
                    else
                    {
                        $action = action('Product@removeList', $product->id);
                    }
                    @endphp
                    <a onclick="return confirm('Are you sure you want to remove from the list?')" href="{{ $action }}" class="btn-block btn btn-danger btn-sm"><i class="glyphicon glyphicon-remove"></i> Remove from wish list</a>
                @endif
            </div>
        </div><hr>
    @endforeach
    @if ($products->isEmpty())
        <div style="margin-top: 10px;" class="alert alert-info">
            No Products Here!
        </div>
    @endif
    <?php
    if (!is_null(Request::get("order_by", null)))
    {
        echo $products->appends(["order_by" => Request::get("order_by")])->links();
    }
    else
    {
        echo $products->links();
    }
    ?>

@stop

@section('scripts')
<script type="text/javascript">
    function onOrderChange() {
        window.location.href = updateQueryStringParameter(window.location.href.split('#')[0], "order_by", $(this).val())
    }
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }
        else {
            return uri + separator + key + "=" + value;
        }
    }
</script>
@stop

@section('styles')
    <style>
        #message {
            position: fixed;
            top: 10px;
            right: 0;
            z-index: 9999;
        }
        #inner-message {
            margin: 0 auto;
        }
    </style>
@stop
