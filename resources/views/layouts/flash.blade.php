@if (Session::has("message"))
    <div class="alert {{Session::get("class")}}">
        {{Session::get("message")}}
    </div>
@endif