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
                                <th>Domain</th>
                                <th>CloudFlare</th>
                                <th>Cpanel</th>
                                <th>Copy files</th>
                                <th>Status</th>
                            </tr>
                            @foreach($task->domains as $domain)
                                <tr>
                                    <td>{{$domain->domain}}</td>
                                    <td>{!! $domain->cloudFlareMsg() !!}</td>
                                    <td>{!! $domain->cpanelMsg() !!}</td>
                                    <td>{!! $domain->copyFilesMsg() !!}</td>
                                    <td>{!! $domain->getStatus() !!}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
