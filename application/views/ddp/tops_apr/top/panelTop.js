ddpTopAprTab1 = new Ext.Panel({
            title:'Definición de Objetivos',
            iconCls: 'home-icon',
            disabled:false,
//            html: '',
            items:[ddpTopAprObjetivosGridPanel]
});
ddpTopAprTab2 = new Ext.Panel({
            title:'Evaluación'+TOP.spa_eval_estado,
            disabled:!TOP.spa_eval,
            items:[ddpTopAprEvaluacionObjetivosGridPanel]
//            html: ''
});
ddpTopAprObjetivosTabPanel=new Ext.TabPanel({
    border: false,
    activeTab: TOP.activeTab,
    enableTabScroll:true,
    items:[ddpTopAprTab1,ddpTopAprTab2]
});
ddpTopAprObjetivosPanel = new Ext.Panel({
    id: 'ddpTopAprObjetivosPanel2',
//    title: 'Objetivos',
    region: 'center',
    layout:'fit',		
    border: true,
//    height: 400,
    items: [ddpTopAprObjetivosTabPanel]

}); 

var alturaBotones=Ext.getBody().getSize().height - 60;

ddpTopAprHistorialPanel = new Ext.Panel({
    id: 'ddpTopAprHistorialPanel',
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
    items: [ddpHistorialObjetivoGrid],
    region: 'south'

}); 

//ddpTopAprWestPanel.on('collapse',ajustarGrillaObj);
//ddpTopAprWestPanel.on('expand',ajustarGrillaObj);
Ext.getCmp('browser').on('resize',ajustarGrillaObj);

ddpTopAprPanel=new Ext.Panel({
    id: 'ddpTopAprPanel',
    title:'Tarjeta de Objetivos Personales (TOP) de:<span style="color:#FFF707"><b> '+USR+'</b></span>',
    layout:'border',		
//    split: true,
    bodyStyle: 'padding:15px',
    border: true,
//    height: 400,
    tbar: [
          {
            text: 'Volver a listado',
            tooltip: 'volver al listado...',
            iconCls:'atras_ico',                     
            handler: clickBtnVolverGrillaApr, 
            hidden: !permiso_alta
//          },"|",{
//            text: 'Descargar',
//            tooltip: 'descargar TOP en formato PDF',
//            iconCls:'btn_pdf_ico',                     
//            handler: clickBtnPdfTopApr, 
//            hidden: !permiso_alta
//          },"|",{
//            text: 'Aprobar',
//            tooltip: 'Aprobar TOP',
//            iconCls:'btn_aprobar_ico',                     
////            handler: clickBtnAprobarTop, 
//            hidden: !permiso_alta
//          },"|",{
//            text: 'Rechazar',
//            tooltip: 'Rechazar TOP',
//            iconCls:'btn_rechazar_ico',                     
////            handler: clickBtnPdfTopApr, 
//            hidden: !permiso_alta
          }
      ],
    items: [ddpTopAprHistorialPanel,ddpTopAprObjetivosPanel],//ddpTopAprWestPanel,
    renderTo: 'panel_top'

});

var alturaColIzq=Ext.getBody().getSize().height - 60;
ddpTopAprPanel.setHeight(alturaColIzq);

function ajustarGrillaObj(){
//    var westPanel=Ext.getCmp('ddpTopAprWestPanel');
    var brow=Ext.getCmp('browser');
//    var widthDimPanel=westPanel.getInnerWidth();
    ddpTopAprObjetivosGridPanel.setWidth(brow.getSize().width);
    ddpTopAprObjetivosGridPanel.setHeight(Ext.getBody().getSize().height - 160);
    ddpTopAprPanel.setWidth(brow.getSize().width);
    ddpTopAprPanel.setHeight(Ext.getBody().getSize().height - 60);
    ddpTopAprObjetivosPanel.setWidth(brow.getSize().width);
    ddpTopAprObjetivosPanel.setHeight(Ext.getBody().getSize().height - 60);
}	


function clickBtnVerHistorial(grid, rowIndex, columnIndex){
     var id=grid.store.data.items[rowIndex].data.id_objetivo;
     var obj=grid.store.data.items[rowIndex].data.obj;
//     console.log(grid);
      var h_panel=Ext.getCmp('ddpTopAprHistorialPanel');
    ddpHistorialObjetivoDataStore.load({params: {id_obj:id}});
    h_panel.setTitle('Historial del objetivo Nro: '+id+': <b style="color: black;">'+obj+"</b>");
    h_panel.show();
    h_panel.expand(false);

};


function clickBtnVolverGrillaApr(){
    Ext.get('browser').load({
        url: CARPETA+"/index/57",
        scripts: true,
        text: "Cargando..."
    });
};
function clickBtnPdfTopApr (){
    Ext.Ajax.request({ 
        url: LINK_GENERICO+'/sesion',
        method: 'POST',
        waitMsg: 'Por favor espere...',
        success: function(response, opts) {
            var result=parseInt(response.responseText);
            switch (result)
            {
                case 0:
                case '0':
                    location.assign(URL_BASE_SITIO+"admin");
                    break;
                case 1:
                case '1':
                    go_clickBtnExcelTopAdmin();
                    break;
            }
      },
        failure: function(response) {
            location.assign(URL_BASE_SITIO+"admin");
        }
        });
}