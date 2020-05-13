tab1 = new Ext.Panel({
            title:'Objetivos',
            iconCls: 'home-icon',
//            html: '',
            items:[objetivosGridPanel]
});
tab2 = new Ext.Panel({
            title:'1ra Evaluación',
            disabled:DISABLED
//            html: ''
});
tab3 = new Ext.Panel({
            title:'2da Evaluación',
            disabled:DISABLED
//            html: ''
});

objetivosTabPanel=new Ext.TabPanel({
     border: false,
        activeTab: 0,
        enableTabScroll:true,
        items:[tab1,tab2,tab3]
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

combosPanel=new Ext.Panel({
    id: 'combosPanel2',
//    title:'Botones',
    bodyStyle: 'padding:15px',
    border: false,
//    flex: 1,
//    defaultType :'button',
//    html: '<p>texto?.</p>',
    items:[
            {xtype: 'displayfield', value: 'Período'}
            ,periodosTopsCombo
            ,{xtype: 'displayfield', value: 'Usuario'}
            ,usuariosTopsCombo
            
        ]
});
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
            id:'botonDescExcel',
            xtype:'button',
            text: 'Descargar en Excel',
            iconCls :'excel_ico',
            cls : 'botones-panel',
            width :150,
            disabled :true
//            handler: clickBtnExcelSupTop
            }
        ]
});

westPanel = new Ext.Panel({
    id: 'westPanel2',
    width: 250,
    minSize: 250,
    maxSize: 400,
    split: true,
    collapsible: true,
    layout: {
        type: 'vbox',
        pack: 'start',
        align: 'stretch'
    },
    defaults: {
        frame: true
    },
    region: 'west',
    items:[combosPanel,dimensionesPanel,botonesPanel]
});



historialPanel = new Ext.Panel({
//    id: 'historialPanel',
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

westPanel.on('collapse',ajustarGrillaObj);
westPanel.on('expand',ajustarGrillaObj);
Ext.getCmp('browser').on('resize',ajustarGrillaObj);

topPanel=new Ext.Panel({
    id: 'topPanel2',
    layout:'border',		
//    split: true,
    bodyStyle: 'padding:15px',
    border: true,
//    height: 400,
    items: [new Ext.BoxComponent({
                region: 'north',
                height: 10, // give north and south regions a height
                autoEl: {
                    tag: 'div'
//                    html:'<p>Filtros?</p>'
                }
            }),historialPanel,westPanel,objetivosPanel],
    renderTo: 'grillita'

});

altura=Ext.getBody().getSize().height - 60;
topPanel.setHeight(altura);

function ajustarGrillaObj(){
    var westPanel=Ext.getCmp('westPanel2');
    var brow=Ext.getCmp('browser');
//    var widthDimPanel=westPanel.getInnerWidth();
    objetivosGridPanel.setWidth(brow.getSize().width);
    objetivosGridPanel.setHeight(Ext.getBody().getSize().height - 60);
    topPanel.setWidth(brow.getSize().width);
    topPanel.setHeight(Ext.getBody().getSize().height - 60);
    objetivosPanel.setWidth(brow.getSize().width);
    objetivosPanel.setHeight(Ext.getBody().getSize().height - 60);
;}	


function clickBtnVerHistorial(grid, rowIndex, columnIndex){
     var id=grid.store.data.items[rowIndex].data.id_objetivo;
     var obj=grid.store.data.items[rowIndex].data.op;
    ddpHistorialObjetivoDataStore.load({params: {id_obj:id}});
    historialPanel.setTitle('Historial del objetivo Nro: '+id+': <b style="color: black;">'+obj+"</b>");
    historialPanel.show();
    historialPanel.expand(false);

}

//function clickBtnExcelSupTop (){
//     var comboUsuarios=Ext.getCmp('usuariosTopsCombo');
//    var comboPeriodos=Ext.getCmp('periodosTopsCombo');
//    var idUsuario=comboUsuarios.getValue();
//    var idPeriodo=comboPeriodos.getValue();
//    
//    var dimPanel=Ext.getCmp('dimensiones-grid-panel2');
//    var repositorio=dimPanel.getStore();
//    var q_obj = repositorio.sum('q_obj');
//    if(q_obj>0)
//    {
////        window.open(CARPETA_EXCEL+'/miTopExcel');
//        var link = document.createElement("a");
//        link.download = "excel";
//       link.href =CARPETA_EXCEL+'/topExcel/'+idPeriodo+"/"+idUsuario ;
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