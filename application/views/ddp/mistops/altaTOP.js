
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
function createDdpTop(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createDdpTop();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}


function go_createDdpTop(){
    if(isDdpTopFormValid()){
       msgProcess('Creando TOP...');
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/nueva_top',
        params: {
          id_periodo    : ddpPeriodoTopCombo.getValue(),         
          id_supervisor : supervisorCombo.getValue(),         
          id_aprobador  : aprobadorCombo.getValue(),         
//          tipo_top      :rb.getValue().inputValue     
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result.success){
            case 'true':
            case true:
                ddpTopCreateWindow.close();
                recargarTop();
                Ext.MessageBox.alert('Alta OK','Su TOP fue creada correctamente.');
                //miTopObjetivosTabPanel.setActiveTab(0);
                break;
            case 'false':
            case false:
                Ext.MessageBox.alert('Error',result.error);
                ddpTopCreateWindow.close();
                break;
            case 'falso':
                Ext.MessageBox.alert('Error',result.error);
//                ddpTopCreateWindow.close();
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
  }// END function createDdpTop

  
// Verifico que los campos del formulario sean válidos
function isDdpTopFormValid(){	  
    var v1 = ddpPeriodoTopCombo.isValid();
    var v2 = supervisorCombo.isValid();
    var v3 = aprobadorCombo.isValid();
    var v4 = (supervisorCombo.getValue() == aprobadorCombo.getValue() ? false : true);
    return( v1 && v2 && v3 && v4);
}
   
// display or bring forth the form
function dFW_ddpNuevoTop(){
    ddpTopsPeriodosDS = new Ext.data.Store({
        id: 'ddpTopsPeriodosDS',
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
   ddpPeriodoTopCombo = new Ext.form.ComboBox({
        id:'ddpPeriodoTopCombo',
        forceSelection : false,
        fieldLabel: 'Per&iacute;odo',
        store: ddpTopsPeriodosDS,
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
    
//    var ddpNombreSupervisorField = new Ext.form.TextField({
//        id: 'ddpNombreSupervisorField',
//        fieldLabel: 'Supervisor',
//        allowBlank: true,
//        disabled: true,
//        value:TOP.txt_supervisor,
//        anchor : '95%',
//        tabIndex: 4
//    });
    
    supervisorDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_supervisor',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            id: 'id_usuario'
        }, [
            {name: 'id_usuario', mapping: 'id_usuario'},
            {name: 'nomape', mapping: 'nomape'},
            {name: 'usuario', mapping: 'usuario'},
            {name: 'puesto', mapping: 'puesto'},
        ])
    });

    // Custom rendering Template
    var supervisorTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
             '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    supervisorCombo = new Ext.form.ComboBox({
        id:'supervisorCombo',
        store: supervisorDS,
        blankText:'campo requerido',
        invalidText:'No existe el usuario',
        allowBlank: false,
        fieldLabel: 'Supervisor',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        triggerAction: 'all',
        anchor : '95%',
        minChars:3,
        //value: TOP.txt_supervisor,
    //        labelStyle: 'font-weight:bold;',
        pageSize:10,
        tabIndex: 2,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        tpl: supervisorTpl,
        itemSelector: 'div.search-item',
    });
    
    
//    console.log(id_supervisor);
    supervisorDS.load();
    supervisorDS.on('load', function(){
//        console.log(id_supervisor);
        supervisorCombo.setValue(id_supervisor);//id_supervisor;
    });// = id_supervisor;
//    console.log(supervisorCombo);
    
    aprobadorDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_supervisor',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            id: 'id_usuario'
        }, [
            {name: 'id_usuario', mapping: 'id_usuario'},
            {name: 'nomape', mapping: 'nomape'},
            {name: 'usuario', mapping: 'usuario'},
            {name: 'puesto', mapping: 'puesto'},
        ])
    });

    // Custom rendering Template
    var aprobadorTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
             '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    aprobadorCombo = new Ext.form.ComboBox({
        id:'aprobadorCombo',
        store: aprobadorDS,
        blankText:'campo requerido',
        invalidText:'No existe el usuario',
        allowBlank: false,
        fieldLabel: 'Aprobador',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        triggerAction: 'all',
        anchor : '95%',
        minChars:3,
        //value: TOP.txt_supervisor,
    //        labelStyle: 'font-weight:bold;',
        pageSize:10,
        tabIndex: 2,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        tpl: aprobadorTpl,
        itemSelector: 'div.search-item',
    });
    
//    console.log(id_aprobador);
    aprobadorDS.load();
    aprobadorDS.on('load', function(){
//        console.log(id_supervisor);
        aprobadorCombo.setValue(id_aprobador);//id_supervisor;
    });
    
    var tipoInicioTop = new Ext.form.RadioGroup({ 
        id:'tipoInicioTop',
        fieldLabel: '¿Su TOP se inicia desde la TOP de su supervisor?',
        tabIndex:2,
        columns: 4,
        items: [ 
              {boxLabel: 'Si', name: 'tipo_top', inputValue: '1', checked: true}, 
              {boxLabel: 'No', name: 'tipo_top', inputValue: '2'}
         ] 
    });
    
 ddpTopCreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:600,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[ddpPeriodoTopCombo/*,ddpNombreSupervisorField*/,supervisorCombo,aprobadorCombo]//,tipoInicioTop]
        }],
        buttons: [{
            text: 'Crear',
            handler: createDdpTop
            },{
            text: 'Cancelar',
            handler: function(){
                ddpTopCreateWindow.close();
            }
            }]
    });//END FormPanel

 
   ddpTopCreateWindow= new Ext.Window({
        id: 'ddpTopCreateWindow',
        title: 'Crear TOP',
        closable:false,
        modal:true,
        width: 330,
        height: 250,
        plain:true,
        layout: 'fit',
        items: ddpTopCreateForm,
        closeAction: 'close'
    });		
    ddpTopCreateWindow.show();
}//END function displayFormWindow

function recargarTop(){
    Ext.get('browser').load({
        url: CARPETA+"/index/29",
        params: {},
        scripts: true,
        text: "Cargando..."
    });
}