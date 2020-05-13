<?php

?>
<script type="text/javascript">
	var CARPETA = "<?= site_url("admin/usuarios")?>";
	var URL = "<?= site_url("admin")?>";
	var USER_ID = <?= $iidd?>;
	Ext.onReady(function(){
	
		Ext.QuickTips.init();
		
		<? include_once("miPerfil.js") ?>
		
		var principal = new Ext.Panel({
			id: 'principal',		
			layout: 'fit',		
			renderTo: 'grillita',			
			autoScroll : false,
			items : [PassCreateForm]
		});
	
		var altura=Ext.getBody().getSize().height - 260;
		var ancho=Ext.getBody().getSize().width - 260;
		
                principal.setHeight(360);
                principal.setWidth(400);
		
//		Ext.getCmp('browser').on('resize',function(comp){
//			
//			principal.setWidth(Ext.getCmp('browser').getSize().width);
//			
//			principal.setHeight(Ext.getBody().getSize().height - 60);
//	
//		});
	
	})
</script>
<div id="grillita"></div>