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

function clickBtnEliminarVencimiento (grid,rowIndex,colIndex,item ,event){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnEliminarVencimiento(grid,rowIndex,colIndex,item ,event);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}
    
 function go_clickBtnEliminarVencimiento(grid,rowIndex,colIndex,item ,event)
 {
    var id_vencimiento      = grid.getStore().getAt(rowIndex).json.id_vencimiento;
    var vencimiento         = grid.getStore().getAt(rowIndex).json.vencimiento;
    var id_estado_actual    = grid.getStore().getAt(rowIndex).json.id_estado;
    Ext.MessageBox.confirm('Eliminar','¿Confirma que desea eliminar el vencimiento <br><i>nro: </i><b>'+id_vencimiento+'</b><br><i>vencimiento :</i><b> '+vencimiento+'</b>?', function(btn, text){
        if(btn=='yes'){
            msgProcess('Procesando...');
                Ext.Ajax.request({   
                    waitMsg: 'Por favor espere...',
                    url: CARPETA+'/eliminarVencimiento',
                    method: 'POST',
                    params: {
                            id_vencimiento      : id_vencimiento,
                            id_estado_actual    : id_estado_actual
                    }, 
                    success: function(response){              
                    var result=eval(response.responseText);
                    switch(result){
                        case 1:
                            Ext.MessageBox.alert('Operación OK','El vencimiento se eliminó correctamente');
                            vencimientosDataStore.reload();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','El vencimiento no pudo ser eliminado');
                            break;
                         case 3:
                            Ext.MessageBox.alert('Error','No puede realizar la acción sobre el vencimiento');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','El vencimiento no pudo ser eliminado');
                            break;
                    }        
                    },
                    failure: function(response){
            //          var result=response.responseText;
                        Ext.MessageBox.alert('Error','No se pudo conectar con el servidor. Intente m&aacute;s tarde');         
                    }                      
                });
        }
     });
  };


