@extends('layouts.app')

@section('content')

    <form method="post" action='{{ url("/posts/update") }}'>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="post_id" value="{{ $post->id }}{{ old('post_id') }}">
        <div class="form-group">
            <input required="required" placeholder="Enter title here" type="text" name = "title" class="form-control" value="@if(!old('title')){{$post->title}}@endif{{ old('title') }}"/>
        </div>
        <div class="form-group">
		<textarea name='body' class="form-control">@if(!old('body')){!! $post->body !!}@endif{!! old('body') !!}
		</textarea>
        </div>
        <div class="form-group">
            <input required="required" value="@if(!old('slug')){{$post->slug}}@endif{{ old('slug') }}" placeholder="Enter slug here" type="text" name = "slug" class="form-control" />
        </div>
        @if($post->active == '1')
            <input type="submit" name='publish' class="btn btn-success" value = "Update"/>
        @else
            <input type="submit" name='publish' class="btn btn-success" value = "Publish"/>
        @endif
        <input type="submit" name='save' class="btn btn-default" value = "Save As Draft" />
        <a href="{{  url('posts/destroy/'.$post->id)}}" class="btn btn-danger">Delete</a>
    </form>
@endsection
