@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">New MultiLogin task</div>

                    <div class="panel-body">
                        @include('errors')
                        <form method="POST">
                            {{csrf_field()}}
                            <table class="table">
                                @foreach($config as $name => $value)
                                    <tr><td>{{$name}}</td><td><input name="config[{{$name}}]" class="form-control" value="{{$value}}"></td></tr>
                                @endforeach
                            </table>
                            <br><br>
                            <input type="submit" name="add" value="Save" class="btn btn-success">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
