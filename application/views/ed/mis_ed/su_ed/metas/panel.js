var superiorFormPanel = 
{
	collapsible: false,
	split: false,
	//region: 'south',
	region: 'north',
	height: 200,
	minSize: 200,		
	layout: 'fit',
	items : [metasForm]
};
var inferiorMetasGridPanel = 
{
	collapsible: false,
	split: false,
	region: 'center',
//	height: 150,
	minSize: 100,		
	layout: 'form',
	items : [metasGridPanel]
};

metasPanel = new Ext.Panel(
{
    id: 'metasPanel',
    title: 'Fijación de metas individuales',
    columnWidth:0.5,
//    region:'west',
    collapsible: false,
    split: false,
    header: true,
//    height: 300,
//    minSize: 100,
//    maxSize: 350,	
    margins: '0 5 5 5',
    layout:'border',
    bodyStyle: 'padding:15px',
    width: ANCHO,
    renderTo: 'grillita_metas',
//    layout: 'column',
    items:[superiorFormPanel,inferiorMetasGridPanel]
});

altura=Ext.getBody().getSize().height - 60;
altura2=altura-310;
metasPanel.setHeight(altura);
metasGridPanel.setHeight(altura2);
	
Ext.getCmp('browser').on('resize',function(comp){
    alto=Ext.getBody().getSize().height - 60;
    alto2=alto-310;
    ancho=this.getSize().width-5;
    metasPanel.setWidth(ancho);
    metasPanel.setHeight(alto);
    metasGridPanel.setHeight(alto2);
});
