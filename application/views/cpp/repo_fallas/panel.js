


cppRepoFallasPanel = new Ext.Panel({
    id: 'cppRepoFallasPanel',
    layout:'border',
    title: 'Generador Reporte de Falllas',
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [cppRepoFallasFiltrosPanel,cppRepoFallasGridPanel],
    renderTo: 'cpp_repo_fallas'

}); 

altura=Ext.getBody().getSize().height - 60;
cppRepoFallasPanel.setHeight(altura);
Ext.getCmp('browser').on('resize',function(comp){
    cppRepoFallasPanel.setWidth(this.getSize().width);
    cppRepoFallasPanel.setHeight(Ext.getBody().getSize().height - 60);
    
});