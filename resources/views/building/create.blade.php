{{-- @author dplazao --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Create a new building</h2>
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
                <form method="POST" action="{{ route('building.createAction')  }}">
                    @csrf
                    <div class="form-group">
                        <label for="buildingName">Building Name</label>
                        <input type="text" class="form-control" name="buildingName" id="buildingName" placeholder="Enter building name" value="{{ old('buildingName')  }}">
                        <small id="buildingNameHelp" class="form-text text-muted">You can change your building name later.</small>

                        <label for="associationID">Association ID</label>
                        <input type="number" class="form-control" name="associationID" id="associationID" placeholder="Enter association ID" value="{{ old('associationID')  }}">
                        <small id="associationIDHelp" class="form-text text-muted">The association ID to create a building for. You can't change this later.</small>

                        <label for="spaceFee">Space Fee</label>
                        <input type="number" step="0.01" class="form-control" name="spaceFee" id="spaceFee" placeholder="Enter space fee" value="{{ old('spaceFee')  }}">
                        <small id="spaceFeeHelp" class="form-text text-muted">The cost in $/m² for each condo in this building. You can change this later</small>
                    </div>
                    <button type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection
