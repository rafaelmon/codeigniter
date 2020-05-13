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
function createDdpObjetivo(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createDdpObjetivo();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}


function go_createDdpObjetivo(){
    if(isDdpObjetivoFormValid()){
	 
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insert',
        params: {
          id_periodo    : ddpPeriodoObjetivoField.getValue(),
          id_dimension  : ddpDimensionObjetivoCB.getValue(),
          objetivo      : ddpObjetivoField.getValue(),
          detalle       : ddpDetalleObjetivoField.getValue(),
          empresas      : empresasObjetivoSBS.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
            case 0:
                Ext.MessageBox.alert('Error','No se pudo crear el registro, ya existe el objetivo que intenta crear.');
                break;
            case 1:
                Ext.MessageBox.alert('Alta OK','El Objetivo fue creado satisfactoriamente.');
                ddpObjetivosDataStore.reload();
                ddpObjetivoCreateWindow.close();
                break;
            default:
                Ext.MessageBox.alert('Error','No se pudo crear el objetivo, por favor vuelva a intentarlo o contacte con el administrador.');
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
  }// END function createDdpObjetivo

  
// Verifico que los campos del formulario sean válidos
function isDdpObjetivoFormValid(){	  
    var v1 = ddpPeriodoObjetivoField.isValid();
    var v2 = ddpDimensionObjetivoCB.isValid();
    var v3 = ddpObjetivoField.isValid();
    var v4 = ddpDetalleObjetivoField.isValid();
    var v5 = empresasObjetivoSBS.isValid();
    return( v1 && v2 && v3 && v4 && v5);
}
   
// display or bring forth the form
function dFW_ddpNuevoObetivo(){
//    if(ddpObjetivoCreateForm){
//        ddpObjetivoCreateForm.destroy();
//        ddpObjetivoCreateWindow.destroy()
//    }

    ddpObjetivosPeriodosDS = new Ext.data.Store({
        id: 'ddpObjetivosPeriodosDS',
        proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/periodos_combo', 
        method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
            }, [
            {name: 'id_periodo', type: 'int'},        
            {name: 'periodo', type: 'string'},
        ])
    });
    ddpPeriodoObjetivoField = new Ext.form.ComboBox({
        id:'periodoField',
        forceSelection : false,
        fieldLabel: 'Per&iacute;odo',
        store: ddpObjetivosPeriodosDS,
        editable : false,
        allowBlank: false,
        blankText:'campo requerido',
        anchor:'95%',
        displayField: 'periodo',
        valueField: 'id_periodo',
        mode:'remote',
        triggerAction: 'all',
        width: 300
    });
    
    ddpObjetivoField = new Ext.form.TextArea({
        id: 'ddpObjetivoField',
        fieldLabel: 'Objetivo a nivel empresa',
        maxLength: 2000,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        maxLengthText:'M&aacute;ximo 2000 caracteres',
        tabIndex: 1
    });
      
    ddpDetalleObjetivoField = new Ext.form.TextArea({
        id: 'ddpDetalleObjetivoField',
        fieldLabel: 'Detalle',
        allowBlank: true,
//        blankText:'campo requerido',
//        width: 50,
        anchor : '95%',
        minLengthText:'M&iacute;nimo 2 caracteres',
        tabIndex: 2
    });
    ddpObjetivosEmpresasDS = new Ext.data.Store({
        id: 'ddpObjetivosEmpresasDS',
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
    empresasObjetivoSBS = new Ext.ux.form.SuperBoxSelect({
            id:'empresasCombo',
            forceSelection : false,
            fieldLabel: 'Seleccion la/s empresa/s en las que se aplica el objetivo',
            store: ddpObjetivosEmpresasDS,
//            editable : false,
            allowBlank: false,
            allowQueryAll : true,
            blankText:'campo requerido',
            displayField: 'empresa',
            valueField: 'id_empresa',
            anchor:'95%',
            triggerAction: 'all',
            width: 300,
            mode: 'remote',
            stackItems:true,
            tabIndex: 3
    });
    ddpObjetivosDimensionesDS = new Ext.data.Store({
        id: 'ddpObjetivosDimensionesDS',
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
    ddpDimensionObjetivoCB = new Ext.form.ComboBox({
            id:'ddpDimensionObjetivoCB',
            forceSelection : false,
            disabled: false,	
            fieldLabel: 'Seleccione la dimensión',
            store: ddpObjetivosDimensionesDS,
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
    ddpPeriodoObjetivoField.setValue('');
    ddpDimensionObjetivoCB.setValue('');
    ddpDetalleObjetivoField.setValue('');
    empresasObjetivoSBS.setValue('');
  }	
  
    ddpObjetivoCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[ddpPeriodoObjetivoField,ddpDimensionObjetivoCB,ddpObjetivoField,ddpDetalleObjetivoField,empresasObjetivoSBS]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createDdpObjetivo
            },{
            text: 'Cancelar',
            handler: function(){
                ddpObjetivoCreateWindow.close();
            }
            }]
    });//END FormPanel

 
    ddpObjetivoCreateWindow= new Ext.Window({
        id: 'ddpObjetivoCreateWindow',
        title: 'Nuevo Objetivo',
        closable:false,
        modal:true,
        width: 650,
        height: 500,
        plain:true,
        layout: 'fit',
        items: ddpObjetivoCreateForm,
        closeAction: 'close'
    });		
    ddpObjetivoCreateWindow.show();
}//END function displayFormWindow