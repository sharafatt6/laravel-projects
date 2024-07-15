@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <h1 class="card-header">{{$post->title}}</h1>
                    <div id="{{$post->id}}" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @if (count($post->media) === 0)
                            <div class="carousel-item active">
                                <img src="{{asset('post_media/demo.jpg')}}" class="d-block w-100" alt="...">
                            </div>  
                
                            @endif
                            @foreach ($post->media as $media)
                            <div class="carousel-item {{$media->file_type === 'image' ? 'active' : ''}}">
                            @if ($media->file_type === 'image')
                                <img src="{{asset('post_media/'.$media->file)}}" class="d-block w-100" alt="...">
                             @elseif ($media->file_type === 'video')
                             <div class="ratio ratio-16x9">
                                <iframe src="{{asset('post_media/'.$media->file)}}" title="YouTube video" allowfullscreen></iframe>
                              </div> 
                            @endif
                        </div>    
                            @endforeach           
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#{{$post->id}}" data-bs-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#{{$post->id}}" data-bs-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Next</span>
                        </button>
                      </div>
                    <div class="card-body">
                        <p class="card-text">{{$post->content}}</p>
                    </div>
                    <div class="d-flex justify-content-start">
                        <a href="{{url('media/download/'.$post->id)}}" class="btn btn-primary mx-2">Download Media</a>
                    </div>
                    <div class="card-footer mt-5" style="background-color: #ffff">
                        <div class="row  d-flex justify-content-center">
                            <div class="col-md-10">
                                <div class="headings d-flex justify-content-between align-items-center mb-3">
                                    <h5>Comments</h5>
                                    <h5 class="badge bg-dark">Post Owner:{{' '}}<small>{{$post->user->name}}</small></h5>
                                </div>
                                 <div id="commentsContainer">                                {{--@foreach ($post->comments as $item) --}}
                                {{-- <div class="card p-3">
            
                                    <div class="d-flex justify-content-between align-items-center">
            
                                  <div class="user d-flex flex-row align-items-center">
            
                                    {{-- <img src="https://i.imgur.com/hczKIze.jpg" width="30" class="user-img rounded-circle mr-2"> 
                                    <span><small class="font-weight-bold text-primary">{{$item->user->name}}</small></span>
                                      
                                  </div>
                                  <small>2 days ago</small>
                                  </div>
                                  <div class="action d-flex justify-content-between mt-2 align-items-center">
            
                                    <div class="reply px-4">
                                      {{$item->content}}
                                       
                                    </div>
            
                                    <div class="icons align-items-center">
            
                                        <i class="fa fa-star text-warning"></i>
                                        <i class="fa fa-check-circle-o check-icon"></i>
                                        
                                    </div>
                                      
                                  </div>   
                                </div> --}}
                                {{-- @endforeach --}}
                       
                            </div>

                                <div class="p-2">
                                    <form >
                                        <div class="form-group">
                                            <label for="comment" class="form-label">Comment</label>

                                            <input type="hidden" id="post_id" name="post_id" value="{{$post->id}}">
                                            <textarea type="text" id="comment" name="comment" class="form-control"></textarea>
                                        </div>
                                        <button type="button" id="makeComment" class="btn btn-primary my-4">Post</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="{{asset('js/jquery.min.js')}}"></script>
<script>
    function downloadMedia (postId) { 
        const data = {
            'post_id' : postId
        };
        $.ajax({
            type: "post",
            url: "{{url('/media/download')}}",
            data: data,
            headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
            dataType: "json",
            success: function (response) {
                console.log(response);
            }
          
        });
     }
    $(document).ready(function () {
        $('#makeComment').on('click',function(){
            let comment = $('#comment').val();
            let postId = $('#post_id').val();
            let data = {
                    postId: postId,
                    comment: comment
                };        
            $.ajax({
                type: "post",
                url: "{{url('post/comment')}}",
                data: data,
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json",
                success: function (response) {
                $('#comment').val('');
                fetchComments();
            }
            });
        });

        function fetchComments() {

            let id = $('#post_id').val();
            const data = {
                    'post_id': id
                }
            $.ajax({
                type: "get",
                url: "{{url('/comments')}}",
                data: data,
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    $('#commentsContainer').html('');
                    $.each(response.comments, function (index, value) { 
                        let createdAt = new Date(value.created_at);
                    let formattedDate = createdAt.toLocaleDateString('en-GB', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                    });   
                    $('#commentsContainer').append(`
                 <div class="card p-3 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="user d-flex flex-row align-items-center">
                                        <span><small class="font-weight-bold text-primary">${value.user.name}</small></span>
                                    </div>
                                    <small>${formattedDate}</small>
                                </div>
                                <div class="mt-2">
                                    <p>${value.content}</p>
                                </div>
                            </div>
                `);
                    });
                }
            });
          }
          fetchComments();
    });
</script>