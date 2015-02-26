@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Delete domains</div>

                    <div class="panel-body">
                        @include('errors')
                        <form method="POST">
                            {{csrf_field()}}
                            <textarea name="domains" class="form-control" rows="7"></textarea>
                            <br>
                            <input type="submit" name="delete" value="Delete" class="btn btn-danger">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
