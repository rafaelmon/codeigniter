
capParticipantesPanel = new Ext.Panel(
{
        collapsible: false,
        split: false,
        header: true,
        region:'center',
        height: 300,
        margins: '0 5 5 5',
        layout: 'column',
        renderTo: 'grillita',
        items:[capGrillaParticipantesPanel,capGrillaCapacitacionesPanel]
});

altura=Ext.getBody().getSize().height - 60;
capParticipantesPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    capParticipantesPanel.setWidth(this.getSize().width);
    capParticipantesPanel.setHeight(Ext.getBody().getSize().height - 60);
});

participantesGrid.on('cellclick',function(grid, rowIndex, columnIndex){
    var id_persona=grid.store.data.items[rowIndex].data.id_persona;
    var participante=grid.store.data.items[rowIndex].data.participante;
    panelCap=Ext.getCmp('capParticipantesCapacitacionesGrid');
    dsCap=panelCap.getStore();
    dsCap.load({params: {id_persona:id_persona,start: 0}});
    panelCap.setTitle('Listado de Capacitaciones para: <div style="color:blue;display:inline;"> -> '+participante+'<div>');
});