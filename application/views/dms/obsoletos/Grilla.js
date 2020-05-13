tiposDocJS = new Ext.data.JsonStore({
	url: CARPETA+'/combo_td',
	root: 'rows',
	fields: ['id_td', 'td']
});
tiposDocJS.load();
tiposDocJS.on('load' , function(  js , records, options ){
	var tRecord = Ext.data.Record.create(
		{name: 'id_td', type: 'int'},
		{name: 'td', type: 'string'}
	);
	var myNewT = new tRecord({
		id_td: -1,
		td   : 'Todos'
	});
	tiposDocJS.insert( 0, myNewT);	
} );

tiposDocFiltro = new Ext.form.ComboBox({
    id:'TiposDocFiltro',
    forceSelection : true,
    value: 'Todos',
    store: tiposDocJS,
    editable : false,
    displayField: 'td',
    valueField:'id_td',
    allowBlank: false,
    selectOnFocus:true,
    triggerAction: 'all'
});	
			


obsoletosDataStore = new Ext.data.Store({
    id: 'obsoletosDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
    }),
//      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_documento'
      },[ 
        {name: 'id_documento',  type: 'int',    mapping: 'id_documento'},        
        {name: 'documento',     type: 'string', mapping: 'documento'},
        {name: 'detalle',       type: 'string', mapping: 'detalle'},
        {name: 'td',            type: 'string', mapping: 'td'},
        {name: 'archivo',       type: 'string', mapping: 'archivo'},
        {name: 'codigo',        type: 'string', mapping: 'codigo'},
        {name: 'f_publicacion', type: 'string', mapping: 'f_publicacion'},
        {name: 'usuario',       type: 'string', mapping: 'usuario'},
        {name: 'f_obsoleto',    type: 'string', mapping: 'f_obsoleto'}
      ]),
      sortInfo:{field: 'f_obsoleto', direction: "desc"},
      remoteSort: true
    });

paginadorObsoletos= new Ext.PagingToolbar({
    pageSize: parseInt(TAM_PAGINA),
    store: obsoletosDataStore,
    displayInfo: true,
    beforePageText:'Página',
    afterPageText:'de {0}',
    displayMsg:'Mostrando {0} - {1} de <b>{2}</b>',
    firstText:'Primera Página',
    lastText:'Última Página',
    prevText:'Anterior',
    nextText:'Siguiente',
    refreshText:'Actualizar',
    buttonAlign:'left',
    emptyMsg:'No hay registros para listar'
});

buscador= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_documento', 'documento','descripcion'],
    disableIndexes:['id_documento','td','f_publicacion','archivo','f_obsoleto','usuario'],
    align:'left',
    minChars:3
});

botonesAccionesAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Acciones',
                hideable:false,
                align:'center',
                width:  50,
//                tooltip:'',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/obs_grabar.png',
//                    getClass: showBtnModificar,
                    tooltip:'Grabar observacion'
//                    handler: clickBtnCC
                },{
                    icon:URL_BASE+'images/preview1.png',
                    iconCls :'col_accion',
//                    getClass: showBtnModificar,
                    tooltip:'Previsualizar',
                    handler: clickBtnPreviewPub
                }]
});

  
obsoletosColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_documento',
        width: 10,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Documento',
        dataIndex: 'documento',
        width:  120,
        sortable: true,
        renderer: function(value, cell){ 
            cell.css = "coolcell";
            return value;
        }
      },{
        header: 'Fecha Publicación',
        dataIndex: 'f_publicacion',
        sortable: true,
        width:  100,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Fecha Obsoleto',
        dataIndex: 'f_obsoleto',
        sortable: true,
        width:  100,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Usuario Obsoleto',
        dataIndex: 'usuario',
        sortable: true,
        width:  160,
        fixed:true,
        readOnly: true,
        align:'center'
      },{
        name:'td',
        header: 'Tipo documento',
        dataIndex: 'td',
        sortable: true,
        width:  80
      },{
        name:'codigo',
        header: 'Código',
        dataIndex: 'codigo',
        sortable: true,
        width:  70,
        renderer :showBold
      },{
        name:'descripcion',
        header: 'Descripcion',
        dataIndex: 'detalle',
        sortable: false,
        width:  200,
        renderer :showQtip
      },botonesAccionesAction]
    );
  
obsoletosGrid =  new Ext.grid.GridPanel({
        id: 'obsoletosGrid',
        title: 'Listado de Documentos obsoletos a la fecha',
        store: obsoletosDataStore,
        cm: obsoletosColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
//        renderTo: 'grillita',
        viewConfig: {
            forceFit: true
        },
        plugins:[buscador],
        height:500,
        layout: 'fit',
        region:'center',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar:paginadorObsoletos,
      tbar: ['Filtrar por Tipo de Documento ',tiposDocFiltro]
    });   

obsoletosDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

//recargo la grilla cuando el combo del filtro sea seleccionado
tiposDocFiltro.on('select', function( combo, record, index ){
    obsoletosDataStore.load({
            params: {
                    filtro_id_td: this.getValue()
            }
    });	
});
   
  	var altura=Ext.getBody().getSize().height - 60;
	obsoletosGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		obsoletosGrid.setWidth(this.getSize().width);
		obsoletosGrid.setHeight(Ext.getBody().getSize().height - 60);

	});
        

function showDocument (value,metaData,row){
    var enlace;
    if (value!="" && row.data.habilitado==1)
        enlace = "<a target='_blank' href='"+URL_BASE_SITIO+"uploads/smn/documentos/"+value+".dot'><img ext:qtip='Descargar' class='x-action-col-icon x-action-col-0  ' src='"+URL_BASE+"/images/document_word.png' alt=''></a>";
    else
        enlace = "";
    return enlace;
    }
function showQtip(value, metaData,record){
    var deviceDetail = record.get('descripcion');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}

function showBold (value,metaData){
     metaData.attr ='style="font-weight:bold;"';
     return value;
}

 function clickBtnPreviewPub(grid,rowIndex,colIndex,item ,event){
     var id=grid.getStore().getAt(rowIndex).json.id_documento;
    var nom=grid.getStore().getAt(rowIndex).json.codigo;
     window.open(CARPETA+'/preview/'+id+"/"+nom)
     
//     new Ext.Window({
//        title: 'My PDF',
//        height: 400,
//        width: 600,
//        bodyCfg: {
//        tag: 'iframe',
//        src: CARPETA+'/preview'+id,
//        style: 'border: 0 none'
//        }
//    }).show();
  };