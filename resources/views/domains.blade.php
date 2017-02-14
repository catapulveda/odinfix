@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Domains</div>

                    <h3>Total: <span class="label label-info">{{$domains->count()}}</span></h3>

                    <div class="panel-body">
                        @include('errors')

                        <textarea name="domains" class="form-control" rows="7">{{implode("\n", $list)}}</textarea>

                        <br><br>
                        <a href="{{url('/domains/download')}}" class="btn btn-success">Download</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
