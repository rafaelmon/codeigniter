
//bcGridPanel.on('cellclick',function(grid, rowIndex, columnIndex){
//    var estado=grid.store.data.items[rowIndex].data.id_estado;
//    var id=grid.store.data.items[rowIndex].data.id_bc;
//    if(columnIndex<(grid.colModel.config.length)-1 && estado>1)
//    {
//        var id_bc=grid.store.data.items[rowIndex].data.id_bc;
//        arbolPanel.setTitle('Detalle del proceso BC Nro'+id);
////        bcTree.destroy(); 
//        if (Ext.getCmp('bcTree'))
//            Ext.getCmp('bcTree').destroy();
//        create(id);
//        
//        var arbol=Ext.getCmp('bcTree');
//        Ext.getCmp('arbolBcPanel').add(arbol);
//        Ext.getCmp('bcTree').getRootNode().reload();
//        arbolPanel.doLayout();
//        
//    }
//    else
//    {
//        arbolPanel.setTitle('');
////        console.log('Detalle del proceso');
//    }
//        
//});
bcGrlPanel = new Ext.Panel({
    id: 'relaciona-panel',
    layout:'border',		
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [
        bcPanel
//        ,arbolPanel
        
    ],
    renderTo: 'grillita'

}); 

altura=Ext.getBody().getSize().height - 60;
bcGrlPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    bcGrlPanel.setWidth(this.getSize().width);
    bcGrlPanel.setHeight(Ext.getBody().getSize().height - 60);
});
