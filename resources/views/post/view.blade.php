{{-- @author ruch --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
            	<div class="card">
                    <div class="card-header">Viewing Post</div>
                    <div class="card-body">
                        @if (!empty(session('message')))
                            <div class="alert alert-{{ session('success') === null ? 'primary' : session('success') ? 'success' : 'danger' }}" role="alert">
                              {{ session('message') }}
                            </div>
                        @endif
                   		
                   		@if (empty($post))
                            <h1>That post does not seem to exist.</h1>
                            <p>Maybe it was deleted, you don't have access, or it never existed.</p>
                        @else
                   			<h1>{{ $post->postName }}</h1>
                   			<p>{{ $post->postText }}</p>
                   			{{ $post->postPicture }}
                   			<img src="{{ asset('storage/'.$post->postPicture) }}" alt="picture">
                   			
                   		@endif
                   	</div>
               	</div>
            </div>
        </div>
    </div>
@endsection
