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
function createRpycTarea(a,b,id_rpyc){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createRpycTarea(a,b,id_rpyc);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

  function go_createRpycTarea(a,b,id_rpyc){
     if(isrpycTareaFormValid()){
        msgProcess('Guardando Tarea');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert_tarea',
            method: 'POST',
            params: {
                hallazgo     : rpycHallazgoField.getValue(),
                grado_crit   : rpycTareaCriticidadRadios.getValue().inputValue,
                tarea        : rpycTareaField.getValue(),
                responsable  : rpycResponsablesCombo.getValue(),
                fecha        : rpycTareaFechaField.getValue(),
                id_rpyc      : id_rpyc,
                opcion       : opcionDescubridorRpycRadio.getValue().inputValue,
                //rpd          : rpycTareaRevisionRadios.getValue().inputValue,
                usuario      : rpycDescubridorCombo.getValue(),
                operario     : rpycOperarioTextField.getValue(),
                contratista  : rpycContraCombo.getValue()
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 0:
                            Ext.MessageBox.alert('Error','Tarea repetida, comuníquese con el administrador');
                            rpycTareaCreateWindow.close();
                            break;
                        case 1:
                            Ext.MessageBox.alert('Alta OK','La Tarea fue creada satisfactoriamente');
                            rpycDataStore.reload();
                            rpycTareasDataStore.load({params: {id:id_rpyc,start: 0}});
                            rpycTareasPanel.show();
                            rpycTareaCreateWindow.close();
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case -3:
                            Ext.MessageBox.alert('Error','Se perdieron datos durante el envío.. por favor reintente');
                            break;
                        case -1:
                            Ext.MessageBox.alert('Error','Verifique sus permisos');
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
  function isrpycTareaFormValid(){	  
	  var v1 = rpycHallazgoField.isValid();
	  var v2 = rpycTareaField.isValid();
	  var v3 = rpycResponsablesCombo.isValid();
	  var v4 = rpycTareaFechaField.isValid();
	  var v5 = rpycDescubridorCombo.isValid();
	  var v6 = rpycOperarioTextField.isValid();
	  var v7 = rpycContraCombo.isValid();
          var v8 = rpycTareaCriticidadRadios.isValid();
          //var v9 = rpycTareaRevisionRadios.isValid();
	  return( v1 && v2 && v3 && v4 && v5 && v6 && v7 && v8);// && v9);
  }
  
  // display or bring forth the form
  function displayRpycTareaFormWindow(id_rpyc){
//	 if(rpycTareaCreateForm){
//	 	if(rpycTareaCreateForm.findById('fieldsetid')) {
//		 //get the fieldset
//	
//	
//		 var oldfieldset = rpycTareaCreateForm.findById('fieldset_form');
//		 //var oldfieldset = rpycTareaCreateForm.items;
//		 
//			//iterate trough each of the component in the fieldset
//			oldfieldset.items.each(function(collection,item,length){
//				var i = item;
//				//destroy the object within the fieldset
//				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
//			});
//		}
//		rpycTareaCreateForm.destroy();
//		rpycTareaCreateWindow.destroy()
//	}
//		
//
  // reset the Form before opening it
  function resetPresidentForm(){
    rpycHallazgoField.setValue('');
    rpycTareaField.setValue('');
    rpycResponsablesCombo.setValue('');
    rpycTareaFechaField.setValue('');
    rpycTareaRevisionRadios.setValue('');
  }	


rpycHallazgoField = new Ext.form.TextArea({
    id: 'rpycHallazgoField',
    fieldLabel: 'Describa el hallazgo',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 2
});
rpycTareaCriticidadRadios = new Ext.form.RadioGroup({ 
    id:'rpycTareaCriticidadRadios',
    fieldLabel: 'Grado Criticidad',
    allowBlank: false,
    anchor : '95%',
    tabIndex:2.1,
    columns: 4,
    items: [ 
          {boxLabel: 'Crítica', name: 'tarea_crit', inputValue: '1'}, //, checked: true
          {boxLabel: 'Alta',    name: 'tarea_crit', inputValue: '2'}, 
          {boxLabel: 'Menor',   name: 'tarea_crit', inputValue: '3'}
     ] 
});

rpycTareaRevisionRadios = new Ext.form.RadioGroup({ 
    id:'rpycTareaRevisionRadios',
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

//actoCondicionRpycRadio = new Ext.form.RadioGroup({ 
//        id:'actoCondicionRpycRadio',
//        fieldLabel: 'Defina el tipo de hallazgo',
//        hidden:false,
//        disabled:false,
//        tabIndex:9,
//        columns: 3, //muestra los radiobuttons en dos columnas 
//        items: [ 
//            {boxLabel: 'Acto Subestándar',         name: 'tipo_hallazgo', inputValue: '1', checked: true},
//            {boxLabel: 'Condición Subestándar',    name: 'tipo_hallazgo', inputValue: '2'}
//        ] 
//    });

//Combo = new Ext.form.ComboBox({
//            id:'sectorOmcCombo',
//            forceSelection : false,
//            hidden:false,
//            disabled:true,
//            fieldLabel: 'Sector',
//            store: sectorOmcDS,
//            editable : false,
//            allowBlank: false,
//            blankText:'campo requerido',
//            displayField: 'sector',
//            valueField: 'id_sector',
//            anchor:'95%',
//            triggerAction: 'all',
//            width: 300,
//            tabIndex: 3
//    });

rpycTareaField = new Ext.form.TextArea({
    id: 'rpycTareaField',
    fieldLabel: 'Describa la Tarea',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 3
});

rpycUsuariosDS = new Ext.data.Store({
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
    var rpycResponsableTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    rpycResponsablesCombo = new Ext.form.ComboBox({
        store: rpycUsuariosDS,
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
        tpl: rpycResponsableTpl,
        itemSelector: 'div.search-item'
    });
 rpycResponsableFieldSet = new Ext.form.FieldSet({
    id:'rpycResponsableFieldSet',
    title : 'Persona Responsable de gestionar la Tarea',
    anchor : '95%',
    growMin:100,
    items:[rpycResponsablesCombo]
}); 

 opcionDescubridorRpycRadio = new Ext.form.RadioGroup({ 
        id:'opcionDescubridorRpycRadio',
        fieldLabel: 'Defina el tipo de usuario que realizo el hallazgo',
        hidden:false,
        disabled:false,
        tabIndex:9,
        columns: 3, //muestra los radiobuttons en dos columnas 
        items: [ 
            {boxLabel: 'Usuario',     name: 'op_desc', inputValue: '1', checked: true},
            {boxLabel: 'Operario',    name: 'op_desc', inputValue: '2'},
            {boxLabel: 'Contratista', name: 'op_desc', inputValue: '3'}
        ] 
    });
    opcionDescubridorRpycRadio.on('change', selectRadioGroupDescubridor);
    
    rpycUsuarios2DS = new Ext.data.Store({
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
    var rpycDescubridorTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    rpycDescubridorCombo = new Ext.form.ComboBox({
        id:'rpycDescubridorCombo',
        store: rpycUsuarios2DS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Nombre del usuario',
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
        hidden:false,
        disabled:false,
        tpl: rpycDescubridorTpl,
        itemSelector: 'div.search-item'
    });
    
    rpycOperarioTextField = new Ext.form.TextField({
    id: 'rpycOperarioTextField',
    fieldLabel: 'Nombre del Operario',
    maxLength: 2000,
    maxLengthText :"Texto Demasiado Largo: Cantidad máxima 2000 caracteres",
    allowBlank: false,
    blankText:'campo requerido',
    anchor : '95%',
    tabIndex: 3,
    hidden:true,
    disabled:true
});
 rpycContraDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_contra',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_contratista'
        }, [
            {name: 'id_contratista', mapping: 'id_contratista'},
            {name: 'contratista', mapping: 'contratista'},
            {name: 'abv', mapping: 'abv'},
        ])
    });

    // Custom rendering Template
    var rpycContraTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<span style="color:#A4A4A4;font-size:10px;font-weight:normal;line-height:normal;">({abv}) </span><span style="color:#000;font-size:12px;font-weight:normal;line-height:normal;"> {contratista}</span>',
        '</div></tpl>'
    );
    rpycContraCombo = new Ext.form.ComboBox({
        id:'rpycContraCombo',
        store: rpycContraDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Razón Social Contratista',
        displayField:'contratista',
        valueField:'id_contratista',
        typeAhead: false,
        triggerAction: 'all',
        loadingText: 'Buscando...',
        anchor : '95%',
        minChars:3,
    //        labelStyle: 'font-weight:bold;',
        pageSize:5,
            tabIndex: 11,
        emptyText:'Ingresa caracteres para buscar',
//        valueNotFoundText:"Contratista no encontrado",
        valueNotFoundText:"",
         tpl: rpycContraTpl,
         itemSelector: 'div.search-item',
        hidden:true,
        disabled:true
    });
    
 rpycDescubridorFieldSet = new Ext.form.FieldSet({
    id:'rpycDescubridorFieldSet',
    title : 'Persona que realizo el hallazgo',
    anchor : '95%',
    growMin:100,
    items:[opcionDescubridorRpycRadio,rpycDescubridorCombo,rpycOperarioTextField,rpycContraCombo]
}); 

rpycTareaFechaField = new Ext.form.DateField({
            id:'rpycTareaFechaField',
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
  
 
  rpycTareaCreateForm = new Ext.FormPanel({
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
                items: [rpycHallazgoField,rpycTareaCriticidadRadios,/*rpycTareaRevisionRadios,/*actoCondicionRpycRadio,*/rpycDescubridorFieldSet,rpycTareaField,rpycResponsableFieldSet,rpycTareaFechaField]
            }]
        }],
		buttons: [{
		  text: 'Guardar',
		  handler: createRpycTarea.createDelegate(this,id_rpyc,true)
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			rpycTareaCreateWindow.close();
		  }
		}]
    });
	
 
  rpycTareaCreateWindow= new Ext.Window({
      id: 'rpycTareaCreateWindow',
      title: 'Crear nueva Tarea para la RPyC Nro: '+id_rpyc,
      closable:false,
      modal:true,
      width: 500,
      height: 700,
      plain:true,
      layout: 'fit',
      items: rpycTareaCreateForm,
      closeAction: 'close'
    });		
    rpycTareaCreateWindow.show();
  }
  
  function selectRadioGroupDescubridor( rg, checked ){
        var obj1,obj2,obj3;
        obj1=Ext.getCmp('rpycDescubridorCombo');
        obj2=Ext.getCmp('rpycOperarioTextField');
        obj3=Ext.getCmp('rpycContraCombo');
        
        
        switch (checked.inputValue)
        {
            case '1':
                obj1.enable();
                obj1.show();
                obj2.reset();
                obj2.disable();
                obj2.hide();
                obj3.reset();
                obj3.disable();
                obj3.hide();
                break;
            case '2':
                obj2.enable();
                obj2.show();
                obj1.reset();
                obj1.disable();
                obj1.hide();
                obj3.reset();
                obj3.disable();
                obj3.hide();
                break;
            case '3':
                obj3.enable();
                obj3.show();
                obj2.reset();
                obj2.disable();
                obj2.hide();
                obj1.reset();
                obj1.disable();
                obj1.hide();
                break;
        }
    }