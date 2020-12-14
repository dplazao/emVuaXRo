{{-- @author dplazao --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Edit this Association</h2>
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
                    <div class="card-header">Old Association</div>

                    <div class="card-body">
                        <h1>{{ $association->name }}</h1>
                        <p>ID: {{ $association->id }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="card-deck d-flex flex-wrap justify-content-around">
                <form method="POST" action="{{ route('association.editAction', $association->id)  }}">
                    @csrf
                    <div class="form-group">
                        <label for="associationName">Association Name</label>
                        <input type="text" class="form-control" name="associationName" id="associationName" placeholder="Enter association name" value="{{ old('associationName') ?? $association->name }}">
                    </div>
                    <div class="form-group">
                        <label for="associationID">Association ID</label>
                        <input type="text" class="form-control" name="associationID" id="associationID" readonly value="{{ $association->id }}">
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
