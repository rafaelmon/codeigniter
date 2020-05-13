
auditoriaGridPanel.on('cellclick',function(grid, rowIndex, columnIndex){
    var q_hallazgos=grid.store.data.items[rowIndex].data.q_hallazgos;
    if(columnIndex<(grid.colModel.config.length)-1 && q_hallazgos>0)
    {
        var id_auditoria=grid.store.data.items[rowIndex].data.id_auditoria;
        auditoriasHallazgosDataStore.load({params: {id:id_auditoria,start: 0}});
        colAuditoriasHallazgos.setTitle('listado de hallazgos de auditoria nro: '+id_auditoria);
        colAuditoriasHallazgos.show();
        colAuditoriasHallazgos.expand(false);
    }
    else
    {
//        colAuditoriasHallazgos.collapse(true);
        colAuditoriasHallazgos.setTitle('Listado de hallazgos de auditoria nro:... (seleccione auditoria)');
        auditoriasHallazgosDataStore.removeAll(true);
        auditoriasHallazgosListingGridPanel.view.refresh();
        inferiorAuditoriaPanel.collapse(true);
        inferiorAuditoriaPanel.setTitle('Listado de Tareas... (seleccionar hallazgo)');
        hallazgosAuditoriasTareasDataStore.removeAll(true);
        hallazgosAuditoriasTareasGridPanel.view.refresh();
    }
        
});
auditoriasHallazgosListingGridPanel.on('cellclick',function(grid, rowIndex, columnIndex){
    var q_tareas=grid.store.data.items[rowIndex].data.q_tareas;
//    var q_tareas=1;
    if(columnIndex<(grid.colModel.config.length)-1 && q_tareas>0)
    {
        var id_hallazgo=grid.store.data.items[rowIndex].data.id_hallazgo;
        hallazgosAuditoriasTareasDataStore.load({params: {id:id_hallazgo,start: 0}});
        inferiorAuditoriaPanel.setTitle('Listado de Tareas para el hallazgo Nro: '+id_hallazgo);
        inferiorAuditoriaPanel.show();
        inferiorAuditoriaPanel.expand(true);
    }
    else
    {
        inferiorAuditoriaPanel.collapse(true);
        inferiorAuditoriaPanel.setTitle('Listado de Tareas... (seleccionar hallazgo)');
        hallazgosAuditoriasTareasDataStore.removeAll(true);
        hallazgosAuditoriasTareasGridPanel.view.refresh();
    }
        
});

auditoriasColsPanel = new Ext.Panel(
{
        id:'auditoriasColsPanel',
//        collapsible: false,
//        collapsed:false,
        split: true,
//        title: ,
//        region: 'north',
//        height: 300,
//        minSize: 100,
//        maxSize: 350,
//        margins: '0 5 5 5',
        layout: 'column',
        items : [colAuditorias,colAuditoriasHallazgos]
});

        var altura=Ext.getBody().getSize().height - 100;
	colAuditorias.setHeight(altura);
	colAuditoriasHallazgos.setHeight(altura);
	Ext.getCmp('browser').on('resize',function(comp){
            colAuditorias.setWidth(this.getSize().width);
            colAuditoriasHallazgos.setWidth(this.getSize().width);
            colAuditorias.setHeight(Ext.getBody().getSize().height - 100);
            colAuditoriasHallazgos.setHeight(Ext.getBody().getSize().height - 100);

	});
        
       var alturaGrillaSup=Ext.getBody().getSize().height - 100;
	auditoriasColsPanel.setHeight(alturaGrillaSup);
	Ext.getCmp('browser').on('resize',function(comp){
            auditoriasColsPanel.setWidth(this.getSize().width);
            auditoriasColsPanel.setHeight(Ext.getBody().getSize().height - 100);

	});




superiorAuditoriaPanel = new Ext.Panel(
{
        collapsible: false,
        collapsed:false,
        split: true,
//        title: 'Listado de Auditorias',
        region: 'center',
//        height: 300,
        minSize: 100,
        maxSize: 350,
        margins: '0 5 5 5',
        html:'<p>panel superior</p>',
        layout: 'fit',
        items : [auditoriasColsPanel]
});

//inferiorAuditoriaPanel = new Ext.Panel(
//{
//        collapsible: false,
//        collapsed:true,
//        split: true,
//        title: 'Listado de Tareas',
//        region: 'south',
//        height: 200,
//        minSize: 100,
//        maxSize: 300,
//        margins: '0 5 5 5',
//        html:'<p>panel inferior</p>',
//        layout: 'fit',
//        items : [hallazgosAuditoriasTareasPanel]
//});
inferiorAuditoriaPanel = new Ext.Panel(
{
        collapsible: true,
        collapsed:true,
        split: false,
        header: true,
        title: 'Listado de Tareas',
        region:'south',
        height: 300,
        minSize: 100,
        maxSize: 350,	
//        margins: '0 5 5 5',
//        html:'<p>panel inferior</p>',
        layout: 'fit',
        items:[hallazgosAuditoriasTareasGridPanel]
});
auditoriasGrlPanel = new Ext.Panel({
    id: 'relaciona-panel',
    layout:'border',		
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [
        superiorAuditoriaPanel
        ,inferiorAuditoriaPanel
    ],
    renderTo: 'grillita'

}); 

altura=Ext.getBody().getSize().height - 60;
auditoriasGrlPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    auditoriasGrlPanel.setWidth(this.getSize().width);
    auditoriasGrlPanel.setHeight(Ext.getBody().getSize().height - 60);
});
