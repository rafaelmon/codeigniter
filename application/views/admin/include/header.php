<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<head>
		<title><?=htmlentities(NOMSIS,ENT_QUOTES,"UTF-8")?></title>
<!--		<meta http-equiv="refresh" content="40">-->
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="Last-Modified" content="0">
		<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
		<meta http-equiv="Pragma" content="no-cache"> 
		<link rel="shortcut icon" href="<?=URL_BASE?>images/oc.png" type="image/x-icon" />
		<link rel="STYLESHEET" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/resources/css/ext-all.css'?>"></link>
                <link rel="STYLESHEET" type="text/css" href="<?=URL_BASE.'js/ext-3.3.0/resources/css/xtheme-blue.css'?>"></link>
		<script type="text/javascript">
		var URL_ACTUAL = "<?=site_url("")?>";
		</script>
		<script type="text/javascript" src="<?=URL_BASE?>js/ext-3.3.0/adapter/ext/ext-base.js"></script>
		<script type="text/javascript" src="<?=URL_BASE?>js/ext-3.3.0/ext-all.js"></script>
                <script type="text/javascript" src="<?=URL_BASE?>js/ext-3.3.0/src/locale/ext-lang-es.js"></script>
		<script type="text/javascript">
			Ext.BLANK_IMAGE_URL='<?=URL_BASE.'js/ext-3.3.0/resources/images/default/s.gif'?>';
		</script>
                <script type="text/javascript">
                    <? 
                        $content="var URL_BASE='".URL_BASE."';";
                        $content.="var URL_BASE_SITIO='".URL_BASE_SITIO."';";
                        echo  $content;
                    ?>
                </script>
		<script type="text/javascript" src="<?=URL_BASE?>js/main.js"></script>
                <script src="<?=URL_BASE.'js/ext-3.3.0/tiny_mce/tiny_mce_src.js?v=2'?>" type="text/javascript"></script>
		<link rel="STYLESHEET" type="text/css" href="<?=URL_BASE.'css/style_ext.css'?>"></link>
	</head>
	<body>