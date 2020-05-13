 // agregar usuario a a roles
function agregarUsuarioRoles(){
            Ext.Ajax.request({   
                    waitMsg: 'Por favor espere...',
                    url: CARPETA+'/agregar_usuario',
                    params: {
                        id_usuario : usuarioCombo.getValue()
                    }, 
                    success: function(response){              
                        var result=eval(response.responseText);
                        switch(result){
                        case 1:
                            //Ext.MessageBox.alert('Alta OK','El M&oacute;dulo fue agregado.');
                            usuariosRolesDataStore.reload();
                            agregaUsuarioWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','El usuario ya se encuentra en el listado.');
                            agregaUsuarioWindow.close();
                        break;
                        case 3:
                            Ext.MessageBox.alert('Error','No tiene permisos para realizar la operaci&oacute;n solicitada.');
                            agregaUsuarioWindow.close();
                        break;
                        default:
                            Ext.MessageBox.alert('Error','No se pudo agregar el m&oacute;dulo.');
                            agregaUsuarioWindow.close();
                            break;
                        }        
                    },
                    failure: function(response){
                        var result=response.responseText;
                        Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
                    }                      
    });
}//fin agregarUsuarioRoles
 
 function displayUsuarioRolesFormWindow(){
    if(agregaUsuarioForm){
        agregaUsuarioForm.destroy();
        agregaUsuarioWindow.destroy()
    }
    // reset the Form before opening it
    function resetPresidentForm(){
        usuarioCombo.setValue('');
    }
    // JavaScript Document
    usuariosDS = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/usuariosRolesCombo',
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
    var resultTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item">',
            '<h3><span>{nomape}</h3>({puesto})</span>',
        '</div></tpl>'
    );
    
    usuarioCombo = new Ext.form.ComboBox({
        store: usuariosDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Seleccione el usuario',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        anchor : '95%',
        minChars:3,
        labelStyle: 'font-weight:bold;',
        pageSize:10,
         tabIndex: 4,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
        tpl: resultTpl,
        itemSelector: 'div.search-item'
    });
    
    // Formulario para alta nuevo usuario a la grilla de config de roles
    agregaUsuarioForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width: 400,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[usuarioCombo]
        }],
        buttons: [{
                text: 'Guardar',
                handler: agregarUsuarioRoles
            },{
                text: 'Cancelar',
                handler: function(){
                agregaUsuarioWindow.hide();
            }
            }]
    });

    
    agregaUsuarioWindow = new Ext.Window({
        id: 'nuevopermisoWindow',
        title: 'Agregar un permiso para el perfil',
        closable:false,
        modal:true,
        width:450,
        height:200,
        plain:true,
        modal : true ,
        layout: 'fit',
        items: agregaUsuarioForm
        //closeAction: 'close'
    });
    agregaUsuarioWindow.show();
 };