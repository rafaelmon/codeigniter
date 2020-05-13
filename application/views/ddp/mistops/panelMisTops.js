
ddpMisTopsrPanel = new Ext.Panel({
    id: 'ddpMisTopsrPanel',
//    title: 'Objetivos',
    region: 'center',
    layout:'fit',		
    border: true,
//    height: 400,
    items: [ddpMisTopsGrid]

}); 

auditoriaPanel = new Ext.Panel({
    id: 'auditoriaPanel',
//    title: 'Historial',
    height: 250,
    minSize: 250,
    maxSize: 250,
    collapsible: true,
    collapsed :true,
    split: true,
    bodyBorder:false,
//    autoScroll :true,
//    margins: '0 0 0 0',
//    contentEl: 'south'
    items: [ddpTopAuditoriaGrid],
    region: 'south'

}); 

//westPanel.on('collapse',ajustarGrillaObj);
//westPanel.on('expand',ajustarGrillaObj);
Ext.getCmp('browser').on('resize',ajustarGrillaObj);

topPanel=new Ext.Panel({
    id: 'topPanel2',
//    title:'Tarjeta de Objetivos Personales (TOP) de:<b> '+USR+'</b>',
    layout:'border',		
//    split: true,
    bodyStyle: 'padding:15px',
    border: true,
//    height: 400,
    tbar: [
//          {
//            text: 'Volver a listado',
//            tooltip: 'volver al listado...',
//            iconCls:'atras_ico',                      // reference to our css
//            handler: clickBtnVolver, 
//            hidden: !permiso_alta
//          }
      ],
    items: [new Ext.BoxComponent({
                region: 'north',
                height: 10, // give north and south regions a height
                autoEl: {
                    tag: 'div'
//                    html:'<p>Filtros?</p>'
                }
            }),auditoriaPanel,ddpMisTopsrPanel],
    renderTo: 'grillita'

});

var alturaColIzq=Ext.getBody().getSize().height - 60;
topPanel.setHeight(alturaColIzq);

function ajustarGrillaObj(){
    var brow=Ext.getCmp('browser');
    ddpMisTopsGrid.setWidth(brow.getSize().width);
    ddpMisTopsGrid.setHeight(Ext.getBody().getSize().height - 60);
    topPanel.setWidth(brow.getSize().width);
    topPanel.setHeight(Ext.getBody().getSize().height - 60);
    ddpMisTopsrPanel.setWidth(brow.getSize().width);
    ddpMisTopsrPanel.setHeight(Ext.getBody().getSize().height - 60);
;}	

function clickBtnVerGrillaAuditoria(grid, rowIndex, columnIndex){
     var id_top = grid.store.data.items[rowIndex].data.id_top;
     //var obj    = grid.store.data.items[rowIndex].data.obj;
    ddpTopAuditoriaDataStore.load({params: {id_top:id_top}});
    auditoriaPanel.setTitle('Auditoria de la TOP Nro: '+id_top);
    auditoriaPanel.show();
    auditoriaPanel.expand(false);
}

function clickBtnPDF (){
//        window.open(CARPETA_EXCEL+'/miTopExcel');
        var link = document.createElement("a");
        link.download = "excel";
        link.href =CARPETA_PDF+'/miTop/'+TOP.id_top ;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        delete link;

};