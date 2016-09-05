<!--Если это текущая страница, то ссылка делается не кликабельной-->
<div class="breadcrumbs">
    <div class="main_page catalog_path breadcrumb"><a href="/">Главная</a></div>
    @if( isset($brand) )
    <div class="catalog_path breadcrumb">
        <?php $href = "brand/".$brand->alias; ?>
        <a {{ $path !== $href?"href=/$href":"class='cur'" }}>{{ $brand->title }}</a>
    </div>
    @endif
    @if( isset($cur_catalog) )
    <div class="catalog_path breadcrumb">
        <?php $href = "brand/".$brand->alias."/".$cur_catalog->alias; ?>
        <a {{ $path !== $href?"href=/$href":"class='cur'" }}>
            {{ $cur_catalog->name }}
        </a>
    </div>
    @endif


    
    @if( isset($category_alias) )
    <div class="catalog_path breadcrumb">
        <?php $href = "brand/" . $brand->alias . "/" . $cur_catalog->alias."/".$category_alias; ?>
        <a {{ $path !== $href?"href=/$href":"class='cur'" }}>
            {{ $category->value }}
        </a>
    </div>
    @endif
    
    
    

</div>