//Combo Filtro Modulos Padres

array_modulos_padres = new Ext.data.JsonStore({
	url: CARPETA+'/padres',
	root: 'data',
//	baseParams:{tarea: ""},
	fields: ['id_padre', 'padre']
});
array_modulos_padres.load();
	
array_modulos_padres.on('load' , function(  js , records, options ){
											   
	var tRecord = Ext.data.Record.create(
		{name: 'id_padre', type: 'int'},
		{name: 'padre', type: 'string'}
	);
	var myNewT = new tRecord({
		id_padre: -1,
		padre   : 'Todos'
	});
	array_modulos_padres.insert( 0, myNewT);	
} );

var padreFiltro = new Ext.form.ComboBox({
			 id:'padreFiltro',
			 forceSelection : true,
			 value: 'Todos',
			 store: array_modulos_padres,
			 editable : false,
			 displayField: 'padre',
			 valueField:'id_padre',
			 allowBlank: false,
			 selectOnFocus:true,
			 triggerAction: 'all'
			})	
			
comboPadreData = new Ext.data.Store({
	id: 'comboPadreData',
	proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/padres', 
                method: 'POST'
            }),
    reader: new Ext.data.JsonReader({
        root: 'data',
        totalProperty: 'num'
      }, [
      	{name: 'id_padre', type: 'int'},        
        {name: 'padre', type: 'string'},
      ])
});

comboPadreData.on('load' , function(  js , records, options ){
											   
	var tRecord = Ext.data.Record.create(
		{name: 'id_padre', type: 'int'},
		{name: 'padre', type: 'string'}
	);
	var myNewT = new tRecord({
		id_padre: 0,
		padre: 'No tiene padre'
	});
	comboPadreData.insert( 0, myNewT);	
} );
		
