@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Delete tasks</div>

                    <div class="panel-body">
                        <h2>Tasks</h2>
                        @include('errors')

                        @if(count($tasks) > 0)
                            <table class="table table-hover">
                                <tr>
                                    <th>#</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>

                                @foreach($tasks as $task)
                                    <tr>
                                        <td>{{$task->id}}</td>
                                        <td><span class="label label-info">{{$task->total}}</span></td>
                                        <td>
                                            @if($task->status == 0) <span class="label label-warning">In-process</span>
                                            @else <span class="label label-success">Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{$task->created_at->format('d.m.Y H:i')}}
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
