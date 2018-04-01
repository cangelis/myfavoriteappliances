@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        @include("sidebar")
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    @yield('panel_heading')
                </div>

                <div class="panel-body">
                    @yield('panel_body')
                </div>
            </div>
        </div>
    </div>
</div>
@stop