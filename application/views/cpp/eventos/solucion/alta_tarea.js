
function clickBtnNuevaTarea(grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnNuevaTarea(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevaTarea(grid,rowIndex)
{
    function createCppCausasTarea()
    { 
        if(iscppEventoTareaFormValid())
        {
           var record = grid.getStore().getAt(rowIndex);
           var id=record.data.id_causa;
           msgProcess('Guardando Tarea');
           Ext.Ajax.request({   
               waitMsg: 'Por favor espere...',
               url: CARPETA+'/insert_tarea',
               method: 'POST',
               params: {
                   id_causa     :id,
                   tarea        : cppEventoDescTareaField.getValue(),
                   responsable  : cppEventoResponsablesCombo.getValue(),
//                   rpd          : cppEventoTareaRevisionRadios.getValue().inputValue,
                   fecha        : cppEventoTareaFechaField.getValue()
               }, 
               success: function(response){              
                   var result=eval(response.responseText);
                   switch(result){
                           case 1:
                               Ext.MessageBox.alert('Alta OK','La Tarea fue creado satisfactoriamente.');
                               cppTareasDataStore.reload();
                               cppEventoTareaCreateWindow.close();
                               break;
                           case 3:
                               Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                               break;
                           case -1:
                               Ext.MessageBox.alert('Error','Verifique sus permisos');
                               break;
                           case -2:
                               Ext.MessageBox.alert('Error','Solo el usuario Observador puede dar de alta tareas');
                               break;
                           default:
                               Ext.MessageBox.alert('Error','No se pudo crear la Tarea.');
                               break;
                   }        
               },
               failure: function(response){
               var result=response.responseText;
               Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
               }                      
         });
       } else {
         Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
       }
    }

  
  // check if the form is valid
  function iscppEventoTareaFormValid(){	  
    var v1 = cppEventoDescTareaField.isValid();
    var v2 = cppEventoResponsablesCombo.isValid();
    var v3 = cppEventoTareaFechaField.isValid();
//    var v4 = cppEventoTareaRevisionRadios.isValid();
    return( v1 && v2 && v3);// && v4);
  }
  
  // reset the Form before opening it
  function resetPresidentForm(){
    cppEventoDescTareaField.setValue('');
    cppEventoResponsablesCombo.setValue('');
    cppEventoTareaFechaField.setValue('');
    cppEventoTareaRevisionRadios.setValue('');
  }	
cppEventoDescTareaField = new Ext.form.TextArea({
    id: 'cppEventoDescTareaField',
    fieldLabel: 'Describa la Tarea',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 3
});


cppEventoTareaRevisionRadios = new Ext.form.RadioGroup({ 
    id:'cppEventoTareaRevisionRadios',
    fieldLabel: 'Entrada para revisión de la dirección',
    allowBlank: false,
    anchor : '95%',
    tabIndex:3,
    columns: 4,
    items: [ 
          {boxLabel: 'Si', name: 'tarea_revision', inputValue: '1'}, //, checked: true
          {boxLabel: 'No', name: 'tarea_revision', inputValue: '2'}
     ] 
});


cppEventoUsuariosDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_responsables',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_usuario'
        }, [
            {name: 'id_usuario', mapping: 'id_usuario'},
            {name: 'nomape', mapping: 'nomape'},
            {name: 'usuario', mapping: 'usuario'},
            {name: 'puesto', mapping: 'puesto'},
        ])
    });

    // Custom rendering Template
    var cppEventoResponsableTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    cppEventoResponsablesCombo = new Ext.form.ComboBox({
        store: cppEventoUsuariosDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Usuario',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        anchor : '95%',
        minChars:3,
//        labelStyle: 'font-weight:bold;',
        pageSize:10,
         tabIndex: 11,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        tpl: cppEventoResponsableTpl,
        itemSelector: 'div.search-item'
    });
 cppEventoResponsableFieldSet = new Ext.form.FieldSet({
    id:'cppEventoResponsableFieldSet',
    title : 'Persona Responsable de gestionar la Tarea',
    anchor : '95%',
    growMin:100,
    items:[cppEventoResponsablesCombo]
}); 

cppEventoTareaFechaField = new Ext.form.DateField({
            id:'cppEventoTareaFechaField',
            allowBlank: false,
            fieldLabel:'Fecha Límite',
            allowBlank: false,
            anchor : '95%',
            blankText:'campo requerido',
            editable: true,
            minValue:MINDATE,
//            maxValue:,
            format:'d/m/Y'
        });
  
 
  cppEventoTareaCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'column',
            border:false,
            items:[{
                columnWidth:1,
                layout: 'form',
                border:false,
                items: [cppEventoDescTareaField,/*cppEventoTareaRevisionRadios,*/cppEventoResponsableFieldSet,cppEventoTareaFechaField]
            }]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: createCppCausasTarea
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			cppEventoTareaCreateWindow.close();
		  }
		}]
    });
	
 
  cppEventoTareaCreateWindow= new Ext.Window({
      id: 'cppEventoTareaCreateWindow',
      title: 'Crear nueva Tarea para desde la causa Nro: '+id,
      closable:false,
      modal:true,
      width: 500,
      height: 500,
      plain:true,
      layout: 'fit',
      items: cppEventoTareaCreateForm,
      closeAction: 'close'
    });		
    cppEventoTareaCreateWindow.show();
}