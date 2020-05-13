function msgProcess(titulo){
 Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:300, 
        wait:true, 
        waitConfig: {interval:200}
    });
}
function createDdpFuente(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createDdpFuente();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}


function go_createDdpFuente(){
    if(isDdpFuenteFormValid()){
	 
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insert',
        params: {
          dimensiones   : ddpDimensionesFuenteSBS.getValue(),
          fuente        : ddpFuenteField.getValue(),
          detalle       : ddpDetalleFuenteField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
            case 0:
                Ext.MessageBox.alert('Error','No se pudo crear el registro, ya existe el fuente que intenta crear.');
                break;
            case 1:
                Ext.MessageBox.alert('Alta OK','El Fuente fue creado satisfactoriamente.');
                ddpFuentesDataStore.reload();
                ddpFuenteCreateWindow.close();
                break;
            default:
                Ext.MessageBox.alert('Error','No se pudo crear el fuente, por favor vuelva a intentarlo o contacte con el administrador.');
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
  }// END function createDdpFuente

  
// Verifico que los campos del formulario sean válidos
function isDdpFuenteFormValid(){	  
    var v1 = ddpDimensionesFuenteSBS.isValid();
    var v2 = ddpFuenteField.isValid();
    var v3 = ddpDetalleFuenteField.isValid();
    return( v1 && v2 && v3);
}
   
// display or bring forth the form
function dFW_ddpNuevaFuente(){
//    if(ddpFuenteCreateForm){
//        ddpFuenteCreateForm.destroy();
//        ddpFuenteCreateWindow.destroy()
//    }

    ddpFuenteField = new Ext.form.TextArea({
        id: 'ddpFuenteField',
        fieldLabel: 'Fuente de Datos',
        maxLength: 100,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        maxLengthText:'M&aacute;ximo 100 caracteres',
        tabIndex: 1
    });
      
    ddpDetalleFuenteField = new Ext.form.TextArea({
        id: 'ddpDetalleFuenteField',
        fieldLabel: 'Detalle',
        allowBlank: true,
//        blankText:'campo requerido',
//        width: 50,
        maxLength: 2000,
        anchor : '95%',
        maxLengthText:'M&aacute;ximo 2000 caracteres',
//        minLengthText:'M&iacute;nimo 2 caracteres',
        tabIndex: 2
    });
   
    ddpDimensionesFuenteDS = new Ext.data.Store({
        id: 'ddpFuentesDimensionesDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/dimensiones_combo', 
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
            }, [
            {name: 'id_dimension', type: 'int'},        
            {name: 'dimension', type: 'string'},
        ])
    });
    ddpDimensionesFuenteSBS = new Ext.ux.form.SuperBoxSelect({
            id:'ddpDimensionFuenteCB',
            forceSelection : false,
            disabled: false,	
            fieldLabel: 'Seleccione la/s dimensión/es',
            store: ddpDimensionesFuenteDS,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            allowQueryAll : true,
            displayField: 'dimension',
            valueField: 'id_dimension',
            anchor:'95%',
            mode: 'remote',	
            triggerAction: 'all',
            width: 300,
            tabIndex: 3
    });
		
  function resetPresidentForm(){
    ddpDimensionesFuenteSBS.setValue('');
    ddpDetalleFuenteField.setValue('');
    ddpFuenteField.setValue('');
  }	
  
    ddpFuenteCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[ddpFuenteField,ddpDetalleFuenteField,ddpDimensionesFuenteSBS]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createDdpFuente
            },{
            text: 'Cancelar',
            handler: function(){
                ddpFuenteCreateWindow.close();
            }
            }]
    });//END FormPanel

 
    ddpFuenteCreateWindow= new Ext.Window({
        id: 'ddpFuenteCreateWindow',
        title: 'Nueva Fuente de Datos',
        closable:false,
        modal:true,
        width: 650,
        height: 500,
        plain:true,
        layout: 'fit',
        items: ddpFuenteCreateForm,
        closeAction: 'close'
    });		
    ddpFuenteCreateWindow.show();
}//END function displayFormWindow