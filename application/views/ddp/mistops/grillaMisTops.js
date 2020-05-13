
ddpTopsAdminDataStore = new Ext.data.GroupingStore({
    id: 'ddpTopsAdminDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listadoMisTops', 
            method: 'POST'
        }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
    root: 'rows',
    totalProperty: 'total',
    id: 'id_top'
    },[ 
    {name: 'id_top',        type: 'int',        mapping: 'id_top'},        
    {name: 'periodo',       type: 'string',     mapping: 'periodo'},
    {name: 'fecha_alta',    type: 'string',     mapping: 'fecha_alta'},
//    {name: 'usuario',       type: 'string',     mapping: 'usuario'},
    {name: 'supervisor',    type: 'string',     mapping: 'supervisor'},
    {name: 'aprobador',     type: 'string',     mapping: 'aprobador'},
    {name: 'gerencia',      type: 'string',     mapping: 'gerencia'},
    {name: 'puesto',        type: 'string',     mapping: 'puesto'},
    {name: 'estado',        type: 'string',     mapping: 'estado'},
    {name: 's_pesos',       type: 'float',      mapping: 's_pesos'},
    {name: 'q_obj',         type: 'int',        mapping: 'q_obj'},
    {name: 'q_obj_sup',     type: 'int',        mapping: 'q_obj_sup'},
    {name: 'q_obj_aprob',   type: 'int',        mapping: 'q_obj_aprob'},
    {name: 'habilitado',    type: 'int',        mapping: 'habilitado'},
    {name: 's_pesoreal',    type: 'float',      mapping: 's_pesoreal'}
    ]),
    sortInfo:{field: 'estado', direction: "ASC"},  
//    groupField:'estado',
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(ddpTopsAdminDataStore);

botonesAction = new Ext.grid.ActionColumn({
    editable:false,
    menuDisabled:true,
    header:'Acción',
    hideable:false,
    align:'center',
    width:  80,
//    tooltip:'Ver TOP',
    hidden:false,
    items:[
        {
            icon:URL_BASE+'images/tooloptions.png',
            iconCls :'col_accion',
            tooltip:'Ver TOP',
            getClass: showBtnVerTopDr,
            handler: clickBtnVerTop
        }
        ,{
            icon:URL_BASE+'images/edit-find.png',
            iconCls :'col_accion',
            tooltip:'Auditoria',
            handler: clickBtnVerGrillaAuditoria
        }
        ,{
            icon:URL_BASE+'images/doc_pdf.png',
            iconCls :'col_accion',
//                    getClass: showBtnModificar,
            tooltip:'Descargar',
            handler: clickBtnDescargarPDFMisTops
        }
    ]
});

