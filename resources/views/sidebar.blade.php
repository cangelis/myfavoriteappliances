<div class="col-md-3">
    <div class="panel panel-default">
        <div class="panel-heading">Categories</div>
        <div class="panel-body">
            <ul>
            @foreach (config("app.product_categories") as $category)
                <li><a href="{{action("Product@filterByCategory", $category["id"])}}">{{$category["title"]}}</a></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>