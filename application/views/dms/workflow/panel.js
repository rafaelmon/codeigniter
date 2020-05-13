
workflowEditorGrid.on('cellclick',function(grid, rowIndex, columnIndex){
//    var id_estado=grid.store.data.items[rowIndex].data.id_estado;
    var q1=grid.store.data.items[rowIndex].data.q_obs;
    var q2=grid.store.data.items[rowIndex].data.q_ges;
    if(columnIndex<(grid.colModel.config.length)-4 && (q1+q2)>0)
    {
        var id_documento=grid.store.data.items[rowIndex].data.id_documento;
        var codigo=grid.store.data.items[rowIndex].data.codigo;
        gestionesDataStore.load({params: {id:id_documento,start: 0}});
        obsDataStore.load({params: {id:id_documento,start: 0}});
        inferiorPanel.setTitle('Historial y Observaciones del Documento:'+codigo);
        inferiorPanel.show();
        inferiorPanel.expand(false);
//        return iDTramite;
    }
    else
    {
        inferiorPanel.collapse(true);
        inferiorPanel.setTitle('Documento sin Historial u Observaciones');

    }
        
});

workflowPanel = new Ext.Panel({
    id: 'relaciona-panel',
    layout:'border',		
    split: true,
    bodyStyle: 'padding:15px',
    border: false,
//    height: 400,
    items: [superiorPanel,inferiorPanel],
    renderTo: 'grillita'

}); 

altura=Ext.getBody().getSize().height - 60;
workflowPanel.setHeight(altura);
	
Ext.getCmp('browser').on('resize',function(comp){
    workflowPanel.setWidth(this.getSize().width);
    workflowPanel.setHeight(Ext.getBody().getSize().height - 60);
});

//ANULAR TRAMITE
//function clickBtnAnular (grid, rowIndex,colIndex)
//{
//    var id_tramite = grid.store.getAt(rowIndex).id
//    Ext.MessageBox.confirm('Confirmation','Confirma que desea anular este trámite?', function(btn){
//        if(btn=='yes'){
//            Ext.Ajax.request({  
//                waitMsg: 'Por favor espere',
//                url: CARPETA+'/anular', 
//                params: { 
//                    id_tramite:  id_tramite
//                }, 
//                success: function(response){
//                    var result=eval(response.responseText);
////                    console.log(result);
//                    switch(result){
//                    case 1:  // Success : simply reload
//                        grid.store.reload();
//                        break;
//                    case 2:  
//                        Ext.MessageBox.alert('Error','Ha ocurrido un error comuníquese con el Administrador.');
//                        break;
//                    case 3:  
//                        Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
//                        break;
//                    default:
//                        Ext.MessageBox.alert('Warning','No se pudo eliminar el registro seleccionado.');
//                        break;
//                    }
//                },
//                failure: function(response){
////                var result=response.responseText;
//                Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
//                }
//            });
//        }
//        
//    });
//    
//}//-->FIN ANULAR TRAMITE  
//
////RECHAZAR TRAMITE
//function clickBtnRechazar (grid, rowIndex,colIndex)
//{
//    var id_tramite = grid.store.getAt(rowIndex).id
//    Ext.MessageBox.confirm('Confirmation','Confirma que desea rechazar este trámite?', function(btn){
//        if(btn=='yes'){
//            Ext.Ajax.request({  
//                waitMsg: 'Por favor espere',
//                url: CARPETA+'/rechazar', 
//                params: { 
//                    id_tramite:  id_tramite
//                }, 
//                success: function(response){
//                    var result=eval(response.responseText);
////                    console.log(result);
//                    switch(result){
//                    case 1:  // Success : simply reload
//                        grid.store.reload();
//                        break;
//                    case 2:  
//                        Ext.MessageBox.alert('Error','Ha ocurrido un error comuníquese con el Administrador.');
//                        break;
//                    case 3:  
//                        Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
//                        break;
//                    default:
//                        Ext.MessageBox.alert('Warning','No se pudo rechazar el registro seleccionado.');
//                        break;
//                    }
//                },
//                failure: function(response){
////                var result=response.responseText;
//                Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
//                }
//            });
//        }
//        
//    });
//    
//}//-->FIN RECHAZAR TRAMITE
////CERRAR TRAMITE
//function clickBtnCerrar (grid, rowIndex,colIndex)
//{
//    var id_tramite = grid.store.getAt(rowIndex).id
//    Ext.MessageBox.confirm('Confirmation','Confirma que desea cerrar este trámite?', function(btn){
//        if(btn=='yes'){
//            Ext.Ajax.request({  
//                waitMsg: 'Por favor espere',
//                url: CARPETA+'/cerrar', 
//                params: { 
//                    id_tramite:  id_tramite
//                }, 
//                success: function(response){
//                    var result=eval(response.responseText);
////                    console.log(result);
//                    switch(result){
//                    case 1:  // Success : simply reload
//                        grid.store.reload();
//                        break;
//                    case 2:  
//                        Ext.MessageBox.alert('Error','Ha ocurrido un error comuníquese con el Administrador.');
//                        break;
//                    case 3:  
//                        Ext.MessageBox.alert('Error','No tiene permisos para realizar la operacion solicitada.');
//                        break;
//                    default:
//                        Ext.MessageBox.alert('Warning','No se pudo cerrar el tramite seleccionado.');
//                        break;
//                    }
//                },
//                failure: function(response){
////                var result=response.responseText;
//                Ext.MessageBox.alert('error','No se pudo conectar con la base de datos. Intente mas tarde');      
//                }
//            });
//        }
//        
//    });
    
//}//-->FIN Cerrar TRAMITE  
