{{-- @author dplazao --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Create a new association</h2>
        </div>
        <div class="row justify-content-center">
            @if (!empty(session('message')))
                <div class="alert alert-{{ session('success') === null ? 'primary' : session('success') ? 'success' : 'danger' }}" role="alert">
                    {{ session('message') }}
                </div>
            @endif
        </div>
        <div class="row justify-content-center">
            <div class="card-deck d-flex flex-wrap justify-content-around">
                <form method="POST" action="{{ route('association.createAction')  }}">
                    @csrf
                    <div class="form-group">
                        <label for="associationName">Association Name</label>
                        <input type="text" class="form-control" name="associationName" id="associationName" placeholder="Enter association name" value="{{ old('associationName')  }}">
                        <small id="associationNameHelp" class="form-text text-muted">You can change your association name later.</small>
                    </div>
                    <button type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection
