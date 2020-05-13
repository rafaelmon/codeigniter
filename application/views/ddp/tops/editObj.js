function dFW_ddpEditObetivo(id_obj){
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

function editDdpObjetivo(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_editDdpObjetivo();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_editDdpObjetivo(){
    if(isDdpObjetivoFormValid()){
       msgProcess('Guardando Objetivo');
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/edit_obj',
        params: {
          id            : id_obj,
          id_dimension  : ID_DIM,
          obj_e         : ddpObjetivoEmpresaField.getValue(),
          obj_p         : ddpObjetivoPersonalField.getValue(),
          indicador     : ddpIndicadorObjetivoField.getValue(),
          valor_ref     : ddpValorRefObjetivoField.getValue(),
          fd            : ddpFuenteDatosObjetivoField.getValue(),
          peso          : ddpPesoObjetivoNumberField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result){
            case 0:
                Ext.MessageBox.alert('Error','No se pudo midificar el registro, ya existe el objetivo que intenta crear.');
                ddpObjetivoEditForm.destroy();
                ddpObjetivoEditWindow.close();
                break;
            case 1:
                var objetivos_DS=Ext.getCmp('objetivosGridPanel2').getStore();
                var dimensionesGridPanel=Ext.getCmp('dimensiones-grid-panel2');
                var dimensionesDS=dimensionesGridPanel.getStore();
                objetivos_DS.reload();
                var rowdim=dimensionesGridPanel.selModel.rowNav.scope.lastActive;
                dimensionesDS.load();
                dimensionesDS.on('load',function(){
                    dimensionesGridPanel.selModel.selectRow(rowdim,false,false);
                });
                dimensionesGridPanel.selModel.selectRow(rowdim);
                ddpObjetivoEditForm.destroy();
                ddpObjetivoEditWindow.close();
                SUM_DIM=0;
//                clickBtnVerHistorial();
                Ext.MessageBox.alert('Alta OK..','El Objetivo  fue modificado correctamente');
                break;
            case 2:
                ddpObjetivoEditForm.destroy();
                ddpObjetivoEditWindow.close();
                Ext.MessageBox.alert('Aviso','No se realizaron cambios en el objetivo');         
                break;
            default:
                Ext.MessageBox.alert('Error','No se pudo modificar el objetivo, por favor vuelva a intentarlo o contacte con el administrador.');
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
  }// END function editDdpObjetivo

  
// Verifico que los campos del formulario sean válidos
function isDdpObjetivoFormValid(){	  
    var v1 = ddpObjetivoEmpresaField.isValid();
    var v2 = ddpObjetivoPersonalField.isValid();
//    var v3 = ddpIndicadorObjetivoField.isValid();
//    var v4 = ddpValorRefObjetivoField.isValid();
//    var v5 = ddpFuenteDatosObjetivoField.isValid();
//    var v6 = ddpPesoObjetivoNumberField.isValid();
//    return( v1 && v2 && v3 && v4 && v5 &&v6);
    return( v1 && v2);
}
   
// display or bring forth the form
//    console.log(ID_DIM);

    
ddpObjetivoEmpresaField = new Ext.form.TextArea({
        id: 'ddpObjetivoEmpresaField',
        fieldLabel: 'Objetivo de la empresa',
        maxLength: 2000,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        maxLengthText:'M&aacute;ximo 2000 caracteres',
        tabIndex: 1
    });
ddpObjetivoPersonalField = new Ext.form.TextArea({
        id: 'ddpObjetivoPersonalField',
        fieldLabel: 'Objetivo personal',
        maxLength: 2000,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        maxLengthText:'M&aacute;ximo 2000 caracteres',
        tabIndex: 2
    });
      
 ddpIndicadorObjetivoField = new Ext.form.TextField({
        id: 'ddpIndicadorObjetivoField',
        fieldLabel: 'Indicador',
        allowBlank: true,
//        blankText:'campo requerido',
//        width: 50,
        anchor : '95%',
        tabIndex: 3
    });
ddpFuenteDatosObjetivoField = new Ext.form.TextField({
        id: 'ddpFuenteDatosObjetivoField',
        fieldLabel: 'Fiente de datos',
        allowBlank: true,
//        blankText:'campo requerido',
//        width: 50,
        anchor : '95%',
        tabIndex: 4
    });
ddpValorRefObjetivoField = new Ext.form.TextField({
        id: 'ddpValorRefObjetivoField',
        fieldLabel: 'Valor de Referencia',
        allowBlank: true,
//        blankText:'campo requerido',
//        width: 50,
        anchor : '95%',
        tabIndex: 5
    });
ddpPesoObjetivoNumberField = new Ext.ux.form.SpinnerField({
        id: 'ddpPesoObjetivoNumberField',
        fieldLabel: 'Peso',
        allowDecimals:true,
        allowNegative:false,
        maxValue:100,
        minValue:0,
        invalidText:'Solo valores entre 0 y 100',
        allowBlank: false,
        blankText:'campo requerido',
        invalidText:'Inválido',
        value:0,
        anchor : '95%',
        tabIndex: 6
    });
  
ddpObjetivoEditForm = new Ext.FormPanel({
        id:'ddpObjetivoEditForm',
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:700,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[ ddpObjetivoEmpresaField
                    ,ddpObjetivoPersonalField
                    ,ddpIndicadorObjetivoField
                    ,ddpFuenteDatosObjetivoField
                    ,ddpValorRefObjetivoField
                    ,ddpPesoObjetivoNumberField
            ]
        }],
        buttons: [{
            text: 'Guardar',
            tabIndex: 7,
            handler: editDdpObjetivo
            },{
            text: 'Cancelar',
            handler: function(){
                ddpObjetivoEditWindow.close();
            }
            }]
    });//END FormPanel

 
ddpObjetivoEditWindow= new Ext.Window({
        id: 'ddpObjetivoEditWindow',
        title: 'Editando Objetivo  nro '+id_obj,
        closable:true,
        modal:true,
        width: 700,
        height: 500,
        plain:true,
        layout: 'fit',
        items: ddpObjetivoEditForm,
        closeAction: 'close'
    });
                ddpObjetivoEditWindow.show();
if (id_obj > 0)
    {
            var formPanel=Ext.getCmp('ddpObjetivoEditForm');
            var winPanel=Ext.getCmp('ddpObjetivoEditWindow');
//        console.log(winPanel);
            formPanel.getForm().load({
                waitMsg: "Cargando...",
                url: CARPETA+'/datos_obj',
                params: {id: id_obj},
                success:function(response){
        //                var result=eval(response.responseText)

                },
                failure:function(response){
                       Ext.MessageBox.alert('Error','No se pudo cargar el objetivo');
                       winPanel.close();

                }
            });
    } 
    
    
}//END function displayFormWindow