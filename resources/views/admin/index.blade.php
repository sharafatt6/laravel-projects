@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Admin Home') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @include('admin.assets.post_tab')
                  
                    <div class="p-2">
                        <h4 >Profile Edit</h4>
                        <form action="{{url('user/profile/update/'.Auth::id())}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="user_name" class="form-label">Name</label>
                                        <input name="user_name" type="text" class="form-control" value="{{Auth::user()->name}}">
                                    </div>
                                </div>
                                <input type="hidden" id="deleteAccoumt" name="delete_account" value="{{false}}">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="user_email" class="form-label">Email</label>
                                        <input name="user_email" type="text" class="form-control" value="{{Auth::user()->email}}">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                {{-- <form id="deleteAccount" action="{{ url('user/delete/account/').Auth::id() }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('post') --}}
                                    <button type="button" onclick="confirmDelete(this)" class="btn btn-danger mt-4 ">Account Delete</button>
                                {{-- </form> --}}
                               <button class="btn btn-primary mt-4">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

