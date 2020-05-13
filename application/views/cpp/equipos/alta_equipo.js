// JavaScript Document
function clickBtnNuevoEquipo (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnNuevoEquipo(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
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
function go_clickBtnNuevoEquipo (grid, rowIndex)
{
    function altaEquipo(){
        if(isModuloFormValid())
        {
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/insert', 
                params: { 
                   equipo             : equipoField.getValue(),
                   descripcion        : descripcionField.getValue(),
                   tag                : tagField.getValue()
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    switch(result){
                        case 1:
                            Ext.MessageBox.alert('Operación OK','Registro agregado correctamente');
                            equiposDataStore.reload();
                            altaEquipoCreateWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','Error inesperado, por favor avise al administrador');
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
          var v1  = equipoField.isValid();
          var v2  = descripcionField.isValid();
          var v3  = tagField.isValid();
        return( v1 && v2 && v3);
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          equipoField.setValue('');
          descripcionField.setValue('');
          tagField.setValue('');
    }	
    
    tagField = new Ext.form.TextField({
      id: 'tagField',
      fieldLabel: 'Tag',
      maxLength: 30,
      allowBlank: false,
      anchor : '80%'
    });
    
    equipoField = new Ext.form.TextField({
      id: 'equipoField',
      fieldLabel: 'Equipo',
      maxLength: 30,
      allowBlank: false,
      anchor : '80%'
    });
    descripcionField = new Ext.form.TextArea({
      id: 'descripcionField',
      fieldLabel: 'Descripción',
      maxLength: 1028,
      allowBlank: true,
      anchor : '80%'
    });
    
    altaEquipoCreateForm = new Ext.FormPanel({
        id:'altaEquipoCreateForm',
        labelAlign: 'left',
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
                    items: [tagField,equipoField,descripcionField]
                    }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: altaEquipo
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                altaEquipoCreateWindow.close();
            }
            }]
    });
	
 
    altaEquipoCreateWindow= new Ext.Window({
        id: 'altaEquipoCreateWindow',
        title: 'Alta nuevo equipo',
        closable:false,
        modal:true,
        width: 600,
        height: 200,
        plain:true,
        layout: 'fit',
        items: altaEquipoCreateForm,
        closeAction: 'close'
    });		
    altaEquipoCreateWindow.show();
    
 }//fin 