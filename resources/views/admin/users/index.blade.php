@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Users') }}</div>

                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                    @include('admin.assets.post_tab')
                    <div class="d-flex justify-content-between">
                        <div>
                        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                            @csrf
              
                            <input type="file" name="file" class="form-control">
            
                            <br>
                            <button class="btn btn-success"><i class="fa fa-file"></i> Import User Data</button>
                        </form>
                    </div>

                        <div>
                              <a class="btn btn-warning mt-3" href="{{ route('users.export') }}"><i class="fa fa-download"></i> Export User Data</a>
                                                </div>

                    </div>
                    <table class="table mt-4 table-bordered">
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            {{-- <th>Action</th> --}}
                        </tr>
                        <tbody>
                            @forelse ($users as $user)
                               <tr>
                                   <td>{{$user->id}}</td> 
                                   <td>{{$user->name}}</td> 
                                   <td>
                                    {{ $user->email}}
                                    </td> 

                                   <td>
                                    <button onclick="editPost({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#editPostModal" class="btn btn-sm btn-info">Edit</button>
                                    <form action="{{ route('posts.destroy', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                   </td>
                               </tr>
                            @empty
                            <tr>
                                <td colspan="4">No Record Available</td>
                            </tr>  
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection