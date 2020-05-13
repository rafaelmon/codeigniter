periodosTopSupJS = new Ext.data.JsonStore({
	url: CARPETA+'/periodos_combo',
	root: 'rows',
	fields: ['id_periodo', 'periodo']
});
periodosTopSupJS.load();
periodosTopSupJS.on('load' , function(  js , records, options ){
	var tRecord = Ext.data.Record.create(
            {name: 'id_periodo', type: 'int'},        
            {name: 'periodo', type: 'string'}
	);
	var myNewT = new tRecord({
		id_periodo: -1,
		periodo   : 'Todos'
	});
	periodosTopSupJS.insert( 0, myNewT);	
} );
periodosTopSupFiltro = new Ext.form.ComboBox({
    id:'periodosTopSupFiltro',
    forceSelection : true,
    value: PERIODO,
    store: periodosTopSupJS,
    editable : false,
    displayField: 'periodo',
    valueField:'id_periodo',
    allowBlank: false,
    width:  200,
    selectOnFocus:true,
    triggerAction: 'all'
});
periodosTopSupFiltro.on('select', filtrarGrillaTopsSup);

ddpSupTopsDataStore = new Ext.data.Store({
    id: 'ddpSupTopsDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
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
    {name: 'usuario',       type: 'string',     mapping: 'usuario'},
    {name: 'puesto',        type: 'string',     mapping: 'puesto'},
    {name: 'estado',        type: 'string',     mapping: 'estado'},
    {name: 's_pesos',       type: 'float',      mapping: 's_pesos'},
    {name: 'q_obj',         type: 'int',        mapping: 'q_obj'},
    {name: 'q_obj_sup',     type: 'int',        mapping: 'q_obj_sup'},
    {name: 'q_obj_aprob',   type: 'int',        mapping: 'q_obj_aprob'},
    {name: 'habilitado',    type: 'int',        mapping: 'habilitado'},
    {name: 's_pesoreal',    type: 'float',      mapping: 's_pesoreal'},
    ]),
    sortInfo:{field: 'estado', direction: "ASC"},    
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(ddpSupTopsDataStore);

botonesAction = new Ext.grid.ActionColumn({
//    width: 15,
    editable:false,
    menuDisabled:true,
    header:'Acción',
    hideable:false,
    align:'center',
    width:  80,
//    tooltip:'Ver TOP',
    hidden:false,
    items:[{
            icon:URL_BASE+'images/tooloptions.png',
            iconCls :'col_accion',
            tooltip:'Ver TOP',
            getClass: showBtnVerTopDr,
            handler: clickBtnVerTopDDpTopSup
        }
        ,{
            icon:URL_BASE+'images/edit-find.png',
            iconCls :'col_accion',
            tooltip:'Auditoria',
            handler: clickBtnVerHistorialDdpTopSup
        }
        ,{
            icon:URL_BASE+'images/doc_pdf.png',
            iconCls :'col_accion',
//                    getClass: showBtnModificar,
            tooltip:'Descargar',
            handler: clickBtnDescargarPDFTopsSup
        }
    ]
});

ddpSupTopshabilitadaCheck = new Ext.grid.CheckColumn({
    id:'habilitado',
    header: "Habilitado",
    dataIndex: 'habilitado',
    width: 60,
    sortable: true,
    menuDisabled:true,
    pintar_deshabilitado:true,
    disabled: false, //-->NO FUNCIONA
    tabla: 'ddp_tops',
    align:'center',
    campo_id: 'id_top'
});

ddpSupTopsColumnModel = new Ext.grid.ColumnModel(
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
            header: 'Usuario',
            dataIndex: 'usuario',
            width: 250,
            sortable: true
        }
        ,{
            header: 'Periodo',
            dataIndex: 'periodo',
            width: 80,
            sortable: true,
            renderer: showQtipTopSup
        }
        ,{
            header: 'Puesto',
            dataIndex: 'puesto',
            width: 200,
            sortable: false
        },{
            header: 'Estado',
            dataIndex: 'estado',
            width: 110,
            renderer: showEstadoDdpTopSup,
            align:'center',
            sortable: true
        },{
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
            header: '&Sigma; <b>Peso Real</b>',
            tooltip:'Cantidad de Peso real',
            dataIndex: 's_pesoreal',
            width: 70,
            align:'center',
            summaryType: 'sum',
            sortable: false
        }
//        {
//        header: '&Sigma; <b>Obj sup</b>',
//        tooltip:'Cantidad de Objetivos para supervisar',
//        dataIndex: 'q_obj_sup',
//        width: 70,
//        align:'center',
//        summaryType: 'sum',
//        sortable: false
//        },
        /*ddpSupTopshabilitadaCheck,*/
        ,botonesAction
    ]
);
//ddpbuscadorTOPs= new Ext.ux.grid.Search({
//    iconCls:'icon-zoom',
//    //    readonlyIndexes:['id_convocatoria'],
//    disableIndexes:['id_top','habilitado'],
//    align:'left',
//    minChars:3
//});
  
   ddpSupTopsGrid =  new Ext.grid.GridPanel({
        id: 'ddpSupTopsGrid',
        title: 'Tops generadas por su personal',
        store: ddpSupTopsDataStore,
        cm: ddpSupTopsColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[/*ddpbuscadorTOPs,*/ddpSupTopshabilitadaCheck],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
//        tbar: ['<b>Filtrar por-></b> Período',periodosTopSupFiltro]
        tbar: []
    }); 

  ddpSupTopsDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

//var altura=Ext.getBody().getSize().height - 60;
//ddpSupTopsGrid.setHeight(altura);
//
//Ext.getCmp('browser').on('resize',function(comp){
//        ddpSupTopsGrid.setWidth(this.getSize().width);
//        ddpSupTopsGrid.setHeight(Ext.getBody().getSize().height - 60);
//
//});

function showQtipTopSup(value, metaData,record){
    var deviceDetail = record.get('operario');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function showQtipSupervisores(value, metaData,record){
    var deviceDetail = record.get('supervisores');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}
function clickBtnVerTopDDpTopSup(grid,rowIndex,colIndex,item ,event){
    var id=grid.getStore().getAt(rowIndex).json.id_top;
//    console.log(id);
    Ext.get('browser').load({
        url: CARPETA+"/top_usuario/31",
        params: {id: id},
        scripts: true,
        text: "Cargando..."
    });
}
function filtrarGrillaTopsSup (combo, record, index){
    ddpSupTopsDataStore.load({
            params: {
                    filtro_id_periodo: Ext.getCmp('periodosTopSupFiltro').getValue()
            }
    });	
};
function showBtnVerTopDr(value,metaData,record){
    var a=record.json.id_top;
    if(a=="" || a==0 || a=='0' || a==null)
    {
        return 'x-hide-display';  
    }            
    else
        return 'x-grid-center-icon';                
};
function showEstadoDdpTopSup(value,metaData,record){
    var e=record.json.id_estado;
//    console.log(e);
    switch(e){
            case '1':
            case 1:
                metaData.attr = 'style="color:#000;background-color:#FFF933;"';
                break;
            case '2':
            case 2:
                metaData.attr = 'style="color:#FFF;background-color:#E46C0A;"';
                break;
            case '3':
            case 3:
                metaData.attr = 'style="color:#FFF;background-color:#00B0F0;"';
              break;
            case '4':
            case 4:
                metaData.attr = 'style="color:#FFF;background-color:#9BBB59;"';
              break;
            default:
              break;
            }        
        return value;       
};

function clickBtnDescargarPDFTopsSup (grid,rowIndex,colIndex,item ,event){
    var id_top=grid.getStore().getAt(rowIndex).json.id_top;
    var link = document.createElement("a");
    link.download = "excel";
    link.href =CARPETA_PDF+'/miTop/'+id_top ;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    delete link;
};