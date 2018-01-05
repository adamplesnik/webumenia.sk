@extends('layouts.master')

@section('title')
Pattern Library | @parent
@stop

@section('link')
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
@stop

@section('content')

<script>
    function toggle_source(event) {
        event.preventDefault();
        $(event.target).parent().find('pre.js-source').toggleClass('hidden');                    
        var txt = $(event.target).parent().find('pre.js-source').hasClass('hidden') ? '<i class="fa fa-code"></i> Show source' : '<i class="fa fa-code"></i> Hide source';
        $(event.target).html(txt);
    }
</script>

<section class="pattern-lib">
    <div class="container">
        <h1>Pattern Library</h1>
        
        @foreach ($components as $component)
            
            <section class="row">
                <div class="col-xs-12">

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2 class="panel-title">{{$component['name']}}</h2>
                        </div>
                        <div class="panel-body">
                            
                            <h4>Component</h4>
                            <div class="clearfix">
                                <div class="relative">
                                    @include($component['include_path'], $component['data'])
                                    @if (isset($component['include_path_js']))
                                        @section('javascript')
                                            @include($component['include_path_js'], $component['data_js'])
                                        @append
                                    @endif
                                </div>
                            </div>

                            <h4>Usage notes</h4> 
                            <p>{{$component['usage_notes']}}</p>

                            @if (isset($component['worked_on_this']))
                                <h4>Worked on this</h4> 
                                <p>
                                    @foreach ($component['worked_on_this'] as $developer)
                                        <span class="btn btn-default btn-xs btn-outline">{{ $developer }}</span>
                                    @endforeach
                                </p>
                            @endif

                            <a href="#" class="btn btn-default btn-outline sans" onclick="toggle_source(event);"><i class="fa fa-code"></i> Show source</a>
                            <pre class="js-source pre-scrollable hidden"><code class="html">{{$component['source_code']}}</code></pre>
                        </div>
                    </div>
                </div>
            </section>
        @endforeach
    </div>
</section>

@stop