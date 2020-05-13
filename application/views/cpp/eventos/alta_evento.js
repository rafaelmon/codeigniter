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
Ext.apply(Ext.form.VTypes, {
    daterange : function(val, field) {
        var date = field.parseDate(val);

        if(!date){
            return false;
        }
        if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
            var start = Ext.getCmp(field.startDateField);
            start.setMaxValue(date);
            start.validate();
            this.dateRangeMax = date;
        }
        else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            var end = Ext.getCmp(field.endDateField);
            end.setMinValue(date);
            end.validate();
            this.dateRangeMin = date;
        }
        return true;
    }
});
function clickBtnNuevoEvento (grid,rowIndex,colIndex,item,event,id){Ext.Ajax.request({url: LINK_GENERICO+'/sesion',method: 'POST',success: function(response, opts) {var result=parseInt(response.responseText);
switch (result){case 0:case '0':location.assign(URL_BASE_SITIO+"admin");break;case 1:case '1':go_clickBtnNuevoEvento(grid,rowIndex,colIndex,item,event,id);break;}},failure: function(response) {location.assign(URL_BASE_SITIO+"admin");}});}

function go_clickBtnNuevoEvento (grid,rowIndex,colIndex,item,event,id)
{
    function altaEvento(){
        if(isModuloFormValid())
        {
            msgProcess('Generando...');
            Ext.Ajax.request({  
                waitMsg: 'Por favor espere',
                url: CARPETA+'/insert', 
                params: { 
                   id                 : id,
                   sector             : sectoresCombo.getValue(),
                   fecha_inicio       : fechaInicioField.getValue(),
                   hora_inicio        : hmInicioField.getValue(),
                   fecha_fin          : fechaFinField.getValue(),
                   hora_fin           : hmFinField.getValue(),
                   equipo             : equiposSBS.getValue(),
                   producto           : productosCombo.getValue(),
                   descripcion        : descripcionEventoField.getValue()
                 }, 
                success: function(response){
                    var result=eval(response.responseText);
                    switch(result){
                        case -1:
                            Ext.MessageBox.alert('Error','Por favor verifique horas de inicio y fin');
                            hmInicioField.markInvalid();
                            hmFinField.markInvalid();
                            break;
                        case 1:
                            Ext.MessageBox.alert('Operación OK','Registro agregado correctamente');
                            cppEventosDataStore.reload();
                            altaEventoCreateWindow.close();
                            break;
                        case 2:
                            Ext.MessageBox.alert('Error','Falta completar campos requeridos');
                            break;
                        default:
                            Ext.MessageBox.alert('Error','Error.');
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
          var v1 = sectoresCombo.isValid();
          var v2 = fechaInicioField.isValid();
          var v3 = fechaFinField.isValid();
          var v4 = equiposSBS.isValid();
          var v5 = productosCombo.isValid();
          var v6  = descripcionEventoField.isValid();
          var v7  = hmInicioField.isValid();
          var v8  = hmFinField.isValid();
          if (v7 && v8)
          {
//          var h_i=hmInicioField.getValue().split(":");
//          var h_f=hmFinField.getValue().split(":");
            if(fechaInicioField.getValue().getTime()==fechaFinField.getValue().getTime() && hmInicioField.getValue()>=hmFinField.getValue())
            {
                v7=false;
                v8=false;
                hmInicioField.markInvalid('La hora de inicio debe ser inferior a la hora de fin');
                hmFinField.markInvalid('La hora de fin debe ser superior a la hora de inicio');
            }
          }
          
        return( v1 && v2 && v3 && v4 && v5 && v6 && v7 && v8);
    }


    // reset the Form before opening it
      function resetPresidentForm(){
          fechaInicioField.setValue('');
          descripcionEventoField.setValue('');
    }
sectoresDS = new Ext.data.Store({
    id: 'sectoresDS',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/combo_sectores', 
        method: 'POST'
    }),
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total'
        },[
        {name: 'id_sector', type: 'int'},        
        {name: 'sector', type: 'string'},
    ])
});
sectoresCombo = new Ext.form.ComboBox({
    id:'sectoresCombo',
    allQuery:'',
    fieldLabel: 'Sector',
    store: sectoresDS,
    editable : true,
    forceSelection : true,
    allowBlank: false,
    blankText:'campo requerido',
    valueField: 'id_sector',
    displayField: 'sector',
    minChars:3,
    anchor:'95%',
    tabIndex:1,
    pageSize:15,
    triggerAction: 'all',
    width: 300
});

fechaInicioField = new Ext.form.DateField({
    id: 'fechaInicioField',
    fieldLabel: 'Fecha',
    allowBlank: false,
    vtype: 'daterange',
    blankText:'campo requerido',
    endDateField: 'fechaFinField',
    tabIndex:2,
//    minValue:,
    maxValue:MINDATE,
    anchor : '50%'
});
//    horaInicioField = new Ext.form.NumberField({
//      id: 'horaInicioField',
//      fieldLabel: 'Hora',
//      allowBlank: false,
//      anchor : '80%'
//    });
//    minutoInicioField = new Ext.form.NumberField({
//      id: 'minutoInicioField',
//      fieldLabel: 'Hora',
//      allowBlank: false,
//      anchor : '80%'
//    });
hmInicioField = new Ext.form.TimeField({
    id: 'hmInicioField',
    fieldLabel: 'Hora',
    allowBlank: false,
    blankText:'campo requerido',
    format:'H:i', 
    increment: 15,
    tabIndex:3,
    width: 90
});

fechaHoraInicio=new Ext.form.CompositeField({
    fieldLabel: 'Inicio',
    combineErrors: false,
    items: [
        {xtype: 'displayfield', value: ' Fecha'},
        fechaInicioField,
        {xtype: 'displayfield', value: ' Hora'},
        hmInicioField,
    ]
});
fechaFinField = new Ext.form.DateField({
    id: 'fechaFinField',
    fieldLabel: 'Fecha fin',
    allowBlank: false,
    vtype: 'daterange',
    minText : 'la fecha fin debe ser igual o posterior a la fecha inicio',
    blankText:'campo requerido',
    startDateField:'fechaInicioField',
    maxValue:MINDATE,
    tabIndex:4,
    anchor : '50%'
});
hmFinField = new Ext.form.TimeField({
    id: 'hmFinField',
    fieldLabel: 'Hora',
    allowBlank: false,
    blankText:'campo requerido',
    format:'H:i',
    increment: 15,
    tabIndex:5,
    width: 90
});
fechaHoraFin=new Ext.form.CompositeField({
    fieldLabel: 'Fin',
    combineErrors: false,
    items: [
        {xtype: 'displayfield', value: ' Fecha'},
        fechaFinField,
        {xtype: 'displayfield', value: ' Hora'},
        hmFinField,
    ]
});

equiposDS = new Ext.data.Store({
    id: 'equiposDS',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/combo_equipos', 
        method: 'POST'
    }),
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total',
        id: 'id_equipo'
        },[
        {name: 'id_equipo', type: 'int'},        
        {name: 'equipo', type: 'string'},
    ])
});
var equiposTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-invest">',
            '<h3><span>{equipo}</h3></span>',
        '</div></tpl>'
    );
