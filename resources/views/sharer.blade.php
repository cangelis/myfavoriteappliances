@extends('layouts.content')

@section('panel_heading')
My Sharers
@stop

@section('panel_body')


    @if ($sharers->isEmpty())
        <div class="alert alert-info">
            <strong>Oh No!</strong> Nobody shared a list with you :(
        </div>
    @else
        <table class="table table-striped">
            @foreach ($sharers as $sharer)
                <tr>
                    <td><a href="{{ action("Product@seeShare", $sharer->id) }}">{{ $sharer->name }}</a></td>
                    <td>{{ $sharer->email }}</td>
                    <td><a href="{{ action("Product@seeShare", $sharer->id) }}" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-eye-open"></i></a></td>
                </tr>
            @endforeach
        </table>
    @endif

@stop