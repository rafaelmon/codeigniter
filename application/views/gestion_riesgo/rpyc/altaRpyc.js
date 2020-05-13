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

function createRpyc(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createRpyc();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

  function go_createRpyc(){
     if(isRpycFormValid()){
        msgProcess('Guardando RPyC');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert',
            method: 'POST',
            params: {
//                titulo          : tituloTextField.getValue(),
//                descripcion     : descTextArea.getValue(),
                usuarios        : usuariosNumberField.getValue(),
                contratistas    : contNumberField.getValue(),
//                programada      : programadaRadios.getValue().inputValue,
//                realizada       : realizadaRadios.getValue().inputValue,
                areas        : areasRpycSBS.getValue()
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 1:
                            Ext.MessageBox.alert('Alta OK','RPyC creada satisfactoriamente.');
                            rpycDataStore.reload();
                            rpycCreateWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','La cantidad de usuarios y la cantidad de contratistas no pueden ser ambos cero');
                            usuariosNumberField.markInvalid('No pueden ser ambos cero');
                            contNumberField.markInvalid();
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case 4:
                            Ext.MessageBox.alert('Error','Error inesperado al guardar el registro!');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo crear la RPyC.');
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
  function isRpycFormValid(){	  
//	  var v1 = tituloTextField.isValid();
//	  var v2 = descTextArea.isValid();
	  var v3 = usuariosNumberField.isValid();
	  var v4 = contNumberField.isValid();
//	  var v5 = programadaRadios.isValid();
//	  var v6 = realizadaRadios.isValid();
	  var v7 = areasRpycSBS.isValid();
//	  return( v3 && v4 && v5 && v6 && v7);
	  return( v3 && v4 && v7);
  }
   
   function clickBtnNuevaRpyc(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_clickBtnNuevaRpyc();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
   
  // display or bring forth the form
  function go_clickBtnNuevaRpyc(){

	 if(rpycCreateForm){
	 	if(rpycCreateForm.findById('fieldsetid')) {
		 //get the fieldset
	
	
		 var oldfieldset = rpycCreateForm.findById('fieldset_form');
		 
			//iterate trough each of the component in the fieldset
			oldfieldset.items.each(function(collection,item,length){
				var i = item;
				//destroy the object within the fieldset
				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
			});
		}
		rpycCreateForm.destroy();
		rpycCreateWindow.destroy()
	}
		

  // reset the Form before opening it
  function resetPresidentForm(){
//    tituloTextField.setValue('');
//    descTextArea.setValue('');
    usuariosNumberField.setValue('');
    contNumberField.setValue('');
  }	

//    tituloTextField = new Ext.form.TextField({
//        id: 'titulo',
//        fieldLabel: 'Titulo de la reunión',
//        maxLength: 1000,
//        maxLengthText :"Texto Demasiado Largo: Cantidad máxima 1000 caracteres",
//        allowBlank: false,
//        blankText:'campo requerido',
////        emptyText:'(Titulo)',
//        anchor : '95%',
//        tabIndex: 1
//    });
//    descTextArea = new Ext.form.TextArea({
//        id: 'descripcion',
//        fieldLabel: 'Descripción',
//        maxLength: 2000,
//        maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
//        allowBlank: false,
//        blankText:'campo requerido',
//        anchor : '95%',
//        tabIndex: 2
//    });
    usuariosNumberField = new Ext.ux.form.SpinnerField({
        id: 'q_usuarios',
        fieldLabel: 'Cantidad de empleados que participaron',
        allowDecimals:false,
        allowNegative:false,
        maxValue:100,
        minValue:0,
        invalidText:'Solo enteros entre 0 y 100',
        allowBlank: false,
        blankText:'campo requerido',
        invalidText:'Inválido',
        value:0,
        anchor : '40%',
        tabIndex: 5
    });
     usuariosNumberField.on('spinup', validarCampo);
    contNumberField = new Ext.ux.form.SpinnerField({
        id: 'q_contratistas',
        fieldLabel: 'Cantidad de contratistas que participaron',
        allowDecimals:false,
        allowNegative:false,
        maxValue:100,
        minValue:0,
        invalidText:'Inválido',
        value:0,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '40%',
        tabIndex: 6
    });
    contNumberField.on('spinup', validarCampo);
    
     empresasRpycDS = new Ext.data.Store({
        id: 'empresasRpycDS',
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
    
    empresaRpycCombo = new Ext.form.ComboBox({
            id:'empresaRpycCombo',
            forceSelection : false,
            fieldLabel: 'Empresa',
            store: empresasRpycDS,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'empresa',
            valueField: 'id_empresa',
            anchor:'95%',
            triggerAction: 'all',
            width: 300,
            tabIndex: 7
    });
//    empresaRpycCombo.on('select', selectEmpresa);
    
//    areasRpycDS = new Ext.data.Store({
//        id: 'areasRpycDS',
//        proxy: new Ext.data.HttpProxy({
//        url: CARPETA+'/areas_combo', 
//        method: 'POST'
//        }),
//        reader: new Ext.data.JsonReader({
//            root: 'rows',
//            totalProperty: 'total'
//            }, [
//            {name: 'id_area', type: 'int'},        
//            {name: 'area', type: 'string'},
//        ])
//    });
    
    areasRpycDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_areas',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_area'
        }, [
            {name: 'id_area', mapping: 'id_area'},
            {name: 'area', mapping: 'area'},
        ])
    });
    
    areasRpycDS.setBaseParam('id',2);
//        areasRpycDS.commitChanges();
    areasRpycDS.load();
//var revisoresTpl = new Ext.XTemplate(
//        '<tpl for="."><div class="search-item">',
//            '<h3><span>{nomape}</h3>({usuario})</span>',
//        '</div></tpl>'
//);
    areasRpycSBS = new Ext.ux.form.SuperBoxSelect({
        id:'areasRpycSBS',
        forceSelection : false,
        fieldLabel: 'Seleccione la/s área/s que participaron',
        store: areasRpycDS,
//        editable : false,
        allowBlank: false,
        emptyText: 'Ingresa caracteres para buscar',
        blankText:'campo requerido',
        displayField: 'area',
        valueField: 'id_area',
        anchor : '95%',
//        displayFieldTpl: '{nomape} ({usuario})',
        mode: 'local',
        valueDelimiter:';',
//        tpl: revisoresTpl,
//        itemSelector: 'div.search-item',
        stackItems:false, //un item por línea
        anchor:'95%',
        triggerAction: 'all',
        forceSelection : true,
        allowQueryAll : true,
        minChars:3,
        maxSelections : 3,
//        disabled:true,
        tabIndex:8
    });
    
    areasRpycFieldSet = new Ext.form.FieldSet({
        id:'areasRpycFieldSet',
        title : 'Areas',
        anchor : '95%',
        growMin:100,
        items:[areasRpycSBS]//empresaRpycCombo, 
    });
    
//     programadaRadios = new Ext.form.RadioGroup({ 
//        id:'programadaRadios',
//        fieldLabel: '¿La reunión fue programada?',
//        tabIndex:3,
//        columns: 2,
//        anchor : '95%',
//        autoWidth: false,
//        boxMaxWidth:100,
//        allowBlank: false,
//        blankText:'Debe seleccionar una opción',
//        items: [ 
//            {boxLabel: 'Si', name: 'programada', inputValue: 'si'}, 
//            {boxLabel: 'No', name: 'programada', inputValue: 'no'}
//        ] 
//    });
//    
//     realizadaRadios = new Ext.form.RadioGroup({ 
//        id:'realizadaRadios',
//        fieldLabel: '¿La reunión fue realizada?',
//        tabIndex:4,
//        columns: 2,
//        anchor : '95%',
//        autoWidth: false,
//        boxMaxWidth:100,
//        allowBlank: false,
//        blankText:'Debe seleccionar una opción',
//        items: [ 
//            {boxLabel: 'Si', name: 'realizada', inputValue: 'si'}, 
//            {boxLabel: 'No', name: 'realizada', inputValue: 'no'}
//        ] 
//    });
    
    
//    analisisRiesgoFieldSet = new Ext.form.FieldSet({
//        id:'analisisRiesgoFieldSet',
//        title : 'Análisis de Riesgo',
//        anchor : '95%',
//        growMin:100,
//        items:[analisisRiesgoRmcRadios]
//    }); 
    
 
   rpycCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        labelWidth:110,
        bodyStyle:'padding:5px',
//        width:500,
        padding:10,
        autoWidth:true,
        items: [{
                layout:'column',
                border:false,
                items:[/*{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [tituloTextField,descTextArea]
                    },*/
                    {
                    columnWidth:1,
                    layout: 'column',
                    border:false,
                    items: [
                        {
                            columnWidth:0.5,
                            layout: 'form',
                            border:false,
                            items: [usuariosNumberField]
                        },{
                            columnWidth:0.5,
                            layout: 'form',
                            border:false,
                            items: [contNumberField]
                        }
                    ]
                    },{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [areasRpycFieldSet]
                    }
                ]
                }
        ],
		buttons: [{
		  text: 'Guardar',
		  handler: createRpyc
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			rpycCreateWindow.close();
		  }
		}]
    });
	
 
    rpycCreateWindow= new Ext.Window({
        id: 'rpycCreateWindow',
        title: 'Crear nueva RPyC',
        closable:false,
        modal:true,
        width: 550,
        height: 400,
        plain:true,
        layout: 'fit',
        items: rpycCreateForm,
        closeAction: 'close'
    });		


    rpycCreateWindow.show();
    
//    function selectEmpresa( combo, record, index ){
//        var id =combo.getValue();
//        var comboarea=Ext.getCmp('areasRpycSBS')
//        comboarea.reset();
//        comboarea.resetStore();
//        areasRpycDS.clearData();
//        areasRpycDS.clearFilter();
////        areasRpycDS.clearModified();
//        areasRpycDS.setBaseParam('id',id);
//        areasRpycDS.commitChanges();
//        areasRpycDS.load();
//        comboarea.enable();
//    }
    function validarCampo (obj){
        contNumberField.unsetActiveError();
        if(!(contNumberField.isValid()))
        {
        }
    }
}