equiposSBS = new Ext.ux.form.SuperBoxSelect({
        id:'equiposSBS',
        fieldLabel: 'Equipos',
        store: equiposDS,
//        editable : false,
        allowBlank: false,
        emptyText: 'Ingresa caracteres para buscar',
        blankText:'campo requerido',
        displayField: 'equipo',
        valueField: 'id_equipo',
//        displayFieldTpl: '{nomape} ({usuario})',
        mode: 'remote',
//        valueDelimiter:';',
        tpl: equiposTpl,
        itemSelector: 'div.search-invest',
        stackItems:true, //un item por l�nea
        anchor:'95%',
//        triggerAction: 'all',
        forceSelection : false,
        allowQueryAll : false,
        minChars:3,
        maxSelections : 6,
        tabIndex:1
    });

productosCheckField = new Ext.form.Checkbox ({
        fieldLabel: '',
        name: 'productosCheckField',
        checked: false
    });
productosCheckField.on('check', selectProductosCheckField); 
productosComposite=new Ext.form.CompositeField({
    fieldLabel: '',
    combineErrors: false,
    items: [
        {xtype: 'displayfield', value: '¿El evento tiene producto asociado?'},
        productosCheckField,
    ]
});
productosDS = new Ext.data.Store({
    id: 'productosDS',
    proxy: new Ext.data.HttpProxy({
        url: CARPETA+'/combo_productos', 
        method: 'POST'
    }),
    reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'total'
        },[
        {name: 'id_producto', type: 'int'},        
        {name: 'producto', type: 'string'},
    ])
});
productosCombo = new Ext.form.ComboBox({
    id:'productosCombo',
    fieldLabel: 'Producto',
    store: productosDS,
    editable : true,
    forceSelection : true,
    allowBlank: false,
    blankText:'campo requerido',
    valueField: 'id_producto',
    displayField: 'producto',
    anchor:'95%',
    tabIndex:7,
    triggerAction: 'all',
    width: 300,
    hidden:false,
    disabled:true
});
    descripcionEventoField = new Ext.form.TextArea({
      id: 'descripcionEventoField',
      fieldLabel: 'Descripción',
      maxLength: 1028,
      allowBlank: false,
      tabIndex:8,
      anchor : '95%'
    });
    
    altaEventoCreateForm = new Ext.FormPanel({
        id:'altaEventoCreateForm',
        labelAlign: 'left',
        labelWidth:80,
        bodyStyle:'padding:5px',
        width: 600,        
        items: [{
                id:'fieldset_form',
                layout:'column',
                border:false,
                items:[
                    {
                    columnWidth:1,
                    layout: 'form',
                    border:false,
                    items: [sectoresCombo
                        ,fechaHoraInicio
                        ,fechaHoraFin
                        ,productosComposite
                        ,productosCombo
                        ,descripcionEventoField
                        ,equiposSBS
                        ]
                    }
                ]
        }],
	buttons: [{
            text: 'Guardar',
            handler: altaEvento
	},{
            text: 'Cancelar',
            handler: function(){
                // because of the global vars, we can only instantiate one window... so let's just hide it.
                altaEventoCreateWindow.close();
            }
            }]
    });
	
 
    altaEventoCreateWindow= new Ext.Window({
        id: 'altaEventoCreateWindow',
        title: 'Alta evento',
        closable:false,
        modal:true,
        width: 600,
        height: 550,
        plain:true,
        layout: 'fit',
        items: altaEventoCreateForm,
        closeAction: 'close'
    });		
    
    function selectProductosCheckField(cb,checked)
    {
        var combo = Ext.getCmp('productosCombo');
        if(checked == true)
        {
            combo.enable();
        }
        else
        {
            combo.reset();
            combo.disable();
            combo.setDisabled(true);
        }
    }
    if (id > 0)
    {
        altaEventoCreateWindow.setTitle('Editar evento Nro '+id);
        Ext.getCmp('productosCombo').enable();
        
        var dsEquipo = Ext.getCmp('equiposSBS').getStore();
        var dsProd = Ext.getCmp('productosCombo').getStore();
        var dsSector = Ext.getCmp('sectoresCombo').getStore();
        dsEquipo.load();
        dsProd.load();
        dsSector.load();
        
        dsEquipo.on('load', function(){
            var formPanel=Ext.getCmp('altaEventoCreateForm');
            formPanel.getForm().load({
                waitMsg: "Cargando...",
                url: CARPETA+'/datos_evento',
                params: {id: id},
                success:function(response){
                    if (productosCombo.getValue() == null)
                    {
                        Ext.getCmp('productosCombo').disable();
                    }
                },
                failure:function(response){
                       Ext.MessageBox.alert('Error','No se pudo cargar el objetivo');
                       setCriticidadEventoCreateWindow.close();

                }
            });
        });
    }
    altaEventoCreateWindow.show();
 }//fin 