@extends('layout.default')

@section('content')

<!--breadcrumbs-->
<div id="content-header">
	<div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a></div>
</div>
<!--End-breadcrumbs-->
<!-- PAGE CONTENT BEGINS -->
<div class="container-fluid"><hr>
<div class="row-fluid">
	<div class="span12">
		<div class="widget-box">
			<div class="widget-title">
				<span class="icon"><i class="icon-info-sign"></i></span>
				<h5 class="lighter">{{$config['title']}}</h5>
			</div>

			<div class="widget-content nopadding">
				<form id="frm_edit" class="form-horizontal" role="form"  action="{{$page['action_path']}}?{{Request::getQueryString()}}"
					  method="post">
					@if($page['action_method']=='put')
					<input type="hidden" name="_method" value="PUT"/>
					@endif

					@foreach($config['items'] as $key=>$item)

					@if($item['type']=='image')
					<div class="control-group">
						<label class="control-label" for="ipt_{{$key}}">{{$item['title']}}</label>

						<div class="controls" id="container_{{$key}}">
							<input class="need_uploader" value="{{$data[$key] or ''}}" id="hid_{{$key}}" type="file"
								   name="{{$key}}"/>
							<input type="file" name="file_upload" id="file_upload" />
							<div id="preview_{{$key}}">
								@if(isset($data[$key])&&$data[$key])
								<img src="{{$data[$key]}}">
								@endif
							</div>
						</div>
						<input class="need_uploader" value="{{$data[$key] or ''}}" id="hid_{{$key}}" type="hidden"
							   name="{{$key}}"/>
					</div>
					
					@elseif($item['type']=='editor')
					<div>
						<h4 class="header green clearfix">
							{{$item['title']}}
						</h4>
						<div class="textarea_editor" id="by_editor_{{$key}}">{{$data[$key] or ''}}</div>
						<textarea id="hid_{{$key}}" style="display: none" name="{{$key}}">
							{{$data[$key] or ''}}
						</textarea>
					</div>
					<hr/>
					@elseif($item['type']=='hidden')
					<input value="{{$data[$key] or ''}}" type="hidden" name="{{$key}}"/>
					@elseif($item['type']=='password')
					<div class="control-group">
						<label class="control-label" for="ipt_{{$key}}">{{$item['title']}}</label>
						<div class="controls">
							<input autocomplete="false" name="{{$key}}"
								   type="{{$item['type']}}"
								   id="ipt_{{$key}}"
								   class="span4"
								   placeholder="请输入{{$item['title']}}">
						</div>
					</div>
					@elseif($item['type']=='textarea')
					<div class="control-group">
						<label class="control-label"  for="ipt_{{$key}}">{{$item['title']}}</label>
						<div class="controls">
							<textarea style="height: 300px" class="form-control" id="ipt_{{$key}}" name="{{$key}}">{{$data[$key] or ''}}</textarea>
						</div>

					</div>
					@elseif($item['type']=='select')
					<div class="control-group">
						<label class="control-label"  for="ipt_{{$key}}">{{$item['title']}}</label>
						<select class="form-control" name="{{$key}}">
							@foreach($item['select-items'] as $select_key=>$select_item)
							@if(isset($data[$key])&&$data[$key]==$select_key)
							<option selected value="{{$select_key}}">{{$select_item}}</option>
							@else
							<option value="{{$select_key}}">{{$select_item}}</option>
							@endif
							@endforeach
						</select>
					</div>
					@else
					<div class="control-group">
						<label class="control-label"  for="ipt_{{$key}}">{{$item['title']}}</label>
						<div class="controls">
							<input autocomplete="false" value="{{$data[$key] or ''}}" name="{{$key}}" class="span4"
								   type="{{$item['type']}}"
								   id="ipt_{{$key}}"
								   placeholder="请输入{{$item['title']}}">
						</div>
					</div>
					@endif
					@endforeach
					<div class="form-actions">
						<button type="button" class="btn btn-danger" onclick="history.back(-1)">
							<i class="icon-arrow-left"></i>
							取消
						</button>

						<button type="submit" class="btn btn-success">
							保存
							<i class="icon-arrow-right icon-on-right"></i>
						</button>
					</div>
				</form>
				<!-- /widget-main -->

				<!-- /widget-body -->
			</div>
		</div>
	</div>
</div>
</div>
@stop

@section('inline_scripts')

<script type="text/javascript">

	$(function () {
		$('#file_upload').uploadify({
			'swf'      : '{{APP_PUBLIC_PATH}}/uploadify.swf',
			'uploader' : '{{URL::action('FileController@upload')}}'
			// Put your options here
		});
		$('.textarea_editor').each(function () {
			var id = $(this).attr('id');
			$("#" + id).wysihtml5().each(function () {
				$(this).prev().addClass('wysiwyg-style2');
				$(this).on('blur', function () {
					$(this).next().html($(this).html());
				});
			});
		});
		//$('.textarea_editor').wysihtml5();
		$("#frm_edit").on('submit', function () {
			$('.wysiwyg-editor').each(function () {
				var html = $(this).html();
				$(this).next().html(html);
			});
		});
		$('a.plus_structure').on('click', function (e) {
			e.stopPropagation();
			e.preventDefault()
			var key = $(this).data('key');
			var html=$("#tpl_plus").html().replace(/\{key\}/g,key);
			$(this).after(html);
		});
	});
</script>
<script type="text/xml" id="tpl_plus">
	<div class=".col-md-12">
		<input name="{key}_k[]" type="text" class="" placeholder="输入名称"/>
		--
		<select name="{key}_v[]">
			<option value="0">文字类型</option>
			<option value="1">图片类型</option>
		</select>
		<a href="javascript:;" onclick="$(this).parent().remove()">X</a>
	</div>
</script>
@stop

