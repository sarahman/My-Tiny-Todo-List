<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="Templates/mUniversity.dwt.php" codeOutsideHTMLIsLocked="false" -->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- InstanceBeginEditable name="doctitle" -->
        <title>M School</title>
        <!-- InstanceEndEditable -->
        <link href="<?php echo site_url('assets/css/style.css') ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo site_url('assets/css/superfish.css') ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo site_url('assets/css/dashboard.css') ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo site_url('assets/css/facebox.css') ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo site_url('assets/css/forms.css') ?>" rel="stylesheet" type="text/css" />

        <script src="<?php echo site_url('assets/js/jquery-1.4.2.min.js') ?>" type="text/javascript"></script>
    </head>

    <body>

        <div id="container">
            <div id="header">
                <div class="logout">
                    Welcome, <?php echo $username ?>. <a href="<?php echo site_url('users/logout') ?>">Logout</a>
                </div>
            </div>

            <?php $this->load->view('layouts/nav') ?>

            <div id="main"><!-- InstanceBeginEditable name="Main" -->

<!--                --><?php //if (!empty($notification['message'])) : ?>
<!---->
<!--                <div class="block message-block">-->
<!---->
<!--                    <div class="message --><?php //echo $notification['messageType'] ?><!--" style="display: block;">-->
<!--                        <p>--><?php //echo $notification['message'] ?><!--</p>-->
<!--                    </div>-->
<!---->
<!--                </div>-->
<!---->
<!--                --><?php //endif ?>

                <?php echo $content_for_layout ?>
            </div><!-- InstanceEndEditable -->

            <div id="footer"> </div>
        </div>

        <script src="<?php echo site_url('assets/js/superfish.js') ?>" type="text/javascript"></script>
        <script src="<?php echo site_url('assets/js/hoverIntent.js') ?>" type="text/javascript"></script>
        <script src="<?php echo site_url('assets/js/facebox.js') ?>" type="text/javascript"></script>
        <script src="<?php echo site_url('assets/js/navform.js') ?> "type="text/javascript"></script>
        <script language="javascript" type="text/javascript">
            (function ($) {
                callFacebox($);
                $('ul.sf-menu').superfish({
                    delay: 0, // one second delay on mouseout
                    animation: {opacity:'show',height:'show'}, // fade-in and slide-down animation
                    speed: 45, // faster animation speed
                    autoArrows: false, // disable generation of arrow mark-up
                    dropShadows: true // disable drop shadows
                });
            })(jQuery);

            function callFacebox($) {
                $('a[rel*=facebox]').facebox({
                    loadingImage:'assets/images/loading.gif',
                    closeImage:'assets/images/closelabel.png'
                });
            }
        </script>

    </body>

</html>