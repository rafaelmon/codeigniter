areasDataStore = new Ext.data.Store({
    id: 'areasDataStore',
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
    {name: 'id_area',       type: 'int',    mapping: 'id_area'},        
    {name: 'area',          type: 'string', mapping: 'area'},
    {name: 'empresa',       type: 'string', mapping: 'empresa'},
    {name: 'area_padre',    type: 'string', mapping: 'area_padre'},
    {name: 'habilitado',    type: 'string', mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'area', direction: "ASC"},
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(areasDataStore);

habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitado',
        header: "Habilitada",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
         disabled: false, //-->NO FUNCIONA
        tabla: 'gr_areas_rpyc',
        align:'center',
        campo_id: 'id_area'
    });
 empresasDSGrilla= new Ext.data.Store({
        id: 'empresasDS',
        proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/empresas_combo', 
        method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
            }, [
            {name: 'id_empresa', type: 'int'},        
            {name: 'empresa', type: 'string'},
        ])
    });
    empresasComboGrilla = new Ext.form.ComboBox({
            id:'empresasCombo',
            forceSelection : false,
            fieldLabel: 'Seleccion la Empresa a la que pertenece la Area',
            store: empresasDSGrilla,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'empresa',
            valueField: 'id_empresa',
            anchor:'95%',
            triggerAction: 'all',
            width: 300,
            tabIndex: 3
});
areasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_area',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Area',
        dataIndex: 'area',
        width: 200,
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
        header: 'Empresa',
        dataIndex: 'empresa',
        width: 160,
        sortable: true,
        editor: empresasComboGrilla,
        renderer: function(value, cell){ 
            cell.css = "coolcell";
            return value;
        }
      },{
        header: 'Area superior',
        dataIndex: 'area_padre',
        width: 200,
        sortable: true,
//        editor: new Ext.form.TextField({
//            disabled: !permiso_modificar,
//            allowBlank: false,
//            maxLength:4,
//            minLength :2,
//            maxLengthText:'M&aacute;ximo 4 caracteres',
//            minLengthText:'M&iacute;nimo 2 caracteres'
//          }),
        renderer: function(value, cell){ 
         cell.css = "coolcell";
         return value;
        }
      },habilitadaCheck]
    );
    buscadorArea= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_convocatoria'],
    disableIndexes:['id_area','habilitado','empresa'],
    align:'left',
    minChars:3
});
  
   areasListingEditorGrid =  new Ext.grid.EditorGridPanel({
        id: 'areasListingEditorGrid',
        title: 'Listado de Areas por Empresa',
        store: areasDataStore,
        cm: areasColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[buscadorArea,habilitadaCheck],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
        tbar: [
            /*
          {
            text: 'Nueva Area',
            tooltip: 'Crear una nueva area...',
            iconCls:'add',                      // reference to our css
            handler: displayFormWindow,
			hidden: !permiso_alta
          }
          */
      ]
    });   

  areasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

  areasListingEditorGrid.on('afteredit', guardarArea);
  
  
   // guarda los cambios en los datos del area luego de la edicion
  function guardarArea(oGrid_event)
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
		 id     : oGrid_event.record.data.id_area,     
		 campo  : encoded_array_f,
		 valor  : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            areasDataStore.commitChanges();
            areasDataStore.reload();
            break;
         case 10:
            Ext.MessageBox.alert('Error','Area ya existente...');
            areasDataStore.reload();
            break;  
         case 11:
            Ext.MessageBox.alert('Error','Ya existe una área con el mismo nombre en la empresa especificada...');
            areasDataStore.reload();
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
	areasListingEditorGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		areasListingEditorGrid.setWidth(this.getSize().width);
		areasListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

	});