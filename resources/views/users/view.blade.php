{{-- @author Annes Cherid 40038453 --}}
@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Users</div>
                    <div class="card-body">
                        @if (!empty(session('message')))
                            <div
                                class="alert alert-{{ (session('success') === null ? 'primary' : session('success')) ? 'success' : 'danger' }}"
                                role="alert">
                                {{ session('message') }}
                            </div>
                        @endif
                        <p>Last updated at {{ date('Y-m-d h:i:s') }}<br> Choose your options:</p>
                        <a href="{{ route('users.list')}}">
                            <button type="button" class="btn btn-outline-info">
                                Display All Users
                            </button>
                        </a>
                        <br>
                        <br>
                        <button type="button" class="collapsible btn btn-outline-success">Create a new User</button>
                        <div class="content" style="display: none">
                            <form method="GET" action="{{ route('users.createUser')  }}">
                                @csrf
                                <div class="form-group">
                                    <label for="userName">User Name</label>
                                    <input type="text" class="form-control" name="userName" id="userName"
                                           placeholder="Enter user name">
                                    <input type="text" class="form-control" name="password" id="password"
                                           placeholder="Enter password">
                                    <input type="text" class="form-control" name="address" id="address"
                                           placeholder="Enter your address">
                                    <input type="text" class="form-control" name="email" id="email"
                                           placeholder="Enter email">
                                    <small id="userNameHelp" class="form-text text-muted">You can change your user name
                                        later.</small>
                                </div>
                                <a href="{{ route('users.createUser')}}">
                                    <button type="submit" class="btn btn-success">Create</button>
                                </a>
                            </form>
                        </div>
                        <br>
                        <br>
                        <button type="button" class="collapsible btn btn-warning">Edit a User</button>
                        <div class="content" style="display: none">
                            <form method="GET" action="{{ route('users.editUser')  }}">
                                @csrf
                                <div class="form-group">
                                    <label for="userName">What is the ID of the user you want to edit?</label>
                                    <input type="text" class="form-control" name="ID" id="ID" placeholder="Enter ID">
                                    <input type="text" class="form-control" name="userName" id="userName"
                                           placeholder="Enter Name you want to change">
                                    <input type="text" class="form-control" name="email" id="email"
                                           placeholder="Enter Email you want to change">
                                    <input type="text" class="form-control" name="password" id="password"
                                           placeholder="Enter password you want to change">
                                    <input type="text" class="form-control" name="address" id="address"
                                           placeholder="Enter Address you want to change">
                                </div>
                                <a href="{{ route('users.editUser')}}">
                                    <button type="submit" class="btn btn-success">Edit</button>
                                </a>
                            </form>
                        </div>
                        </br>
                        </br>
                        <button type="button" class="collapsible btn btn-outline-danger">Delete a User</button>
                        <div class="content" style="display: none">
                            <form method="GET" action="{{ route('users.deleteUser')  }}">
                                @csrf
                                <div class="form-group">
                                    <label for="userName">User Name</label>
                                    <input type="text" class="form-control" name="userName" id="userName"
                                           placeholder="Enter user name">
                                    <input type="text" class="form-control" name="ID" id="ID"
                                           placeholder="Enter the ID of the user you want to delete">
                                </div>
                                <a href="{{ route('users.deleteUser')}}">
                                    <button type="submit" class="btn btn-success">Delete</button>
                                </a>
                            </form>
                        </div>
                        </br>
                        </br>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var coll = document.getElementsByClassName('collapsible')
            var i
            for (i = 0; i < coll.length; i++) {
                coll[i].addEventListener('click', function () {
                    this.classList.toggle('active')
                    var content = this.nextElementSibling
                    if (content.style.display === 'block') {
                        content.style.display = 'none'
                    } else {
                        content.style.display = 'block'
                    }
                })
            }</script>
    </div>
@endsection
