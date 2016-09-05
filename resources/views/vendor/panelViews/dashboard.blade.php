@extends('panelViews::mainTemplate')
@section('page-wrapper')
<style>
    .nav-pills li{
        font-size:18px;
        padding:10px 0;
        display:inline-block;
        width:100%;
    }
</style>
        
            <div class="row">

                <div class="col-lg-12">
                    <h1 class="page-header">{{ \Lang::get('panel::fields.dashboard') }}</h1>
                    <div class="icon-bg ic-layers"></div>
                </div>
                            
            </div>
            <!-- /.row -->
            <div class="row box-holder">
                <ul class="nav nav-pills nav-stacked">
                @if(is_array(\Serverfireteam\Panel\Link::returnUrls()))
                    @foreach (Serverfireteam\Panel\libs\dashboard::create() as $box)
                    <li class="active alert-info">
                       <div class="col-lg-5 title"><a href='{{$box['showListUrl']}}' class="pull-left"> {{$box['title']}}</a></div>
                       <div class="col-lg-1">  <span class="badge">{{$box['count']}}</span></div>
                       <div class="col-lg-6">
                        <a href='{{$box['showListUrl']}}' class="pull-left">
                            Перейти
                            <i class="icon ic-chevron-right"></i>
                        </a>
                        </div>
                       </li>
                       
                    

                    @endforeach
                    <li class="active alert-info">
                        <span class="col-lg-12">
                        <a  href="/panel/Product/parse_xml">
        
                            Обновление из XML </a> 
                            </span>

                    </li>
                    <li class="active alert-info">
                        <span class="col-lg-12">
                        <a  href="/panel/Product/catalog_cache">

                            Кеширование каталога </a> 
                            </span>

                    </li>
                                        <li class="active alert-info">
                        <span class="col-lg-12">
                        <a  href="/panel/Product/null_catalog">

                            Товары без каталога </a> 
                            </span>

                    </li>
                    </ul>

                @endif


            </div>
            <div class="row hide update">
                <div class="alert alert-warning" role="alert">
                    <a href="http://laravelpanel.com/docs/master/update" class="alert-link"></a>
                </div>
            </div>

<script>
    $(function(){
        var color = ['primary','green','orange','red','purple','green2','blue2','yellow'];
        var pointer = 0;
        $('.panel').each(function(){
            if(pointer > color.length) pointer = 0;
            $(this).addClass('panel-'+color[pointer]);
            $(this).find('.pull-right .add').addClass('panel-'+color[pointer]);
            pointer++;
        })
        // check for update of laravelpanel 
        $.getJSON( "http://api.laravelpanel.com/checkupdate/{{ $version }}", function( data ) {
          if(data.needUpdate){
            $(".update a").text(data.text);
            $(".update").removeClass('hide');
          }
        })
        
    })
</script>
@stop            
