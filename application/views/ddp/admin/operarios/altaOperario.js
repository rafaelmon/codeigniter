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
function createDdpOperario(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createDdpOperario();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}


function go_createDdpOperario(){
    if(isDdpOperarioFormValid()){
      msgProcess('Guardando...');
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/insert',
        params: {
          empresa       : ddpOperariosEmpresasCombo.getValue(),
          legajo        : ddpLegajoOperarioNumberField.getValue(),
          nombre        : ddpNombreOperarioField.getValue(),
          apellido      : ddpApellidoOperarioField.getValue(),
          id_supervisor : ddpSupsOperarioCombo.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
            case -2:
                Ext.MessageBox.alert('Error','No se pudo crear el registro, el lagajo ingresado ya se encuentra en la nómina.');
                break;
            case 1:
                Ext.MessageBox.alert('Alta OK','El Operario fue creado satisfactoriamente.');
                ddpOperariosDataStore.reload();
                ddpOperarioCreateWindow.close();
                break;
            default:
                Ext.MessageBox.alert('Error','No se pudo crear el operario, por favor vuelva a intentarlo o contacte con el administrador.');
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
  }// END function createDdpOperario

  
// Verifico que los campos del formulario sean válidos
function isDdpOperarioFormValid(){	  
    var v1 = ddpNombreOperarioField.isValid();
    var v2 = ddpApellidoOperarioField.isValid();
    var v3 = ddpSupsOperarioCombo.isValid();
    var v4 = ddpLegajoOperarioNumberField.isValid();
    return( v1 && v2 && v3 && v4);
}
   
// display or bring forth the form
function dFW_ddpNuevaOperario(){
//    if(ddpOperarioCreateForm){
//        ddpOperarioCreateForm.destroy();
//        ddpOperarioCreateWindow.destroy()
//    }
 ddpOperariosEmpresasDS = new Ext.data.Store({
        id: 'ddpOperariosEmpresasDS',
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
    
    ddpOperariosEmpresasCombo = new Ext.form.ComboBox({
            id:'ddpOperariosEmpresasCombo',
            forceSelection : false,
            fieldLabel: 'Empresa',
            store: ddpOperariosEmpresasDS,
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
    ddpOperariosEmpresasCombo.on('select', ddpSelectEmpresa);
     
    function ddpSelectEmpresa( combo, record, index ){
        var id =combo.getValue();
        var supervisoresCombo=Ext.getCmp('ddpSupsOperarioCombo')
        var supervisoresDS=supervisoresCombo.getStore();
        supervisoresCombo.enable();
        supervisoresCombo.reset();
        supervisoresDS.setBaseParam('id_empresa',id);
//        supervisoresDS.load();
    }
    
     ddpLegajoOperarioNumberField = new Ext.form.NumberField({
        id: 'nro_legajo',
        fieldLabel: 'Ingrese el Nro de Legajo',
        allowDecimals:false,
        allowNegative:false,
        maxValue:9999,
        minValue:0,
        invalidText:'Solo valor entero entre 1 y 9999',
        allowBlank: false,
        blankText:'campo requerido',
//        value:'',
        anchor : '30%',
        tabIndex: 5
    });
    
    ddpNombreOperarioField = new Ext.form.TextField({
        id: 'ddpNombreOperarioField',
        fieldLabel: 'Nombre',
        maxLength: 120,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        maxLengthText:'M&aacute;ximo 120 caracteres',
        tabIndex: 1
    });
      
    ddpApellidoOperarioField = new Ext.form.TextField({
        id: 'ddpApellidoOperarioField',
        fieldLabel: 'Apellido',
        allowBlank: false,
        blankText:'campo requerido',
//        width: 50,
        maxLength: 120,
        anchor : '95%',
        maxLengthText:'M&aacute;ximo 120 caracteres',
        tabIndex: 2
    });
   
    ddpSupsOperarioDS = new Ext.data.Store({
        id:'ddpSupsOperarioDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/supervisores_combo',
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
    var supervisoresTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
ddpSupsOperarioCombo = new Ext.form.ComboBox({
        id:'ddpSupsOperarioCombo',
        forceSelection : true,
        disabled :true,
        fieldLabel: 'Supervisor/es',
        store: ddpSupsOperarioDS,
        allowBlank: false,
        emptyText: 'Ingresa caracteres para buscar',
        blankText:'campo requerido',
        invalidText :'Máximo 2 Supervisores',
        displayField: 'nomape',
        valueField: 'id_usuario',
        anchor : '95%',
        mode: 'remote',
        tpl: supervisoresTpl,
        itemSelector: 'div.search-item',
        stackItems:true, //un item por línea
        allowQueryAll : true,
        triggerAction: 'all',
        minChars:3,
        tabIndex:3
    });
ddpSupsOperarioCombo.on('additem', selectSupervisor);
 ddpSupsOperarioCombo.on('removeitem', selectSupervisor);
 function selectSupervisor( combo, record, index ){
//   console.log(combo);
        var x =combo.getValue();
        x= x.split(",");
        x= x.length;
        ddpSupsOperarioDS.setBaseParam('q',x);
        if (x>2)
        {
            combo.markInvalid();
            combo.setReadOnly(true);
        }
        else
        {
            if (x==3)
                combo.setReadOnly(true);
                
            else
                combo.setReadOnly(false);
            
        }
            
    }
		
  function resetPresidentForm(){
    ddpDimensionesOperarioSBS.setValue('');
    ddpDetalleOperarioField.setValue('');
    ddpOperarioField.setValue('');
  }	
  
    ddpOperarioCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[ddpOperariosEmpresasCombo,ddpLegajoOperarioNumberField,ddpNombreOperarioField,ddpApellidoOperarioField,ddpSupsOperarioCombo]
        }],
        buttons: [{
            text: 'Guardar',
            handler: createDdpOperario
            },{
            text: 'Cancelar',
            handler: function(){
                ddpOperarioCreateWindow.close();
            }
            }]
    });//END FormPanel

 
    ddpOperarioCreateWindow= new Ext.Window({
        id: 'ddpOperarioCreateWindow',
        title: 'Nuevo Operario',
        closable:false,
        modal:true,
        width: 400,
        height: 450,
        plain:true,
        layout: 'fit',
        items: ddpOperarioCreateForm,
        closeAction: 'close'
    });		
    ddpOperarioCreateWindow.show();
}//END function displayFormWindow