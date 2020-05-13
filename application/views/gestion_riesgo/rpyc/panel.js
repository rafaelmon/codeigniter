
rpycGridPanel.on('cellclick',function(grid, rowIndex, columnIndex){
    var q_tareas=grid.store.data.items[rowIndex].data.tareas;
    if(columnIndex<(grid.colModel.config.length)-1 && q_tareas>0)
    {
        var id_rpyc=grid.store.data.items[rowIndex].data.id_rpyc;
        rpycTareasDataStore.load({params: {id:id_rpyc,start: 0}});
        rpycTareasPanel.setTitle('Listado de Tareas para RPyC Nro: '+id_rpyc);
        rpycTareasPanel.show();
        rpycTareasPanel.expand(false);
//        return iDTramite;
    }
    else
    {
        rpycTareasPanel.collapse(true);
        rpycTareasPanel.setTitle('RPyC Sin Tareas Asignadas');
        rpycTareasDataStore.removeAll(true);
        rpycTareasListingGridPanel.view.refresh();
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
        rpycPanel
        ,rpycTareasPanel
    ],
    renderTo: 'grillita'

}); 

altura=Ext.getBody().getSize().height - 60;
tareasGrlPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    tareasGrlPanel.setWidth(this.getSize().width);
    tareasGrlPanel.setHeight(Ext.getBody().getSize().height - 60);
});
