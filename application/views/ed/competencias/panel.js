competenciasListingEditorGrid.on('cellclick',function(grid, rowIndex, columnIndex){
    var id_competencia=grid.store.data.items[rowIndex].data.id_competencia;
    var competencia=grid.store.data.items[rowIndex].data.competencia;
    IDCOMPETENCIA = id_competencia;
    subcompetenciasDataStore.load({params: {id:id_competencia,start: 0}});
    subcompetenciasPanel.setTitle('Subcompetencias de competencia: '+competencia);
});

combinacionPanel = new Ext.Panel(
{
        collapsible: false,
        split: false,
        header: true,
        region:'center',
        height: 300,
        minSize: 100,
        maxSize: 350,	
        margins: '0 5 5 5',
        layout: 'column',
        items:[competenciasPanel,subcompetenciasPanel]
});


pantallaPanel = new Ext.Panel({
    id: 'relaciona-panel',
    layout:'border',		
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
    items: [combinacionPanel],
    renderTo: 'grillita'
}); 

altura=Ext.getBody().getSize().height - 60;
pantallaPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    pantallaPanel.setWidth(this.getSize().width);
    pantallaPanel.setHeight(Ext.getBody().getSize().height - 60);
});
