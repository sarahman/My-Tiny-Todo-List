<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Account Activation</title>
    </head>
    <body>
        <p>Dear New <?php echo $this->siteTitle;?> Member,</p>

        <p>You have chosen <?php print $email; ?> as the email address for your new <?php echo $this->siteTitle;?> account.</p>

        <p> Please verify this email address by clicking the link below:</p>

        <p><?php echo $this->url->activate(array("code"=>$activation)); ?></p>

        <p>Thank you for registering with <?php echo $this->siteTitle;?></p>
    </body>
</html>

