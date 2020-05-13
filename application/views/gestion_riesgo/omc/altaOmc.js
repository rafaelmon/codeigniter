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

function createOmc(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createOmc();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

  function go_createOmc(){
     if(isOmcFormValid()){
        msgProcess('Guardando OMC');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert',
            method: 'POST',
            params: {
                acomp1          : acomp1Combo.getValue(),
                acomp2          : acomp2Combo.getValue(),
//                empresa         : empresaOmcCombo.getValue(),
                sitio           : sitioOmcCombo.getValue(),
                sector          : sectorOmcCombo.getValue(),
                ar              : analisisRiesgoRmcRadios.getValue().inputValue
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case -1:
                            Ext.MessageBox.alert('Error','Su usuario no tiene permisos para realizar esta acción.');
                            break;
                        case 1:
                            Ext.MessageBox.alert('Alta OK','OMC creada satisfactoriamente.');
                            omcDataStore.reload();
                            omcCreateWindow.close();
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case 4:
                            Ext.MessageBox.alert('Error','Por favor verifique, su usuario no esta definido para realizar OMC');
                            break;
                        case 5:
                            Ext.MessageBox.alert('Error','Los usuarios acompañantes no pueden ser los mismos.');
//                            acomp1Combo.markInvalid();
//                            acomp1Combo.setActiveError('Error');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo crear la OMC.');
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
  function isOmcFormValid(){	  
	  var v1 = acomp1Combo.isValid();
//	  var v2 = empresaOmcCombo.isValid();
	  var v3 = sitioOmcCombo.isValid();
	  var v4 = sectorOmcCombo.isValid();
	  var v5 = analisisRiesgoRmcRadios.isValid();
	  var v6 = acomp2Combo.isValid();
	  return( v1 && v3 && v4 && v5 && v6);
  }
   
   function clickBtnNuevaOmc(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_clickBtnNuevaOmc();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
   
  // display or bring forth the form
  function go_clickBtnNuevaOmc(){

	
	 if(omcCreateForm){
	 	if(omcCreateForm.findById('fieldsetid')) {
		 //get the fieldset
	
	
		 var oldfieldset = omcCreateForm.findById('fieldset_form');
		 
			//iterate trough each of the component in the fieldset
			oldfieldset.items.each(function(collection,item,length){
				var i = item;
				//destroy the object within the fieldset
				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
			});
		}
		omcCreateForm.destroy();
		omcCreateWindow.destroy();
	}
		

  // reset the Form before opening it
  function resetPresidentForm(){
    acomp1Combo.setValue('');
    empresaOmcCombo.setValue('');
    sitioOmcCombo.setValue('');
    sectorOmcCombo.setValue('');
  }	
    acomp1DS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_acomp',
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
    
    acomp1Combo = new Ext.form.ComboBox({
        id:'acomp1Combo',
        store: acomp1DS,
        blankText:'campo requerido',
        invalidText:'Acompañante inválido',
        allowBlank: false,
        fieldLabel: 'Acompañante 1',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        triggerAction: 'all',
        anchor : '95%',
        minChars:3,
    //        labelStyle: 'font-weight:bold;',
        pageSize:10,
        tabIndex: 1,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        tpl: acomp1Tpl,
        itemSelector: 'div.search-item',
        hidden:false,
        disabled:false
    });
    
    acomp1Combo.on('select', selectAcomp2);
    
    acomp2DS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_acomp2',
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
    var acomp2Tpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
             '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    acomp2Combo = new Ext.form.ComboBox({
        id:'acomp2Combo',
        store: acomp2DS,
        blankText:'campo requerido',
        invalidText:'Acompañante inválido',
        allowBlank: true,
        fieldLabel: 'Acompañante 2',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        triggerAction: 'all',
        anchor : '95%',
        minChars:3,
    //        labelStyle: 'font-weight:bold;',
        pageSize:10,
        tabIndex: 2,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        tpl: acomp2Tpl,
        itemSelector: 'div.search-item',
        hidden:false,
        disabled:false
    });
     
   
    
    acompFieldSet = new Ext.form.FieldSet({
        id:'acompFieldSet',
        title : 'Acompañante',
        anchor : '95%',
//        growMin:100,
        items:[acomp1Combo,acomp2Combo]
    });
     
//    empresasOmcDS = new Ext.data.Store({
//        id: 'empresasOmcDS',
//        proxy: new Ext.data.HttpProxy({
//        url: CARPETA+'/empresas_combo', 
//        method: 'POST'
//        }),
//        reader: new Ext.data.JsonReader({
//            root: 'rows',
//            totalProperty: 'total'
//            }, [
//            {name: 'id_empresa', type: 'int'},        
//            {name: 'empresa', type: 'string'},
//        ])
//    });
//    
//    empresaOmcCombo = new Ext.form.ComboBox({
//            id:'empresaOmcCombo',
//            forceSelection : false,
//            fieldLabel: 'Empresa',
//            store: empresasOmcDS,
//            editable : false,
//            allowBlank: false,
//            blankText:'campo requerido',
//            displayField: 'empresa',
//            valueField: 'id_empresa',
//            anchor:'95%',
//            triggerAction: 'all',
//            width: 300,
//            tabIndex: 3
//    });
//    
//    empresaOmcCombo.on('select', selectEmpresa);
    
    sitioOmcDS = new Ext.data.Store({
        id: 'sitioOmcDS',
        proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/sitios_combo', 
        method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
            }, [
            {name: 'id_sitio', type: 'int'},        
            {name: 'sitio', type: 'string'}
        ])
    });
    sitioOmcCombo = new Ext.form.ComboBox({
            id:'sitioOmcCombo',
            forceSelection : false,
            hidden:false,
            //disabled:false,
            fieldLabel: 'Sitio',
            store: sitioOmcDS,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'sitio',
            valueField: 'id_sitio',
            anchor:'95%',
            triggerAction: 'all',
            width: 300,
            tabIndex: 3
    });
    
    sitioOmcCombo.on('select', selectSitio);
    
    sectorOmcDS = new Ext.data.Store({
        id: 'sectorOmcDS',
        proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/sectores_combo', 
        method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total'
            }, [
            {name: 'id_sector', type: 'int'},        
            {name: 'sector', type: 'string'},
        ])
    });
    
    sectorOmcCombo = new Ext.form.ComboBox({
            id:'sectorOmcCombo',
            forceSelection : false,
            hidden:false,
            disabled:true,
            fieldLabel: 'Sector',
            store: sectorOmcDS,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'sector',
            valueField: 'id_sector',
            anchor:'95%',
            triggerAction: 'all',
            width: 300,
            tabIndex: 3
    });
    
    analisisRiesgoRmcRadios = new Ext.form.RadioGroup({ 
        id:'analisisRiesgoRmcRadios',
        fieldLabel: '¿Considera el Análisis de Riesgo Efectivo?',
        columns: 2,
        anchor : '95%',
        autoWidth: false,
        boxMaxWidth:100,
        labelStyle :'width:240px;',
        allowBlank: false,
//        tooltip:'Especificar si considera que el Análisis de Riesgo es efectivo o no lo es',
        blankText:'Debe seleccionar una opción',
        items: [ 
            {boxLabel: 'Si', name: 'rgARiesgo', inputValue: 'si'}, 
            {boxLabel: 'No', name: 'rgARiesgo', inputValue: 'no'}
        ] 
    });
    
    analisisRiesgoFieldSet = new Ext.form.FieldSet({
        id:'analisisRiesgoFieldSet',
        title : 'Análisis de Riesgo',
        anchor : '95%',
        growMin:100,
        items:[analisisRiesgoRmcRadios]
    }); 
    
    sectorFieldSet = new Ext.form.FieldSet({
        id:'sectorFieldSet',
        title : 'Sector donde se realizo la Observación',
        anchor : '95%',
        growMin:100,
        items:[/*empresaOmcCombo,*/sitioOmcCombo,sectorOmcCombo]
    }); 

   omcCreateForm = new Ext.FormPanel({
        labelAlign: 'left',
        labelWidth:110,
        bodyStyle:'padding:5px',
        width:500,        
        items: [{
                layout:'column',
                border:false,
                items:[{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [acompFieldSet,sectorFieldSet,analisisRiesgoFieldSet]
                    }
                ]
                }
        ],
		buttons: [{
		  text: 'Guardar',
		  handler: createOmc
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			omcCreateWindow.close();
		  }
		}]
    });
	
 
    omcCreateWindow= new Ext.Window({
        id: 'omcCreateWindow',
        title: 'Crear nueva OMC',
        closable:false,
        modal:true,
        width: 550,
        height: 350,
        plain:true,
        layout: 'fit',
        items: omcCreateForm,
        closeAction: 'close'
    });	
    
    omcCreateWindow.show();
    
