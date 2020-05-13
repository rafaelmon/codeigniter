var miTopObjetivosTabPanel;
var miTopTab1;
var miTopTab2;
var miTopTab3;
var mitopDimensionesPanel;
var miTopObjetivosTabPanel;
var miTopObjetivosPanel;
var miTopPanel;
var DISABLED=true;
var SUM_DIM=0;

miTopTab1 = new Ext.Panel({
            title:'Definición de Objetivos',
            iconCls: 'home-icon',
//            html: '',
//            disabled:false,
            disabled:!TOP.spa_obj,
            items:[miTopObjetivosGridPanel]
});
miTopTab2 = new Ext.Panel({
            title:'Evaluación'+TOP.spa_eval_estado,
//            disabled:false,
            disabled:!TOP.spa_eval,
            items:[objetivos1raEvGridPanel]
//            html: ''
});

miTopObjetivosTabPanel=new Ext.TabPanel({
    border: false,
    activeTab: TOP.activeTab,
    enableTabScroll:true,
    items:[miTopTab1,miTopTab2]
});


miTopObjetivosPanel = new Ext.Panel({
    id: 'miTopObjetivosPanel',
//    title: 'Objetivos',
    region: 'center',
    layout:'fit',		
    border: true,
//    height: 400,
    tbar: [
        {
            text: 'Volver a listado',
            tooltip: 'volver al listado...',
            iconCls:'atras_ico',
            handler: clickBtnVolver, 
            hidden: !permiso_alta
        }
        ,'&emsp;|&emsp;'
        ,{
            id:'miTopBotonCerrar',
            text: 'Cerrar Objetivos TOP',
            iconCls:'cerrar_ico',
            disabled :!TOP.btn_cerrar,
            handler: clickBtnCerrarTop
        }
        ,'&emsp;|&emsp;'
        ,{
            id:'miTopBotonCerrar1ev',
            text: 'Cerrar evaluación',
            iconCls:'cerrar_ico',
            disabled :!TOP.btn_cerrar_eval,
            handler: clickBtnCerrarTop1ev
        }
        ,'&emsp;|&emsp;'
        ,{
            id:'miTopBotonPDF',
            text: 'Descargar TOP',
            disabled :false,
            iconCls :'mied_pdf_ico',
            handler: clickBtnPDF
        }
    ],
    items: [miTopObjetivosTabPanel]
}); 



miTopBotonesPanel=new Ext.Panel({
    id: 'miTopBotonesPanel',
//    title:'Botones',
    bodyStyle: 'padding:15px',
    flex: 1,
    defaultType :'button',
    defaults :{
        cls : 'botones-panel',
        width :150
    },
    items:[
//        {
//            id:'botonaltatop',
//            text: 'Iniciar TOP',
//            disabled :BTN_NUEVATOP,
//            handler: dFW_ddpNuevoTop
//        }
        {
            id: 'volver',
            text: 'Volver a listado',
            tooltip: 'volver al listado...',
            iconCls:'atras_ico',                      // reference to our css
            handler: clickBtnVolver, 
            hidden: !permiso_alta
        },{
            id:'botonaltaobj',
            text: 'Nuevo objetivo',
            disabled :!TOP.btn_altaObj,
            handler: clickBtnNuevoObj
        },{
            id:'miTopBotonCerrar',
            text: 'Cerrar Objetivos TOP',
            disabled :!TOP.btn_cerrar,
            handler: clickBtnCerrarTop
        },{
            id:'miTopBotonCerrar1ev',
            text: 'Cerrar evaluación',
            disabled :!TOP.btn_cerrar_eval,
            handler: clickBtnCerrarTop1ev
        }
        ]
});

miTopWestPanel = new Ext.Panel({
    id: 'miTopWestPanel',
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
    items:[]//,miTopBotonesPanel]
});



miTopHistorialPanel = new Ext.Panel({
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
    items: [miTopDdpHistorialObjetivoGrid],
    region: 'south'

}); 

miTopWestPanel.on('collapse',ajustarGrillaObj);
miTopWestPanel.on('expand',ajustarGrillaObj);
Ext.getCmp('browser').on('resize',ajustarGrillaObj);

miTopPanel=new Ext.Panel({
    id: 'miTopPanel',
    title: 'Mi Tarjeta de Objetivos Personales - Supervisada por '+supervisor,
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
            }),miTopHistorialPanel,miTopObjetivosPanel],//miTopWestPanel
    renderTo: 'grillita'

});

altura=Ext.getBody().getSize().height - 60;
miTopPanel.setHeight(altura);

function ajustarGrillaObj(){
    var brow=Ext.getCmp('browser');
//    var widthDimPanel=westPanel.getInnerWidth();
    miTopObjetivosGridPanel.setWidth(brow.getSize().width);
    miTopObjetivosGridPanel.setHeight(Ext.getBody().getSize().height - 160);
    miTopPanel.setWidth(brow.getSize().width);
    miTopPanel.setHeight(Ext.getBody().getSize().height - 60);
    miTopObjetivosPanel.setWidth(brow.getSize().width);
    miTopObjetivosPanel.setHeight(Ext.getBody().getSize().height - 60);
;}	

