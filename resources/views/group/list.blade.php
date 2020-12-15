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
                <a href="{{ route('group.create') }}">
                    <button type="button" class="btn btn-success">
                        Create Group
                    </button>
                </a>
            </div>
        @endauth
        <div class="row justify-content-center">
            <div class="card-deck d-flex flex-wrap justify-content-around">
                @foreach($groups as $group)
                    <div class="card flex-fill mb-3">
                        <div class="card-body">
                                <h1>{{ $group->name }}</h1>
                                @auth
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        @if ($group->isOwner)
                                            <span class="badge badge-primary">Owner</span>
                                        @elseif ($group->hasJoinRequest)
                                            <span class="badge badge-light">Join Request Pending</span>
                                        @elseif ($group->isInGroup)
                                            <span class="badge badge-primary">In group</span>
                                        @endif
                                    </h6>
                                @endauth
                                <p>{{ $group->information }}</p>

                                <a href="{{ route('group.view', ['groupID' => $group->id])  }}">
                                    <button type="button" class="btn btn-primary">
                                        View Group
                                    </button>
                                </a>
                                <a href="{{ route('group.view', ['groupID' => $group->id])  }}">
                                    <button type="button" class="btn btn-outline-dark">
                                        Members <span class="badge badge-light" style="padding-top: 4px;">{{ $group->memberCount  }}</span>
                                    </button>
                                </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
