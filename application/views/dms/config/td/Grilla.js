TiposDocsDataStore = new Ext.data.Store({
    id: 'TiposDocsDataStore',
    proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/listado', 
            method: 'POST'
        }),
    baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
    reader: new Ext.data.JsonReader({
    root: 'rows',
    totalProperty: 'total',
    id: 'id'
    },[ 
    {name: 'id_td',   type: 'int',    mapping: 'id_td'},        
    {name: 'td',      type: 'string', mapping: 'td'},
    {name: 'detalle',       type: 'string', mapping: 'detalle'},
    {name: 'abv',           type: 'string', mapping: 'abv'},
    {name: 'habilitado',    type: 'string', mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'td', direction: "ASC"},
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(TiposDocsDataStore);

habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitado',
        header: "Habilitado",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
         disabled: false, //-->NO FUNCIONA
        tabla: 'dms_tipos_documento',
        align:'center',
        campo_id: 'id_td'
    });
 
TiposDocsColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_td',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Tipo documento',
        dataIndex: 'td',
        width: 130,
        sortable: true,
        editor: new Ext.form.TextField({
            disabled: !permiso_modificar,
            allowBlank: false,
            maxLength: 150
          }),
        renderer: function(value, cell){ 
         cell.css = "coolcell";
         return value;
        }
      },{
        header: 'Detalle',
        dataIndex: 'detalle',
        width: 300,
        sortable: false,
        editor: new Ext.form.TextField({
            disabled: !permiso_modificar,
            allowBlank: true,
            maxLength: 512
        }),
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
      },habilitadaCheck]
    );
    buscadorTipoDoc= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_convocatoria'],
    disableIndexes:['id_td','habilitado'],
    align:'right',
    minChars:3
});
  
   TiposDocsListingEditorGrid =  new Ext.grid.EditorGridPanel({
        id: 'PermisosListingEditorGrid',
        title: 'Listado de Tipos de Documentos',
        store: TiposDocsDataStore,
        cm: TiposDocsColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[buscadorTipoDoc,habilitadaCheck],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: [paginador],
        tbar: [
          {
            text: 'Nuevo Tipo de Documento',
            tooltip: 'Crear un nuevo tipo de documento...',
            iconCls:'add',                      // reference to our css
            handler: displayFormWindow,
			hidden: !permiso_alta
          }
      ]
    });   

  TiposDocsDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

  TiposDocsListingEditorGrid.on('afteredit', guardarTipoDoc);
  
  
   // guarda los cambios en los datos del tiposDoc luego de la edicion
  function guardarTipoDoc(oGrid_event)
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
		 id     : oGrid_event.record.data.id_td,     
		 campo  : encoded_array_f,
		 valor  : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            TiposDocsDataStore.commitChanges();
            TiposDocsDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','El tipo de Documento ya existente...');
            TiposDocsDataStore.reload();
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
	TiposDocsListingEditorGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		TiposDocsListingEditorGrid.setWidth(this.getSize().width);
		TiposDocsListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

	});