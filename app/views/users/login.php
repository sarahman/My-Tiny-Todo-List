<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>M School</title>
        <link href="<?php echo site_url('assets/css/login.css') ?>" rel="stylesheet" type="text/css" />
    </head>

    <body>

        <form id="login-form" action="<?php echo site_url('users/login') ?>" method="post">

            <fieldset>

                <legend>Log in</legend>
                <div class="err">
                    <?php echo !empty($isError) ? 'Username or password is incorrect' : '' ?>
                </div>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" />
                <div class="clear"></div>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" />
                <div class="clear"></div>

                <label for="remember_me" style="padding: 0;">Remember me?</label>
                <input type="checkbox" id="remember_me" style="margin-top: 3px;" name="remember_me"/>
                <div class="clear"></div>

                <input type="submit" style="margin: 10px 0 0 287px;" class="button" name="commit" value="Log in"/>

            </fieldset>

        </form>

    </body>

</html>