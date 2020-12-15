{{-- @author Ronick Uch 40093861 --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Delete your post</h2>
        </div>
        <div class="row justify-content-center">
            @if (!empty(session('message')))
                <div class="alert alert-{{ session('success') === null ? 'primary' : session('success') ? 'success' : 'danger' }}" role="alert">
                    {{ session('message') }}
                </div>
            @endif
        </div>
        <div class="row justify-content-center mb-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Your Posting</div>

                    <div class="card-body">
                        <h1>{{ $post->postName }}</h1>
                        <p>{{ $post->postText }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="card-deck d-flex flex-wrap justify-content-center">
                <form method="POST" action="{{ route('post.deleteAction', $post->id)  }}" style="text-align: center;">
                    @csrf
                    <h1>Are you sure?</h1>
                    <h3>This <i>cannot</i> be undone.</h3>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection
