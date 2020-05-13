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

function clickBtnCierraEvento (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnCierraEvento(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnCierraEvento (grid,rowIndex,colIndex,item,event)
{
    var record = grid.getStore().getAt(rowIndex);
    var evento = record.data.id;
    function cierraEvento(){
        if(isModuloFormValid())
        {
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/cerrarEvento', 
                params: { 
                    id_evento         : evento,
                    dias_esp       : diasPlazoEsperaField.getValue(),
                    dias_ver       : diasPlazoVerificacionField.getValue()
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    if(result.success){
                        cppEventosDataStore.reload();
                        cppCausasDataStore.reload();    
                        cierraEventoCreateWindow.close();
                        Ext.MessageBox.alert('OK',result.msg);
                    }
                    else
                        Ext.MessageBox.alert('Error',result.msg);
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
          var v1  = diasPlazoEsperaField.isValid();
          var v2  = diasPlazoVerificacionField.isValid();
        return( v1 && v2);
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          diasPlazoEsperaField.setValue('');
          diasPlazoVerificacionField.setValue('');
    }

  diasPlazoEsperaField = new Ext.form.NumberField({
      id: 'diasPlazoEsperaField',
      fieldLabel: 'Plazo de espera (días)',
      maxValue: 365,
      maxText :'365 días es el plazo máximo ',
      minValue:1,
      minText :'1 día es el plazo mínimo',
      allowBlank: false,
      allowNegative:false,
      allowDecimals:false,
      anchor : '95%'
    });
  diasPlazoVerificacionField = new Ext.form.NumberField({
      id: 'diasPlazoVerificacionField',
      fieldLabel: 'Plazo para verificar (días)',
      maxValue: 365,
      maxText :'365 días es el plazo máximo ',
      minValue:1,
      minText :'1 día es el plazo mínimo',
      allowBlank: false,
      allowNegative:false,
      allowDecimals:false,
      anchor : '95%'
    });
    plazosFieldSet = new Ext.form.FieldSet({
    id:'plazosFieldSet',
    title : 'Defina plazos',
    anchor : '95%',
    growMin:100,
    items:[diasPlazoEsperaField,diasPlazoVerificacionField]
});

cierraEventoCreateForm = new Ext.FormPanel({
    id:'cierraEventoCreateForm',
    labelAlign: 'left',
    labelWidth:150,
    bodyStyle:'padding:5px',
    width: 300,        
    items: [{
            id:'fieldset_form',
            layout:'column',
            border:false,
            html: '<br>¿Confirma que desea cerrar el evento nro: <b>'+evento+'</b>?<br><br>&nbsp;',
            items:[{
                columnWidth:1,
                layout: 'form',
                border:false,
                items: [plazosFieldSet]
                }]
    }],
    buttons: [{
        text: 'Guardar',
        handler: cierraEvento
    },{
        text: 'Cancelar',
        handler: function(){
            // because of the global vars, we can only instantiate one window... so let's just hide it.
            cierraEventoCreateWindow.close();
        }
        }]
});
	
 
    cierraEventoCreateWindow= new Ext.Window({
        id: 'cierraEventoCreateWindow',
        title: 'Cerrar Evento',
        closable:false,
        modal:true,
        width: 300,
        height: 270,
        plain:true,
        layout: 'fit',
        items: cierraEventoCreateForm,
        closeAction: 'close'
    });		
    cierraEventoCreateWindow.show();
    
 }//fin 