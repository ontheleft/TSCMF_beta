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
        if (Auth::check())
        {
            return Redirect::action('HomeController@getIndex');
        }
        else
        {
            return Redirect::action('ActionController@getLogin');
        }
    }


    public function missingMethod($parameters = array())
    {
        print_r($parameters);
        echo 'asdf';
        exit;
    }

}
