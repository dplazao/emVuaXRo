{{-- @author dplazao --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Create a new member</h2>
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
                <form method="POST" action="{{ route('association.createMemberAction', [ 'associationID' => $association->id ])  }}">
                    @csrf
                    <div class="form-group">
                        <label for="memberPrivilege">Member Privilege</label>
                        <select class="form-control" name="memberPrivilege" id="memberPrivilege" {{ $userIsSysadmin ? '' : 'readonly' }} contenteditable="{{ $userIsSysadmin }}">
                            <option value="owner" selected>Owner</option>
                            <option value="admin">Admin</option>
                        </select>
                        <small id="groupNameHelp" class="form-text text-muted">The privilege this user has over the association.</small>
                        <small id="groupNameHelp" class="form-text text-muted">Owner -> Condo owner</small>
                        <small id="groupNameHelp" class="form-text text-muted">Admin -> Can modify and change the association</small>
                        @if (!$userIsSysadmin)
                            <small id="groupNameHelp" class="form-text text-muted">Want to add another Admin? Contact your sysadmin.</small>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="memberEmail">Member Email</label>
                        <input type="email" class="form-control" name="memberEmail" id="memberEmail" value="{{ old('memberEmail') }}" placeholder="Enter email">
                        <small id="memberEmailHelp" class="form-text text-muted">The email the member will use to login.</small>
                    </div>

                    <div class="form-group">
                        <label for="memberInternalEmail">Member Internal Email</label>
                        <input type="text" class="form-control" name="memberInternalEmail" id="memberInternalEmail" value="{{ old('memberInternalEmail') }}" placeholder="Enter internal email"/>
                        <small id="memberInternalEmailHelp" class="form-text text-muted">The email the member will use to send internal emails.</small>
                    </div>

                    <div class="form-group">
                        <label for="memberPassword">Member Password</label>
                        <input type="password" class="form-control" name="memberPassword" id="memberPassword" value="{{ old('memberPassword') }}" placeholder="Enter password"/>
                        <small id="memberPasswordHelp" class="form-text text-muted">The password the member will use to login.</small>
                    </div>

                    <div class="form-group">
                        <label for="memberName">Member Name</label>
                        <input type="text" class="form-control" name="memberName" id="memberName" value="{{ old('memberName') }}" placeholder="Enter name">
                        <small id="memberNameHelp" class="form-text text-muted">The member's name.</small>
                    </div>

                    <div class="form-group">
                        <label for="memberAddress">Member Address</label>
                        <input type="text" class="form-control" name="memberAddress" id="memberAddress" value="{{ old('memberAddress') }}" placeholder="Enter address"/>
                        <small id="memberAddressHelp" class="form-text text-muted">The member's address.</small>
                    </div>

                    <div class="form-group">
                        <label for="memberAssociationName">Association Name</label>
                        <input type="text" class="form-control" name="memberAssociationName" id="memberAssociationName" value="{{ $association->name  }}" readonly/>
                        <small id="memberAssociationNameHelp" class="form-text text-muted">The association the member will belong to.</small>
                    </div>

                    <div class="form-group">
                        <label for="memberAssociationID">Association ID</label>
                        <input type="text" class="form-control" name="memberAssociationID" id="memberAssociationID" value="{{ $association->id  }}" readonly/>
                        <small id="memberAssociationIDHelp" class="form-text text-muted">You cannot change this later, make sure it's right.</small>
                    </div>

                    <button type="submit" class="btn btn-success">Create Member</button>
                </form>
            </div>
        </div>
    </div>
@endsection
