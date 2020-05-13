
rmcGridPanel.on('cellclick',function(grid, rowIndex, columnIndex){
    var q_tareas=grid.store.data.items[rowIndex].data.tareas;
    var q_archivos=grid.store.data.items[rowIndex].data.archivos;
    var q_tareas=2;
    if(columnIndex<(grid.colModel.config.length)-1 && (q_tareas>0 || q_archivos>0))
    if(columnIndex==13 && q_tareas>0)
    {
        var id_rmc=grid.store.data.items[rowIndex].data.id_rmc;
        rmcTareasDataStore.load({params: {id:id_rmc,start: 0}});
        rmcArchivosDataStore.load({params: {id:id_rmc,start: 0}});
        rmcTareasPanel.setTitle('Listado de Tareas para el Reporte Nro: '+id_rmc);
        rmcArchivosPanel.setTitle('Listado de Archivos para el Reporte Nro: '+id_rmc);
        rmcTabs_panel.show();
        rmcTabs_panel.expand(false);
//        return iDTramite;
    }
    else
    {
        rmcTabs_panel.collapse(true);
        rmcTareasPanel.setTitle('Reporte sin tareas asignadas');
        rmcArchivosPanel.setTitle('Reporte sin tareas asignadas');
        rmcTareasDataStore.removeAll(true);
//        rmcTareasListingGridPanel.view.refresh();
    }
        
});


rmcTab_1 = new Ext.Panel({
    id:"rmcTab_1",
    title:'Archivos',
//  disabled:!TOP.spa_2daEv,
    disabled:false,
//  autoLoad: {url: CARPETA+'/historial_acciones', params: {id:ID_TAREA}, method: 'POST', scripts: true},
    items:[rmcArchivosListingGridPanel]
});
rmcTab_2 = new Ext.Panel({
    id:"rmcTab_2",
    title:'Tareas',
//  iconCls: 'home-icon',
    disabled:false,
    items:[rmcTareasListingGridPanel]
});


rmcTabs_panel=new Ext.TabPanel({
    id:"rmcTabs_panel",
    border: false,
    activeTab: 0,
    region: 'south',
    height: 300,
    enableTabScroll:true,
    collapsible: true,
    collapsed:true,
    items:[rmcTab_1,rmcTab_2]
});


rmcGrlPanel = new Ext.Panel({
    id: 'relaciona-panel',
    layout:'border',		
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [
        rmcPanel
        ,rmcTabs_panel
    ],
    renderTo: 'grillita'

}); 

altura=Ext.getBody().getSize().height - 60;
rmcGrlPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    rmcGrlPanel.setWidth(this.getSize().width);
    rmcGrlPanel.setHeight(Ext.getBody().getSize().height - 60);
});
