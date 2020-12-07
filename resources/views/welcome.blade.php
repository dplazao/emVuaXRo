@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">ConMgr</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <p>Welcome to ConMgr, the condo management platform.</p>

                        @guest
                            <p>You are not logged in.</p>
                            <h2>Don't have an account?</h2>
                            <p>Please ask your condo-association administrator to create an account for you.</p>
                            <h2>Have an account?</h2>
                            <a>
                                <a href="{{ route('login') }}">
                                    <button type="button" class="btn btn-primary">
                                        Login
                                    </button>
                                </a>
                            </a>
                        @else
                            <p>You are logged in, enjoy your day.</p>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
