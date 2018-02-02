<?php
$hasanalytics = (empty($PAGE->theme->settings->useanalytics)) ? false : $PAGE->theme->settings->useanalytics;
echo $OUTPUT->doctype();
?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Moodle Theme by 3rd Wave Media | elearning.3rdwavemedia.com" />
    <?php require_once(dirname(__FILE__).'/includes/iosicons.php'); ?>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <link href='//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700' rel='stylesheet' type='text/css'>
    <?php require_once(dirname(__FILE__).'/includes/glyphicons.php'); ?>    
    <?php echo $OUTPUT->standard_head_html() ?>
    <!-- Respond.js IE8 support of media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <?php if ($hasanalytics) { ?>
    <!-- Start Google Analytics -->
    <?php require_once(dirname(__FILE__).'/includes/analytics.php'); ?>
    <!-- End Google Analytics -->
    <?php } ?>  
    
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>
<?php echo $OUTPUT->standard_top_of_body_html() ?>
<div class="wrapper">

    <?php require_once(dirname(__FILE__).'/includes/header.php'); ?>
    
     <?php //require_once(dirname(__FILE__).'/includes/nav.php'); ?>    
    
    <div id="page" class="container-fluid">
        <div id="page-content" class="row login">
            <section id="region-main" class="col-md-12">
                <div class="region-main-inner">
                	<!-- <div id="page-navbar" class="clearfix">
                		<nav class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></nav>
                    	<div class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></div>
                	</div>     -->            	
                    <?php
                    echo $OUTPUT->course_content_header();
                    echo $OUTPUT->main_content();
                    echo $OUTPUT->course_content_footer();
                    ?>
                </div><!--//region-main-inner-->
            </section>
        </div><!---//page-content-->    	
    </div><!--//#page-->
    <!--custom page build-->
    

</div><!--wrapper-->

<footer id="page-footer" class="footer">
	<?php require_once(dirname(__FILE__).'/includes/footer.php'); ?>
</footer>

<?php echo $OUTPUT->standard_end_of_body_html() ?>

</body>
</html>
