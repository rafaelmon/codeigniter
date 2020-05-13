<script type="text/javascript">
<?php 
$cooki=$this->session->userdata(USER_DATA_SESSION);
$usuario=$cooki['nombre']." ".$cooki['apellido'];
$perfil=$cooki['perfil_nomb'];
$content="var Menu = {id: 'menu',region: 'north',layout: 'menu',margins: '29 0 0 0',activeItem: 0,border: false,tbar:[{text: 'Inicio',iconCls: 'home',buttonAlign: 'right',handler: function(){location.assign('".site_url("admin")."');}},'-',";
foreach($menu as $menu2)
{
	$pasa = 0;
	//$content .="{text: '".$menu2['modulo']."'";
	if($menu2['accion']<>"")
	{
		$pasa = 1;
		$content .="{text: '".$menu2['modulo']."',iconCls: '".$menu2['icono']."'";
		$content .=",handler: function(){cargar('browser','".site_url($menu2['accion']."/".$menu2['id_modulo'])."');}";
	}
	elseif(count($menu2['submenu']) > 0)
	{
		$pasa = 1;
		$content .="{text: '".$menu2['modulo']."'";
		$content .=",menu:[";
		$content2='';
		foreach($menu2['submenu'] as $menu3)
		{
			$content2 .="{text: '".$menu3['modulo']."',iconCls:  '";
			if($menu3["icono"]<>"")
			{ 
				$content2 .=$menu3['icono'];
			}
			else
			{ 
				$content2 .="options_ico";
			}
			$content2 .="',handler: function(){
	           				cargar('browser','".site_url($menu3['accion']."/".$menu3['id_modulo'])."');}},";	
		}
		$content .=substr($content2,0,strlen($content2)-1);
		$content .="]";
	}
	if ($pasa == 1)
		$content .="},'-',";
	//echo "<pre>".print_r($menu2,true)."</pre>";
}
$content .= "{text: 'Mi Perfil',iconCls: 'permisos',buttonAlign: 'right',handler: function(){cargar('browser','".site_url("admin/usuarios/mi_perfil")."');}}";
$content .= ",'-',";
$content .="{text: 'Salir',iconCls: 'remove',buttonAlign: 'right',handler: function(){location.assign('".site_url("admin/admin/salir")."');}}";
$content .= ",'->',";
$content .= "{text: '".$usuario."',iconCls: 'user_loguin_ico',buttonAlign: 'right',handler: function(){cargar('browser','".site_url("admin/usuarios/mi_perfil")."');}}";
$content .="]};";
echo $content;

?>
Ext.onReady(function()
{
	Ext.QuickTips.init();
});
</script>