<?php

class CrudController extends BaseController
{
	protected $Model = '';

	/**
	 * Display a listing of the resource.
	 * GET /tests
	 *
	 * @return Response
	 */
	public function index()
	{
		$class        = $this->Model;
		$data         = $class::all();
		$admin_config = get_class_vars($class)['admin_config'] ? : [];

		$template = isset($admin_config['template_index']) ? $admin_config['template_index'] : 'crud.index';

		return View::make($template, [
			'page'   => [],
			'data'   => $data,
			'config' => $admin_config,
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /tests/create
	 *
	 * @return Response
	 */
	public function create()
	{
		$admin_config = get_class_vars($this->Model)['admin_config'] ? : [];
		$template     = isset($admin_config['template_edit']) ? $admin_config['template_edit'] : 'crud.edit';

		return View::make($template, [
			'page'   => [
				'action_path'   => $admin_config['router'],
				'action_method' => 'post',
				'scripts'       => [
					'markdown/markdown.min.js',
					'markdown/bootstrap-markdown.min.js',
					'jquery.hotkeys.min.js',
					'uncompressed/bootstrap-wysiwyg.js',
				],
			],
			'data'   => Request::all(),
			'config' => $admin_config,
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /tests
	 *
	 * @return Response
	 */
	public function store()
	{
		$admin_config = get_class_vars($this->Model)['admin_config'] ? : [];
		$rules        = $this->getRules($admin_config);
		$data         = Input::all();
		if (count($rules)) {
			$validator = Validator::make($data, $rules);

			if ($validator->fails()) {
				$messages = [];
				foreach ($validator->messages()->all() as $message) {
					$messages[] = [
						'class' => 'danger',
						'text'  => $message,
					];
				}
				Session::flash('messages', $messages);
			}
		}
		$class = $this->Model;
		$obj   = new $class;
		$this->saveObject($obj, $data, $admin_config);

		return Redirect::intended($admin_config['router'] . '?' . Request::getQueryString());
	}

	/**
	 * Display the specified resource.
	 * GET /tests/{id}
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function show($id)
	{
		return $this->edit($id);

	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /tests/{id}/edit
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function edit($id)
	{
		$pathinfo     = str_replace('/edit', '', Request::getPathInfo());
		$admin_config = get_class_vars($this->Model)['admin_config'] ? : [];
		$action_path  = isset($admin_config['store_path']) ? $admin_config['store_path'] : $pathinfo;
		$class        = $this->Model;
		$data         = $class::find($id);
//		print_r($data['plus_structure']);exit;
		$data = array_merge(Request::all(), $data->toArray());

		$template = isset($admin_config['template_edit']) ? $admin_config['template_edit'] : 'crud.edit';

		return View::make($template, [
			'page'   => [
				'action_path'   => $action_path,
				'action_method' => 'put',
				'scripts'       => [
					'markdown/markdown.min.js',
					'markdown/bootstrap-markdown.min.js',
					'jquery.hotkeys.min.js',
					'uncompressed/bootstrap-wysiwyg.js',
				],
			],
			'config' => $admin_config,
			'data'   => $data,
		]);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /tests/{id}
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function update($id)
	{

		$admin_config = get_class_vars($this->Model)['admin_config'] ? : [];
		$rules        = $this->getRules($admin_config);
		$data         = Input::all();
		if (count($rules)) {
			$validator = Validator::make($data, $rules);
			if ($validator->fails()) {
				$messages = [];
				foreach ($validator->messages()->all() as $message) {
					$messages[] = [
						'class' => 'danger',
						'text'  => $message,
					];
				}
				Session::flash('messages', $messages);

				return Redirect::to($admin_config['router'] . '/' . $id . '/edit');
			}

		}
		$class = $this->Model;

		$obj = $class::find($id);
		$this->saveObject($obj, $data, $admin_config);

		return Redirect::intended($admin_config['router'] . '?' . Request::getQueryString());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /tests/{id}
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$admin_config = get_class_vars($this->Model)['admin_config'] ? : [];
		$class        = $this->Model;
		$obj          = $class::find($id);
		$obj->delete();

		// redirect
		Session::flash('messages', [
			[
				'class' => 'info',
				'text'  => 'Successfully deleted!',
			]
		]);

		return Redirect::to($admin_config['router'] . '?' . Request::getQueryString());
	}

	protected function getRules($config)
	{
		$rules = [];
		foreach ($config['items'] as $key => $item) {
			if (isset($item['validator'])) {
				$rules[$key] = $item['validator'];
			}
		}

		return $rules;

	}

	protected function saveObject($obj, $data, $admin_config)
	{
		foreach ($admin_config['items'] as $key => $value) {
			if ($value['type'] === 'password') {
				if ($data[$key] != '') {
					$obj[$key] = Hash::make($data[$key]);
				} else {
					unset($obj[$key]);
				}


			} elseif ($value['type'] === 'plus_s') {
				$plus_structure_k = Input::get($key . '_k');
				$plus_structure_v = Input::get($key . '_v');
				$plus_structure   = [];
				if (count($plus_structure_v) === count($plus_structure_k)) {
					if ($plus_structure_k) {
						foreach ($plus_structure_k as $k => $v) {
							$plus_structure[$v] = $plus_structure_v[$k];
						}
					}
				}
				$obj[$key] = ($plus_structure);
			} elseif ($value['type'] === 'plus_d') {
				$plus_structure_k = Input::get($key . '_k');
				$plus_structure_v = Input::get($key . '_v');
				$plus_structure   = [];
				if (count($plus_structure_v) === count($plus_structure_k)) {
					if ($plus_structure_k) {
						foreach ($plus_structure_k as $k => $v) {
							$plus_structure[$v] = $plus_structure_v[$k];
						}
					}
				}
				$obj[$key] = ($plus_structure);
			} else {
				$obj[$key] = $data[$key];
			}
		}

//		echo '<pre>';print_r($obj);exit;
		$obj->save();

		return $obj;
	}

	public static function initRouter($configs)
	{
		if (count($configs)) foreach ($configs as $config) {
			Route::resource($config['router'], $config['router_controller']);
		}
	}

	function __construct()
	{
		parent::__construct();
	}
}
