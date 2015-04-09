@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">New task</div>

                    <div class="panel-body">
                        @include('errors')
                        <form method="POST">
                            {{csrf_field()}}
                            <h4>Name</h4>
                            <input class="form-control" name="name" value="">

                            <h4>Domains</h4>
                            <textarea name="domains" class="form-control" rows="10"></textarea>
                            <br><br>
                            <input type="submit" name="add" value="Create" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
