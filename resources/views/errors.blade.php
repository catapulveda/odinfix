@if(count($errors) > 0)
<div class="alert alert-danger" role="alert">
    {!! implode('', $errors->all('<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> :message<br>')) !!}
</div>
@elseif(Session::has('msg'))
    <div class="alert alert-success" role="alert">
        {!! Session::get('msg') !!}
    </div>
@endif