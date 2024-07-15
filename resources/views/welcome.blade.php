@extends('layouts.app')

@section('content')
    

<div class="container">
    <h1 class="text-center mb-4">Let's Get Start to Learn</h1>
    <div class="row">
        @foreach ($posts as $item)
        <?php
        $image = $item->image ?? 'demo.jpg';
       ?>

        <div class="col-md-4"> 
        <div class="card " style="background-color: #ffff">
           <h4 class="card-header">{{$item->title}}</h4>
           <div id="carouselExampleControls{{$item->id}}" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @if (count($item->media) === 0)
                <div class="carousel-item active">
                    <img src="{{asset('post_media/demo.jpg')}}" class="d-block w-100" alt="...">
                </div>  
    
                @endif
                @foreach ($item->media as $media)
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
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls{{$item->id}}" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls{{$item->id}}" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
        
           {{-- @if ($item->file_extension === 'mp4')
          
           {{-- <video class="w-100" autoplay loop muted>
            <source src="{{asset('post_media/'.$item->file_name)}}" type="video/mp4" />
         </video>     
          @else
          <img src="{{asset('post_media/'. $image)}}" class="card-img-top" alt="...">

          @endif--}}
           <div class="card-body">
            <p class="card-text">
                {{ \Illuminate\Support\Str::limit($item->content, 200) }}
            </p>
            <p class="badge bg-light text-dark border p-2"><span class="text-dark">{{__('Writer:')}}</span>{{' '.$item->user->name}}</p>
            <div>
                <a href="{{url('post/'.$item->slug)}}" class="btn btn-dark text-light">Read More</a>
            </div>
           </div>
        </div>
    </div>
    @endforeach
    </div>
</div>
@endsection