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

  function setCriticidadRmc(a,b,id_rmc){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_setCriticidadRmc(a,b,id_rmc);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

  function go_setCriticidadRmc(a,b,id_rmc){
     if(isRmcSetCritFormValid()){
        msgProcess('Guardando...');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/setCriticidad',
            method: 'POST',
            params: {
                id_rmc    : id_rmc,
                clas      : clasificacionRmcSBS.getValue(),
                id_crit   : criticidadRmcRadios.getValue().inputValue,
                inv1      : inv1RmcCombo.getValue(),
                inv2      : inv2RmcCombo.getValue()
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 1:
                            Ext.MessageBox.alert('Alta OK','Acción finalizada correctamente');
                            rmcDataStore.reload();
                            rmcSetCritCreateWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','Error al insertar los datos');
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case 4:
                            Ext.MessageBox.alert('Error','Acción solo permitida a Usuarios del Area de GR');
//                            acomp1Combo.setActiveError('Error');
                            break;
                        case 5:
                            Ext.MessageBox.alert('Error','Por favor verifique, ambos investigadores no pueden ser la misma persona');
                            inv1RmcCombo.markInvalid();
                            inv2RmcCombo.markInvalid();
//                            acomp1Combo.setActiveError('Error');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo realizar la acción...');
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
  function isRmcSetCritFormValid(){	  
	  var v1 = criticidadRmcRadios.isValid();
	  var v2 = inv1RmcCombo.isValid();
	  var v3 = inv2RmcCombo.isValid();
          var v4 = clasificacionRmcSBS.isValid();
	  return( v1 && v2 && v3 && v4);
  }
   
  // display or bring forth the form
  function rmcSetCritDisplayFormWindow(id_rmc){
	 if(rmcSetCritCreateForm){
	 	if(rmcSetCritCreateForm.findById('fieldsetid')) {
		 //get the fieldset
	
	
		 var oldfieldset = rmcSetCritCreateForm.findById('fieldset_form');
		 
			//iterate trough each of the component in the fieldset
			oldfieldset.items.each(function(collection,item,length){
				var i = item;
				//destroy the object within the fieldset
				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
			});
		}
		rmcSetCritCreateForm.destroy();
		rmcSetCritCreateWindow.destroy()
	}
		

  // reset the Form before opening it
  function resetPresidentForm(){
    inv1RmcCombo.setValue('');
    inv2RmcCombo.setValue('');
  }	

criticidadRmcRadios = new Ext.form.RadioGroup({ 
        id:'criticidadRmcRadios',
        fieldLabel: 'Nivel de Criticidad',
        tabIndex:1,
        columns: 4,
        anchor : '95%',
        autoWidth: true,
//        boxMaxWidth:100,
        allowBlank: false,
        blankText:'Debe seleccionar una opción',
        items: [ 
            {boxLabel: '<span style="background-color:#01A9DB; color:#FFF;">&nbspFuera de Alcance&nbsp;</span>', name: 'rgCriticidad', inputValue: '4'},
            {boxLabel: '<span style="background-color:#088A08; color:#FFF;">&nbsp;Menor&nbsp;</span></br>', name: 'rgCriticidad', inputValue: '3'}, 
            {boxLabel: '<span style="background-color:#DF7401; color:#FFF;">&nbsp;Alto&nbsp;</span>',      name: 'rgCriticidad', inputValue: '2'}, 
            {boxLabel: '<span style="background-color:#FF0000; color:#FFF;">&nbsp;Crítico&nbsp;</span>',   name: 'rgCriticidad', inputValue: '1'}
        ] 
    });
tipoIncidenteRmcRadios = new Ext.form.RadioGroup({ 
        id:'tipoIncidenteRmcRadios',
        fieldLabel: 'Tipo de incidente',
        tabIndex:1,
        columns: 3,
        anchor : '95%',
        autoWidth: false,
//        boxMaxWidth:100,
        allowBlank: false,
        blankText:'Debe seleccionar una opción',
        items: [ 
            {boxLabel: '<span style="background-color:#DF7401; color:#FFF;">Accidente</span>',      name: 'tipoIncidente', inputValue: '2'}, 
            {boxLabel: '<span style="background-color:#FF0000; color:#FFF;">Cuasiaccidente</span>',   name: 'tipoIncidente', inputValue: '1'}
        ] 
});

criticidadRmcRadios.on('change', selectRadioGroupCrit);

    inv1RmcDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_inv',
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
    var acomp1Tpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
             '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    inv1RmcCombo = new Ext.form.ComboBox({
        id:'inv1RmcCombo',
        store: inv1RmcDS,
        blankText:'campo requerido',
        invalidText:'Investigador 1 e Investigador 2 no pueden ser la misma persona',
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
        tpl: acomp1Tpl,
        itemSelector: 'div.search-item',
        hidden:true,
        disabled:true
    });
    inv1RmcCombo.on('select',function(combo,record,index){
        var combo2=Ext.getCmp('inv2RmcCombo');
        var store2=combo2.getStore();
        id=combo.getValue();
        store2.setBaseParam('idNot',id);
        store2.removeAll();
    });
    inv2RmcDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_inv',
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
    var acomp1Tpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
             '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    inv2RmcCombo = new Ext.form.ComboBox({
        id:'inv2RmcCombo',
        store: inv2RmcDS,
        blankText:'campo requerido',
        invalidText:'Investigador 1 e Investigador 2 no pueden ser la misma persona',
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
        tpl: acomp1Tpl,
        itemSelector: 'div.search-item',
        hidden:true,
        disabled:true
    });
    inv2RmcCombo.on('select',function(combo,record,index){
        var combo2=Ext.getCmp('inv1RmcCombo');
        var store2=combo2.getStore();
        id=combo.getValue();
        store2.setBaseParam('idNot',id);
        store2.removeAll();
    });
          
    invRmcFieldSet = new Ext.form.FieldSet({
        id:'invRmcFieldSet',
        title : 'Investigadores',
        anchor : '95%',
//        growMin:100,
        items:[inv1RmcCombo,inv2RmcCombo]
    });
    
    claificacionRmcDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_clasificaciones',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_clasificacion'
        }, [
            {name: 'id_clasificacion', mapping: 'id_clasificacion'},
            {name: 'clasificacion', mapping: 'clasificacion'},
        ])
    });
