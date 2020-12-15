{{-- @author dplazao 40132793 --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Viewing Association</div>

                    <div class="card-body">

                        @if (!empty(session('message')))
                            <div class="alert alert-{{ session('success') === null ? 'primary' : session('success') ? 'success' : 'danger' }}" role="alert">
                              {{ session('message') }}
                            </div>
                        @endif

                        @if (empty($association))
                            <h1>That association doesn't seem to exist.</h1>
                            <p>Maybe it was deleted, you don't have access, or it never existed.</p>
                        @else
                            <h1>{{ $association->name }}</h1>

                            <p>
                                @if ($user['isOwner'] || $user['isSysadmin'])

                                    @if ($user['isSysadmin'])
                                        <p class="card-subtitle mb-2 text-muted">You are a sysadmin.</p>
                                    @else
                                        <p class="card-subtitle mb-2 text-muted">You own this group.</p>
                                    @endif

                                    <p>
                                        <a href="{{ route('association.createMemberView', ['associationID' => $association->id])  }}">
                                            <button type="button" class="btn btn-success">
                                                Create Member
                                            </button>
                                        </a>
                                        <a href="{{ route('association.editView', ['associationID' => $association->id])  }}">
                                            <button type="button" class="btn btn-primary">
                                                Edit Association
                                            </button>
                                        </a>
                                        @if ($user['isSysadmin'])
                                            <a href="{{ route('association.deleteView', ['associationID' => $association->id])  }}">
                                                <button type="button" class="btn btn-danger">
                                                    Delete Association
                                                </button>
                                            </a>
                                        @endif
                                    </p>
                                @elseif ($user['isInAssociation'])
                                    <p>You are part of this association</p>
                                @else
                                    <p>You are not part of this association</p>
                                @endif
                            </p>

                            @if (empty($members))
                                <p>This association is empty! Not even an owner!</p>
                                <p>Here be ghosts!</p>
                            @else
                                <p>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Internal Email</th>
                                                @if ($user['isOwner'] || $user['isSysadmin'])
                                                    <th>Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($members as $member)
                                                <tr class="{{ $member->id === $user['id'] ? 'table-info' : ''  }}">
                                                    <td>{{$member->id}}</td>
                                                    <td>{{$member->name}} @if ($member->isOwner) <span class="badge badge-success">Owner</span> @endif @if ($member->id === $user['id']) <span class="badge badge-primary">You</span> @endif </td>
                                                    <td>{{$member->internalEmailAddress}}</td>
                                                    @if ($user['isOwner'] || $user['isSysadmin'])
                                                        <td>
                                                            @if ($member->id !== $user['id'] && !$member->isOwner)
                                                                <a href="{{ route('association.removeMember', ['associationID' => $association->id, 'memberID' => $member->id])  }}">
                                                                    <button type="button" class="btn btn-outline-danger">
                                                                        Remove Member
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
