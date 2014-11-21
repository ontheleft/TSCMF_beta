<!DOCTYPE html>
<html lang="en">
<head>
        <title>{{$title}}--TSCMF</title><meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="{{APP_PUBLIC_PATH}}/css/bootstrap.min.css" />
        <link rel="stylesheet" href="{{APP_PUBLIC_PATH}}/css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="{{APP_PUBLIC_PATH}}/css/matrix-login.css" />
        <link href="{{APP_PUBLIC_PATH}}/font-awesome/css/font-awesome.css" rel="stylesheet" />
        <link href='{{APP_PUBLIC_PATH}}/css/font-css.css' rel='stylesheet' type='text/css'>
    </head>
    <body>
        <div id="loginbox">            
            <form id="loginForm" class="form-vertical" method="post" action="{{URL::action('ActionController@postLogin')}}">
				 <div class="control-group normal_text"> <h3><img src="{{APP_PUBLIC_PATH}}/img/logo.png" alt="Logo" /></h3></div>
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lg"><i class="icon-user"></i></span><input type="text" name="username" placeholder="用户名" />
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_ly"><i class="icon-lock"></i></span><input type="password" name="password" placeholder="密码" />
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <span class="pull-left"><a href="#" class="flip-link btn btn-info" id="to-recover"> 忘记密码?</a></span>
                    <span class="pull-right"><a id="loginA" href="javascript:void(0);" class="btn btn-success" > 登陆</a></span>
                </div>
            </form>
            <form id="recoverform" action="#" class="form-vertical">
				<p class="normal_text">Enter your e-mail address below and we will send you instructions how to recover a password.</p>
				
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lo"><i class="icon-envelope"></i></span><input type="text" placeholder="E-mail address" />
                        </div>
                    </div>
               
                <div class="form-actions">
                    <span class="pull-left"><a href="#" class="flip-link btn btn-success" id="to-login">&laquo; Back to login</a></span>
                    <span class="pull-right"><a class="btn btn-info">Reecover</a></span>
                </div>
            </form>
        </div>

        <script src="{{APP_PUBLIC_PATH}}/js/jquery.min.js"></script>
        <script src="{{APP_PUBLIC_PATH}}/js/matrix.login.js"></script>

    </body>

</html>
