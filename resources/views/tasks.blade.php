@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Tasks</div>

                    <div class="panel-body">
                        <h2>Tasks</h2>
                        @include('errors')

                        @if(count($tasks) > 0)
                            <table class="table table-hover">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Total</th>
                                    <th>Errors</th>
                                    <th>Success</th>
                                    <th>In process</th>
                                    <th>Status</th>
                                    <th>View</th>
                                </tr>

                                @foreach($tasks as $task)
                                    <tr>
                                        <td>{{$task->id}}</td>
                                        <td>{{$task->name}}</td>
                                        <td><span class="label label-info">{{$task->total()}}</span></td>
                                        <td><span class="label label-danger">{{$task->errors()}}</span></td>
                                        <td><span class="label label-success">{{$task->success()}}</span></td>
                                        <td><span class="label label-warning">{{$task->inProcess()}}</span></td>
                                        <td>
                                            @if($task->status == 0) <span class="label label-warning">In process</span>
                                            @else <span class="label label-success">Complete</span>
                                            @endif
                                        </td>
                                        <td><a href="{{route('task', ['task' => $task])}}" class="btn btn-primary">View</a></td>
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
