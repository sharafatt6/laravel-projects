@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Posts') }}</div>

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

                    @include('layouts.assets.post_tab')
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal">
                            Create
                          </button>
                    </div>
                    <table class="table mt-4 table-bordered">
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Action</th>
                        </tr>
                        <tbody>
                            @forelse ($posts as $post)
                               <tr>
                                   <td>{{$post->id}}</td> 
                                   <td>{{$post->title}}</td> 
                                   <td>
                                    {{  \Illuminate\Support\Str::limit($post->content, 100) }}
                                    </td> 

                                   <td>
                                    <button onclick="editPost({{ $post->id }})" data-bs-toggle="modal" data-bs-target="#editPostModal" class="btn btn-sm btn-info">Edit</button>
                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" style="display:inline;">
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

<!-- Create Modal -->
<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('posts.store')}}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" id="title" name="title" class="form-control">
                    </div>
                    <div class="form-group mb-2">
                        <label for="content" class="form-label">Content</label>
                        <textarea id="content" name="content" class="form-control"></textarea>
                    </div>
                    <input type="hidden" name="user_id" value="{{Auth::id()}}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPostForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editPostTitle" class="form-label">Title</label>
                        <input type="text" id="editPostTitle" name="title" class="form-control">
                    </div>
                    <div class="form-group mb-2">
                        <label for="editPostContent" class="form-label">Content</label>
                        <textarea id="editPostContent" name="content" class="form-control"></textarea>
                    </div>
                    <input type="hidden" id="editPostId" name="id">
                    <input type="hidden" name="user_id" value="{{Auth::id()}}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script src="{{asset('js/jquery.min.js')}}"></script>
<script>
    function editPost(postId) {
        $.ajax({
            type: "GET",
            url: "{{ url('posts') }}/" + postId + "/edit",
            dataType: "json",
            success: function (response) {
                console.log(response.post);
                $('#editPostTitle').val(response.post.title);
                $('#editPostContent').val(response.post.content);
                $('#editPostId').val(response.post.id);
                $('#editPostForm').attr('action', "{{ url('posts') }}/" + postId);

                // $('#editPostModal').modal('show');
            },
            error: function (error) {
                console.log(error);
            }
        });
    }
    function confirmDelete(button) {
        if (confirm('Are you sure you want to delete this post?')) {
            $(button).closest('form').submit();
        }
    }</script>
