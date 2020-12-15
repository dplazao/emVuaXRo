{{-- @author dplazao 40132793 --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Transfer ownership</h2>
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
                    <div class="card-header">Your Group</div>

                    <div class="card-body">
                        <h1>{{ $group->name }}</h1>
                        <p>{{ $group->information }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mb-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">New Owner</div>

                    <div class="card-body">
                        <h1>{{ $member->name }}</h1>
                        <p>{{ $member->internalEmailAddress }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="card-deck d-flex flex-wrap justify-content-center">
                <form method="POST" action="{{ route('group.transferOwnershipAction', [$group->id, $member->id])  }}" style="text-align: center;">
                    @csrf
                    <h1>Are you sure?</h1>
                    <h3>To undo this, you will need the other user to transfer the group back.</h3>
                    <button type="submit" class="btn btn-danger">Transfer Ownership</button>
                </form>
            </div>
        </div>
    </div>
@endsection
