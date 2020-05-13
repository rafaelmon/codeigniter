function ddpSupTopEv1EditObj_DFW(id_obj){

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

function createDdpEditObjSupTopEv1(){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts){var result=parseInt(response.responseText);switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':
go_createDdpEditObjSupTopEv1();break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_createDdpEditObjSupTopEv1(){
    if(isDdpEditObjSupTopEv1FormValid()){
       msgProcess('Guardando Objetivo');
      Ext.Ajax.request({   
        waitMsg: 'Por favor espere...',
        url: CARPETA+'/eval1',
        params: {
          id            : id_obj,
          real1          : ddpSupTopReal1ObjetivoNumberField.getValue()
        }, 
        success: function(response){              
          var result=eval(response.responseText);
          switch(result.success){
            case 0:
                Ext.MessageBox.alert('Error','No se pudo modificar el registro, ya existe el objetivo que intenta crear.');
                ddpEditObjSupTopEv1CreateForm.destroy();
                ddpEditObjSupTopEv1CreateWindow.close();
                break;
            case true:
            case 1:
                var objetivos_DS=Ext.getCmp('supTopObjetivosGridPanel').getStore();
                objetivos_DS.reload();
                ddpEditObjSupTopEv1CreateForm.destroy();
                ddpEditObjSupTopEv1CreateWindow.close();
                Ext.MessageBox.alert('Operación OK..','El Objetivo  fue modificado correctamente');
                break;
            default:
                Ext.MessageBox.alert('Error','No se pudo realizar la operación. Mensage error:'+result.error);
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
  }// END function createDdpObjetivo

  
// Verifico que los campos del formulario sean válidos
function isDdpEditObjSupTopEv1FormValid(){	  
    var v1 = ddpSupTopReal1ObjetivoNumberField.isValid();
    return( v1);
}
   
// display or bring forth the form
//    console.log(ID_DIM);

    
var ddpSupTopObjetivoField = new Ext.form.TextArea({
        id: 'ddpSupTopObjetivoField',
        fieldLabel: 'Objetivo',
        maxLength: 2000,
        disabled: true,
        allowBlank: false,
        blankText:'campo requerido',
        anchor : '95%',
        maxLengthText:'M&aacute;ximo 2000 caracteres',
        tabIndex: 1
    });
      
 var ddpSupTopIndicadorObjetivoField = new Ext.form.TextField({
        id: 'ddpSupTopIndicadorObjetivoField',
        fieldLabel: 'Indicador',
        allowBlank: true,
        disabled: true,
//        blankText:'campo requerido',
//        width: 50,
        anchor : '95%',
        tabIndex: 3
    });
var ddpSupTopFuenteDatosObjetivoField = new Ext.form.TextField({
        id: 'ddpSupTopFuenteDatosObjetivoField',
        fieldLabel: 'Fuente de datos',
        allowBlank: true,
        disabled: true,
//        blankText:'campo requerido',
//        width: 50,
        anchor : '95%',
        tabIndex: 4
    });
var ddpSupTopValorRefObjetivoField = new Ext.form.TextField({
        id: 'ddpSupTopValorRefObjetivoField',
        fieldLabel: 'Valor de Referencia',
        allowBlank: true,
        disabled: true,
//        blankText:'campo requerido',
//        width: 50,
        anchor : '95%',
        tabIndex: 5
    });
var ddpSupTopPesoObjetivoNumberField = new Ext.form.NumberField({
        id: 'ddpSupTopPesoObjetivoNumberField',
        fieldLabel: 'Peso',
        disabled: true,
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
var ddpSupTopReal1ObjetivoNumberField = new Ext.form.NumberField({
        id: 'ddpSupTopReal1ObjetivoNumberField',
        fieldLabel: '% Alcanzado (ingrese un valor entre 0 y 100)',
        disabled: false,
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
  
var ddpEditObjSupTopEv1CreateForm = new Ext.FormPanel({
        labelAlign: 'top',
        bodyStyle:'padding:5px',
        width:700,        
        items: [{
            id:'fieldset_form',
            layout:'form',
            border:false,
            items:[ ddpSupTopObjetivoField
                    ,ddpSupTopIndicadorObjetivoField
                    ,ddpSupTopFuenteDatosObjetivoField
                    ,ddpSupTopValorRefObjetivoField
                    ,ddpSupTopPesoObjetivoNumberField
                    ,ddpSupTopReal1ObjetivoNumberField
            ]
        }],
        buttons: [{
            text: 'Guardar',
            tabIndex: 7,
            handler: createDdpEditObjSupTopEv1
            },{
            text: 'Cancelar',
            tabIndex: 8,
            handler: function(){
                ddpEditObjSupTopEv1CreateWindow.close();
            }
            }]
    });//END FormPanel

 
var ddpEditObjSupTopEv1CreateWindow= new Ext.Window({
        id: 'ddpEditObjSupTopEv1CreateWindow',
         title: 'Editando objetivo NRO '+id_obj,
        closable:true,
        modal:true,
        width: 700,
        height: 550,
        plain:true,
        layout: 'fit',
        items: ddpEditObjSupTopEv1CreateForm,
        closeAction: 'close'
    });
if (id_obj > 0)
    {
            ddpEditObjSupTopEv1CreateForm.getForm().load({
            waitMsg: "Cargando...",
            url: CARPETA+'/datos_obj_ev1',
            params: {id: id_obj},
            success:function(form,action)
            {
//             var maxNum=action.result.data.ddpSupTopPesoObjetivoNumberField;
//            var realSpinner=Ext.getCmp('ddpSupTopReal1ObjetivoNumberField');
//            realSpinner.setMaxValue(maxNum);

            },
            failure:function(response){
                    //console.log('no cargado');

            }
            });
    } 
    
    ddpEditObjSupTopEv1CreateWindow.show();
}//END function displayFormWindow