{{-- @author dplazao 40132793 --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Create a new relationship</h2>
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
                <form method="POST" action="{{ route('relationship.createAction')  }}">
                    @csrf
                    <div class="form-group">
                        <label for="memberID">Your ID</label>
                        <input type="number" class="form-control" name="memberID" id="memberID" placeholder="Enter your ID" readonly value="{{ \Illuminate\Support\Facades\Auth::id()  }}">
                        <small id="memberIDHelp" class="form-text text-muted">This is your ID.</small>

                        <div class="form-group">
                            <label for="relationshipType">Relationship Type</label>
                            <select class="form-control" name="relationshipType" id="relationshipType">
                                <option value="friend" selected>Friend</option>
                                <option value="family">Family</option>
                                <option value="colleague">Colleague</option>
                            </select>
                            <small id="relationshipTypeHelp" class="form-text text-muted">The type of relationship you have with the other user.</small>
                        </div>

                        <label for="withMemberID">With member ID</label>
                        <input type="number" class="form-control" name="withMemberID" id="withMemberID" placeholder="Enter other member ID" value="{{ old('withMemberID')  }}">
                        <small id="withMemberIDHelp" class="form-text text-muted">The ID of the other member you want to add a relationship to.</small>
                    </div>
                    <button type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection
