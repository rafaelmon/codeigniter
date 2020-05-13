empresasDataStore = new Ext.data.Store({
    id: 'empresasDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
        }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
    root: 'rows',
    totalProperty: 'total',
    id: 'id_empresa'
    },[ 
    {name: 'id_empresa',    type: 'int',    mapping: 'id_empresa'},        
    {name: 'empresa',        type: 'string', mapping: 'empresa'},
    {name: 'abv',      type: 'string', mapping: 'abv'},
    {name: 'logo',      type: 'string', mapping: 'logo'},
    {name: 'habilitado',    type: 'string', mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'id_empresa', direction: "ASC"},
    remoteSort: true
});
//asigno el datastore al paginador
//paginador.bindStore(empresasDataStore);

empresashabilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitado',
        header: "Habilitada",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
         disabled: false, //-->NO FUNCIONA
        tabla: 'grl_empresas',
        align:'center',
        campo_id: 'id_empresa'
    });

EmpresasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_empresa',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Empresa',
        dataIndex: 'empresa',
        width: 130,
        sortable: true,/*
        editor: new Ext.form.TextField({
		  disabled: !permiso_modificar,
		  allowBlank: false,
          maxLength: 150
          }),*/
        renderer: function(value, cell){ 
         cell.css = "coolcell";
         return value;
        }
      },{
        header: 'Nombre abreviado',
        dataIndex: 'abv',
        width: 130,
        sortable: true,
        editor: new Ext.form.TextField({
            disabled: !permiso_modificar,
            allowBlank: false,
            maxLength:4,
            minLength :2,
            maxLengthText:'M&aacute;ximo 4 caracteres',
            minLengthText:'M&iacute;nimo 2 caracteres'
          }),
        renderer: function(value, cell){ 
         cell.css = "coolcell";
         return value;
        }
      },{
        header: 'Logo',
        dataIndex: 'logo',
        width: 200,
        sortable: false,
        renderer: showLogo
      },empresashabilitadaCheck]
    );
//    buscadorEmpresa= new Ext.ux.grid.Search({
//    iconCls:'icon-zoom',
////    readonlyIndexes:['id_convocatoria'],
//    disableIndexes:['id_empresa','habilitado','logo'],
//    align:'left',
//    minChars:3
//});
  
   EmpresasListingEditorGrid =  new Ext.grid.EditorGridPanel({
        id: 'PermisosListingEditorGrid',
        title: 'Listado de Empresas',
        store: empresasDataStore,
        cm: EmpresasColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[/*buscadorEmpresa,*/empresashabilitadaCheck],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false})
/*        bbar: paginador,
        tbar: [
          {
            text: 'Nueva Empresa',
            tooltip: 'Crear una nueva empresa...',
            iconCls:'add',                      // reference to our css
            handler: displayFormWindow,
            hidden: !permiso_alta
          }
      ]*/
    });   

  empresasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

  EmpresasListingEditorGrid.on('afteredit', guardarEmpresa);
  
  
   // guarda los cambios en los datos del empresa luego de la edicion
  function guardarEmpresa(oGrid_event)
  {
  	 //console.log(oGrid_event);
        var fields = [];
        fields.push(oGrid_event.field);
	var values = [];
       	values.push(oGrid_event.value);
 
	var encoded_array_f = Ext.encode(fields);
	var encoded_array_v = Ext.encode(values);
   Ext.Ajax.request({   
      waitMsg: 'Por favor espere...',
      url: CARPETA+'/modificar',
      params: {
		 id     : oGrid_event.record.data.id_empresa,     
		 campo  : encoded_array_f,
		 valor  : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            empresasDataStore.commitChanges();
            empresasDataStore.reload();
            break;
         case 10:
            Ext.MessageBox.alert('Error','Empresa existente...');
            empresasDataStore.reload();
            break;  
         case 11:
            Ext.MessageBox.alert('Error','Email existente...');
            empresasDataStore.reload();
            break;          
         default:
            Ext.MessageBox.alert('Uh uh...','No se pudo actualizar...');
            break;
         }
      },
      failure: function(response){
         var result=response.responseText;
         Ext.MessageBox.alert('error','No se pudo conectar a la Base de Datos. Intente mas tarde');    
      }                      
   });  
  }
  
 
 
   
  	var altura=Ext.getBody().getSize().height - 60;
	EmpresasListingEditorGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		EmpresasListingEditorGrid.setWidth(this.getSize().width);
		EmpresasListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

	});
function showLogo (value,metaData,row){
    var enlace="";
    if (value!="")
        enlace = "<img ext:qtip='Logo "+row.data.empresa+"' class='x-action-col-icon x-action-col-0  ' src='"+URL_DMS_IMAGES+value+"' alt=''>";
    else
        enlace = "<img ext:qtip='Sin Logo' class='x-action-col-icon x-action-col-0  ' src='"+URL_DMS_IMAGES+"NoLogo.png' alt=''>";
    return enlace;
    }