ModulosDataStore = new Ext.data.Store({
      id: 'ModulosDataStore',
      proxy: new Ext.data.HttpProxy({
                url: CARPETA+'/listado', 
                method: 'POST'
            }),
//      baseParams:{tampagina: TAM_PAGINA}, // this parameter is passed for any HTTP request
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_modulo'
      },[ 
        {name: 'id_modulo',     type: 'int',        mapping: 'id_modulo'},        
        {name: 'modulo',        type: 'string',     mapping: 'modulo'},
        {name: 'accion',        type: 'string',     mapping: 'accion'},
        {name: 'icono',         type: 'string',     mapping: 'icono'},
        {name: 'padre',         type: 'string',     mapping: 'padre'},
        {name: 'orden',         type: 'int',        mapping: 'orden'},
        {name: 'hijos',         type: 'boolean',    mapping: 'hijos'},
        {name: 'menu',          type: 'boolean',    mapping: 'menu'},
        {name: 'habilitado',    type: 'boolean',    mapping: 'habilitado'}
      ]),
      sortInfo:{field: 'id_modulo', direction: "ASC"}
    });
    
 hijosCheck = new Ext.grid.CheckColumn({
        id:'hijoscheck',
        header: "Hijos",
        dataIndex: 'hijos',
        width: 90,
        sortable: false,
        menuDisabled:true,
        disabled: true,
        tabla: 'sys_modulos_backend',
        campo_id: 'id_modulo'
    });
 habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitadocheck',
        header: "Habilitado",
        dataIndex: 'habilitado',
        width: 90,
        pintar_deshabilitado:true,
        sortable: false,
        menuDisabled:true,
        disabled: true,
        tabla: 'sys_modulos_backend',
        campo_id: 'id_modulo'
    });
    
    
  buscadorModulo= new Ext.ux.grid.Search({
        iconCls:'icon-zoom',
    //    readonlyIndexes:['id_'],
        disableIndexes:['id_modulo','padre','orden','hijos','menu','habilitado'],
        align:'left',
        minChars:3
    });
    
  menuCheck = new Ext.grid.CheckColumn({
        id:'menu',
        header: "Visible en Menu",
        dataIndex: 'menu',
        width: 90,
        sortable: false,
        menuDisabled:true,
        disabled: !permiso_modificar,
        tabla: 'sys_modulos_backend',
        campo_id: 'id_modulo'
    });
        	
 ModulosColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_modulo',
        width: 20,        
        renderer: function(value, cell){
            cell.css = "readonlycell";
            return value;		
        },
        hidden: false
      },{
        header: 'M&oacute;dulo',
        dataIndex: 'modulo',
        width:  120,
        readOnly: !permiso_modificar,
        editor: new Ext.form.TextField({
            disabled: !permiso_modificar,
            allowBlank: false,
            maxLength: 50,
            maskRe: /([a-zA-Z \u00f1\u00d1\u00e1\u00e9\u00ed\u00f3\u00fa\u00c1\u00c9\u00cd\u00d3\u00da\u00c7\u00e7\u00dc\u00fc 0-9\s]+)$/
          }),
        renderer: function(value, cell){ 
         cell.css = "coolcell";
         return value;
        }
      },{
        header: 'Acci&oacute;n',
        dataIndex: 'accion',
        width:  140,
        readOnly: !permiso_modificar,
        editor: new Ext.form.TextField({
            disabled: !permiso_modificar,
            allowBlank: true,
            maxLength: 50,
            maskRe: /([a-z._@A-Z0-9/\s]+)$/
          }),
        renderer: function(value, cell){ 
            cell.css = "coolcell";
            return value;
        }
      },
      {
        header: 'Icono',
        dataIndex: 'icono',
        width:  100,
        readOnly: !permiso_modificar,
        editor: new Ext.form.TextField({
            disabled: !permiso_modificar,
            allowBlank: true,
            maxLength: 50,
            maskRe: /([a-z._@A-Z0-9\s]+)$/
          }),
        renderer: function(value, cell){ 
            cell.css = "coolcell";
            return value;
        }
      },
      {
      header: 'Padre',
      dataIndex: 'padre',
      readOnly: !permiso_modificar,
      width: 200,
      editor: new Ext.grid.GridEditor(new Ext.form.ComboBox({
      		disabled: !permiso_modificar,
            id:'cmpadres',
            listWidth : 250,
            hiddenName: 'cmpid',
            store: comboPadreData,
            displayField:'padre',
            valueField:'id_padre',
            allowBlank: true,
            typeAhead: true,
            mode: 'remote',
            triggerAction: 'all'
            })
            )
      },
      {
        header: 'Orden',
        dataIndex: 'orden',
        width: 90,
        editor: new Ext.form.TextField({
		  disabled: !permiso_modificar,
		  allowBlank: false,
          maxLength: 2
          }),
        renderer: function(value, cell){ 
         cell.css = "coolcell";
          return value;
        }
      },hijosCheck,menuCheck,habilitadaCheck
      ]
    );
 ModulosColumnModel.defaultSortable= true;

 //asigno el datastore al paginador
