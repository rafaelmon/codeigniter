  // inserta Departamento en DB
function createDepartamento(){
    if(isDepartamentoFormValid()){
	 
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insert',
        params: {
          departamento      : nombreDepartamentoField.getValue(),
          abv           : abvDepartamentoField.getValue(),
          id_gerencia    : gerenciaCombo.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
            case 0:
                Ext.MessageBox.alert('Error','No se pudo crear el registro, ya existe una departamento con ese nombre para esa gerencia.');
                break;
            case 1:
                Ext.MessageBox.alert('Alta OK','El Registro fue creado satisfactoriamente.');
                DepartamentosDataStore.reload();
                DepartamentoCreateWindow.close();
                break;
            default:
                Ext.MessageBox.alert('Error','No se pudo crear el registro, por favor vuelva a intentarlo o contacte con el administrador.');
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
  }// END function createDepartamento

  
// Verifico que los campos del formulario sean válidos
function isDepartamentoFormValid(){	  
    var v1 = nombreDepartamentoField.isValid();
    var v2 = abvDepartamentoField.isValid();
    var v3 = gerenciaCombo.isValid();
    return( v1 && v2 && v3 );
}
   
// display or bring forth the form
function displayFormWindow(){
    if(DepartamentoCreateForm){
        DepartamentoCreateForm.destroy();
        DepartamentoCreateWindow.destroy()
    }
		
    nombreDepartamentoField = new Ext.form.TextField({
        id: 'nombreDepartamentoField',
        fieldLabel: 'Nombre Departamento',
        maxLength: 30,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        tabIndex: 1
    });
      
    abvDepartamentoField = new Ext.form.TextField({
        id: 'abvDepartamentoField',
        fieldLabel: 'Nombre abreviado',
        allowBlank: false,
        blankText:'campo requerido',
//        width: 50,
        anchor : '15%',
        tabIndex: 2,
        maxLength:4,
        minLength :2,
        maxLengthText:'M&aacute;ximo 4 caracteres',
        minLengthText:'M&iacute;nimo 2 caracteres'
    });
    empresasDS = new Ext.data.Store({
        id: 'empresasDS',
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
    empresaCombo = new Ext.form.ComboBox({
            id:'empresasCombo',
            forceSelection : false,
            fieldLabel: 'Seleccion la empresa a la que pertenece el departamento',
            store: empresasDS,
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
    gerenciasDS = new Ext.data.Store({
        id: 'gerenciasDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/gerencias_combo', 
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
            }, [
            {name: 'id_gerencia', type: 'int'},        
            {name: 'gerencia', type: 'string'},
        ])
    });
    gerenciaCombo = new Ext.form.ComboBox({
            id:'gerenciaCombo',
            forceSelection : false,
            disabled: true,	
            fieldLabel: 'Seleccion la gerencia a la que pertenece el departamento',
            store: gerenciasDS,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'gerencia',
            valueField: 'id_gerencia',
            anchor:'95%',
            mode: 'local',	
            triggerAction: 'all',
            width: 300,
            tabIndex: 3
    });
		
  function resetPresidentForm(){
    nombreDepartamentoField.setValue('');
    abvDepartamentoField.setValue('');
    empresaCombo.setValue('');
    gerenciaCombo.setValue('');
  }	
  
    DepartamentoCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[empresaCombo,gerenciaCombo,nombreDepartamentoField,abvDepartamentoField]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createDepartamento
            },{
            text: 'Cancelar',
            handler: function(){
                DepartamentoCreateWindow.close();
            }
            }]
    });//END FormPanel

//listener para los combos dependientes
empresaCombo.on('select',function(cmb,record,index){
	gerenciaCombo.enable();			
	gerenciaCombo.clearValue();		
	gerenciaCombo.reset();		
	gerenciasDS.load({			
            params:{
                id_empresa:record.get('id_empresa')	
            }	
	});
});

 
    DepartamentoCreateWindow= new Ext.Window({
        id: 'DepartamentoCreateWindow',
        title: 'Alta nueva Departamento',
        closable:false,
        modal:true,
        width: 610,
        height: 300,
        plain:true,
        layout: 'fit',
        items: DepartamentoCreateForm,
        closeAction: 'close'
    });		
    DepartamentoCreateWindow.show();
}//END function displayFormWindow