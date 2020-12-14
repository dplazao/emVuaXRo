{{-- @author Annes Cherid 40038453 --}}
@extends('layouts.app')
@section('content')
    <div class="container">
        @if (!empty(session('message')))
            <div
                class="alert alert-{{ (session('success') === null ? 'primary' : session('success')) ? 'success' : 'danger' }}"
                role="alert">
                {{ session('message') }}
            </div>
        @endif
        @auth
        @endauth
        <div class="row justify-content-center">
            <div class="card-deck d-flex flex-wrap justify-content-around">
                @foreach($users as $user)
                    <div class="card flex-fill mb-3">
                        <div class="card-body">
                            <h2>Username: {{ $user->name }}</h2>
                            <h4>ID: {{ $user->id }}</h4>
                            <h4>Privilege: {{ $user->privilege }}</h4>
                            <h4>Status: {{ $user->status }}</h4>
                            <h4>email: {{ $user->email }}</h4>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