ddpTopsAdminColumnModel = new Ext.grid.ColumnModel(
    [
        {
            header: '#',
            readOnly: true,
            dataIndex: 'id_top',
            width: 40,        
            sortable: true,
            renderer: function(value, cell){
                cell.css = "readonlycell";
                return value;
                },
            hidden: false
        }
        ,{
            header: 'Periodo',
            dataIndex: 'periodo',
            width: 80,
            sortable: true,
            renderer: showQTipPeriodo
        }
        ,{
            header: 'Estado',
            dataIndex: 'estado',
            width: 120,
            sortable: true,
            renderer: showEstado
        }
//        ,{
//            header: 'Usuario',
//            dataIndex: 'usuario',
//            width: 250,
//            sortable: true
//        }
        ,{
            header: 'Puesto',
            dataIndex: 'puesto',
            width: 200,
            sortable: false
        }
        ,{
            header: 'Gerencia',
            dataIndex: 'gerencia',
            width: 250,
            sortable: true
        }
        ,{
            header: 'Supervisor',
            dataIndex: 'supervisor',
            width: 200,
            sortable: true
        }
        ,{
            header: 'Aprobador',
            dataIndex: 'aprobador',
            width: 200,
            sortable: true
        }
        ,{
            header: '&Sigma; <b>Obj</b>',
            tooltip:'Cantidad de Objetivos',
            dataIndex: 'q_obj',
            width: 70,
            align:'center',
            summaryType: 'sum',
            sortable: false
        }
        ,{
            header: '&Sigma; <b>Peso</b>',
            tooltip:'Sumatoria de Pesos',
            dataIndex: 's_pesos',
            width: 70,
            align:'center',
            summaryType: 'sum',
            sortable: false
        }
        ,{
            header: '&Sigma; <b>Peso real</b>',
            tooltip:'Cantidad de pesos reales',
            dataIndex: 's_pesoreal',
            width: 70,
            align:'center',
            summaryType: 'sum',
            sortable: false
        }
        ,botonesAction
//        {
//        header: '&Sigma; <b>Obj sup</b>',
//        tooltip:'Cantidad de Objetivos para supervisar',
//        dataIndex: 'q_obj_sup',
//        width: 70,
//        align:'center',
//        summaryType: 'sum',
//        sortable: false
//        },
        /*ddpTopsAdminhabilitadaCheck,*/
    ]
);
ddpTopsAdminBuscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
    //    readonlyIndexes:[''],
    disableIndexes:['id_top','periodo','q_obj','s_pesos','q_obj_aprob','q_obj_sup','estado','s_pesoreal'],
    align:'left',
    minChars:5
});
  
   ddpMisTopsGrid =  new Ext.grid.GridPanel({
        id: 'ddpMisTopsGrid',
        title: 'Mis TOPs',
        store: ddpTopsAdminDataStore,
        cm: ddpTopsAdminColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
//        viewConfig: {
//            forceFit: false
//        },
        view: new Ext.grid.GroupingView({
            forceFit:true,
            showGroupName:false,
            groupTextTpl: '{text} <b style="color:#A4A4A4;">({[values.rs.length]} {[values.rs.length > 1 ? "objetivos" : "objetivo"]})</b>'
        }),
        plugins:[ddpTopsAdminBuscador/*,ddpTopsAdminhabilitadaCheck*/],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
//        tbar: ['<b>Filtrar por-></b> Período',periodosTopsAdminFiltro]
        tbar: [
            {
                id:'botonaltatop',
                text: 'Nueva TOP',
                iconCls:'add',
//                disabled :BTN_NUEVATOP,
                handler: dFW_ddpNuevoTop
            }
            /*,'&emsp;|&emsp;'
            ,{
                text: 'Descargar listado',
    //            tooltip: 'e...',
                iconCls:'archivo_excel_ico'
//                handler: clickBtnExcelTopAdmin
            }*/]
    }); 

  ddpTopsAdminDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

//var altura=Ext.getBody().getSize().height - 60;
//ddpMisTopsGrid.setHeight(altura);
//
//Ext.getCmp('browser').on('resize',function(comp){
//        ddpMisTopsGrid.setWidth(this.getSize().width);
//        ddpMisTopsGrid.setHeight(Ext.getBody().getSize().height - 60);
//
//});
function msgProcess(titulo){
    Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:300, 
        wait:true, 
        waitConfig: {interval:200}
    });
}

function showQTipPeriodo(value, metaData,record){
    var deviceDetail = record.get('periodo');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipSupervisores(value, metaData,record){
    var deviceDetail = record.get('supervisores');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function clickBtnVerTop(grid,rowIndex,colIndex,item ,event){
    var id_top=grid.getStore().getAt(rowIndex).json.id_top;
    Ext.get('browser').load({
        url: CARPETA+"/mi_top/29",
        params: {id_top: id_top},
        scripts: true,
        text: "Cargando..."
    });
}
//function filtrarGrillaTopsAdmin (combo, record, index){
//    ddpTopsAdminDataStore.load({
//            params: {
//                    filtro_id_periodo: Ext.getCmp('periodosTopsAdminFiltro').getValue()
//            }
//    });	
//};
function showBtnVerTopDr(value,metaData,record){
    var a=record.json.id_top;
    if(a=="" || a==0 || a=='0' || a==null)
    {
        return 'x-hide-display';  
    }            
    else
        return 'x-grid-center-icon';                
};
function showEstado(value,metaData,record){
    var a=record.json.id_estado;
    switch (a) {
        case '1':
        case 1:
            metaData.attr = 'style="color:#000;background-color:#FFFA54;"';
            break;
        case '2':
        case 2:
            metaData.attr = 'style="color:#000;background-color:#96CCFF;"';
            break;
        case '4':
        case 4:
            metaData.attr = 'style="color:#FFF;background-color:#21610B;"';
            break;
            
        default:
            metaData.attr = 'style="color:#FFF;background-color:#FF0000;"';
            break;
    }
    return value;
};
function clickBtnDescargarPDFMisTops (grid,rowIndex,colIndex,item ,event){
    var id_top=grid.getStore().getAt(rowIndex).json.id_top;
    var link = document.createElement("a");
    link.download = "pdf";
    link.href =CARPETA_PDF+'/miTop/'+id_top ;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    delete link;
};