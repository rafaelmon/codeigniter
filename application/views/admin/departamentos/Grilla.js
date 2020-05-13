DepartamentosDataStore = new Ext.data.Store({
    id: 'DepartamentosDataStore',
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
    {name: 'id_departamento',   type: 'int',      mapping: 'id_departamento'},        
    {name: 'departamento',      type: 'string',   mapping: 'departamento'},
    {name: 'empresa',     type: 'string',   mapping: 'empresa'},
    {name: 'gerencia',    type: 'string',   mapping: 'gerencia'},
    {name: 'id_gerencia', type: 'int',      mapping: 'id_gerencia'},
    {name: 'abv',         type: 'string',   mapping: 'abv'},
    {name: 'habilitado',  type: 'string',   mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'id_departamento', direction: "ASC"},
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(DepartamentosDataStore);

botonesAction = new Ext.grid.ActionColumn({
		width: 15,
                editable:false,
                menuDisabled:true,
                header:'Acción',
                hideable:false,
                align:'center',
                width:  50,
                tooltip:'Modificar',
                 hidden:false,
		items:[{
                    icon:URL_BASE+'images/tooloptions.png',
                    iconCls :'col_accion',
                    tooltip:'Editar Departamento',
                    handler: clickBtnModificaDepartamento
                }]
});

habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitado',
        header: "Habilitado",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
         disabled: false, //-->NO FUNCIONA
        tabla: 'grl_departamentos',
        align:'center',
        campo_id: 'id_departamento'
    });
 empresasDSGrilla = new Ext.data.Store({
        id: 'empresasDSGrilla',
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
    empresaComboGrilla = new Ext.form.ComboBox({
            id:'empresaComboGrilla',
            forceSelection : false,
            fieldLabel: 'Seleccion la Empresa a la que pertenece el Departamento',
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
 gerenciasDSGrilla = new Ext.data.Store({
        id: 'gerenciasDSGrilla',
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
//    gerenciaComboGrilla = new Ext.form.ComboBox({
//            id:'gerenciaComboGrilla',
//            forceSelection : false,
//            fieldLabel: 'Seleccion la Gerencia a la que pertenece el Departamento',
//            store: gerenciasDSGrilla,
//            editable : false,
//            allowBlank: false,
//            blankText:'campo requerido',
//            displayField: 'gerencia',
//            valueField: 'id_gerencia',
//            anchor:'95%',
//            triggerAction: 'all',
//            width: 300,
//            tabIndex: 3
//    });
DepartamentosColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_departamento',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;
        },
        hidden: false
      },{
        header: 'Departamento',
        dataIndex: 'departamento',
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
//        editor: empresaComboGrilla,
        renderer: function(value, cell){ 
            cell.css = "coolcell";
            return value;
        }
      },{
        header: 'Gerencia',
        dataIndex: 'gerencia',
        width: 130,
        sortable: true,
//        editor: gerenciaComboGrilla,
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
      },habilitadaCheck,botonesAction]
    );
    buscadorDepartamento= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_convocatoria'],
    disableIndexes:['id_departamento','habilitado'],
    align:'left',
    minChars:3
});
  
   DepartamentosListingEditorGrid =  new Ext.grid.EditorGridPanel({
        id: 'DepartamentosListingEditorGrid',
        title: 'Listado de Departamentos por Gerencia y Empresa',
        store: DepartamentosDataStore,
        cm: DepartamentosColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[buscadorDepartamento,habilitadaCheck],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
        tbar: [
          {
            text: 'Nuevo Departamento',
            tooltip: 'Crear un nuevo departamento...',
            iconCls:'add',                      // reference to our css
            handler: displayFormWindow,
            hidden: !permiso_alta
          }
      ]
    });   

  DepartamentosDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

  DepartamentosListingEditorGrid.on('afteredit', guardarDepartamento);
  
  
   // guarda los cambios en los datos del departamento luego de la edicion
  function guardarDepartamento(oGrid_event)
  {
      console.log(oGrid_event);
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
		 id         : oGrid_event.record.data.id_departamento, 
                 id_gerencia:oGrid_event.record.data.id_gerencia,
		 campo      : encoded_array_f,
		 valor      : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            DepartamentosDataStore.commitChanges();
            DepartamentosDataStore.reload();
            break;
         case -1:
            Ext.MessageBox.alert('Error','Departamento existente...');
            DepartamentosDataStore.reload();
            break;  
         
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
	DepartamentosListingEditorGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		DepartamentosListingEditorGrid.setWidth(this.getSize().width);
		DepartamentosListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

	});