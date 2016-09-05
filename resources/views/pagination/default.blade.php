<?php
$currentPage = $paginator->currentPage();
$lastPage = $paginator->lastPage();
$dots = '<li>...</li>';
$from = 1;
$to = $lastPage;
$dd1 = $dd2 = '';
$firstLast = false;
if($lastPage > 5)
{
    $firstLast = true;
    if($currentPage < 3)
    {
        $to = 3;
        $dd2 = $dots;
    }
    elseif($currentPage > ($lastPage - 2))
    {
        $from = ($lastPage - 2);
        $dd1 = $dots;
    }
    if($currentPage >= 3 && $currentPage <= ($lastPage - 2)) 
    {
        $from = ($currentPage - 1);
        $to = ($currentPage + 1);
        if($currentPage > 3)
        {
            $dd1 = $dots;
        }
        if($currentPage < ($lastPage - 2))
        {
            $dd2 = $dots;
        }
    }
}
?>

@if ($lastPage > 1)
    <ul class="pagination">
        @if($currentPage >= 3 && $firstLast)
        <li>
            <a href="{{ $paginator->url(1) }}">{{ 1 }}</a>
        </li>
        @endif
        {!! $dd1 !!}
        @for($i = $from; $i <= $to; $i++)
        <li class="{{ ($currentPage == $i) ? ' disabled' : '' }}">
            <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
        </li>
        @endfor
        {!! $dd2 !!}
        @if($currentPage <= ($lastPage - 2) && $firstLast)
        <li>
            <a href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
        </li>
        @endif
    </ul>
@endif