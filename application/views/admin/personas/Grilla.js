PersonasDataStore = new Ext.data.Store({
    id: 'PersonasDataStore',
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
    {name: 'id_persona',    type: 'int',    mapping: 'id_persona'},        
    {name: 'nombre',        type: 'string', mapping: 'nombre'},
    {name: 'apellido',      type: 'string', mapping: 'apellido'},
    {name: 'documento',     type: 'string', mapping: 'documento'},
    {name: 'td',            type: 'string', mapping: 'td'},
//    {name: 'usuario',       type: 'string', mapping: 'usuario'},
    {name: 'genero',       type: 'string', mapping: 'genero'},
    {name: 'habilitado',    type: 'bool', mapping: 'habilitado'}
    ]),
    sortInfo:{field: 'id_persona', direction: "ASC"},
    remoteSort: true
});
//asigno el datastore al paginador
paginador.bindStore(PersonasDataStore);
tdGrillaDS = new Ext.data.Store({
        id: 'tdDS',
        proxy: new Ext.data.HttpProxy({
        url: LINK_GENERICO+'/tipos_documentos', 
        method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
            }, [
            {name: 'id_td', type: 'int'},        
            {name: 'td', type: 'string'},
        ])
});
tdGrillaCombo = new Ext.form.ComboBox({
            id:'tdGrillaCombo',
            forceSelection : false,
            fieldLabel: 'Tipo Documento',
            store: tdGrillaDS,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'td',
            valueField: 'id_td',
            anchor:'95%',
            triggerAction: 'all',
            width: 300,
            tabIndex: 3
});
habilitadaCheck = new Ext.grid.CheckColumn({
        id:'habilitado',
        header: "Habilitada",
        dataIndex: 'habilitado',
        width: 60,
        sortable: true,
        menuDisabled:true,
        pintar_deshabilitado:true,
         disabled: false, //-->NO FUNCIONA
        tabla: 'grl_personas',
        align:'center',
        campo_id: 'id_persona'
    });

PersonasColumnModel = new Ext.grid.ColumnModel(
    [{
        header: '#',
        readOnly: true,
        dataIndex: 'id_persona',
        width: 40,        
        sortable: true,
        renderer: function(value, cell){
				cell.css = "readonlycell";
         		return value;
        },
        hidden: false
      },{
        header: 'Nombre',
        dataIndex: 'nombre',
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
        header: 'Apellido',
        dataIndex: 'apellido',
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
        header: 'Tipo Documento',
        dataIndex: 'td',
        width: 100,
        sortable: true,
        editor: tdGrillaCombo,
        renderer: function(value, cell){ 
         cell.css = "coolcell";
         return value;
        }
      },{
        header: 'Documento',
        dataIndex: 'documento',
        width:  130,
        sortable: true,
        readOnly: permiso_modificar,
        editor: new Ext.form.TextField({
		  disabled: !permiso_modificar,
		  allowBlank: false,
          maxLength: 130
          }),
        renderer: function(value, cell){ 
            cell.css = "coolcell";
            return value;
        }
      },{
        header: 'Genero',
        dataIndex: 'genero',
        width:  130,
        sortable: true,
        readOnly: permiso_modificar,
        renderer: showGenero,
      }
//      ,{
//        header: 'Usuario',
//        dataIndex: 'usuario',
//        width:  100,
//        sortable: true,
//        readOnly: permiso_modificar,
//        renderer: showUsr
//      }
      ,habilitadaCheck]
    );
    buscadorPersona= new Ext.ux.grid.Search({
    iconCls:'icon-zoom',
//    readonlyIndexes:['id_convocatoria'],
    disableIndexes:['id_persona','habilitado','genero'],
    align:'left',
    minChars:3
});
  
   PersonasListingEditorGrid =  new Ext.grid.EditorGridPanel({
        id: 'PermisosListingEditorGrid',
        title: 'Listado de Personas',
        store: PersonasDataStore,
        cm: PersonasColumnModel,
        enableColLock:false,
        trackMouseOver:true, //que se coloree cuando pasas el mouse por encima
        loadMask: true, //que te ponga el loading cuando se esta generando el store
        renderTo: 'grillita',
        viewConfig: {
            forceFit: false
        },
        plugins:[buscadorPersona,habilitadaCheck],
        clicksToEdit:3,
        height:500,
        layout: 'fit',
        selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
        bbar: paginador,
        tbar: [
          {
            text: 'Nueva Persona',
            tooltip: 'Crear un nuevo persona...',
            iconCls:'add',                      // reference to our css
            handler: displayFormWindow,
			hidden: !permiso_alta
          }
      ]
    });   

  PersonasDataStore.load({params: {start: 0, limit: TAM_PAGINA}});

  PersonasListingEditorGrid.on('afteredit', guardarPersona);
  
  
   // guarda los cambios en los datos del persona luego de la edicion
  function guardarPersona(oGrid_event)
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
		 id     : oGrid_event.record.data.id_persona,     
		 campo  : encoded_array_f,
		 valor  : encoded_array_v
      }, 
      success: function(response){              
         var result=eval(response.responseText);
         switch(result){
         case 0:
            PersonasDataStore.commitChanges();
            PersonasDataStore.reload();
            break;
         case 1:
           Ext.MessageBox.alert('Error','Error al insertar el registro');
            PersonasDataStore.reload();
            break;
         case 2:
            Ext.MessageBox.alert('Error','El Nro de documento ingresado ya existe...');
            PersonasDataStore.reload();
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
	PersonasListingEditorGrid.setHeight(altura);
	
	Ext.getCmp('browser').on('resize',function(comp){
		PersonasListingEditorGrid.setWidth(this.getSize().width);
		PersonasListingEditorGrid.setHeight(Ext.getBody().getSize().height - 60);

	});
        
function showUsr (value,metaData,superData){
    if (value=='')
    {
        metaData.attr = 'style="background-color:#6E6E6E; color:#FFF;"';
        value='Sin usuario'
    }
    return value;
}

function showGenero (value,metaData,superData){
    if (value=='F')
        value='Femenino'
    else
        value='Masculino'
    return value;
}