<?php

global $header_no_menus;

$header_no_menus = true;
require dirname(__FILE__).'/header.php';

?><section id="wrapper" class="login-register">
<div class="login-box">
  <div class="white-box">
	<form class="form-horizontal form-material" id="loginform" action="">
	  <h3 class="box-title m-b-20">Sign In</h3>
	  <div class="form-group ">
		<div class="col-xs-12">
		  <input name="login" class="form-control" type="text" required="" placeholder="Username">
		</div>
	  </div>
	  <div class="form-group">
		<div class="col-xs-12">
		  <input name="password" class="form-control" type="password" required="" placeholder="Password">
		</div>
	  </div>
	  <div class="form-group">
		<div class="col-md-12">
		  <div class="checkbox checkbox-primary pull-left p-t-0">
			<input id="checkbox-signup" type="checkbox" name="isremember" value="1">
			<label for="checkbox-signup"> Remember me </label>
		  </div>
		  <!-- <a href="javascript:void(0)" id="to-recover" class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i> Forgot pwd?</a> -->
		</div>
	  </div>
	  <div class="form-group text-center m-t-20">
		<div class="col-xs-12">
		  <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light btn_login" type="submit">Log In</button>
		</div>
	  </div>
	</form>
	<form class="form-horizontal" id="recoverform" action="">
	  <div class="form-group ">
		<div class="col-xs-12">
		  <h3>Recover Password</h3>
		  <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
		</div>
	  </div>
	  <div class="form-group ">
		<div class="col-xs-12">
		  <input class="form-control" type="text" required="" placeholder="Email">
		</div>
	  </div>
	  <div class="form-group text-center m-t-20">
		<div class="col-xs-12">
		  <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
		</div>
	  </div>
	</form>
  </div>
</div>
</section>
<script type="text/javascript">
jQuery(document).ready(function()
{
	jQuery('.btn_login').on('click', function(e)
	{
		e.preventDefault();

		var form = jQuery('#loginform');
		var data = {
			'login': jQuery('[name="login"]', form).val(),
			'password': jQuery('[name="password"]', form).val(),
			'isremember': jQuery('[name="isremember"]', form).is(':checked') ? 1 : 0,
		};
		jxAction('login', data);

		return false;
	});
});
</script>
<?php

require dirname(__FILE__).'/footer.php';
