
omcGridPanel.on('cellclick',function(grid, rowIndex, columnIndex){
    var q_tareas=grid.store.data.items[rowIndex].data.tareas;
//    if(columnIndex<(grid.colModel.config.length)-1 && q_tareas>0)
    if(columnIndex==7 && q_tareas>0)
    {
        var id_omc=grid.store.data.items[rowIndex].data.id_omc;
        omcTareasDataStore.load({params: {id:id_omc,start: 0}});
        omcTareasPanel.setTitle('Listado de Tareas para OMC Nro: '+id_omc);
        omcTareasPanel.show();
        omcTareasPanel.expand(false);
//        return iDTramite;
    }
    else
    {
        omcTareasPanel.collapse(true);
        omcTareasPanel.setTitle('OMC Sin Tareas Asignadas');
        omcTareasDataStore.removeAll(true);
        omcTareasListingGridPanel.view.refresh();
    }
        
});

tareasGrlPanel = new Ext.Panel({
    id: 'relaciona-panel',
    layout:'border',		
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [
        omcPanel
        ,omcTareasPanel
    ],
    renderTo: 'grillita'

}); 

altura=Ext.getBody().getSize().height - 60;
tareasGrlPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    tareasGrlPanel.setWidth(this.getSize().width);
    tareasGrlPanel.setHeight(Ext.getBody().getSize().height - 60);
});
