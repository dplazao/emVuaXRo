{{-- @author dplazao --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Edit this Condo</h2>
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
                    <div class="card-header">Old Condo</div>

                    <div class="card-body">
                        <h1>Condo #{{ $condo->id }}</h1>
                        <p>Building ID: {{ $condo->buildingID }}</p>
                        <p>Owner ID: {{ $condo->ownerID }}</p>
                        <p>Parking Spaces: {{ $condo->parkingSpaces }}</p>
                        <p>Storage Spaces: {{ $condo->storageSpace }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="card-deck d-flex flex-wrap justify-content-around">
                <form method="POST" action="{{ route('building.editCondoAction', ['buildingID' => $condo->buildingID, 'condoID' => $condo->id])  }}">
                    @csrf

                    <div class="form-group">
                        <label for="buildingID">Building ID</label>
                        <input type="text" class="form-control" name="buildingID" id="buildingID" readonly value="{{ $condo->id }}">
                    </div>

                    <div class="form-group">
                        <label for="buildingID">Building ID</label>
                        <input type="text" class="form-control" name="buildingID" id="buildingID" readonly value="{{ $condo->buildingID }}">
                    </div>

                    <div class="form-group">
                        <label for="condoOwner">Owner ID</label>
                        <input type="text" class="form-control" name="condoOwner" id="condoOwner" value="{{ old('condoOwner') ?? $condo->ownerID }}">
                        <small id="condoOwnerHelp" class="form-text text-muted">Transfer the condo to another owner. <i>If you're current owner this is not reversible</i> except by having the new owner transfer it back!</small>
                        <small class="form-text text-muted">You can leave this empty for ownerless condos</small>
                    </div>

                    <div class="form-group">
                        <label for="parkingSpaces">Parking spaces</label>
                        <input {{ $canModifyBuilding ? '' : 'readonly' }} type="number" class="form-control" name="parkingSpaces" id="parkingSpaces" placeholder="Enter parking spaces" value="{{ old('parkingSpaces') ?? $condo->parkingSpaces }}">
                    </div>

                    <div class="form-group">
                        <label for="storageSpace">Storage space</label>
                        <input {{ $canModifyBuilding ? '' : 'readonly' }} type="number" class="form-control" name="storageSpace" id="storageSpace" placeholder="Enter storage space" value="{{ old('storageSpace') ?? $condo->storageSpace }}">
                    </div>

                    <button type="submit" class="btn btn-success">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
