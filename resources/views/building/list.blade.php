{{-- @author dplazao --}}
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
                <a href="{{ route('building.create') }}">
                    <button type="button" class="btn btn-success">
                        Create Building
                    </button>
                </a>
            </div>
        @endauth
        <div class="row justify-content-center">
            <div class="card-deck d-flex flex-wrap justify-content-around">
                @foreach($buildings as $building)
                    <div class="card flex-fill mb-3">
                        <div class="card-body">
                                <h1>{{ $building->name }}</h1>
                                @auth
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        ID <span class="badge badge-primary">{{ $building->id  }}</span>
                                    </h6>
                                @endauth

                                <p>Space Fee: {{ $building->spaceFee }} $/m²</p>
                                <p>Condos: {{ $building->condoCount }}</p>

                                <a href="{{ route('building.view', ['buildingID' => $building->id])  }}">
                                    <button type="button" class="btn btn-primary">
                                        View Building
                                    </button>
                                </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
