{{-- @author dplazao 40132793 --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Create a new group</h2>
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
                <form method="POST" action="{{ route('group.createGroup')  }}">
                    @csrf
                    <div class="form-group">
                        <label for="groupName">Group Name</label>
                        <input type="text" class="form-control" name="groupName" id="groupName" placeholder="Enter group name">
                        <small id="groupNameHelp" class="form-text text-muted">You can change your group name later.</small>
                    </div>
                    <div class="form-group">
                        <label for="groupInformation">Group Information</label>
                        <textarea type="text" class="form-control" name="groupInformation" id="groupInformation" placeholder="A description of what your group is"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection
