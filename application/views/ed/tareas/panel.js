
historialTab1 = new Ext.Panel({
            title:'Historial de acciones',
//            disabled:!TOP.spa_2daEv,
            disabled:false,
//            autoLoad: {url: CARPETA+'/historial_acciones', params: {id:ID_TAREA}, method: 'POST', scripts: true},
//            html: '',
            items:[tareasHistorialAccionesPanel]
});
historialTab2 = new Ext.Panel({
            title:'Historial de cambios',
//            iconCls: 'home-icon',
            disabled:false,
//            html: '',
            items:[tareasHistorialCambiosPanel]
});

historialTabPanel=new Ext.TabPanel({
    border: false,
    activeTab: 0,
    region: 'south',
    enableTabScroll:true,
    items:[historialTab1,historialTab2]
});
tareasHistorialesPanel = new Ext.Panel(
{
        collapsible: true,
        collapsed:true,
        split: false,
//        title: 'Historial',
        region: 'south',
        height: 300,
        minSize: 100,
        maxSize: 350,
        margins: '0 5 5 5',
        html:'<p>panel inferior</p>',
//        layout: 'fit',
        items : [historialTabPanel]
});


tareasGrlPanel = new Ext.Panel({
    id: 'relaciona-panel',
    layout:'border',		
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [tareasSuperiorPanel,tareasHistorialesPanel],//,],
    renderTo: 'grillita'

}); 

altura=Ext.getBody().getSize().height - 60;
tareasGrlPanel.setHeight(altura);
tareasHistorialesPanel.setHeight(300);	
Ext.getCmp('browser').on('resize',function(comp){
    tareasGrlPanel.setWidth(this.getSize().width);
    tareasGrlPanel.setHeight(Ext.getBody().getSize().height - 60);
});
