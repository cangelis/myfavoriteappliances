@extends('layouts.content')

@section('panel_heading')
Manage Shares
@stop

@section('panel_body')

<form class="form-inline" action="{{action('Share@newSharee')}}" method="POST">
    <div class="alert alert-info">
        <strong>Info!</strong> Enter the e-mail address of the user you want to share your wish list with
    </div>
    @include('layouts.flash')
    <div class="form-group">
        <label for="email">E-mail address</label>
        <input type="text" class="form-control" id="email" name="email" placeholder="E-mail address">
    </div>
    {{csrf_field()}}
    <button type="submit" class="btn btn-primary">Share!</button>
</form>

    <fieldset style="margin-top: 20px;">
        <legend>My Shares</legend>
    </fieldset>

    @if (Auth::getUser()->sharees->isEmpty())
        <div class="alert alert-info">
            <strong>Oh No!</strong> You did not share your list with anyone
        </div>

    @else
        <table class="table table-striped">
            @foreach (Auth::getUser()->sharees as $sharee)
                <tr>
                    <td>{{ $sharee->name }}</td>
                    <td>{{ $sharee->email }}</td>
                    <td><a onclick="return confirm('Are you sure?')" href="{{ action('Share@removeSharee', $sharee->id) }}" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i></a></td>
                </tr>
            @endforeach
        </table>
    @endif

@stop