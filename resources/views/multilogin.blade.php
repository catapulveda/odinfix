@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">MultiLogin Tasks</div>

                    <div class="panel-body">
                        @include('errors')

                        @if(count($tasks) > 0)
                            <table class="table table-hover">
                                <tr>
                                    <th>#</th>
                                    <th>Prefix</th>
                                    <th>Range</th>
                                    <th>Total</th>
                                    <th>Errors</th>
                                    <th>Success</th>
                                    <th>In process</th>
                                    <th>Status</th>
                                    <th>View</th>
                                    <th>Delete</th>
                                </tr>

                                @foreach($tasks as $task)
                                    <tr>
                                        <td>{{$task->id}}</td>
                                        <td>{{$task->prefix}}</td>
                                        <td>{{$task->from}} - {{$task->to}}</td>
                                        <td><span class="label label-info">{{$task->total()}}</span></td>
                                        <td><span class="label label-danger">{{$task->errors()}}</span></td>
                                        <td><span class="label label-success">{{$task->success()}}</span></td>
                                        <td><span class="label label-warning">{{$task->inProcess()}}</span></td>
                                        <td>
                                            @if($task->status == 0) <span class="label label-warning">In process</span>
                                            @elseif($task->status == -1) <span class="label label-primary">Deleted</span>
                                            @else <span class="label label-success">Complete</span>
                                            @endif
                                        </td>
                                        <td><a href="{{route('items', ['task' => $task])}}" class="btn btn-primary">View</a></td>
                                        <td>
                                            @if($task->status == 1)
                                                <a href="{{route('delete_task', ['task' => $task])}}" class="btn btn-danger">Delete</a>
                                            @else
                                                <a href="#" class="btn btn-default disabled">Delete</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            <i>Tasks not found</i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
