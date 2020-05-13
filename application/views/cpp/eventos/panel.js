
cppTab_1 = new Ext.Panel({
    id:"cppTab_1",
    title:'Consecuencias',
//  disabled:!TOP.spa_2daEv,
    disabled:false,
//  autoLoad: {url: CARPETA+'/historial_acciones', params: {id:ID_TAREA}, method: 'POST', scripts: true},
    items:[consecuenciasGridPanel]
});
cppTab_2 = new Ext.Panel({
    id:"cppTab_2",
    title:'Investigadores',
//  iconCls: 'home-icon',
    disabled:false,
    items:[investigadoresGridPanel]
});
cppTab_3 = new Ext.Panel({
    id:"cppTab_3",
    title:'Solución',
//  iconCls: 'home-icon',
    disabled:false,
    items:[cppSolucionPanel] 
});
cppTab_4 = new Ext.Panel({
    id:"cppTab_4",
    title:'Auditoría',
//  iconCls: 'home-icon',
    disabled:false,
    items:[auditoriaGridPanel]
});

cppTabs_panel=new Ext.TabPanel({
    id:"cppTabs_panel",
    border: false,
    activeTab: 0,
    region: 'south',
    height: 200,
    enableTabScroll:true,
    collapsible: true,
    collapsed:true,
    items:[cppTab_1,cppTab_2,cppTab_3,cppTab_4]
});



cppEventosPanel = new Ext.Panel({
    id: 'cppEventosPanel',
    layout:'border',		
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [cppTabs_panel,cppEventosPanel],
    renderTo: 'cpp_eventos'

}); 

altura=Ext.getBody().getSize().height - 60;
cppEventosPanel.setHeight(altura);
Ext.getCmp('browser').on('resize',function(comp){
    cppEventosPanel.setWidth(this.getSize().width);
    cppEventosPanel.setHeight(Ext.getBody().getSize().height - 60);
    
    consecuenciasGridPanel.setWidth(this.getSize().width);
    consecuenciasGridPanel.setHeight(ALT_INF);
    
    investigadoresGridPanel.setWidth(this.getSize().width);
    investigadoresGridPanel.setHeight(ALT_INF);
    
    auditoriaGridPanel.setWidth(this.getSize().width);
    auditoriaGridPanel.setHeight( ALT_INF);
    
    cppCausasGridPanel.setWidth((this.getSize().width)/2);
    cppTareasGridPanel.setHeight(ALT_INF);
    
    cppCausasGridPanel.setWidth((this.getSize().width)/2);
    cppCausasGridPanel.setHeight(ALT_INF);
});



cppTabs_panel.on('tabchange',function(panel,tab){
    var grid=Ext.getCmp('cppEventosGridPanel');
    var sm=grid.getSelectionModel();
    var title = "";
    var title2 = "";
    if(sm.selections.items.length == 1)
    {
        var row=sm.getSelected();   
        var id_evento=row.data.id;
        switch (tab.id)
        {
            case 'cppTab_1':
                title='Listado de consecuencias del evento Nro:<div style="color:blue;display:inline;"> '+id_evento+'</div>';
                controlExecTab(consecuenciasGridPanel,id_evento,title);
                break;
            case 'cppTab_2': 
                title='Listado de investigadores para el evento Nro:<div style="color:blue;display:inline;"> '+id_evento+'</div>';
                controlExecTab(investigadoresGridPanel,id_evento,title);
                break;
            case 'cppTab_3': 
                title='Listado de causas para el evento Nro:<div style="color:blue;display:inline;"> '+id_evento+'</div>';
                controlExecTab(cppCausasGridPanel,id_evento,title);
                title2='Listado de Tareas - medidas correctivas - para el evento Nro:<div style="color:blue;display:inline;"> '+id_evento+'</div>';
                controlExecTab(cppTareasGridPanel,id_evento,title2);
                break;
            case 'cppTab_4': 
                title='Listado de acciones para el evento Nro:<div style="color:blue;display:inline;"> '+id_evento+'</div>';
                controlExecTab(auditoriaGridPanel,id_evento,title);
                break;
                
        }
    }
    else
        cppTabs_panel.expand(false);
});
function controlExecTab(panel,id_evento,title){
    var ds = panel.getStore();
    panel.setTitle(title);
    if(typeof ds.baseParams.evento==="undefined")
    {
        ds.setBaseParam('evento',id_evento); 
        ds.load();
    }
    else
    {
        if(ds.baseParams.evento!=id_evento)
        {
            ds.setBaseParam('evento',id_evento); 
            ds.load();

        }

    }
        
};
cppEventosGridPanel.on('cellclick',function(grid, rowIndex, columnIndex){
    
    if(columnIndex<(grid.colModel.config.length)-1)
    {
        consecuenciasDataStore.removeAll();
        investigadoresDataStore.removeAll();
        cppCausasDataStore.removeAll();
        cppTareasDataStore.removeAll();
        auditoriaDataStore.removeAll();
        
        var tabPanel=Ext.getCmp('cppTabs_panel');
        var tab=tabPanel.getActiveTab();
        var store=grid.getStore();   
        var id_evento=store.data.items[rowIndex].data.id;
        BTN_VERIFICAR_TAREAS=store.reader.jsonData.rows[rowIndex].btn_verificar_tareas;
//        console.log("btn="+BTN_VERIFICAR_TAREAS);
        ID_EVENTO_SELECT=id_evento;
        
        cppTabs_panel.expand();
        switch (tab.id)
        {
            case 'cppTab_1': 
            default:
                consecuenciasDataStore.setBaseParam('evento',id_evento); consecuenciasDataStore.load();
                consecuenciasGridPanel.setTitle('Listado de consecuencias del evento Nro:<div style="color:blue;display:inline;"> '+id_evento+'</div>');
                tabPanel.setActiveTab(0);
//                cppTabs_panel.show();
            break;
            case 'cppTab_2': 
                investigadoresDataStore.setBaseParam('evento',id_evento); investigadoresDataStore.load();
                investigadoresGridPanel.setTitle('Listado de investigadores para el evento Nro:<div style="color:blue;display:inline;"> '+id_evento+'</div>');
            break;
            case 'cppTab_3': 
                cppCausasGridPanel.setTitle('Listado de causas para el evento Nro:<div style="color:blue;display:inline;"> '+id_evento+'</div>');
                cppTareasGridPanel.setTitle('Listado de Tareas - medidas correctivas - para el evento Nro:<div style="color:blue;display:inline;"> '+id_evento+'</div>');
                cppCausasDataStore.setBaseParam('evento',id_evento); cppCausasDataStore.load();
                cppTareasDataStore.setBaseParam('evento',id_evento); cppTareasDataStore.load();
            break;
            case 'cppTab_4': 
                auditoriaDataStore.setBaseParam('evento',id_evento); auditoriaDataStore.load();
                auditoriaGridPanel.setTitle('Listado de acciones para el evento Nro:<div style="color:blue;display:inline;"> '+id_evento+'</div>');
            break;
        }
    }
});