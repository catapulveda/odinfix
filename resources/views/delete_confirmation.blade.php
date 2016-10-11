@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Delete confirmation</div>

                    <div class="panel-body">
                        @include('errors')
                        <form method="POST" action="{{url('/delete/complete')}}">
                            {{csrf_field()}}
                            Total: <span class="label label-primary">{{$total}}</span> will be deleted <span class="label label-warning">{{$deleted}}</span>
                            <br><br>
                            <textarea name="domains" class="form-control" style="display: none;">{{implode("\n", $domainsToDelete)}}</textarea>
                            <br>
                            <input type="submit" name="delete" value="Confirmation" class="btn btn-danger">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
