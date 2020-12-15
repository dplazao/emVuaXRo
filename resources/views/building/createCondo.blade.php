{{-- @author dplazao --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Create a new condo</h2>
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
                <form method="POST" action="{{ route('building.createCondoAction', [ 'buildingID' => $building->id ])  }}">
                    @csrf
                    <div class="form-group">
                        <label for="parkingSpaces">Parking Spaces</label>
                        <input type="number" class="form-control" name="parkingSpaces" id="parkingSpaces" value="{{ old('parkingSpaces') }}" placeholder="Enter parking spaces">
                        <small id="parkingSpacesHelp" class="form-text text-muted">The email the member will use to login.</small>
                    </div>

                    <div class="form-group">
                        <label for="storageSpace">Storage Space</label>
                        <input type="number" class="form-control" name="storageSpace" id="storageSpace" value="{{ old('storageSpace') }}" placeholder="Enter storage space"/>
                        <small id="storageSpaceHelp" class="form-text text-muted">The email the member will use to send internal emails.</small>
                    </div>

                    <div class="form-group">
                        <label for="condoOwner">Condo Owner</label>
                        <input type="number" class="form-control" name="condoOwner" id="condoOwner" value="{{ old('condoOwner') }}" placeholder="Enter the condo owner"/>
                        <small id="condoOwnerHelp" class="form-text text-muted">The ID of the condo owner. You can change this later, or omit it if there's no owner right now.</small>
                    </div>

                    <div class="form-group">
                        <label for="buildingName">Building Name</label>
                        <input type="text" class="form-control" name="buildingName" id="buildingName" value="{{ $building->name  }}" readonly/>
                        <small id="buildingNameHelp" class="form-text text-muted">The building the condo will belong to.</small>
                    </div>

                    <div class="form-group">
                        <label for="buildingID">Building ID</label>
                        <input type="text" class="form-control" name="buildingID" id="buildingID" value="{{ $building->id  }}" readonly/>
                        <small id="buildingIDHelp" class="form-text text-muted">You cannot change this later, make sure it's right.</small>
                    </div>

                    <button type="submit" class="btn btn-success">Create Condo</button>
                </form>
            </div>
        </div>
    </div>
@endsection
