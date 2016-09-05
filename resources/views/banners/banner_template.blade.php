<div class="banner_box">
    @if(!empty($banner->url))
    <a href="{{ $banner->getUrl() }}" class="img" target="_blank" >
    @endif
        <img src="{{ url('/') }}/uploads/banners/{{$banner->img}}" />
    @if(!empty($banner->url))
    </a>
    @endif
</div>