supTopTab1 = new Ext.Panel({
            title:'Definición de Objetivos',
            iconCls: 'home-icon',
            disabled:false,
//            html: '',
            items:[supTopObjetivosGridPanel]
});
supTopTab2 = new Ext.Panel({
            title:'Evaluación'+TOP.spa_eval_estado,
            disabled:!TOP.spa_eval,
            items:[supTop1raEvObjetivosGridPanel]
//            html: ''
});
//supTopTab3 = new Ext.Panel({
//            title:'2da Evaluación',
////            disabled:!TOP.spa_2daEv,
//            disabled:true,
////            items:[]
////            html: ''
//});

supTopObjetivosTabPanel=new Ext.TabPanel({
    border: false,
    activeTab: TOP.activeTab,
    enableTabScroll:true,
    items:[supTopTab1,supTopTab2]
});


supTopObjetivosPanel = new Ext.Panel({
    id: 'supTopObjetivosPanel2',
//    title: 'Objetivos',
    region: 'center',
    layout:'fit',		
    border: true,
//    height: 400,
    items: [supTopObjetivosTabPanel]

}); 

//supTopBotonesPanel=new Ext.Panel({
//    id: 'supTopBotonesPanel',
////    title:'Botones',
//    bodyStyle: 'padding:15px',
//    border: false,
//     height: 220,
////    flex: 1,
////    defaultType :'button',
////    html: '<p>texto?.</p>',
//    items:[
//            
//        ]
//});
var alturaBotones=Ext.getBody().getSize().height - 60;
//supTopBotonesPanel.setHeight(alturaBotones);

//supTopWestPanel = new Ext.Panel({
//    id: 'supTopWestPanel',
//    width: 250,
//    minSize: 250,
////    maxSize: 400,
//    split: true,
//    collapsible: true,
//    layout: {
//        type: 'vbox',
//        pack: 'start',
//        align: 'stretch'
//    },
//    defaults: {
//        frame: true
//    },
//    region: 'west',
//    items:[supTopDimensionesPanel]
//});



supTopHistorialPanel = new Ext.Panel({
    id: 'supTopHistorialPanel',
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

//supTopWestPanel.on('collapse',ajustarGrillaObj);
//supTopWestPanel.on('expand',ajustarGrillaObj);
Ext.getCmp('browser').on('resize',ajustarGrillaObj);

supTopPanel=new Ext.Panel({
    id: 'supTopPanel',
    title:'Tarjeta de Objetivos Personales (TOP) de:<b> '+USR+'</b>',
    layout:'border',		
//    split: true,
    bodyStyle: 'padding:15px',
    border: true,
//    height: 400,
    tbar: [
          {
            text: 'Volver a listado',
            tooltip: 'volver al listado...',
            iconCls:'atras_ico',                      // reference to our css
            handler: clickBtnVolver, 
            hidden: !permiso_alta
          }
//          ,'&emsp;|&emsp;'
//          ,{
//            id:'botonDescExcel',
//            xtype:'button',
//            text: 'Descargar en Excel',
//            iconCls :'excel_ico',
//            cls : 'botones-panel',
////            width :150,
//            disabled :true,
//            handler: clickBtnExcelSupTop
//            }
      ],
    items: [supTopHistorialPanel,supTopObjetivosPanel],//supTopWestPanel,
    renderTo: 'grillita'

});

var alturaColIzq=Ext.getBody().getSize().height - 60;
supTopPanel.setHeight(alturaColIzq);

function ajustarGrillaObj(){
//    var westPanel=Ext.getCmp('supTopWestPanel');
    var brow=Ext.getCmp('browser');
//    var widthDimPanel=westPanel.getInnerWidth();
    supTopObjetivosGridPanel.setWidth(brow.getSize().width);
    supTopObjetivosGridPanel.setHeight(Ext.getBody().getSize().height - 160);
    supTopPanel.setWidth(brow.getSize().width);
    supTopPanel.setHeight(Ext.getBody().getSize().height - 60);
    supTopObjetivosPanel.setWidth(brow.getSize().width);
    supTopObjetivosPanel.setHeight(Ext.getBody().getSize().height - 60);
}	


function clickBtnVerHistorial(grid, rowIndex, columnIndex){
     var id=grid.store.data.items[rowIndex].data.id_objetivo;
     var obj=grid.store.data.items[rowIndex].data.obj;
//     console.log(grid);
      var h_panel=Ext.getCmp('supTopHistorialPanel');
    ddpHistorialObjetivoDataStore.load({params: {id_obj:id}});
    h_panel.setTitle('Historial del objetivo Nro: '+id+': <b style="color: black;">'+obj+"</b>");
    h_panel.show();
    h_panel.expand(false);

};

//function clickBtnExcelSupTop (){
//     var comboUsuarios=Ext.getCmp('usuariosTopsCombo');
//    var comboPeriodos=Ext.getCmp('periodosTopsCombo');
//    var idUsuario=comboUsuarios.getValue();
//    var idPeriodo=comboPeriodos.getValue();
//    var idTop=TOP.id_top;
//    supTopDimensionesGridPanel
//    var repositorio=supTopDimensionesGridPanel.getStore();
//    var dimPanel=Ext.getCmp('dimensiones-grid-panel2');
//    var repositorio=dimPanel.getStore();
//    var q_obj = repositorio.sum('q_obj');
//    if(q_obj>0)
//    {
//        window.open(CARPETA_EXCEL+'/miTopExcel');
//        var link = document.createElement("a");
//        link.download = "excel";
//       link.href =CARPETA_EXCEL+'/topExcel/'+idTop ;
//        document.body.appendChild(link);
//        link.click();
//        document.body.removeChild(link);
//        delete link;
//    }
//    else
//    {
//        Ext.MessageBox.alert('Error','El reporte se encuentra sin datos aún');
//    }
//};

function clickBtnVolver(){
    Ext.get('browser').load({
        url: CARPETA+"/index/31",
        scripts: true,
        text: "Cargando..."
    });
};