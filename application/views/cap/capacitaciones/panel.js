// JavaScript Document
capPanelDerecha = new Ext.Panel(
{
    collapsible: false,
    split: false,
    heanchorader: true,
    height: 300,
//    margins: '0 5 5 5',
    columnWidth:.5,
    layout: 'anchor',
    items:[capTareasGridPanel,capParticipantesGridPanel]
});


capCapacitacionesPanel = new Ext.Panel(
{
    collapsible: false,
    split: false,
    header: true,
    height: 300,
    margins: '0 5 5 5',
    layout: 'column',
    renderTo: 'capacitaciones',
    items:[capCapacitacionesListingEditorGridPanel,capPanelDerecha]
});

altura=Ext.getBody().getSize().height - 60;
capCapacitacionesPanel.setHeight(altura);
capPanelDerecha.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    capCapacitacionesPanel.setWidth(this.getSize().width);
    capCapacitacionesPanel.setHeight(Ext.getBody().getSize().height - 60);
    capPanelDerecha.setHeight(Ext.getBody().getSize().height - 60);
    
});

capCapacitacionesListingEditorGridPanel.on('cellclick',function (grid,rowIndex,colIndex,item,event){ Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
    switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickCapacitacionTarea(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
);
capTareasGridPanel.on('cellclick',function (grid,rowIndex,colIndex,item,event){ Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
    switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickCapacitacionTareaParticipante(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
);

function go_clickCapacitacionTarea (grid,rowIndex,columnIndex,item,event)
{
    var id_capacitacion=grid.store.data.items[rowIndex].data.id_capacitacion;
    var titulo=grid.store.data.items[rowIndex].data.titulo;

    if(columnIndex != 4)
    {
        capTareasDataStore.load({params: {id_capacitacion:id_capacitacion,start: 0}});
        capCapacitacionesParticipantesDataStore.load({params: {id_capacitacion:id_capacitacion,start: 0}});
        capTareasGridPanel.setTitle('Tareas generadas para el tema de capaciación: <div style="color:blue;display:inline;"> Nro '+id_capacitacion+'-> '+titulo+'</div>');
        capParticipantesGridPanel.setTitle('Personas capcitadas para el tema de capaciación: <div style="color:blue;display:inline;"> Nro '+id_capacitacion+'-> '+titulo+'</div>');
    }
}
function go_clickCapacitacionTareaParticipante (grid,rowIndex,columnIndex,item,event)
{
    var gridCap=Ext.getCmp('capCapacitacionesListingEditorGridPanel');
    var smCap=gridCap.getSelectionModel();
    var rowCap=smCap.getSelected();
    var id_cap=rowCap.data.id_capacitacion;
    var id_tarea=grid.store.data.items[rowIndex].data.id_tarea;
    var titulo=grid.store.data.items[rowIndex].data.titulo;
    capParticipantesGridPanel.setTitle('Personas capcitadas para el tema de capaciación: <div style="color:blue;display:inline;"> Nro '+id_cap+'</div> Tarea Nro: <div style="color:blue;display:inline;">'+id_tarea+'</div>');
    capCapacitacionesParticipantesDataStore.load({params: {id_capacitacion:id_cap,id_tarea:id_tarea,start: 0}});
}