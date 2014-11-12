<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    function __construct()
    {
        View::share(Config::get('admin'));
        if (Auth::check()) {
            $user_info = Auth::getUser()->toArray();
            if ($user_info['avatar']) {
                $user_info['avatar'] = sprintf('http://baicheng-cms.qiniudn.com/%s-w36', $user_info['avatar']);
            }
            View::share('user', $user_info);
        }
    }


    public function missingMethod($parameters = array())
    {
        print_r($parameters);
        echo 'asdf';
        exit;
    }

}
