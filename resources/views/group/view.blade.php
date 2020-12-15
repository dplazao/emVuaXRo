{{-- @author dplazao 40132793 --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Viewing Group</div>

                    <div class="card-body">

                        @if (!empty(session('message')))
                            <div class="alert alert-{{ session('success') === null ? 'primary' : session('success') ? 'success' : 'danger' }}" role="alert">
                              {{ session('message') }}
                            </div>
                        @endif

                        @if (empty($group))
                            <h1>That group doesn't seem to exist.</h1>
                            <p>Maybe it was deleted, you don't have access, or it never existed.</p>
                        @else
                            <h1>{{ $group->name }}</h1>
                            <p>{{ $group->information }}</p>

                            <p>
                                @if ($user['isOwner'])
                                    <p class="card-subtitle mb-2 text-muted">You own this group.</p>
                                    <p>
                                        <a href="{{ route('group.editView', ['groupID' => $group->id])  }}">
                                            <button type="button" class="btn btn-primary">
                                                Edit group
                                            </button>
                                        </a>
                                        <a href="{{ route('group.deleteView', ['groupID' => $group->id])  }}">
                                            <button type="button" class="btn btn-danger">
                                                Delete group
                                            </button>
                                        </a>
                                    </p>
                                @elseif ($user['isInGroup'])
                                    @if ($user['isAccepted'])
                                        <p class="card-subtitle mb-2 text-muted">You are a member of this group.</p>
                                        <a href="{{ route('group.leave', ['groupID' => $group->id])  }}">
                                            <button type="button" class="btn btn-danger">
                                                Leave group
                                            </button>
                                        </a>
                                    @else
                                        <p class="card-subtitle mb-2 text-muted">You requested to join this group, but you haven't been accepted yet.</p>
                                        <a href="{{ route('group.leave', ['groupID' => $group->id])  }}">
                                            <button type="button" class="btn btn-danger">
                                                Cancel Join Request
                                            </button>
                                        </a>
                                    @endif
                                @else
                                    @auth
                                        <a href="{{ route('group.join', ['groupID' => $group->id])  }}">
                                            <button type="button" class="btn btn-primary">
                                                Request to Join Group
                                            </button>
                                        </a>
                                    @endauth
                                    @guest
                                        <a href="{{ route('group.join', ['groupID' => $group->id])  }}">
                                            <button type="button" class="btn btn-primary">
                                                Login to join
                                            </button>
                                        </a>
                                    @endguest
                                @endif
                            </p>

                            @if (empty($members))
                                <p>This group is empty! Not even an owner!</p>
                                <p>Here be ghosts!</p>
                            @else
                                <p>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Internal Email</th>
                                                @if ($user['isOwner'])
                                                    <th>Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($members as $member)
                                                @if ($member->accepted || $member->id === $user['id'] || $user['isOwner'])
                                                    <tr class="{{ $member->id === $user['id'] ? 'table-info' : ''  }}">
                                                        <td>{{$member->id}}</td>
                                                        <td>{{$member->name}} @if ($member->isOwner) <span class="badge badge-success">Owner</span> @endif @if (!$member->accepted) <span class="badge badge-warning">Not Accepted</span> @endif @if ($member->id === $user['id']) <span class="badge badge-primary">You</span> @endif </td>
                                                        <td>{{$member->internalEmailAddress}}</td>
                                                        @if ($user['isOwner'])
                                                            <td>
                                                                @if ($member->id !== $user['id'])
                                                                    @if ($member->accepted)
                                                                        <a href="{{ route('group.transferOwnershipView', ['groupID' => $group->id, 'memberID' => $member->id])  }}">
                                                                            <button type="button" class="btn btn-outline-dark">
                                                                                Transfer ownership
                                                                            </button>
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ route('group.acceptMember', ['groupID' => $group->id, 'memberID' => $member->id])  }}">
                                                                            <button type="button" class="btn btn-outline-success">
                                                                                Accept Member
                                                                            </button>
                                                                        </a>
                                                                    @endif
                                                                    <a href="{{ route('group.removeMember', ['groupID' => $group->id, 'memberID' => $member->id])  }}">
                                                                        <button type="button" class="btn btn-outline-danger">
                                                                            Remove Member
                                                                        </button>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endif
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