//    function selectEmpresa( combo, record, index ){
//        var id =combo.getValue();
//        var comboSitio=Ext.getCmp('sitioOmcCombo');
//        var comboSector=Ext.getCmp('sectorOmcCombo');
//        comboSitio.reset();
//        comboSector.reset();
//        sitioOmcDS.setBaseParam('id',id);
//        sitioOmcDS.load();
//        comboSitio.enable();
//        comboSector.disable();
//    }
    
    function selectSitio( combo, record, index ){
        var id =combo.getValue();
        //var comboEmpresa=Ext.getCmp('empresaOmcCombo');
        var comboSector=Ext.getCmp('sectorOmcCombo');
        //var id2 =comboEmpresa.getValue();
        comboSector.reset();
        sectorOmcDS.setBaseParam('id_s',id);
        //sectorOmcDS.setBaseParam('id_e',id2);
        sectorOmcDS.load();
        comboSector.enable();
    }
    
    function selectAcomp2( combo, record, index ){
        var id =combo.getValue();
//        console.log(id);
//        console.log(record.data);
        //var comboEmpresa=Ext.getCmp('empresaOmcCombo');
        //var comboSector=Ext.getCmp('sectorOmcCombo');
        //var id2 =comboEmpresa.getValue();
        acomp2Combo.reset();
        acomp2DS.setBaseParam('id_acomp1',id);
        //sectorOmcDS.setBaseParam('id_e',id2);
        //sectorOmcDS.load();
        //comboSector.enable();
    }
}