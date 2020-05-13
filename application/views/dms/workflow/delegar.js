// JavaScript Document

function clickBtnDelegar (grid, rowIndex, colIndex)
{
      
    var record = grid.getStore().getAt(rowIndex); 
    var id_documento=record.data.id_documento;
    var codigo=record.data.codigo;
    var id_estado=record.data.id_estado;
    
    var rol="";
    console.log(rol);
    switch (id_estado)
    {
        case '1':
        case '2':
            rol= "Editor";
            break;
        case '3':
            rol= "Revisor";
            break;
        case '4':
            rol= "Aprobador";
            break;
        case '5':
            rol= "Publicador";
            break;
    }
    var msg = function(title, msg){
        Ext.Msg.show({
            title: title,
            msg: msg,
            minWidth: 200,
            modal: true,
            icon: Ext.Msg.INFO,
            buttons: Ext.Msg.OK
        });
    };
    
delegadosDS = new Ext.data.Store({
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/combo_delegar',
        method: 'POST'
    }),
    baseParams: {id_documento:id_documento},
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

 var delegadoTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
delegadoCombo = new Ext.form.ComboBox({
    id:'delegadoCombo',
    fieldLabel: rol,
    store: delegadosDS,
    editable : true,
    blankText:'campo requerido',
    allowBlank: false,
    displayField:'nomape',
    valueField:'id_usuario',
    typeAhead: false,
    anchor : '90%',
    triggerAction: 'all',
    loadingText: 'Buscando...',
    minChars:2,
//        labelStyle: 'font-weight:bold;',
    pageSize:10,
    tabIndex: 11,
    emptyText:'Ingresa caracteres para buscar '+rol,
    valueNotFoundText:"",
    tpl: delegadoTpl,
    itemSelector: 'div.search-item'
});
	

var delegarCreateForm = new Ext.FormPanel({
//    title: 'Delegar gestión',
    url: CARPETA+'/delegar',
    width: 520, 
    buttonAlign: 'center',
    frame: true, 
    fileUpload: true, 
    labelAlign: 'left',
    waitTitle:'Espere Por favor...',
    labelWidth:100,
    style: 'margin: 0 auto;',
    items: [delegadoCombo]
});


    delegarCreateWindow= new Ext.Window({
        id: 'delegarCreateWindow',
        title: 'Delegar editor del documento '+codigo,
        modal:true,
        width: 550,
        height: 120,
        plain:true,
        layout: 'fit',
        items: delegarCreateForm,
        closable:true,
        closeAction: 'close',
        resizable:false,
        buttons: [{
        text: 'Delegar',
        handler: function() {
            delegarCreateForm.getForm().submit({
                waitMsg: 'Delegando...',
                timeout:30,
                params: { 
                   id_documento: id_documento,
                   id_editor: delegadoCombo.getValue()
                },
                success: function(form, action){
                    msg('Success', 'Su gestión ha sido procesada correctamente');
                    workflowDataStore.reload();
                    delegarCreateWindow.close();
                },
                failure: function(form, action){
                    var result=eval(action.response.responseText);
                    msg('Error', result.error);
                }
            });
        }
    },{
        text: 'Cancelar',
        handler: function(){
            delegarCreateForm.destroy();
            delegarCreateWindow.close();
        }
    },{
        text: 'Reset',
        handler: function() {
            delegarCreateForm.getForm().reset();
        }
    }]
    });		
    delegarCreateWindow.show();

  
 }//fin clickBtnDelegar()