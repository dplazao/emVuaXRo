{{-- @author dplazao 40132793 --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Viewing Building</div>

                    <div class="card-body">

                        @if (!empty(session('message')))
                            <div class="alert alert-{{ session('success') === null ? 'primary' : session('success') ? 'success' : 'danger' }}" role="alert">
                              {{ session('message') }}
                            </div>
                        @endif

                        @if (empty($building))
                            <h1>That building doesn't seem to exist.</h1>
                            <p>Maybe it was deleted, you don't have access, or it never existed.</p>
                        @else
                            <h1>{{ $building->name }}</h1>

                            <p>ID: {{ $building->id }}</p>
                            <p>Association ID: {{ $building->associationID }}</p>
                            <p>Space Fee: {{ $building->spaceFee }} $/m²</p>

                            <p>
                                @if ($user['canModifyBuilding'] || $user['isSysadmin'])

                                    @if ($user['isSysadmin'])
                                        <p class="card-subtitle mb-2 text-muted">You are a sysadmin.</p>
                                    @else
                                        <p class="card-subtitle mb-2 text-muted">You own this group.</p>
                                    @endif

                                    <p>
                                        <a href="{{ route('building.createCondoView', ['buildingID' => $building->id])  }}">
                                            <button type="button" class="btn btn-success">
                                                Create Condo
                                            </button>
                                        </a>
                                        <a href="{{ route('building.editView', ['buildingID' => $building->id])  }}">
                                            <button type="button" class="btn btn-primary">
                                                Edit Building
                                            </button>
                                        </a>
                                        @if ($user['isSysadmin'])
                                            <a href="{{ route('building.deleteView', ['buildingID' => $building->id])  }}">
                                                <button type="button" class="btn btn-danger">
                                                    Delete Building
                                                </button>
                                            </a>
                                        @endif
                                    </p>
                                @elseif ($user['isInBuilding'])
                                    <p>You are part of this building</p>
                                @else
                                    <p>You are not part of this building</p>
                                @endif
                            </p>

                            @if (empty($condos))
                                <p>This building is empty! Not even a single condo!</p>
                                <p>Here be ghosts!</p>
                            @else
                                <p>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Owner ID</th>
                                                <th>Parking Spaces</th>
                                                <th>Storage Space</th>
                                                @if ($user['canModifyBuilding'] || $user['isSysadmin'])
                                                    <th>Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($condos as $condo)
                                                <tr class="{{ $condo->ownerID === $user['id'] ? 'table-info' : ''  }}">
                                                    <td>{{$condo->id}}</td>
                                                    <td>{{$condo->ownerID}} @if ($condo->ownerID === $user['id']) <span class="badge badge-primary">Your Condo</span> @endif</td>
                                                    <td>{{$condo->parkingSpaces}}</td>
                                                    <td>{{$condo->storageSpace}}</td>
                                                    @if ($user['canModifyBuilding'] || $user['isSysadmin'])
                                                        <td>
                                                            @if ($user['canModifyBuilding'])
                                                                <a href="{{ route('building.removeCondo', ['buildingID' => $building->id, 'condoID' => $condo->id])  }}">
                                                                    <button type="button" class="btn btn-outline-danger">
                                                                        Remove Condo
                                                                    </button>
                                                                </a>
                                                            @endif
                                                            @if ($user['id'] == $condo->ownerID || $user['canModifyBuilding'])
                                                                <a href="{{ route('building.editCondoView', ['buildingID' => $building->id, 'condoID' => $condo->id])  }}">
                                                                    <button type="button" class="btn btn-outline-primary">
                                                                        Edit Condo
                                                                    </button>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
