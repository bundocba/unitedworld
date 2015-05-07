<?php
defined ( '_JEXEC' ) or die ();



$path = $this->baseurl . '/templates/' . $this->template;
require_once('mobile_detect.php');


JHtml::_ ( 'behavior.modal', 'a.modal' );



$component = JRequest::getVar ( 'option' );



$slider = $this->countModules ( 'slider' );

$left = $this->countModules ( 'left' );

$itemid = JRequest::getInt ( 'Itemid', '1' );
$detect = new Mobile_Detect;
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD fwxhtml 1.0 Transitional//EN" "http://www.w3.org/TR/fwxhtml1/DTD/fwxhtml1-transitional.dtd">



<html xmlns="http://www.w3.org/1999/fwxhtml">



<head>





<!--<link href="/templates/izucosmetic/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />-->



<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />



<jdoc:include type="head" />



<link rel="stylesheet" href="<?php echo $path ?>/css/fontface.css"

  type="text/css">



<link rel="stylesheet" href="<?php echo $path ?>/css/custom.css"

  type="text/css">



<link rel="stylesheet" href="<?php echo $path ?>/css/reset.css"

  type="text/css">

<link rel="stylesheet" href="<?php echo $path ?>/css/message.css"

  type="text/css">

<link rel="stylesheet" href="<?php echo $path ?>/css/k2custom.css"

  type="text/css">

<link rel="stylesheet" href="<?php echo $path ?>/css/menu.css"

  type="text/css">



<!--[if IE 7]>



  <link rel="stylesheet" href="<?php echo $path ?>/css/ie7.css" type="text/css">



<![endif]-->







<!--[if IE 8]>



  <link rel="stylesheet" href="<?php echo $path ?>/css/ie8.css" type="text/css">



<![endif]-->

<!--[if IE 9]>



  <link rel="stylesheet" href="<?php echo $path ?>/css/ie9.css" type="text/css">



<![endif]-->





<script type="text/javascript" src="<?php echo $path ?>/js/custom.js"></script>



<script type="text/javascript">



      window.addEvent('domready', function() {



        SqueezeBox.initialize({});



        SqueezeBox.assign($$('a.pg-modal-button'), {



          parse: 'rel'



        });



      });  



      jQuery(document).ready(function() {

        jQuery("ul").each(function(){ 

          jQuery(this).find("li:first").addClass("first");  

          jQuery(this).find("li:last").addClass("last");  

        });

        /* jQuery('li.item-256 ').hover(function(){

          jQuery(this).parent().css("display","block");

        }); */

          

      });

      

</script>

<?php if($detect->isMobile()){ ?>
  <style>
    #mainmenu > ul > li > a{
      font-size:14px !important;
    }
     </style>
<?php }?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-54478144-1', 'auto');
  ga('send', 'pageview');

</script>

</head>





<body>

  <div id="wrapper-outer">

    <div id="wrapper-out">

      <div id="top">

        <div class="center">

          <div id="logo">

            <jdoc:include type="modules" name="logo" />

          </div>

          <div id="right">

            <div class="clr10"></div>

            <div id="sologan">

              <jdoc:include type="modules" name="sologan" />

            </div>

            <div id="search">

              <jdoc:include type="modules" name="search" />

            </div>

            <div class="clr"></div>

            <div id="mainmenu">

              <jdoc:include type="modules" name="mainmenu" />

            </div>

          </div>

        </div>  

      </div>

      <div class="clr"></div>

      <div id="slider">

        <jdoc:include type="modules" name="slider" />

      </div>

      <div id="header_text">

        <div class="center">

        <!--<div class="clr50"></div>-->

          <jdoc:include type="modules" name="header_text" />

        </div>

      </div>

      <div class="clr"></div>

      <div id="component_area">

        <div class="center">

          <jdoc:include type="message" />

                  <jdoc:include type="component" />

           </div>

      </div>

      <div class="clr"></div>

      <?php if($this->countModules ( 'utility' )){?>

      <div id="utility">

        <div class="clr30"></div>

        <div class="center">

          <jdoc:include type="modules" name="utility" style="xhtml" />

        </div>

        <div class="clr30"></div>

      </div>

      <div class="clr"></div>

      <?php }?>

      <div id="bottom">

        <div class="center">

          <div class="clr20"></div>

          <div id="left">

            <jdoc:include type="modules" name="bottom" />  

          </div>

          <div id="right">

            <jdoc:include type="modules" name="social" />

          </div>

        </div>  

      </div>

      <div class="clr"></div>

    </div>

  </div>



</body>



</html>