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

function clickBtnEditSupervisorAdmEd (grid,rowIndex,colIndex,item,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnEditSupervisorAdmEd(grid,rowIndex,colIndex,item,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnEditSupervisorAdmEd (grid, rowIndex)
{
    var periodo=grid.getStore().getAt(rowIndex).json.periodo;
    var empleado=grid.getStore().getAt(rowIndex).json.empleado;
    var id_evaluacion=grid.getStore().getAt(rowIndex).json.id_evaluacion;
    var id_usuario_supervisor=grid.getStore().getAt(rowIndex).json.id_usuario_supervisor;
//    console.log(id_evaluacion);
    function editSupervisorAdmEd(){
        if(isModuloFormValid())
        {
//            var record = grid.getStore().getAt(rowIndex);
//            var id = record.data.id;
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/modificar', 
                params: { 
                   id_evaluacion            : id_evaluacion,
                   id_usuario_supervisor    : usuariosSupervisorAdmEdCombo.getValue(),
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    switch(result){
                        case 1:
                           edAdminDataStore.commitChanges();
                           edAdminDataStore.reload();
                           editSupervisorAdmEdCreateWindow.close();
                           Ext.MessageBox.alert('OK','Supervisor modificado correctamente.');
                           break;
                        case 2:
                           Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
//                           edAdminDataStore.commitChanges();
                           edAdminDataStore.reload();
                           editSupervisorAdmEdCreateWindow.close();
                           break;          
                        case 3:
                           Ext.MessageBox.alert('Error','No debe ingresar como supervisor al mismo usuario');
//                           edAdminDataStore.commitChanges();
                           edAdminDataStore.reload();
                           editSupervisorAdmEdCreateWindow.close();
                           break; 
                       case 4:
                           Ext.MessageBox.alert('Error','Solo se puede modificar la evaluación si su estado es "En curso".');
               //            edAdminDataStore.commitChanges();
                           edAdminDataStore.reload();
                           editSupervisorAdmEdCreateWindow.close();
                           break;  
                        case 5:
                           Ext.MessageBox.alert('Error','Debe seleccionar un supervisor diferente al existente.');
               //            edAdminDataStore.commitChanges();
                           edAdminDataStore.reload();
                           editSupervisorAdmEdCreateWindow.close();
                           break;
                        default:
                           Ext.MessageBox.alert('Error','No hay conexión con la base de datos. Asegurese de tener conexion');
                           break;
                    }
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
          var v1 = usuariosSupervisorAdmEdCombo.isValid();
        return( v1 );
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          usuariosSupervisorAdmEdCombo.setValue('');
    }

    usuariosSupervisorAdmEdDS = new Ext.data.Store({
        id:'usuariosSupervisorAdmEdDS',
        proxy: new Ext.data.HttpProxy({
            url: CARPETA+'/combo_usuarios',
            method: 'POST'
        }),
        baseParams:{id_usuario_supervisor: id_usuario_supervisor},
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'total',
            id: 'id_usuario'
        }, [
            {name: 'id_usuario', mapping: 'id_usuario'},
            {name: 'nomape', mapping: 'nomape'}
        ])
    });

    usuariosSupervisorAdmEdCombo = new Ext.form.ComboBox({
        id:'usuariosSupervisorAdmEdCombo',
        store: usuariosSupervisorAdmEdDS,
        blankText:'campo requerido',
        allowBlank: false,
        fieldLabel: 'Supervisor',
        displayField:'nomape',
        valueField:'id_usuario',
        typeAhead: false,
        loadingText: 'Buscando...',
        anchor : '100%',
        forceSelection : true,
        minChars:3,
    //        labelStyle: 'font-weight:bold;',
        pageSize:10,
        tabIndex: 11,
        emptyText:'Ingresa caracteres para buscar',
        valueNotFoundText:"",
    //        tpl: aprobadorTpl,
    //        itemSelector: 'div.search-item'
    }); 
    
    editSupervisorAdmEdCreateForm = new Ext.FormPanel({
        id:'editSupervisorAdmEdCreateForm',
        labelAlign: 'left',
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
                    items: [usuariosSupervisorAdmEdCombo]
                    }]
        }],
	buttons: [{
            text: 'Guardar',
            handler: editSupervisorAdmEd
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                editSupervisorAdmEdCreateWindow.close();
            }
            }]
    });
	
 
    editSupervisorAdmEdCreateWindow= new Ext.Window({
        id: 'editSupervisorAdmEdCreateWindow',
        title: 'Editar supervisor ED Nro:'+id_evaluacion+'|'+empleado+'|'+periodo,
        closable:false,
        modal:true,
        width: 400,
        height: 150,
        plain:true,
        layout: 'fit',
        items: editSupervisorAdmEdCreateForm,
        closeAction: 'close'
    });		
    editSupervisorAdmEdCreateWindow.show();
    
 }//fin 