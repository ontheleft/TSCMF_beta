@extends('layout.default')

@section('content')
<!--breadcrumbs-->
<div id="content-header">
<div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a></div>
</div>
<!--End-breadcrumbs-->
<!-- PAGE CONTENT BEGINS -->
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title"> <span class="icon"> <i class="icon-th"></i> </span>
                    <h5>{{$config['title']}} - <a href="{{$config['router']}}/create?{{Request::getQueryString()}}">创建</a></h5>
                </div>
                <div class="widget-content nopadding">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                @foreach($config['items'] as $key=>$item)
                                @if(!isset($item['hidden'])||$item['hidden']!==true)
                                <th>{{$item['title']}}</th>
                                @endif
                                @endforeach
                                <th >查看/编辑/删除</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $value)
                        <tr>
                            @foreach($config['items'] as $key=>$item)
                            @if(!isset($item['hidden'])||$item['hidden']!==true)
                            @if($item['type']=='image')
                            <td>
                                @if($value[$key])
                                <img src="http://baicheng-cms.qiniudn.com/{{$value[$key]}}-w36" alt=""/>
                                @endif
                            </td>
                            @elseif($item['type']=='select')
                            <td>{{$item['select-items'][$value[$key]]}}</td>
                            @else
                            <td>{{$value[$key]}}</td>
                            @endif
                            @endif
                            @endforeach
                            <td>


                                <a class="btn btn-minier btn-success"
                                   href="{{ URL::to($config['router'].'/'. $value->id) }}?{{Request::getQueryString()}}">
                                    查看
                                </a>
                                <a class="btn btn-minier btn-info"
                                   href="{{ URL::to($config['router'].'/' . $value->id . '/edit') }}?{{Request::getQueryString()}}">
                                    编辑
                                </a>
                                {{ Form::open(array('url' => $config['router'].'/' . $value->id.'?'.Request::getQueryString(), 'class' =>
                                'pull-right')) }}
                                {{ Form::hidden('_method', 'DELETE') }}
                                {{ Form::submit('删除', array('class' => 'btn btn-minier btn-warning')) }}
                                {{ Form::close() }}
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <!-- /widget-body -->
                </div>
            </div>
        </div>
    </div>
</div>
@stop



