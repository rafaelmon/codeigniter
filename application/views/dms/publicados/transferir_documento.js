
// JavaScript Document
function msgProcess(titulo){
    Ext.MessageBox.show({
        title: titulo,
        msg: 'Procesando, por favor espere...',
        progress:true,
        progressText: 'Procesando...', 
        width:400, 
        wait:true, 
        waitConfig: {interval:200}
    });
}

function clickBtntransferirDoc (id,cod,id_g){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"sys");break;case 1:case '1':go_clickBtntransferirDoc(id,cod,id_g);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"sys");}});}

function go_clickBtntransferirDoc (id,cod,id_g)
{
    function transferirDoc(){
        if(isModuloFormValid())
        {
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/transferir_documento', 
                params: { 
                    id_documento : id,
                    id_gerencia  : gerenciasCombo.getValue(),
                    id_editor    : editoresCombo.getValue()
                }, 
                success: function(response){
                    var result=eval(response.responseText);
                    switch(result.error){
                case 0:
                    var nuevo=result.id;
                    Ext.MessageBox.alert('Operación OK','Se ha creado y transferido una nueva version del documento y se encuentra disponible en la bandeja de trabajo del editor bajo el identificador Nro:<b>'+nuevo+'</b>');
                    transferirDocCreateWindow.close();
                    publicadosDataStore.reload();
                    break;
                default:
                    transferirDocCreateWindow.close();
                    Ext.MessageBox.alert('Error',result.error);
                    break;
                }        
                },
                failure: function(response){
        //          var result=response.responseText;
                Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
                } 
           });

       } else {
         Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
       }
     }

  
    // check if the form is valid
    function isModuloFormValid()
    {
        var v1 = gerenciasCombo.isValid();
        var v2 = editoresCombo.isValid();
        return(v1 && v2);
    }

    gerenciasDS = new Ext.data.Store({
        id:'gerenciasDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_gerencias_transferir',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_area'
        }, [
            {name: 'id_area',   mapping: 'id_area'},
            {name: 'area',      mapping: 'area'},
            {name: 'abv',       mapping: 'abv'}
        ])
    });
    gerenciasDS.setBaseParam('id_g',id_g);
    
    var gerenciasTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
             '<h3><span>{area} - {abv}</h3>\n\</span>',
        '</div></tpl>'
    );    
    
    gerenciasCombo = new Ext.form.ComboBox({
        id:'gerenciasCombo',
        store: gerenciasDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Gerencia destino',
        displayField:'area',
        tpl: gerenciasTpl,
        valueField:'id_area',
        typeAhead: false,
        loadingText: 'Buscando...',
        anchor : '95%',
        forceSelection : true,
        minChars:3,
//        labelStyle: 'font-weight:bold;',
        itemSelector: 'div.search-item',
        pageSize:10,
        tabIndex: 1,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        triggerAction: 'all'
    });
    
    gerenciasCombo.on('select', cargar_combo_editores);
    
    codigoLabel = new Ext.form.Label(
    {
        id: 'codigoLabel',
        visible: false,
        fieldLabel: 'Codigo documento',
    })
    
    editoresDS = new Ext.data.Store({
        id:'editoresDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_editores_transferencia',
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_usuario'
        }, [
            {name: 'id_usuario',  mapping: 'id_usuario'},
            {name: 'nomape',      mapping: 'nomape'}
        ])
    });
    
    
    editoresCombo = new Ext.form.ComboBox({
        id:'editoresCombo',
        store: editoresDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Editor',
        displayField:'nomape',
        valueField:'id_usuario',
        loadingText: 'Buscando...',
        anchor : '95%',
        //triggerAction: 'all',
        forceSelection : false,
        minChars:3,
//        labelStyle: 'font-weight:bold;',
        pageSize:10,
        tabIndex: 2,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        disabled: true
    });
    
    tranferirDocCreateForm = new Ext.FormPanel({
        id:'tranferirDocCreateForm',
        labelAlign: 'top',
        labelWidth:80,
        bodyStyle:'padding:5px',
        width: 600,        
        items: [{
                id:'fieldset_form',
                layout:'column',
                border:false,
                items:[{
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [gerenciasCombo,codigoLabel,editoresCombo]
                    }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: transferirDoc
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                transferirDocCreateWindow.close();
            }
            }]
    });
    
    function cargar_combo_editores( combo, record, index ){
        var id_g =combo.getValue();
        editoresDS.setBaseParam('id',id);
        editoresDS.setBaseParam('id_g',id_g);
        editoresCombo.enable();
        var parcial = cod.substring(0, 3);
        //var codigo = parcial + 
//        console.log(gerenciasCombo);
//        console.log(gerenciasCombo.selectedIndex);
//        console.log(gerenciasCombo.value);
//        console.log(gerenciasCombo.store.data.keys[gerenciasCombo.selectedIndex]);
        var abvGerencia = gerenciasDS.data.items[gerenciasCombo.selectedIndex].data.abv;
        codigoLabel.setText(parcial + '-' + abvGerencia + '-XXXX-XX');
        codigoLabel.setVisible(true);
    }
	
 
    transferirDocCreateWindow= new Ext.Window({
        id: 'transferirDocCreateWindow',
        title: 'Transferir documento ' + cod,
        closable:false,
        modal:true,
        width: 600,
        height: 250,
        plain:true,
        layout: 'fit',
        items: tranferirDocCreateForm,
        closeAction: 'close'
    });
    
    transferirDocCreateWindow.show();
    
 }//fin 