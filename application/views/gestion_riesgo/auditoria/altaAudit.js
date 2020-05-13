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

function createAuditoria(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createAuditoria();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

  function go_createAuditoria(){
     if(isAuditoriaFormValid()){
        msgProcess('Guardando Auditoría');
        Ext.Ajax.request({   
            waitMsg: 'Por favor espere...',
            url: CARPETA+'/insert',
            method: 'POST',
            params: {
                fecha          : fechaAuditoriaField.getValue(),
                q_usuarios     : usuariosAuditoriaNumberField.getValue(),
                auditores      : auditoresSBS.getValue(),
                programada     : programadaAuditoriaRadios.getValue().inputValue,
                realizada      : realizadaAuditoriaRadios.getValue().inputValue,
                sector         : sectorAuditoriaCombo.getValue()
            }, 
            success: function(response){              
                var result=eval(response.responseText);
                switch(result){
                        case 1:
                            Ext.MessageBox.alert('Alta OK','Auditoría creada satisfactoriamente.');
                            auditoriaDataStore.reload();
                            auditoriaCreateWindow.close();
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case 3:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        case 4:
                            Ext.MessageBox.alert('Error','Por favor verifique, máximo 3 auditores');
                            auditoresSBS.markInvalid();
//                            acomp1Combo.setActiveError('Error');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo crear la auditoría.');
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
  function isAuditoriaFormValid(){	  
	  var v1 = fechaAuditoriaField.isValid();
	  var v2 = usuariosAuditoriaNumberField.isValid();
	  var v3 = auditoresSBS.isValid();
	  var v4 = fechaAuditoriaField.isValid();
	  var v5 = programadaAuditoriaRadios.isValid();
	  var v6 = realizadaAuditoriaRadios.isValid();
	  var v7 = empresaAuditoriaCombo.isValid();
	  var v8 = sectorAuditoriaCombo.isValid();
	  return( v1 && v2 && v3 && v4 && v5 && v6 && v7 && v8);
  }
   
   function clickBtnNuevaAuditoria(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_clickBtnNuevaAuditoria();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
   
  // display or bring forth the form
  function go_clickBtnNuevaAuditoria(){

	
	 if(auditoriaCreateForm){
	 	if(auditoriaCreateForm.findById('fieldsetid')) {
		 //get the fieldset
	
	
		 var oldfieldset = auditoriaCreateForm.findById('fieldset_form');
		 
			//iterate trough each of the component in the fieldset
			oldfieldset.items.each(function(collection,item,length){
				var i = item;
				//destroy the object within the fieldset
				for(i=item; i<length; i++){oldfieldset.items.get(i).destroy();}
			});
		}
		auditoriaCreateForm.destroy();
		auditoriaCreateWindow.destroy()
	}
		

  // reset the Form before opening it
  function resetPresidentForm(){
    fechaAuditoriaField.setValue('');
    usuariosAuditoriaNumberField.setValue('');
    programadaAuditoriaRadios.setValue('');
    realizadaAuditoriaRadios.setValue('');
    sectorAuditoriaCombo.setValue('');
  }	

    fechaAuditoriaField = new Ext.form.DateField({
            allowBlank: false,
            tabIndex: 1,
            fieldLabel:'Fecha Auditoría',
            allowBlank: false,
            anchor : '95%',
            blankText:'campo requerido',
            editable: true,
//            minValue:MINDATE,
            maxValue:MAXDATE,
            format:'d/m/Y'
    });
    usuariosAuditoriaNumberField = new Ext.ux.form.SpinnerField({
        id: 'q_usuarios',
        fieldLabel: 'Cantidad de usuarios',
        allowDecimals:false,
        allowNegative:false,
        maxValue:200,
        minValue:0,
        invalidText:'Solo enteros entre>0',
        allowBlank: false,
        blankText:'campo requerido',
        invalidText:'Inválido',
        value:0,
        anchor : '95%',
        tabIndex: 2
    });
    auditoresDS = new Ext.data.Store({
        id:'revisoresDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_auditores',
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
    var auditoresTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
auditoresSBS = new Ext.ux.form.SuperBoxSelect({
        id:'auditoresSBS',
        forceSelection : false,
        fieldLabel: 'Auditor/es',
        store: auditoresDS,
//        editable : false,
        allowBlank: false,
        emptyText: 'Ingresa caracteres para buscar',
        blankText:'campo requerido',
        displayField: 'nomape',
        valueField: 'id_usuario',
        anchor : '95%',
//        displayFieldTpl: '{nomape} ({usuario})',
        mode: 'remote',
//        valueDelimiter:';',
        tpl: auditoresTpl,
        itemSelector: 'div.search-item',
        stackItems:true, //un item por línea
//        anchor:'90%',
//        triggerAction: 'all',
//        forceSelection : true,
        allowQueryAll : false,
        minChars:3,
        maxSelections : 2,
        invalidText :'Máximo 3 Auditores',
        tabIndex:3
    });
 auditoresSBS.on('additem', selectAuditor);
 auditoresSBS.on('removeitem', selectAuditor);
 function selectAuditor( combo, record, index ){
//   console.log(combo);
        var x =combo.getValue();
        x= x.split(",");
        x= x.length;
        auditoresDS.setBaseParam('q',x);
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
//auditoresFieldSet = new Ext.form.FieldSet({
//    id:'auditoresFieldSet',
//    title : 'Auditores',
//    anchor : '95%',
//    growMin:100,
//    items:[auditoresSBS]
//});
    programadaAuditoriaRadios = new Ext.form.RadioGroup({ 
        id:'programadaAuditoriaRadios',
        fieldLabel: '¿La Auditoria fue programada?',
        tabIndex:4,
        columns: 2,
        anchor : '95%',
        autoWidth: false,
        boxMaxWidth:100,
        allowBlank: false,
        blankText:'Debe seleccionar una opción',
        items: [ 
            {boxLabel: 'Si', name: 'programada', inputValue: 'si'}, 
            {boxLabel: 'No', name: 'programada', inputValue: 'no'}
        ] 
    });
    
     realizadaAuditoriaRadios = new Ext.form.RadioGroup({ 
        id:'realizadaAuditoriaRadios',
        fieldLabel: '¿La auditoria fue realizada?',
        tabIndex:5,
        columns: 2,
        anchor : '95%',
        autoWidth: false,
        boxMaxWidth:100,
        allowBlank: false,
        blankText:'Debe seleccionar una opción',
        items: [ 
            {boxLabel: 'Si', name: 'realizada', inputValue: 'si'}, 
            {boxLabel: 'No', name: 'realizada', inputValue: 'no'}
        ] 
    });
    
    empresasAuditoriaDS = new Ext.data.Store({
        id: 'empresasAuditoriaDS',
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
    
    empresaAuditoriaCombo = new Ext.form.ComboBox({
            id:'empresaAuditoriaCombo',
            forceSelection : false,
            fieldLabel: 'Empresa',
            store: empresasAuditoriaDS,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'empresa',
            valueField: 'id_empresa',
            anchor:'95%',
            triggerAction: 'all',
//            width: 300,
            tabIndex: 6
    });
    empresaAuditoriaCombo.on('select', selectAuditoriaEmpresa);
    
    sectorAuditoriaDS = new Ext.data.Store({
        id: 'sectorAuditoriaDS',
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
    sectorAuditoriaCombo = new Ext.form.ComboBox({
            id:'sectorAuditoriaCombo',
            forceSelection : false,
            hidden:false,
            disabled:true,
            fieldLabel: 'Sector',
            store: sectorAuditoriaDS,
            editable : false,
            allowBlank: false,
            blankText:'campo requerido',
            displayField: 'sector',
            valueField: 'id_sector',
            anchor:'95%',
            triggerAction: 'all',
//            width: 300,
            tabIndex: 7
    });
     sectorAuditoriaFieldSet = new Ext.form.FieldSet({
        id:'sectorAuditoriaFieldSet',
        title : 'Sector auditado',
        anchor : '95%',
        growMin:100,
        items:[empresaAuditoriaCombo,sectorAuditoriaCombo]
    }); 
    
   auditoriaCreateForm = new Ext.FormPanel({
        labelAlign: 'left',
//        labelWidth:110,
        bodyStyle:'padding:5px',
        width:550,        
        items: [{
                layout:'column',
                border:false,
                items:[{
                            labelWidth:180,
                            columnWidth:1,
                            layout: 'form',
                            border:false,
                            items: [fechaAuditoriaField,usuariosAuditoriaNumberField,auditoresSBS,programadaAuditoriaRadios,realizadaAuditoriaRadios,sectorAuditoriaFieldSet]
                        }
                ]
                }
        ],
		buttons: [{
		  text: 'Guardar',
		  handler: createAuditoria
		},{
		  text: 'Cancelar',
		  handler: function(){
			// because of the global vars, we can only instantiate one window... so let's just hide it.
			auditoriaCreateWindow.close();
		  }
		}]
    });
	
 
    auditoriaCreateWindow= new Ext.Window({
        id: 'auditoriaCreateWindow',
        title: 'Alta Nueva Auditoría',
        closable:false,
        modal:true,
        width: 550,
        height: 400,
        plain:true,
        layout: 'fit',
        items: auditoriaCreateForm,
        closeAction: 'close'
    });		


    auditoriaCreateWindow.show();
    
    function selectEmpresa( combo, record, index ){
        var id =combo.getValue();
        var combosector=Ext.getCmp('sectorAuditoriaCombo')
        combosector.reset();
        sectorAuditoriaDS.setBaseParam('id',id);
        sectorAuditoriaDS.load();
        combosector.enable();
    }
    
    function selectAuditoriaEmpresa( combo, record, index ){
        var id =combo.getValue();
        var combosector=Ext.getCmp('sectorAuditoriaCombo')
        combosector.reset();
        sectorAuditoriaDS.setBaseParam('id',id);
        sectorAuditoriaDS.load();
        combosector.enable();
    }
//    function selectSector( combo, record, index ){
//        var id =combo.getValue();
//        var combosector=Ext.getCmp('sectorAuditoriamboCombo')
//        combosector.reset();
//        combosector.enable();
//        sectorAuditoriaDS.setBaseParam('id',id);
//        sectorAuditoriaDS.load();
//    }
}