<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Forgotten Password Request</title>
    </head>
    <body>
        <h1>Forgotten Password Request</h1>
        <p> You (<?php echo $identity; ?>) have requested a new password.</p>
        <p>To get a <?php echo anchor($this->url->recoverPass(array('code'=>$forgotten_password_code)), 'New Password'); ?> Password Please following link bellow</p>
        <p><?php echo $this->url->recoverPass(array('code'=>$forgotten_password_code)); ?></p>
    </body>
</html>
