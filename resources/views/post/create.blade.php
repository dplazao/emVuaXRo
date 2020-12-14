{{-- @author ruch --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h2>Create a new post</h2>
        </div>
        <div class="row justify-content-center">
            @if (!empty(session('message')))
                <div class="alert alert-{{ session('success') === null ? 'primary' : session('success') ? 'success' : 'danger' }}" role="alert">
                    {{ session('message') }}
                </div>
            @endif
        </div>
        <div class="row justify-content-center">
            <div>
            <form method="POST" enctype="multipart/form-data" action="{{ route('post.createPost') }}">
                    @csrf
                    <div class="form-group">
                        <label for="postName">Post Name</label>
                        <input type="text" class="form-control" name="postName" id="postName" placeholder="Enter post name">
                    </div>
                    <div class="form-group">
                    	<label for="postText">Post Text</label>
                        <input type="text" class="form-control" name="postText" id="postText" placeholder="Enter something about your posting">
                    </div>
                    <div class="form-group">
                        <label for="image">Post Picture</label>
                        <input type="file" class="form-control-file" name="image" id="image">
                    </div>
                    <div class="form-group">
    					<label for="classification">Post Classification</label>
    						<div class="row">
                				<input type="radio" name="classification" value="viewOnly">View Only   
                				<input type="radio" name="classification" value="viewAndComment">View and Comment   
                				<input type="radio" name="classification" value="viewAndAddLink">View and Add Link   
            				</div>
            		</div>
            		<div class="form-group">
    					<label for="privacy">Post Privacy</label>
    						<div class="row">
                				<input type="radio" name="privacy" value="systemWide">System Wide
                				<input type="radio" name="privacy" value="condoOwners">Condo Owners  
                				<input type="radio" name="privacy" value="public">Public  
                				<input type="radio" name="privacy" value="private">Private 
                				<input type="radio" name="privacy" value="group">Group 
            				</div>
            		</div>
                	<button type="submit" class="btn btn-success">Create new post</button>
                </form>
            </div>
        </div>
    </div>
@endsection
