@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">{{$title}}</div>

                    <div class="panel-body">
                        @include('errors')
                        <form method="POST">
                            {{csrf_field()}}
                            <h4>Prefix</h4>
                            <input class="form-control" name="prefix" value="">

                            <h4>From</h4>
                            <input class="form-control" name="from" value="">

                            <h4>To</h4>
                            <input class="form-control" name="to" value="">

                            @if(!$delete)
                                <h4>Proxies</h4>
                                <textarea name="ips" class="form-control"></textarea>

                            @endif
                            <br><br>
                            @if($delete)
                                <input type="submit" name="add" value="DELETE" class="btn btn-danger">
                            @else
                                <input type="submit" name="add" value="Create" class="btn btn-primary">
                            @endif
                        </form>

                        @if($delete)
                            <h2>Tasks</h2>

                            @if(count($tasks) > 0)
                                <table class="table table-hover">
                                    <tr>
                                        <th>#</th>
                                        <th>Prefix</th>
                                        <th>Range</th>
                                        <th>Status</th>
                                    </tr>

                                    @foreach($tasks as $task)
                                        <tr>
                                            <td>{{$task->id}}</td>
                                            <td>{{$task->prefix}}</td>
                                            <td>{{$task->from}} - {{$task->to}}</td>
                                            <td>{!! $task->getStatus() !!}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @else
                                <i>No tasks</i>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
