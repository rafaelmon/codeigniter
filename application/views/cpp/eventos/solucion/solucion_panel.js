
cppCausasGridPanel.on('cellclick',function(grid, rowIndex, columnIndex){

        
});

cppSolucionPanel = new Ext.Panel({
    id: 'relaciona-panel',
    layout:'border',		
//    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [
        cppCausasGridPanel
        ,cppTareasGridPanel
    ]

}); 
cppCausasGridPanel.setWidth(600);
cppTareasGridPanel.setWidth(ANCH-600);

cppSolucionPanel.setHeight(ALT_INF);
Ext.getCmp('browser').on('resize',function(comp){
    cppSolucionPanel.setWidth(this.getSize().width);
    cppSolucionPanel.setHeight(ALT_INF);
});
