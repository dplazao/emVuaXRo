{{-- @author dplazao 40132793 --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Edit this Building</h2>
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
                    <div class="card-header">Old Building</div>

                    <div class="card-body">
                        <h1>{{ $building->name }}</h1>
                        <p>ID: {{ $building->id }}</p>
                        <p>Space Fee: {{ $building->spaceFee }} $/m²</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="card-deck d-flex flex-wrap justify-content-around">
                <form method="POST" action="{{ route('building.editAction', $building->id)  }}">
                    @csrf
                    <div class="form-group">
                        <label for="buildingName">Building Name</label>
                        <input type="text" class="form-control" name="buildingName" id="buildingName" placeholder="Enter association name" value="{{ old('buildingName') ?? $building->name }}">
                    </div>

                    <div class="form-group">
                        <label for="buildingID">Building ID</label>
                        <input type="text" class="form-control" name="buildingID" id="buildingID" readonly value="{{ $building->id }}">
                    </div>

                    <div class="form-group">
                        <label for="associationID">Association ID</label>
                        <input type="text" class="form-control" name="buildingID" id="buildingID" readonly value="{{ $building->associationID }}">
                    </div>

                    <div class="form-group">
                        <label for="spaceFee">Space Fee</label>
                        <input type="number" step="0.01" class="form-control" name="spaceFee" id="spaceFee" placeholder="Enter space fee" value="{{ old('spaceFee') ?? $building->spaceFee }}">
                    </div>

                    <button type="submit" class="btn btn-success">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
