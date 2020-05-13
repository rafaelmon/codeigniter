
obsoletosGrid.on('cellclick',function(grid, rowIndex, columnIndex){
    var id_estado=grid.store.data.items[rowIndex].data.id_estado;
//    if(columnIndex<(grid.colModel.config.length)-2 && id_estado!=1)
    if(columnIndex==6 && id_estado!=1)
    {
        var id_documento=grid.store.data.items[rowIndex].data.id_documento;
        var codigo=grid.store.data.items[rowIndex].data.codigo;
        gestionesDocsObsoletosDataStore.load({params: {id:id_documento,start: 0}});
        obsoletosInferiorPanel.setTitle('Historial del Documento Código:'+codigo);
        obsoletosInferiorPanel.show();
        obsoletosInferiorPanel.expand(false);
//        return iDTramite;
    }
    else
    {
        obsoletosInferiorPanel.collapse(true);
        obsoletosInferiorPanel.setTitle('Historial del Documento Código:...');
        gestionesDocsObsoletosDataStore.removeAll();

    }
        
});

obsoletosPanel = new Ext.Panel({
    id: 'relaciona-panel',
    layout:'border',		
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [obsoletosGrid,obsoletosInferiorPanel],
    renderTo: 'grillita'

}); 

altura=Ext.getBody().getSize().height - 60;
obsoletosPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    obsoletosPanel.setWidth(this.getSize().width);
    obsoletosPanel.setHeight(Ext.getBody().getSize().height - 60);
});
