
fotalezasPanel = new Ext.Panel(
{
    id: 'fotalezasPanel',
    title: 'Fortalezas',
    columnWidth:0.5,
//    region:'west',
    collapsible: false,
    split: false,
    header: true,
    region:'center',
//    height: 300,
//    minSize: 100,
//    maxSize: 350,	
    margins: '0 5 5 5',
    layout: 'form',
    region: 'west',
    width: ANCHO,
//    layout: 'column',
    items:[fortalezasForm,fortalezasGridPanel]
});
aamPanel = new Ext.Panel(
{
    id: 'aamPanel',
    title: 'Aspectos a mejorar',
    columnWidth:0.5,
//    region:'north',
    collapsible: false,
    split: false,
    header: true,
    layout: 'form',
    region:'center',
    width: ANCHO,
//    height: 300,
//    minSize: 100,
//    maxSize: 350,	
    margins: '0 5 5 5',
//    layout: 'column',
    items:[aamForm,aamGridPanel]
});
fotalezasAamPanel = new Ext.Panel(
{
        id: 'relaciona-panel',
//        collapsible: false,
//        split: false,
        header: false,
        layout:'border',
//        layout:'column',
//        region:'center',
//        height: 300,
//        minSize: 100,
//        maxSize: 350,	
        margins: '0 5 5 5',
//        layout:'border',
        renderTo: 'grillita_fortalezas_aam',
        items:[fotalezasPanel,aamPanel]
});

altura=Ext.getBody().getSize().height - 60;
altura2=altura-260;
fotalezasAamPanel.setHeight(altura);
fotalezasPanel.setHeight(altura);
aamPanel.setHeight(altura);
fortalezasGridPanel.setHeight(altura2);
aamGridPanel.setHeight(altura2);
	
Ext.getCmp('browser').on('resize',function(comp){
    alto=Ext.getBody().getSize().height - 60;
    alto2=alto-260;
    ancho=this.getSize().width-5;
    fotalezasAamPanel.setWidth(ancho);
    fotalezasAamPanel.setHeight(alto);
    fortalezasGridPanel.setHeight(alto2);
    aamGridPanel.setHeight(alto2);
});
