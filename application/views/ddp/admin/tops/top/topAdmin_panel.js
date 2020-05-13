tab1 = new Ext.Panel({
            title:'Objetivos',
            iconCls: 'home-icon',
//            html: '',
            disabled:!TOP.spa_obj,
            items:[objetivosSupPanel]
});
tab2 = new Ext.Panel({
            title:'Evaluación',
            disabled:!TOP.spa_1raEv,
            items:[objetivos1raEvGridPanel]
//            disabled:DISABLED
//            html: ''
});
//tab3 = new Ext.Panel({
//            title:'2da Evaluavión',
//            disabled:DISABLED
////            html: ''
//});

objetivosTabPanel=new Ext.TabPanel({
        border: false,
        activeTab: TOP.activeTab,
        enableTabScroll:true,
        items:[tab1,tab2]
    });


objetivosPanel = new Ext.Panel({
    id: 'objetivosPanel2',
//    title: 'Objetivos',
    region: 'center',
    layout:'fit',		
    border: true,
//    height: 400,
    items: [objetivosTabPanel]

}); 

//combosPanel=new Ext.Panel({
//    id: 'combosPanel2',
////    title:'Botones',
//    bodyStyle: 'padding:15px',
//    border: false,
////    flex: 1,
////    defaultType :'button',
////    html: '<p>texto?.</p>',
//    items:[
//            {xtype: 'displayfield', value: 'Período'}
//            ,periodosTopsCombo
//            ,{xtype: 'displayfield', value: 'Usuario'}
//            ,usuariosTopsCombo
//            
//        ]
//});
botonesPanel=new Ext.Panel({
    id: 'botonesPanel2',
//    title:'Botones',
    bodyStyle: 'padding:15px',
    border: false,
     height: 220,
//    flex: 1,
//    defaultType :'button',
//    html: '<p>texto?.</p>',
    items:[
        {
            id:'miTopBotonPDF',
            text: 'Descargar TOP',
            disabled :false,
            iconCls :'mied_pdf_ico',
            handler: clickBtnPDF
        }
        ]
});
var alturaBotones=Ext.getBody().getSize().height - 60;
botonesPanel.setHeight(alturaBotones);

//westPanel = new Ext.Panel({
//    id: 'westPanel2',
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
//    items:[botonesPanel]
//});



historialPanel = new Ext.Panel({
    id: 'historialObjSupPanel',
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

//westPanel.on('collapse',ajustarGrillaObj);
//westPanel.on('expand',ajustarGrillaObj);
Ext.getCmp('browser').on('resize',ajustarGrillaObj);

topPanel=new Ext.Panel({
    id: 'topPanel2',
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
      ],
    items: [new Ext.BoxComponent({
                region: 'north',
                height: 10, // give north and south regions a height
                autoEl: {
                    tag: 'div'
//                    html:'<p>Filtros?</p>'
                }
            }),historialPanel,objetivosPanel],
    renderTo: 'grillita'

});

var alturaColIzq=Ext.getBody().getSize().height - 60;
topPanel.setHeight(alturaColIzq);

function ajustarGrillaObj(){
    var brow=Ext.getCmp('browser');
    objetivosGridPanel.setWidth(brow.getSize().width);
    objetivosGridPanel.setHeight(Ext.getBody().getSize().height - 60);
    topPanel.setWidth(brow.getSize().width);
    topPanel.setHeight(Ext.getBody().getSize().height - 60);
    objetivosPanel.setWidth(brow.getSize().width);
    objetivosPanel.setHeight(Ext.getBody().getSize().height - 60);
;}	


function clickBtnVerHistorial(grid, rowIndex, columnIndex){
     var id=grid.store.data.items[rowIndex].data.id_objetivo;
     var obj=grid.store.data.items[rowIndex].data.obj;
    ddpHistorialObjetivoDataStore.load({params: {id_obj:id}});
    historialPanel.setTitle('Historial del objetivo Nro: '+id+': <b style="color: black;">'+obj+"</b>");
    historialPanel.show();
    historialPanel.expand(false);

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

function clickBtnVolver(){
    Ext.get('browser').load({
        url: CARPETA+"/index/31",
        scripts: true,
        text: "Cargando..."
    });
};