paginador.bindStore(ModulosDataStore);

 ModulosListingEditorGrid =  new Ext.grid.EditorGridPanel({
    id: 'ModulosListingEditorGrid',
    title: 'Modulos',
    store: ModulosDataStore,
    cm: ModulosColumnModel,
    enableColLock:false,
    trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
    loadMask: true, //que te ponga el loading cuando se esta generando el store
    renderTo: 'grillita',
    viewConfig: {
        forceFit: false
    },
    plugins:[habilitadaCheck,menuCheck,hijosCheck,buscadorModulo],
    clicksToEdit:2,
    height:500,
    layout: 'fit',
    selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
    bbar: paginador,
      tbar: [
          {
            text: 'Nuevo Modulo',
            tooltip: 'Crear un nuevo modulo...',
            iconCls:'add',                      // reference to our css
            handler: displayFormWindow,
            hidden: !permiso_alta
          }, '-', { 
            text: 'Eliminar',
            tooltip: 'Eliminar el modulo seleccionado',
            handler: confirmDeleteModulos,   // Confirm before deleting
            iconCls:'remove',
			hidden: !permiso_eliminar
          },'-','Filtrar por Padre ',padreFiltro
		  
      ]
    });
     
   ModulosDataStore.load({params: {start: 0, limit: TAM_PAGINA}});
   
   ModulosListingEditorGrid.on('afteredit', guardarModulo);
   
   // guarda los cambios en los datos del modulo luego de la edicion
  function guardarModulo(oGrid_event){
        var fields = [];
        fields.push('modulo');
        fields.push('accion');
        fields.push('icono');
        fields.push('padre_id');
        fields.push('orden');
        var values = [];
        values.push(oGrid_event.record.data.modulo);
        values.push(oGrid_event.record.data.accion);
        values.push(oGrid_event.record.data.icono);
        values.push(oGrid_event.record.data.padre);
        values.push(oGrid_event.record.data.orden);
        var encoded_array_f = Ext.encode(fields);
        var encoded_array_v = Ext.encode(values);
		 
   Ext.Ajax.request({   
      waitMsg: 'Por favor espere...',
      url: CARPETA+'/modificar',
      params: {
		 id_modulo: oGrid_event.record.data.id_modulo,     
		 campos : encoded_array_f,
		 valores : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 1:
            ModulosDataStore.commitChanges();
            array_modulos_padres.reload();
            comboPadreData.reload();
            ModulosDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
            ModulosDataStore.commitChanges();
            ModulosDataStore.reload();
            break;          
         default:
            Ext.MessageBox.alert('Error','No hay conexión con la base de datos. Asegurese de tener conexion');
            break;
         }
      },
      failure: function(response){
         var result=response.responseText;
         Ext.MessageBox.alert('Uh uh...','No hay conexión con la base de datos. Intenta otra vez');    
      }                      
   });   
  }
  
  function confirmDeleteModulos(){
    if(ModulosListingEditorGrid.selModel.getCount() == 1) // only one president is selected here
    {
      Ext.MessageBox.confirm('Confirmation','Est&aacute; por borrar un modulo. Desea continuar?', deleteModulos);
    } else if(ModulosListingEditorGrid.selModel.getCount() > 1){
      Ext.MessageBox.confirm('Confirmation','Desea borrar estos modulos?', deleteModulos);
    } else {
      Ext.MessageBox.alert('Uh oh...','Para borrar un modulo debe seleccionar alguno del listado');
    }
  }  
  
  function deleteModulos(btn){
    if(btn=='yes'){
         var selections = ModulosListingEditorGrid.selModel.getSelections();
         var prez = [];
         for(i = 0; i< ModulosListingEditorGrid.selModel.getCount(); i++){
          prez.push(selections[i].json.id_modulo);
		 
         }
         var encoded_array = Ext.encode(prez);
		  //alert(encoded_array);
         Ext.Ajax.request({  
            waitMsg: 'Por favor espere',
            url: CARPETA+'/borrar', 
            params: { 
               ids:  encoded_array
              }, 
            success: function(response){
              var result=eval(response.responseText);
              switch(result){
              case 1:  // Success : simply reload
              	array_modulos_padres.reload();
            	comboPadreData.reload();
                ModulosDataStore.reload();
                break;
              case 2:  // Success : simply reload
                Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
                break;
              case 3:  // Success : simply reload
                Ext.MessageBox.alert('Error','Primero debe borrar los m&oacute;dulos hijos.');
                break;
              default:
                Ext.MessageBox.alert('Warning','No se pudo eliminar el registro seleccionado.');
                break;
              }
            },
            failure: function(response){
              var result=response.responseText;
              Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
              }
         });
      }  
  }
  
  //recargo la grilla cuando el combo del filtro sea seleccionado
  padreFiltro.on('select', function( combo, record, index ){
		ModulosDataStore.load({
			params: {
				filtro_padre_id: this.getValue()
			}
		});	
	});
  
  var altura=Ext.getBody().getSize().height - 60;
	ModulosListingEditorGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		
		ModulosListingEditorGrid.setWidth(this.getSize().width);
		
		ModulosListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

	});
	
	ModulosListingEditorGrid.on('cellclick',function(grid, rowIndex, columnIndex, e){
		if(columnIndex==5){
			
			console.log(rowIndex);
		}
	});