@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Task #{{$task->id}} / {{$task->name}}</div>

                    <div class="panel-body">
                        @include('errors')

                        <table class="table table-hover">
                            <tr>
                                <th>Name</th>
                                <th>Num</th>
                                <th>Proxy</th>
                                <th>Status</th>
                                <th>Delete</th>
                            </tr>
                            @foreach($task->items()->withTrashed()->get() as $item)
                                <tr>
                                    <td>{{$item->name}}</td>
                                    <td>{{ $item->num }}</td>
                                    <td>{{ $item->getProxy() }}</td>
                                    <td>{!! $item->getStatus() !!}</td>
                                    @if(!$item->trashed())
                                        <td><a href="{{route('delete', $item)}}" class="btn btn-danger">Delete</a></td>
                                    @else
                                        <td><a href="#" class="btn btn-default disabled">Delete</a></td>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