//var revisoresTpl = new Ext.XTemplate(
//        '<tpl for="."><div class="search-item">',
//            '<h3><span>{nomape}</h3>({usuario})</span>',
//        '</div></tpl>'
//);
clasificacionRmcSBS = new Ext.ux.form.SuperBoxSelect({
        id:'clasificacionRmcSBS',
        forceSelection : false,
        fieldLabel: 'Seleccione la/s clasificacion/es para el RI (pueden ser + de 1)',
        store: claificacionRmcDS,
//        editable : false,
        allowBlank: false,
        emptyText: 'Ingresa caracteres para buscar',
        blankText:'campo requerido',
        displayField: 'clasificacion',
        valueField: 'id_clasificacion',
        anchor : '95%',
//        displayFieldTpl: '{nomape} ({usuario})',
        mode: 'remote',
        valueDelimiter:';',
//        tpl: revisoresTpl,
//        itemSelector: 'div.search-item',
        stackItems:true, //un item por línea
        anchor:'90%',
        triggerAction: 'all',
        forceSelection : true,
        allowQueryAll : false,
        minChars:3,
        maxSelections : 3,
        tabIndex:0
    });
    
clasificacionRmcFieldSet = new Ext.form.FieldSet({
    id:'clasificacionRmcFieldSet',
    title : 'Clasificación',
    anchor : '95%',
    growMin:100,
    items:[clasificacionRmcSBS]
});
    
 
   rmcSetCritCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        labelWidth:110,
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
                layout:'column',
                border:false,
                items:[{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [clasificacionRmcFieldSet,criticidadRmcRadios,invRmcFieldSet]
                    }
                ]
                }
        ],
		buttons: [{
		  text: 'Guardar',
		  handler: setCriticidadRmc.createDelegate(this,id_rmc,true)
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			rmcSetCritCreateWindow.close();
		  }
		}]
    });
	
 
    rmcSetCritCreateWindow= new Ext.Window({
        id: 'rmcSetCritCreateWindow',
        title: 'Definir la criticidad del Reporte',
        closable:false,
        modal:true,
        width: 550,
        height: 600,
        plain:true,
        layout: 'fit',
        items: rmcSetCritCreateForm,
        closeAction: 'close'
    });		
    rmcSetCritCreateWindow.show();
    
    function selectRadioGroupCrit(rg, checked ){
        var combo,combo2;
        combo1=Ext.getCmp('inv1RmcCombo')
        combo2=Ext.getCmp('inv2RmcCombo')
        switch (checked.inputValue)
        {
            case '1':
            case '2':
                combo1.enable();
                combo1.show();
                combo2.enable();
                combo2.show();                
                break;
            case '3':
            case '4':
                combo1.reset();
                combo1.disable();
                combo1.hide();
                combo2.reset();
                combo2.disable();
                combo2.hide();
                break;
        }
    }
}