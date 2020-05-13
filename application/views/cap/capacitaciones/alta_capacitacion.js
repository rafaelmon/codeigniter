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

function clickBtnNuevaCapacitacion (){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnNuevaCapacitacion();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevaCapacitacion ()
{
    function altaCapacitacion(){
        if(isModuloFormValid())
        {
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/insert', 
                params: { 
                   titulo                 : tituloField.getValue(),
                   descripcion            : descripcionField.getValue()
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    switch(result){
                        case 1:
                            Ext.MessageBox.alert('Operación OK','Registro agregado correctamente');
                            capCapacitacionesDataStore.reload();
                            altaCapacitacionCreateWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','No tiene los permisos necesarios para realizar esta acción');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo dar de alta la capacitación.');
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
          var v1 = tituloField.isValid();
          var v2 = descripcionField.isValid();
        return(v1 && v2);
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          tituloField.setValue('');
          descripcionField.setValue('');
    }	

    tituloField = new Ext.form.TextField({
      id: 'tituloField',
      fieldLabel: 'Tema de capacitación (Sera el hallazgo en la tarea)',
      maxLength: 80,
      allowBlank: false,
      tabIndex:1,
      anchor : '95%'
    });
    
    descripcionField = new Ext.form.TextArea({
        id: 'descripcionField',
        fieldLabel: 'Describa el tema de la capacitación (este texto sera usado como descripción en las tareas)',
        maxLength: 2000,
        maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 2
    });
    

    
    altaCapacitacionCreateForm = new Ext.FormPanel({
        id:'altaCapacitacionCreateForm',
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
                    items: [tituloField,descripcionField]
                    }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: altaCapacitacion
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                altaCapacitacionCreateWindow.close();
            }
            }]
    });
	
 
    altaCapacitacionCreateWindow= new Ext.Window({
        id: 'altaCapacitacionCreateWindow',
        title: 'Alta nuevo tema de capacitación',
        closable:false,
        modal:true,
        width: 600,
        height: 250,
        plain:true,
        layout: 'fit',
        items: altaCapacitacionCreateForm,
        closeAction: 'close'
    });
    
    altaCapacitacionCreateWindow.show();
    
 }//fin 