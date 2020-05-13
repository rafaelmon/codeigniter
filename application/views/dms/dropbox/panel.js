dropBoxPanel = new Ext.Panel({
    id: 'relaciona-panel',
    layout:'border',		
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [arbolDropBoxPanel,contenidoDropBoxPanel],
    renderTo: 'grillita'

}); 

altura=Ext.getBody().getSize().height - 60;
dropBoxPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    dropBoxPanel.setWidth(this.getSize().width);
    dropBoxPanel.setHeight(Ext.getBody().getSize().height - 60);
});
