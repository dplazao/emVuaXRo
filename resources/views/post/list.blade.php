{{-- @author ruch --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        @if (!empty(session('message')))
            <div class="alert alert-{{ session('success') === null ? 'primary' : session('success') ? 'success' : 'danger' }}" role="alert">
                {{ session('message') }}
            </div>
        @endif
        <h1>Postings</h1>
        @auth
            <div class="row justify-content-center mb-3">
                <a href="{{ route('post.create') }}">
                    <button type="button" class="btn btn-success">
                        Create Post
                    </button>
                </a>
            </div>
        @endauth
        <div class="row justify-content-center">
            <div class="card-deck d-flex flex-wrap justify-content-around">
                @foreach($posts as $post)
                    <div class="card flex-fill mb-3">
                        <div class="card-body">
                        	<h1>{{ $post->postName }}</h1>
                        	<h5>Posted by {{ $post->memberName }}</h5>
                        </div>
                        <div class="row justify-content-center mb-2">
                            <a href="{{ route('post.view', ['postID' => $post->id])  }}">
                            	<button type="button" class="btn btn-primary">
                                	View Post
                                </button>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