function clickGridDimension(grid, rowIndex, columnIndex){
//    console.log(BTN_NUEVATOP)
    if(BTN_NUEVATOP)
    {
    //    var id_estado=grid.store.data.items[rowIndex].data.id_estado;
        var id=grid.store.data.items[rowIndex].data.id_dimension;
        var o_panel=Ext.getCmp('miTopObjetivosGridPanel');
        var o_cm=o_panel.getColumnModel();
//        if (id==6)
//        {
//            for (var i=2;i<=7;i++)
//                o_cm.setEditable(i,false);
//        }
//        else
//        {
//            for (var i=2;i<=7;i++)
//                o_cm.setEditable(i,true);
//        }
        ID_DIM=id;
        //DIM=grid.store.data.items[rowIndex].data.dimension;
        miTopObjetivosDS.load({params: {id_dimension:id,start: 0}});
//        objetivos1raEvDS.load({params: {id_dimension:id,start: 0}});

    }
};
function clickBtnNuevoObj (id_dimension){
//    if (miTopDimensionesGridPanel.selModel.hasSelection())
//    {
        //var q_sel = miTopDimensionesGridPanel.selModel.getCount();
        //var selection = miTopDimensionesGridPanel.selModel.getSelections();
//        var id_dimension = 1;//selection[0].json.id_dimension;
        ID_DIM = id_dimension;
//        console.log(ID_DIM);
        if(id_dimension <6)
        {
            dFW_ddpNuevoObetivo();
        }
        else
        {
            if (id_dimension==6)
                Ext.MessageBox.alert('Error','La dimensión <b>Principios</b> no lleva objetivos personales');	  
            else
                Ext.MessageBox.alert('Error','Por Favor seleccione solo una dimensión');	  
        }
        
//    }
//    else
//        Ext.MessageBox.alert('Error','Por Favor seleccione antes una dimensi&oacute;n');	  
};
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

function clickBtnVerHistorial(grid, rowIndex, columnIndex){
     var id=grid.store.data.items[rowIndex].data.id_objetivo;
     var obj=grid.store.data.items[rowIndex].data.obj;
     var dim=grid.store.data.items[rowIndex].data.dimension;
    miTopDdpHistorialObjetivoDataStore.load({params: {id_obj:id}});
    miTopHistorialPanel.setTitle('Historial '+dim+' Id nro: '+id+': <b style="color: black;">'+obj+"</b>");
    miTopHistorialPanel.show();
    miTopHistorialPanel.expand(false);

}
function clickBtnCerrarTop (grid,rowIndex,colIndex,item,event){
      msgProcess('Procesando...');
        Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/cerrar_mitop',
        method: 'POST',
        params: {
          id:TOP.id_top   
        }, 
        success: function(response){              
          var result=eval(response.responseText);
//          Ext.MessageBox.hide(); 
          switch(result.success){
          case 'true':
          case true:
                var btnCerrar=Ext.getCmp('miTopBotonCerrar');
                var btnAltaObj=Ext.getCmp('botonaltaobj');
                btnCerrar.disable();
                btnAltaObj.disable();
                recargarTop();
                Ext.MessageBox.alert('Operación OK','TOP cerrada correctamente');
                break;
          case 'false':
          case false:
                Ext.MessageBox.alert('Error',result.error);
            break;
          default:
                Ext.MessageBox.alert('Error',result.error);
            break;
          }        
        },
        failure: function(response){
//          var result=response.responseText;
          Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
        }                      
      });
  };
  function clickBtnCerrarTop1ev (grid,rowIndex,colIndex,item,event){
      msgProcess('Procesando...');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/cerrar_eval1',
            method: 'POST',
            params: {
              id:TOP.id_top   
            }, 
            success: function(response){              
              var result=eval(response.responseText);
    //          Ext.MessageBox.hide(); 
              switch(result.success){
              case 'true':
              case true:
                    var btnCerrar=Ext.getCmp('miTopBotonCerrar');
                    var btnAltaObj=Ext.getCmp('botonaltaobj');
                    var btnCerrarEval1=Ext.getCmp('miTopBotonCerrar1ev');
                    btnCerrar.disable();
                    btnAltaObj.disable();
                    btnCerrarEval1.disable();
                    recargarTop();
                    Ext.MessageBox.alert('Operación OK','1ra Evaluación cerrada correctamente');
                    break;
              case 'false':
              case false:
                    Ext.MessageBox.alert('Error',result.error);
                break;
              default:
                    Ext.MessageBox.alert('Error',result.error);
                break;
              }        
            },
            failure: function(response){
    //          var result=response.responseText;
              Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
            }                      
      });
  };
function clickBtnCerrarTop2ev (grid,rowIndex,colIndex,item,event){};
//console.log(miTopTab1);

function altaQue()
{
    var id_dimension = 1;
    DIM = 'Que';
    clickBtnNuevoObj(id_dimension);
}

function altaComo()
{
    var id_dimension = 2;
    DIM = 'Como';
    clickBtnNuevoObj(id_dimension);
}

function altaOrg()
{
    var id_dimension = 3;
    DIM = 'Organizacional';
    clickBtnNuevoObj(id_dimension);
}
  
function recargarTop(){
    Ext.get('browser').load({
        url: CARPETA+"/index/29",
        params: {},
        scripts: true,
        text: "Cargando..."
    });
}

function clickBtnVolver(){
    Ext.get('browser').load({
        url: CARPETA+"/index/29",
        scripts: true,
        text: "Cargando..."
    });
};