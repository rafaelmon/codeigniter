archivosDropBoxDataStore = new Ext.data.Store({
    id: 'archivosDropBoxDataStore',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/archivos', 
        method: 'POST'
    }),
    baseParams:{limit: TAM_PAGINA}, 
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_gestion'
    },[ 
        {name: 'id_archivo',    type: 'int',    mapping: 'id_archivo'},
        {name: 'archivo',       type: 'string', mapping: 'archivo'},
        {name: 'size',       type: 'string', mapping: 'size'},
        {name: 'ext',       type: 'string', mapping: 'ext'}
    ]),
    sortInfo:{field: 'id_archivo', direction: "asc"},
    remoteSort : true
});

archivosDropBoxColumnModel = new Ext.grid.ColumnModel([
    {
        header: '#',
//        readOnly: true,
        dataIndex: 'id_archivo',
        width: 40,
        hidden: false,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        }
    },{
        header: '',
        dataIndex: 'ext',
        sortable: false,
        width:  25,
        fixed:true,
        readOnly: true,
        align:'right',
        renderer:showico
      },{
        header: 'Archivo',
        dataIndex: 'archivo',
        sortable: false,
        width:  450,
        fixed:true,
        readOnly: true,
        align:'left',
        renderer:showpointer
      },{
        header: 'Tamaño',
        dataIndex: 'size',
        sortable: false,
        width:  100,
        fixed:true,
        readOnly: true,
        align:'right'
      }
    ]);
    
archivosDropBoxGrid =  new Ext.grid.GridPanel({
    id: 'archivosDropBoxGrid',	  
    store: archivosDropBoxDataStore,
    cm: archivosDropBoxColumnModel,
    enableColLock:false,	 
    viewConfig: {
        forceFit: false,
        stripeRows: true
    },      
    autoScroll : true,	 
    bbar:[]
});    


contenidoDropBoxPanel = new Ext.Panel({
        collapsible: false,
        collapsed:false,
        split: true,
        title: 'Archivos',
        region: 'center',
        height: 300,
        minSize: 100,
        maxSize: 350,
        margins: '0 5 5 5',
        html:'<p>panel inferior</p>',
        layout: 'fit',
        items : [archivosDropBoxGrid]
});

function showico(value, metaData, record, rowIndex, colIndex, store){
  var ico="";
  switch (value)
  {
      case 'pdf':
        ico='<img src="'+URL_BASE+'images/file_pdf.png">';
        break;
      case 'xlsx':
      case 'xls':
        ico='<img src="'+URL_BASE+'images/file_excel.png">';
        break;
      case 'docx':
      case 'doc':
        ico='<img src="'+URL_BASE+'images/file_word.png">';
        break;
      case 'jpg':
      case 'gif':
        ico='<img src="'+URL_BASE+'images/file_image.png">';
        break;
      default:
        ico='<img src="'+URL_BASE+'images/file_default.gif">';
        break;
  }
  value=ico;
  return value;
};
function showpointer(value, metaData, record, rowIndex, colIndex, store){
  var pointer='<p class="x-action-col-icon">'+value+'</p>';
  return pointer;
};

archivosDropBoxGrid.on ('rowclick',function(grid,rowIndex){
    var id=grid.getStore();
    id=id.data.items[rowIndex].data.id_archivo;
     window.open(CARPETA_DOWNLOAD+'/dms_dbx/'+id)

});