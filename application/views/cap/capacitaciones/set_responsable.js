// JavaScript Document
function msgProcess(titulo){
    Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:400, 
        wait:true, 
        waitConfig: {interval:200}
    });
}

function clickBtnSetResponsable (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts)  {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnSetResponsable(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnSetResponsable (grid, rowIndex)
{
    var record = grid.getStore().getAt(rowIndex);
    var id=record.data.id_capacitacion;
    var titulo=record.data.titulo;
        
    function setResponsable(){
        
        if(isModuloFormValid())
        {
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/setResponsables', 
                params: { 
                   id_capacitacion  : id,
                   id_usuario       : responsablesCombo.getValue(),
                   fecha            : fechaField.getValue(),
                   id_criticidad    : capacitacionCriticidadRadios.getValue().inputValue,
//                   rpd              : capacitacionRevisionRadios.getValue().inputValue
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    switch(result){
                        case 1:
                            Ext.MessageBox.alert('Operación OK','Registro agregado correctamente');
                            capTareasDataStore.load({params: {id_capacitacion:id,start: 0}});
                            capTareasGridPanel.setTitle('Tareas generadas para el tema de capaciaci�n: <div style="color:blue;display:inline;"> Nro '+id+'-> '+titulo+'<div>');
                            setResponsableCreateWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','No tiene los permisos necesarios para realizar esta acción');
                            break;
                        case 4:
                            Ext.MessageBox.alert('Error','No se pudo dar de alta la tarea.');
                            setResponsableCreateWindow.close()
                            break;
                        case 5:
                            Ext.MessageBox.alert('Error','Error al guardar el registro.');
                            setResponsableCreateWindow.close()
                            break;
                        case 6:
                            Ext.MessageBox.alert('Error','Capacitacion repetida.');
                            break;
                        case 7:
                            Ext.MessageBox.alert('Error','El usuario solicitante y responsable no pueden ser los mismos.');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo definir la tarea de capacitacion.');
                            break;
                    }
                },
                failure: function(response){
                    var result=eval(response.responseText);
                    Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
                }
           });

       } else {
         Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
       }
     }

  
    // check if the form is valid
    function isModuloFormValid(){	  
          var v1 = responsablesCombo.isValid();
          var v2 = fechaField.isValid();
          var v3 = capacitacionCriticidadRadios.isValid();
//          var v4 = capacitacionRevisionRadios.isValid();
        return(v1 && v2 && v3);// && v4);
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          responsablesCombo.setValue('');
          descripcionField.setValue('');
          capacitacionCriticidadRadios.setValue('');
          capacitacionRevisionRadios.setValue('');
    }	
    
    
    capacitacionCriticidadRadios = new Ext.form.RadioGroup({ 
        id:'capacitacionCriticidadRadios',
        fieldLabel: 'Grado Criticidad',
        allowBlank: false,
        anchor : '95%',
        tabIndex:1,
        columns: 4,
        items: [ 
              {boxLabel: 'Crítica', name: 'tarea_crit', inputValue: '1'}, //, checked: true
              {boxLabel: 'Alta',    name: 'tarea_crit', inputValue: '2'}, 
              {boxLabel: 'Menor',   name: 'tarea_crit', inputValue: '3'}
         ] 
    });
    
    capacitacionRevisionRadios = new Ext.form.RadioGroup({ 
    id:'capacitacionRevisionRadios',
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

    usuariosDS = new Ext.data.Store({
        id: 'usuariosDS',
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
            {name: 'puesto', mapping: 'puesto'}
        ])
    });

    // Custom rendering Template
    var responsableTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    responsablesCombo = new Ext.form.ComboBox({
        store: usuariosDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Usuario Responsable',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        anchor : '95%',
        minChars:3,
//        labelStyle: 'font-weight:bold;',
        pageSize:10,
         tabIndex: 2,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        tpl: responsableTpl,
        itemSelector: 'div.search-item'
    });
    
    fechaField = new Ext.form.DateField({
        allowBlank: false,
        tabIndex: 3,
        fieldLabel:'Fecha Límite',
        anchor : '95%',
        blankText:'campo requerido',
        editable: true,
        minValue:MINDATE,
//            maxValue:,
        format:'d/m/Y'
    });
    

    
    setResponsableCreateForm = new Ext.FormPanel({
        id:'setResponsableCreateForm',
        labelAlign: 'top',
        labelWidth:80,
        bodyStyle:'padding:5px',
        width: 600,        
        items: [{
                id:'fieldset_form',
                layout:'column',
                border:false,
                items:[{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [capacitacionCriticidadRadios,responsablesCombo,fechaField]//,capacitacionRevisionRadios]
                    }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: setResponsable
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                setResponsableCreateWindow.close();
            }
            }]
    });
	
 
    setResponsableCreateWindow= new Ext.Window({
        id: 'setResponsableCreateWindow',
        title: 'Nueva tarea de capactación',
        closable:false,
        modal:true,
        width: 500,
        height: 270,
        plain:true,
        layout: 'fit',
        items: setResponsableCreateForm,
        closeAction: 'close'
    });
    
    setResponsableCreateWindow.show();
    
 }//fin 