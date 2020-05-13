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

function clickBtnDesignarInvest (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnDesignarInvest(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnDesignarInvest (grid, rowIndex)
{
    var record = grid.getStore().getAt(rowIndex);
    var evento=record.data.id;
    function designarInvestigadres(){
        if(isModuloFormValid())
        {
            var form=designarInvestigadresCreateForm.getForm();
            var is=form.findField('investigadoresSBS');
            var list = is.getValue();
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/insert_ei', 
                params: { 
                   evento       : evento,
                   lista       :  list,
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    if(result.success){
                        cppEventosDataStore.reload();
                        investigadoresDataStore.reload();
                        designarInvestigadresCreateWindow.close();
                        Ext.MessageBox.alert('OK',result.msg);
                    }
                    else
                        Ext.MessageBox.alert('Error',result.msg);
                },
                failure: function(response){
                    var result=eval(response.responseText);
                    Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
                }
           });

       } else {
         Ext.MessageBox.alert('Alerta', 'Los datos no son v&aacute;lidos. Complete correctamente el formulario.');
       }
     }

  
    // check if the form is valid
    function isModuloFormValid(){	  
          var v1 = true;
        return( v1);
    }


    // reset the Form before opening it
      function resetPresidentForm(){
//          designarInvestigadresCreateForm.getForm().findField('itemselector').reset()
    }
investigadoresDS = new Ext.data.Store({
    id: 'investigadoresDS',
//    remoteSort: true,
    baseParams:{evento: evento},
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/combo_investigadores', 
        method: 'POST'
    }),
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total'
        },[
        {name: 'id_usuario', mapping: 'id_usuario'},
        {name: 'nomape', mapping: 'nomape'},
        {name: 'usuario', mapping: 'usuario'},
        {name: 'puesto', mapping: 'puesto'}
    ])
});
//investigadoresDS.load();

var investigadoresTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-invest">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
investigadoresSBS = new Ext.ux.form.SuperBoxSelect({
        id:'investigadoresSBS',
        fieldLabel: 'Investigador/es',
        store: investigadoresDS,
//        editable : false,
        allowBlank: false,
        emptyText: 'Ingresa caracteres para buscar',
        blankText:'campo requerido',
        displayField: 'nomape',
        valueField: 'id_usuario',
//        displayFieldTpl: '{nomape} ({usuario})',
        mode: 'remote',
//        valueDelimiter:';',
        tpl: investigadoresTpl,
        itemSelector: 'div.search-invest',
        stackItems:true, //un item por línea
        anchor:'90%',
//        triggerAction: 'all',
        forceSelection : false,
        allowQueryAll : false,
        minChars:3,
        maxSelections : 15,
        tabIndex:1
    });

    
    
    designarInvestigadresCreateForm = new Ext.FormPanel({
        id:'designarInvestigadresCreateForm',
        labelAlign: 'left',
        labelWidth:100,
        bodyStyle:'padding:5px',
        width: 800,        
        items: [investigadoresSBS],
	buttons: [{
            text: 'Guardar',
            handler: designarInvestigadres
            },{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                designarInvestigadresCreateWindow.close();
            }
            }]
    });
	
 
    designarInvestigadresCreateWindow= new Ext.Window({
        id: 'designarInvestigadresCreateWindow',
        title: 'Definir investigadres',
        closable:false,
        modal:true,
        width: 600,
        height: 450,
        plain:true,
        maximizable:true,
        autoScroll :true,
        autoHeight :false,
        layout: 'fit',
        items: designarInvestigadresCreateForm,
        closeAction: 'close'
    });		
    designarInvestigadresCreateWindow.show();
    
 }//fin 