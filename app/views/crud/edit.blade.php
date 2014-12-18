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
					@if(array_key_exists('attr',$item)&&$item['attr']=='onlyShow')
						<?php continue; ?>
					@endif
					@if($item['type']=='image')
					<div class="form-group">
						<label class="control-label" for="ipt_{{$key}}">{{$item['title']}}</label>

						<div class="controls" id="container_{{$key}}">
							<a class="btn btn-default btn-lg " id="ipt_{{$key}}" href="#">
								<i class="glyphicon glyphicon-plus"></i>
								<sapn>选择文件</sapn>
							</a>

							<div id="preview_{{$key}}">
								@if(isset($data[$key])&&$data[$key])
								<img src="http://baicheng-cms.qiniudn.com/{{$data[$key]}}-w100">
								@endif
							</div>
						</div>
						<input class="need_uploader" value="{{$data[$key] or ''}}" id="hid_{{$key}}" type="hidden"
							   name="{{$key}}"/>
					</div>
					<hr/>
					@elseif($item['type']=='editor')
					<div>
						<h4 class="header green clearfix">
							{{$item['title']}}
						</h4>
						<div class="wysiwyg-editor" id="by_editor_{{$key}}">{{$data[$key] or ''}}</div>
						<textarea id="hid_{{$key}}" style="display: none" name="{{$key}}">
							{{$data[$key] or ''}}
						</textarea>
					</div>
					<hr/>
					@elseif($item['type']=='hidden')
					<input value="{{$data[$key] or ''}}" type="hidden" name="{{$key}}"/>
					@elseif($item['type']=='password')
					<div class="form-group">
						<label class="control-label" for="ipt_{{$key}}">{{$item['title']}}</label>
						<div class="controls">
							<input autocomplete="false" name="{{$key}}"
								   type="{{$item['type']}}"
								   id="ipt_{{$key}}"
								   placeholder="请输入{{$item['title']}}">
						</div>
					</div>
					@elseif($item['type']=='plus_s')
					<div class="form-group">
						<label class="control-label"  for="ipt_{{$key}}">{{$item['title']}}</label>
						<a href="#" data-key="{{$key}}" class="plus_structure">+++</a>
						@if(isset($data[$key])&&$data[$key])
						@foreach($data[$key] as $kk=> $vv)
						<div class="controls .col-md-12">
							<input name="{{$key}}_k[]" type="text" value="{{$kk}}" placeholder="输入名称"/>
							--
							<select name="{{$key}}_v[]">
								<option {{$vv===0?'selected="selected"':''}} value="0">文字类型</option>
								<option {{$vv===1?'selected="selected"':''}} value="1">图片类型</option>
							</select>
							<a href="javascript:;" onclick="$(this).parent().remove()">X</a>
						</div>
						@endforeach
						@endif
					</div>
					@elseif($item['type']=='plus_d'&&isset($data['plus_s']))
					<div class="form-group">
						<label class="control-label"  for="ipt_{{$key}}">{{$item['title']}}</label>
						@foreach(array_merge($data['plus_s'],isset($data[$key])?$data[$key]:[]) as $kk=> $vv)
						<div class="controls .col-md-12">
							{{$kk}} :
							<input name="{{$key}}_v[]" type="text" value="{{$vv}}" placeholder="输入{{$kk}}"/>
							<input name="{{$key}}_k[]" type="hidden" value="{{$kk}}"/>
						</div>
						@endforeach
					</div>
					@elseif($item['type']=='textarea')
					<div class="form-group">
						<label class="control-label"  for="ipt_{{$key}}">{{$item['title']}}</label>
						<div class="controls">
							<textarea style="height: 300px" class="form-control" id="ipt_{{$key}}" name="{{$key}}">{{$data[$key] or ''}}</textarea>
						</div>

					</div>
					@elseif($item['type']=='select')
					<div class="form-group">
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
					<div class="form-group">
						<label class="control-label"  for="ipt_{{$key}}">{{$item['title']}}</label>
						<div class="controls">
							<input autocomplete="false" value="{{$data[$key] or ''}}" name="{{$key}}"
								   type="{{$item['type']}}"
								   id="ipt_{{$key}}"
								   placeholder="请输入{{$item['title']}}">
						</div>
					</div>
					@endif
					@endforeach
					<div class="form-actions">
						<button type="button" class="btn btn-warning" onclick="history.back(-1)">
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
<script type="text/javascript" src="/admin/assets/js/plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="/admin/assets/js/plupload/i18n/zh_CN.js"></script>
<script type="text/javascript" src="/admin/assets/js/qiniu.js"></script>
<script type="text/javascript">
	$(function () {
		$(".need_uploader").each(function () {
			var name = $(this).attr('name');
			Qiniu.uploader({
				runtimes     : 'html5,flash,html4',
				browse_button: 'ipt_' + name,
				container    : 'container_' + name,
				drop_element : 'container_' + name,
				max_file_size: '100mb',
				flash_swf_url: '/admin/assets/js/plupload/Moxie.swf',
				dragdrop     : true,
				chunk_size   : '4mb',
				uptoken_url  : '/file/token',
				domain       : 'http://baicheng-cms.qiniudn.com/',
				auto_start   : true,
				init         : {
					'Key'         : function (up, file) {
						var key = $.ajax({
							url  : "/file/key/master",
							async: false
						}).responseText;
						return key;
					},
					'BeforeUpload': function (up, file) {
						$('#preview_' + name).html('上传中..');
					},
					'FileUploaded': function (up, file, info) {
						var info = $.parseJSON(info);
						if (info.key) {
							$("#hid_" + name).val(info.key);
							$('#preview_' + name).empty().append($('<img/>').attr('src', 'http://baicheng-cms.qiniudn.com/' + info.key + '-w100'));
						} else {
							alert('上传失败');
						}

					}
				}
			});
		});

		$('.wysiwyg-editor').each(function () {
			var id = $(this).attr('id');
			$("#" + id).ace_wysiwyg({
				toolbar: [
					'font',
					null,
					'fontSize',
					null,
					{name: 'bold', className: 'btn-info'},
					{name: 'italic', className: 'btn-info'},
					{name: 'strikethrough', className: 'btn-info'},
					{name: 'underline', className: 'btn-info'},
					null,
					{name: 'insertunorderedlist', className: 'btn-success'},
					{name: 'insertorderedlist', className: 'btn-success'},
					{name: 'outdent', className: 'btn-purple'},
					{name: 'indent', className: 'btn-purple'},
					null,
					{name: 'justifyleft', className: 'btn-primary'},
					{name: 'justifycenter', className: 'btn-primary'},
					{name: 'justifyright', className: 'btn-primary'},
					{name: 'justifyfull', className: 'btn-inverse'},
					null,
					{name: 'createLink', className: 'btn-pink'},
					{name: 'unlink', className: 'btn-pink'},
					null,
					{name: 'insertImage', className: 'btn-success'},
					null,
					'foreColor',
					null,
					{name: 'undo', className: 'btn-grey'},
					{name: 'redo', className: 'btn-grey'}
				]
			}).each(function () {
				$(this).prev().addClass('wysiwyg-style2');
				$(this).on('blur', function () {
					$(this).next().html($(this).html());
				});
			});
		});

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

