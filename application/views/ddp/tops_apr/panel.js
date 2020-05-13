
ddpTopsAprobadorPanel = new Ext.Panel({
    id: 'ddpTopsAprobadorPanel',
//    title: 'Objetivos',
    region: 'center',
    layout:'fit',		
    border: true,
//    height: 400,
    items: [ddpAprTopsGrid]

}); 

ddpTopsAprAuditoriaPanel = new Ext.Panel({
    id: 'ddpTopsAprAuditoriaPanel',
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
    items: [ddpTopAprAuditoriaGrid],
    region: 'south'

}); 

//westPanel.on('collapse',ajustarGrillaObj);
//westPanel.on('expand',ajustarGrillaObj);
Ext.getCmp('browser').on('resize',ajustarGrillaObj);

ddpTopsAprPanel=new Ext.Panel({
    id: 'ddpTopsAprPanel2',
//    title:'Tarjeta de Objetivos Personales (TOP) de:<b> '+USR+'</b>',
    layout:'border',		
//    split: true,
    bodyStyle: 'padding:15px',
    border: true,
//    height: 400,
    tbar: [],
    items: [new Ext.BoxComponent({
                region: 'north',
                height: 10, // give north and south regions a height
                autoEl: {
                    tag: 'div'
//                    html:'<p>Filtros?</p>'
                }
            }),ddpTopsAprAuditoriaPanel,ddpTopsAprobadorPanel],
    renderTo: 'grillita'

});

var alturaColIzq=Ext.getBody().getSize().height - 60;
ddpTopsAprPanel.setHeight(alturaColIzq);

function ajustarGrillaObj(){
    var brow=Ext.getCmp('browser');
    ddpAprTopsGrid.setWidth(brow.getSize().width);
    ddpAprTopsGrid.setHeight(Ext.getBody().getSize().height - 60);
    ddpTopsAprPanel.setWidth(brow.getSize().width);
    ddpTopsAprPanel.setHeight(Ext.getBody().getSize().height - 60);
    ddpTopsAprobadorPanel.setWidth(brow.getSize().width);
    ddpTopsAprobadorPanel.setHeight(Ext.getBody().getSize().height - 60);
;}	

function clickBtnVerHistorialDDpTopAprAud(grid, rowIndex, columnIndex){
     var id_top = grid.store.data.items[rowIndex].data.id_top;
     //var obj    = grid.store.data.items[rowIndex].data.obj;
    ddpTopAprAuditoriaDataStore.load({params: {id_top:id_top}});
    ddpTopsAprAuditoriaPanel.setTitle('Auditoria de la TOP Nro: '+id_top);
    ddpTopsAprAuditoriaPanel.show();
    ddpTopsAprAuditoriaPanel.expand(false);
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