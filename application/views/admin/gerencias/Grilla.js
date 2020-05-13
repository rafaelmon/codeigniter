GerenciasDataStore = new Ext.data.Store({
    id: 'GerenciasDataStore',
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
    {name: 'id_gerencia',   type: 'int',    mapping: 'id_gerencia'},        
    {name: 'gerencia',      type: 'string', mapping: 'gerencia'},
    {name: 'empresa',       type: 'string', mapping: 'empresa'},
    {name: 'abv',           type: 'string', mapping: 'abv'},
    {name: 'habilitado',    type: 'string', mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'id_gerencia', direction: "ASC"},
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(GerenciasDataStore);

habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitado',
        header: "Habilitada",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
         disabled: false, //-->NO FUNCIONA
        tabla: 'grl_gerencias',
        align:'center',
        campo_id: 'id_gerencia'
    });
 empresasDSGrilla = new Ext.data.Store({
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
            fieldLabel: 'Seleccion la Empresa a la que pertenece la Gerencia',
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
GerenciasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_gerencia',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Gerencia',
        dataIndex: 'gerencia',
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
        header: 'Empresa',
        dataIndex: 'empresa',
        width: 130,
        sortable: true,
        editor: empresasComboGrilla,
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
    buscadorGerencia= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_convocatoria'],
    disableIndexes:['id_gerencia','habilitado'],
    align:'left',
    minChars:3
});
  
   GerenciasListingEditorGrid =  new Ext.grid.EditorGridPanel({
        id: 'PermisosListingEditorGrid',
        title: 'Listado de Gerencias por Empresa',
        store: GerenciasDataStore,
        cm: GerenciasColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[buscadorGerencia,habilitadaCheck],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
        tbar: [
          {
            text: 'Nueva Gerencia',
            tooltip: 'Crear una nueva gerencia...',
            iconCls:'add',                      // reference to our css
            handler: displayFormWindow,
			hidden: !permiso_alta
          }
      ]
    });   

  GerenciasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

  GerenciasListingEditorGrid.on('afteredit', guardarGerencia);
  
  
   // guarda los cambios en los datos del gerencia luego de la edicion
  function guardarGerencia(oGrid_event)
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
		 id     : oGrid_event.record.data.id_gerencia,     
		 campo  : encoded_array_f,
		 valor  : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            GerenciasDataStore.commitChanges();
            GerenciasDataStore.reload();
            break;
         case 10:
            Ext.MessageBox.alert('Error','Gerencia existente...');
            GerenciasDataStore.reload();
            break;  
         case 11:
            Ext.MessageBox.alert('Error','Email existente...');
            GerenciasDataStore.reload();
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
	GerenciasListingEditorGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		GerenciasListingEditorGrid.setWidth(this.getSize().width);
		GerenciasListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

	});