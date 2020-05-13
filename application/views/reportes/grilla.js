reportesDataStore = new Ext.data.Store({
    id: 'reportesDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
    }),
      baseParams:{
          limit: TAM_PAGINA
//          filtro_id_td: Ext.getCmp('tiposDocFiltro').getValue(),
//          filtro_id_gcia: Ext.getCmp('gerenciasFiltro').getValue()
            }, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_reporte'
      },[ 
        {name: 'id_reporte',  type: 'int',    mapping: 'id_reporte'},        
        {name: 'reporte',     type: 'string', mapping: 'reporte'},
        {name: 'fecha_alta',    type: 'string',     mapping: 'fecha_alta'},
        {name: 'nom_archivo',      type: 'string', mapping: 'nom_archivo'},
        {name: 'detalle',      type: 'string', mapping: 'detalle'},
        {name: 'habilitado',    type: 'string', mapping: 'habilitado'}
        
      ]),
      sortInfo:{field: 'id_reporte', direction: "desc"},
      remoteSort: true
    });

repoHabilitadaCheck = new Ext.grid.CheckColumn({
        id:'repo-habilitado',
        header: "Habilitado",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
        tabla: 'rep_reportes',
        align:'center',
        campo_id: 'id_reporte'
    });
//repoHabilitadaCheck.on('selectionchange', reloadDS);
 function reloadDS(){
        reportesDataStore.reload();
    };

botonesRepoAction = new Ext.grid.ActionColumn({
                editable:false,
                menuDisabled:true,
                header:'Acciones',
                hideable:false,
                align:'center',
                width:  100,
                tooltip:'',
                hidden:false,
		items:[{
                    icon:URL_BASE+'images/excel2.png',
                    iconCls :'col_accion',
                    getClass: showBtnDownoad,
                    tooltip:'Generar y descargar reporte',
                    handler: clickBtnGeneraRepo
                }]
});
  
reportesColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_reporte',
        width: 40,        
        sortable: false,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Reporte',
        dataIndex: 'reporte',
        width:  120,
        sortable: false
      },{
        header: 'Fecha alta',
        dataIndex: 'fecha_alta',
        sortable: false,
        width:  100,
//        fixed:true,
        readOnly: true,
        align:'center'
      },{
        header: 'Nombre Archivo',
        dataIndex: 'nom_archivo',
        width:  120,
        sortable: false
      },{
        header: 'Detallle',
        dataIndex: 'detalle',
        sortable: false,
        renderer :showDescQtip,
        width:  300
      },repoHabilitadaCheck,botonesRepoAction]
    );
  
reportesGrid =  new Ext.grid.GridPanel({
        id: 'reportesGrid',
//        title: 'Listado de Reportes reportes a la fecha',
        store: reportesDataStore,
        cm: reportesColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        viewConfig: {
            forceFit: false
        },
        plugins:[repoHabilitadaCheck],
        height:500,
        layout: 'fit',
        renderTo: 'grillita',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar:[],
      tbar: []
    });   
reportesDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

     

function showDocument (value,metaData,row){
    var enlace;
    if (value!="" && row.data.habilitado==1)
        enlace = "<a target='_blank' href='"+URL_BASE_SITIO+"uploads/smn/reportes/"+value+".dot'><img ext:qtip='Descargar' class='x-action-col-icon x-action-col-0  ' src='"+URL_BASE+"/images/document_word.png' alt=''></a>";
    else
        enlace = "";
    return enlace;
    }
function showDescQtip(value, metaData,record){
    var deviceDetail = record.get('detalle');
    metaData.attr += 'ext:qtip="'+ deviceDetail + '"';
    return value;
}

 function imprimeCNC(grid,rowIndex,colIndex,item ,event){
     var id=grid.getStore().getAt(rowIndex).json.id_reporte;
//     window.open(CARPETA+'/preview_c/'+id)
     var pbar3 = new Ext.ProgressBar({
        id:'pbar3',
        width:300,
        renderTo:'waite-win'
    });
     new Ext.Window({
         id:'waite-win',
        title: 'Reporte ',
        height: 400,
        width: 600,
        bodyCfg: {
            tag: 'iframe',
            src: CARPETA+'/imprime/'+id,
            style: 'border: 0 none'
        }
    }).show();
  };

 function clickBtnGeneraRepo(grid,rowIndex,colIndex,item ,event){
     var id=grid.getStore().getAt(rowIndex).json.id_reporte;
     var nom_archivo=grid.getStore().getAt(rowIndex).json.nom_archivo;
     window.open(CARPETA+'/download/'+id+"/"+nom_archivo)

  };
  function showBtnDownoad(value,metaData,record){
        var h=record.json.habilitado;
        if (h==1) {
            return 'x-grid-center-icon';                
        } else {
            return 'x-hide-display';  

        }
    }
 