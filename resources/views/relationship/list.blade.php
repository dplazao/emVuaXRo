{{-- @author dplazao 40132793 --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        @if (!empty(session('message')))
            <div class="alert alert-{{ session('success') === null ? 'primary' : session('success') ? 'success' : 'danger' }}" role="alert">
                {{ session('message') }}
            </div>
        @endif
        @auth
            <div class="row justify-content-center mb-3">
                <a href="{{ route('relationship.create') }}">
                    <button type="button" class="btn btn-success">
                        Add relationship
                    </button>
                </a>
            </div>
        @endauth
        <div class="row justify-content-center">
            <div class="card-deck d-flex flex-wrap justify-content-around">
                @foreach($relationships as $relationship)
                    <div class="card flex-fill mb-3">
                        <div class="card-body">
                            <h1>{{ $relationship->firstID === $userID ? $relationship->secondName : $relationship->firstName }}</h1>
                            <h5 class="card-subtitle mb-2 text-muted">
                                ID <span class="badge badge-primary" style="margin-top: -5px">{{ $relationship->firstID === $userID ? $relationship->secondID : $relationship->firstID  }}</span>
                            </h5>

                            <h2 class="card-subtitle mb-2">
                                <span class="badge badge-primary">{{ $relationship->type  }}</span>
                            </h2>

                            <form method="POST" action="{{ route('relationship.delete', ['memberID' => $relationship->firstID, 'withMemberID' => $relationship->secondID])  }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    Remove relationship
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
