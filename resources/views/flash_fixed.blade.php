@if (Session::has("message"))
    <div id="message">
        <div style="padding: 5px;">
            <div id="inner-message" class="alert {{Session::get("class")}}">
                <button type="button" class="pull-left close" data-dismiss="alert">&times;</button>
                &nbsp;<strong>{{Session::get("message")}}</strong>
            </div>
        </div>
    </div>
@endif